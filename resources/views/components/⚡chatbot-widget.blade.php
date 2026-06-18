<?php

use App\Models\DeliveryOrderReceipt;
use Illuminate\Support\Facades\Http;
use Livewire\Component;
use Livewire\Attributes\On; // Tambahkan import ini untuk Event Listener

new class extends Component {
    public bool $isOpen = false;
    public string $message = '';
    public array $chats = [];
    public bool $isTyping = false; // Tambahkan state untuk animasi loading AI

    public function toggleChat()
    {
        $this->isOpen = !$this->isOpen;
        if (empty($this->chats)) {
            $this->chats[] = [
                'role' => 'assistant',
                'content' => 'Halo! Saya AI Support Receiving 2.0. Ada yang bisa saya bantu terkait status PO, Delivery Order, atau pengecekan status material?'
            ];
        }
    }

    public function sendMessage()
    {
        $this->validate(['message' => 'required|string|max:255']);

        // 1. Simpan pesan user ke variabel lokal
        $userMessage = $this->message;

        // 2. Render pesan user ke UI secepat mungkin & kosongkan input
        $this->chats[] = ['role' => 'user', 'content' => $userMessage];
        $this->message = '';
        $this->isTyping = true; // Nyalakan indikator AI sedang "mengetik"

        // 3. Dispatch event ke frontend agar Livewire segera merender UI,
        // lalu memanggil method fetchAiResponse di request terpisah secara otomatis.
        $this->dispatch('process-ai-response', userMessage: $userMessage);
    }

    #[On('process-ai-response')]
    public function fetchAiResponse(string $userMessage)
    {
        // 1. Ekstrak potensi nomor (PO/DO) atau teks dari pesan user
        // Regex ini akan mengambil semua deretan angka (misal: 5300057474) atau kata dari pesan
        preg_match_all('/\b[A-Za-z0-9-]+\b/', $userMessage, $matches);
        $searchTerms = $matches[0];

        // Mulai Query Dasar
        $query = DeliveryOrderReceipt::with(['deliveryOrderReceiptDetails.purchaseOrderIssued', 'qcHistories', 'transmittals']);

        // 2. Filter dinamis berdasarkan pesan user
        // Jika ada term pencarian, prioritaskan mencari data spesifik tersebut
        if (!empty($searchTerms)) {
            $query->where(function ($q) use ($searchTerms) {
                foreach ($searchTerms as $term) {
                    // Abaikan kata-kata umum agar query tidak berat
                    if (strlen($term) < 3)
                        continue;

                    $q->orWhere('delivery_oder_no', 'LIKE', "%{$term}%")
                        ->orWhereHas('deliveryOrderReceiptDetails', function ($qDetail) use ($term) {
                            $qDetail->where('material_code', 'LIKE', "%{$term}%")
                                ->orWhereHas('purchaseOrderIssued', function ($qPo) use ($term) {
                                    // PERBAIKAN: Ubah po_number menjadi purchase_order_no
                                    $qPo->where('purchase_order_no', 'LIKE', "%{$term}%");
                                });
                        });
                }
            });
        }

        // 3. Tarik Konteks Data dari Database (Batas 10 data spesifik atau terbaru)
        $recentReceipts = $query->latest('received_date')
            ->take(10)
            ->get();

        \Carbon\Carbon::setLocale('id');

        $contextData = $recentReceipts->map(function ($receipt) {
            $details = $receipt->deliveryOrderReceiptDetails->map(function ($detail) {
                // PERBAIKAN: Sesuaikan pemanggilan property
                $poNumber = $detail->purchaseOrderIssued ? $detail->purchaseOrderIssued->purchase_order_no : 'Tidak Ada PO';

                return "- Item: {$detail->description} ({$detail->material_code}) | Qty: {$detail->quantity} | PO: {$poNumber}";
            })->implode("\n");

            // Info Transmittal (Posisi Dokumen)
            $latestTransmittal = $receipt->transmittals->sortByDesc('created_at')->first();
            $transmittalInfo = $latestTransmittal
                ? "Dikirim ke {$latestTransmittal->destination} via Transmittal No: {$latestTransmittal->transmittal_no} (Tipe: {$latestTransmittal->type}) pada {$latestTransmittal->created_at->isoFormat('D MMMM YYYY')}"
                : "Belum ada riwayat Transmittal.";

            // Info QC History (Masalah QC)
            $qcNotes = $receipt->qcHistories->map(function ($qc) {
                return "- [{$qc->created_at->isoFormat('D MMMM YYYY HH:mm')}] Status QC: {$qc->status} | Catatan: " . strip_tags($qc->notes);
            })->implode("\n");

            if (empty($qcNotes)) {
                $qcNotes = "- Belum ada riwayat masalah QC.";
            }

            return "DO No: {$receipt->delivery_oder_no} | Status Utama: {$receipt->status} | Tanggal Terima: {$receipt->received_date->isoFormat('D MMMM YYYY')}
Posisi/Status Dokumen (Transmittal): {$transmittalInfo}
Histori QC & Masalah:
{$qcNotes}
Detail Barang:
{$details}";
        })->implode("\n\n-------------------\n\n");

        // 4. Susun Prompt untuk Gemini
        $systemPrompt = "Kamu adalah Asisten Logistik cerdas untuk aplikasi Receiving 2.0. Tugasmu adalah menjawab pertanyaan terkait status penerimaan barang, posisi dokumen, dan masalah QC berdasarkan data database berikut.

                        Data Penerimaan Terkait:
                        " . ($contextData ?: 'Tidak ditemukan data penerimaan yang cocok dengan pencarian.') . "

                        Instruksi Menjawab:
                        1. Jawablah dengan sangat rapi, terstruktur, ramah, dan ringkas. Gunakan Markdown (seperti **bold**, *italic*, atau list bullet) agar informasi mudah dibaca dan poin-poinnya jelas.
                        2. Jika user bertanya tentang status dokumen/posisi dokumen, periksa bagian 'Posisi/Status Dokumen (Transmittal)' dan beritahu mereka ke mana dokumen tersebut terakhir dikirim atau dikembalikan.
                        3. Jika user bertanya mengenai masalah QC, revisi, atau penolakan, periksa bagian 'Histori QC & Masalah' lalu jelaskan alasan/catatannya dengan format bullet atau terstruktur.
                        4. Jika data ada, sampaikan status dan detail materialnya secara singkat dan rapi.
                        5. Pastikan semua format tanggal yang kamu sebutkan menggunakan format bahasa Indonesia yang rapi, contoh: '17 Juni 2026'.
                        6. Jika user menanyakan proses lanjutan yang datanya TIDAK ADA dalam teks di atas, berikan jawaban sopan: 'Mohon maaf, untuk proses selanjutnya saat ini masih dalam tahap administrasi. Silakan lakukan pengecekan kembali secara berkala.'
                        7. Jika nomor PO/DO sama sekali tidak ditemukan, katakan: 'Maaf, saya tidak dapat menemukan data tersebut di riwayat penerimaan terbaru. Mohon pastikan nomor PO atau DO sudah benar.'";

        $geminiChatHistory = [];
        foreach ($this->chats as $chat) {
            // Mapping role 'assistant' ke 'model' agar sesuai dengan standar Gemini API
            $role = $chat['role'] === 'assistant' ? 'model' : 'user';

            $geminiChatHistory[] = [
                'role' => $role,
                'parts' => [
                    ['text' => $chat['content']]
                ]
            ];
        }

        // 3. Hit API Gemini
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])
                ->withoutVerifying() // Nonaktifkan verifikasi SSL (Gunakan hanya untuk local development)
                ->timeout(60)        // Tambahkan batas waktu koneksi menjadi 60 detik
                // UPDATE: Menggunakan endpoint model gemini-3.1-flash-lite
                ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-3.1-flash-lite:generateContent?key=" . config('services.gemini.api_key'), [
                    'system_instruction' => [
                        'parts' => [
                            ['text' => $systemPrompt]
                        ]
                    ],
                    'contents' => $geminiChatHistory
                ]);

            if ($response->successful()) {
                $aiReply = $response->json('candidates.0.content.parts.0.text');
                $this->chats[] = ['role' => 'assistant', 'content' => $aiReply];
            } else {
                // Jika API membalas tapi dengan status error (misal: 400 Bad Request / 403 Forbidden)
                \Illuminate\Support\Facades\Log::error('Gemini API Error: ' . $response->body());
                $this->chats[] = ['role' => 'assistant', 'content' => 'Maaf, terjadi gangguan dari server AI. Coba lagi nanti.'];
            }
        } catch (\Exception $e) {
            // Jika koneksi internet putus, timeout, atau masalah SSL
            \Illuminate\Support\Facades\Log::error('Gemini Connection Exception: ' . $e->getMessage());
            $this->chats[] = ['role' => 'assistant', 'content' => 'Error koneksi jaringan: Gagal menghubungi AI.'];
        }

        // 4. Matikan indikator loading setelah mendapatkan balasan
        $this->isTyping = false;
    }
};
?>

<div class="fixed bottom-6 right-6 z-50 font-sans">
    <!-- Tombol Chat (Floating) -->
    <div class="relative group">
        <!-- Efek Glow Oranye di belakang tombol -->
        <div x-show="!$wire.isOpen"
            class="absolute -inset-1 bg-gradient-to-r from-[#F47920] to-[#0A4F86] rounded-full blur-md opacity-40 group-hover:opacity-70 transition duration-500">
        </div>
        <button wire:click="toggleChat"
            class="relative w-14 h-14 bg-gradient-to-br from-[#0A4F86] to-[#1261a0] hover:from-[#0d5c9c] hover:to-[#0A4F86] rounded-full shadow-lg shadow-[#0A4F86]/30 flex items-center justify-center text-white transition-all duration-300 ease-out transform hover:scale-105 active:scale-95 border border-[#1a7bc7]/30">

            <!-- Icon Chat (Tutup) -->
            <svg x-show="!$wire.isOpen" class="w-6 h-6 transition-transform duration-300" fill="none"
                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23-.693L5 14.5m14.8.8l1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0112 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5">
                </path>
            </svg>

            <!-- Icon Close (Buka) -->
            <svg x-show="$wire.isOpen" style="display: none;"
                class="w-6 h-6 transition-transform duration-300 rotate-90" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <!-- Jendela Chat -->
    <div x-show="$wire.isOpen" style="display: none;" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-8 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-8 scale-95"
        class="absolute bottom-20 right-0 w-[340px] sm:w-[420px] bg-white/95 backdrop-blur-2xl rounded-3xl shadow-[0_30px_80px_-15px_rgba(10,79,134,0.3)] border border-white/50 overflow-hidden flex flex-col h-[36rem] ring-1 ring-slate-900/5">

        <!-- Header -->
        <div
            class="bg-gradient-to-r from-[#0A4F86] via-[#1261a0] to-[#0A4F86] bg-[length:200%_auto] animate-gradient px-6 py-4 flex items-center justify-between border-b border-[#0A4F86]/80 shadow-[0_4px_20px_-5px_rgba(10,79,134,0.4)] relative z-20 overflow-hidden">
            <!-- Glass Overlay -->
            <div class="absolute inset-0 bg-white/5 backdrop-blur-sm"></div>

            <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 rounded-full bg-white/10 blur-3xl"></div>

            <div class="flex items-center gap-3.5 relative z-10">
                <div class="relative group">
                    <div
                        class="w-11 h-11 bg-gradient-to-tr from-[#F47920] to-[#f89b53] rounded-2xl flex items-center justify-center shadow-lg border border-[#F47920]/40 group-hover:shadow-[#F47920]/40 transition-all duration-300">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <span
                        class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-400 border-[2.5px] border-[#0A4F86] rounded-full shadow-sm"></span>
                </div>
                <div>
                    <h3 class="text-[15px] font-extrabold text-white tracking-wide leading-tight">Receiving AI</h3>
                    <p class="text-[11px] text-blue-100/90 font-medium tracking-wide mt-0.5">Asisten Gudang Cerdas</p>
                </div>
            </div>

            <button wire:click="toggleChat"
                class="text-blue-100 hover:text-white transition-all duration-300 relative z-10 bg-white/10 hover:bg-white/20 p-2 rounded-xl backdrop-blur-sm">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"></path>
                </svg>
            </button>
        </div>

        <!-- Ruang Obrolan -->
        <div class="flex-1 p-5 overflow-y-auto bg-slate-50/60 flex flex-col gap-6 scrollbar-thin scrollbar-thumb-[#0A4F86]/20 scrollbar-track-transparent relative"
            id="chat-container">

            <div class="flex justify-center">
                <span
                    class="text-[10px] font-bold tracking-widest text-slate-400 uppercase bg-slate-200/50 px-4 py-1.5 rounded-full shadow-sm">
                    Hari ini
                </span>
            </div>

            @foreach($chats as $chat)
                @if($chat['role'] === 'assistant')
                        <!-- Bubble AI -->
                        <div class="flex items-start gap-3 max-w-[92%] group">
                            <div class="relative flex-shrink-0 mt-1">
                                <div
                                    class="w-9 h-9 rounded-full bg-gradient-to-tr from-[#F47920] to-[#f89b53] flex items-center justify-center shadow-md border border-[#F47920]/30 relative z-10">
                                    <svg class="w-4.5 h-4.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                </div>
                                <div class="absolute inset-0 bg-[#F47920] rounded-full animate-ping opacity-20 scale-110"></div>
                            </div>
                            <div
                                class="bg-white px-5 py-4 rounded-2xl rounded-tl-sm shadow-[0_4px_20px_-5px_rgba(0,0,0,0.05)] border border-slate-100/80 text-[13.5px] text-slate-700 leading-relaxed ai-markdown-content relative overflow-hidden">
                                {!! str($chat['content'])->markdown([
                        'html_input' => 'escape',
                        'allow_unsafe_links' => false,
                    ]) !!}
                            </div>
                        </div>
                @else
                    <!-- Bubble User -->
                    <div class="flex items-end justify-end w-full">
                        <div
                            class="bg-gradient-to-br from-[#0A4F86] to-[#0d5c9c] text-white px-5 py-3.5 rounded-2xl rounded-tr-sm shadow-[0_8px_20px_-6px_rgba(10,79,134,0.35)] border border-[#1261a0]/50 text-[13.5px] leading-relaxed max-w-[85%] relative overflow-hidden">
                            <!-- Efek Kilap Kaca -->
                            <div
                                class="absolute top-0 left-0 w-full h-full bg-gradient-to-b from-white/10 to-transparent opacity-60 pointer-events-none">
                            </div>
                            <span class="relative z-10 block">{{ $chat['content'] }}</span>
                        </div>
                    </div>
                @endif
            @endforeach

            <!-- Loading Indicator -->
            @if($isTyping)
                <div class="flex items-start gap-3 max-w-[85%]">
                    <div
                        class="w-9 h-9 rounded-full bg-slate-200/80 flex-shrink-0 flex items-center justify-center mt-1 animate-pulse border border-slate-300/50">
                        <span class="w-1.5 h-1.5 bg-slate-400 rounded-full"></span>
                    </div>
                    <div
                        class="bg-white px-5 py-4 rounded-2xl rounded-tl-sm shadow-[0_4px_20px_-5px_rgba(0,0,0,0.05)] border border-slate-100 flex items-center gap-1.5">
                        <span
                            class="w-2 h-2 bg-gradient-to-r from-[#0A4F86] to-[#F47920] rounded-full animate-bounce"></span>
                        <span class="w-2 h-2 bg-gradient-to-r from-[#0A4F86] to-[#F47920] rounded-full animate-bounce"
                            style="animation-delay: 0.15s"></span>
                        <span class="w-2 h-2 bg-gradient-to-r from-[#0A4F86] to-[#F47920] rounded-full animate-bounce"
                            style="animation-delay: 0.3s"></span>
                    </div>
                </div>
            @endif
        </div>

        <!-- Input Area -->
        <form wire:submit="sendMessage"
            class="p-5 bg-white/95 backdrop-blur-md border-t border-slate-100 shadow-[0_-15px_30px_-15px_rgba(0,0,0,0.04)] relative z-20 rounded-b-3xl">
            <div
                class="relative flex items-center bg-slate-50 border border-slate-200 rounded-full focus-within:ring-4 focus-within:ring-[#0A4F86]/10 focus-within:border-[#0A4F86]/40 focus-within:bg-white transition-all duration-300 shadow-inner group">

                <!-- Disable input saat AI sedang mengetik -->
                <input wire:model="message" type="text" placeholder="Tanya PO, DO, atau Material..."
                    class="w-full pl-6 pr-14 py-4 bg-transparent text-[13.5px] text-slate-800 placeholder-slate-400 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed rounded-full font-medium"
                    required autocomplete="off" @disabled($isTyping)>

                <button type="submit"
                    class="absolute right-2 w-11 h-11 bg-gradient-to-br from-[#0A4F86] to-[#1261a0] rounded-full flex items-center justify-center text-white hover:from-[#F47920] hover:to-[#f89b53] shadow-md hover:shadow-[#F47920]/30 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed hover:-translate-y-0.5 active:translate-y-0"
                    @disabled($isTyping)>
                    <svg class="w-5 h-5 translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M4.5 12h15m0 0l-6.75-6.75M19.5 12l-6.75 6.75"></path>
                    </svg>
                </button>
            </div>

            <!-- Footer Branding -->
            <div class="text-center mt-3.5 flex justify-center items-center gap-2">
                <div
                    class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse shadow-[0_0_5px_rgba(52,211,153,0.8)]">
                </div>
                <span class="text-[10px] font-semibold text-slate-400 tracking-wide">POWERED BY <span
                        class="text-transparent bg-clip-text bg-gradient-to-r from-[#0A4F86] to-[#F47920] font-black tracking-wider">MOKONDO
                        AI</span></span>
            </div>
        </form>
    </div>

    <!-- Script auto-scroll -->
    @script
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.hook('morph.updated', () => {
                const container = document.getElementById('chat-container');
                if (container) {
                    container.scrollTo({
                        top: container.scrollHeight,
                        behavior: 'smooth'
                    });
                }
            });
        });
    </script>
    @endscript

    <style>
        /* Animasi Gradient Header */
        @keyframes gradient {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        .animate-gradient {
            animation: gradient 4s ease infinite;
        }

        /* Custom minimal scrollbar */
        #chat-container::-webkit-scrollbar {
            width: 5px;
        }

        #chat-container::-webkit-scrollbar-track {
            background: transparent;
        }

        #chat-container::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 20px;
        }

        #chat-container:hover::-webkit-scrollbar-thumb {
            background-color: #94a3b8;
        }

        /* Styling Markdown AI */
        .ai-markdown-content {
            font-size: 13.5px;
            color: #334155;
            line-height: 1.6;
        }

        .ai-markdown-content p {
            margin-bottom: 0.75rem;
        }

        .ai-markdown-content p:last-child {
            margin-bottom: 0;
        }

        .ai-markdown-content strong {
            color: #0A4F86;
            font-weight: 800;
        }

        .ai-markdown-content ul {
            list-style-type: none;
            padding-left: 0;
            margin-top: 0.75rem;
            margin-bottom: 0.75rem;
        }

        .ai-markdown-content ul li {
            position: relative;
            padding-left: 1.25rem;
            margin-bottom: 0.35rem;
        }

        .ai-markdown-content ul li::before {
            content: "•";
            color: #F47920;
            font-weight: bold;
            font-size: 18px;
            position: absolute;
            left: 0;
            top: -2px;
        }

        .ai-markdown-content hr {
            border-top: 1px dashed #e2e8f0;
            margin: 1rem 0;
        }
    </style>
</div>
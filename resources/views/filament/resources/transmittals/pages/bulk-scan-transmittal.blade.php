<x-filament-panels::page>
    <div x-data="{
        step: @entangle('step'),
        type: @entangle('data.type'),
        itemToDelete: null,
        playSuccess() {
            let audio = new Audio('https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3');
            audio.play().catch(e => console.log('Audio error:', e));
        },
        playError() {
            let audio = new Audio('https://assets.mixkit.co/active_storage/sfx/2955/2955-preview.mp3');
            audio.play().catch(e => console.log('Audio error:', e));
        }
    }" 
    @play-success-sound.window="playSuccess()"
    @play-error-sound.window="playError()"
    @focus-103-input.window="setTimeout(() => { document.getElementById('input-103').focus(); }, 100)"
    @focus-document-input.window="setTimeout(() => { document.getElementById('input-document').focus(); }, 100)"
    class="space-y-6">

        <!-- Form Pengaturan -->
        <div class="p-6 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-sm">
            <form wire:submit.prevent>
                {{ $this->form }}
            </form>
        </div>

        <!-- Premium Glassmorphism Scanner Area -->
        <div class="relative py-10">
            <!-- Decorative Glow Backgrounds -->
            <div class="absolute inset-0 overflow-hidden pointer-events-none rounded-3xl">
                <div class="absolute -top-24 -left-24 w-96 h-96 bg-primary-500/20 dark:bg-primary-500/10 rounded-full blur-3xl mix-blend-multiply dark:mix-blend-screen transition-all duration-1000" :class="step === 1 ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-10'"></div>
                <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-amber-500/20 dark:bg-amber-500/10 rounded-full blur-3xl mix-blend-multiply dark:mix-blend-screen transition-all duration-1000" :class="step === 2 ? 'opacity-100 translate-x-0' : 'opacity-0 translate-x-10'"></div>
            </div>

            <!-- Glass Card -->
            <div class="relative max-w-md mx-auto backdrop-blur-xl bg-white/60 dark:bg-gray-900/60 border border-white/40 dark:border-gray-700/50 rounded-2xl shadow-[0_8px_32px_0_rgba(31,38,135,0.07)] p-6 overflow-hidden transition-all duration-500"
                 :class="step === 1 ? 'ring-1 ring-primary-500/30' : 'ring-1 ring-amber-500/30'">
                 
                <!-- Floating Badges -->
                <div class="absolute top-4 right-4 flex space-x-2">
                    <span x-show="type === 'Kirim'" class="px-2 py-0.5 text-[10px] font-bold tracking-widest uppercase rounded-full backdrop-blur-md transition-colors duration-300"
                          :class="step === 1 ? 'bg-primary-500/20 text-primary-700 dark:text-primary-300' : 'bg-amber-500/20 text-amber-700 dark:text-amber-300'">
                        Tahap <span x-text="step"></span>/2
                    </span>
                </div>

                <div class="text-center mb-6 pt-2">
                    <h2 class="text-xl font-extrabold tracking-tight text-transparent bg-clip-text bg-gradient-to-r transition-colors duration-500"
                        :class="step === 1 ? 'from-primary-600 to-blue-500 dark:from-primary-400 dark:to-blue-300' : 'from-amber-600 to-orange-500 dark:from-amber-400 dark:to-orange-300'">
                        <span x-text="step === 1 ? 'Scan Dokumen DO' : 'Scan Kode 103'"></span>
                    </h2>
                    <p class="mt-1 text-sm font-medium text-gray-500 dark:text-gray-400">
                        <span x-show="step === 1">Silakan pindai barcode/QR dokumen Anda.</span>
                        <span x-show="step === 2">Pindai QR 103 untuk PO <span class="text-amber-600 dark:text-amber-400 font-bold underline decoration-amber-500/30">{{ $this->pending_do_no }}</span></span>
                    </p>
                </div>

                <!-- Morphing Input Container -->
                <div class="relative w-full">
                    
                    <!-- Input Step 1: Scan Dokumen -->
                    <div x-show="step === 1" x-transition:enter="transition ease-out duration-300 delay-150" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-4" class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <x-heroicon-o-qr-code class="w-5 h-5 text-primary-500/70" />
                        </div>
                        <input 
                            type="text" 
                            id="input-document"
                            wire:model="scanned_document" 
                            wire:keydown.enter="submitDocumentScan"
                            class="w-full pl-10 pr-4 py-2 text-sm font-medium bg-white/50 dark:bg-gray-800/50 backdrop-blur-md border border-white/50 dark:border-gray-700/50 focus:outline-none focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 rounded-lg shadow-inner transition-all duration-300 uppercase placeholder:text-gray-400/70 text-gray-900 dark:text-white"
                            placeholder="KODE DOKUMEN..."
                            autofocus
                        >
                    </div>

                    <!-- Input Step 2: Scan QR 103 -->
                    <div x-show="step === 2" style="display: none;" x-transition:enter="transition ease-out duration-300 delay-150" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-4" class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <x-heroicon-o-document-magnifying-glass class="w-5 h-5 text-amber-500/70" />
                        </div>
                        <input 
                            type="text" 
                            id="input-103"
                            wire:model="scanned_103" 
                            wire:keydown.enter="submit103Scan"
                            class="w-full pl-10 pr-10 py-2 text-sm font-medium bg-amber-50/50 dark:bg-amber-900/20 backdrop-blur-md border border-amber-200/50 dark:border-amber-700/50 focus:outline-none focus:ring-2 focus:ring-amber-500/40 focus:border-amber-500 rounded-lg shadow-inner transition-all duration-300 uppercase placeholder:text-amber-700/30 dark:placeholder:text-amber-500/30 text-amber-900 dark:text-amber-100"
                            placeholder="KODE 103..."
                        >
                        <div class="absolute inset-y-0 right-1 flex items-center">
                            <button type="button" wire:click="resetScanState" class="p-1 text-gray-400 hover:text-red-500 transition-colors rounded-md hover:bg-white/50 dark:hover:bg-gray-800/50 backdrop-blur-sm" title="Batal">
                                <x-heroicon-s-x-circle class="w-5 h-5" />
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Tabel Riwayat Hari Ini -->
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/50 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                    Riwayat Transmittal Hari Ini
                </h3>
                <span class="px-3 py-1 bg-gray-100 dark:bg-gray-800 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300">
                    Total: {{ count($this->scannedItems) }}
                </span>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-gray-50 dark:bg-gray-800/80 border-b border-gray-200 dark:border-gray-800">
                        <tr>
                            <th class="px-6 py-4 font-semibold text-gray-600 dark:text-gray-300">Nomor PO</th>
                            <th class="px-6 py-4 font-semibold text-gray-600 dark:text-gray-300">QR 103</th>
                            <th class="px-6 py-4 font-semibold text-gray-600 dark:text-gray-300">Waktu Scan</th>
                            <th class="px-6 py-4 font-semibold text-right text-gray-600 dark:text-gray-300">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse ($this->scannedItems as $item)
                            @php
                                $qrCode = $item->deliveryOrderReceipt->qr_103_code ?? '';
                                $poNumber = '-';
                                if (!empty($qrCode)) {
                                    $parts = explode('-', $qrCode);
                                    $poNumber = $parts[0] ?? '-';
                                } else {
                                    // Fallback jika QR 103 kosong, coba ambil dari detail DO
                                    $detail = $item->deliveryOrderReceipt?->deliveryOrderReceiptDetails?->first();
                                    $poNumber = $detail?->purchaseOrderIssued?->purchase_order_no ?? '-';
                                }
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
                                            <x-heroicon-o-check-circle class="w-5 h-5 text-green-600 dark:text-green-400" />
                                        </div>
                                        <span class="font-bold text-gray-900 dark:text-white">
                                            {{ $poNumber }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-mono text-gray-600 dark:text-gray-400">
                                        {{ $item->deliveryOrderReceipt->qr_103_code ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-500 dark:text-gray-400">
                                    {{ $item->created_at->format('H:i:s') }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button 
                                        type="button" 
                                        x-on:click="itemToDelete = {{ $item->id }}; $dispatch('open-modal', { id: 'delete-confirmation' })"
                                        class="p-2 text-red-500 dark:text-red-400 hover:text-red-600 dark:hover:text-red-300 transition-colors rounded-lg hover:bg-red-50 dark:hover:bg-red-900/30"
                                        title="Hapus Dokumen"
                                    >
                                        <x-heroicon-o-trash class="w-5 h-5" />
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-400 dark:text-gray-500">
                                        <x-heroicon-o-inbox class="w-12 h-12 mb-3 opacity-50" />
                                        <p class="text-base font-medium">Belum ada dokumen yang dipindai.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    <!-- Delete Confirmation Modal -->
    <x-filament::modal id="delete-confirmation" width="sm">
        <x-slot name="heading">
            Hapus Dokumen
        </x-slot>

        <x-slot name="description">
            Yakin ingin menghapus dokumen ini dari transmittal? Tindakan ini juga akan menghapus riwayat QC terkait secara otomatis.
        </x-slot>

        <x-slot name="footer">
            <div class="flex justify-end gap-x-3">
                <x-filament::button color="gray" x-on:click="$dispatch('close-modal', { id: 'delete-confirmation' })">
                    Batal
                </x-filament::button>
                <x-filament::button color="danger" x-on:click="$wire.deleteItem(itemToDelete); $dispatch('close-modal', { id: 'delete-confirmation' })">
                    Hapus
                </x-filament::button>
            </div>
        </x-slot>
    </x-filament::modal>

    </div>
</x-filament-panels::page>

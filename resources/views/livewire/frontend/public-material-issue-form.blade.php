<div class="min-h-screen bg-slate-50 dark:bg-[#031525] transition-colors duration-500 pt-28 pb-24 font-sans relative overflow-hidden">
    <!-- Sophisticated Abstract Background -->
    <div class="absolute inset-0 z-0 pointer-events-none overflow-hidden flex items-center justify-center">
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-blue-100/40 via-transparent to-transparent dark:from-blue-900/20 dark:via-transparent dark:to-transparent"></div>
        <div class="absolute w-full h-full bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0MCIgaGVpZ2h0PSI0MCI+PGNpcmNsZSBjeD0iMjAiIGN5PSIyMCIgcj0iMSIgZmlsbD0icmdiYSgxNDgsIDE2MywgMTg0LCAwLjE1KSIvPjwvc3ZnPg==')] opacity-50 dark:opacity-20"></div>
        
        <!-- Glow Orbs -->
        <div class="absolute top-[-10%] left-[-10%] w-[50rem] h-[50rem] bg-[#F47920] rounded-full mix-blend-multiply filter blur-[140px] opacity-10 dark:opacity-5 animate-pulse" style="animation-duration: 15s;"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[50rem] h-[50rem] bg-blue-600 rounded-full mix-blend-multiply filter blur-[140px] opacity-10 dark:opacity-5 animate-pulse" style="animation-duration: 20s;"></div>
    </div>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        
        <!-- Header Section -->
        <div class="mb-16 text-center relative z-10">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/70 dark:bg-[#F47920]/10 border border-white dark:border-[#F47920]/20 shadow-sm backdrop-blur-md mb-6 transition-transform hover:-translate-y-0.5 duration-300">
                <span class="relative flex h-2.5 w-2.5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#F47920] opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-[#F47920]"></span>
                </span>
                <span class="text-xs font-bold tracking-widest uppercase text-slate-600 dark:text-[#F47920]">Portal Logistik Terpadu</span>
            </div>
            
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-slate-800 dark:text-white tracking-tight mb-6">
                Material Issue <br class="hidden sm:block"/>
                <span class="text-transparent bg-clip-text bg-linear-to-r from-[#F47920] to-[#BE5A27] dark:from-orange-400 dark:to-[#F47920]">Request</span>
            </h1>
            
            <p class="text-base sm:text-lg text-slate-500 dark:text-slate-400 max-w-2xl mx-auto font-medium leading-relaxed">
                Formulir resmi permintaan pengambilan barang. Pastikan kuantitas yang diminta tidak melebihi <span class="text-slate-800 dark:text-slate-200 font-bold border-b border-[#F47920]/30 pb-0.5">stok gudang (BOH)</span> yang tersedia.
            </p>
        </div>

        @if($showSuccessMessage)
            <div class="bg-green-500/10 border border-green-500/20 backdrop-blur-xl rounded-3xl p-6 sm:p-8 mb-12 flex items-start gap-5 animate-fade-in-up shadow-lg shadow-green-500/5">
                <div class="bg-gradient-to-br from-green-400 to-green-600 rounded-2xl p-3 flex-shrink-0 shadow-md shadow-green-500/20">
                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <div class="flex-1 pt-1">
                    <h4 class="text-xl font-bold text-green-800 dark:text-green-400 mb-2 tracking-tight">Pengajuan Berhasil!</h4>
                    <p class="text-green-700/80 dark:text-green-300/80 leading-relaxed">Material Issue Request Anda telah direkam ke dalam sistem dan akan segera divalidasi oleh tim gudang. Anda dapat melacak statusnya melalui dashboard.</p>
                </div>
                <button wire:click="$set('showSuccessMessage', false)" class="text-green-600/50 hover:text-green-800 dark:text-green-500/50 dark:hover:text-green-300 transition-colors pt-1">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        @endif

        <form wire:submit.prevent="confirmSubmit" class="relative">
            <!-- Vertical Connecting Line (Desktop Only) -->
            <div class="absolute left-[2.75rem] top-12 bottom-32 w-0.5 bg-gradient-to-b from-slate-200 via-slate-200 to-transparent dark:from-slate-800 dark:via-slate-800 dark:to-transparent hidden md:block z-0"></div>

            <!-- STEP 1: Informasi Peminta -->
            <div class="relative z-10 mb-12 group/step">
                <div class="flex items-center gap-6 mb-6">
                    <div class="flex flex-col items-center justify-center w-24 h-24 sm:w-20 sm:h-20 md:w-24 md:h-24 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-white/5 shadow-sm shrink-0 relative overflow-hidden transition-all duration-500 group-hover/step:shadow-md group-hover/step:border-[#F47920]/30">
                        <div class="absolute inset-0 bg-gradient-to-br from-[#F47920]/10 to-transparent opacity-0 group-hover/step:opacity-100 transition-opacity duration-500"></div>
                        <span class="text-3xl font-black text-slate-800 dark:text-white z-10">1</span>
                    </div>
                    <div>
                        <h3 class="text-2xl sm:text-3xl font-bold text-slate-800 dark:text-white tracking-tight">Informasi Peminta</h3>
                        <p class="text-sm sm:text-base text-slate-500 dark:text-slate-400 mt-1.5 font-medium">Data diri karyawan yang mengajukan permintaan.</p>
                    </div>
                </div>
                
                <div class="md:pl-[8.5rem]">
                    <div class="bg-white/60 dark:bg-slate-900/40 backdrop-blur-3xl border border-white/60 dark:border-white/5 rounded-[2.5rem] p-6 sm:p-8 lg:p-10 shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.2)] transition-all hover:shadow-[0_8px_40px_rgb(0,0,0,0.08)]">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                            <!-- Nama Peminta -->
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2.5">Nama Peminta <span class="text-[#F47920]">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    </div>
                                    <input type="text" wire:model.live.debounce.300ms="diminta_oleh" class="pl-12 w-full bg-white/50 dark:bg-black/20 backdrop-blur-md border border-slate-200/80 dark:border-white/10 text-slate-900 dark:text-white rounded-2xl py-4 focus:ring-2 focus:ring-[#F47920]/50 focus:border-[#F47920] focus:bg-white dark:focus:bg-slate-900/60 transition-all shadow-sm placeholder-slate-400" required placeholder="Contoh: John Doe">
                                </div>
                                @error('diminta_oleh') <span class="text-red-500 text-xs font-semibold mt-2 block">{{ $message }}</span> @enderror
                            </div>
                            
                            <!-- Diterima Oleh -->
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2.5">Diterima Oleh <span class="text-[#F47920]">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    </div>
                                    <input type="text" wire:model="diterima_oleh" class="pl-12 w-full bg-slate-100/50 dark:bg-black/40 border border-slate-200/50 dark:border-white/5 text-slate-500 dark:text-slate-400 rounded-2xl py-4 cursor-not-allowed font-medium" readonly placeholder="(Terisi otomatis)">
                                </div>
                                @error('diterima_oleh') <span class="text-red-500 text-xs font-semibold mt-2 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2.5">No. HP <span class="text-[#F47920]">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                    </div>
                                    <input type="text" wire:model="no_hp" class="pl-12 w-full bg-white/50 dark:bg-black/20 backdrop-blur-md border border-slate-200/80 dark:border-white/10 text-slate-900 dark:text-white rounded-2xl py-4 focus:ring-2 focus:ring-[#F47920]/50 focus:border-[#F47920] focus:bg-white dark:focus:bg-slate-900/60 transition-all shadow-sm placeholder-slate-400" required placeholder="0812...">
                                </div>
                                @error('no_hp') <span class="text-red-500 text-xs font-semibold mt-2 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2.5">Departemen <span class="text-[#F47920]">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                    </div>
                                    <input type="text" wire:model="departemen" class="pl-12 w-full bg-white/50 dark:bg-black/20 backdrop-blur-md border border-slate-200/80 dark:border-white/10 text-slate-900 dark:text-white rounded-2xl py-4 focus:ring-2 focus:ring-[#F47920]/50 focus:border-[#F47920] focus:bg-white dark:focus:bg-slate-900/60 transition-all shadow-sm placeholder-slate-400" required placeholder="Nama Departemen">
                                </div>
                                @error('departemen') <span class="text-red-500 text-xs font-semibold mt-2 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2.5">Bagian <span class="text-[#F47920]">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                    </div>
                                    <input type="text" wire:model="bagian" class="pl-12 w-full bg-white/50 dark:bg-black/20 backdrop-blur-md border border-slate-200/80 dark:border-white/10 text-slate-900 dark:text-white rounded-2xl py-4 focus:ring-2 focus:ring-[#F47920]/50 focus:border-[#F47920] focus:bg-white dark:focus:bg-slate-900/60 transition-all shadow-sm placeholder-slate-400" required placeholder="Sub-bagian">
                                </div>
                                @error('bagian') <span class="text-red-500 text-xs font-semibold mt-2 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- STEP 2: Dokumen & Keperluan -->
            <div class="relative z-10 mb-12 group/step">
                <div class="flex items-center gap-6 mb-6">
                    <div class="flex flex-col items-center justify-center w-24 h-24 sm:w-20 sm:h-20 md:w-24 md:h-24 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-white/5 shadow-sm shrink-0 relative overflow-hidden transition-all duration-500 group-hover/step:shadow-md group-hover/step:border-blue-500/30">
                        <div class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-transparent opacity-0 group-hover/step:opacity-100 transition-opacity duration-500"></div>
                        <span class="text-3xl font-black text-slate-800 dark:text-white z-10">2</span>
                    </div>
                    <div>
                        <h3 class="text-2xl sm:text-3xl font-bold text-slate-800 dark:text-white tracking-tight">Dokumen Referensi</h3>
                        <p class="text-sm sm:text-base text-slate-500 dark:text-slate-400 mt-1.5 font-medium">Referensi Purchase Order dan keperluan teknis.</p>
                    </div>
                </div>
                
                <div class="md:pl-[8.5rem]">
                    <div class="bg-white/60 dark:bg-slate-900/40 backdrop-blur-3xl border border-white/60 dark:border-white/5 rounded-[2.5rem] p-6 sm:p-8 lg:p-10 shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.2)] transition-all hover:shadow-[0_8px_40px_rgb(0,0,0,0.08)]">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2.5">Tanggal Pengajuan <span class="text-[#F47920]">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </div>
                                    <input type="date" wire:model="tanggal" class="pl-12 w-full bg-white/50 dark:bg-black/20 backdrop-blur-md border border-slate-200/80 dark:border-white/10 text-slate-900 dark:text-white rounded-2xl py-4 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 focus:bg-white dark:focus:bg-slate-900/60 transition-all shadow-sm" required>
                                </div>
                                @error('tanggal') <span class="text-red-500 text-xs font-semibold mt-2 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2.5">Cari & Pilih Nomor PO <span class="text-[#F47920]">*</span></label>
                                <div class="relative rounded-2xl shadow-sm overflow-hidden border border-slate-200/80 dark:border-white/10 focus-within:ring-2 focus-within:ring-blue-500/50 focus-within:border-blue-500 transition-all bg-white/50 dark:bg-black/20 backdrop-blur-md">
                                    <div class="absolute top-4 left-4 flex items-center pointer-events-none z-10">
                                        <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                    </div>
                                    <input type="text" wire:model.live.debounce.300ms="po_search" placeholder="Ketik No PO disini..." class="pl-12 w-full bg-transparent border-0 border-b border-slate-200/50 dark:border-white/5 text-slate-900 dark:text-white py-4 focus:ring-0 placeholder-slate-400">
                                    <select wire:model.live="purchase_order_issued_id" class="w-full bg-transparent border-0 text-slate-900 dark:text-white py-2 focus:ring-0 scrollbar-thin scrollbar-thumb-slate-200 dark:scrollbar-thumb-slate-700" required size="4">
                                        @forelse($available_pos as $po)
                                            <option value="{{ $po->id }}" class="py-2.5 px-4 hover:bg-blue-50/80 dark:hover:bg-blue-900/30 rounded-lg cursor-pointer mx-2 my-0.5 transition-colors">{{ $po->purchase_order_no }}</option>
                                        @empty
                                            <option disabled class="py-2.5 px-4 text-slate-400 italic mx-2">Ketik untuk mencari PO...</option>
                                        @endforelse
                                    </select>
                                </div>
                                @error('purchase_order_issued_id') <span class="text-red-500 text-xs font-semibold mt-2 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-5 lg:gap-6 mb-8">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2.5 uppercase tracking-wider">No. Reservasi</label>
                                <input type="text" wire:model="no_reservasi" class="w-full bg-white/50 dark:bg-black/20 backdrop-blur-md border border-slate-200/80 dark:border-white/10 text-slate-900 dark:text-white rounded-xl py-3.5 px-4 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 focus:bg-white dark:focus:bg-slate-900/60 transition-all shadow-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2.5 uppercase tracking-wider">No. JOR/WO</label>
                                <input type="text" wire:model="no_jor_wo" class="w-full bg-white/50 dark:bg-black/20 backdrop-blur-md border border-slate-200/80 dark:border-white/10 text-slate-900 dark:text-white rounded-xl py-3.5 px-4 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 focus:bg-white dark:focus:bg-slate-900/60 transition-all shadow-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2.5 uppercase tracking-wider">No. Alat</label>
                                <input type="text" wire:model="no_alat" class="w-full bg-white/50 dark:bg-black/20 backdrop-blur-md border border-slate-200/80 dark:border-white/10 text-slate-900 dark:text-white rounded-xl py-3.5 px-4 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 focus:bg-white dark:focus:bg-slate-900/60 transition-all shadow-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2.5 uppercase tracking-wider">Kode Biaya</label>
                                <input type="text" wire:model="kode_biaya" class="w-full bg-white/50 dark:bg-black/20 backdrop-blur-md border border-slate-200/80 dark:border-white/10 text-slate-900 dark:text-white rounded-xl py-3.5 px-4 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 focus:bg-white dark:focus:bg-slate-900/60 transition-all shadow-sm">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2.5">Digunakan Untuk <span class="text-[#F47920]">*</span></label>
                            <textarea wire:model="digunakan_untuk" rows="3" class="w-full bg-white/50 dark:bg-black/20 backdrop-blur-md border border-slate-200/80 dark:border-white/10 text-slate-900 dark:text-white rounded-2xl py-4 px-5 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 focus:bg-white dark:focus:bg-slate-900/60 transition-all shadow-sm placeholder-slate-400" required placeholder="Jelaskan secara singkat peruntukan material ini..."></textarea>
                            @error('digunakan_untuk') <span class="text-red-500 text-xs font-semibold mt-2 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- STEP 3: Item Material -->
            @if($purchase_order_issued_id)
            <div class="relative z-10 group/step animate-fade-in-up">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                    <div class="flex items-center gap-6">
                        <div class="flex flex-col items-center justify-center w-24 h-24 sm:w-20 sm:h-20 md:w-24 md:h-24 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-white/5 shadow-sm shrink-0 relative overflow-hidden transition-all duration-500 group-hover/step:shadow-md group-hover/step:border-green-500/30">
                            <div class="absolute inset-0 bg-gradient-to-br from-green-500/10 to-transparent opacity-0 group-hover/step:opacity-100 transition-opacity duration-500"></div>
                            <span class="text-3xl font-black text-slate-800 dark:text-white z-10">3</span>
                        </div>
                        <div>
                            <h3 class="text-2xl sm:text-3xl font-bold text-slate-800 dark:text-white tracking-tight">Daftar Material</h3>
                            <p class="text-sm sm:text-base text-slate-500 dark:text-slate-400 mt-1.5 font-medium">Pilih item dari PO dan tentukan jumlah pengambilan.</p>
                        </div>
                    </div>
                    <div class="md:pl-0 sm:self-center self-start pl-[5.5rem]">
                        <button type="button" wire:click="addDetail" class="group flex items-center gap-2.5 px-5 py-3 bg-white/80 dark:bg-white/5 hover:bg-green-50 dark:hover:bg-green-500/10 backdrop-blur-xl border border-slate-200/80 dark:border-white/10 hover:border-green-400 dark:hover:border-green-500/50 rounded-2xl text-slate-700 dark:text-slate-300 hover:text-green-600 dark:hover:text-green-400 font-bold transition-all shadow-sm">
                            <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path></svg>
                            Tambah Item
                        </button>
                    </div>
                </div>

                <div class="md:pl-[8.5rem] space-y-6">
                    @foreach($details as $index => $detail)
                        <div class="relative bg-white/70 dark:bg-slate-900/60 backdrop-blur-3xl border border-white/60 dark:border-white/10 rounded-[2.5rem] p-6 sm:p-8 shadow-[0_4px_20px_rgb(0,0,0,0.03)] dark:shadow-[0_4px_20px_rgb(0,0,0,0.2)] hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] transition-all group/card overflow-hidden">
                            
                            <!-- Item Header -->
                            <div class="flex justify-between items-center mb-6 pb-5 border-b border-slate-200/60 dark:border-white/5">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-2xl bg-slate-100 dark:bg-white/5 flex items-center justify-center text-slate-500 dark:text-slate-400 font-black text-sm border border-slate-200/50 dark:border-white/5">
                                        {{ $index + 1 }}
                                    </div>
                                    <h4 class="text-lg font-bold text-slate-700 dark:text-slate-200">Item Pengambilan</h4>
                                </div>
                                @if(count($details) > 1)
                                    <button type="button" wire:click="removeDetail({{ $index }})" class="p-2.5 text-slate-400 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-500/20 dark:hover:text-red-400 rounded-xl transition-colors" title="Hapus Item">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                @endif
                            </div>

                            <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
                                <!-- Pilih Item -->
                                <div class="xl:col-span-4">
                                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2.5 uppercase tracking-wider">Pilih Material <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <select wire:model.live="details.{{ $index }}.delivery_order_receipt_detail_id" class="w-full appearance-none bg-white/50 dark:bg-black/20 backdrop-blur-md border border-slate-200/80 dark:border-white/10 text-slate-900 dark:text-white rounded-2xl px-5 py-4 focus:ring-2 focus:ring-[#F47920]/50 focus:border-[#F47920] focus:bg-white dark:focus:bg-slate-900/60 transition-all font-semibold shadow-sm" required>
                                            <option value="">-- Pilih dari PO --</option>
                                            @foreach($available_po_items as $item)
                                                <option value="{{ $item->id }}">Item {{ $item->item_no }} &middot; {{ $item->material_code }}</option>
                                            @endforeach
                                        </select>
                                        <div class="absolute inset-y-0 right-0 flex items-center px-5 pointer-events-none text-slate-400">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                                        </div>
                                    </div>
                                    @error("details.{$index}.delivery_order_receipt_detail_id") <span class="text-red-500 text-xs font-semibold mt-2 block">{{ $message }}</span> @enderror
                                </div>

                                <!-- Deskripsi -->
                                <div class="xl:col-span-8">
                                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2.5 uppercase tracking-wider">Deskripsi & Lokasi Gudang</label>
                                    <div class="flex flex-col sm:flex-row gap-3">
                                        <input type="text" value="{{ $details[$index]['description'] }}" class="flex-1 bg-slate-100/50 dark:bg-black/40 border border-slate-200/50 dark:border-white/5 text-slate-500 dark:text-slate-400 rounded-2xl px-5 py-4 cursor-not-allowed font-medium" readonly placeholder="Deskripsi terisi otomatis...">
                                        <input type="text" value="{{ $details[$index]['location'] }}" class="sm:w-32 bg-slate-100/50 dark:bg-black/40 border border-slate-200/50 dark:border-white/5 text-slate-500 dark:text-slate-400 rounded-2xl px-5 py-4 cursor-not-allowed text-center font-bold tracking-wider" readonly placeholder="Lokasi">
                                    </div>
                                </div>

                                <!-- Kuantitas Section -->
                                <div class="xl:col-span-12 mt-2">
                                    <div class="bg-gradient-to-r from-slate-50 to-white dark:from-white/5 dark:to-transparent border border-slate-200/60 dark:border-white/5 rounded-3xl p-5 sm:p-6 flex flex-col md:flex-row items-center gap-6 md:gap-8 shadow-sm">
                                        
                                        <!-- BOH Panel -->
                                        <div class="flex flex-col items-center justify-center p-4 px-8 bg-white dark:bg-[#051F34]/80 rounded-2xl shadow-[0_4px_15px_rgb(0,0,0,0.05)] border border-slate-100 dark:border-white/5 min-w-[160px]">
                                            <span class="text-[11px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1">Stok Gudang (BOH)</span>
                                            <div class="flex items-baseline gap-1.5">
                                                <span class="text-3xl font-black text-slate-800 dark:text-slate-200">{{ $details[$index]['boh'] !== '' ? $details[$index]['boh'] : '-' }}</span>
                                                <span class="text-sm font-bold text-slate-400">{{ $details[$index]['uoi'] ?: '' }}</span>
                                            </div>
                                        </div>

                                        <div class="hidden md:block w-px h-16 bg-slate-200 dark:bg-white/10"></div>

                                        <!-- Input Qty -->
                                        <div class="flex-1 w-full">
                                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-3">Jumlah Pengambilan <span class="text-[#F47920]">*</span></label>
                                            <div class="relative flex items-stretch h-[3.5rem]">
                                                <input type="number" step="0.01" wire:model.live.debounce.300ms="details.{{ $index }}.diminta" class="block w-full h-full text-2xl font-black text-[#F47920] bg-white/50 dark:bg-black/20 backdrop-blur-md border border-slate-200/80 dark:border-white/10 rounded-l-2xl py-0 pl-6 focus:ring-2 focus:ring-[#F47920]/50 focus:border-[#F47920] focus:bg-white dark:focus:bg-slate-900/60 transition-all shadow-sm" required placeholder="0">
                                                <div class="flex items-center justify-center min-w-[5rem] h-full px-5 bg-gradient-to-br from-orange-50 to-orange-100 dark:from-[#F47920]/20 dark:to-[#F47920]/10 border border-l-0 border-slate-200/80 dark:border-white/10 rounded-r-2xl text-orange-600 dark:text-[#F47920] font-black text-lg shadow-sm">
                                                    {{ $details[$index]['uoi'] ?: 'UOI' }}
                                                </div>
                                            </div>
                                            @error("details.{$index}.diminta") <span class="text-red-500 text-xs font-semibold mt-2 block">{{ $message }}</span> @enderror
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Submit Area -->
            <div class="relative z-10 md:pl-[8.5rem] mt-12">
                <!-- Agreement Checkbox -->
                <div class="mb-8">
                    <label class="flex items-start gap-4 cursor-pointer group">
                        <div class="relative flex items-center justify-center shrink-0 mt-1">
                            <input type="checkbox" wire:model="agreement" class="peer appearance-none w-6 h-6 border-2 border-slate-300 dark:border-slate-600 rounded-lg bg-white/50 dark:bg-black/20 checked:bg-[#F47920] checked:border-[#F47920] focus:ring-2 focus:ring-offset-2 focus:ring-[#F47920] dark:focus:ring-offset-[#031525] transition-all cursor-pointer shadow-sm">
                            <svg class="absolute w-4 h-4 text-white opacity-0 peer-checked:opacity-100 pointer-events-none transition-opacity" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <span class="text-sm font-semibold text-slate-700 dark:text-slate-300 group-hover:text-slate-900 dark:group-hover:text-white transition-colors">
                                Saya menyatakan bahwa saya telah mengisi data di atas dengan sebenar-benarnya dan setuju untuk menandatangani dokumen serah terima pengambilan secara fisik di Gudang Transit (Receiving).
                            </span>
                            @error('agreement') <span class="text-red-500 text-xs font-bold mt-1.5 block">{{ $message }}</span> @enderror
                        </div>
                    </label>
                </div>

                <div class="pt-8 border-t border-slate-200 dark:border-white/10 flex flex-col-reverse sm:flex-row justify-between items-center gap-6">
                    <a href="{{ url('/') }}" wire:navigate class="w-full sm:w-auto text-center px-6 py-4 text-sm font-bold text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white hover:bg-slate-200/50 dark:hover:bg-white/5 rounded-2xl transition-all">
                        &larr; Kembali ke Beranda
                    </a>
                    
                    <button type="submit" class="w-full sm:w-auto group relative inline-flex items-center justify-center px-12 py-4 text-lg font-black text-white bg-gradient-to-r from-[#F47920] to-[#BE5A27] rounded-2xl overflow-hidden transition-all duration-300 shadow-[0_10px_20px_rgba(244,121,32,0.2)] hover:shadow-[0_15px_30px_rgba(244,121,32,0.4)] hover:-translate-y-1 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                        <span class="absolute inset-0 w-0 bg-white/20 transition-all duration-500 ease-out group-hover:w-full"></span>
                        <span class="relative flex items-center gap-3">
                            <svg wire:loading.remove wire:target="confirmSubmit" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                            <svg wire:loading wire:target="confirmSubmit" class="animate-spin -ml-1 h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span wire:loading.remove wire:target="confirmSubmit">Kirim Pengajuan</span>
                            <span wire:loading wire:target="confirmSubmit">Memproses Data...</span>
                        </span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Confirmation Modal -->
    @if($showConfirmModal)
    <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6" x-data="{ show: false }" x-init="setTimeout(() => show = true, 50)">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-[#031525]/80 backdrop-blur-sm transition-opacity duration-300 ease-out"
             :class="show ? 'opacity-100' : 'opacity-0'"
             wire:click="$set('showConfirmModal', false)"></div>
        
        <!-- Modal Dialog -->
        <div class="relative w-full max-w-lg bg-white dark:bg-slate-900 rounded-[2rem] shadow-2xl overflow-hidden border border-slate-200 dark:border-white/10 transition-all duration-300 ease-out transform"
             :class="show ? 'opacity-100 translate-y-0 scale-100' : 'opacity-0 translate-y-8 scale-95'">
            
            <!-- Decorative Header -->
            <div class="absolute top-0 inset-x-0 h-32 bg-gradient-to-br from-[#F47920]/20 to-orange-600/20 dark:from-[#F47920]/10 dark:to-orange-900/10"></div>
            
            <div class="relative p-8 sm:p-10">
                <div class="w-20 h-20 bg-white dark:bg-slate-800 rounded-3xl shadow-xl flex items-center justify-center mx-auto mb-6 border border-slate-100 dark:border-white/5 transform -rotate-6">
                    <div class="w-16 h-16 bg-gradient-to-br from-[#F47920] to-[#BE5A27] rounded-2xl flex items-center justify-center transform rotate-6">
                        <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                        </svg>
                    </div>
                </div>

                <div class="text-center">
                    <h3 class="text-2xl font-black text-slate-800 dark:text-white mb-3">Konfirmasi Pengajuan</h3>
                    <p class="text-slate-500 dark:text-slate-400 mb-8 font-medium">
                        Apakah Anda yakin ingin mengirim Material Issue Request ini? Pastikan kembali jumlah pengambilan sudah sesuai dengan yang dibutuhkan.
                    </p>
                </div>

                <div class="flex flex-col-reverse sm:flex-row gap-4">
                    <button type="button" wire:click="$set('showConfirmModal', false)" class="flex-1 px-6 py-3.5 text-sm font-bold text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-xl transition-colors text-center">
                        Batal
                    </button>
                    <button type="button" wire:click="submit" class="flex-1 px-6 py-3.5 text-sm font-bold text-white bg-gradient-to-r from-[#F47920] to-[#BE5A27] hover:from-orange-500 hover:to-orange-700 rounded-xl transition-all shadow-lg shadow-orange-500/30 flex items-center justify-center gap-2">
                        <span wire:loading.remove wire:target="submit">Ya, Kirim Sekarang</span>
                        <svg wire:loading wire:target="submit" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span wire:loading wire:target="submit">Memproses...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

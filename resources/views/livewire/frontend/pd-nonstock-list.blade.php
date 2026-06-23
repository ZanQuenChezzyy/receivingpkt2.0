<div class="min-h-screen bg-slate-50 dark:bg-[#031525] transition-colors duration-500 pt-28 pb-24 font-sans relative overflow-hidden">
    <!-- Sophisticated Abstract Background -->
    <div class="absolute inset-0 z-0 pointer-events-none overflow-hidden flex items-center justify-center">
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-blue-100/40 via-transparent to-transparent dark:from-blue-900/20 dark:via-transparent dark:to-transparent"></div>
        <div class="absolute w-full h-full bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0MCIgaGVpZ2h0PSI0MCI+PGNpcmNsZSBjeD0iMjAiIGN5PSIyMCIgcj0iMSIgZmlsbD0icmdiYSgxNDgsIDE2MywgMTg0LCAwLjE1KSIvPjwvc3ZnPg==')] opacity-50 dark:opacity-20"></div>
        
        <!-- Glow Orbs -->
        <div class="absolute top-[-10%] left-[-10%] w-[50rem] h-[50rem] bg-[#F47920] rounded-full mix-blend-multiply filter blur-[140px] opacity-10 dark:opacity-5 animate-pulse" style="animation-duration: 15s;"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[50rem] h-[50rem] bg-blue-600 rounded-full mix-blend-multiply filter blur-[140px] opacity-10 dark:opacity-5 animate-pulse" style="animation-duration: 20s;"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        
        <!-- Header Section -->
        <div class="mb-12 text-center relative z-10">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/70 dark:bg-[#F47920]/10 border border-white dark:border-[#F47920]/20 shadow-sm backdrop-blur-md mb-6 transition-transform hover:-translate-y-0.5 duration-300">
                <span class="relative flex h-2.5 w-2.5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#F47920] opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-[#F47920]"></span>
                </span>
                <span class="text-xs font-bold tracking-widest uppercase text-slate-600 dark:text-[#F47920]">ReceivingPKT Info</span>
            </div>
            
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-slate-800 dark:text-white tracking-tight mb-6">
                Material <br class="hidden sm:block"/>
                <span class="text-transparent bg-clip-text bg-linear-to-r from-[#F47920] to-[#BE5A27] dark:from-orange-400 dark:to-[#F47920]">Siap Ambil</span>
            </h1>
            
            <p class="text-base sm:text-lg text-slate-500 dark:text-slate-400 max-w-2xl mx-auto font-medium leading-relaxed">
                Daftar barang berjenis PD (Pembelian Langsung) dan Non-Stock yang sudah tiba di gudang Receiving.
            </p>
        </div>

        <!-- Segmented Controls / Tabs & Search -->
        <div class="mb-8 flex flex-col md:flex-row justify-between items-center gap-4">
            
            <!-- Tabs -->
            <div class="flex p-1 space-x-1 bg-slate-200/50 dark:bg-black/30 backdrop-blur-md rounded-2xl w-full md:w-auto">
                <button wire:click="setTab('PD')" 
                        class="flex-1 md:w-48 py-2.5 px-4 rounded-xl text-sm font-bold transition-all duration-300 {{ $activeTab === 'PD' ? 'bg-white dark:bg-[#0A4F86] text-slate-900 dark:text-white shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
                    Material PD
                </button>
                <button wire:click="setTab('NONSTOCK')" 
                        class="flex-1 md:w-48 py-2.5 px-4 rounded-xl text-sm font-bold transition-all duration-300 {{ $activeTab === 'NONSTOCK' ? 'bg-white dark:bg-[#0A4F86] text-slate-900 dark:text-white shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
                    Material Non-Stock
                </button>
            </div>

            <!-- Search -->
            <div class="relative w-full md:w-80 group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none z-20">
                    <svg class="h-5 w-5 text-slate-400 group-focus-within:text-[#F47920] transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input wire:model.live.debounce.300ms="search" type="text" 
                       class="pl-11 w-full bg-white/70 dark:bg-black/20 backdrop-blur-md border border-slate-200/80 dark:border-white/10 text-slate-900 dark:text-white rounded-2xl py-3 focus:ring-2 focus:ring-[#F47920]/50 focus:border-[#F47920] transition-all shadow-sm" 
                       placeholder="Cari PO, Deskripsi, dll...">
            </div>
        </div>

        <!-- Data Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 relative z-10">
            @forelse($this->items as $item)
                <div class="bg-white/60 dark:bg-white/5 backdrop-blur-xl border border-slate-200/80 dark:border-white/10 rounded-3xl p-6 shadow-xl hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 group flex flex-col justify-between h-full">
                    
                    <div>
                        <!-- Badge & Date -->
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex flex-col gap-2">
                                <span class="inline-flex w-max items-center px-2.5 py-1 rounded-lg text-xs font-bold {{ $activeTab === 'PD' ? 'bg-[#0A4F86]/10 text-[#0A4F86] dark:bg-[#0A4F86]/30 dark:text-blue-300' : 'bg-[#F47920]/10 text-[#F47920] dark:bg-[#F47920]/30 dark:text-orange-300' }} border {{ $activeTab === 'PD' ? 'border-[#0A4F86]/20' : 'border-[#F47920]/20' }}">
                                    {{ $activeTab === 'PD' ? 'PD' : 'NON-STOCK' }}
                                </span>
                                @if($item->deliveryOrderReceipt)
                                @php
                                    $isPending = $item->deliveryOrderReceipt->status === 'Pending';
                                    $docStatus = $isPending ? 'Pending' : ($item->deliveryOrderReceipt->stage ?: $item->deliveryOrderReceipt->status);
                                    
                                    $dateSource = $isPending && $item->deliveryOrderReceipt->pending_date 
                                        ? $item->deliveryOrderReceipt->pending_date 
                                        : $item->deliveryOrderReceipt->updated_at;
                                        
                                    $statusDate = $dateSource ? \Carbon\Carbon::parse($dateSource)->translatedFormat('d M Y') : '';
                                    
                                    $badgeColor = $isPending 
                                        ? 'bg-red-50 text-red-600 dark:bg-red-500/10 dark:text-red-400 border-red-200 dark:border-red-500/20' 
                                        : 'bg-slate-100 text-slate-600 dark:bg-white/10 dark:text-slate-300 border-slate-200/50 dark:border-white/10';
                                @endphp
                                <span class="inline-flex w-max items-center px-2 py-0.5 rounded text-[10px] font-bold border {{ $badgeColor }}">
                                    {{ $docStatus }} {{ $statusDate ? '(' . $statusDate . ')' : '' }}
                                </span>
                                @endif
                            </div>
                            <span class="text-xs font-medium text-slate-500 dark:text-slate-400 text-right">
                                Kedatangan:<br/>{{ $item->deliveryOrderReceipt?->received_date ? \Carbon\Carbon::parse($item->deliveryOrderReceipt->received_date)->translatedFormat('d M Y') : '-' }}
                            </span>
                        </div>

                        <!-- PO Info -->
                        <div class="mb-3">
                            <p class="text-[11px] uppercase tracking-wider font-bold text-slate-400 mb-0.5">PO Number</p>
                            <p class="text-lg font-black text-slate-800 dark:text-white">{{ $item->purchaseOrderIssued?->purchase_order_no ?? '-' }}</p>
                        </div>
                        
                        <!-- Material Info -->
                        <div class="mb-4 bg-slate-100/50 dark:bg-black/20 rounded-xl p-3 border border-slate-200/50 dark:border-white/5">
                            <p class="text-sm font-bold text-slate-800 dark:text-slate-200 mb-1 line-clamp-2" title="{{ $item->description }}">
                                {{ $item->description }}
                            </p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                Material Code: <span class="font-semibold">{{ $item->material_code ?? '-' }}</span>
                            </p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                Requisitioner: <span class="font-semibold">{{ $item->purchaseOrderIssued?->requisitioner ?? '-' }}</span>
                            </p>
                        </div>

                        <!-- Quantities -->
                        <div class="grid grid-cols-2 gap-3 mb-6">
                            <div class="bg-white/50 dark:bg-white/5 rounded-xl p-3 border border-slate-200/50 dark:border-white/5 text-center">
                                <p class="text-[10px] uppercase font-bold text-slate-500 mb-1">Tiba di Gudang</p>
                                <p class="text-xl font-black text-slate-800 dark:text-white">{{ number_format($item->quantity, 0, ',', '.') }} <span class="text-xs font-medium text-slate-500">{{ $item->uoi }}</span></p>
                            </div>
                            <div class="bg-green-500/10 dark:bg-green-500/20 rounded-xl p-3 border border-green-500/20 text-center">
                                <p class="text-[10px] uppercase font-bold text-green-600 dark:text-green-400 mb-1">Sisa Tersedia</p>
                                <p class="text-xl font-black text-green-700 dark:text-green-300">{{ number_format($item->quantity - $item->issued_quantity, 0, ',', '.') }} <span class="text-xs font-medium text-green-600/80">{{ $item->uoi }}</span></p>
                            </div>
                        </div>
                    </div>

                    <!-- Action Button -->
                    <a href="{{ route('frontend.mir.create', ['po' => $item->purchaseOrderIssued?->purchase_order_no]) }}" wire:navigate class="w-full flex items-center justify-center gap-2 py-3 px-4 bg-linear-to-r from-[#0A4F86] to-blue-700 text-white rounded-xl font-bold text-sm shadow-lg hover:shadow-blue-500/30 hover:-translate-y-0.5 transition-all duration-300">
                        <span>Buat MIR Sekarang</span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>
                </div>
            @empty
                <div class="col-span-full py-16 flex flex-col items-center justify-center text-center">
                    <div class="w-20 h-20 mb-4 rounded-full bg-slate-200/50 dark:bg-white/5 flex items-center justify-center border border-slate-300 dark:border-white/10">
                        <svg class="w-10 h-10 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-black text-slate-800 dark:text-white mb-2">Tidak Ada Data</h3>
                    <p class="text-slate-500 dark:text-slate-400 max-w-md">
                        Belum ada material {{ $activeTab === 'PD' ? 'PD' : 'Non-Stock' }} yang tersedia di gudang receiving, atau mungkin tidak cocok dengan pencarian Anda.
                    </p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-8 relative z-10">
            {{ $this->items->links() }}
        </div>
    </div>
</div>

<!-- Background Texture -->
    <div class="absolute inset-0 z-0 bg-[radial-gradient(#1e293b_1px,transparent_1px)] bg-size-[24px_24px] opacity-20"></div>

    <!-- Animated Glassmorphism Blobs -->
    <div class="absolute top-0 right-0 z-0 w-full h-full overflow-hidden pointer-events-none">
        <!-- Top Right Navy Glow -->
        <div class="absolute -top-[10%] -right-[5%] w-[50%] h-[60%] rounded-full bg-linear-to-bl from-[#0A4F86]/40 via-[#0A4F86]/10 to-transparent blur-[120px] animate-pulse duration-3000"></div>
        <!-- Bottom Left Orange Glow -->
        <div class="absolute -bottom-[10%] -left-[10%] w-[40%] h-[50%] rounded-full bg-linear-to-tr from-[#F47920]/30 via-[#F47920]/5 to-transparent blur-[120px] animate-pulse duration-3000" style="animation-delay: 2s;"></div>
        <!-- Center Subtle Blue -->
        <div class="absolute top-[30%] left-[30%] w-[30%] h-[40%] rounded-full bg-[#0A4F86]/20 blur-[150px]"></div>
    </div>
<main class="relative z-10 flex items-center justify-center min-h-screen pt-28 pb-12">
        <div class="container mx-auto px-5 md:px-12 lg:px-24 flex flex-col-reverse lg:flex-row items-center gap-12 lg:gap-12">

            <!-- Left Column: Text & CTA -->
            <div class="w-full lg:w-5/12 flex flex-col items-start text-left mt-8 lg:mt-0">
                <!-- Glowing Badge -->
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-slate-50 dark:bg-white/5 backdrop-blur-md border border-[#F47920]/30 shadow-[0_0_15px_rgba(244,121,32,0.15)] text-[#F47920] text-xs font-black tracking-widest uppercase mb-6 sm:mb-8">
                    <span class="relative flex w-2 h-2 sm:w-2.5 sm:h-2.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#F47920] opacity-75"></span>
                        <span class="relative inline-flex rounded-full w-2 h-2 sm:w-2.5 sm:h-2.5 bg-[#F47920]"></span>
                    </span>
                    ReceivingPKT v2.0
                </div>

                <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-black leading-[1.1] text-slate-800 dark:text-white mb-6 tracking-tight">
                    Otomasi Logistik <br class="hidden sm:block">
                    <span class="text-transparent bg-clip-text bg-linear-to-r from-[#F47920] to-[#ff9b52] relative inline-block">
                        Inventaris
                        <svg class="absolute w-full h-2 sm:h-3 -bottom-2 left-0 text-[#0A4F86]/80" viewBox="0 0 100 10" preserveAspectRatio="none">
                            <path d="M0 5 Q 50 10 100 5" stroke="currentColor" stroke-width="4" fill="none" stroke-linecap="round" />
                        </svg>
                    </span>
                </h1>

                <p class="text-base sm:text-lg text-slate-600 dark:text-slate-300 mb-10 max-w-lg leading-relaxed font-medium">
                    Platform terpadu untuk memantau pergerakan material, mempercepat proses administrasi di <span class="text-[#F47920] font-bold">receiving</span>, dan memastikan akurasi data gudang secara presisi.
                </p>

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row items-center gap-4 w-full sm:w-auto">
                    <a href="{{ filament()->getLoginUrl() }}" wire:navigate
                        class="w-full sm:w-auto flex items-center justify-center gap-2 px-8 py-4 text-sm sm:text-base font-bold text-white bg-linear-to-r from-[#F47920] to-[#BE5A27] rounded-2xl shadow-[0_10px_30px_-10px_rgba(244,121,32,0.8)] hover:shadow-[0_15px_40px_-10px_rgba(244,121,32,1)] hover:-translate-y-1 transition-all duration-300">
                        Masuk Dashboard
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                    </a>
                    <button type="button" class="w-full sm:w-auto flex items-center justify-center px-8 py-4 text-sm sm:text-base font-bold text-slate-700 dark:text-slate-200 bg-slate-50 dark:bg-white/5 backdrop-blur-md border border-slate-200 dark:border-white/10 rounded-2xl hover:bg-slate-200 dark:hover:bg-white/10 hover:border-white/20 transition-all duration-300">
                        Cek Status
                    </button>
                </div>

                <!-- Glass Cards -->
                <div class="mt-12 sm:mt-16 w-full grid grid-cols-3 gap-3 sm:gap-5 max-w-md">
                    <!-- Service Card 1 -->
                    <div class="bg-slate-50 dark:bg-white/5 backdrop-blur-xl p-4 sm:p-5 rounded-2xl border border-slate-200 dark:border-white/10 shadow-[0_8px_32px_rgba(0,0,0,0.2)] hover:bg-slate-200 dark:hover:bg-white/10 transition-colors">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-[#0A4F86]/10 dark:bg-[#0A4F86]/50 border border-[#0A4F86]/20 dark:border-[#0A4F86] flex items-center justify-center mb-3">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-[#0A4F86] dark:text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        </div>
                        <p class="text-lg sm:text-xl font-black text-slate-800 dark:text-white">ZSM</p>
                        <p class="text-[10px] sm:text-xs text-slate-500 dark:text-slate-400 font-bold uppercase tracking-widest mt-1">Support</p>
                    </div>

                    <!-- Service Card 2 -->
                    <div class="bg-slate-50 dark:bg-white/5 backdrop-blur-xl p-4 sm:p-5 rounded-2xl border border-slate-200 dark:border-white/10 shadow-[0_8px_32px_rgba(0,0,0,0.2)] hover:bg-slate-200 dark:hover:bg-white/10 transition-colors">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-[#F47920]/10 dark:bg-[#F47920]/20 border border-[#F47920]/30 dark:border-[#F47920]/50 flex items-center justify-center mb-3">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-[#F47920]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        <p class="text-lg sm:text-xl font-black text-slate-800 dark:text-white">ZSP</p>
                        <p class="text-[10px] sm:text-xs text-slate-500 dark:text-slate-400 font-bold uppercase tracking-widest mt-1">Sparepart</p>
                    </div>

                    <!-- Service Card 3 -->
                    <div class="bg-slate-50 dark:bg-white/5 backdrop-blur-xl p-4 sm:p-5 rounded-2xl border border-slate-200 dark:border-white/10 shadow-[0_8px_32px_rgba(0,0,0,0.2)] hover:bg-slate-200 dark:hover:bg-white/10 transition-colors">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-[#0A4F86]/10 dark:bg-[#0A4F86]/50 border border-[#0A4F86]/20 dark:border-[#0A4F86] flex items-center justify-center mb-3">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-[#0A4F86] dark:text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                        </div>
                        <p class="text-lg sm:text-xl font-black text-slate-800 dark:text-white">ZRM</p>
                        <p class="text-[10px] sm:text-xs text-slate-500 dark:text-slate-400 font-bold uppercase tracking-widest mt-1">Raw Mat.</p>
                    </div>
                </div>
            </div>

            <!-- Right Column: Dashboard Illustration -->
            <div class="w-full lg:w-7/12 relative mt-4 md:mt-0">
                <div class="relative w-full max-w-full sm:max-w-2xl mx-auto px-2 sm:px-0">
                    
                    <!-- Dark Glass Dashboard Frame -->
                    <div class="relative bg-white/80 dark:bg-[#051F34]/60 backdrop-blur-2xl rounded-3xl sm:rounded-[2rem] shadow-[0_20px_50px_rgba(0,0,0,0.5)] border border-slate-200 dark:border-white/10 overflow-hidden flex flex-col transform perspective-1000 rotate-y-[-5deg] rotate-x-[2deg] transition-transform hover:rotate-y-0 hover:rotate-x-0 duration-700">
                        <!-- Header Bar -->
                        <div class="h-10 sm:h-14 bg-slate-50 dark:bg-white/5 border-b border-slate-200 dark:border-white/10 flex items-center px-4 sm:px-6 gap-2">
                            <div class="w-2.5 h-2.5 sm:w-3 sm:h-3 rounded-full bg-red-500/80"></div>
                            <div class="w-2.5 h-2.5 sm:w-3 sm:h-3 rounded-full bg-amber-500/80"></div>
                            <div class="w-2.5 h-2.5 sm:w-3 sm:h-3 rounded-full bg-green-500/80"></div>
                            <div class="hidden sm:flex ml-4 px-4 py-1.5 rounded-lg bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 items-center gap-3 w-56">
                                <svg class="w-3.5 h-3.5 text-slate-500 dark:text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                <div class="w-full h-1.5 bg-white/10 rounded-full"></div>
                            </div>
                        </div>

                        <!-- Dashboard Content -->
                        <div class="p-5 sm:p-8 flex-1 flex flex-col gap-5 sm:gap-6 bg-linear-to-b from-transparent to-black/20">
                            <!-- Top Stats -->
                            <div class="grid grid-cols-3 gap-3 sm:gap-5">
                                <div class="h-24 sm:h-32 rounded-2xl bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 p-4 sm:p-5 flex flex-col justify-between relative overflow-hidden group">
                                    <div class="absolute -top-10 -right-10 w-24 h-24 bg-[#0A4F86]/40 blur-xl rounded-full group-hover:bg-[#0A4F86]/60 transition-colors"></div>
                                    <div>
                                        <div class="text-xl sm:text-3xl font-black text-slate-800 dark:text-white">1,284</div>
                                        <div class="w-12 sm:w-20 h-1.5 mt-2 rounded-full bg-[#0A4F86]"></div>
                                    </div>
                                    <p class="text-[10px] sm:text-xs text-slate-500 dark:text-slate-400 font-medium">Total MIGO</p>
                                </div>
                                <div class="h-24 sm:h-32 rounded-2xl bg-[#F47920]/10 border border-[#F47920]/30 p-4 sm:p-5 flex flex-col justify-between relative overflow-hidden group shadow-[inset_0_0_20px_rgba(244,121,32,0.1)]">
                                    <div class="absolute -top-10 -right-10 w-24 h-24 bg-[#F47920]/30 blur-xl rounded-full group-hover:bg-[#F47920]/50 transition-colors"></div>
                                    <div>
                                        <div class="text-xl sm:text-3xl font-black text-slate-800 dark:text-white">856</div>
                                        <div class="w-12 sm:w-20 h-1.5 mt-2 rounded-full bg-[#F47920]"></div>
                                    </div>
                                    <p class="text-[10px] sm:text-xs text-[#F47920] font-medium">Material Issued</p>
                                </div>
                                <div class="h-24 sm:h-32 rounded-2xl bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 p-4 sm:p-5 flex flex-col justify-between shadow-sm">
                                    <div>
                                        <div class="text-xl sm:text-3xl font-black text-slate-800 dark:text-white">24</div>
                                        <div class="w-10 sm:w-16 h-1.5 mt-2 rounded-full bg-slate-600"></div>
                                    </div>
                                    <p class="text-[10px] sm:text-xs text-slate-500 dark:text-slate-400 font-medium">Pending QC</p>
                                </div>
                            </div>

                            <!-- Main Chart Area -->
                            <div class="flex-1 rounded-2xl bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 shadow-inner p-5 sm:p-6 flex flex-col min-h-36 sm:min-h-0 relative overflow-hidden">
                                <div class="absolute bottom-0 left-0 w-full h-1/2 bg-linear-to-t from-[#0A4F86]/20 to-transparent"></div>
                                
                                <div class="flex justify-between items-center mb-6 relative z-10">
                                    <div class="w-24 sm:w-32 h-3 sm:h-4 bg-white/10 rounded-full"></div>
                                    <div class="w-12 sm:w-16 h-5 sm:h-6 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-md"></div>
                                </div>
                                
                                <!-- Mock Bars -->
                                <div class="flex items-end gap-2 sm:gap-4 h-24 sm:h-36 mt-auto relative z-10">
                                    <div class="w-full bg-white/10 rounded-t-md h-[40%] hover:bg-white/20 transition-colors"></div>
                                    <div class="w-full bg-white/20 rounded-t-md h-[70%] hover:bg-white/30 transition-colors"></div>
                                    <div class="w-full bg-[#0A4F86]/80 rounded-t-md h-[50%] hover:bg-[#0A4F86] transition-colors border-t border-[#0A4F86]/50"></div>
                                    <div class="w-full bg-linear-to-t from-[#BE5A27] to-[#F47920] rounded-t-md h-[90%] shadow-[0_0_15px_rgba(244,121,32,0.6)] relative group cursor-pointer">
                                        <div class="absolute -top-10 left-1/2 -translate-x-1/2 bg-white text-[#051F34] text-[10px] sm:text-xs font-black px-2.5 py-1.5 rounded-md shadow-lg opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
                                            Puncak MIGO
                                        </div>
                                    </div>
                                    <div class="w-full bg-[#0A4F86]/60 rounded-t-md h-[60%] hover:bg-[#0A4F86]/80 transition-colors"></div>
                                    <div class="w-full bg-white/10 rounded-t-md h-[30%] hover:bg-white/20 transition-colors"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Floating Glass Widget 1 -->
                    <div class="absolute -bottom-5 left-0 sm:-bottom-10 sm:-left-12 bg-white/90 dark:bg-[#051F34]/80 backdrop-blur-2xl p-3 sm:p-5 rounded-2xl sm:rounded-3xl shadow-xl dark:shadow-[0_10px_30px_rgba(0,0,0,0.5)] border border-slate-200 dark:border-white/10 flex items-center gap-3 sm:gap-4 animate-bounce hover:scale-105 transition-transform" style="animation-duration: 4s;">
                        <div class="relative flex h-10 w-10 sm:h-14 sm:w-14 items-center justify-center rounded-xl bg-[#F47920]/10 dark:bg-[#F47920]/20 border border-[#F47920]/30 dark:border-[#F47920]/50">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-xl bg-[#F47920] opacity-30"></span>
                            <svg class="w-5 h-5 sm:w-7 sm:h-7 text-[#F47920]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <div class="pr-2 sm:pr-4">
                            <p class="text-sm sm:text-base font-black text-slate-800 dark:text-white tracking-wide">Real-time SAP</p>
                            <p class="text-[10px] sm:text-xs font-medium text-slate-500 dark:text-slate-400 mt-0.5">Sinkronisasi otomatis</p>
                        </div>
                    </div>

                    <!-- Floating Glass Widget 2 -->
                    <div class="absolute -top-6 right-2 sm:top-10 sm:-right-10 bg-white/90 dark:bg-white/10 backdrop-blur-xl px-4 py-3 sm:px-6 sm:py-4 rounded-xl sm:rounded-2xl shadow-xl border border-slate-200 dark:border-white/20 flex items-center gap-3 transform hover:-rotate-3 transition-transform z-20">
                        <div class="w-2 h-2 sm:w-3 sm:h-3 rounded-full bg-green-400 animate-pulse shadow-[0_0_10px_rgba(74,222,128,0.8)]"></div>
                        <p class="text-xs sm:text-sm font-bold text-slate-800 dark:text-white tracking-wider">MB51 Tracking Active</p>
                    </div>

                </div>
            </div>
        </div>
    </main>
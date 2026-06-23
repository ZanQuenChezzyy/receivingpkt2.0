<nav x-data="{ mobileMenuOpen: false }"
        class="fixed top-0 w-full z-50 bg-white/80 dark:bg-[#051F34]/60 backdrop-blur-xl border-b border-slate-200 dark:border-white/10 transition-all duration-300 px-5 py-4 md:px-12 lg:px-24 shadow-[0_4px_30px_rgba(0,0,0,0.1)]">

        <div class="flex justify-between items-center w-full">
            <div class="flex items-center">
                <a href="/" wire:navigate class="block">
                    <!-- Gunakan logo putih untuk tema gelap -->
                    <img :src="isDark ? '{{ asset('images/logo/receiving_white.png') }}' : '{{ asset('images/logo/receiving_dark.png') }}'" alt="Receiving PKT Logo"
                        class="h-7 sm:h-8 md:h-10 w-auto object-contain transition-transform hover:scale-105 duration-300 drop-shadow-md">
                </a>
            </div>

            <div class="nav-desktop hidden lg:flex items-center gap-8 z-50">
                <!-- Dropdown 1: Cek Status -->
                <div x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false" class="relative group h-full py-2">
                    <button @click="open = !open" class="flex items-center gap-1.5 text-sm font-bold text-slate-600 dark:text-slate-300 hover:text-[#F47920] dark:hover:text-white transition-colors relative pb-1">
                        Daftar Material
                        <svg :class="{'rotate-180': open}" class="w-4 h-4 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                        </svg>
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-linear-to-r from-[#F47920] to-[#BE5A27] transition-all duration-300 group-hover:w-full shadow-[0_0_8px_rgba(244,121,32,0.8)]"></span>
                    </button>

                    <div x-show="open" style="display: none;" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-y-3" x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-3"
                        class="absolute left-0 mt-2 w-56 bg-white/95 dark:bg-[#051F34]/95 backdrop-blur-2xl rounded-2xl shadow-[0_20px_50px_-10px_rgba(0,0,0,0.1)] dark:shadow-[0_20px_50px_-10px_rgba(0,0,0,0.5)] border border-slate-200/60 dark:border-white/10 overflow-hidden z-[100] p-2">
                        <a href="{{ route('frontend.list-material') }}" wire:navigate class="group flex items-center gap-3 px-3 py-2.5 text-sm font-bold text-slate-600 dark:text-slate-300 hover:text-[#F47920] dark:hover:text-white hover:bg-[#F47920]/10 dark:hover:bg-white/10 rounded-xl transition-all duration-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-300 dark:bg-slate-600 group-hover:bg-[#F47920] transition-colors"></span>
                            Material PD
                        </a>
                        <a href="{{ route('frontend.list-material', ['activeTab' => 'NONSTOCK']) }}" wire:navigate class="group flex items-center gap-3 px-3 py-2.5 text-sm font-bold text-slate-600 dark:text-slate-300 hover:text-[#F47920] dark:hover:text-white hover:bg-[#F47920]/10 dark:hover:bg-white/10 rounded-xl transition-all duration-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-300 dark:bg-slate-600 group-hover:bg-[#F47920] transition-colors"></span>
                            Material Non-Stock
                        </a>
                    </div>
                </div>

                <!-- Dropdown 2: Pengambilan Barang -->
                <div x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false" class="relative group h-full py-2">
                    <button @click="open = !open" class="flex items-center gap-1.5 text-sm font-bold text-slate-600 dark:text-slate-300 hover:text-[#F47920] dark:hover:text-white transition-colors relative pb-1">
                        Pengambilan Barang
                        <svg :class="{'rotate-180': open}" class="w-4 h-4 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                        </svg>
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-linear-to-r from-[#F47920] to-[#BE5A27] transition-all duration-300 group-hover:w-full shadow-[0_0_8px_rgba(244,121,32,0.8)]"></span>
                    </button>

                    <div x-show="open" style="display: none;" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-y-3" x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-3"
                        class="absolute left-0 mt-2 w-56 bg-white/95 dark:bg-[#051F34]/95 backdrop-blur-2xl rounded-2xl shadow-[0_20px_50px_-10px_rgba(0,0,0,0.1)] dark:shadow-[0_20px_50px_-10px_rgba(0,0,0,0.5)] border border-slate-200/60 dark:border-white/10 overflow-hidden z-[100] p-2">
                        <a href="{{ route('frontend.mir.create') }}" wire:navigate class="group flex items-center gap-3 px-3 py-2.5 text-sm font-bold text-slate-600 dark:text-slate-300 hover:text-[#F47920] dark:hover:text-white hover:bg-[#F47920]/10 dark:hover:bg-white/10 rounded-xl transition-all duration-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-300 dark:bg-slate-600 group-hover:bg-[#F47920] transition-colors"></span>
                            Material Issue (MIR)
                        </a>
                        <a href="{{ filament()->getLoginUrl() }}" wire:navigate class="group flex items-center gap-3 px-3 py-2.5 text-sm font-bold text-slate-600 dark:text-slate-300 hover:text-[#F47920] dark:hover:text-white hover:bg-[#F47920]/10 dark:hover:bg-white/10 rounded-xl transition-all duration-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-300 dark:bg-slate-600 group-hover:bg-[#F47920] transition-colors"></span>
                            Reservasi
                        </a>
                    </div>
                </div>
            </div>

            <!-- Login Button (Desktop) & Hamburger Icon (Mobile) -->
            <div class="flex items-center gap-2 md:gap-4">

                <!-- Theme Toggler -->
                <button @click="isDark = !isDark" class="p-2 rounded-xl transition-all duration-300 bg-slate-200 dark:bg-white/10 text-slate-700 dark:text-yellow-400 hover:bg-slate-300 dark:hover:bg-white/20">
                    <svg x-show="isDark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    <svg x-show="!isDark" style="display: none;" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                </button>

                <a href="{{ filament()->getLoginUrl() }}" wire:navigate
                    class="group relative inline-flex items-center justify-center px-5 py-2.5 text-sm font-bold text-slate-800 dark:text-white bg-slate-200/80 dark:bg-white/10 backdrop-blur-md border border-slate-300 dark:border-white/20 rounded-xl overflow-hidden transition-all hover:bg-transparent dark:hover:bg-white/20 hover:border-[#F47920]/50 dark:hover:border-white/40 hover:shadow-[0_0_15px_rgba(244,121,32,0.3)] hidden sm:inline-flex">
                    <span class="absolute inset-0 w-0 bg-linear-to-r from-[#F47920] to-[#BE5A27] transition-all duration-300 ease-out group-hover:w-full"></span>
                    <span class="relative flex items-center gap-2 group-hover:text-white transition-colors duration-300">
                        <span class="hidden sm:inline">Akses Sistem</span>
                        <svg class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </span>
                </a>

                <!-- Hamburger Button (Visible on Mobile & Tablet) -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="nav-mobile-btn text-slate-600 dark:text-slate-300 hover:text-white focus:outline-none p-1">
                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16m-7 6h7"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile/Tablet Menu Overlay & Drawer (Dark Glass) -->
        <div x-show="mobileMenuOpen" style="display: none;" x-transition.opacity class="lg:hidden fixed inset-0 z-40 bg-slate-200/80 dark:bg-[#031525]/80 backdrop-blur-sm" @click="mobileMenuOpen = false"></div>
            
        <div x-show="mobileMenuOpen" style="display: none;"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
            class="lg:hidden fixed top-0 right-0 z-50 w-[85%] sm:w-[60%] md:w-80 h-screen bg-white/95 dark:bg-[#051F34]/90 backdrop-blur-2xl shadow-2xl border-l border-slate-200 dark:border-white/10 overflow-y-auto flex flex-col">
            
            <div class="px-5 py-5 border-b border-slate-200 dark:border-white/10 flex items-center justify-between sticky top-0 bg-slate-100/95 dark:bg-[#051F34]/50 backdrop-blur-xl z-10">
                <img :src="isDark ? '{{ asset('images/logo/receiving_white.png') }}' : '{{ asset('images/logo/receiving_dark.png') }}'" alt="Receiving PKT Logo" class="h-6 w-auto object-contain drop-shadow-md">
                <button @click="mobileMenuOpen = false" class="p-2 -mr-2 text-slate-500 dark:text-slate-400 hover:text-[#F47920] bg-slate-50 dark:bg-white/5 hover:bg-slate-200 dark:hover:bg-white/10 rounded-full transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="px-5 py-6 flex flex-col gap-4">
                <h3 class="text-xs font-black text-[#F47920] uppercase tracking-widest mb-1 opacity-80">Menu Utama</h3>

                <div x-data="{ accOpen: false }" class="border border-slate-200 dark:border-white/10 rounded-2xl bg-slate-50 dark:bg-white/5 shadow-sm overflow-hidden">
                    <button @click="accOpen = !accOpen" class="w-full flex justify-between items-center px-4 py-3.5 text-sm font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:bg-white/5 transition-colors">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#F47920]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                            Cek Status Material
                        </span>
                        <svg :class="{'rotate-180': accOpen}" class="w-4 h-4 transition-transform text-[#F47920]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="accOpen" style="display: none;" class="px-4 pb-4 flex flex-col gap-1.5 pt-2 border-t border-white/5">
                        <a href="{{ route('frontend.list-material') }}" wire:navigate class="block px-3 py-2.5 text-sm font-medium text-slate-500 dark:text-slate-400 hover:text-[#F47920] dark:hover:text-white hover:bg-slate-200 dark:hover:bg-white/10 rounded-xl transition-colors">Material PD</a>
                        <a href="{{ route('frontend.list-material', ['activeTab' => 'NONSTOCK']) }}" wire:navigate class="block px-3 py-2.5 text-sm font-medium text-slate-500 dark:text-slate-400 hover:text-[#F47920] dark:hover:text-white hover:bg-slate-200 dark:hover:bg-white/10 rounded-xl transition-colors">Material Non Stock</a>
                    </div>
                </div>

                <div x-data="{ accOpen: false }" class="border border-slate-200 dark:border-white/10 rounded-2xl bg-slate-50 dark:bg-white/5 shadow-sm overflow-hidden">
                    <button @click="accOpen = !accOpen" class="w-full flex justify-between items-center px-4 py-3.5 text-sm font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:bg-white/5 transition-colors">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#F47920]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            Pengambilan Barang
                        </span>
                        <svg :class="{'rotate-180': accOpen}" class="w-4 h-4 transition-transform text-[#F47920]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="accOpen" style="display: none;" class="px-4 pb-4 flex flex-col gap-1.5 pt-2 border-t border-white/5">
                        <a href="{{ route('frontend.mir.create') }}" wire:navigate class="block px-3 py-2.5 text-sm font-medium text-slate-500 dark:text-slate-400 hover:text-[#F47920] dark:hover:text-white hover:bg-slate-200 dark:hover:bg-white/10 rounded-xl transition-colors">Material Issue (MIR)</a>
                        <a href="{{ filament()->getLoginUrl() }}" class="block px-3 py-2.5 text-sm font-medium text-slate-500 dark:text-slate-400 hover:text-white hover:bg-slate-200 dark:hover:bg-white/10 rounded-xl transition-colors">Reservasi</a>
                    </div>
                </div>
            </div>

            <div class="mt-auto p-5 border-t border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5">
                <a href="{{ filament()->getLoginUrl() }}" wire:navigate
                    class="w-full flex items-center justify-center gap-2 px-5 py-3.5 text-sm font-bold text-white bg-linear-to-r from-[#F47920] to-[#BE5A27] rounded-xl shadow-[0_8px_20px_-6px_rgba(244,121,32,0.5)] hover:-translate-y-0.5 transition-transform">
                    <span>Akses Sistem</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                    </svg>
                </a>
            </div>
        </div>
    </nav>
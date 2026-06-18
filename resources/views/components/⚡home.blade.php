<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('components.layouts.frontend')] #[Title('Welcome - Receiving PKT')] class extends Component {
    // Logika komponen Anda di sini
};
?>

<div class="relative w-full min-h-screen bg-slate-50 selection:bg-[#F47920] selection:text-white">
    <livewire:chatbot-widget />
    <div class="absolute inset-0 z-0 bg-[radial-gradient(#cbd5e1_1px,transparent_1px)] bg-size-[24px_24px] opacity-40">
    </div>

    <div class="absolute top-0 right-0 z-0 w-full h-full overflow-hidden pointer-events-none">
        <div
            class="absolute top-[20%] right-[10%] w-[60%] h-[70%] rounded-full bg-linear-to-bl from-[#0A4F86]/15 via-[#0A4F86]/5 to-transparent blur-[100px]">
        </div>
        <div
            class="absolute bottom-[0%] left-[10%] w-[50%] h-[60%] rounded-full bg-linear-to-tr from-[#F47920]/15 via-[#F47920]/5 to-transparent blur-[100px]">
        </div>
    </div>

    <nav x-data="{ mobileMenuOpen: false }"
        class="fixed top-0 w-full z-50 backdrop-blur-md bg-white/80 md:bg-white/70 border-b border-white/20 transition-all duration-300 px-5 py-4 md:px-12 lg:px-24 shadow-[0_4px_30px_rgba(0,0,0,0.02)]">

        <div class="flex justify-between items-center w-full">
            <div class="flex items-center">
                <a href="/" wire:navigate class="block">
                    <img src="{{ asset('images/logo/receiving_dark.png') }}" alt="Receiving PKT Logo"
                        class="h-7 sm:h-8 md:h-10 w-auto object-contain transition-transform hover:scale-105 duration-300">
                </a>
            </div>

            <div class="hidden md:flex items-center gap-8">
                <div x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false"
                    class="relative group">
                    <button @click="open = !open"
                        class="flex items-center gap-1.5 text-sm font-bold text-slate-600 hover:text-[#0A4F86] transition-colors relative pb-1">
                        Cek Status Material
                        <svg :class="{'rotate-180': open}" class="w-4 h-4 transition-transform duration-300" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                        </svg>
                        <span
                            class="absolute bottom-0 left-0 w-0 h-0.5 bg-[#F47920] transition-all duration-300 group-hover:w-full"></span>
                    </button>

                    <div x-show="open" style="display: none;" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-y-3"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-3"
                        class="absolute left-0 mt-1 w-48 bg-white/95 backdrop-blur-xl rounded-2xl shadow-[0_10px_40px_-10px_rgba(0,0,0,0.1)] border border-slate-100 overflow-hidden z-50 p-1.5">
                        <a href="#"
                            class="block px-4 py-2.5 text-sm font-bold text-slate-600 hover:text-[#0A4F86] hover:bg-[#0A4F86]/5 rounded-xl transition-colors">PD</a>
                        <a href="#"
                            class="block px-4 py-2.5 text-sm font-bold text-slate-600 hover:text-[#0A4F86] hover:bg-[#0A4F86]/5 rounded-xl transition-colors">NON
                            STOCK</a>
                    </div>
                </div>

                <!-- Dropdown 2: Pengambilan Barang -->
                <div x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false"
                    class="relative group">
                    <button @click="open = !open"
                        class="flex items-center gap-1.5 text-sm font-bold text-slate-600 hover:text-[#0A4F86] transition-colors relative pb-1">
                        Pengambilan Barang
                        <svg :class="{'rotate-180': open}" class="w-4 h-4 transition-transform duration-300" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                        </svg>
                        <span
                            class="absolute bottom-0 left-0 w-0 h-0.5 bg-[#F47920] transition-all duration-300 group-hover:w-full"></span>
                    </button>

                    <div x-show="open" style="display: none;" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-y-3"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-3"
                        class="absolute left-0 mt-1 w-48 bg-white/95 backdrop-blur-xl rounded-2xl shadow-[0_10px_40px_-10px_rgba(0,0,0,0.1)] border border-slate-100 overflow-hidden z-50 p-1.5">
                        <a href="#"
                            class="block px-4 py-2.5 text-sm font-bold text-slate-600 hover:text-[#0A4F86] hover:bg-[#0A4F86]/5 rounded-xl transition-colors">MIR</a>
                        <a href="#"
                            class="block px-4 py-2.5 text-sm font-bold text-slate-600 hover:text-[#0A4F86] hover:bg-[#0A4F86]/5 rounded-xl transition-colors">Reservasi</a>
                    </div>
                </div>
            </div>

            <!-- Login Button (Desktop) & Hamburger Icon (Mobile) -->
            <div class="flex items-center gap-4">
                <!-- Sembunyikan tulisan di mobile agar tidak sesak, hanya icon -->
                <a href="{{ filament()->getLoginUrl() }}" wire:navigate
                    class="group relative inline-flex items-center justify-center px-4 py-2 md:px-5 md:py-2.5 text-sm font-bold text-[#0A4F86] bg-white border border-[#0A4F86]/10 rounded-xl overflow-hidden transition-all hover:border-[#0A4F86]/30 hover:shadow-md hover:shadow-[#0A4F86]/5">
                    <span
                        class="absolute inset-0 w-0 bg-[#0A4F86]/5 transition-all duration-300 ease-out group-hover:w-full"></span>
                    <span class="relative flex items-center gap-2">
                        <span>Masuk Sistem</span>
                        <svg class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </span>
                </a>

                <!-- Hamburger Button (Muncul di Mobile) -->
                <button @click="mobileMenuOpen = !mobileMenuOpen"
                    class="md:hidden text-slate-600 hover:text-[#0A4F86] focus:outline-none p-1">
                    <!-- Icon Menu (Burger) -->
                    <svg x-show="!mobileMenuOpen" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16m-7 6h7"></path>
                    </svg>
                    <!-- Icon Close (X) -->
                    <svg x-show="mobileMenuOpen" style="display: none;" class="w-7 h-7" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Menu Dropdown -->
        <div x-show="mobileMenuOpen" style="display: none;" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-5" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-5"
            class="md:hidden absolute top-full left-0 w-full bg-white/95 backdrop-blur-xl border-b border-slate-200 shadow-xl overflow-hidden">
            <div class="px-5 py-4 flex flex-col gap-2">

                <!-- Accordion 1 -->
                <div x-data="{ accOpen: false }" class="border border-slate-100 rounded-xl bg-white overflow-hidden">
                    <button @click="accOpen = !accOpen"
                        class="w-full flex justify-between items-center px-4 py-3 text-sm font-bold text-slate-700">
                        Cek Status Material
                        <svg :class="{'rotate-180': accOpen}" class="w-4 h-4 transition-transform text-[#F47920]"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="accOpen" style="display: none;" class="px-4 pb-3 flex flex-col gap-1 bg-slate-50/50">
                        <a href="#"
                            class="block px-3 py-2 text-sm font-semibold text-slate-500 hover:text-[#0A4F86] hover:bg-slate-100 rounded-lg">PD</a>
                        <a href="#"
                            class="block px-3 py-2 text-sm font-semibold text-slate-500 hover:text-[#0A4F86] hover:bg-slate-100 rounded-lg">NON
                            STOCK</a>
                    </div>
                </div>

                <!-- Accordion 2 -->
                <div x-data="{ accOpen: false }" class="border border-slate-100 rounded-xl bg-white overflow-hidden">
                    <button @click="accOpen = !accOpen"
                        class="w-full flex justify-between items-center px-4 py-3 text-sm font-bold text-slate-700">
                        Pengambilan Barang
                        <svg :class="{'rotate-180': accOpen}" class="w-4 h-4 transition-transform text-[#F47920]"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="accOpen" style="display: none;" class="px-4 pb-3 flex flex-col gap-1 bg-slate-50/50">
                        <a href="#"
                            class="block px-3 py-2 text-sm font-semibold text-slate-500 hover:text-[#0A4F86] hover:bg-slate-100 rounded-lg">MIR</a>
                        <a href="#"
                            class="block px-3 py-2 text-sm font-semibold text-slate-500 hover:text-[#0A4F86] hover:bg-slate-100 rounded-lg">Reservasi</a>
                    </div>
                </div>

            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <main class="relative z-10 flex items-center justify-center min-h-screen pt-28 pb-12">
        <div
            class="container mx-auto px-5 md:px-12 lg:px-24 flex flex-col-reverse lg:flex-row items-center gap-12 lg:gap-8">

            <!-- Left Column: Text & CTA -->
            <div class="w-full lg:w-5/12 flex flex-col items-start text-left mt-8 lg:mt-0">
                <!-- Badge -->
                <div
                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/80 backdrop-blur-sm border border-[#0A4F86]/10 shadow-sm text-[#0A4F86] text-xs font-bold tracking-wide uppercase mb-6 sm:mb-8">
                    <span class="relative flex w-2 h-2 sm:w-2.5 sm:h-2.5">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#F47920] opacity-75"></span>
                        <span class="relative inline-flex rounded-full w-2 h-2 sm:w-2.5 sm:h-2.5 bg-[#F47920]"></span>
                    </span>
                    ReceivingPKT v2.0
                </div>

                <h1
                    class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-black leading-[1.2] sm:leading-[1.15] text-slate-900 mb-4 sm:mb-6 tracking-tight">
                    Otomasi Logistik & <br class="hidden sm:block">
                    <span
                        class="text-transparent bg-clip-text bg-linear-to-r from-[#0A4F86] via-[#0A4F86] to-[#F47920] relative inline-block">
                        Manajemen Inventaris
                        <!-- Underline swoop -->
                        <svg class="absolute w-full h-2 sm:h-3 -bottom-1 left-0 text-[#F47920]/20" viewBox="0 0 100 10"
                            preserveAspectRatio="none">
                            <path d="M0 5 Q 50 10 100 5" stroke="currentColor" stroke-width="4" fill="none"
                                stroke-linecap="round" />
                        </svg>
                    </span>
                </h1>

                <p class="text-base sm:text-lg text-slate-600 mb-8 max-w-lg leading-relaxed font-medium">
                    Platform terpadu untuk memantau pergerakan material, mempercepat proses administrasi di <span
                        class="text-[#0A4F86] font-semibold">receiving</span>, dan memastikan akurasi data gudang secara
                    presisi.
                </p>

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
                    <a href="{{ filament()->getLoginUrl() }}" wire:navigate
                        class="w-full sm:w-auto flex items-center justify-center px-6 sm:px-8 py-3.5 sm:py-4 text-sm sm:text-base font-bold text-white bg-linear-to-r from-[#F47920] to-[#BE5A27] rounded-xl shadow-[0_8px_25px_-8px_rgba(244,121,32,0.6)] hover:shadow-[0_15px_35px_-10px_rgba(244,121,32,0.7)] hover:-translate-y-1 transition-all duration-300">
                        Akses Dashboard
                    </a>
                    <button type="button"
                        class="w-full sm:w-auto flex items-center justify-center px-6 sm:px-8 py-3.5 sm:py-4 text-sm sm:text-base font-bold text-slate-700 bg-white border border-slate-200 rounded-xl shadow-sm hover:border-[#0A4F86]/30 hover:bg-slate-50 transition-all duration-300">
                        Pelajari Fitur
                    </button>
                </div>

                <!-- Material Cards (ZSM, ZSP, ZRM) -->
                <div class="mt-10 sm:mt-14 w-full grid grid-cols-3 gap-2 sm:gap-4 max-w-md">
                    <div
                        class="bg-white/60 backdrop-blur-md p-3 sm:p-4 rounded-xl sm:rounded-2xl border border-white shadow-sm hover:-translate-y-1 transition-transform">
                        <div
                            class="w-6 h-6 sm:w-8 sm:h-8 rounded-lg bg-[#0A4F86]/10 flex items-center justify-center mb-1.5 sm:mb-2">
                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-[#0A4F86]" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                        <p class="text-lg sm:text-xl font-black text-slate-800">ZSM</p>
                        <p
                            class="text-[9px] sm:text-[10px] text-slate-500 font-bold uppercase tracking-wider mt-0.5 wrap-wrap-break-words line-clamp-1">
                            Support</p>
                    </div>

                    <div
                        class="bg-white/60 backdrop-blur-md p-3 sm:p-4 rounded-xl sm:rounded-2xl border border-white shadow-sm hover:-translate-y-1 transition-transform">
                        <div
                            class="w-6 h-6 sm:w-8 sm:h-8 rounded-lg bg-[#0F416A]/10 flex items-center justify-center mb-1.5 sm:mb-2">
                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-[#0F416A]" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <p class="text-lg sm:text-xl font-black text-slate-800">ZSP</p>
                        <p
                            class="text-[9px] sm:text-[10px] text-slate-500 font-bold uppercase tracking-wider mt-0.5 wrap-break-words line-clamp-1">
                            Sparepart</p>
                    </div>

                    <div
                        class="bg-white/60 backdrop-blur-md p-3 sm:p-4 rounded-xl sm:rounded-2xl border border-white shadow-sm hover:-translate-y-1 transition-transform">
                        <div
                            class="w-6 h-6 sm:w-8 sm:h-8 rounded-lg bg-[#F47920]/10 flex items-center justify-center mb-1.5 sm:mb-2">
                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-[#F47920]" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                </path>
                            </svg>
                        </div>
                        <p class="text-lg sm:text-xl font-black text-slate-800">ZRM</p>
                        <p
                            class="text-[9px] sm:text-[10px] text-slate-500 font-bold uppercase tracking-wider mt-0.5 wrap-break-words line-clamp-1">
                            Raw Mat.</p>
                    </div>
                </div>
            </div>

            <!-- Right Column: Dashboard Illustration -->
            <div class="w-full lg:w-7/12 relative mt-4 md:mt-0">
                <div class="relative w-full max-w-full sm:max-w-2xl mx-auto px-2 sm:px-0">
                    <!-- Glow Behind -->
                    <div
                        class="absolute inset-0 bg-linear-to-tr from-[#0A4F86] to-[#0F416A] rounded-[2.5rem] transform rotate-1 scale-100 opacity-15 blur-xl">
                    </div>

                    <!-- Main Dashboard Frame -->
                    <div
                        class="relative bg-white/90 backdrop-blur-xl rounded-2xl sm:rounded-4xl shadow-2xl border border-white overflow-hidden flex flex-col">
                        <!-- Header Bar -->
                        <div
                            class="h-8 sm:h-12 bg-slate-50/80 border-b border-slate-100 flex items-center px-4 sm:px-6 gap-2">
                            <div class="w-2 h-2 sm:w-3 sm:h-3 rounded-full bg-red-400"></div>
                            <div class="w-2 h-2 sm:w-3 sm:h-3 rounded-full bg-amber-400"></div>
                            <div class="w-2 h-2 sm:w-3 sm:h-3 rounded-full bg-green-400"></div>
                            <div
                                class="hidden sm:flex ml-4 px-3 py-1 rounded bg-white shadow-sm border border-slate-100 items-center gap-2 w-48">
                                <svg class="w-3 h-3 text-slate-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <div class="w-full h-1.5 bg-slate-100 rounded-full"></div>
                            </div>
                        </div>

                        <!-- Dashboard Content -->
                        <div class="p-4 sm:p-8 flex-1 flex flex-col gap-4 sm:gap-6 bg-slate-50/30">
                            <!-- Top Stats (Scrollable horizontally on very small phones if needed, or tight grid) -->
                            <div class="grid grid-cols-3 gap-2 sm:gap-5">
                                <div
                                    class="h-20 sm:h-28 rounded-xl sm:rounded-2xl bg-linear-to-br from-[#0A4F86]/5 to-transparent border border-[#0A4F86]/10 p-3 sm:p-5 flex flex-col justify-between relative overflow-hidden">
                                    <div
                                        class="absolute top-0 right-0 w-10 sm:w-16 h-10 sm:h-16 bg-[#0A4F86]/5 rounded-bl-full">
                                    </div>
                                    <div>
                                        <div class="text-base sm:text-2xl font-black text-slate-800">1,284</div>
                                        <div class="w-10 sm:w-20 h-1 sm:h-2 mt-1 rounded bg-[#0A4F86]/20"></div>
                                    </div>
                                </div>
                                <div
                                    class="h-20 sm:h-28 rounded-xl sm:rounded-2xl bg-linear-to-br from-[#F47920]/5 to-transparent border border-[#F47920]/10 p-3 sm:p-5 flex flex-col justify-between relative overflow-hidden">
                                    <div
                                        class="absolute top-0 right-0 w-10 sm:w-16 h-10 sm:h-16 bg-[#F47920]/5 rounded-bl-full">
                                    </div>
                                    <div>
                                        <div class="text-base sm:text-2xl font-black text-slate-800">856</div>
                                        <div class="w-10 sm:w-20 h-1 sm:h-2 mt-1 rounded bg-[#F47920]/20"></div>
                                    </div>
                                </div>
                                <div
                                    class="h-20 sm:h-28 rounded-xl sm:rounded-2xl bg-linear-to-br from-white to-slate-100 border border-slate-200 p-3 sm:p-5 flex flex-col justify-between shadow-sm">
                                    <div>
                                        <div class="text-base sm:text-2xl font-black text-slate-800">24</div>
                                        <div class="w-8 sm:w-16 h-1 sm:h-2 mt-1 rounded bg-slate-200"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Main Chart Area -->
                            <div
                                class="flex-1 rounded-xl sm:rounded-2xl bg-linear-to-br from-white to-slate-100 border border-slate-200 shadow-sm p-4 sm:p-6 flex flex-col min-h-30 sm:min-h-0">
                                <div class="flex justify-between items-center mb-4 sm:mb-6">
                                    <div class="w-20 sm:w-32 h-3 sm:h-4 bg-slate-200 rounded-full"></div>
                                    <div class="w-10 sm:w-16 h-4 sm:h-6 bg-slate-100 rounded-md"></div>
                                </div>
                                <!-- Mock Bars -->
                                <div class="flex items-end gap-1 sm:gap-3 h-20 sm:h-32 mt-auto">
                                    <div class="w-full bg-[#0A4F86]/10 rounded-t-sm h-[40%]"></div>
                                    <div class="w-full bg-[#0A4F86]/30 rounded-t-sm h-[70%]"></div>
                                    <div class="w-full bg-[#0A4F86]/60 rounded-t-sm h-[50%]"></div>
                                    <div
                                        class="w-full bg-[#F47920] rounded-t-sm sm:rounded-t-md h-[90%] shadow-[0_0_10px_rgba(244,121,32,0.4)] relative">
                                        <div
                                            class="hidden sm:block absolute -top-8 left-1/2 -translate-x-1/2 bg-slate-800 text-white text-[10px] font-bold px-2 py-1 rounded">
                                            MIGO</div>
                                    </div>
                                    <div class="w-full bg-[#0A4F86]/20 rounded-t-sm h-[60%]"></div>
                                    <div class="w-full bg-[#0A4F86]/10 rounded-t-sm h-[30%]"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Floating Sync Widget (Mobile Optimized Position) -->
                    <div
                        class="absolute -bottom-6 left-2 sm:-bottom-8 sm:-left-12 bg-white/95 backdrop-blur-md p-3 sm:p-4 rounded-xl sm:rounded-2xl shadow-xl border border-slate-100 flex items-center gap-3 animate-pulse duration-3000 z-20">
                        <div
                            class="relative flex h-8 w-8 sm:h-12 sm:w-12 items-center justify-center rounded-full bg-green-50 border border-green-100">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-20"></span>
                            <svg class="w-4 h-4 sm:w-6 sm:h-6 text-green-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <div class="pr-2 sm:pr-4">
                            <p class="text-xs sm:text-sm font-extrabold text-slate-800">SAP Sync</p>
                            <p class="hidden sm:block text-[11px] font-medium text-slate-500">Terhubung secara real-time
                            </p>
                        </div>
                    </div>

                    <!-- Floating Data Widget (Mobile Optimized Position) -->
                    <div
                        class="absolute -top-4 right-2 sm:top-12 sm:-right-8 bg-white/95 backdrop-blur-md px-3 py-2 sm:px-5 sm:py-3 rounded-lg sm:rounded-2xl shadow-xl border border-slate-100 flex items-center gap-2 transform hover:scale-105 transition-transform z-20">
                        <div class="w-1.5 h-1.5 sm:w-2 sm:h-2 rounded-full bg-[#F47920] animate-pulse"></div>
                        <p class="text-[10px] sm:text-xs font-bold text-slate-700">MB51 Tracking</p>
                    </div>

                </div>
            </div>
        </div>
    </main>
</div>
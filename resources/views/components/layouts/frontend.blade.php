<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Welcome - Receiving PKT' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- 1. Jembatan CSS Filament -->
    @filamentStyles

    <!-- Font Google: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .nav-desktop { display: none !important; }
        .nav-mobile-btn { display: flex !important; }
        
        @media (min-width: 1024px) {
            .nav-desktop { display: flex !important; }
            .nav-mobile-btn { display: none !important; }
        }
    </style>
    <script>
        function applyTheme() {
            if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        }
        applyTheme();
        document.addEventListener('livewire:navigated', applyTheme);
    </script>
</head>

<body x-data="{ isDark: localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches) }" 
      x-init="$watch('isDark', val => { localStorage.setItem('theme', val ? 'dark' : 'light'); if(val) document.documentElement.classList.add('dark'); else document.documentElement.classList.remove('dark'); })"
      class="bg-slate-50 dark:bg-[#031525] text-slate-800 dark:text-slate-200 antialiased overflow-x-hidden transition-colors duration-500 flex flex-col min-h-screen">

    <x-frontend.navbar />

    <main class="flex-grow">
        {{ $slot }}
    </main>

    <x-frontend.footer />

    <!-- Chatbot Widget -->
    <livewire:chatbot-widget />

    <!-- 2. Jembatan JavaScript Filament -->
    @filamentScripts
</body>

</html>
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
    </style>
</head>

<body class="bg-slate-50 text-slate-800 antialiased overflow-x-hidden">

    {{ $slot }}

    <!-- 2. Jembatan JavaScript Filament -->
    @filamentScripts
</body>

</html>
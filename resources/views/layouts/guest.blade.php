<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-gray-900 antialiased">
    {{-- 
        The background gradient now sits on the main container.
        Using min-h-screen ensures the background fills the full height, 
        and it stays fixed even if the page scrolls.
    --}}
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-blue-50 via-cyan-50 to-blue-100">
        {{-- This card holds your Login or Register form --}}
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-2xl rounded-2xl border border-blue-100/50">
            {{ $slot }}
        </div>
    </div>
</body>
</html>
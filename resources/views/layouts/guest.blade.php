<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} - Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, .font-serif { font-family: 'Playfair Display', serif; }
    </style>
</head>
<body class="bg-gradient-to-br from-amber-50 via-cream-50 to-orange-50 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <img src="{{ asset('images/logo.jpeg') }}" class="h-20 w-20 mx-auto rounded-2xl object-cover shadow-md mb-3" alt="Logo">
            <h1 class="text-3xl font-bold text-amber-900">Mamitha Bakery</h1>
            <p id="layout-subtitle" class="text-amber-700 mt-1">{{ $subtitle ?? 'Warm Fresh Bakery' }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-lg border border-amber-100 p-8">
            {{ $slot }}
        </div>
        <p class="text-center text-xs text-gray-400 mt-6">&copy; {{ date('Y') }} Mamitha Bakery. All rights reserved.</p>
    </div>
</body>
</html>

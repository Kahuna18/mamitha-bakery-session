<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Kitchen') - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="{{ asset('js/thermal-printer.js') }}"></script>
</head>
<body class="bg-gray-50 font-['Inter']">
    <nav class="bg-amber-900 text-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-14 items-center">
                <div class="flex items-center space-x-2">
                    <span class="text-xl">🍳</span>
                    <span class="font-semibold">Mamitha Kitchen</span>
                </div>
                <div class="flex items-center space-x-2 sm:space-x-3">
                    <!-- Printer status badge -->
                    <div id="printer-status" class="hidden sm:block"></div>

                    <!-- Printer settings button -->
                    <button onclick="ThermalPrinter.openSettings()"
                        class="px-3 py-1.5 bg-amber-800 hover:bg-amber-700 text-sm rounded-lg transition flex items-center gap-1.5"
                        title="Pengaturan Printer">
                        🖨️ Printer
                    </button>

                    <span class="text-sm text-amber-200 hidden md:inline">{{ auth()->user()->name }}</span>
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="px-3 py-1.5 bg-amber-800 hover:bg-amber-700 text-sm rounded-lg transition">Dashboard Admin</a>
                        <a href="{{ route('kitchen.dashboard') }}" class="px-3 py-1.5 bg-amber-700 hover:bg-amber-600 text-sm rounded-lg transition">Dashboard Kitchen</a>
                    @elseif(auth()->user()->isKitchen())
                        <a href="{{ route('kitchen.dashboard') }}" class="px-3 py-1.5 bg-amber-800 hover:bg-amber-700 text-sm rounded-lg transition">Dashboard</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-sm rounded-lg transition">Keluar</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 py-6">
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm mb-4">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm mb-4">{{ session('error') }}</div>
        @endif
        @yield('content')
    </main>
    @stack('scripts')
</body>
</html>

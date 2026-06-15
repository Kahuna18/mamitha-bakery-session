<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name')) - Mamitha Bakery</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, h4, .font-serif { font-family: 'Playfair Display', serif; }
    </style>
</head>
<body class="bg-cream-50 text-gray-800 antialiased">
    <nav class="bg-white shadow-sm sticky top-0 z-50 border-b border-amber-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <a href="{{ route('home') }}" class="flex items-center space-x-2">
                    <img src="{{ asset('images/logo.jpeg') }}" class="h-8 w-8 rounded-lg object-cover" alt="Logo">
                    <span class="font-serif text-xl font-bold text-amber-800">Mamitha Bakery</span>
                </a>
                <div class="hidden md:flex items-center space-x-1">
                    <a href="{{ route('home') }}" class="px-4 py-2 rounded-lg text-sm font-medium text-gray-700 hover:text-amber-700 hover:bg-amber-50 transition">Beranda</a>
                    <a href="{{ route('menu') }}" class="px-4 py-2 rounded-lg text-sm font-medium text-gray-700 hover:text-amber-700 hover:bg-amber-50 transition">Menu Roti</a>
                    <a href="{{ route('how-to-order') }}" class="px-4 py-2 rounded-lg text-sm font-medium text-gray-700 hover:text-amber-700 hover:bg-amber-50 transition">Cara Pesan</a>
                    <a href="{{ route('about') }}" class="px-4 py-2 rounded-lg text-sm font-medium text-gray-700 hover:text-amber-700 hover:bg-amber-50 transition">Tentang Kami</a>
                    <a href="{{ route('contact') }}" class="px-4 py-2 rounded-lg text-sm font-medium text-gray-700 hover:text-amber-700 hover:bg-amber-50 transition">Kontak</a>
                    @auth
                        @if(auth()->user()->role === 'admin')
                            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 rounded-lg text-sm font-medium text-gray-700 hover:text-amber-700 hover:bg-amber-50 transition">Dashboard Admin</a>
                        @else
                            <a href="{{ route('kitchen.dashboard') }}" class="px-4 py-2 rounded-lg text-sm font-medium text-gray-700 hover:text-amber-700 hover:bg-amber-50 transition">Dashboard Kitchen</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="px-4 py-2 rounded-lg text-sm font-medium text-red-650 hover:text-red-750 hover:bg-red-50 transition">Keluar</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 rounded-lg text-sm font-medium text-gray-700 hover:text-amber-700 hover:bg-amber-50 transition">Masuk</a>
                    @endauth
                    <a href="{{ route('order.create') }}" class="ml-2 px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-semibold rounded-lg shadow-sm transition text-sm">Pesan Sekarang</a>
                </div>
                <button id="mobile-menu-btn" class="md:hidden p-2 rounded-lg hover:bg-amber-50">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </div>
        </div>
        <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-amber-100">
            <div class="px-4 py-3 space-y-1">
                <a href="{{ route('home') }}" class="block px-4 py-2 rounded-lg text-gray-700 hover:bg-amber-50">Beranda</a>
                <a href="{{ route('menu') }}" class="block px-4 py-2 rounded-lg text-gray-700 hover:bg-amber-50">Menu Roti</a>
                <a href="{{ route('how-to-order') }}" class="block px-4 py-2 rounded-lg text-gray-700 hover:bg-amber-50">Cara Pesan</a>
                <a href="{{ route('about') }}" class="block px-4 py-2 rounded-lg text-gray-700 hover:bg-amber-50">Tentang Kami</a>
                <a href="{{ route('contact') }}" class="block px-4 py-2 rounded-lg text-gray-700 hover:bg-amber-50">Kontak</a>
                @auth
                    @if(auth()->user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 rounded-lg text-gray-700 hover:bg-amber-50">Dashboard Admin</a>
                    @else
                        <a href="{{ route('kitchen.dashboard') }}" class="block px-4 py-2 rounded-lg text-gray-700 hover:bg-amber-50">Dashboard Kitchen</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" class="block">
                        @csrf
                        <button type="submit" class="block w-full text-left px-4 py-2 rounded-lg text-red-650 hover:bg-red-50">Keluar</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="block px-4 py-2 rounded-lg text-gray-700 hover:bg-amber-50">Masuk</a>
                @endauth
                <a href="{{ route('order.create') }}" class="block px-4 py-2.5 bg-amber-600 text-white font-semibold rounded-lg text-center">Pesan Sekarang</a>
            </div>
        </div>
    </nav>

    <main>
        @if(session('success'))
            <div class="max-w-7xl mx-auto px-4 mt-4">
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
            </div>
        @endif
        @if(session('error'))
            <div class="max-w-7xl mx-auto px-4 mt-4">
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">{{ session('error') }}</div>
            </div>
        @endif
        @yield('content')
    </main>

    <footer class="bg-amber-900 text-amber-100 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid md:grid-cols-3 gap-8">
                <div>
                    <h4 class="font-serif text-xl font-bold text-white mb-3">{{ \App\Models\Setting::getValue('store_name', 'Mamitha Bakery') }}</h4>
                    <p class="text-sm text-amber-200 leading-relaxed">{{ \App\Models\Setting::getValue('about_text') ?: 'Roti fresh setiap hari, dibuat dengan bahan berkualitas dan penuh cinta. Siap melayani pesanan Anda.' }}</p>
                </div>
                <div>
                    <h4 class="font-semibold text-white mb-3">Jam Operasional</h4>
                    <p class="text-sm text-amber-200">Setiap Hari: {{ \App\Models\Setting::getValue('open_time', '07:00') }} - {{ \App\Models\Setting::getValue('close_time', '20:00') }}</p>
                </div>
                <div>
                    <h4 class="font-semibold text-white mb-3">Kontak</h4>
                    <p class="text-sm text-amber-200">WA: {{ \App\Models\Setting::getValue('store_phone', '0812-3456-7890') }}</p>
                    <p class="text-sm text-amber-200">{{ \App\Models\Setting::getValue('store_email', 'info@mamithabakery.com') }}</p>
                    <div class="mt-3">
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', \App\Models\Setting::getValue('store_whatsapp', '6281234567890')) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg transition">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 448 512"><path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"/></svg>
                            Hubungi WhatsApp
                        </a>
                    </div>
                </div>
            </div>
            <div class="border-t border-amber-800 mt-8 pt-6 text-center text-sm text-amber-300">
                &copy; {{ date('Y') }} {{ \App\Models\Setting::getValue('store_name', 'Mamitha Bakery') }}. All rights reserved.
            </div>
        </div>
    </footer>

    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', \App\Models\Setting::getValue('store_whatsapp', '6281234567890')) }}" target="_blank" class="fixed bottom-6 right-6 bg-green-500 hover:bg-green-600 text-white w-14 h-14 rounded-full shadow-lg flex items-center justify-center z-50 transition transform hover:scale-105">
        <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 448 512"><path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"/></svg>
    </a>

    <script>
        document.getElementById('mobile-menu-btn')?.addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        });
    </script>
    @stack('scripts')
</body>
</html>

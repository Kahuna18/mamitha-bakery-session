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
    <script>
        if (localStorage.getItem('theme') === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, h4, .font-serif { font-family: 'Playfair Display', serif; }

        /* View Transition Animations for theme toggling */
        ::view-transition-old(root),
        ::view-transition-new(root) {
            animation: none;
            mix-blend-mode: normal;
        }
        ::view-transition-old(root) {
            z-index: 1;
        }
        ::view-transition-new(root) {
            z-index: 9999;
        }
    </style>
</head>
<body class="bg-cream-50 dark:bg-gray-900 text-gray-800 dark:text-gray-100 antialiased pb-20 md:pb-0 transition-colors duration-200">
    <nav class="bg-white dark:bg-gray-900/90 shadow-sm sticky top-0 z-50 border-b border-amber-100 dark:border-gray-800 backdrop-blur-md transition-colors duration-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <a href="{{ route('home') }}" class="flex items-center space-x-2">
                    <img src="{{ asset('images/logo.jpeg') }}" class="h-8 w-8 rounded-lg object-cover" alt="Logo">
                    <span class="font-serif text-xl font-bold text-amber-800 dark:text-amber-400">Mamitha Bakery</span>
                </a>
                <div class="hidden md:flex items-center space-x-1">
                    <a href="{{ route('home') }}" class="px-4 py-2 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-amber-700 dark:hover:text-amber-400 hover:bg-amber-50 dark:hover:bg-gray-800 transition">Beranda</a>
                    <a href="{{ route('menu') }}" class="px-4 py-2 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-amber-700 dark:hover:text-amber-400 hover:bg-amber-50 dark:hover:bg-gray-800 transition">Menu Roti</a>
                    <a href="{{ route('how-to-order') }}" class="px-4 py-2 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-amber-700 dark:hover:text-amber-400 hover:bg-amber-50 dark:hover:bg-gray-800 transition">Cara Pesan</a>
                    <a href="{{ route('about') }}" class="px-4 py-2 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-amber-700 dark:hover:text-amber-400 hover:bg-amber-50 dark:hover:bg-gray-800 transition">Tentang Kami</a>
                    <a href="{{ route('contact') }}" class="px-4 py-2 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-amber-700 dark:hover:text-amber-400 hover:bg-amber-50 dark:hover:bg-gray-800 transition">Kontak</a>
                    @auth
                        <!-- Global Theme Toggle (Desktop) -->
                        <button onclick="toggleThemeGlobally(event)" class="mr-2 p-2 rounded-2xl border border-amber-100 dark:border-gray-700 text-gray-500 dark:text-gray-400 hover:bg-amber-50 dark:hover:bg-gray-800 transition duration-150 cursor-pointer select-none active:scale-95 flex items-center justify-center w-9 h-9" title="Ubah Tema">
                            <span id="theme-toggle-btn-icon" class="text-sm">🌙</span>
                        </button>

                        <!-- User Profile Dropdown (Alpine.js) -->
                        <div x-data="{ open: false }" @click.away="open = false" class="relative inline-block text-left">
                            <button @click="open = !open" type="button" class="inline-flex items-center px-4 py-2 border border-amber-100 dark:border-gray-700 rounded-2xl text-sm font-medium text-gray-700 dark:text-gray-300 bg-amber-50/20 dark:bg-gray-850 hover:bg-amber-50 dark:hover:bg-gray-800 hover:text-amber-800 dark:hover:text-amber-400 transition duration-150 cursor-pointer active:scale-95">
                                @php
                                    $user = auth()->user();
                                    $customerRecord = $user->customer;
                                    $badge = '🥉';
                                    $roleName = 'Member';
                                    if ($user->isAdmin()) {
                                        $badge = '👑';
                                        $roleName = 'Administrator';
                                    } elseif ($user->isKitchen()) {
                                        $badge = '👨‍🍳';
                                        $roleName = 'Kitchen Staff';
                                    } else {
                                        $badge = $customerRecord ? $customerRecord->rank_badge : '🥉';
                                        $roleName = ($customerRecord ? $customerRecord->rank_name : 'Bronze') . ' Member';
                                    }
                                @endphp
                                <span class="mr-1.5 select-none text-base">{{ $badge }}</span>
                                <span>{{ $user->name }}</span>
                                <svg class="w-4 h-4 ml-1.5 text-gray-500 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <div x-show="open" 
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-52 rounded-2xl bg-white dark:bg-gray-800 border border-amber-100/60 dark:border-gray-700 shadow-xl py-1.5 z-50 ring-1 ring-black/5"
                                 style="display: none;">
                                 
                                <div class="px-4 py-2 border-b border-amber-50 dark:border-gray-700/50">
                                    <p class="text-[9px] uppercase tracking-wider text-gray-400 font-bold">Peringkat / Peran</p>
                                    <p class="text-xs font-black text-amber-800 dark:text-amber-400 mt-0.5">{{ $roleName }}</p>
                                </div>

                                @if($user->isAdmin())
                                    <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2 text-xs font-bold text-gray-700 dark:text-gray-300 hover:bg-amber-50 dark:hover:bg-gray-700 hover:text-amber-800 dark:hover:text-amber-400 transition">
                                        📊 Dashboard Admin
                                    </a>
                                    <a href="{{ route('kitchen.dashboard') }}" class="flex items-center px-4 py-2 text-xs font-bold text-gray-700 dark:text-gray-300 hover:bg-amber-50 dark:hover:bg-gray-700 hover:text-amber-800 dark:hover:text-amber-400 transition">
                                        👨‍🍳 Dashboard Kitchen
                                    </a>
                                    <a href="{{ route('member.profile') }}" class="flex items-center px-4 py-2 text-xs font-bold text-gray-700 dark:text-gray-300 hover:bg-amber-50 dark:hover:bg-gray-700 hover:text-amber-800 dark:hover:text-amber-400 transition">
                                        👤 Profil Member Saya
                                    </a>
                                    <a href="{{ route('order.history') }}" class="flex items-center px-4 py-2 text-xs font-bold text-gray-700 dark:text-gray-300 hover:bg-amber-50 dark:hover:bg-gray-700 hover:text-amber-800 dark:hover:text-amber-400 transition">
                                        📋 Riwayat Pesanan
                                    </a>
                                @elseif($user->isKitchen())
                                    <a href="{{ route('kitchen.dashboard') }}" class="flex items-center px-4 py-2 text-xs font-bold text-gray-700 dark:text-gray-300 hover:bg-amber-50 dark:hover:bg-gray-700 hover:text-amber-800 dark:hover:text-amber-400 transition">
                                        👨‍🍳 Dashboard Kitchen
                                    </a>
                                @else
                                    <a href="{{ route('member.profile') }}" class="flex items-center px-4 py-2 text-xs font-bold text-gray-700 dark:text-gray-300 hover:bg-amber-50 dark:hover:bg-gray-700 hover:text-amber-800 dark:hover:text-amber-400 transition">
                                        👤 Profil Member Saya
                                    </a>
                                    <a href="{{ route('order.history') }}" class="flex items-center px-4 py-2 text-xs font-bold text-gray-700 dark:text-gray-300 hover:bg-amber-50 dark:hover:bg-gray-700 hover:text-amber-800 dark:hover:text-amber-400 transition">
                                        📋 Riwayat Pesanan
                                    </a>
                                @endif

                                <div class="border-t border-amber-50 dark:border-gray-700/50 my-1"></div>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center px-4 py-2 text-xs font-bold text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/20 hover:text-red-750 dark:hover:text-red-300 transition text-left cursor-pointer">
                                        🚪 Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-amber-700 dark:hover:text-amber-400 hover:bg-amber-50 dark:hover:bg-gray-800 transition">Masuk</a>
                    @endauth
                    <a href="{{ route('order.create') }}" id="desktop-pesan-btn" class="ml-2 px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-semibold rounded-lg shadow-sm transition text-sm flex items-center gap-2 relative">
                        <span>Pesan Sekarang</span>
                        <span id="desktop-cart-badge" class="bg-red-500 text-white text-[9px] font-extrabold px-1.5 py-0.5 rounded-full hidden">0</span>
                    </a>
                </div>
                <div class="flex items-center gap-2 md:hidden">
                    @auth
                        <!-- Global Theme Toggle (Mobile) -->
                        <button onclick="toggleThemeGlobally(event)" class="p-2 rounded-2xl border border-amber-100 dark:border-gray-700 text-gray-500 dark:text-gray-400 hover:bg-amber-50 dark:hover:bg-gray-800 transition duration-150 cursor-pointer select-none active:scale-95 flex items-center justify-center w-9 h-9" title="Ubah Tema">
                            <span id="theme-toggle-btn-icon-mobile" class="text-sm">🌙</span>
                        </button>
                    @endauth
                    <button id="mobile-menu-btn" class="p-2 rounded-lg hover:bg-amber-50 dark:hover:bg-gray-800 dark:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                </div>
            </div>
        </div>
        <div id="mobile-menu" class="hidden md:hidden bg-white dark:bg-gray-900 border-t border-amber-100 dark:border-gray-800">
            <div class="px-4 py-3 space-y-1">
                <a href="{{ route('home') }}" class="block px-4 py-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-amber-50 dark:hover:bg-gray-800">Beranda</a>
                <a href="{{ route('menu') }}" class="block px-4 py-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-amber-50 dark:hover:bg-gray-800">Menu Roti</a>
                <a href="{{ route('how-to-order') }}" class="block px-4 py-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-amber-50 dark:hover:bg-gray-800">Cara Pesan</a>
                <a href="{{ route('about') }}" class="block px-4 py-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-amber-50 dark:hover:bg-gray-800">Tentang Kami</a>
                <a href="{{ route('contact') }}" class="block px-4 py-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-amber-50 dark:hover:bg-gray-800">Kontak</a>
                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-amber-50 dark:hover:bg-gray-800">Dashboard Admin</a>
                        <a href="{{ route('kitchen.dashboard') }}" class="block px-4 py-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-amber-50 dark:hover:bg-gray-800">Dashboard Kitchen</a>
                    @elseif(auth()->user()->isKitchen())
                        <a href="{{ route('kitchen.dashboard') }}" class="block px-4 py-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-amber-50 dark:hover:bg-gray-800">Dashboard Kitchen</a>
                    @else
                        <a href="{{ route('member.profile') }}" class="block px-4 py-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-amber-50 dark:hover:bg-gray-800">👤 Profil Member</a>
                        <a href="{{ route('order.history') }}" class="block px-4 py-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-amber-50 dark:hover:bg-gray-800">📋 Riwayat Pesanan</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" class="block">
                        @csrf
                        <button type="submit" class="block w-full text-left px-4 py-2 rounded-lg text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/20">Keluar</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="block px-4 py-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-amber-50 dark:hover:bg-gray-800">Masuk</a>
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

    @if(!request()->routeIs('member.profile'))
    <footer class="bg-amber-900 text-amber-100 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid md:grid-cols-3 gap-8">
                <div>
                    <h4 class="font-serif text-xl font-bold text-white mb-3">{{ \App\Models\Setting::getValue('store_name', 'Mamitha Bakery') }}</h4>
                    <p class="text-sm text-amber-200 leading-relaxed">{{ \App\Models\Setting::getValue('about_text') ?: 'Roti fresh setiap hari, dibuat dengan bahan berkualitas and penuh cinta. Siap melayani pesanan Anda.' }}</p>
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
    @endif

    <!-- Mobile Bottom Navigation Bar (Sticky) -->
    @php
        $isHome = request()->routeIs('home');
        $isMenu = request()->routeIs('menu');
        $isOrder = request()->routeIs('order.create') || request()->routeIs('order.success');
        $isProfile = request()->routeIs('member.profile') || request()->routeIs('login') || request()->routeIs('register') || request()->routeIs('order.history');
    @endphp
    <div class="md:hidden fixed bottom-0 inset-x-0 bg-white/95 dark:bg-gray-900/95 backdrop-blur-md border-t border-amber-100/50 dark:border-gray-800/80 py-2.5 px-4 flex justify-around items-center z-[45] shadow-[0_-4px_12px_rgba(0,0,0,0.03)] transition-colors duration-200">
        <!-- Home -->
        <a href="{{ route('home') }}" class="flex flex-col items-center justify-center transition select-none {{ $isHome ? 'text-amber-800 dark:text-amber-400' : 'text-gray-400 hover:text-gray-600' }}">
            @if($isHome)
                <div class="bg-amber-100/60 dark:bg-amber-950/35 rounded-2xl px-4 py-1.5 flex items-center gap-1.5 text-xs text-amber-900 dark:text-amber-400 font-extrabold">
                    <span class="text-sm">🏠</span>
                    <span class="text-[9px] uppercase tracking-wider">Beranda</span>
                </div>
            @else
                <span class="text-lg">🏠</span>
            @endif
        </a>

        <!-- Menu -->
        <a href="{{ route('menu') }}" class="flex flex-col items-center justify-center transition select-none {{ $isMenu ? 'text-amber-800 dark:text-amber-400' : 'text-gray-400 hover:text-gray-600' }}">
            @if($isMenu)
                <div class="bg-amber-100/60 dark:bg-amber-950/35 rounded-2xl px-4 py-1.5 flex items-center gap-1.5 text-xs text-amber-900 dark:text-amber-400 font-extrabold">
                    <span class="text-sm">🥐</span>
                    <span class="text-[9px] uppercase tracking-wider">Menu</span>
                </div>
            @else
                <span class="text-lg">🥐</span>
            @endif
        </a>

        <!-- Order / Cart -->
        <a href="{{ route('order.create') }}" id="nav-pesan-btn" class="flex flex-col items-center justify-center transition select-none {{ $isOrder ? 'text-amber-800 dark:text-amber-400' : 'text-gray-400 hover:text-gray-600' }}">
            @if($isOrder)
                <div class="bg-amber-100/60 dark:bg-amber-950/35 rounded-2xl px-4 py-1.5 flex items-center gap-1.5 text-xs text-amber-900 dark:text-amber-400 font-extrabold relative">
                    <span class="text-sm">🛍️</span>
                    <span class="text-[9px] uppercase tracking-wider">Pesan</span>
                    <span id="nav-cart-badge" class="absolute -top-1 -right-1 bg-red-500 text-white text-[8px] font-bold px-1.5 py-0.5 rounded-full hidden">0</span>
                </div>
            @else
                <div class="relative flex flex-col items-center">
                    <span class="text-lg">🛍️</span>
                    <span id="nav-cart-badge" class="absolute -top-1.5 -right-2 bg-red-500 text-white text-[8px] font-bold px-1 py-0.5 rounded-full hidden">0</span>
                </div>
            @endif
        </a>

        <!-- Profile -->
        <a href="{{ auth()->check() ? route('member.profile') : route('login') }}" class="flex flex-col items-center justify-center transition select-none {{ $isProfile ? 'text-amber-800 dark:text-amber-400' : 'text-gray-400 hover:text-gray-600' }}">
            @if($isProfile)
                <div class="bg-amber-100/60 dark:bg-amber-950/35 rounded-2xl px-4 py-1.5 flex items-center gap-1.5 text-xs text-amber-900 dark:text-amber-400 font-extrabold">
                    <span class="text-sm">👤</span>
                    <span class="text-[9px] uppercase tracking-wider font-extrabold">Profil</span>
                </div>
            @else
                <span class="text-lg">👤</span>
            @endif
        </a>
    </div>

    @if(!request()->routeIs('member.profile'))
    <a id="whatsapp-float" href="https://wa.me/{{ preg_replace('/[^0-9]/', '', \App\Models\Setting::getValue('store_whatsapp', '6281234567890')) }}" target="_blank" class="fixed bottom-24 md:bottom-6 right-6 bg-green-500 hover:bg-green-600 text-white w-14 h-14 rounded-full shadow-lg flex items-center justify-center z-50 transition-all duration-300 transform hover:scale-105">
        <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 448 512"><path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"/></svg>
    </a>
    @endif

    <script>
        document.getElementById('mobile-menu-btn')?.addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        });

        // Global theme helper functions
        function applyTheme(isDark) {
            if (isDark) {
                document.documentElement.classList.add('dark');
                localStorage.setItem('theme', 'dark');
                
                // Update profile page UI elements if they exist
                document.getElementById('theme-midnight')?.classList.add('border-amber-500', 'bg-amber-50/5');
                document.getElementById('theme-midnight')?.classList.remove('border-gray-200', 'dark:border-gray-700');
                const midnightRadio = document.querySelector('#theme-midnight .theme-radio');
                if (midnightRadio) midnightRadio.className = 'theme-radio w-5 h-5 rounded-full border-2 border-amber-500 flex items-center justify-center transition';
                document.querySelector('#theme-midnight .theme-radio-inner')?.classList.remove('hidden');
                
                document.getElementById('theme-daylight')?.classList.remove('border-amber-500', 'bg-amber-50/5');
                document.getElementById('theme-daylight')?.classList.add('border-gray-200', 'dark:border-gray-700');
                const daylightRadio = document.querySelector('#theme-daylight .theme-radio');
                if (daylightRadio) daylightRadio.className = 'theme-radio w-5 h-5 rounded-full border-2 border-gray-300 flex items-center justify-center transition';
                document.querySelector('#theme-daylight .theme-radio-inner')?.classList.add('hidden');
            } else {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('theme', 'light');
                
                document.getElementById('theme-daylight')?.classList.add('border-amber-500', 'bg-amber-50/5');
                document.getElementById('theme-daylight')?.classList.remove('border-gray-200', 'dark:border-gray-700');
                const daylightRadio = document.querySelector('#theme-daylight .theme-radio');
                if (daylightRadio) daylightRadio.className = 'theme-radio w-5 h-5 rounded-full border-2 border-amber-500 flex items-center justify-center transition';
                document.querySelector('#theme-daylight .theme-radio-inner')?.classList.remove('hidden');
                
                document.getElementById('theme-midnight')?.classList.remove('border-amber-500', 'bg-amber-50/5');
                document.getElementById('theme-midnight')?.classList.add('border-gray-200', 'dark:border-gray-700');
                const midnightRadio = document.querySelector('#theme-midnight .theme-radio');
                if (midnightRadio) midnightRadio.className = 'theme-radio w-5 h-5 rounded-full border-2 border-gray-300 flex items-center justify-center transition';
                document.querySelector('#theme-midnight .theme-radio-inner')?.classList.add('hidden');
            }
            
            // Update navbar theme button icons
            const themeBtnIcon = document.getElementById('theme-toggle-btn-icon');
            if (themeBtnIcon) themeBtnIcon.textContent = isDark ? '☀️' : '🌙';
            
            const themeBtnIconMobile = document.getElementById('theme-toggle-btn-icon-mobile');
            if (themeBtnIconMobile) themeBtnIconMobile.textContent = isDark ? '☀️' : '🌙';
        }

        function setAppTheme(theme, event) {
            const isDark = theme === 'dark';
            if (!document.startViewTransition) {
                applyTheme(isDark);
                return;
            }
            const x = event ? event.clientX : window.innerWidth / 2;
            const y = event ? event.clientY : window.innerHeight / 2;
            const endRadius = Math.hypot(
                Math.max(x, window.innerWidth - x),
                Math.max(y, window.innerHeight - y)
            );
            const transition = document.startViewTransition(() => {
                applyTheme(isDark);
            });
            transition.ready.then(() => {
                const clipPath = [
                    `circle(0px at ${x}px ${y}px)`,
                    `circle(${endRadius}px at ${x}px ${y}px)`
                ];
                document.documentElement.animate(
                    {
                        clipPath: clipPath
                    },
                    {
                        duration: 450,
                        easing: 'ease-in-out',
                        pseudoElement: '::view-transition-new(root)'
                    }
                );
            });
        }

        function toggleThemeGlobally(event) {
            const isDark = document.documentElement.classList.contains('dark');
            setAppTheme(isDark ? 'light' : 'dark', event);
        }

        // Apply current theme on page load to keep navbar icons synced
        window.addEventListener('DOMContentLoaded', () => {
            const currentTheme = localStorage.getItem('theme') || 'light';
            applyTheme(currentTheme === 'dark');
        });

        // Global cart badge and shortcut click handler
        function initGlobalCartNavigation() {
            function updateBadges() {
                try {
                    const storedCart = localStorage.getItem('mamitha_cart');
                    let totalItems = 0;
                    if (storedCart) {
                        const cart = JSON.parse(storedCart);
                        Object.keys(cart).forEach(key => {
                            totalItems += cart[key].qty;
                        });
                    }
                    
                    const mobileBadge = document.getElementById('nav-cart-badge');
                    if (mobileBadge) {
                        if (totalItems > 0) {
                            mobileBadge.textContent = totalItems;
                            mobileBadge.classList.remove('hidden');
                        } else {
                            mobileBadge.classList.add('hidden');
                        }
                    }
                    
                    const desktopBadge = document.getElementById('desktop-cart-badge');
                    if (desktopBadge) {
                        if (totalItems > 0) {
                            desktopBadge.textContent = totalItems;
                            desktopBadge.classList.remove('hidden');
                        } else {
                            desktopBadge.classList.add('hidden');
                        }
                    }
                } catch (e) {
                    console.error('Error updating nav cart badge:', e);
                }
            }

            // Update badges on load
            updateBadges();

            // Listen for storage events to update badges if changed in another tab
            window.addEventListener('storage', function(e) {
                if (e.key === 'mamitha_cart') {
                    updateBadges();
                }
            });

            // Expose update function globally so create.blade.php can call it
            window.updateGlobalCartBadges = updateBadges;

            // Handle navigation clicks to open drawer if already on order page
            const handleCartClick = function(e) {
                if (window.location.pathname === '/pesan' && typeof window.toggleCheckoutDrawer === 'function') {
                    e.preventDefault();
                    window.toggleCheckoutDrawer(true);
                }
            };

            document.getElementById('nav-pesan-btn')?.addEventListener('click', handleCartClick);
            document.getElementById('desktop-pesan-btn')?.addEventListener('click', handleCartClick);
        }

        document.addEventListener('DOMContentLoaded', initGlobalCartNavigation);
    </script>
    @stack('scripts')
</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @stack('styles')
    <script src="{{ asset('js/thermal-printer.js') }}"></script>
    <script>
        (function() {
            const theme = localStorage.getItem('admin-theme') || 'light';
            if (theme === 'dark') {
                document.documentElement.setAttribute('data-theme', 'dark');
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.setAttribute('data-theme', 'light');
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>
    <style>
        /* Smooth transitions for theme toggle */
        body, header, aside, main, div, p, span, h1, h2, h3, table, td, th, a, button, input, textarea, select {
            transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease, box-shadow 0.3s ease;
        }
        
        /* Global Dark Mode - deep slate-violet palette */
        html.dark body {
            background-color: #0f111a !important;
            color: #f1f5f9 !important;
        }
        html.dark main {
            background-color: #0f111a !important;
        }
        html.dark header {
            background-color: #151824 !important;
            border-color: rgba(255, 255, 255, 0.06) !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25) !important;
        }
        html.dark header span.text-gray-600 {
            color: #cbd5e1 !important;
        }
        html.dark .bg-white {
            background-color: #171928 !important;
        }
        html.dark .bg-gray-50 {
            background-color: #0f111a !important;
        }
        html.dark .text-gray-800,
        html.dark .text-gray-900 {
            color: #f8fafc !important;
        }
        html.dark .text-gray-700 {
            color: #e2e8f0 !important;
        }
        html.dark .text-gray-600 {
            color: #cbd5e1 !important;
        }
        html.dark .text-gray-500,
        html.dark .text-gray-400 {
            color: #94a3b8 !important;
        }
        html.dark .border-gray-100,
        html.dark .border-gray-200,
        html.dark .border-gray-300,
        html.dark .border-amber-100 {
            border-color: #22243d !important;
        }
        html.dark .hover\:bg-gray-50:hover {
            background-color: rgba(255, 255, 255, 0.03) !important;
        }
        /* Dark mode tables */
        html.dark table th {
            color: #cbd5e1 !important;
            background-color: rgba(255, 255, 255, 0.01) !important;
            border-color: #22243d !important;
        }
        html.dark table td {
            color: #e2e8f0 !important;
            border-color: #22243d !important;
        }
        /* Dark mode form inputs */
        html.dark input,
        html.dark textarea,
        html.dark select {
            background-color: #1f2238 !important;
            color: #f8fafc !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
        }
        html.dark input::placeholder,
        html.dark textarea::placeholder {
            color: #6b7280 !important;
        }
        /* Dark mode cards & alerts */
        html.dark .bg-green-50 { background-color: rgba(16, 185, 129, 0.08) !important; border-color: rgba(16, 185, 129, 0.15) !important; }
        html.dark .bg-red-50 { background-color: rgba(239, 68, 68, 0.08) !important; border-color: rgba(239, 68, 68, 0.15) !important; }
        html.dark .bg-amber-50 { background-color: rgba(245, 158, 11, 0.08) !important; border-color: rgba(245, 158, 11, 0.15) !important; }
        html.dark .text-green-700 { color: #34d399 !important; }
        html.dark .text-red-700 { color: #f87171 !important; }
        html.dark .text-amber-700 { color: #fbbf24 !important; }
        /* Dark mode headings */
        html.dark h1, html.dark h2, html.dark h3 {
            color: #f8fafc !important;
        }
        /* Dark mode links */
        html.dark a.text-amber-600 {
            color: #fbbf24 !important;
        }
        
        /* Sidebar concept override in dark mode */
        html.dark aside#sidebar,
        html.dark #mobile-sidebar aside {
            background-color: #16120e !important;
            border-right: 1px solid rgba(255, 255, 255, 0.04) !important;
        }
        html.dark aside .border-amber-800 {
            border-color: rgba(255, 255, 255, 0.04) !important;
        }
        html.dark aside nav a {
            color: #fbbf24 !important;
        }
        html.dark aside nav a.bg-amber-800 {
            background-color: #92400e !important;
            color: #ffffff !important;
            border-left: 3.5px solid #f59e0b !important;
            font-weight: 600 !important;
        }
        html.dark aside nav a:hover:not(.bg-amber-800) {
            background-color: rgba(245, 158, 11, 0.08) !important;
            color: #ffffff !important;
        }
        html.dark aside nav a svg {
            color: inherit !important;
        }

        /* Glowing accents */
        .glow-card {
            border: 1px solid rgba(59, 130, 246, 0.15);
            box-shadow: 0 0 15px rgba(59, 130, 246, 0.05);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .glow-card:hover {
            box-shadow: 0 0 25px rgba(59, 130, 246, 0.12);
        }
        html.dark .glow-card {
            border: 1px solid rgba(59, 130, 246, 0.25) !important;
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.1) !important;
        }
        html.dark .glow-card:hover {
            border-color: rgba(59, 130, 246, 0.4) !important;
            box-shadow: 0 0 30px rgba(59, 130, 246, 0.2) !important;
        }
        .glow-line-chart {
            filter: drop-shadow(0px 8px 16px rgba(59, 130, 246, 0.3));
        }
        .glow-progress {
            box-shadow: 0 0 12px rgba(59, 130, 246, 0.5);
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        html.dark ::-webkit-scrollbar-thumb {
            background: #374151;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        html.dark ::-webkit-scrollbar-thumb:hover {
            background: #4b5563;
        }
    </style>
</head>
<body class="bg-gray-50 font-['Inter'] dark:bg-gray-900 dark:text-gray-100 transition-colors duration-300">
    <div class="flex h-screen overflow-hidden">
        <aside id="sidebar" class="bg-amber-900 text-amber-100 w-64 flex-shrink-0 hidden md:flex flex-col">
            <div class="p-4 border-b border-amber-800">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-2">
                    <img src="{{ asset('images/logo.jpeg') }}" class="h-8 w-8 rounded-lg object-cover" alt="Logo">
                    <span class="font-semibold text-lg">Mamitha Admin</span>
                </a>
            </div>
            <nav class="flex-1 overflow-y-auto p-3 space-y-1">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.dashboard') ? 'bg-amber-800 text-white font-medium' : 'text-amber-200 hover:bg-amber-800 hover:text-white' }} transition">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    Dashboard
                </a>
                <a href="{{ route('home') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm text-amber-200 hover:bg-amber-800 hover:text-white transition">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Halaman Toko
                </a>
                <a href="{{ route('admin.reports.index') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.reports.*') ? 'bg-amber-800 text-white font-medium' : 'text-amber-200 hover:bg-amber-800 hover:text-white' }} transition">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    Laporan Keuangan
                </a>
                <a href="{{ route('admin.orders.index') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.orders.*') ? 'bg-amber-800 text-white font-medium' : 'text-amber-200 hover:bg-amber-800 hover:text-white' }} transition">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    Order Masuk
                </a>
                <a href="{{ route('admin.products.index') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.products.*') ? 'bg-amber-800 text-white font-medium' : 'text-amber-200 hover:bg-amber-800 hover:text-white' }} transition">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    Produk
                </a>
                <a href="{{ route('admin.categories.index') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.categories.*') ? 'bg-amber-800 text-white font-medium' : 'text-amber-200 hover:bg-amber-800 hover:text-white' }} transition">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    Kategori
                </a>
                <a href="{{ route('admin.testimonials.index') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.testimonials.*') ? 'bg-amber-800 text-white font-medium' : 'text-amber-200 hover:bg-amber-800 hover:text-white' }} transition">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    Testimoni Toko
                </a>
                <a href="{{ route('admin.reviews.index') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.reviews.*') ? 'bg-amber-800 text-white font-medium' : 'text-amber-200 hover:bg-amber-800 hover:text-white' }} transition">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.907c.961 0 1.361 1.248.58 1.81l-3.97 2.883a1 1 0 00-.364 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.971-2.883a1 1 0 00-1.18 0l-3.97 2.883c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.364-1.118L2.98 9.42c-.782-.563-.382-1.81.58-1.81h4.908a1 1 0 00.95-.69l1.519-4.674z"/></svg>
                    Ulasan Produk
                </a>
                <a href="{{ route('admin.kitchen') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.kitchen') ? 'bg-amber-800 text-white font-medium' : 'text-amber-200 hover:bg-amber-800 hover:text-white' }} transition">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z"/></svg>
                    Kitchen
                </a>
                <a href="{{ route('admin.customers') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.customers') ? 'bg-amber-800 text-white font-medium' : 'text-amber-200 hover:bg-amber-800 hover:text-white' }} transition">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/></svg>
                    Pelanggan
                </a>
                <a href="{{ route('admin.settings.index') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.settings.*') ? 'bg-amber-800 text-white font-medium' : 'text-amber-200 hover:bg-amber-800 hover:text-white' }} transition">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Pengaturan Toko
                </a>
            </nav>
            <div class="p-3 border-t border-amber-800">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center w-full px-4 py-2.5 rounded-lg text-sm text-amber-200 hover:bg-amber-800 hover:text-white transition">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Keluar
                    </button>
                </form>
            </div>
        </aside>

        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="flex items-center justify-between px-4 py-3">
                    <div class="flex items-center space-x-2">
                        <button id="sidebar-toggle" class="md:hidden p-2 rounded-lg hover:bg-gray-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </button>
                        <a href="{{ route('admin.dashboard') }}" class="flex md:hidden items-center space-x-2">
                            <img src="{{ asset('images/logo.jpeg') }}" class="h-8 w-8 rounded-lg object-cover" alt="Logo">
                            <span class="font-semibold text-base text-gray-800 dark:text-white">Mamitha Admin</span>
                        </a>
                    </div>
                    <div class="flex items-center space-x-2 sm:space-x-3">
                        <div id="printer-status" class="hidden sm:block"></div>
                        <button onclick="ThermalPrinter.openSettings()" class="px-3 py-1.5 bg-amber-50 hover:bg-amber-100 dark:bg-amber-950/20 dark:text-amber-300 text-amber-700 text-xs font-medium rounded-lg transition flex items-center gap-1.5" title="Pengaturan Printer">
                            🖨️ Printer
                        </button>
                        
                        <!-- Theme Toggle Button -->
                        <button id="theme-toggle-btn" class="p-1.5 rounded-lg text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 transition active:scale-90 flex items-center justify-center cursor-pointer" title="Ubah Tema">
                            <!-- Moon Icon (Light Mode Active) -->
                            <svg id="theme-toggle-moon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                            <!-- Sun Icon (Dark Mode Active) -->
                            <svg id="theme-toggle-sun" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M14 12a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </button>

                        <span class="text-sm text-gray-600 dark:text-gray-300 hidden md:inline font-medium">{{ auth()->user()->name }}</span>
                        <span class="px-2.5 py-1 bg-amber-100 dark:bg-amber-950/40 text-amber-800 dark:text-amber-300 text-xs font-semibold rounded-full hidden md:inline-flex">{{ ucfirst(auth()->user()->role) }}</span>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto bg-gray-50 p-4 md:p-6">
                @if(session('success'))
                    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm mb-4">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm mb-4">{{ session('error') }}</div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>

    <div id="mobile-sidebar" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/50" onclick="document.getElementById('mobile-sidebar').classList.add('hidden')"></div>
        <aside class="absolute left-0 top-0 bottom-0 bg-amber-900 text-amber-100 w-64 flex flex-col">
            <div class="p-4 border-b border-amber-800 flex justify-between items-center">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-2">
                    <img src="{{ asset('images/logo.jpeg') }}" class="h-8 w-8 rounded-lg object-cover" alt="Logo">
                    <span class="font-semibold text-lg">Mamitha Admin</span>
                </a>
                <button onclick="document.getElementById('mobile-sidebar').classList.add('hidden')" class="p-1 hover:bg-amber-800 rounded">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <nav class="flex-1 overflow-y-auto p-3 space-y-1">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.dashboard') ? 'bg-amber-800 text-white font-medium' : 'text-amber-200 hover:bg-amber-800 hover:text-white' }} transition">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    Dashboard
                </a>
                <a href="{{ route('home') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm text-amber-200 hover:bg-amber-800 hover:text-white transition">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Halaman Toko
                </a>
                <a href="{{ route('admin.reports.index') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.reports.*') ? 'bg-amber-800 text-white font-medium' : 'text-amber-200 hover:bg-amber-800 hover:text-white' }} transition">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    Laporan Keuangan
                </a>
                <a href="{{ route('admin.orders.index') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.orders.*') ? 'bg-amber-800 text-white font-medium' : 'text-amber-200 hover:bg-amber-800 hover:text-white' }} transition">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    Order Masuk
                </a>
                <a href="{{ route('admin.products.index') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.products.*') ? 'bg-amber-800 text-white font-medium' : 'text-amber-200 hover:bg-amber-800 hover:text-white' }} transition">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    Produk
                </a>
                <a href="{{ route('admin.categories.index') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.categories.*') ? 'bg-amber-800 text-white font-medium' : 'text-amber-200 hover:bg-amber-800 hover:text-white' }} transition">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    Kategori
                </a>
                <a href="{{ route('admin.testimonials.index') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.testimonials.*') ? 'bg-amber-800 text-white font-medium' : 'text-amber-200 hover:bg-amber-800 hover:text-white' }} transition">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    Testimoni Toko
                </a>
                <a href="{{ route('admin.reviews.index') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.reviews.*') ? 'bg-amber-800 text-white font-medium' : 'text-amber-200 hover:bg-amber-800 hover:text-white' }} transition">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.907c.961 0 1.361 1.248.58 1.81l-3.97 2.883a1 1 0 00-.364 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.971-2.883a1 1 0 00-1.18 0l-3.97 2.883c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.364-1.118L2.98 9.42c-.782-.563-.382-1.81.58-1.81h4.908a1 1 0 00.95-.69l1.519-4.674z"/></svg>
                    Ulasan Produk
                </a>
                <a href="{{ route('admin.kitchen') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.kitchen') ? 'bg-amber-800 text-white font-medium' : 'text-amber-200 hover:bg-amber-800 hover:text-white' }} transition">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z"/></svg>
                    Kitchen
                </a>
                <a href="{{ route('admin.customers') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.customers') ? 'bg-amber-800 text-white font-medium' : 'text-amber-200 hover:bg-amber-800 hover:text-white' }} transition">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/></svg>
                    Pelanggan
                </a>
                <a href="{{ route('admin.settings.index') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.settings.*') ? 'bg-amber-800 text-white font-medium' : 'text-amber-200 hover:bg-amber-800 hover:text-white' }} transition">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Pengaturan Toko
                </a>
            </nav>
            <div class="p-3 border-t border-amber-800">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center w-full px-4 py-2.5 rounded-lg text-sm text-amber-200 hover:bg-amber-800 hover:text-white transition">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Keluar
                    </button>
                </form>
            </div>
        </aside>

    <script>
        document.getElementById('sidebar-toggle')?.addEventListener('click', function() {
            document.getElementById('mobile-sidebar').classList.remove('hidden');
        });
    </script>

    <!-- Admin Alarm Modal Overlay -->
    <div id="admin-alarm-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm hidden transition-all duration-300">
        <div class="bg-white dark:bg-gray-800 rounded-3xl p-8 max-w-md w-full mx-4 shadow-2xl border border-amber-100 dark:border-gray-700 text-center transform scale-95 transition-all duration-300" id="admin-alarm-card">
            <div class="w-20 h-20 bg-amber-100 dark:bg-amber-950/30 rounded-full flex items-center justify-center mx-auto text-4xl shadow-inner mb-5 animate-bounce">
                🔔
            </div>
            <h3 class="text-2xl font-black text-gray-800 dark:text-white">Pesanan Baru Masuk!</h3>
            <p class="text-gray-500 dark:text-gray-400 mt-2 text-sm">Ada pesanan baru masuk dari pelanggan yang harus segera diproses.</p>
            
            <div class="mt-6 flex gap-3">
                <button onclick="stopAdminAlarmAndClose()" class="flex-1 py-3 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-white font-bold rounded-2xl transition">
                    Tutup
                </button>
                <a href="{{ route('admin.orders.index') }}" class="flex-1 py-3 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-2xl shadow-lg transition text-center flex items-center justify-center">
                    Lihat Pesanan
                </a>
            </div>
        </div>
    </div>

    <script>
        let lastOrderId = {{ \App\Models\Order::max('id') ?: 0 }};
        let adminAlarmInterval = null;
        let adminAudioCtx = null;
        
        function initAdminAudio() {
            if (!adminAudioCtx) {
                adminAudioCtx = new (window.AudioContext || window.webkitAudioContext)();
            }
        }
        
        function playAdminAlarmSound() {
            initAdminAudio();
            if (!adminAudioCtx) return;
            
            if (adminAudioCtx.state === 'suspended') {
                adminAudioCtx.resume();
            }
            
            const playTone = (frequency, startTime, duration, type = 'sine') => {
                const osc = adminAudioCtx.createOscillator();
                const gainNode = adminAudioCtx.createGain();
                
                osc.type = type;
                osc.frequency.setValueAtTime(frequency, startTime);
                
                gainNode.gain.setValueAtTime(0.35, startTime);
                gainNode.gain.exponentialRampToValueAtTime(0.0001, startTime + duration);
                
                osc.connect(gainNode);
                gainNode.connect(adminAudioCtx.destination);
                
                osc.start(startTime);
                osc.stop(startTime + duration);
            };
            
            // Double tone chime ringtone
            playTone(880, adminAudioCtx.currentTime, 0.15, 'triangle');
            playTone(1200, adminAudioCtx.currentTime + 0.15, 0.35, 'sine');
        }
        
        function startAdminAlarm() {
            const modal = document.getElementById('admin-alarm-modal');
            const card = document.getElementById('admin-alarm-card');
            if (modal) {
                modal.classList.remove('hidden');
                setTimeout(() => {
                    card.classList.remove('scale-95');
                    card.classList.add('scale-100');
                }, 10);
            }
            
            playAdminAlarmSound();
            if (!adminAlarmInterval) {
                adminAlarmInterval = setInterval(playAdminAlarmSound, 1500);
            }
        }
        
        function stopAdminAlarm() {
            if (adminAlarmInterval) {
                clearInterval(adminAlarmInterval);
                adminAlarmInterval = null;
            }
        }
        
        function stopAdminAlarmAndClose() {
            stopAdminAlarm();
            const modal = document.getElementById('admin-alarm-modal');
            const card = document.getElementById('admin-alarm-card');
            if (modal) {
                card.classList.remove('scale-100');
                card.classList.add('scale-95');
                setTimeout(() => {
                    modal.classList.add('hidden');
                }, 150);
            }
        }
        
        async function checkNewOrdersForAdmin() {
            try {
                const response = await fetch(`{{ route('admin.check-new-orders') }}?last_order_id=${lastOrderId}`);
                if (!response.ok) throw new Error('Response error');
                
                const data = await response.json();
                if (data.new_orders_count > 0) {
                    startAdminAlarm();
                }
            } catch (error) {
                console.error('Error checking new orders:', error);
            }
        }
        
        // Poll every 10 seconds
        setInterval(checkNewOrdersForAdmin, 10000);
        
        // Initialize AudioContext on first user interaction (required by browsers)
        document.addEventListener('click', () => {
            initAdminAudio();
        }, { once: true });
    </script>
    @stack('scripts')
    
    <!-- Theme Toggle Switch Script -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const btn = document.getElementById('theme-toggle-btn');
            const moonIcon = document.getElementById('theme-toggle-moon');
            const sunIcon = document.getElementById('theme-toggle-sun');
            
            function updateToggleIcons() {
                const isDark = document.documentElement.classList.contains('dark');
                if (isDark) {
                    sunIcon.classList.remove('hidden');
                    moonIcon.classList.add('hidden');
                } else {
                    moonIcon.classList.remove('hidden');
                    sunIcon.classList.add('hidden');
                }
            }
            
            // Set initial state
            updateToggleIcons();
            
            btn?.addEventListener('click', () => {
                const isDark = document.documentElement.classList.contains('dark');
                if (isDark) {
                    document.documentElement.classList.remove('dark');
                    document.documentElement.setAttribute('data-theme', 'light');
                    localStorage.setItem('admin-theme', 'light');
                } else {
                    document.documentElement.classList.add('dark');
                    document.documentElement.setAttribute('data-theme', 'dark');
                    localStorage.setItem('admin-theme', 'dark');
                }
                updateToggleIcons();
            });
        });
    </script>
</body>
</html>

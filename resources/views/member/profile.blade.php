@extends('layouts.app')

@section('title', 'Profil Member Saya')

@push('styles')
<style>
    /* -------------------------------------------------------------------------
     * Custom Rank Animations (TikTok Game style UI)
     * ------------------------------------------------------------------------- */
    @keyframes spin-slow {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    .animate-spin-slow {
        animation: spin-slow 15s linear infinite;
    }

    @keyframes bounce-slow {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-8px); }
    }
    .animate-bounce-slow {
        animation: bounce-slow 3s ease-in-out infinite;
    }

    /* Silver Metallic Shimmer Glare */
    @keyframes metallic-sweep {
        0% { background-position: -200% 0; }
        100% { background-position: 200% 0; }
    }
    .shimmer-silver {
        background: linear-gradient(110deg, rgba(255,255,255,0) 35%, rgba(255,255,255,0.4) 50%, rgba(255,255,255,0) 65%);
        background-size: 200% 100%;
        animation: metallic-sweep 3s infinite linear;
    }

    /* Gold Sparkles */
    @keyframes gold-sparkle {
        0%, 100% { transform: scale(1) rotate(0deg); opacity: 0.8; }
        50% { transform: scale(1.25) rotate(180deg); opacity: 1; }
    }
    .sparkle-star {
        animation: gold-sparkle 2.5s infinite ease-in-out;
    }

    /* Platinum Neon Pulse Drop-shadow */
    @keyframes plat-breathing {
        0%, 100% { filter: drop-shadow(0 0 8px rgba(6, 182, 212, 0.4)) drop-shadow(0 0 15px rgba(6, 182, 212, 0.2)); }
        50% { filter: drop-shadow(0 0 20px rgba(6, 182, 212, 0.75)) drop-shadow(0 0 30px rgba(6, 182, 212, 0.4)); }
    }
    .animate-plat-breathing {
        animation: plat-breathing 3.5s infinite ease-in-out;
    }

    /* Progress bar slide animation */
    @keyframes progress-slide {
        from { width: 0%; }
        to { width: var(--progress-width); }
    }
    .progress-bar-fill {
        animation: progress-slide 1.2s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    }
</style>
@endpush

@section('content')
@php
    $theme = $customer->rank_theme;
    $progressPercent = $customer->rank_progress_percentage;
@endphp

<div class="min-h-screen bg-cream-50 dark:bg-gray-900 py-12 px-4 transition-colors duration-200">
    <div class="max-w-4xl mx-auto space-y-8">
        
        <!-- Welcome Greeting & Header -->
        <div class="flex flex-col sm:flex-row justify-between items-center sm:items-start gap-4 border-b border-amber-100/50 dark:border-gray-800 pb-6">
            <div class="text-center sm:text-left space-y-1">
                <h1 class="text-3xl font-black text-amber-900 dark:text-amber-100 font-serif">Dashboard Member</h1>
                <p class="text-xs text-gray-500 dark:text-gray-400">Selamat datang kembali, <span class="font-extrabold text-amber-800 dark:text-amber-400">{{ $customer->name }}</span>! Terima kasih telah setia bersama Mamitha Bakery.</p>
            </div>
            <div class="bg-amber-100/40 dark:bg-amber-950/20 px-4 py-2 rounded-2xl border border-amber-200/30 text-xs font-bold text-amber-800 dark:text-amber-400">
                📅 Member Sejak: {{ $customer->created_at->format('d M Y') }}
            </div>
        </div>

        <!-- 2 Column Layout -->
        <div class="grid md:grid-cols-5 gap-8">
            
            <!-- Left Column: Rank Progression Card (TikTok game theme) -->
            <div class="md:col-span-2 space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-[36px] border border-amber-100/50 dark:border-gray-700/50 shadow-md p-6 relative overflow-hidden text-center group">
                    <!-- Spinning glowing backdrop layer -->
                    <div class="absolute -top-12 -left-12 w-64 h-64 bg-amber-500/5 rounded-full blur-3xl pointer-events-none"></div>
                    <div class="absolute -bottom-12 -right-12 w-64 h-64 bg-orange-500/5 rounded-full blur-3xl pointer-events-none"></div>

                    <!-- Rank Title Header -->
                    <div class="space-y-1 relative z-10">
                        <span class="text-[9px] font-black text-amber-600 dark:text-amber-500 uppercase tracking-[0.2em] block">Status Keanggotaan</span>
                        <h2 class="text-2xl font-black text-gray-900 dark:text-white font-serif tracking-tight">
                            Member <span class="{{ $theme['text'] }}">{{ $customer->rank_name }}</span>
                        </h2>
                    </div>

                    <!-- Rank Badge Center Stage -->
                    <div class="relative w-44 h-44 mx-auto flex items-center justify-center my-6">
                        <!-- Outer rotating blur aura ring -->
                        <div class="absolute inset-0 bg-gradient-to-tr {{ $theme['bg'] }} rounded-full blur-xl opacity-35 animate-spin-slow"></div>
                        
                        <!-- Inner Glowing Circle -->
                        <div class="relative w-32 h-32 bg-gradient-to-tr {{ $theme['bg'] }} rounded-full shadow-2xl flex items-center justify-center border-4 border-white dark:border-gray-800 transition duration-300 transform group-hover:scale-105 {{ $customer->rank_name === 'Platinum' ? 'animate-plat-breathing' : 'animate-bounce-slow' }}">
                            <!-- Shimmer light overlay (Specifically for silver) -->
                            @if($customer->rank_name === 'Silver')
                            <div class="absolute inset-0 rounded-full shimmer-silver pointer-events-none"></div>
                            @endif
                            <span class="text-6xl select-none filter drop-shadow">{{ $customer->rank_badge }}</span>
                        </div>

                        <!-- Ornaments / Particles depending on rank -->
                        @if($customer->rank_name === 'Gold')
                            <span class="absolute top-2 left-6 text-amber-400 text-lg sparkle-star">⭐</span>
                            <span class="absolute top-8 right-6 text-yellow-300 text-sm sparkle-star" style="animation-delay:0.5s;">⭐</span>
                            <span class="absolute bottom-6 left-6 text-amber-500 text-lg sparkle-star" style="animation-delay:1s;">✨</span>
                        @elseif($customer->rank_name === 'Platinum')
                            <span class="absolute top-4 left-6 text-cyan-400 text-sm animate-pulse">✨</span>
                            <span class="absolute top-10 right-4 text-teal-300 text-lg animate-pulse" style="animation-delay:0.7s;">💎</span>
                            <span class="absolute bottom-4 left-8 text-blue-400 text-sm animate-pulse" style="animation-delay:1.4s;">✨</span>
                        @endif
                    </div>

                    <!-- Points Progression Section -->
                    <div class="space-y-4 relative z-10">
                        <div class="flex justify-between items-end px-2">
                            <div class="text-left">
                                <p class="text-[9px] text-gray-400 uppercase font-bold tracking-wider">Total Poin Saya</p>
                                <p class="text-2xl font-black text-amber-800 dark:text-amber-400">{{ $customer->points }} <span class="text-xs text-gray-400 font-semibold">Poin</span></p>
                            </div>
                            <div class="text-right">
                                <p class="text-[9px] text-gray-400 uppercase font-bold tracking-wider">Target Rank Berikutnya</p>
                                <p class="text-xs font-black text-gray-700 dark:text-gray-300">{{ $customer->next_rank_name }}</p>
                            </div>
                        </div>

                        <!-- Progress Bar (TikTok animated style) -->
                        <div class="w-full h-3 bg-gray-150 dark:bg-gray-700 rounded-full overflow-hidden relative shadow-inner p-[1px]">
                            <div class="h-full bg-gradient-to-r {{ $theme['bg'] }} rounded-full progress-bar-fill shadow" 
                                 style="--progress-width: {{ $progressPercent }}%; width: {{ $progressPercent }}%;"></div>
                        </div>

                        <!-- Progress status text -->
                        <p class="text-[11px] text-gray-500 dark:text-gray-400 leading-relaxed">
                            @if($customer->rank_name === 'Platinum')
                                👑 Anda telah mencapai kasta tertinggi! Nikmati layanan prioritas utama.
                            @else
                                Kumpulkan <span class="font-extrabold text-amber-700 dark:text-amber-400">{{ $customer->points_for_next_rank }} Poin</span> lagi untuk naik ke <span class="font-bold text-gray-700 dark:text-gray-350">{{ $customer->next_rank_name }} Member</span>.
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Right Column: Member Info & Order Stats -->
            <div class="md:col-span-3 space-y-6">
                <!-- Member Virtual Card (TikTok Style) -->
                <div class="bg-gradient-to-br from-amber-700 to-amber-900 rounded-[32px] p-6 text-white shadow-xl relative overflow-hidden border border-amber-600/30">
                    <!-- Bakery Background patterns overlay -->
                    <div class="absolute inset-0 opacity-15 bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:12px_12px] z-0"></div>
                    <div class="absolute -right-10 -bottom-10 w-44 h-44 bg-white/5 rounded-full blur-xl pointer-events-none"></div>
                    
                    <div class="relative z-10 h-full flex flex-col justify-between space-y-8">
                        <!-- Top details -->
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-[9px] uppercase font-bold tracking-widest text-amber-200">Kartu Member Eksklusif</p>
                                <h3 class="text-xl font-bold font-serif mt-1">MAMITHA MEMBER</h3>
                            </div>
                            <span class="text-3xl filter drop-shadow">🥐</span>
                        </div>

                        <!-- Code / ID -->
                        <div>
                            <p class="text-[9px] uppercase font-bold tracking-widest text-amber-200">ID Anggota</p>
                            <p class="text-xl font-mono font-bold tracking-wider mt-0.5">MTH-MBR-{{ str_pad($customer->id, 5, '0', STR_PAD_LEFT) }}</p>
                        </div>

                        <!-- Bottom row: owner & rank badge -->
                        <div class="flex justify-between items-end pt-2 border-t border-white/10">
                            <div>
                                <p class="text-[9px] uppercase font-bold tracking-widest text-amber-200">Nama Pemegang</p>
                                <p class="text-sm font-black uppercase mt-0.5 tracking-wide">{{ $customer->name }}</p>
                            </div>
                            <span class="bg-white/20 border border-white/30 rounded-xl px-3 py-1 text-xs font-black uppercase tracking-wider flex items-center gap-1.5 backdrop-blur-sm shadow-sm select-none">
                                {{ $customer->rank_badge }} {{ $customer->rank_name }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Personal Information Section -->
                <div class="bg-white dark:bg-gray-800 rounded-3xl border border-amber-100/50 dark:border-gray-700/50 shadow-sm p-6 space-y-4">
                    <h3 class="font-bold text-gray-800 dark:text-gray-100 font-serif text-lg">Informasi Profil</h3>
                    <div class="grid grid-cols-2 gap-4 text-xs">
                        <div class="space-y-1">
                            <span class="text-gray-400 font-medium">Nama Lengkap</span>
                            <p class="font-bold text-gray-800 dark:text-gray-200">{{ $customer->name }}</p>
                        </div>
                        <div class="space-y-1">
                            <span class="text-gray-400 font-medium">Nomor WhatsApp</span>
                            <p class="font-bold text-gray-800 dark:text-gray-200">{{ $customer->phone ?: '-' }}</p>
                        </div>
                        <div class="space-y-1">
                            <span class="text-gray-400 font-medium">Email Terdaftar</span>
                            <p class="font-bold text-gray-800 dark:text-gray-200">{{ $customer->user->email }}</p>
                        </div>
                        <div class="space-y-1 col-span-2">
                            <span class="text-gray-400 font-medium">Alamat Default Pengantaran</span>
                            <p class="font-bold text-gray-800 dark:text-gray-200 leading-relaxed">{{ $customer->address ?: '-' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Appearance Customization Card (TikTok Style) -->
                <div class="bg-white dark:bg-gray-800 rounded-3xl border border-amber-100/50 dark:border-gray-700/50 shadow-sm p-6 space-y-4">
                    <div class="space-y-1">
                        <span class="text-[9px] font-black text-amber-600 dark:text-amber-500 uppercase tracking-[0.2em] block">Kustomisasi Tampilan</span>
                        <h3 class="font-bold text-gray-850 dark:text-gray-100 font-serif text-lg">Tema Tampilan (Appearance)</h3>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <!-- Daylight Option -->
                        <div id="theme-daylight" onclick="setAppTheme('light')" class="border-2 rounded-2xl p-4 cursor-pointer transition flex flex-col justify-between text-left h-36 relative overflow-hidden group select-none bg-amber-50/10 dark:bg-amber-950/5">
                            <div class="flex justify-between items-start">
                                <!-- Card Mockup Icon -->
                                <div class="w-12 h-8 bg-gradient-to-br from-amber-100 to-amber-200 border border-amber-200/50 rounded-md flex items-center justify-center text-xs shadow-sm">
                                    🍞
                                </div>
                                <!-- Radio indicator -->
                                <div class="theme-radio w-5 h-5 rounded-full border-2 border-gray-300 flex items-center justify-center transition">
                                    <div class="theme-radio-inner w-2.5 h-2.5 rounded-full bg-amber-600 hidden"></div>
                                </div>
                            </div>
                            <div>
                                <h4 class="text-xs font-black text-gray-850 dark:text-gray-100 tracking-wide flex items-center gap-1.5">
                                    ☀️ Daylight
                                </h4>
                                <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-1 leading-normal">
                                    Bright & warm cream bakery theme.
                                </p>
                            </div>
                        </div>

                        <!-- Midnight Option -->
                        <div id="theme-midnight" onclick="setAppTheme('dark')" class="border-2 rounded-2xl p-4 cursor-pointer transition flex flex-col justify-between text-left h-36 relative overflow-hidden group select-none bg-gray-900/5 dark:bg-gray-800/20">
                            <div class="flex justify-between items-start">
                                <!-- Card Mockup Icon -->
                                <div class="w-12 h-8 bg-gradient-to-br from-gray-750 to-gray-900 border border-gray-700/50 rounded-md flex items-center justify-center text-xs shadow-sm text-white">
                                    🥐
                                </div>
                                <!-- Radio indicator -->
                                <div class="theme-radio w-5 h-5 rounded-full border-2 border-gray-300 flex items-center justify-center transition">
                                    <div class="theme-radio-inner w-2.5 h-2.5 rounded-full bg-amber-600 hidden"></div>
                                </div>
                            </div>
                            <div>
                                <h4 class="text-xs font-black text-gray-850 dark:text-gray-100 tracking-wide flex items-center gap-1.5">
                                    🌙 Midnight
                                </h4>
                                <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-1 leading-normal">
                                    Easy on the eyes night-shift mode.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Preferences Card (TikTok Style) -->
                <div class="bg-white dark:bg-gray-800 rounded-3xl border border-amber-100/50 dark:border-gray-700/50 shadow-sm p-6 space-y-4">
                    <div class="space-y-1">
                        <span class="text-[9px] font-black text-amber-600 dark:text-amber-500 uppercase tracking-[0.2em] block">Preferensi</span>
                        <h3 class="font-bold text-gray-850 dark:text-gray-100 font-serif text-lg">Pengaturan Tambahan</h3>
                    </div>
                    
                    <div class="divide-y divide-amber-50 dark:divide-gray-700/50">
                        <!-- Push Notifications Preference -->
                        <div class="py-3.5 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl bg-amber-50 dark:bg-amber-950/30 flex items-center justify-center text-sm shadow-inner select-none">
                                    🔔
                                </div>
                                <div>
                                    <h4 class="text-xs font-black text-gray-800 dark:text-gray-255">Push Notifications</h4>
                                    <p class="text-[9px] text-gray-400 dark:text-gray-500">Terima info diskon roti hangat terbaru.</p>
                                </div>
                            </div>
                            <!-- Switch Button -->
                            <button type="button" onclick="togglePreference('push_notif')" id="pref-push_notif" class="w-12 h-6 rounded-full bg-gray-200 dark:bg-gray-700 relative p-1 transition-colors duration-200 focus:outline-none select-none cursor-pointer">
                                <div class="switch-dot w-4 h-4 bg-white rounded-full shadow-md transform transition-transform duration-200"></div>
                            </button>
                        </div>

                        <!-- Face ID Preference -->
                        <div class="py-3.5 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl bg-amber-50 dark:bg-amber-950/30 flex items-center justify-center text-sm shadow-inner select-none">
                                    📸
                                </div>
                                <div>
                                    <h4 class="text-xs font-black text-gray-800 dark:text-gray-255">Unlock with Face ID</h4>
                                    <p class="text-[9px] text-gray-400 dark:text-gray-500">Masuk cepat menggunakan deteksi wajah.</p>
                                </div>
                            </div>
                            <!-- Switch Button -->
                            <button type="button" onclick="togglePreference('face_id')" id="pref-face_id" class="w-12 h-6 rounded-full bg-gray-200 dark:bg-gray-700 relative p-1 transition-colors duration-200 focus:outline-none select-none cursor-pointer">
                                <div class="switch-dot w-4 h-4 bg-white rounded-full shadow-md transform transition-transform duration-200"></div>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section: Order History -->
        <div class="space-y-4">
            <h3 class="text-xl font-bold text-amber-900 dark:text-amber-100 font-serif">Riwayat Pesanan Member</h3>
            
            @forelse($orders as $order)
            <!-- Order Card -->
            <div class="bg-white dark:bg-gray-800 rounded-3xl border border-amber-100/50 dark:border-gray-700/50 shadow-sm p-6 hover:shadow-md transition duration-200 space-y-4">
                <!-- Top Header: Date, Number & Status -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-gray-50 dark:border-gray-700/50 pb-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] text-gray-400 uppercase font-bold tracking-wider">No. Pesanan:</span>
                            <span class="font-extrabold text-sm text-amber-800 dark:text-amber-400">{{ $order->order_number }}</span>
                        </div>
                        <p class="text-[10px] text-gray-400 mt-0.5">{{ $order->order_date->format('d M Y, H:i') }}</p>
                    </div>
                    
                    <div class="flex items-center gap-2">
                        <!-- Tipe Badge -->
                        <span class="px-2 py-0.5 text-[10px] font-extrabold rounded-lg shadow-inner {{ $order->type === 'delivery' ? 'bg-blue-50 text-blue-700 dark:bg-blue-950/20 dark:text-blue-400' : 'bg-amber-50 text-amber-800 dark:bg-amber-950/20 dark:text-amber-300' }}">
                            {{ $order->type === 'delivery' ? '🚚 Delivery' : '🏪 Pickup' }}
                        </span>
                        
                        <!-- Status Badge -->
                        @if($order->status == 'pending')
                            <span class="px-2.5 py-0.5 bg-yellow-500/10 text-yellow-600 dark:text-yellow-400 text-[10px] font-black rounded-lg border border-yellow-500/20">⏳ Pending</span>
                        @elseif($order->status == 'confirmed')
                            <span class="px-2.5 py-0.5 bg-blue-500/10 text-blue-600 dark:text-blue-400 text-[10px] font-black rounded-lg border border-blue-500/20">👍 Dikonfirmasi</span>
                        @elseif($order->status == 'producing')
                            <span class="px-2.5 py-0.5 bg-orange-500/10 text-orange-600 dark:text-orange-400 text-[10px] font-black rounded-lg border border-orange-500/20">🔥 Dipanggang</span>
                        @elseif($order->status == 'ready')
                            <span class="px-2.5 py-0.5 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[10px] font-black rounded-lg border border-emerald-500/20">🛵 Siap</span>
                        @elseif($order->status == 'done')
                            <span class="px-2.5 py-0.5 bg-green-500/10 text-green-600 dark:text-green-400 text-[10px] font-black rounded-lg border border-green-500/20">✅ Selesai</span>
                        @else
                            <span class="px-2.5 py-0.5 bg-red-500/10 text-red-600 dark:text-red-400 text-[10px] font-black rounded-lg border border-red-500/20">❌ Dibatalkan</span>
                        @endif
                    </div>
                </div>

                <!-- Items list -->
                <div class="divide-y divide-gray-50 dark:divide-gray-700/30">
                    @foreach($order->items as $item)
                    <div class="py-2.5 flex items-center justify-between text-xs">
                        <div class="flex items-center gap-3">
                            @if($item->product && $item->product->image)
                                <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}" class="w-8 h-8 object-cover rounded-lg shadow-sm">
                            @else
                                <div class="w-8 h-8 bg-amber-50 dark:bg-amber-950/20 rounded-lg flex items-center justify-center text-base shadow-inner">🍞</div>
                            @endif
                            <div>
                                <p class="font-bold text-gray-800 dark:text-gray-200">
                                    {{ $item->product->name }}
                                    @if($item->variant)
                                        <span class="text-[10px] text-gray-500 dark:text-gray-400 font-normal">({{ $item->variant->name }})</span>
                                    @endif
                                </p>
                                <p class="text-[10px] text-gray-450 mt-0.5">Rp {{ number_format($item->price, 0, ',', '.') }} x {{ $item->quantity }}</p>
                            </div>
                        </div>
                        <span class="font-bold text-gray-800 dark:text-gray-200">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                    @endforeach
                </div>

                <!-- Summary Row -->
                <div class="border-t border-gray-150/40 dark:border-gray-700/50 pt-4 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="text-center sm:text-left flex items-center gap-6">
                        <div>
                            <p class="text-[9px] text-gray-400 uppercase font-bold tracking-wider">Total Belanja</p>
                            <p class="text-base font-black text-amber-800 dark:text-amber-400">Rp {{ number_format($order->total, 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-amber-50 dark:bg-amber-950/30 px-3 py-1 rounded-xl border border-amber-200/20 text-center">
                            <p class="text-[9px] text-amber-700 dark:text-amber-500 uppercase font-bold tracking-wider">Poin Diperoleh</p>
                            <p class="text-xs font-black text-amber-850 dark:text-amber-400">+{{ (int) floor($order->total / 10000) }} Poin</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <!-- Contact store -->
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $storeWhatsapp) }}?text=Halo%20Mamitha%20Bakery,%20saya%20ingin%20tanya%2520mengenai%20order%20{{ $order->order_number }}" target="_blank" class="flex-1 sm:flex-none px-3.5 py-2 bg-green-500 hover:bg-green-600 text-white font-extrabold text-[10px] rounded-xl text-center transition flex items-center justify-center gap-1 active:scale-95">
                            💬 Hubungi Toko
                        </a>
                        
                        <!-- Lacak detail -->
                        <a href="{{ route('order.success', $order->id) }}" class="flex-1 sm:flex-none px-3.5 py-2 bg-gray-900 dark:bg-gray-100 hover:bg-amber-700 dark:hover:bg-amber-500 text-white dark:text-gray-900 font-extrabold text-[10px] rounded-xl text-center transition flex items-center justify-center gap-1 active:scale-95">
                            📍 Lacak Status &rarr;
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="bg-white dark:bg-gray-800 rounded-3xl border border-amber-100/50 dark:border-gray-700/50 p-12 text-center space-y-3 shadow-sm">
                <span class="text-4xl block">🥐</span>
                <h4 class="font-bold text-gray-800 dark:text-gray-150 font-serif">Belum Ada Transaksi</h4>
                <p class="text-xs text-gray-500 dark:text-gray-400 max-w-xs mx-auto leading-relaxed">Silakan lakukan pesanan roti hangat pertama Anda untuk mulai mengumpulkan poin dan meningkatkan Rank member!</p>
                <a href="{{ route('order.create') }}" class="inline-block px-5 py-2.5 bg-gradient-to-r from-amber-600 to-orange-600 text-white text-xs font-black rounded-xl transition shadow active:scale-95">Mulai Pesan Roti</a>
            </div>
            @endforelse
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    function setAppTheme(theme) {
        if (theme === 'dark') {
            document.documentElement.classList.add('dark');
            localStorage.setItem('theme', 'dark');
            
            // Update UI selected state for Midnight
            document.getElementById('theme-midnight').classList.add('border-amber-500', 'bg-amber-50/5');
            document.getElementById('theme-midnight').classList.remove('border-gray-200', 'dark:border-gray-700');
            document.querySelector('#theme-midnight .theme-radio').classList.add('border-amber-500');
            document.querySelector('#theme-midnight .theme-radio').classList.remove('border-gray-350');
            document.querySelector('#theme-midnight .theme-radio-inner').classList.remove('hidden');
            
            // Reset Daylight UI
            document.getElementById('theme-daylight').classList.remove('border-amber-500', 'bg-amber-50/5');
            document.getElementById('theme-daylight').classList.add('border-gray-200', 'dark:border-gray-700');
            document.querySelector('#theme-daylight .theme-radio').classList.remove('border-amber-500');
            document.querySelector('#theme-daylight .theme-radio').classList.add('border-gray-350');
            document.querySelector('#theme-daylight .theme-radio-inner').classList.add('hidden');
        } else {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('theme', 'light');
            
            // Update UI selected state for Daylight
            document.getElementById('theme-daylight').classList.add('border-amber-500', 'bg-amber-50/5');
            document.getElementById('theme-daylight').classList.remove('border-gray-200', 'dark:border-gray-700');
            document.querySelector('#theme-daylight .theme-radio').classList.add('border-amber-500');
            document.querySelector('#theme-daylight .theme-radio').classList.remove('border-gray-350');
            document.querySelector('#theme-daylight .theme-radio-inner').classList.remove('hidden');
            
            // Reset Midnight UI
            document.getElementById('theme-midnight').classList.remove('border-amber-500', 'bg-amber-50/5');
            document.getElementById('theme-midnight').classList.add('border-gray-200', 'dark:border-gray-700');
            document.querySelector('#theme-midnight .theme-radio').classList.remove('border-amber-500');
            document.querySelector('#theme-midnight .theme-radio').classList.add('border-gray-350');
            document.querySelector('#theme-midnight .theme-radio-inner').classList.add('hidden');
        }
    }

    function togglePreference(key) {
        const btn = document.getElementById(`pref-${key}`);
        const dot = btn.querySelector('.switch-dot');
        const isActive = btn.classList.contains('bg-orange-600');
        
        if (isActive) {
            btn.classList.remove('bg-orange-600');
            btn.classList.add('bg-gray-200', 'dark:bg-gray-700');
            dot.classList.remove('translate-x-6');
            localStorage.setItem(`pref_${key}`, 'false');
        } else {
            btn.classList.add('bg-orange-600');
            btn.classList.remove('bg-gray-200', 'dark:bg-gray-700');
            dot.classList.add('translate-x-6');
            localStorage.setItem(`pref_${key}`, 'true');
        }
    }

    // Initialize theme and preference toggles state on load
    window.addEventListener('load', () => {
        const currentTheme = localStorage.getItem('theme') || 
            (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        setAppTheme(currentTheme);

        // Init Preferences toggles to true by default (matching screenshot)
        ['push_notif', 'face_id'].forEach(key => {
            const btn = document.getElementById(`pref-${key}`);
            if (!btn) return;
            const dot = btn.querySelector('.switch-dot');
            const val = localStorage.getItem(`pref_${key}`) !== 'false';
            
            if (val) {
                btn.classList.add('bg-orange-600');
                btn.classList.remove('bg-gray-200', 'dark:bg-gray-700');
                dot.classList.add('translate-x-6');
            } else {
                btn.classList.remove('bg-orange-600');
                btn.classList.add('bg-gray-200', 'dark:bg-gray-700');
                dot.classList.remove('translate-x-6');
            }
        });
    });
</script>
@endpush

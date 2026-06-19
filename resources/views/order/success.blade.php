@extends('layouts.app')

@section('title', 'Status Pelacakan Pesanan')

@push('styles')
<!-- Leaflet CSS for Map -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<style>
    #tracking-map {
        height: 280px;
        z-index: 10;
    }
    /* Dark Mode override for Leaflet on success page */
    .dark #tracking-map .leaflet-tile {
        filter: brightness(0.6) invert(1) contrast(3) hue-rotate(200deg) saturate(0.3);
    }

    /* Level Up Modal Custom CSS */
    @keyframes spin-slow {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    .animate-spin-slow {
        animation: spin-slow 10s linear infinite;
    }

    @keyframes bounce-slow {
        0%, 100% { transform: translateY(0) scale(1); }
        50% { transform: translateY(-8px) scale(1.15); }
    }
    .animate-bounce-slow {
        animation: bounce-slow 3s ease-in-out infinite;
    }

    @keyframes pulse-btn {
        0%, 100% { box-shadow: 0 10px 20px -5px rgba(217, 119, 6, 0.45); transform: scale(1); }
        50% { box-shadow: 0 15px 25px 0px rgba(217, 119, 6, 0.65); transform: scale(1.025); }
    }
    .animate-pulse-btn {
        animation: pulse-btn 2s infinite ease-in-out;
    }

    .scale-up-badge {
        animation: scale-up-badge-anim 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        animation-delay: 0.3s;
        transform: scale(0);
    }
    @keyframes scale-up-badge-anim {
        to { transform: scale(1); }
    }
</style>
@endpush

@section('content')
@php
    $storeLat = \App\Models\Setting::getValue('store_latitude', '-7.7609582');
    $storeLng = \App\Models\Setting::getValue('store_longitude', '110.2529556');
    $hasRoute = $order->type === 'delivery' && $order->latitude && $order->longitude;
@endphp

@php
    $showModal = false;
    $modalType = 'none'; // 'levelup', 'points', 'guest'
    
    if (session()->has('level_up')) {
        $showModal = true;
        $modalType = 'levelup';
        $levelUpData = session('level_up'); // ['old' => '...', 'new' => '...', 'badge' => '...']
        $rankName = $levelUpData['new'];
        $rankBadge = $levelUpData['badge'];
    } elseif (session()->has('points_earned') && $order->customer->is_member) {
        $showModal = true;
        $modalType = 'points';
        $pointsEarned = session('points_earned');
        $rankName = $order->customer->rank_name;
        $rankBadge = $order->customer->rank_badge;
    } elseif ($whatsappUrl && !$order->customer->is_member) {
        $showModal = true;
        $modalType = 'guest';
    }
@endphp

@if($showModal)
<!-- Level Up / Rank Unlocked Modal Overlay (TikTok Style) -->
<div id="success-levelup-modal" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 backdrop-blur-md transition-opacity duration-500 ease-out opacity-0 pointer-events-none">
    <div id="success-card-sheet" class="bg-white dark:bg-gray-900 rounded-[36px] p-8 max-w-sm w-full mx-4 shadow-2xl border border-amber-100/50 dark:border-gray-800/80 text-center relative overflow-hidden transform scale-75 opacity-0 transition-all duration-500 ease-out">
        <!-- Canvas for particle star burst -->
        <canvas id="star-canvas" class="absolute inset-0 pointer-events-none z-0"></canvas>
        
        <!-- Rotating light rays backdrop -->
        <div class="absolute -top-12 -left-12 w-64 h-64 bg-amber-500/10 rounded-full blur-3xl pointer-events-none z-0"></div>
        <div class="absolute -bottom-12 -right-12 w-64 h-64 bg-orange-500/10 rounded-full blur-3xl pointer-events-none z-0"></div>

        <div class="relative z-10 space-y-6">
            @if($modalType === 'levelup')
                <!-- Header Text -->
                <span class="text-[10px] font-black text-amber-600 dark:text-amber-500 uppercase tracking-[0.3em] block animate-pulse">
                    Rank Unlocked
                </span>

                <!-- Main Title -->
                <h2 class="text-3xl font-black text-gray-955 dark:text-white font-serif tracking-tight leading-none">
                    Rank Naik!
                </h2>

                <!-- Theme class resolution -->
                @php
                    $themeClass = 'from-amber-400 via-amber-250 to-yellow-500';
                    if ($rankName === 'Platinum') $themeClass = 'from-cyan-400 via-teal-300 to-blue-500';
                    elseif ($rankName === 'Gold') $themeClass = 'from-amber-400 via-amber-250 to-yellow-500';
                    elseif ($rankName === 'Silver') $themeClass = 'from-slate-400 via-gray-250 to-slate-500';
                    elseif ($rankName === 'Bronze') $themeClass = 'from-orange-500 via-orange-350 to-amber-600';
                @endphp

                <!-- Rank Badge Icon with rotating glow -->
                <div class="relative w-40 h-40 mx-auto flex items-center justify-center">
                    <!-- Outer spinning blur ring -->
                    <div class="absolute inset-0 bg-gradient-to-tr {{ $themeClass }} rounded-full blur-xl opacity-35 animate-spin-slow"></div>
                    
                    <!-- Inner Rank Badge Circle -->
                    <div class="relative w-28 h-28 bg-gradient-to-tr {{ $themeClass }} rounded-full shadow-xl flex items-center justify-center border-4 border-white dark:border-gray-800 scale-up-badge">
                        <span class="text-5xl select-none filter drop-shadow">{{ $rankBadge }}</span>
                    </div>

                    <!-- Floating Star Ornaments -->
                    <span class="absolute top-2 left-6 text-amber-400 text-lg animate-bounce-slow" style="animation-delay: 0.1s;">⭐</span>
                    <span class="absolute top-8 right-4 text-yellow-300 text-sm animate-bounce-slow" style="animation-delay: 0.4s;">⭐</span>
                    <span class="absolute bottom-4 left-4 text-amber-500 text-lg animate-bounce-slow" style="animation-delay: 0.7s;">✨</span>
                </div>

                <!-- Description -->
                <div class="space-y-1.5">
                    <p class="text-sm font-black text-gray-800 dark:text-gray-200">Pesanan {{ $order->order_number }} Sukses!</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed max-w-xs mx-auto">
                        Selamat! Rank member Anda naik dari <span class="font-extrabold text-amber-850 dark:text-amber-400">{{ $levelUpData['old'] }}</span> menjadi <span class="font-extrabold text-amber-850 dark:text-amber-400">{{ $rankName }}</span>!
                    </p>
                </div>

                <!-- Reward Unlocked Card (Tiktok Style) -->
                <div class="bg-amber-50/50 dark:bg-amber-950/10 rounded-2xl p-4 border border-amber-100/40 dark:border-amber-900/20 text-left flex items-start gap-3">
                    <span class="text-2xl mt-0.5 select-none">🎁</span>
                    <div>
                        <h4 class="text-xs font-black text-amber-800 dark:text-amber-400 uppercase tracking-wide">
                            Benefit Rank {{ $rankName }}
                        </h4>
                        <p class="text-[11px] text-gray-650 dark:text-gray-350 mt-0.5 leading-relaxed">
                            @if($rankName === 'Platinum')
                                Anda mendapatkan prioritas utama antrean panggangan cepat, free delivery khusus, & gift voucher bulanan!
                            @elseif($rankName === 'Gold')
                                Prioritas panggangan cepat & voucher potongan 10% untuk pesanan berikutnya!
                            @elseif($rankName === 'Silver')
                                Bonus poin extra +10% untuk setiap transaksi berikutnya!
                            @else
                                Kumpulkan terus poin belanja Anda untuk naik ke rank berikutnya!
                            @endif
                        </p>
                    </div>
                </div>

            @elseif($modalType === 'points')
                <!-- Header Text -->
                <span class="text-[10px] font-black text-amber-600 dark:text-amber-500 uppercase tracking-[0.3em] block animate-pulse">
                    Poin Diperoleh
                </span>

                <!-- Main Title -->
                <h2 class="text-3xl font-black text-gray-950 dark:text-white font-serif tracking-tight leading-none">
                    +{{ $pointsEarned }} Poin!
                </h2>

                <!-- Theme class resolution -->
                @php
                    $themeClass = 'from-amber-400 via-amber-250 to-yellow-500';
                    if ($rankName === 'Platinum') $themeClass = 'from-cyan-400 via-teal-300 to-blue-500';
                    elseif ($rankName === 'Gold') $themeClass = 'from-amber-400 via-amber-250 to-yellow-500';
                    elseif ($rankName === 'Silver') $themeClass = 'from-slate-400 via-gray-250 to-slate-500';
                    elseif ($rankName === 'Bronze') $themeClass = 'from-orange-500 via-orange-350 to-amber-600';
                @endphp

                <!-- Rank Badge Icon -->
                <div class="relative w-40 h-40 mx-auto flex items-center justify-center">
                    <div class="absolute inset-0 bg-gradient-to-tr {{ $themeClass }} rounded-full blur-xl opacity-20 animate-spin-slow"></div>
                    <div class="relative w-28 h-28 bg-gradient-to-tr {{ $themeClass }} rounded-full shadow-xl flex items-center justify-center border-4 border-white dark:border-gray-800 scale-up-badge">
                        <span class="text-5xl select-none filter drop-shadow">{{ $rankBadge }}</span>
                    </div>
                </div>

                <!-- Description -->
                <div class="space-y-1.5">
                    <p class="text-sm font-black text-gray-800 dark:text-gray-200">Pesanan {{ $order->order_number }} Sukses!</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed max-w-xs mx-auto">
                        Berhasil mendapatkan <span class="font-extrabold text-amber-800 dark:text-amber-400">{{ $pointsEarned }} poin</span>! Total poin Anda sekarang adalah <span class="font-extrabold text-amber-800 dark:text-amber-400">{{ $order->customer->points }} poin</span>.
                    </p>
                </div>

                <!-- Progress to Next Rank -->
                @if($order->customer->rank_name !== 'Platinum')
                <div class="bg-amber-50/50 dark:bg-amber-950/10 rounded-2xl p-4 border border-amber-100/40 dark:border-amber-900/20 text-left space-y-2">
                    <div class="flex justify-between items-center text-[10px] uppercase font-bold text-gray-500 dark:text-gray-400">
                        <span>Progress {{ $order->customer->next_rank_name }}</span>
                        <span>{{ $order->customer->points }} / {{ $order->customer->next_rank_points }} Poin</span>
                    </div>
                    <div class="w-full h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r {{ $themeClass }} rounded-full" style="width: {{ $order->customer->rank_progress_percentage }}%"></div>
                    </div>
                    <p class="text-[10px] text-gray-500 dark:text-gray-400 leading-tight">
                        Kumpulkan <span class="font-bold text-amber-700 dark:text-amber-400">{{ $order->customer->points_for_next_rank }} Poin</span> lagi untuk naik ke rank <span class="font-bold">{{ $order->customer->next_rank_name }}</span>!
                    </p>
                </div>
                @else
                <div class="bg-amber-50/50 dark:bg-amber-950/10 rounded-2xl p-4 border border-amber-100/40 dark:border-amber-900/20 text-center">
                    <p class="text-xs text-cyan-600 dark:text-cyan-400 font-extrabold">👑 Level Maksimal Tercapai!</p>
                    <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-1">Anda berada di rank Platinum (Kasta tertinggi). Terima kasih atas kesetiaan Anda!</p>
                </div>
                @endif

            @else
                <!-- Guest Welcome Modal -->
                <!-- Header Text -->
                <span class="text-[10px] font-black text-amber-600 dark:text-amber-500 uppercase tracking-[0.3em] block animate-pulse">
                    Order Confirmed
                </span>

                <!-- Main Title -->
                <h2 class="text-3xl font-black text-gray-950 dark:text-white font-serif tracking-tight leading-none">
                    Roti Siap Dipanggang!
                </h2>

                <!-- Croissant Badge Icon -->
                <div class="relative w-40 h-40 mx-auto flex items-center justify-center">
                    <div class="absolute inset-0 bg-gradient-to-tr from-amber-500 to-orange-500 rounded-full blur-xl opacity-35 animate-spin-slow"></div>
                    <div class="relative w-28 h-28 bg-gradient-to-tr from-amber-400 via-amber-250 to-yellow-500 rounded-full shadow-xl flex items-center justify-center border-4 border-white dark:border-gray-800 scale-up-badge">
                        <span class="text-5xl select-none filter drop-shadow">🥐</span>
                    </div>
                </div>

                <!-- Description -->
                <div class="space-y-1.5">
                    <p class="text-sm font-black text-gray-800 dark:text-gray-200">Pesanan {{ $order->order_number }} Sukses!</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed max-w-xs mx-auto">
                        Terima kasih! Roti kesukaan Anda sedang dipersiapkan dan segera masuk antrean panggang hangat di dapur Mamitha.
                    </p>
                </div>

                <!-- Member Promotion Invitation Card -->
                <div class="bg-amber-50/50 dark:bg-amber-950/10 rounded-2xl p-4 border border-amber-100/40 dark:border-amber-900/20 text-left flex items-start gap-3">
                    <span class="text-2xl mt-0.5 select-none">✨</span>
                    <div>
                        <h4 class="text-xs font-black text-amber-800 dark:text-amber-400 uppercase tracking-wide">
                            Gabung Member Mamitha
                        </h4>
                        <p class="text-[11px] text-gray-650 dark:text-gray-350 mt-0.5 leading-relaxed">
                            Dapatkan <span class="font-extrabold text-amber-800 dark:text-amber-400">diskon 10% otomatis</span> untuk setiap pesanan berikutnya dan kumpulkan poin belanja Anda untuk naik rank!
                        </p>
                    </div>
                </div>
            @endif

            <!-- Pulsing Action Button -->
            <button type="button" onclick="dismissLevelUpModal()" class="w-full py-4 bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-700 hover:to-orange-700 text-white font-extrabold text-sm rounded-2xl shadow-xl transition-all duration-300 transform active:scale-95 hover:shadow-2xl hover:shadow-orange-500/20 animate-pulse-btn">
                Lacak Pesanan Saya &rarr;
            </button>
        </div>
    </div>
</div>
@endif

<div class="min-h-screen bg-cream-50 dark:bg-gray-900 py-12 px-4 transition-colors duration-200">
    <div class="max-w-2xl mx-auto space-y-6">
        
        <!-- Main Success Tracker Card -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl border border-amber-100/50 dark:border-gray-700/50 shadow-xl overflow-hidden">
            @if($hasRoute)
            <!-- Leaflet Tracking Map (Header of Card) -->
            <div class="relative">
                <div id="tracking-map" class="w-full"></div>
                
                <!-- GPS Track Me Button -->
                <button type="button" onclick="trackMyLocation()" id="track-gps-btn" class="absolute bottom-4 right-4 z-30 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 p-2.5 rounded-full shadow-md hover:bg-gray-100 dark:hover:bg-gray-700 transition flex items-center justify-center border border-gray-200 dark:border-gray-700 animate-pulse hover:animate-none" style="animation-duration: 3s;" title="Lacak Lokasi Saya">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" id="track-gps-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </button>

                <div class="absolute top-4 left-4 z-20 bg-gray-900/90 dark:bg-gray-800/90 text-white rounded-2xl px-4 py-2 text-xs font-bold shadow-md">
                    @if($order->status == 'pending')
                        ⏳ Menunggu Konfirmasi
                    @elseif($order->status == 'confirmed')
                        👍 Pesanan Dikonfirmasi
                    @elseif($order->status == 'producing')
                        🔥 Sedang Dipanggang
                    @elseif($order->status == 'ready')
                        🛵 Siap Diambil / Diantar
                    @elseif($order->status == 'done')
                        ✅ Pesanan Selesai
                    @else
                        ❌ Dibatalkan
                    @endif
                </div>
            </div>
            @else
            <!-- Status Header without Map -->
            <div class="p-4 bg-gray-900 dark:bg-gray-800 flex justify-center">
                <div class="bg-gray-800 dark:bg-gray-700 border border-gray-700 dark:border-gray-600 text-white rounded-2xl px-6 py-2.5 text-sm font-bold shadow-md">
                    @if($order->status == 'pending')
                        ⏳ Menunggu Konfirmasi
                    @elseif($order->status == 'confirmed')
                        👍 Pesanan Dikonfirmasi
                    @elseif($order->status == 'producing')
                        🔥 Sedang Dipanggang
                    @elseif($order->status == 'ready')
                        🛵 Siap Diambil / Diantar
                    @elseif($order->status == 'done')
                        ✅ Pesanan Selesai
                    @else
                        ❌ Dibatalkan
                    @endif
                </div>
            </div>
            @endif

            <!-- Arrival estimate banner -->
            <div class="p-6 border-b border-gray-100 dark:border-gray-700/50 flex flex-col sm:flex-row justify-between items-center gap-4 bg-gray-50/50 dark:bg-gray-800/30">
                <div class="text-center sm:text-left">
                    <p class="text-xs text-gray-400 dark:text-gray-500 font-bold uppercase tracking-wider">Estimasi Waktu</p>
                    <h2 class="text-2xl font-black text-amber-800 dark:text-amber-400 font-serif">
                        @if($order->status == 'done')
                            Pesanan Telah Tiba
                        @elseif($order->type == 'pickup')
                            Siap Diambil Jam 15-20 min
                        @else
                            Tiba dalam 15-20 min
                        @endif
                    </h2>
                </div>
                <div class="text-center sm:text-right">
                    <p class="text-xs text-gray-400 dark:text-gray-500 font-bold uppercase tracking-wider">Nomor Pesanan</p>
                    <p class="text-lg font-extrabold text-gray-800 dark:text-gray-200">{{ $order->order_number }}</p>
                </div>
            </div>

            <!-- Courier Card (TikTok Style) -->
            @php
                $courierName = \App\Models\Setting::getValue('courier_name', 'Pak Budi (Mamitha Courier)');
                $courierPhone = \App\Models\Setting::getValue('courier_phone') ?: $storeWhatsapp;
            @endphp
            <div class="p-6 border-b border-gray-100 dark:border-gray-700/50 flex items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <!-- Avatar image or placeholder -->
                    <div class="w-14 h-14 bg-gradient-to-tr from-amber-100 to-amber-200 dark:from-amber-950 dark:to-amber-900 rounded-2xl flex items-center justify-center text-3xl shadow-inner select-none">
                        🛵
                    </div>
                    <div>
                        <h4 class="font-extrabold text-gray-800 dark:text-gray-100 text-sm">{{ $courierName }}</h4>
                        <div class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            <span class="text-amber-500">★ 4.9</span>
                            <span>•</span>
                            <span>Kurir Toko Roti</span>
                        </div>
                    </div>
                </div>
                
                <!-- Chat/Call Shortcuts -->
                <div class="flex items-center gap-2">
                    <!-- WhatsApp Chat button -->
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $courierPhone) }}?text=Halo%20{{ urlencode($courierName) }},%20saya%20ingin%20tanya%20status%20order%20{{ $order->order_number }}" target="_blank" class="w-10 h-10 bg-green-500 hover:bg-green-600 text-white rounded-full flex items-center justify-center shadow-md transition transform active:scale-95">
                        💬
                    </a>
                    <!-- Call button -->
                    <a href="tel:{{ preg_replace('/[^0-9]/', '', $courierPhone) }}" class="w-10 h-10 bg-gray-900 dark:bg-gray-100 hover:bg-amber-700 dark:hover:bg-amber-500 text-white dark:text-gray-900 rounded-full flex items-center justify-center shadow-md transition transform active:scale-95">
                        📞
                    </a>
                </div>
            </div>

            <!-- Vertical Timeline Tracker (Tiktok status style) -->
            <div class="p-6">
                <h3 class="font-extrabold text-gray-700 dark:text-gray-300 text-xs tracking-wider uppercase mb-6">Status Perjalanan</h3>
                <div class="relative pl-8 space-y-8 before:absolute before:inset-y-1 before:left-3 before:w-0.5 before:bg-gray-200 dark:before:bg-gray-700">
                    
                    <!-- Step 1: Order confirmed -->
                    <div class="relative">
                        <!-- Bullet status indicator -->
                        <span class="absolute -left-8 top-1.5 w-6.5 h-6.5 rounded-full flex items-center justify-center text-xs font-black
                            {{ $order->status !== 'cancelled' ? 'bg-green-500 text-white shadow-md' : 'bg-red-500 text-white shadow-md' }}">
                            ✔
                        </span>
                        <div>
                            <p class="font-bold text-sm text-gray-800 dark:text-gray-100">Pesanan Diterima</p>
                            <p class="text-xs text-gray-400 mt-0.5">Terima kasih, pesanan Anda telah tersimpan.</p>
                        </div>
                    </div>

                    <!-- Step 2: Confirmed/Verified by admin -->
                    @php
                        $isConfirmed = in_array($order->status, ['confirmed', 'producing', 'ready', 'done']);
                    @endphp
                    <div class="relative">
                        <span class="absolute -left-8 top-1.5 w-6.5 h-6.5 rounded-full flex items-center justify-center text-xs font-black
                            {{ $isConfirmed ? 'bg-green-500 text-white shadow-md' : 'bg-gray-200 dark:bg-gray-700 text-gray-400 dark:text-gray-500' }}">
                            {{ $isConfirmed ? '✔' : '2' }}
                        </span>
                        <div>
                            <p class="font-bold text-sm {{ $isConfirmed ? 'text-gray-800 dark:text-gray-100' : 'text-gray-400 dark:text-gray-500' }}">
                                Dikonfirmasi Admin
                            </p>
                            <p class="text-xs text-gray-400 mt-0.5">Admin sedang memverifikasi pesanan Anda.</p>
                        </div>
                    </div>

                    <!-- Step 3: Producing / Baking -->
                    @php
                        $isProducing = in_array($order->status, ['producing', 'ready', 'done']);
                    @endphp
                    <div class="relative">
                        <span class="absolute -left-8 top-1.5 w-6.5 h-6.5 rounded-full flex items-center justify-center text-xs font-black
                            {{ $isProducing ? 'bg-green-500 text-white shadow-md' : 'bg-gray-200 dark:bg-gray-700 text-gray-400 dark:text-gray-500' }}">
                            {{ $isProducing ? '✔' : '3' }}
                        </span>
                        <div>
                            <p class="font-bold text-sm {{ $isProducing ? 'text-gray-800 dark:text-gray-100' : 'text-gray-400 dark:text-gray-500' }}">
                                Sedang Diproses (Dapur)
                            </p>
                            <p class="text-xs text-gray-400 mt-0.5">Roti kesukaan Anda sedang dipanggang hangat.</p>
                        </div>
                    </div>

                    <!-- Step 4: Ready / Delivery -->
                    @php
                        $isReady = in_array($order->status, ['ready', 'done']);
                    @endphp
                    <div class="relative">
                        <span class="absolute -left-8 top-1.5 w-6.5 h-6.5 rounded-full flex items-center justify-center text-xs font-black
                            {{ $isReady ? 'bg-green-500 text-white shadow-md' : 'bg-gray-200 dark:bg-gray-700 text-gray-400 dark:text-gray-500' }}">
                            {{ $isReady ? '✔' : '4' }}
                        </span>
                        <div>
                            <p class="font-bold text-sm {{ $isReady ? 'text-gray-800 dark:text-gray-100' : 'text-gray-400 dark:text-gray-500' }}">
                                {{ $order->type === 'delivery' ? 'Dalam Perjalanan Kurir' : 'Siap Diambil' }}
                            </p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                {{ $order->type === 'delivery' ? 'Kurir sedang mengantar pesanan ke alamat Anda.' : 'Roti siap diambil di outlet Mamitha.' }}
                            </p>
                        </div>
                    </div>

                    <!-- Step 5: Completed -->
                    @php
                        $isDone = $order->status === 'done';
                    @endphp
                    <div class="relative">
                        <span class="absolute -left-8 top-1.5 w-6.5 h-6.5 rounded-full flex items-center justify-center text-xs font-black
                            {{ $isDone ? 'bg-green-500 text-white shadow-md' : 'bg-gray-200 dark:bg-gray-700 text-gray-400 dark:text-gray-500' }}">
                            {{ $isDone ? '✔' : '5' }}
                        </span>
                        <div>
                            <p class="font-bold text-sm {{ $isDone ? 'text-gray-800 dark:text-gray-100' : 'text-gray-400 dark:text-gray-500' }}">
                                Pesanan Selesai
                            </p>
                            <p class="text-xs text-gray-400 mt-0.5">Selesai! Terima kasih telah berbelanja di Mamitha Bakery.</p>
                        </div>
                    </div>

                </div>
            </div>

        </div>

        <!-- Order Detail Summary Card -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl border border-amber-100/50 dark:border-gray-700/50 p-6 shadow-md space-y-4">
            <h3 class="font-bold text-gray-800 dark:text-gray-100 font-serif text-lg">Rincian Roti</h3>
            <div class="divide-y divide-gray-100 dark:divide-gray-700/50">
                @foreach($order->items as $item)
                <div class="py-3 flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400 font-medium">{{ $item->product->name }} x {{ $item->quantity }}</span>
                    <span class="font-bold text-gray-800 dark:text-gray-200">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                </div>
                @endforeach
            </div>
            
            <div class="border-t border-gray-100 dark:border-gray-700/50 pt-4 flex justify-between items-center">
                <div>
                    <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider">Metode Pengiriman</p>
                    <p class="text-xs font-bold text-gray-800 dark:text-gray-200 mt-0.5">{{ $order->type == 'pickup' ? '🏪 Ambil di Toko' : '🚚 Diantar Kurir' }}</p>
                </div>
                <div class="text-right">
                    <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider">Total Dibayar</p>
                    <p class="text-xl font-black text-amber-800 dark:text-amber-400 mt-0.5">Rp {{ number_format($order->total, 0, ',', '.') }}</p>
                </div>
            </div>

            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700/50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 text-xs">
                <div>
                    <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider">Alamat Toko</p>
                    <p class="text-gray-600 dark:text-gray-400 font-medium mt-0.5">{{ \App\Models\Setting::getValue('store_address') }}</p>
                </div>
                @php
                    $gmapsLink = \App\Models\Setting::getValue('store_gmaps_link') ?: "https://www.google.com/maps?q={$storeLat},{$storeLng}";
                @endphp
                <a href="{{ $gmapsLink }}" target="_blank" class="shrink-0 px-3 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 dark:bg-amber-950/20 dark:hover:bg-amber-900/35 dark:text-amber-400 font-extrabold rounded-lg transition flex items-center gap-1">
                    🗺️ Google Maps Toko &rarr;
                </a>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex flex-col sm:flex-row gap-3">
            @if(session('whatsapp_url') || $whatsappUrl)
            <a href="{{ session('whatsapp_url') ?? $whatsappUrl }}" target="_blank" class="flex-1 py-4 bg-green-600 hover:bg-green-700 text-white font-extrabold text-center rounded-2xl shadow-lg transition transform active:scale-95 flex items-center justify-center gap-2">
                <span>💬</span> Konfirmasi via WhatsApp
            </a>
            @endif
            <a href="{{ route('home') }}" class="flex-1 py-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:bg-amber-50 dark:hover:bg-gray-750 text-gray-700 dark:text-gray-300 font-extrabold text-center rounded-2xl shadow-sm transition transform active:scale-95 flex items-center justify-center">
                Kembali ke Beranda
            </a>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<!-- Leaflet.js Library -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    const customerLocation = [{{ $order->latitude ?? $storeLat }}, {{ $order->longitude ?? $storeLng }}];
    const hasRoute = {{ $hasRoute ? 'true' : 'false' }};
    
    let map = null;
    let customerMarker = null;
    let bakeryMarker = null;
    
    // Initialize Map only if tracking map exists (hasRoute is true)
    if (hasRoute && document.getElementById('tracking-map')) {
        map = L.map('tracking-map', {
            zoomControl: false,
            attributionControl: false
        }).setView(customerLocation, 16);

        // Add tiles layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19
        }).addTo(map);

        // Customer Icon Custom
        customerMarker = L.marker(customerLocation).addTo(map)
            .bindPopup('📍 Lokasi Pengiriman').openPopup();
            
        fitMapBounds();
    }

    // Function to calculate map boundaries
    function fitMapBounds(userLat = null, userLng = null) {
        if (!map) return;
        
        const markers = [];
        if (customerMarker) markers.push(customerMarker);
        
        // Include live user location in bounds if present
        let tempUserMarker = null;
        if (userLat !== null && userLng !== null) {
            tempUserMarker = L.marker([userLat, userLng]);
            markers.push(tempUserMarker);
        }

        if (markers.length > 0) {
            const group = new L.featureGroup(markers);
            map.fitBounds(group.getBounds().pad(0.3));
        }
    }

    // =========================================================================
    // Live GPS Location Tracking
    // =========================================================================
    let watchId = null;
    let myLocationMarker = null;
    let myLocationCircle = null;

    function trackMyLocation() {
        const btn = document.getElementById('track-gps-btn');
        const icon = document.getElementById('track-gps-icon');

        if (!navigator.geolocation) {
            alert('Browser Anda tidak mendukung GPS.');
            return;
        }

        if (watchId !== null) {
            // Stop tracking
            navigator.geolocation.clearWatch(watchId);
            watchId = null;
            if (myLocationMarker) map.removeLayer(myLocationMarker);
            if (myLocationCircle) map.removeLayer(myLocationCircle);
            myLocationMarker = null;
            myLocationCircle = null;
            icon.classList.remove('animate-pulse');
            fitMapBounds();
            alert('Pelacakan GPS dinonaktifkan.');
            return;
        }

        icon.classList.add('animate-pulse');

        watchId = navigator.geolocation.watchPosition(
            function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                const accuracy = position.coords.accuracy;

                // Update/Create Blue Dot Marker
                if (myLocationMarker) {
                    myLocationMarker.setLatLng([lat, lng]);
                } else {
                    const blueDotIcon = L.divIcon({
                        className: 'relative flex items-center justify-center w-6 h-6',
                        html: `
                            <span class="absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75 animate-ping"></span>
                            <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-blue-600 border-2 border-white shadow-md"></span>
                        `
                    });
                    myLocationMarker = L.marker([lat, lng], { icon: blueDotIcon }).addTo(map)
                        .bindPopup('<b>Lokasi Anda saat ini</b>');
                }

                // Update/Create Accuracy Circle
                if (myLocationCircle) {
                    myLocationCircle.setLatLng([lat, lng]).setRadius(accuracy);
                } else {
                    myLocationCircle = L.circle([lat, lng], {
                        radius: accuracy,
                        color: '#3b82f6',
                        fillColor: '#3b82f6',
                        fillOpacity: 0.1,
                        weight: 1
                    }).addTo(map);
                }

                // Adjust map bounds to include Bakery, Destination, and Current GPS Location
                fitMapBounds(lat, lng);
            },
            function(error) {
                console.error('GPS tracking error:', error);
                alert('Gagal mengambil lokasi GPS Anda. Silakan periksa izin lokasi browser.');
                icon.classList.remove('animate-pulse');
                if (watchId !== null) {
                    navigator.geolocation.clearWatch(watchId);
                    watchId = null;
                }
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    }

    // =========================================================================
    // Level Up Modal Particle & Animation Control
    // =========================================================================
    function runParticleBurst() {
        const canvas = document.getElementById('star-canvas');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        
        // Resize canvas to match its bounding container
        const rect = canvas.getBoundingClientRect();
        canvas.width = rect.width;
        canvas.height = rect.height;
        
        const centerX = canvas.width / 2;
        const centerY = canvas.height / 2 - 40; // offset up slightly to match center of badge
        
        const particles = [];
        const colors = ['#f59e0b', '#fbbf24', '#fef08a', '#ffffff', '#ea580c'];
        
        // Star drawing helper
        function drawStar(ctx, cx, cy, spikes, outerRadius, innerRadius, color, alpha) {
            let rot = Math.PI / 2 * 3;
            let x = cx;
            let y = cy;
            let step = Math.PI / spikes;

            ctx.save();
            ctx.globalAlpha = alpha;
            ctx.beginPath();
            ctx.moveTo(cx, cy - outerRadius)
            for (let i = 0; i < spikes; i++) {
                x = cx + Math.cos(rot) * outerRadius;
                y = cy + Math.sin(rot) * outerRadius;
                ctx.lineTo(x, y)
                rot += step

                x = cx + Math.cos(rot) * innerRadius;
                y = cy + Math.sin(rot) * innerRadius;
                ctx.lineTo(x, y)
                rot += step
            }
            ctx.lineTo(cx, cy - outerRadius)
            ctx.closePath();
            ctx.fillStyle = color;
            ctx.fill();
            ctx.restore();
        }

        // Initialize particles (stars and circles)
        for (let i = 0; i < 45; i++) {
            const angle = Math.random() * Math.PI * 2;
            const speed = 2.5 + Math.random() * 5.5;
            particles.push({
                x: centerX,
                y: centerY,
                vx: Math.cos(angle) * speed,
                vy: Math.sin(angle) * speed - (1 + Math.random() * 2), // bias upwards
                radius: 4 + Math.random() * 6,
                color: colors[Math.floor(Math.random() * colors.length)],
                alpha: 1,
                decay: 0.012 + Math.random() * 0.018,
                spikes: Math.random() > 0.4 ? 5 : 4,
                isStar: Math.random() > 0.3
            });
        }

        function animate() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            let activeParticles = 0;

            particles.forEach(p => {
                if (p.alpha <= 0) return;
                activeParticles++;
                
                // Update position
                p.x += p.vx;
                p.y += p.vy;
                
                // Gravity and drag
                p.vy += 0.04; // gravity
                p.vx *= 0.97; // drag
                p.vy *= 0.97;
                
                // Decay transparency
                p.alpha -= p.decay;
                if (p.alpha < 0) p.alpha = 0;
                
                if (p.isStar) {
                    drawStar(ctx, p.x, p.y, p.spikes, p.radius, p.radius / 2, p.color, p.alpha);
                } else {
                    ctx.save();
                    ctx.globalAlpha = p.alpha;
                    ctx.beginPath();
                    ctx.arc(p.x, p.y, p.radius / 2, 0, Math.PI * 2);
                    ctx.fillStyle = p.color;
                    ctx.fill();
                    ctx.restore();
                }
            });

            if (activeParticles > 0) {
                requestAnimationFrame(animate);
            }
        }

        animate();
    }

    window.addEventListener('load', () => {
        // Show Level Up modal
        const modal = document.getElementById('success-levelup-modal');
        const sheet = document.getElementById('success-card-sheet');
        if (modal && sheet) {
            setTimeout(() => {
                modal.classList.remove('opacity-0', 'pointer-events-none');
                modal.classList.add('opacity-100');
                sheet.classList.remove('scale-75', 'opacity-0');
                sheet.classList.add('scale-100', 'opacity-100');
                
                // Trigger particle burst
                runParticleBurst();
            }, 300);
        }
    });

    function dismissLevelUpModal() {
        const modal = document.getElementById('success-levelup-modal');
        const sheet = document.getElementById('success-card-sheet');
        if (modal && sheet) {
            sheet.style.transition = 'transform 0.4s cubic-bezier(0.25, 1, 0.5, 1), opacity 0.3s ease';
            sheet.classList.remove('scale-100', 'opacity-100');
            sheet.classList.add('translate-y-12', 'opacity-0');
            
            modal.style.transition = 'opacity 0.4s ease';
            modal.classList.remove('opacity-100');
            modal.classList.add('opacity-0', 'pointer-events-none');
            
            // Allow interactions underneath once animation finishes
            setTimeout(() => {
                modal.remove();
            }, 450);
        }
    }
</script>
@endpush

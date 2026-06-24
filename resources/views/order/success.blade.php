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

    /* Domino's style Pizza Tracker styling */
    .tracker-container {
        display: flex;
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
        position: relative;
        width: 100%;
        margin-top: 2rem;
        margin-bottom: 2rem;
    }
    @media (max-width: 768px) {
        .tracker-container {
            flex-direction: column;
            align-items: flex-start;
            gap: 2rem;
        }
    }
    
    .tracker-step {
        display: flex;
        flex-direction: column;
        align-items: center;
        z-index: 10;
        flex: 1;
        position: relative;
    }
    @media (max-width: 768px) {
        .tracker-step {
            flex-direction: row;
            align-items: center;
            width: 100%;
            gap: 1rem;
        }
    }

    .tracker-icon-outer {
        width: 56px;
        height: 56px;
        border-radius: 9999px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f3f4f6;
        border: 4px solid #e5e7eb;
        color: #9ca3af;
        transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
        position: relative;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }
    .dark .tracker-icon-outer {
        background: #1f2937;
        border-color: #374151;
        color: #6b7280;
    }

    /* Completed state */
    .tracker-step.completed .tracker-icon-outer {
        background: #10b981;
        border-color: #a7f3d0;
        color: #ffffff;
        box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3);
    }
    .dark .tracker-step.completed .tracker-icon-outer {
        border-color: #065f46;
        background: #059669;
    }

    /* Active state */
    .tracker-step.active .tracker-icon-outer {
        background: #ff6310;
        border-color: #ffedd5;
        color: #ffffff;
        transform: scale(1.12);
        box-shadow: 0 0 20px rgba(255, 99, 16, 0.45);
    }
    .dark .tracker-step.active .tracker-icon-outer {
        border-color: #7c2d12;
    }

    /* Progress bar connecting lines (Horizontal) */
    .tracker-connector {
        position: absolute;
        top: 28px;
        left: 10%;
        right: 10%;
        height: 4px;
        background: #e5e7eb;
        z-index: 1;
        transition: all 0.5s ease;
    }
    .dark .tracker-connector {
        background: #374151;
    }
    .tracker-connector-fill {
        height: 100%;
        background: linear-gradient(90deg, #10b981, #ff6310);
        width: 0%;
        transition: width 0.8s ease-in-out;
    }
    @media (max-width: 768px) {
        .tracker-connector {
            top: 28px;
            left: 28px;
            bottom: 28px;
            right: auto;
            width: 4px;
            height: auto;
        }
        .tracker-connector-fill {
            width: 100%;
            height: 0%;
            transition: height 0.8s ease-in-out;
        }
    }

    /* Custom animated SVG styles */
    .animated-mixing-bowl .spoon {
        transform-origin: 12px 10px;
        animation: mix 1.5s infinite linear;
    }
    @keyframes mix {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .animated-oven .fire {
        animation: burn 1s infinite alternate;
        transform-origin: 12px 20px;
    }
    @keyframes burn {
        0% { transform: scaleY(0.9) opacity(0.8); }
        100% { transform: scaleY(1.1) opacity(1); }
    }

    .animated-box .lid {
        transform-origin: 12px 6px;
        animation: lid-pulse 2s infinite ease-in-out;
    }
    @keyframes lid-pulse {
        0%, 100% { transform: rotate(0deg); }
        50% { transform: rotate(-10deg); }
    }

    .animated-scooter .wheel {
        transform-origin: center;
        animation: spin 1s infinite linear;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* Voice / tracking locked widget styling */
    .lock-widget {
        background: linear-gradient(135deg, rgba(255, 99, 16, 0.08), rgba(220, 38, 38, 0.08));
        border: 1.5px dashed rgba(255, 99, 16, 0.25);
        border-radius: 24px;
        padding: 1.25rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
    }
    .dark .lock-widget {
        background: linear-gradient(135deg, rgba(255, 99, 16, 0.04), rgba(220, 38, 38, 0.04));
        border-color: rgba(255, 99, 16, 0.15);
    }
    @keyframes pulse-slow {
        0%, 100% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.15); opacity: 0.85; }
    }
    #countdown-timer-icon {
        display: inline-block;
        animation: pulse-slow 2s infinite ease-in-out;
    }
</style>
@endpush

@section('content')
@php
    $storeLat = \App\Models\Setting::getValue('store_latitude', '-7.7609582');
    $storeLng = \App\Models\Setting::getValue('store_longitude', '110.2529556');
    $isPendingPayment = $order->payment_status === 'unpaid' && !in_array($order->payment_method, ['Cash On Delivery / COD', 'WhatsApp Confirmation']);
    $hasRoute = !$isPendingPayment && $order->type === 'delivery' && $order->latitude && $order->longitude;

    $maxReadyTime = '15-20 min';
    if ($order->items->isNotEmpty()) {
        $maxMinutes = 0;
        $maxTimeStr = '15-20 min';
        foreach ($order->items as $item) {
            $timeStr = $item->product->ready_time ?? '15-20 min';
            preg_match_all('/\d+/', $timeStr, $matches);
            if (!empty($matches[0])) {
                $mins = max(array_map('intval', $matches[0]));
                if (stripos($timeStr, 'jam') !== false || stripos($timeStr, 'hour') !== false || stripos($timeStr, 'hr') !== false) {
                    $mins *= 60;
                }
                if ($mins > $maxMinutes) {
                    $maxMinutes = $mins;
                    $maxTimeStr = $timeStr;
                }
            }
        }
        $maxReadyTime = $maxTimeStr;
    }
@endphp

@php
    $showModal = false;
    $modalType = 'none'; // 'new_member', 'levelup', 'points', 'guest'
    
    if (session()->has('newly_joined_member')) {
        $showModal = true;
        $modalType = 'new_member';
        $rankName = $order->customer->rank_name;
        $rankBadge = $order->customer->rank_badge;
    } elseif (session()->has('level_up')) {
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
<!-- Level Up / Rank Unlocked Modal Overlay (Premium TikTok/Flutter Style) -->
<div id="success-levelup-modal" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 backdrop-blur-sm transition-opacity duration-500 ease-out opacity-0 pointer-events-none">
    <div id="success-card-sheet" class="bg-white dark:bg-gray-900 rounded-[32px] p-6 max-w-sm w-full mx-4 shadow-2xl border border-gray-100 dark:border-gray-800/80 text-center relative overflow-hidden transform scale-90 opacity-0 transition-all duration-500 ease-out">
        <!-- Close Button (Absolute) -->
        <button type="button" onclick="dismissLevelUpModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-250 transition duration-150 p-1.5 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 z-50">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <!-- Canvas for particle star burst -->
        <canvas id="star-canvas" class="absolute inset-0 pointer-events-none z-0"></canvas>
        
        <!-- Rotating light rays backdrop -->
        <div class="absolute -top-12 -left-12 w-64 h-64 bg-amber-500/10 rounded-full blur-3xl pointer-events-none z-0"></div>
        <div class="absolute -bottom-12 -right-12 w-64 h-64 bg-orange-500/10 rounded-full blur-3xl pointer-events-none z-0"></div>

        <div class="relative z-10">
            <!-- Drag Handle Indicator -->
            <div class="w-12 h-1 bg-gray-200 dark:bg-gray-700 rounded-full mx-auto mb-6"></div>

            @if($modalType === 'levelup' || $modalType === 'points' || $modalType === 'new_member')
                @php
                    $rankName = ($modalType === 'levelup') ? $levelUpData['new'] : $order->customer->rank_name;
                    $rankBadge = ($modalType === 'levelup') ? $levelUpData['badge'] : $order->customer->rank_badge;
                    $customerPoints = $order->customer->points;

                    // Determine percentile, rewards, and theme gradient based on rank name
                    if ($rankName === 'Platinum') {
                        $percentile = '2%';
                        $rewardTitle = 'Priority kitchen + free delivery + monthly voucher';
                        $rewardDesc = 'Your orders jump the queue, free delivery on all items, plus monthly treats.';
                        $themeGradient = 'from-cyan-400 via-teal-500 to-blue-600';
                        $glowColor = 'bg-cyan-500/20';
                    } elseif ($rankName === 'Gold') {
                        $percentile = '8%';
                        $rewardTitle = 'Priority kitchen + free delivery';
                        $rewardDesc = 'Your orders jump the queue, every day, no fee.';
                        $themeGradient = 'from-[#FFF59D] via-[#FBC02D] to-[#F57F17]';
                        $glowColor = 'bg-amber-500/20';
                    } elseif ($rankName === 'Silver') {
                        $percentile = '25%';
                        $rewardTitle = '10% bonus points + free shipping';
                        $rewardDesc = 'Earn rewards faster and zero delivery fees on all warm breads.';
                        $themeGradient = 'from-slate-350 via-slate-500 to-slate-650';
                        $glowColor = 'bg-slate-500/15';
                    } else { // Bronze
                        $percentile = '60%';
                        $rewardTitle = '10% off member discount';
                        $rewardDesc = 'Get an automatic 10% discount on all your warm bakes.';
                        $themeGradient = 'from-[#A1887F] via-[#BCAAA4] to-[#5D4037]';
                        $glowColor = 'bg-[#5D4037]/15';
                    }
                @endphp

                <!-- Rank Badge Icon with glowing shadow -->
                <div class="relative w-32 h-32 mx-auto flex items-center justify-center mb-5">
                    <!-- Glow halo -->
                    <div class="absolute inset-0 {{ $glowColor }} rounded-full blur-xl animate-pulse"></div>
                    <!-- Outer ring -->
                    <div class="w-24 h-24 rounded-full bg-gradient-to-b {{ $themeGradient }} p-0.5 shadow-lg flex items-center justify-center">
                        <!-- Inner circle with 3D gradient -->
                        <div class="w-full h-full rounded-full bg-gradient-to-b {{ $themeGradient }} flex items-center justify-center relative shadow-[inset_0_3px_6px_rgba(255,255,255,0.45),0_6px_12px_rgba(0,0,0,0.2)] border-2 border-white/20">
                            <!-- Ribbon medal silhouette -->
                            <svg class="w-11 h-11 text-white filter drop-shadow-[0_2px_3px_rgba(0,0,0,0.25)]" fill="currentColor" viewBox="0 0 24 24">
                                <circle cx="12" cy="8" r="5" />
                                <path d="M8 12.5v7l4-2 4 2v-7" />
                                <path d="M12 5.5l.6 1.4 1.5.2-1.1 1 .3 1.5-1.3-.7-1.3.7.3-1.5-1.1-1 1.5-.2z" fill="#FFF" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Text details -->
                <div class="space-y-1 mb-6">
                    <span class="text-[10px] font-black text-amber-600 dark:text-amber-500 uppercase tracking-[0.25em] block animate-pulse">
                        @if($modalType === 'new_member')
                            Selamat Bergabung!
                        @elseif($modalType === 'levelup')
                            Rank Unlocked
                        @else
                            Member Status
                        @endif
                    </span>
                    <h2 class="text-2xl font-black text-gray-955 dark:text-white leading-tight">
                        @if($modalType === 'new_member')
                            Welcome Member!
                        @else
                            You're {{ $rankName }}
                        @endif
                    </h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">
                        @if($modalType === 'new_member')
                            Sekarang Anda adalah Member Resmi Mamitha
                        @elseif($modalType === 'levelup')
                            {{ $customerPoints }} points - top {{ $percentile }} of members
                        @else
                            +{{ $pointsEarned }} points &bull; {{ $customerPoints }} points - top {{ $percentile }}
                        @endif
                    </p>
                </div>

                <!-- Reward Unlocked Card -->
                <div class="bg-[#FCF5F0] dark:bg-gray-800/40 rounded-2xl p-4 border border-amber-100/30 dark:border-gray-700/30 text-left flex items-center gap-4 mb-6">
                    <div class="w-12 h-12 bg-white dark:bg-gray-800 rounded-xl flex items-center justify-center border border-gray-100 dark:border-gray-700/80 shadow-sm shrink-0">
                        <!-- 3D Box SVG -->
                        <svg class="w-6 h-6 text-amber-700 dark:text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-14L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <div>
                        <span class="text-[9px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Benefit Member</span>
                        <h4 class="text-xs font-extrabold text-gray-900 dark:text-white leading-tight mt-0.5">
                            {{ $rewardTitle }}
                        </h4>
                        <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-0.5 leading-snug">
                            {{ $rewardDesc }}
                        </p>
                    </div>
                </div>

                <!-- View my rewards Action Button -->
                <a href="{{ route('member.profile') }}" class="block w-full py-4 bg-[#D84315] hover:bg-[#C62828] text-white font-extrabold text-sm rounded-2xl shadow-xl transition-all duration-300 transform active:scale-95 text-center">
                    Lihat Profil Member
                </a>

            @else
                <!-- Guest Welcome Modal -->
                <div class="relative w-32 h-32 mx-auto flex items-center justify-center mb-5">
                    <div class="absolute inset-0 bg-amber-500/20 rounded-full blur-xl animate-pulse"></div>
                    <div class="w-24 h-24 rounded-full bg-gradient-to-b from-amber-400 via-yellow-500 to-orange-600 p-0.5 shadow-lg flex items-center justify-center">
                        <div class="w-full h-full rounded-full bg-gradient-to-b from-amber-400 via-orange-500 to-red-650 flex items-center justify-center relative shadow-[inset_0_3px_6px_rgba(255,255,255,0.45),0_6px_12px_rgba(0,0,0,0.2)] border-2 border-white/20">
                            <span class="text-4xl select-none filter drop-shadow">🥐</span>
                        </div>
                    </div>
                </div>
                <!-- Text details -->
                <div class="space-y-1 mb-6">
                    @if($order->status === 'pending' && $order->payment_status === 'unpaid' && (stripos($order->payment_method, 'transfer') !== false || stripos($order->payment_method, 'midtrans') !== false))
                        <span class="text-[10px] font-black text-red-650 dark:text-red-500 uppercase tracking-[0.25em] block animate-pulse">
                            ⏳ Order Pending (Unpaid)
                        </span>
                        <h2 class="text-2xl font-black text-gray-950 dark:text-white leading-tight">
                            Menunggu Pembayaran
                        </h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">
                            Selesaikan pembayaran agar pesanan dapat dikirim ke dapur.
                        </p>
                    @else
                        <span class="text-[10px] font-black text-amber-600 dark:text-amber-500 uppercase tracking-[0.25em] block animate-pulse">
                            Order Confirmed
                        </span>
                        <h2 class="text-2xl font-black text-gray-950 dark:text-white leading-tight">
                            Roti Siap Dipanggang!
                        </h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">
                            Fresh from the oven - delivered warm
                        </p>
                    @endif
                </div>

                <!-- Member Promotion Invitation Card -->
                <div class="bg-[#FCF5F0] dark:bg-gray-800/40 rounded-2xl p-4 border border-amber-100/30 dark:border-gray-700/30 text-left flex items-center gap-4 mb-6">
                    <div class="w-12 h-12 bg-white dark:bg-gray-800 rounded-xl flex items-center justify-center border border-gray-100 dark:border-gray-700/80 shadow-sm shrink-0">
                        <span class="text-2xl select-none">✨</span>
                    </div>
                    <div>
                        <span class="text-[9px] font-black text-amber-700 dark:text-amber-500 uppercase tracking-wider block">Gabung Member</span>
                        <h4 class="text-xs font-extrabold text-gray-900 dark:text-white leading-tight mt-0.5">
                            @if(session('wants_to_join_member'))
                                Selesaikan Pendaftaran
                            @else
                                Diskon 10% Otomatis
                            @endif
                        </h4>
                        <p class="text-[10px] text-gray-550 dark:text-gray-400 mt-0.5 leading-snug">
                            @if(session('wants_to_join_member'))
                                Akun Anda belum terdaftar. Daftarkan sekarang agar poin & level dari pesanan ini langsung masuk ke profil Anda!
                            @else
                                Kumpulkan poin belanja Anda untuk naik rank dan nikmati free delivery!
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Action Button for Guest -->
                <a href="{{ route('register') }}" class="block w-full py-4 bg-[#D84315] hover:bg-[#C62828] text-white font-extrabold text-sm rounded-2xl shadow-xl transition-all duration-300 transform active:scale-95 text-center mb-3">
                    Gabung Member Sekarang
                </a>
                <button type="button" onclick="dismissLevelUpModal()" class="text-xs font-bold text-gray-500 hover:text-gray-800 dark:hover:text-gray-300 transition duration-150">
                    Lacak Pesanan Saya &rarr;
                </button>
            @endif
        </div>
    </div>
</div>
@endif

<div class="min-h-screen bg-cream-50 dark:bg-gray-900 py-12 px-4 transition-colors duration-200">
    <div class="max-w-2xl mx-auto space-y-6">
        
        <!-- Main Success Tracker Card -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl border border-amber-100/50 dark:border-gray-700/50 shadow-xl overflow-hidden">
            @if(!$isPendingPayment)
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
                        @if($order->payment_status === 'unpaid' && (stripos($order->payment_method, 'transfer') !== false || stripos($order->payment_method, 'midtrans') !== false))
                            ⏳ Menunggu Pembayaran
                        @else
                            ⏳ Menunggu Konfirmasi
                        @endif
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
                        @if($order->payment_status === 'unpaid' && (stripos($order->payment_method, 'transfer') !== false || stripos($order->payment_method, 'midtrans') !== false))
                            ⏳ Menunggu Pembayaran
                        @else
                            ⏳ Menunggu Konfirmasi
                        @endif
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
                    <h2 class="text-2xl font-black text-amber-800 dark:text-amber-400 font-serif" id="arrival-estimate-text">
                        @if($order->status == 'done')
                            Pesanan Telah Tiba
                        @elseif($order->type == 'pickup')
                            Siap Diambil dalam {{ $maxReadyTime }}
                        @else
                            Tiba dalam {{ $maxReadyTime }}
                        @endif
                    </h2>

                    <!-- Countdown Timer Badge -->
                    <div id="countdown-timer-container" class="hidden mt-2 inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-[11px] font-extrabold tracking-wide uppercase transition-all duration-300">
                        <span id="countdown-timer-icon" class="text-sm"></span>
                        <span id="countdown-timer-label"></span>
                        <span id="countdown-timer-clock" class="font-mono text-xs bg-black/5 dark:bg-white/10 px-2 py-0.5 rounded-md"></span>
                    </div>
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

            <!-- Domino's Pizza Tracker layout -->
            <div class="p-6">
                <h3 class="font-extrabold text-gray-700 dark:text-gray-300 text-xs tracking-wider uppercase mb-6 flex justify-between items-center">
                    <span>Progres Pesanan (Tracker)</span>
                    @auth
                    <span class="text-[9px] bg-green-500/10 text-green-600 dark:bg-green-950/20 dark:text-green-400 px-2 py-0.5 rounded-full lowercase font-bold tracking-normal animate-pulse">Live Polling Aktif</span>
                    @else
                    <span class="text-[9px] bg-gray-100 dark:bg-gray-800 text-gray-400 px-2 py-0.5 rounded-full lowercase font-bold tracking-normal">Mode Statis</span>
                    @endauth
                </h3>

                <!-- Stepper Container -->
                <div class="relative min-h-[320px] md:min-h-0 md:py-6">
                    <!-- Progress Bar Connector Line -->
                    <div class="tracker-connector">
                        <div id="tracker-connector-fill" class="tracker-connector-fill" style="width: 0%;"></div>
                    </div>

                    <div class="tracker-container">
                        <!-- Step 1: Placed -->
                        <div id="step-placed" class="tracker-step">
                            <div class="tracker-icon-outer" title="Pesanan Diterima">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div class="text-center md:mt-2.5">
                                <p class="font-bold text-xs text-gray-800 dark:text-gray-200">
                                    @if($order->status === 'pending' && $order->payment_status === 'unpaid' && (stripos($order->payment_method, 'transfer') !== false || stripos($order->payment_method, 'midtrans') !== false))
                                        Menunggu Bayar
                                    @else
                                        Diterima
                                    @endif
                                </p>
                                <p class="text-[9px] text-gray-400 dark:text-gray-500 mt-0.5 hidden md:block">
                                    @if($order->status === 'pending' && $order->payment_status === 'unpaid' && (stripos($order->payment_method, 'transfer') !== false || stripos($order->payment_method, 'midtrans') !== false))
                                        Pending Payment
                                    @else
                                        Order Placed
                                    @endif
                                </p>
                            </div>
                        </div>

                        <!-- Step 2: Confirmed -->
                        <div id="step-confirmed" class="tracker-step">
                            <div class="tracker-icon-outer animated-mixing-bowl" title="Dikonfirmasi Admin">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path class="spoon" stroke-linecap="round" stroke-linejoin="round" d="M12 4v4m0 0l-2-2m2 2l2-2" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 10a7 7 0 01-7 7 7 7 0 01-7-7h14z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 10V6a2 2 0 012-2h10a2 2 0 012 2v4" />
                                </svg>
                            </div>
                            <div class="text-center md:mt-2.5">
                                <p class="font-bold text-xs text-gray-400 dark:text-gray-500">Adonan</p>
                                <p class="text-[9px] text-gray-400 dark:text-gray-500 mt-0.5 hidden md:block">Prep Room</p>
                            </div>
                        </div>

                        <!-- Step 3: Baking -->
                        <div id="step-producing" class="tracker-step">
                            <div class="tracker-icon-outer animated-oven" title="Sedang Dipanggang">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <rect x="3" y="3" width="18" height="18" rx="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <line x1="3" y1="14" x2="21" y2="14" />
                                    <circle cx="8" cy="8" r="1.5" />
                                    <circle cx="16" cy="8" r="1.5" />
                                    <path class="fire" stroke="#ff6310" d="M12 19c1.5 0 2-1 2-2.5s-1-2-2-3.5c-1 1.5-2 2-2 3.5s.5 2.5 2 2.5z"/>
                                </svg>
                            </div>
                            <div class="text-center md:mt-2.5">
                                <p class="font-bold text-xs text-gray-400 dark:text-gray-500">Dipanggang</p>
                                <p class="text-[9px] text-gray-400 dark:text-gray-500 mt-0.5 hidden md:block">Baking Oven</p>
                            </div>
                        </div>

                        <!-- Step 4: Ready -->
                        <div id="step-ready" class="tracker-step">
                            <div class="tracker-icon-outer animated-box" title="Siap Diambil/Diantar">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path class="lid" stroke-linecap="round" stroke-linejoin="round" d="M12 3L2 7l10 4 10-4-10-4zM2 17l10 4 10-4M2 12l10 4 10-4" />
                                </svg>
                            </div>
                            <div class="text-center md:mt-2.5">
                                <p class="font-bold text-xs text-gray-400 dark:text-gray-500">Siap</p>
                                <p class="text-[9px] text-gray-400 dark:text-gray-500 mt-0.5 hidden md:block">Boxed & Check</p>
                            </div>
                        </div>

                        <!-- Step 5: Completed -->
                        <div id="step-done" class="tracker-step">
                            <div class="tracker-icon-outer animated-scooter" title="Pesanan Selesai">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <circle class="wheel" cx="6" cy="18" r="3" />
                                    <circle class="wheel" cx="18" cy="18" r="3" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 18h4M8 15h8l-2-6H10l-2 6zM15 9V6a1 1 0 00-1-1h-3" />
                                </svg>
                            </div>
                            <div class="text-center md:mt-2.5">
                                <p class="font-bold text-xs text-gray-400 dark:text-gray-500">Selesai</p>
                                <p class="text-[9px] text-gray-400 dark:text-gray-500 mt-0.5 hidden md:block">Delivered</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @guest
            <!-- Lock Widget promoting Member sign-in / registration for real-time tracking -->
            <div class="px-6 pb-6">
                <div class="lock-widget">
                    <span class="text-3xl select-none">🔒</span>
                    <div class="flex-1 text-left">
                        <h4 class="text-xs font-black text-gray-800 dark:text-gray-200 uppercase tracking-wider">Lacak Pesanan Real-Time Terkunci</h4>
                        <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-1 leading-normal font-medium">
                            Ingin melacak dengan **Voice Updates**, **Live GPS Map**, dan **Auto-Polling**? Masuk atau daftar akun member Mamitha (gratis) untuk membuka fitur premium ini!
                        </p>
                        <div class="flex gap-2.5 mt-3">
                            <a href="{{ route('login') }}" class="px-4 py-1.5 bg-amber-600 hover:bg-amber-700 text-white font-extrabold text-[9px] rounded-lg transition shadow-sm">Login</a>
                            <a href="{{ route('register') }}" class="px-4 py-1.5 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-extrabold text-[9px] rounded-lg transition">Daftar Member</a>
                        </div>
                    </div>
                </div>
            </div>
            @endguest
            @else
            <!-- Pending Payment Premium Card / Section -->
            <div class="relative overflow-hidden bg-white dark:bg-gray-800 rounded-3xl">
                <!-- Header Sunset Gradient -->
                <div class="relative bg-gradient-to-tr from-amber-500 via-orange-600 to-rose-600 p-8 text-center text-white">
                    <!-- Subtle radial overlay -->
                    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_var(--tw-gradient-stops))] from-white/20 via-transparent to-transparent pointer-events-none"></div>
                    
                    <div class="relative z-10 flex flex-col items-center">
                        <!-- Floating Hourglass in blur glassmorphism circle -->
                        <div class="w-20 h-20 bg-white/10 dark:bg-black/20 backdrop-blur-md rounded-3xl border border-white/25 shadow-2xl flex items-center justify-center mb-4 relative group hover:scale-105 transition-all duration-300">
                            <!-- Glowing Aura -->
                            <div class="absolute inset-0 rounded-3xl bg-amber-400/30 blur-lg animate-pulse"></div>
                            <span class="text-4xl relative z-10 animate-bounce-slow" style="animation-duration: 3s;">⏳</span>
                        </div>
                        
                        <span class="text-[10px] font-black uppercase tracking-[0.25em] text-amber-200 animate-pulse block mb-1">Status Pembayaran</span>
                        <h2 class="text-2xl font-black font-serif tracking-tight text-white">Menunggu Pembayaran</h2>
                        <p class="text-xs text-amber-50 mt-2 max-w-md mx-auto leading-relaxed">
                            Pesanan <span class="font-mono font-bold text-amber-250 bg-black/30 px-2 py-0.5 rounded">{{ $order->order_number }}</span> telah kami terima. Silakan selesaikan pembayaran agar pesanan Anda dapat segera kami proses di dapur.
                        </p>
                    </div>
                </div>

                <!-- Info Preview for Locked Status -->
                <div class="p-6 bg-gray-50/50 dark:bg-gray-900/30 border-b border-gray-100 dark:border-gray-700/50">
                    <h4 class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest text-center mb-4">Fitur Lacak Pesanan (Terkunci)</h4>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-w-lg mx-auto">
                        <!-- Feature 1: Live Map -->
                        <div class="bg-white dark:bg-gray-800 border border-gray-200/50 dark:border-gray-700/55 rounded-2xl p-3.5 flex items-center gap-3 shadow-sm opacity-60">
                            <span class="text-2xl">🗺️</span>
                            <div class="text-left">
                                <p class="text-xs font-bold text-gray-800 dark:text-gray-200">Peta GPS Real-time</p>
                                <p class="text-[10px] text-gray-500 dark:text-gray-400">Lacak lokasi kurir di peta</p>
                            </div>
                        </div>
                        
                        <!-- Feature 2: Stepper -->
                        <div class="bg-white dark:bg-gray-800 border border-gray-200/50 dark:border-gray-700/55 rounded-2xl p-3.5 flex items-center gap-3 shadow-sm opacity-60">
                            <span class="text-2xl">🔥</span>
                            <div class="text-left">
                                <p class="text-xs font-bold text-gray-800 dark:text-gray-200">Progres Pemanggangan</p>
                                <p class="text-[10px] text-gray-550 dark:text-gray-400">Pantau proses dari adonan ke oven</p>
                            </div>
                        </div>

                        <!-- Feature 3: Courier Call -->
                        <div class="bg-white dark:bg-gray-800 border border-gray-200/50 dark:border-gray-700/55 rounded-2xl p-3.5 flex items-center gap-3 shadow-sm opacity-60">
                            <span class="text-2xl">🛵</span>
                            <div class="text-left">
                                <p class="text-xs font-bold text-gray-800 dark:text-gray-200">Informasi Kurir</p>
                                <p class="text-[10px] text-gray-500 dark:text-gray-400">Hubungi langsung via WA / Telpon</p>
                            </div>
                        </div>

                        <!-- Feature 4: Voice Chimes -->
                        <div class="bg-white dark:bg-gray-800 border border-gray-200/50 dark:border-gray-700/55 rounded-2xl p-3.5 flex items-center gap-3 shadow-sm opacity-60">
                            <span class="text-2xl">📢</span>
                            <div class="text-left">
                                <p class="text-xs font-bold text-gray-800 dark:text-gray-200">Notifikasi Suara Live</p>
                                <p class="text-[10px] text-gray-500 dark:text-gray-400">Notifikasi chimes otomatis</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 flex justify-center items-center gap-2 text-[10px] font-black text-amber-700 dark:text-amber-400 bg-amber-500/10 dark:bg-amber-950/20 px-4 py-2 rounded-xl w-fit mx-auto border border-amber-500/20 dark:border-amber-400/10 shadow-sm uppercase tracking-wide">
                        <span>🔒</span> Lacak Pesanan Terbuka Otomatis Setelah Pembayaran Sukses
                    </div>
                </div>

                <!-- Payment Details Panel -->
                @if(stripos($order->payment_method, 'transfer') !== false)
                <!-- Bank Transfer Details (Luxury dark card layout) -->
                <div class="p-6 sm:p-8 space-y-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-amber-50 dark:bg-amber-950/30 rounded-2xl flex items-center justify-center text-xl shrink-0 shadow-inner">
                            🏦
                        </div>
                        <div class="text-left">
                            <h3 class="font-extrabold text-gray-900 dark:text-white text-sm">Transfer Bank Manual</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Lakukan transfer ke rekening bank resmi Mamitha Bakery berikut:</p>
                        </div>
                    </div>

                    <!-- Holographic Bank Card UI -->
                    <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-gray-900 text-white rounded-[32px] p-5 sm:p-6 shadow-2xl relative overflow-hidden border border-white/10 max-w-sm mx-auto">
                        <!-- Glowing aura spots -->
                        <div class="absolute -top-24 -right-24 w-48 h-48 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
                        <div class="absolute -bottom-24 -left-24 w-48 h-48 bg-rose-500/10 rounded-full blur-3xl pointer-events-none"></div>
                        
                        <div class="relative z-10 space-y-5">
                            <!-- Card Header -->
                            <div class="flex justify-between items-start">
                                <div>
                                    <span class="text-[8px] font-black uppercase tracking-widest text-slate-400">Official Merchant Account</span>
                                    <h4 class="text-sm sm:text-base font-black tracking-tight text-white mt-0.5">{{ \App\Models\Setting::getValue('bank_account_holder', 'Mamitha Bakery') }}</h4>
                                </div>
                                <div class="w-10 h-8 bg-gradient-to-br from-yellow-300 to-amber-500 rounded-lg shadow-[inset_0_1px_3px_rgba(255,255,255,0.4)] flex items-center justify-center border border-yellow-300/10">
                                    <span class="text-lg opacity-40">💳</span>
                                </div>
                            </div>

                            <!-- Card Number -->
                            <div class="text-left">
                                <span class="text-[8px] font-black uppercase tracking-wider text-slate-400">Nomor Rekening {{ \App\Models\Setting::getValue('bank_name', 'BCA') }}</span>
                                <div class="flex flex-wrap items-center justify-between mt-1 gap-2">
                                    <span class="font-mono text-lg sm:text-xl font-bold tracking-[0.12em] text-white">{{ \App\Models\Setting::getValue('bank_account_number', '1234 5678 90') }}</span>
                                    <button type="button" onclick="copyText('{{ str_replace(' ', '', \App\Models\Setting::getValue('bank_account_number', '1234567890')) }}', 'No. Rekening {{ \App\Models\Setting::getValue('bank_name', 'BCA') }} berhasil disalin')" class="px-3 py-1 bg-white/10 hover:bg-white/20 active:scale-95 border border-white/10 text-white text-[10px] font-extrabold rounded-xl transition cursor-pointer flex items-center gap-1 shadow-sm shrink-0">
                                        <span>📋</span> Salin
                                    </button>
                                </div>
                            </div>

                            <!-- Card Details -->
                            <div class="border-t border-white/5 pt-4 flex flex-wrap justify-between items-center gap-3 text-left">
                                <div>
                                    <span class="text-[8px] font-black uppercase tracking-wider text-slate-400">Atas Nama</span>
                                    <p class="text-xs font-bold text-slate-200 mt-0.5">{{ \App\Models\Setting::getValue('bank_account_holder', 'Mamitha Bakery') }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="text-[8px] font-black uppercase tracking-wider text-slate-400">Total Transfer</span>
                                    <div class="flex items-center gap-2 justify-end mt-0.5">
                                        <span class="font-mono text-sm sm:text-base font-black text-amber-400">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                                        <button type="button" onclick="copyText('{{ (int) $order->total }}', 'Jumlah transfer berhasil disalin')" class="p-1.5 bg-white/10 hover:bg-white/20 active:scale-95 border border-white/10 text-white rounded-lg transition cursor-pointer shadow-sm">
                                            📋
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Steps Box -->
                    <div class="max-w-lg mx-auto text-xs text-gray-600 dark:text-gray-300 leading-relaxed space-y-2 text-left bg-gray-50 dark:bg-gray-900/40 p-5 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-inner">
                        <p class="flex gap-2">
                            <span class="font-black text-amber-600">1.</span>
                            <span>Lakukan transfer sejumlah <b class="text-gray-800 dark:text-gray-100 font-mono">Rp {{ number_format($order->total, 0, ',', '.') }}</b> ke rekening {{ \App\Models\Setting::getValue('bank_name', 'BCA') }} di atas.</span>
                        </p>
                        <p class="flex gap-2">
                            <span class="font-black text-amber-600">2.</span>
                            <span>Simpan bukti transaksi / foto resi transfer Anda.</span>
                        </p>
                        <p class="flex gap-2">
                            <span class="font-black text-amber-600">3.</span>
                            <span>Kirim bukti transfer ke admin dengan menekan tombol <b>Konfirmasi via WhatsApp</b> di bawah agar order dikirim ke dapur.</span>
                        </p>
                    </div>
                </div>
                @else
                <!-- Online Payment / Midtrans Pending Panel -->
                <div class="p-6 sm:p-8 space-y-5 max-w-lg mx-auto text-left">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-amber-50 dark:bg-amber-950/30 rounded-2xl flex items-center justify-center text-xl shrink-0 shadow-inner">
                            💳
                        </div>
                        <div>
                            <h3 class="font-extrabold text-gray-900 dark:text-white text-sm">Pembayaran Online Instan</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Selesaikan pembayaran online instan Anda:</p>
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-900/40 rounded-2xl p-5 border border-gray-100 dark:border-gray-800 space-y-3">
                        <p class="text-xs text-gray-600 dark:text-gray-300 leading-relaxed font-medium">
                            Selesaikan pembayaran Anda secara instan menggunakan tombol <b>Bayar Sekarang</b> di bawah. Kami mendukung berbagai metode pembayaran online berikut:
                        </p>
                        <div class="flex items-center gap-1.5 flex-wrap pt-1 text-[10px] text-gray-500 dark:text-gray-400 font-extrabold">
                            <span class="px-2 py-0.5 bg-white dark:bg-gray-800 border border-gray-200/50 dark:border-gray-700/80 rounded">QRIS</span>
                            <span class="px-2 py-0.5 bg-white dark:bg-gray-800 border border-gray-200/50 dark:border-gray-700/80 rounded">GoPay</span>
                            <span class="px-2 py-0.5 bg-white dark:bg-gray-800 border border-gray-200/50 dark:border-gray-700/80 rounded">ShopeePay</span>
                            <span class="px-2 py-0.5 bg-white dark:bg-gray-800 border border-gray-200/50 dark:border-gray-700/80 rounded">Virtual Account BCA/BNI/BRI</span>
                            <span class="px-2 py-0.5 bg-white dark:bg-gray-800 border border-gray-200/50 dark:border-gray-700/80 rounded">Kartu Kredit</span>
                        </div>
                        
                        @if($order->snap_token && $order->payment_status === 'unpaid')
                        <div class="pt-3 border-t border-gray-200/30 dark:border-gray-700/30 mt-3">
                            <button type="button" onclick="triggerSnapPayment()" class="w-full py-4 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-extrabold text-center rounded-2xl shadow-xl transition transform active:scale-95 flex items-center justify-center gap-2 animate-pulse-btn cursor-pointer">
                                <span>💳</span> Bayar Sekarang (Online Payment)
                            </button>
                            <p class="text-[10px] text-gray-400 dark:text-gray-500 text-center mt-2 leading-relaxed">
                                *Izin redirect diperlukan. Jika halaman pembayaran tidak terbuka otomatis, silakan klik tombol di atas.
                            </p>
                        </div>
                        @elseif($order->payment_status === 'unpaid')
                        <div class="pt-3 border-t border-red-200/30 dark:border-red-800/30 mt-3 p-3.5 bg-red-50/50 dark:bg-red-950/20 text-red-700 dark:text-red-450 rounded-2xl text-[11px] space-y-1.5 border border-red-100/50 dark:border-red-900/35">
                            <p class="font-extrabold text-xs flex items-center gap-1"><span>⚠️</span> Online Payment Not Ready</p>
                            <p class="leading-relaxed">Gagal menghubungkan ke gerbang pembayaran Midtrans. Silakan lakukan salah satu langkah berikut:</p>
                            <ul class="list-disc pl-4 space-y-1">
                                <li>Pastikan variabel lingkungan (`MIDTRANS_SERVER_KEY`, `MIDTRANS_CLIENT_KEY`, dan `MIDTRANS_IS_PRODUCTION`) sudah diatur di tab <b>Variables</b> proyek Railway Anda.</li>
                                <li>Pastikan nilai key yang diatur sesuai (Sandbox vs Production).</li>
                                <li>Hubungi pemilik toko atau klik <b>Konfirmasi via WhatsApp</b> untuk beralih ke pembayaran manual.</li>
                            </ul>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>
            @endif

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
            
            <div class="border-t border-gray-100 dark:border-gray-700/50 pt-4 flex justify-between items-center gap-4">
                <div>
                    <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider">Metode Pengiriman</p>
                    <p class="text-xs font-bold text-gray-800 dark:text-gray-200 mt-0.5">{{ $order->type == 'pickup' ? '🏪 Ambil di Toko' : '🚚 Diantar Kurir' }}</p>
                </div>
                @if($order->payment_method)
                <div>
                    <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider">Metode Pembayaran</p>
                    <p class="text-xs font-bold text-gray-800 dark:text-gray-200 mt-0.5">{{ $order->payment_method }}</p>
                </div>
                @endif
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
            @if($order->snap_token && $order->payment_status === 'unpaid')
            <button id="pay-button" class="flex-grow-2 flex-1 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-extrabold text-center rounded-2xl shadow-xl transition transform active:scale-95 flex items-center justify-center gap-2 animate-pulse-btn">
                <span>💳</span> Bayar Sekarang (Online Payment)
            </button>
            @endif
            @if(session('whatsapp_url') || $whatsappUrl)
            <a href="{{ session('whatsapp_url') ?? $whatsappUrl }}" target="_blank" class="flex-1 py-4 bg-green-600 hover:bg-green-700 text-white font-extrabold text-center rounded-2xl shadow-lg transition transform active:scale-95 flex items-center justify-center gap-2">
                <span>💬</span> Konfirmasi via WhatsApp
            </a>
            @endif
            <a href="{{ route('home') }}" class="flex-1 py-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:bg-amber-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 font-extrabold text-center rounded-2xl shadow-sm transition transform active:scale-95 flex items-center justify-center">
                Kembali ke Beranda
            </a>
        </div>

    </div>
</div>
@endsection

@push('scripts')
@if($order->snap_token && $order->payment_status === 'unpaid')
<script src="{{ config('services.midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
@endif
<!-- Leaflet.js Library -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    @if($order->snap_token && $order->payment_status === 'unpaid')
    // Expose triggerSnapPayment globally so both the inline card button and the DOM listener can execute it
    window.triggerSnapPayment = function() {
        const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        const isSnapLoaded = typeof window.snap !== 'undefined';

        if (isMobile || !isSnapLoaded) {
            // For mobile or if snap.js is blocked, redirect parent page directly to the Midtrans check-out site
            const snapUrl = '{{ config('services.midtrans.is_production') ? 'https://app.midtrans.com/snap/v2/vtweb/' : 'https://app.sandbox.midtrans.com/snap/v2/vtweb/' }}' + '{{ $order->snap_token }}';
            window.location.href = snapUrl;
        } else {
            // For desktop, open the snap modal
            window.snap.pay('{{ $order->snap_token }}', {
                onSuccess: function(result){
                    console.log('payment success!', result);
                    confirmPaymentOnServer(result.order_id);
                },
                onPending: function(result){
                    console.log('payment pending...', result);
                    confirmPaymentOnServer(result.order_id);
                },
                onError: function(result){
                    console.log('payment error!', result);
                    alert('Pembayaran gagal atau terjadi kesalahan. Silakan coba lagi.');
                },
                onClose: function(){
                    console.log('customer closed the popup without finishing the payment');
                }
            });
        }
    };

    document.addEventListener("DOMContentLoaded", function() {
        const payButton = document.getElementById('pay-button');
        if (payButton) {
            payButton.addEventListener('click', function () {
                triggerSnapPayment();
            });
        }

        // Automatically open Snap payment redirect/popup on load
        setTimeout(function() {
            triggerSnapPayment();
        }, 800);

        function confirmPaymentOnServer(orderId) {
            fetch('{{ route('order.confirm-payment') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ order_id: orderId })
            })
            .then(response => response.json())
            .then(data => {
                console.log('Server payment confirmation:', data);
                window.location.reload();
            })
            .catch(error => {
                console.error('Error confirming payment:', error);
                window.location.reload();
            });
        }
    });
    @endif

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

    // =========================================================================
    // Domino's Pizza Tracker Polling & Audio Update Logic
    // =========================================================================
    const orderId = {{ $order->id }};
    const isLoggedIn = {{ auth()->check() ? 'true' : 'false' }};
    let currentStatus = '{{ $order->status }}';

    // Map order status to progress bar percentage and active steps
    const statusSteps = {
        'pending': { percent: 10, index: 0 },
        'confirmed': { percent: 32, index: 1 },
        'producing': { percent: 55, index: 2 },
        'ready': { percent: 78, index: 3 },
        'done': { percent: 100, index: 4 },
        'cancelled': { percent: 0, index: -1 }
    };

    const statusVoiceAlerts = {
        'pending': 'Pesanan Anda sudah masuk di Mamitha Bakery.',
        'confirmed': 'Pesanan Anda sedang disiapkan di dapur.',
        'producing': 'Roti Anda sedang dipanggang.',
        'ready': 'Roti Anda sudah matang dan siap diantar atau diambil.',
        'done': 'Pesanan selesai! Terima kasih telah berbelanja di Mamitha Bakery.',
        'cancelled': 'Pesanan Anda telah dibatalkan.'
    };

    function updateTrackerUI(status) {
        if (!statusSteps[status]) return;
        
        const config = statusSteps[status];
        const connector = document.getElementById('tracker-connector-fill');
        if (connector) {
            // Apply fill percentage depending on mobile/desktop
            const isMobile = window.innerWidth <= 768;
            if (isMobile) {
                connector.style.height = config.percent + '%';
                connector.style.width = '100%';
            } else {
                connector.style.width = config.percent + '%';
                connector.style.height = '100%';
            }
        }

        // Stepper step elements
        const steps = ['placed', 'confirmed', 'producing', 'ready', 'done'];
        steps.forEach((stepName, idx) => {
            const stepId = 'step-' + (stepName === 'placed' ? 'placed' : stepName);
            const el = document.getElementById(stepId);
            if (!el) return;

            // Reset classes
            el.classList.remove('completed', 'active');
            
            if (idx < config.index) {
                el.classList.add('completed');
            } else if (idx === config.index) {
                el.classList.add('active');
            }
        });

        // Dynamic status banner texts in the DOM
        const bannerHeader = document.querySelector('.p-6.border-b h2');
        if (bannerHeader && status !== currentStatus) {
            if (status === 'done') {
                bannerHeader.textContent = 'Pesanan Telah Tiba';
            } else if (status === 'ready') {
                bannerHeader.textContent = 'Siap Diambil / Diantar';
            }
        }
    }

    // =========================================================================
    // Countdown Timer logic for Producing (Baking) and Ready (Delivery)
    // =========================================================================
    let countdownInterval = null;
    let localTimeOffset = 0; // difference between server time and local time

    function initCountdown(status, type, updatedAtStr, serverTimeStr, bakingMins, deliveryMins) {
        if (countdownInterval) {
            clearInterval(countdownInterval);
            countdownInterval = null;
        }

        const container = document.getElementById('countdown-timer-container');
        const icon = document.getElementById('countdown-timer-icon');
        const label = document.getElementById('countdown-timer-label');
        const clock = document.getElementById('countdown-timer-clock');
        const estimateText = document.getElementById('arrival-estimate-text');

        if (!container || !icon || !label || !clock) return;

        // Parse times
        const statusUpdatedAt = new Date(updatedAtStr).getTime();
        const serverTime = new Date(serverTimeStr).getTime();
        const localTime = Date.now();
        
        // Calculate offset (serverTime - localTime)
        localTimeOffset = serverTime - localTime;

        // Determine if we should show countdown
        let totalDurationSeconds = 0;
        let isCountdownActive = false;
        let activeLabel = '';
        let activeIcon = '';
        let timerClasses = [];
        let doneLabel = '';

        if (status === 'producing') {
            totalDurationSeconds = bakingMins * 60;
            isCountdownActive = true;
            activeLabel = 'Sedang Dipanggang';
            activeIcon = '🔥';
            timerClasses = ['bg-orange-500/10', 'text-orange-600', 'dark:bg-orange-950/20', 'dark:text-orange-400', 'border', 'border-orange-200/30'];
            doneLabel = 'Selesai Dipanggang!';
        } else if (status === 'ready' && type === 'delivery') {
            totalDurationSeconds = deliveryMins * 60;
            isCountdownActive = true;
            activeLabel = 'Dalam Perjalanan';
            activeIcon = '🛵';
            timerClasses = ['bg-blue-500/10', 'text-blue-600', 'dark:bg-blue-950/20', 'dark:text-blue-400', 'border', 'border-blue-200/30'];
            doneLabel = 'Kurir segera sampai!';
        }

        // Clean container classes first
        container.className = 'mt-2 inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-[11px] font-extrabold tracking-wide uppercase transition-all duration-300';
        
        if (!isCountdownActive) {
            container.classList.add('hidden');
            return;
        }

        // Apply classes
        timerClasses.forEach(c => container.classList.add(c));
        container.classList.remove('hidden');
        icon.textContent = activeIcon;
        label.textContent = activeLabel;

        // Run interval
        function updateClock() {
            const currentLocalTime = Date.now();
            const currentServerTimeEstimate = currentLocalTime + localTimeOffset;
            const elapsedSeconds = Math.floor((currentServerTimeEstimate - statusUpdatedAt) / 1000);
            const remainingSeconds = totalDurationSeconds - elapsedSeconds;

            if (remainingSeconds <= 0) {
                clock.textContent = '00:00';
                label.textContent = doneLabel;
                if (status === 'producing' && estimateText) {
                    estimateText.textContent = 'Siap Diambil / Diantar';
                } else if (status === 'ready' && type === 'delivery' && estimateText) {
                    estimateText.textContent = 'Kurir Tiba di Lokasi';
                }
                clearInterval(countdownInterval);
            } else {
                const mins = Math.floor(remainingSeconds / 60);
                const secs = remainingSeconds % 60;
                clock.textContent = (mins < 10 ? '0' : '') + mins + ':' + (secs < 10 ? '0' : '') + secs;
            }
        }

        updateClock();
        countdownInterval = setInterval(updateClock, 1000);
    }

    // Play Audio Tones via Web Audio API
    function playStatusChime() {
        try {
            const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            const playTone = (freq, start, duration) => {
                const osc = audioCtx.createOscillator();
                const gainNode = audioCtx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(freq, start);
                gainNode.gain.setValueAtTime(0.2, start);
                gainNode.gain.exponentialRampToValueAtTime(0.0001, start + duration);
                osc.connect(gainNode);
                gainNode.connect(audioCtx.destination);
                osc.start(start);
                osc.stop(start + duration);
            };
            // Beautiful triple note chime
            playTone(523.25, audioCtx.currentTime, 0.2); // C5
            playTone(659.25, audioCtx.currentTime + 0.15, 0.2); // E5
            playTone(783.99, audioCtx.currentTime + 0.3, 0.4); // G5
        } catch (e) {
            console.error('AudioContext error:', e);
        }
    }

    // Speech synthesis announcement
    function speakStatusChange(status) {
        if (!window.speechSynthesis) return;
        const text = statusVoiceAlerts[status];
        if (!text) return;

        // Cancel previous speaking to prevent backlog
        window.speechSynthesis.cancel();
        
        const utterance = new SpeechSynthesisUtterance(text);
        utterance.lang = 'id-ID'; // Indonesian
        utterance.rate = 1.0;
        utterance.pitch = 1.1;
        window.speechSynthesis.speak(utterance);
    }

    // Polling function (Runs only if logged-in)
    async function pollOrderStatus() {
        if (!isLoggedIn || currentStatus === 'done' || currentStatus === 'cancelled') return;

        try {
            const response = await fetch(`/pesan/status-json/${orderId}`);
            if (!response.ok) throw new Error('Status fetch error');
            const data = await response.json();

            if (data.status && data.status !== currentStatus) {
                // Play sounds and alert
                playStatusChime();
                speakStatusChange(data.status);
                
                // Update local storage and DOM
                currentStatus = data.status;
                updateTrackerUI(data.status);

                // Update countdown dynamically
                initCountdown(
                    data.status,
                    data.type,
                    data.updated_at,
                    data.current_time,
                    data.baking_duration_minutes,
                    data.delivery_duration_minutes
                );

                // If status transitions to Ready or Done, we reload/update map or other view details
                if (data.status === 'ready' || data.status === 'done') {
                    // Slight timeout to let animations complete
                    setTimeout(() => window.location.reload(), 2000);
                }
            }
        } catch (err) {
            console.error('Polling error:', err);
        }
    }

    // Initialize tracker UI and set interval on load
    window.addEventListener('load', () => {
        updateTrackerUI(currentStatus);
        
        // Initial countdown trigger
        initCountdown(
            '{{ $order->status }}',
            '{{ $order->type }}',
            '{{ $order->updated_at->toIso8601String() }}',
            '{{ now()->toIso8601String() }}',
            {{ (int) \App\Models\Setting::getValue('baking_duration_minutes', 15) }},
            {{ (int) \App\Models\Setting::getValue('delivery_duration_minutes', 20) }}
        );

        if (isLoggedIn) {
            // Poll every 5 seconds
            setInterval(pollOrderStatus, 5000);
        }

        // WhatsApp auto-redirect if payment method is WhatsApp Confirmation
        @if($order->payment_method === 'WhatsApp Confirmation')
            const waUrl = "{{ session('whatsapp_url') ?? $whatsappUrl }}";
            if (waUrl) {
                setTimeout(() => {
                    window.location.href = waUrl;
                }, 1500); // 1.5 seconds delay so they see success state first
            }
        @endif
    });

    // Make sure connector line adjusts layout on screen resize
    window.addEventListener('resize', () => {
        updateTrackerUI(currentStatus);
    });

    window.copyText = function(text, successMsg) {
        navigator.clipboard.writeText(text).then(() => {
            alert(successMsg);
        }).catch(err => {
            console.error('Copy failed:', err);
        });
    }
</script>
@endpush

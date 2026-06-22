@extends('layouts.app')

@section('title', 'Settings')

@push('styles')
<style>
    /* Custom Stacking and Animations for circular theme reveal */
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

    /* Rank card animations */
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
</style>
@endpush

@section('content')
<div class="min-h-screen bg-cream-50 dark:bg-gray-950 py-10 px-4 transition-colors duration-200">
    <div class="max-w-md mx-auto space-y-6 relative">
        
        <!-- Header: Title and Save Changes button -->
        <div class="flex justify-between items-center">
            <h1 class="text-3xl font-black text-gray-850 dark:text-white font-serif">Settings</h1>
            <button type="submit" form="profile-form" onclick="triggerHaptic()" class="px-5 py-2.5 bg-[#ff6310] hover:bg-orange-700 text-white text-xs font-black rounded-full shadow-md transition-all active:scale-95 cursor-pointer select-none">
                Save Changes
            </button>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 dark:bg-red-950/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-4 py-3 rounded-2xl text-xs space-y-1">
                <p class="font-bold">Gagal memperbarui profil:</p>
                <ul class="list-disc pl-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="bg-green-50 dark:bg-green-950/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 px-4 py-3 rounded-2xl text-xs font-bold">
                {{ session('success') }}
            </div>
        @endif

        <!-- Form wrapper for profile edit -->
        <form id="profile-form" action="{{ route('member.profile.update') }}" method="POST" class="space-y-6">
            @csrf

            <!-- 1. Profile Header Card -->
            <div class="bg-gradient-to-r from-amber-900 to-stone-900 rounded-[32px] p-6 text-white shadow-xl relative overflow-hidden border border-amber-800/30">
                <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:12px_12px] pointer-events-none"></div>
                <div class="relative z-10 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <!-- Profile Avatar -->
                        <div class="w-16 h-16 rounded-full bg-white/20 border-2 border-white/40 flex items-center justify-center text-2xl font-black shadow-inner select-none uppercase">
                            {{ substr($customer->name, 0, 1) }}
                        </div>
                        <div>
                            <h2 class="text-lg font-black tracking-wide">{{ $customer->name }}</h2>
                            <p class="text-xs text-amber-200/80 font-mono mt-0.5">{{ $customer->user->email }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Info Fields Card -->
            <div class="space-y-3">
                <span class="text-[9px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] block pl-2">Personal Information</span>
                <div class="bg-white dark:bg-gray-800 rounded-3xl border border-amber-200/70 dark:border-gray-750 p-5 space-y-4 shadow-sm">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 mb-1.5 uppercase tracking-wider">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ $customer->name }}" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 dark:text-white transition">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 mb-1.5 uppercase tracking-wider">Nomor WhatsApp</label>
                        <input type="text" name="phone" value="{{ $customer->phone }}" placeholder="Contoh: 081234567890" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 dark:text-white transition">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 mb-1.5 uppercase tracking-wider">Alamat default</label>
                        <textarea name="address" rows="3" placeholder="Masukkan alamat lengkap pengiriman" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 dark:text-white transition">{{ $customer->address }}</textarea>
                    </div>
                </div>
            </div>

        <!-- Collapsible Membership Status -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl border border-amber-200/70 dark:border-gray-750 shadow-sm overflow-hidden transition duration-200">
            <button type="button" onclick="toggleSection('member-status-content')" class="w-full px-6 py-4 flex items-center justify-between text-left focus:outline-none select-none cursor-pointer">
                <div class="flex items-center gap-3">
                    <span class="text-lg">🥇</span>
                    <span class="text-xs font-black text-gray-850 dark:text-gray-100 uppercase tracking-wider">Membership Rank Status</span>
                </div>
                <svg id="arrow-member-status" class="w-4 h-4 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            
            <div id="member-status-content" class="px-6 pb-6 space-y-6 hidden border-t border-amber-50/50 dark:border-gray-700/30 pt-4">
                <!-- Virtual Card -->
                <div class="bg-gradient-to-br from-amber-700 to-amber-900 rounded-2xl p-5 text-white shadow-md relative overflow-hidden border border-amber-600/30">
                    <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:10px_10px] pointer-events-none"></div>
                    <div class="relative z-10 flex flex-col justify-between h-28">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-[8px] uppercase font-bold tracking-widest text-amber-200">Kartu Member Eksklusif</p>
                                <h3 class="text-sm font-bold font-serif">MAMITHA MEMBER</h3>
                            </div>
                            <span class="text-2xl select-none">🥐</span>
                        </div>
                        <div class="flex justify-between items-end border-t border-white/10 pt-2 mt-2">
                            <div>
                                <p class="text-[8px] uppercase font-bold tracking-widest text-amber-200">ID Anggota</p>
                                <p class="text-xs font-mono font-bold">MTH-MBR-{{ str_pad($customer->id, 5, '0', STR_PAD_LEFT) }}</p>
                            </div>
                            <span class="bg-white/20 border border-white/30 rounded-lg px-2 py-0.5 text-[10px] font-black uppercase tracking-wider">
                                {{ $customer->rank_badge }} {{ $customer->rank_name }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Points progression -->
                <div class="space-y-3">
                    <div class="flex justify-between items-end">
                        <div>
                            <p class="text-[9px] text-gray-400 uppercase font-bold">Total Poin</p>
                            <p class="text-lg font-black text-amber-800 dark:text-amber-400">{{ $customer->points }} <span class="text-xs text-gray-400 font-semibold">Poin</span></p>
                        </div>
                        <div class="text-right">
                            <p class="text-[9px] text-gray-400 uppercase font-bold">Target Berikutnya</p>
                            <p class="text-xs font-black text-gray-700 dark:text-gray-300">{{ $customer->next_rank_name }}</p>
                        </div>
                    </div>
                    <!-- Progress Bar -->
                    <div class="w-full h-2.5 bg-gray-150 dark:bg-gray-700 rounded-full overflow-hidden relative shadow-inner">
                        <div class="h-full bg-gradient-to-r {{ $customer->rank_theme['bg'] }} rounded-full" 
                             style="width: {{ $customer->rank_progress_percentage }}%;"></div>
                    </div>
                    <p class="text-[10px] text-gray-500 dark:text-gray-400 leading-normal">
                        @if($customer->rank_name === 'Platinum')
                            👑 Anda telah mencapai peringkat tertinggi! Nikmati layanan prioritas utama.
                        @else
                            Kumpulkan <span class="font-extrabold text-amber-700 dark:text-amber-400">{{ $customer->points_for_next_rank }} Poin</span> lagi untuk naik ke <span class="font-bold">{{ $customer->next_rank_name }} Member</span>.
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- 2. APPEARANCE SECTION -->
        <div class="space-y-3">
            <span class="text-[9px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] block pl-2">Appearance</span>
            <div class="grid grid-cols-2 gap-4">
                
                <!-- Daylight Option -->
                <div id="theme-daylight-card" onclick="setAppTheme('light', event)" class="theme-card border-2 rounded-[28px] p-4 cursor-pointer transition flex flex-col justify-between text-left h-44 relative select-none bg-white dark:bg-gray-800 border-amber-200/50 dark:border-gray-700 shadow-sm">
                    <!-- Daylight Card Mockup (exact preview from screenshot) -->
                    <div class="w-full h-20 bg-gradient-to-br from-amber-50 via-cream-50 to-orange-100 border border-amber-200/50 rounded-xl p-3 flex flex-col justify-between shadow-inner">
                        <div class="space-y-1.5">
                            <div class="h-1.5 w-10 bg-amber-800/30 rounded-full"></div>
                            <div class="h-1 w-16 bg-amber-800/10 rounded-full"></div>
                        </div>
                        <div class="flex items-center gap-1">
                            <div class="h-3 w-8 bg-amber-600 rounded-full"></div>
                            <div class="h-2 w-2 bg-amber-200 rounded-full"></div>
                        </div>
                    </div>
                    <div class="flex justify-between items-center mt-2.5">
                        <div>
                            <h4 class="text-xs font-black text-gray-850 dark:text-gray-100 tracking-wide">Daylight</h4>
                            <p class="text-[9px] text-gray-400 dark:text-gray-500 mt-0.5">Light theme</p>
                        </div>
                        <!-- Radio indicator -->
                        <div class="theme-radio w-5 h-5 rounded-full border-2 border-gray-250 dark:border-gray-650 flex items-center justify-center transition">
                            <div class="theme-radio-inner w-2.5 h-2.5 rounded-full bg-[#ff6310] hidden"></div>
                        </div>
                    </div>
                </div>

                <!-- Midnight Option -->
                <div id="theme-midnight-card" onclick="setAppTheme('dark', event)" class="theme-card border-2 rounded-[28px] p-4 cursor-pointer transition flex flex-col justify-between text-left h-44 relative select-none bg-white dark:bg-gray-800 border-amber-200/50 dark:border-gray-700 shadow-sm">
                    <!-- Midnight Card Mockup (exact preview from screenshot) -->
                    <div class="w-full h-20 bg-gray-900 border border-gray-800 rounded-xl p-3 flex flex-col justify-between shadow-inner">
                        <div class="space-y-1.5">
                            <div class="h-1.5 w-10 bg-white/20 rounded-full"></div>
                            <div class="h-1 w-16 bg-white/5 rounded-full"></div>
                        </div>
                        <div class="flex items-center gap-1">
                            <div class="h-3 w-8 bg-[#ff6310] rounded-full"></div>
                            <div class="h-2 w-2 bg-gray-800 rounded-full"></div>
                        </div>
                    </div>
                    <div class="flex justify-between items-center mt-2.5">
                        <div>
                            <h4 class="text-xs font-black text-gray-850 dark:text-gray-100 tracking-wide">Midnight</h4>
                            <p class="text-[9px] text-gray-400 dark:text-gray-500 mt-0.5">Dark theme</p>
                        </div>
                        <!-- Radio indicator -->
                        <div class="theme-radio w-5 h-5 rounded-full border-2 border-gray-200 dark:border-gray-700 flex items-center justify-center transition">
                            <div class="theme-radio-inner w-2.5 h-2.5 rounded-full bg-[#ff6310] hidden"></div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- 3. PREFERENCES SECTION -->
        <div class="space-y-3">
            <span class="text-[9px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] block pl-2">Preferences</span>
            <div class="bg-white dark:bg-gray-800 rounded-3xl border border-amber-200/70 dark:border-gray-750 p-4 space-y-4 shadow-sm">
                
                <!-- Push Notifications -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-amber-50 dark:bg-gray-900 flex items-center justify-center text-sm shadow-inner select-none">
                            🔔
                        </div>
                        <div>
                            <h4 class="text-xs font-black text-gray-800 dark:text-gray-100">Notifikasi Pemberitahuan</h4>
                            <p class="text-[9px] text-gray-450 dark:text-gray-500">Dapatkan Update Tentang Order dan Menu Terbaru</p>
                        </div>
                    </div>
                    <!-- Custom Switch (styled like screenshot) -->
                    <button type="button" onclick="togglePreference('push_notif')" id="pref-push_notif" class="pref-switch w-12 h-6 rounded-full bg-gray-200 dark:bg-gray-700 relative p-1 transition-colors duration-250 cursor-pointer focus:outline-none select-none">
                        <div class="switch-dot w-4 h-4 bg-white rounded-full shadow-md transform transition-transform duration-250"></div>
                    </button>
                </div>

                <!-- Unlock with Face ID -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-amber-50 dark:bg-gray-900 flex items-center justify-center text-sm shadow-inner select-none">
                            👤
                        </div>
                        <div>
                            <h4 class="text-xs font-black text-gray-800 dark:text-gray-100">Buka Dengan Face ID</h4>
                            <p class="text-[9px] text-gray-450 dark:text-gray-500">Mengamankan Akses Ke Portal Membership</p>
                        </div>
                    </div>
                    <button type="button" onclick="togglePreference('face_id')" id="pref-face_id" class="pref-switch w-12 h-6 rounded-full bg-gray-200 dark:bg-gray-700 relative p-1 transition-colors duration-250 cursor-pointer focus:outline-none select-none">
                        <div class="switch-dot w-4 h-4 bg-white rounded-full shadow-md transform transition-transform duration-250"></div>
                    </button>
                </div>

                <!-- Haptic Feedback -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-amber-50 dark:bg-gray-900 flex items-center justify-center text-sm shadow-inner select-none">
                            📳
                        </div>
                        <div>
                            <h4 class="text-xs font-black text-gray-800 dark:text-gray-100">Getaran Umpan Balik</h4>
                            <p class="text-[9px] text-gray-450 dark:text-gray-500">Umpan Balik Fisik Taktil Melalui Getaran di Ponsel Anda</p>
                        </div>
                    </div>
                    <button type="button" onclick="togglePreference('haptic_feedback')" id="pref-haptic_feedback" class="pref-switch w-12 h-6 rounded-full bg-gray-200 dark:bg-gray-700 relative p-1 transition-colors duration-250 cursor-pointer focus:outline-none select-none">
                        <div class="switch-dot w-4 h-4 bg-white rounded-full shadow-md transform transition-transform duration-250"></div>
                    </button>
                </div>

            </div>
        </div>

        <!-- 4. ACCOUNT SECTION -->
        <div class="space-y-3">
            <span class="text-[9px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] block pl-2">Account</span>
            <div class="bg-white dark:bg-gray-800 rounded-3xl border border-amber-200/70 dark:border-gray-750 p-2 shadow-sm divide-y divide-amber-100 dark:divide-gray-750">
                
                <!-- My orders -->
                <button type="button" onclick="toggleSection('orders-section'); triggerHaptic();" class="w-full flex items-center justify-between p-3.5 hover:bg-amber-50/20 dark:hover:bg-gray-700/30 rounded-2xl transition cursor-pointer select-none text-left focus:outline-none">
                    <div class="flex items-center gap-3">
                        <span class="text-base select-none">📦</span>
                        <span class="text-xs font-black text-gray-800 dark:text-gray-200">Pesanan Saya</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="bg-orange-500/10 text-[#ff6310] text-[9px] font-bold px-2 py-0.5 rounded-full">{{ count($orders) }}</span>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </div>
                </button>

                <!-- Payment methods -->
                <button type="button" onclick="openModal('modal-payment-methods')" class="w-full flex items-center justify-between p-3.5 hover:bg-amber-50/20 dark:hover:bg-gray-700/30 rounded-2xl transition cursor-pointer select-none text-left focus:outline-none">
                    <div class="flex items-center gap-3">
                        <span class="text-base select-none">💳</span>
                        <span class="text-xs font-black text-gray-800 dark:text-gray-200">Metode Pembayaran</span>
                    </div>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </button>

                <!-- Saved addresses -->
                <button type="button" onclick="openModal('modal-saved-addresses')" class="w-full flex items-center justify-between p-3.5 hover:bg-amber-50/20 dark:hover:bg-gray-700/30 rounded-2xl transition cursor-pointer select-none text-left focus:outline-none">
                    <div class="flex items-center gap-3">
                        <span class="text-base select-none">📍</span>
                        <span class="text-xs font-black text-gray-800 dark:text-gray-200">Alamat Tersimpan</span>
                    </div>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </button>

            </div>
        </div>

        </form>

        <!-- 5. GREEN LOGOUT BUTTON (Matching screenshots) -->
        <form method="POST" action="{{ route('logout') }}" id="logout-form">
            @csrf
            <button type="submit" onclick="triggerHaptic()" class="w-full py-4.5 bg-[#10b981] hover:bg-emerald-600 text-white font-extrabold text-sm rounded-[24px] shadow-lg shadow-emerald-500/10 transition-all duration-200 transform active:scale-95 flex items-center justify-center gap-2.5 cursor-pointer">
                <!-- Log out door icon -->
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span>Keluar dari Akun</span>
            </button>
        </form>

        <!-- 6. DYNAMIC ORDER HISTORY PANEL (slides down when clicking My Orders) -->
        <div id="orders-section" class="hidden space-y-4 pt-2">
            <h3 class="text-lg font-bold text-gray-850 dark:text-white font-serif">Riwayat Pesanan Member</h3>
            
            @forelse($orders as $order)
            <!-- Order Card -->
            <div class="bg-white dark:bg-gray-800 rounded-3xl border border-amber-100/50 dark:border-gray-700/50 shadow-sm p-5 space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-amber-50 dark:border-gray-700/50 pb-3">
                    <div>
                        <div class="flex items-center gap-1.5">
                            <span class="text-[9px] text-gray-400 font-bold uppercase">No:</span>
                            <span class="font-extrabold text-xs text-amber-800 dark:text-amber-400">{{ $order->order_number }}</span>
                        </div>
                        <p class="text-[9px] text-gray-400 mt-0.5">{{ $order->order_date->format('d M Y, H:i') }}</p>
                    </div>
                    
                    <div class="flex items-center gap-1.5">
                        <span class="px-2 py-0.5 text-[9px] font-black rounded-lg {{ $order->type === 'delivery' ? 'bg-blue-50 text-blue-700 dark:bg-blue-950/20 dark:text-blue-400' : 'bg-amber-50 text-amber-800 dark:bg-amber-950/20 dark:text-amber-300' }}">
                            {{ $order->type === 'delivery' ? '🚚 Delivery' : '🏪 Pickup' }}
                        </span>
                        
                        @if($order->status == 'pending')
                            <span class="px-2 py-0.5 bg-yellow-500/10 text-yellow-600 dark:text-yellow-450 text-[9px] font-black rounded-lg border border-yellow-500/20">⏳ Pending</span>
                        @elseif($order->status == 'confirmed')
                            <span class="px-2 py-0.5 bg-blue-500/10 text-blue-600 dark:text-blue-400 text-[9px] font-black rounded-lg border border-blue-500/20">👍 Dikonfirmasi</span>
                        @elseif($order->status == 'producing')
                            <span class="px-2 py-0.5 bg-orange-500/10 text-orange-600 dark:text-orange-400 text-[9px] font-black rounded-lg border border-orange-500/20">🔥 Dipanggang</span>
                        @elseif($order->status == 'ready')
                            <span class="px-2 py-0.5 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[9px] font-black rounded-lg border border-emerald-500/20">🛵 Siap</span>
                        @elseif($order->status == 'done')
                            <span class="px-2 py-0.5 bg-green-500/10 text-green-600 dark:text-green-400 text-[9px] font-black rounded-lg border border-green-500/20">✅ Selesai</span>
                        @else
                            <span class="px-2 py-0.5 bg-red-500/10 text-red-600 dark:text-red-400 text-[9px] font-black rounded-lg border border-red-500/20">❌ Dibatalkan</span>
                        @endif
                    </div>
                </div>

                <!-- Item details -->
                <div class="divide-y divide-amber-50 dark:divide-gray-700/30">
                    @foreach($order->items as $item)
                    <div class="py-2 flex items-center justify-between text-xs">
                        <div class="flex items-center gap-2">
                            @if($item->product && $item->product->image)
                                <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}" class="w-8 h-8 object-cover rounded-lg">
                            @else
                                <div class="w-8 h-8 bg-amber-50 dark:bg-amber-950/20 rounded-lg flex items-center justify-center text-sm">🍞</div>
                            @endif
                            <div>
                                <p class="font-bold text-gray-800 dark:text-gray-200">
                                    {{ $item->product->name }}
                                    @if($item->variant)
                                        <span class="text-[9px] text-gray-500 dark:text-gray-400 font-normal">({{ $item->variant->name }})</span>
                                    @endif
                                </p>
                                <p class="text-[9px] text-gray-400 mt-0.5">Rp {{ number_format($item->price, 0, ',', '.') }} x {{ $item->quantity }}</p>
                            </div>
                        </div>
                        <span class="font-bold text-gray-850 dark:text-gray-200">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                    @endforeach
                </div>

                <div class="border-t border-amber-50 dark:border-gray-700/50 pt-3 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <div>
                        <p class="text-[8px] text-gray-400 font-bold uppercase">Total Belanja</p>
                        <p class="text-sm font-black text-amber-800 dark:text-amber-400">Rp {{ number_format($order->total, 0, ',', '.') }}</p>
                    </div>
                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $storeWhatsapp) }}?text=Halo%20Mamitha%20Bakery,%20saya%20ingin%20tanya%20mengenai%20order%20{{ $order->order_number }}" target="_blank" class="flex-1 text-center px-3 py-1.5 bg-green-500 hover:bg-green-600 text-white font-extrabold text-[10px] rounded-lg transition active:scale-95 select-none">
                            💬 Hubungi
                        </a>
                        <a href="{{ route('order.success', $order->id) }}" class="flex-1 text-center px-3 py-1.5 bg-gray-900 dark:bg-gray-100 hover:bg-amber-600 dark:hover:bg-amber-500 text-white dark:text-gray-900 font-extrabold text-[10px] rounded-lg transition active:scale-95 select-none">
                            📍 Lacak Status &rarr;
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="bg-white dark:bg-gray-800 rounded-3xl border border-amber-100/50 dark:border-gray-700/50 p-8 text-center space-y-2 shadow-sm">
                <span class="text-3xl block">🥐</span>
                <h4 class="font-bold text-gray-800 dark:text-gray-250">Belum Ada Transaksi</h4>
                <p class="text-[11px] text-gray-500 dark:text-gray-400 max-w-xs mx-auto leading-normal">Silakan lakukan pesanan pertama Anda untuk mulai mengumpulkan poin member!</p>
            </div>
            @endforelse
        </div>

    </div>
</div>

{{-- MODALS SECTION --}}



<!-- Modal 2: Payment Methods -->
<div id="modal-payment-methods" class="modal-wrapper fixed inset-0 bg-gray-900/60 dark:bg-black/85 flex items-center justify-center p-4 z-[99999] opacity-0 pointer-events-none transition-all duration-300">
    <div class="modal-card bg-white dark:bg-gray-800 rounded-[32px] w-full max-w-md p-6 border border-amber-50 dark:border-gray-700 shadow-2xl transform scale-90 transition-all duration-300">
        <div class="flex justify-between items-center border-b border-amber-50 dark:border-gray-750 pb-4 mb-4">
            <h3 class="text-xl font-bold text-gray-850 dark:text-white font-serif">Payment Methods</h3>
            <button onclick="closeModal('modal-payment-methods')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 focus:outline-none cursor-pointer text-sm font-bold">Close</button>
        </div>
        <div class="space-y-3" id="payment-methods-list-container">
            @forelse($customer->paymentMethods as $pm)
            <div class="flex items-center justify-between p-3.5 bg-gray-50 dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-750 select-none">
                <div class="flex items-center gap-3">
                    <span class="text-xl">
                        @if($pm->type === 'credit_card') 💳 
                        @elseif($pm->type === 'e_wallet') 📱
                        @else 🏦 @endif
                    </span>
                    <div>
                        <p class="text-xs font-black text-gray-800 dark:text-gray-250">{{ $pm->provider }} ({{ $pm->account_name }})</p>
                        <p class="text-[9px] text-gray-400 font-mono mt-0.5">
                            @if($pm->type === 'credit_card')
                                **** **** **** {{ substr($pm->account_number, -4) }}
                            @else
                                {{ $pm->account_number }}
                            @endif
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    @if($pm->is_default)
                        <span class="text-[9px] text-orange-650 font-extrabold bg-orange-500/10 dark:bg-orange-950/20 px-2 py-0.5 rounded-full select-none">Default</span>
                    @endif
                    <!-- Edit button -->
                    <button type="button" onclick="editPaymentMethod({{ json_encode($pm) }})" class="p-1 text-gray-450 hover:text-amber-500 dark:hover:text-amber-400 transition" title="Edit">
                        ✏️
                    </button>
                    <!-- Delete form -->
                    <button type="button" onclick="confirmDeletePaymentMethod('delete-form-{{ $pm->id }}')" class="p-1 text-red-500 hover:text-red-700 transition" title="Hapus">
                        🗑️
                    </button>
                    <form id="delete-form-{{ $pm->id }}" action="{{ route('member.payment-method.destroy', $pm->id) }}" method="POST" class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>
            @empty
            <div class="text-center py-6 text-gray-400 text-xs">
                Belum ada metode pembayaran disimpan.
            </div>
            @endforelse

            <button type="button" id="btn-show-payment-form" onclick="showAddPaymentForm()" class="w-full py-3 bg-transparent border-2 border-dashed border-gray-300 dark:border-gray-700 hover:border-orange-500 text-gray-500 hover:text-orange-500 font-bold text-xs rounded-xl transition cursor-pointer text-center select-none">
                + Tambah Metode Pembayaran
            </button>
        </div>

        <!-- Form to Add/Edit Payment Method -->
        <form id="payment-method-form" method="POST" action="{{ route('member.payment-method.store') }}" class="hidden space-y-4 border-t border-amber-50 dark:border-gray-750 pt-4 mt-4">
            @csrf
            <!-- Form Method Spoofing for Update -->
            <div id="method-field-container"></div>

            <h4 id="payment-form-title" class="text-sm font-bold text-gray-800 dark:text-gray-100 font-serif">Tambah Metode Pembayaran</h4>
            
            <div>
                <label class="block text-[9px] font-bold text-gray-400 mb-1.5 uppercase tracking-wider">Tipe Pembayaran</label>
                <select name="type" id="payment-type-select" required onchange="onPaymentTypeChange()" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-250 dark:border-gray-700 rounded-xl text-xs focus:ring-2 focus:ring-amber-500 focus:border-amber-500 dark:text-white transition">
                    <option value="credit_card">💳 Kartu Kredit</option>
                    <option value="e_wallet">📱 E-Wallet (DANA, GoPay, OVO, etc.)</option>
                    <option value="bank_transfer">🏦 Transfer Bank (BCA, Mandiri, BNI, etc.)</option>
                </select>
            </div>

            <div>
                <label id="payment-provider-label" class="block text-[9px] font-bold text-gray-400 mb-1.5 uppercase tracking-wider">Nama Penyedia / Nama Bank</label>
                <select name="provider" id="payment-provider-input" required class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-250 dark:border-gray-700 rounded-xl text-xs focus:ring-2 focus:ring-amber-500 focus:border-amber-500 dark:text-white transition">
                </select>
            </div>

            <div>
                <label id="payment-number-label" class="block text-[9px] font-bold text-gray-400 mb-1.5 uppercase tracking-wider">Nomor Akun / Kartu</label>
                <input type="text" name="account_number" id="payment-number-input" required placeholder="Masukkan nomor rekening, kartu, atau HP" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-250 dark:border-gray-700 rounded-xl text-xs focus:ring-2 focus:ring-amber-500 focus:border-amber-500 dark:text-white transition">
            </div>

            <div>
                <label id="payment-name-label" class="block text-[9px] font-bold text-gray-400 mb-1.5 uppercase tracking-wider">Nama Pemilik Akun / Kartu</label>
                <input type="text" name="account_name" id="payment-name-input" value="{{ $customer->name }}" readonly required placeholder="Masukkan nama pemilik" class="w-full px-3 py-2.5 bg-gray-100 dark:bg-gray-800 border border-gray-250 dark:border-gray-700 rounded-xl text-xs focus:ring-2 focus:ring-amber-500 focus:border-amber-500 dark:text-white transition cursor-not-allowed">
            </div>

            <div class="flex gap-2 justify-end pt-2">
                <button type="button" onclick="hidePaymentForm()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-650 text-gray-600 dark:text-gray-300 text-xs font-bold rounded-xl transition cursor-pointer select-none">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-[#ff6310] hover:bg-orange-700 text-white text-xs font-bold rounded-xl transition shadow cursor-pointer select-none">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 3: Saved Addresses -->
<div id="modal-saved-addresses" class="modal-wrapper fixed inset-0 bg-gray-900/60 dark:bg-black/85 flex items-center justify-center p-4 z-[99999] opacity-0 pointer-events-none transition-all duration-300">
    <div class="modal-card bg-white dark:bg-gray-800 rounded-[32px] w-full max-w-md p-6 border border-amber-50 dark:border-gray-700 shadow-2xl transform scale-90 transition-all duration-300">
        <div class="flex justify-between items-center border-b border-amber-50 dark:border-gray-750 pb-4 mb-4">
            <h3 class="text-xl font-bold text-gray-850 dark:text-white font-serif">Saved Addresses</h3>
            <button onclick="closeModal('modal-saved-addresses')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 focus:outline-none cursor-pointer text-sm font-bold">Close</button>
        </div>
        <div class="space-y-4">
            <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-750 relative select-none">
                <div class="flex justify-between items-start">
                    <span class="text-[10px] font-black uppercase text-orange-600 tracking-wider">🏠 Rumah (Alamat Utama)</span>
                    <button onclick="editAddressOnPage()" class="text-[10px] text-gray-400 hover:text-orange-600 font-bold transition">Ubah</button>
                </div>
                <p class="text-xs font-bold text-gray-800 dark:text-gray-200 mt-2">{{ $customer->name }} ({{ $customer->phone ?: '-' }})</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 leading-relaxed">{{ $customer->address ?: 'Belum mengatur alamat default.' }}</p>
            </div>
            
            <button onclick="editAddressOnPage()" class="w-full py-3 bg-[#ff6310] hover:bg-orange-700 text-white font-extrabold text-xs rounded-xl transition shadow active:scale-95 cursor-pointer text-center select-none">
                Kelola Alamat Pengiriman
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Trigger vibration haptic feedback if enabled
    function triggerHaptic() {
        const hapticEnabled = localStorage.getItem('pref_haptic_feedback') !== 'false';
        if (hapticEnabled && navigator.vibrate) {
            navigator.vibrate([15]);
        }
    }

    // Scroll to and focus address input directly on the settings page
    function editAddressOnPage() {
        closeModal('modal-saved-addresses');
        setTimeout(() => {
            const addressField = document.querySelector('textarea[name="address"]');
            if (addressField) {
                addressField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                addressField.focus();
            }
        }, 300);
    }

    // Toggle Section visibility (e.g. My Orders or Membership status)
    function toggleSection(sectionId) {
        const section = document.getElementById(sectionId);
        if (!section) return;
        
        section.classList.toggle('hidden');
        triggerHaptic();

        // Rotate arrow icon if present
        if (sectionId === 'member-status-content') {
            const arrow = document.getElementById('arrow-member-status');
            if (arrow) arrow.classList.toggle('rotate-180');
        }
    }

    // Modal Control functions
    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) return;
        
        triggerHaptic();
        modal.classList.remove('opacity-0', 'pointer-events-none');
        modal.querySelector('.modal-card').classList.remove('scale-90');
        modal.querySelector('.modal-card').classList.add('scale-100');
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) return;
        
        triggerHaptic();
        modal.classList.add('opacity-0', 'pointer-events-none');
        modal.querySelector('.modal-card').classList.remove('scale-100');
        modal.querySelector('.modal-card').classList.add('scale-90');
    }

    // Toggle Preference Switch states (Push Notif, Face ID, Haptic)
    function togglePreference(key) {
        const btn = document.getElementById(`pref-${key}`);
        const dot = btn.querySelector('.switch-dot');
        const isActive = btn.classList.contains('bg-[#ff6310]');
        
        triggerHaptic();
        
        if (isActive) {
            btn.classList.remove('bg-[#ff6310]');
            btn.classList.add('bg-gray-200', 'dark:bg-gray-700');
            dot.classList.remove('translate-x-6');
            localStorage.setItem(`pref_${key}`, 'false');
        } else {
            btn.classList.add('bg-[#ff6310]');
            btn.classList.remove('bg-gray-200', 'dark:bg-gray-700');
            dot.classList.add('translate-x-6');
            localStorage.setItem(`pref_${key}`, 'true');
        }
    }

    // Update styling matching current active theme (Light/Dark cards)
    function syncThemeCardUI(isDark) {
        const daylightCard = document.getElementById('theme-daylight-card');
        const midnightCard = document.getElementById('theme-midnight-card');
        
        if (isDark) {
            // Select Midnight
            midnightCard?.classList.add('border-[#ff6310]', 'bg-amber-50/5');
            midnightCard?.classList.remove('border-gray-100', 'dark:border-gray-700');
            const midnightRadio = midnightCard?.querySelector('.theme-radio');
            if (midnightRadio) midnightRadio.className = 'theme-radio w-5 h-5 rounded-full border-2 border-[#ff6310] flex items-center justify-center transition';
            midnightCard?.querySelector('.theme-radio-inner')?.classList.remove('hidden');
            
            // Deselect Daylight
            daylightCard?.classList.remove('border-[#ff6310]', 'bg-amber-50/5');
            daylightCard?.classList.add('border-gray-100', 'dark:border-gray-700');
            const daylightRadio = daylightCard?.querySelector('.theme-radio');
            if (daylightRadio) daylightRadio.className = 'theme-radio w-5 h-5 rounded-full border-2 border-gray-200 dark:border-gray-700 flex items-center justify-center transition';
            daylightCard?.querySelector('.theme-radio-inner')?.classList.add('hidden');
        } else {
            // Select Daylight
            daylightCard?.classList.add('border-[#ff6310]', 'bg-amber-50/5');
            daylightCard?.classList.remove('border-gray-100', 'dark:border-gray-700');
            const daylightRadio = daylightCard?.querySelector('.theme-radio');
            if (daylightRadio) daylightRadio.className = 'theme-radio w-5 h-5 rounded-full border-2 border-[#ff6310] flex items-center justify-center transition';
            daylightCard?.querySelector('.theme-radio-inner')?.classList.remove('hidden');
            
            // Deselect Midnight
            midnightCard?.classList.remove('border-[#ff6310]', 'bg-amber-50/5');
            midnightCard?.classList.add('border-gray-100', 'dark:border-gray-700');
            const midnightRadio = midnightCard?.querySelector('.theme-radio');
            if (midnightRadio) midnightRadio.className = 'theme-radio w-5 h-5 rounded-full border-2 border-gray-200 dark:border-gray-700 flex items-center justify-center transition';
            midnightCard?.querySelector('.theme-radio-inner')?.classList.add('hidden');
        }
    }

    // Listen to theme changes from the parent layout to keep these cards synced
    const originalApplyTheme = window.applyTheme;
    window.applyTheme = function(isDark) {
        originalApplyTheme(isDark);
        syncThemeCardUI(isDark);
    };

    // Initialize UI states on page load
    window.addEventListener('load', () => {
        // 1. Theme sync
        const currentTheme = document.documentElement.classList.contains('dark') ? 'dark' : 'light';
        syncThemeCardUI(currentTheme === 'dark');

        // 2. Preferences switches sync
        ['push_notif', 'face_id', 'haptic_feedback'].forEach(key => {
            const btn = document.getElementById(`pref-${key}`);
            if (!btn) return;
            const dot = btn.querySelector('.switch-dot');
            const val = localStorage.getItem(`pref_${key}`) !== 'false';
            
            if (val) {
                btn.classList.add('bg-[#ff6310]');
                btn.classList.remove('bg-gray-200', 'dark:bg-gray-700');
                dot.classList.add('translate-x-6');
            } else {
                btn.classList.remove('bg-[#ff6310]');
                btn.classList.add('bg-gray-200', 'dark:bg-gray-700');
                dot.classList.remove('translate-x-6');
            }
        });

        // 3. Auto-fill user name when typing account/phone number
        document.getElementById('payment-number-input')?.addEventListener('input', function() {
            const nameInput = document.getElementById('payment-name-input');
            if (nameInput && !nameInput.value.trim()) {
                nameInput.value = "{{ auth()->user()->name }}";
            }
        });
    });

    // Saved Payment Methods JS Handlers
    function showAddPaymentForm() {
        triggerHaptic();
        
        const form = document.getElementById('payment-method-form');
        if (!form) return;
        
        // Reset form inputs
        form.reset();
        document.getElementById('method-field-container').innerHTML = '';
        form.action = "{{ route('member.payment-method.store') }}";
        document.getElementById('payment-form-title').innerText = 'Tambah Metode Pembayaran';
        
        // Set the pre-populated registered name
        document.getElementById('payment-name-input').value = "{{ $customer->name }}";
        
        onPaymentTypeChange();
        
        form.classList.remove('hidden');
        document.getElementById('btn-show-payment-form').classList.add('hidden');
    }
    
    function hidePaymentForm() {
        triggerHaptic();
        
        const form = document.getElementById('payment-method-form');
        if (!form) return;
        
        form.classList.add('hidden');
        document.getElementById('btn-show-payment-form').classList.remove('hidden');
    }
    
    function populateProviderOptions(type, currentValue = '') {
        const select = document.getElementById('payment-provider-input');
        if (!select) return;
        
        select.innerHTML = '';
        
        let options = [];
        if (type === 'e_wallet') {
            options = [
                { value: 'DANA', label: '📱 DANA' },
                { value: 'GoPay', label: '📱 GoPay' },
                { value: 'OVO', label: '📱 OVO' },
                { value: 'LinkAja', label: '📱 LinkAja' },
                { value: 'ShopeePay', label: '📱 ShopeePay' }
            ];
        } else if (type === 'bank_transfer') {
            options = [
                { value: 'BCA', label: '🏦 Bank Central Asia (BCA)' },
                { value: 'Mandiri', label: '🏦 Bank Mandiri' },
                { value: 'BNI', label: '🏦 Bank Negara Indonesia (BNI)' },
                { value: 'BRI', label: '🏦 Bank Rakyat Indonesia (BRI)' },
                { value: 'BSI', label: '🏦 Bank Syariah Indonesia (BSI)' },
                { value: 'CIMB Niaga', label: '🏦 CIMB Niaga' }
            ];
        } else if (type === 'credit_card') {
            options = [
                { value: 'Visa', label: '💳 Visa' },
                { value: 'Mastercard', label: '💳 Mastercard' },
                { value: 'JCB', label: '💳 JCB' },
                { value: 'American Express', label: '💳 American Express' }
            ];
        }
        
        options.forEach(opt => {
            const el = document.createElement('option');
            el.value = opt.value;
            el.text = opt.label;
            if (opt.value === currentValue) {
                el.selected = true;
            }
            select.appendChild(el);
        });
    }

    function onPaymentTypeChange(savedProvider = '') {
        const select = document.getElementById('payment-type-select');
        if (!select) return;
        
        const type = select.value;
        const providerLabel = document.getElementById('payment-provider-label');
        const numberLabel = document.getElementById('payment-number-label');
        const numberInput = document.getElementById('payment-number-input');
        const nameLabel = document.getElementById('payment-name-label');
        const nameInput = document.getElementById('payment-name-input');
        
        populateProviderOptions(type, savedProvider);
        
        if (type === 'credit_card') {
            providerLabel.innerText = 'Penyedia Kartu';
            numberLabel.innerText = 'Nomor Kartu Kredit';
            numberInput.placeholder = 'Contoh: 4111222233334444';
            nameLabel.innerText = 'Nama di Kartu';
            nameInput.placeholder = 'Contoh: John Doe';
        } else if (type === 'e_wallet') {
            providerLabel.innerText = 'Penyedia E-Wallet';
            numberLabel.innerText = 'Nomor HP Terdaftar';
            numberInput.placeholder = 'Contoh: 081234567890';
            nameLabel.innerText = 'Nama Terdaftar';
            nameInput.placeholder = 'Contoh: John Doe';
        } else if (type === 'bank_transfer') {
            providerLabel.innerText = 'Nama Bank';
            numberLabel.innerText = 'Nomor Rekening';
            numberInput.placeholder = 'Contoh: 1234567890';
            nameLabel.innerText = 'Nama Pemilik Rekening';
            nameInput.placeholder = 'Contoh: John Doe';
        }
    }
    
    function editPaymentMethod(pm) {
        triggerHaptic();
        
        const form = document.getElementById('payment-method-form');
        if (!form) return;
        
        // Show form
        form.classList.remove('hidden');
        document.getElementById('btn-show-payment-form').classList.add('hidden');
        
        // Change action url and title
        form.action = `/profil/metode-pembayaran/${pm.id}`;
        document.getElementById('payment-form-title').innerText = 'Ubah Metode Pembayaran';
        
        // Inject PUT method hidden input
        document.getElementById('method-field-container').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        
        // Populate inputs
        document.getElementById('payment-type-select').value = pm.type;
        document.getElementById('payment-number-input').value = pm.account_number;
        document.getElementById('payment-name-input').value = pm.account_name;
        
        // Trigger select change to sync labels
        onPaymentTypeChange(pm.provider);
    }
</script>
@endpush

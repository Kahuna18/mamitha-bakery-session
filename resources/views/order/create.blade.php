@extends('layouts.app')

@section('title', 'Pesan Roti')

@push('styles')
<!-- Leaflet CSS for Map -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<style>
    /* Custom Scrollbar for Category Pills */
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .no-scrollbar {
        -ms-overflow-style: none;  /* IE and Edge */
        scrollbar-width: none;  /* Firefox */
    }

    /* Checkout Drawer Animation */
    .drawer-transition {
        transition: transform 0.3s ease-in-out;
    }

    /* Leaflet Map custom styles */
    #map-container {
        height: 240px;
        z-index: 10;
    }

    /* Dark Mode overrides for Leaflet */
    .dark .leaflet-tile {
        filter: brightness(0.6) invert(1) contrast(3) hue-rotate(200deg) saturate(0.3);
    }
    .dark .leaflet-container {
        background: #111827;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-cream-50 dark:bg-gray-900 text-gray-800 dark:text-gray-100 transition-colors duration-200">
    <!-- Pizza Mojo Style Header Banner -->
    <div class="relative h-64 md:h-80 w-full overflow-hidden">
        <!-- Warm Gradient Background + Bakery Pattern Mock -->
        <div class="absolute inset-0 bg-gradient-to-r from-amber-700 via-amber-800 to-orange-800 opacity-95"></div>
        <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:16px_16px]"></div>
        
        <!-- Premium Floating Details Card -->
        <div class="absolute bottom-0 inset-x-0 transform translate-y-12 max-w-5xl mx-auto px-4">
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl border border-amber-100/50 dark:border-gray-700/50 p-6 flex flex-col md:flex-row items-center md:items-start md:justify-between gap-6">
                <div class="flex flex-col md:flex-row items-center gap-6">
                    <!-- Logo / Emblem -->
                    <div class="w-20 h-20 rounded-2xl flex items-center justify-center shadow-md border-2 border-white dark:border-gray-700 transform -mt-12 md:mt-0 overflow-hidden bg-white">
                        <img src="{{ asset('images/logo.jpeg') }}" class="w-full h-full object-cover" alt="Logo">
                    </div>
                    <!-- Brand info -->
                    <div class="text-center md:text-left">
                        <div class="flex items-center justify-center md:justify-start gap-2">
                            <h1 class="text-2xl md:text-3xl font-extrabold text-amber-900 dark:text-amber-100 font-serif">Mamitha Bakery</h1>
                            <span class="bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-xs font-semibold px-2.5 py-0.5 rounded-full">Buka</span>
                        </div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm mt-1 max-w-md">Roti hangat fresh baked setiap hari dengan bahan-bahan premium pilihan.</p>
                        
                        <!-- Badges/Info Row -->
                        <div class="flex flex-wrap justify-center md:justify-start gap-4 mt-3 text-xs text-gray-600 dark:text-gray-300 font-medium">
                            <span class="flex items-center gap-1"><span class="text-amber-500">★</span> 4.9 (1.2k+ rating)</span>
                            <span class="flex items-center gap-1">🕒 07:00 - 20:00</span>
                            <span class="flex items-center gap-1 text-orange-600 dark:text-orange-400">🏷️ Diskon 10% Otomatis</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="max-w-5xl mx-auto px-4 pt-24 pb-32">
        @if(!$canOrder)
        <div class="bg-orange-50 dark:bg-orange-950/20 border border-orange-200 dark:border-orange-900/40 text-orange-700 dark:text-orange-400 px-6 py-4 rounded-2xl text-center mb-8">
            <p class="font-bold text-lg">Maaf, toko sedang tutup atau kuota pesanan hari ini penuh.</p>
            <p class="text-sm mt-1">Silakan coba beberapa saat lagi atau hubungi admin via WhatsApp.</p>
        </div>
        @endif

        <!-- Search and Categories Row -->
        <div class="mb-8 space-y-4">
            <!-- Search Bar -->
            <div class="relative max-w-md">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </span>
                <input type="text" id="search-input" placeholder="Cari roti manis, cake, snack..." class="w-full pl-11 pr-4 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 dark:focus:ring-amber-600 transition" onkeyup="filterProducts()">
            </div>

            <!-- Categories Horizontal Scroll -->
            <div class="flex gap-2 overflow-x-auto no-scrollbar pb-2 mask-linear">
                <button onclick="filterCategory('all', this)" class="category-pill whitespace-nowrap px-5 py-2.5 rounded-full text-xs font-semibold bg-amber-600 text-white shadow-sm transition">
                    Semua Menu
                </button>
                @foreach($categories as $cat)
                <button onclick="filterCategory('{{ $cat->id }}', this)" class="category-pill whitespace-nowrap px-5 py-2.5 rounded-full text-xs font-semibold bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-amber-50 dark:hover:bg-gray-700 border border-gray-100 dark:border-gray-700/50 transition">
                    {{ $cat->name }}
                </button>
                @endforeach
            </div>
        </div>

        <!-- Product List (Pizza Mojo Style Cards) -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="product-grid">
            @foreach($products as $product)
            <div class="product-card bg-white dark:bg-gray-800 rounded-3xl border border-amber-100/40 dark:border-gray-700/40 overflow-hidden shadow-sm hover:shadow-md transition-all duration-300 flex flex-col justify-between" data-category="{{ $product->category_id }}" data-name="{{ strtolower($product->name) }}">
                <div>
                    <!-- Product Image Section -->
                    <div class="relative aspect-[4/3] bg-amber-50/50 dark:bg-gray-900/50 overflow-hidden group">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover transition duration-500 group-hover:scale-105 {{ $product->stock <= 0 ? 'grayscale opacity-50' : '' }}">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-7xl select-none {{ $product->stock <= 0 ? 'grayscale opacity-50' : '' }}">
                                🍞
                            </div>
                        @endif
                        <!-- Tags overlay -->
                        <div class="absolute top-3 left-3 flex flex-col gap-1">
                            <span class="bg-amber-100 dark:bg-amber-950/40 text-amber-800 dark:text-amber-300 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider">
                                {{ $product->category->name }}
                            </span>
                            @if($product->stock > 0)
                            <span class="bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider">
                                10% OFF
                            </span>
                            @endif
                        </div>
                        <!-- Stock Badge -->
                        <div class="absolute top-3 right-3">
                            @if($product->stock <= 0)
                                <span class="bg-red-600 text-white text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider shadow-md">
                                    Habis
                                </span>
                            @else
                                <span class="bg-white/90 dark:bg-gray-800/90 text-gray-700 dark:text-gray-200 text-[10px] font-bold px-2.5 py-1 rounded-full shadow-sm backdrop-blur-sm">
                                    Stok: {{ $product->stock }}
                                </span>
                            @endif
                        </div>
                        <!-- Out of Stock Overlay -->
                        @if($product->stock <= 0)
                        <div class="absolute inset-0 bg-gray-900/30 flex items-center justify-center">
                            <span class="bg-red-600 text-white text-sm font-extrabold px-5 py-2 rounded-full shadow-lg transform -rotate-12">
                                SOLD OUT
                            </span>
                        </div>
                        @endif
                    </div>

                    <!-- Details -->
                    <div class="p-5">
                        <div class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400">
                            <span>⏱️ 15-20 min</span>
                            <span>•</span>
                            <span class="text-amber-500">★ 4.9</span>
                        </div>
                        <h3 class="font-bold text-gray-800 dark:text-gray-100 text-lg mt-1 font-serif">{{ $product->name }}</h3>
                        <p class="text-gray-500 dark:text-gray-400 text-xs mt-1 line-clamp-2">{{ $product->description ?? 'Roti hangat dan empuk yang dibuat fresh hari ini.' }}</p>
                    </div>
                </div>

                <!-- Footer Card -->
                <div class="p-5 pt-0 flex items-center justify-between mt-auto">
                    <!-- Price block -->
                    <div>
                        <span class="text-xs text-gray-400 line-through">Rp {{ number_format($product->price * 1.1, 0, ',', '.') }}</span>
                        <p class="text-amber-800 dark:text-amber-400 font-extrabold text-lg -mt-1">
                            Rp {{ number_format($product->price, 0, ',', '.') }}
                        </p>
                    </div>

                    <!-- Interactive Add Button -->
                    @if($product->stock > 0)
                    <div class="relative w-28 h-10 flex items-center justify-end" id="btn-container-{{ $product->id }}">
                        <!-- Add Button -->
                        <button onclick="addToCart({{ $product->id }}, '{{ $product->name }}', {{ $product->price }})" class="add-btn px-4 py-2 bg-gray-900 dark:bg-gray-100 hover:bg-amber-700 dark:hover:bg-amber-500 text-white dark:text-gray-900 text-xs font-extrabold rounded-full shadow-sm hover:shadow-md transition duration-200">
                            + ADD
                        </button>
                        
                        <!-- Quantity Selector Controls (Hidden initially) -->
                        <div class="qty-controls absolute inset-0 bg-gray-900 dark:bg-gray-100 rounded-full flex items-center justify-between px-2 text-white dark:text-gray-900 scale-0 opacity-0 transition duration-200">
                            <button onclick="decrementQty({{ $product->id }})" class="w-8 h-8 rounded-full flex items-center justify-center hover:bg-gray-800 dark:hover:bg-gray-200 font-extrabold text-sm">-</button>
                            <span class="font-extrabold text-sm" id="card-qty-{{ $product->id }}">0</span>
                            <button onclick="incrementQty({{ $product->id }})" class="w-8 h-8 rounded-full flex items-center justify-center hover:bg-gray-800 dark:hover:bg-gray-200 font-extrabold text-sm">+</button>
                        </div>
                    </div>
                    @else
                    <div class="relative w-28 h-10 flex items-center justify-end">
                        <span class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-500 dark:text-gray-400 text-xs font-extrabold rounded-full cursor-not-allowed">
                            HABIS
                        </span>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Floating Bottom Cart Bar -->
    <div id="floating-cart" class="fixed bottom-6 inset-x-4 max-w-lg mx-auto bg-gray-900 dark:bg-gray-100 text-white dark:text-gray-900 rounded-3xl shadow-2xl p-4 flex items-center justify-between z-40 transform translate-y-32 opacity-0 transition-all duration-300">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-amber-600 rounded-2xl flex items-center justify-center text-xl shadow-inner">
                🛒
            </div>
            <div>
                <p class="text-xs text-gray-400 dark:text-gray-600" id="cart-item-count">0 Item</p>
                <p class="font-extrabold text-base text-amber-400 dark:text-amber-700" id="cart-total-price">Rp 0</p>
            </div>
        </div>
        <button onclick="toggleCheckoutDrawer(true)" class="px-5 py-2.5 bg-amber-600 hover:bg-amber-500 text-white text-xs font-black rounded-2xl shadow transition">
            Lihat Keranjang →
        </button>
    </div>

    <!-- Checkout Overlay Panel (Tiktok Checkout Style Drawer) -->
    <div id="checkout-overlay" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-50 opacity-0 pointer-events-none transition-all duration-300">
        <div class="absolute inset-y-0 right-0 max-w-lg w-full bg-white dark:bg-gray-900 shadow-2xl flex flex-col justify-between drawer-transition transform translate-x-full">
            
            <!-- Drawer Header -->
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <button onclick="toggleCheckoutDrawer(false)" class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-500">
                        ←
                    </button>
                    <h2 class="text-xl font-bold font-serif text-gray-800 dark:text-gray-100">Checkout</h2>
                </div>
                <span class="text-xs text-amber-700 dark:text-amber-400 font-bold bg-amber-50 dark:bg-amber-950/30 px-3 py-1 rounded-full">Mamitha Bakery</span>
            </div>

            <!-- Drawer Body (Form Content) -->
            <div class="flex-1 overflow-y-auto px-6 py-4 space-y-6">
                <form id="orderForm" method="POST" action="{{ route('order.store') }}">
                    @csrf
                    <!-- Dynamic Hidden Inputs Container -->
                    <div id="hidden-cart-inputs"></div>

                    <!-- Personal Information -->
                    <div class="space-y-4">
                        <h3 class="font-bold text-gray-700 dark:text-gray-300 text-sm tracking-wide uppercase">Data Diri</h3>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Nama Lengkap</label>
                            <input type="text" name="name" required placeholder="Masukkan nama Anda" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border-0 rounded-2xl text-sm focus:ring-2 focus:ring-amber-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Nomor WhatsApp</label>
                            <input type="tel" name="phone" required placeholder="Contoh: 08123456789" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border-0 rounded-2xl text-sm focus:ring-2 focus:ring-amber-500">
                        </div>
                    </div>

                    <!-- Delivery Type Options -->
                    <div class="space-y-4 mt-6">
                        <h3 class="font-bold text-gray-700 dark:text-gray-300 text-sm tracking-wide uppercase">Pilihan Pengiriman</h3>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="border-2 border-gray-100 dark:border-gray-800 rounded-2xl p-4 cursor-pointer hover:border-amber-500 dark:hover:border-amber-600 transition flex flex-col items-center justify-center text-center has-[:checked]:border-amber-600 has-[:checked]:bg-amber-50/35">
                                <input type="radio" name="type" value="pickup" checked class="hidden" onchange="updateDeliveryType('pickup')">
                                <span class="text-2xl mb-1">🏪</span>
                                <span class="font-bold text-xs text-gray-800 dark:text-gray-200">Ambil di Toko</span>
                            </label>
                            <label class="border-2 border-gray-100 dark:border-gray-800 rounded-2xl p-4 cursor-pointer hover:border-amber-500 dark:hover:border-amber-600 transition flex flex-col items-center justify-center text-center has-[:checked]:border-amber-600 has-[:checked]:bg-amber-50/35">
                                <input type="radio" name="type" value="delivery" class="hidden" onchange="updateDeliveryType('delivery')">
                                <span class="text-2xl mb-1">🚚</span>
                                <span class="font-bold text-xs text-gray-800 dark:text-gray-200">Diantar Kurir</span>
                            </label>
                        </div>
                    </div>

                    <!-- Delivery Address & Leaflet Map (Shows only for delivery) -->
                    <div id="delivery-details-section" class="hidden mt-6 space-y-4">
                        <h3 class="font-bold text-gray-700 dark:text-gray-300 text-sm tracking-wide uppercase">Detail Lokasi</h3>
                        <div class="relative">
                            <input type="text" id="map-search" placeholder="Cari alamat di peta..." class="w-full pl-4 pr-10 py-3 bg-gray-50 dark:bg-gray-800 border-0 rounded-2xl text-sm focus:ring-2 focus:ring-amber-500" onkeydown="if(event.key==='Enter'){ searchAddress(); event.preventDefault(); }">
                            <button type="button" onclick="searchAddress()" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-amber-500">
                                🔍
                            </button>
                        </div>

                        <!-- GPS Locate Me Button -->
                        <button type="button" onclick="useMyLocation()" id="gps-btn" class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 text-white text-xs font-extrabold rounded-2xl shadow-md transition-all duration-200 active:scale-[0.98]">
                            <svg class="w-4 h-4 animate-none" id="gps-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span id="gps-text">📍 Gunakan Lokasi Saya (GPS)</span>
                        </button>
                        
                        <!-- Map Visual Container -->
                        <div class="w-full rounded-2xl border border-gray-100 dark:border-gray-800 overflow-hidden shadow-sm">
                            <div id="map-container"></div>
                        </div>

                        <!-- Lat/Lng Hidden Inputs -->
                        <input type="hidden" name="latitude" id="latitude">
                        <input type="hidden" name="longitude" id="longitude">

                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Alamat Lengkap Pengiriman</label>
                            <textarea name="address" id="address-text" rows="3" required placeholder="Tuliskan nama jalan, blok, nomor rumah..." class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border-0 rounded-2xl text-sm focus:ring-2 focus:ring-amber-500"></textarea>
                        </div>

                        <!-- Shipping Option Card -->
                        <div class="bg-gray-50 dark:bg-gray-800/50 p-4 rounded-2xl border border-gray-100 dark:border-gray-800 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="text-2xl">⚡</span>
                                <div>
                                    <p class="text-xs font-extrabold text-gray-800 dark:text-gray-200">Standard Delivery</p>
                                    <p class="text-[10px] text-gray-400">Arriving in 15-20 min • Tercepat</p>
                                </div>
                            </div>
                            <span class="text-xs font-bold text-amber-700 dark:text-amber-400">Rp 10.000</span>
                        </div>
                    </div>

                    <!-- Pickup/Delivery Schedule Date -->
                    <div class="space-y-4 mt-6">
                        <h3 class="font-bold text-gray-700 dark:text-gray-300 text-sm tracking-wide uppercase">Waktu Pengambilan</h3>
                        <input type="date" name="pickup_date" id="pickup-date-input" value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border-0 rounded-2xl text-sm focus:ring-2 focus:ring-amber-500">
                    </div>

                    <!-- Order Notes -->
                    <div class="space-y-4 mt-6">
                        <h3 class="font-bold text-gray-700 dark:text-gray-300 text-sm tracking-wide uppercase">Catatan Tambahan</h3>
                        <textarea name="notes" rows="2" placeholder="Catatan opsional (contoh: jangan terlalu manis, dll)" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border-0 rounded-2xl text-sm focus:ring-2 focus:ring-amber-500"></textarea>
                    </div>

                    <!-- Payment Methods (Tiktok Checkout Style) -->
                    <div class="space-y-4 mt-6">
                        <h3 class="font-bold text-gray-700 dark:text-gray-300 text-sm tracking-wide uppercase">Metode Pembayaran</h3>
                        <div class="space-y-2">
                            <!-- Transfer Bank -->
                            <label class="border-2 border-gray-100 dark:border-gray-800 rounded-2xl p-4 cursor-pointer hover:border-amber-500 dark:hover:border-amber-600 transition flex items-center justify-between has-[:checked]:border-amber-600 has-[:checked]:bg-amber-50/35">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-amber-50 dark:bg-amber-950/20 rounded-xl flex items-center justify-center text-lg">🏦</div>
                                    <div>
                                        <p class="font-bold text-xs text-gray-800 dark:text-gray-200">Transfer Bank / QRIS</p>
                                        <p class="text-[10px] text-gray-400">Verifikasi otomatis & aman</p>
                                    </div>
                                </div>
                                <input type="radio" name="payment_method" value="transfer" checked class="text-amber-600 focus:ring-amber-500">
                            </label>

                            <!-- WhatsApp Manual -->
                            <label class="border-2 border-gray-100 dark:border-gray-800 rounded-2xl p-4 cursor-pointer hover:border-amber-500 dark:hover:border-amber-600 transition flex items-center justify-between has-[:checked]:border-amber-600 has-[:checked]:bg-amber-50/35">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-green-50 dark:bg-green-950/20 rounded-xl flex items-center justify-center text-lg">💬</div>
                                    <div>
                                        <p class="font-bold text-xs text-gray-800 dark:text-gray-200">WhatsApp Confirmation</p>
                                        <p class="text-[10px] text-gray-400">Konfirmasi detail manual dengan admin</p>
                                    </div>
                                </div>
                                <input type="radio" name="payment_method" value="whatsapp" class="text-amber-600 focus:ring-amber-500">
                            </label>

                            <!-- Cash On Delivery -->
                            <label class="border-2 border-gray-100 dark:border-gray-800 rounded-2xl p-4 cursor-pointer hover:border-amber-500 dark:hover:border-amber-600 transition flex items-center justify-between has-[:checked]:border-amber-600 has-[:checked]:bg-amber-50/35">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-orange-50 dark:bg-orange-950/20 rounded-xl flex items-center justify-center text-lg">💵</div>
                                    <div>
                                        <p class="font-bold text-xs text-gray-800 dark:text-gray-200">Cash On Delivery / COD</p>
                                        <p class="text-[10px] text-gray-400">Bayar saat roti diterima</p>
                                    </div>
                                </div>
                                <input type="radio" name="payment_method" value="cod" class="text-amber-600 focus:ring-amber-500">
                            </label>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Drawer Footer (Order Summary & Pay Button) -->
            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800 space-y-4 bg-gray-50/70 dark:bg-gray-900/50">
                <div class="space-y-1.5 text-xs text-gray-500 dark:text-gray-400">
                    <div class="flex justify-between">
                        <span>Harga Roti (Subtotal)</span>
                        <span id="summary-subtotal" class="font-medium text-gray-800 dark:text-gray-200">Rp 0</span>
                    </div>
                    <div class="flex justify-between text-green-600 dark:text-green-400">
                        <span>Diskon Promo (10% OFF)</span>
                        <span id="summary-discount" class="font-bold">-Rp 0</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Biaya Pengiriman</span>
                        <span id="summary-shipping" class="font-medium text-gray-800 dark:text-gray-200">Rp 0</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Biaya Layanan & Pajak</span>
                        <span id="summary-tax" class="font-medium text-gray-800 dark:text-gray-200">Rp 2.000</span>
                    </div>
                    <div class="flex justify-between text-base font-black text-gray-800 dark:text-gray-100 pt-2 border-t border-gray-200/50 dark:border-gray-700/50">
                        <span>Total Bayar</span>
                        <span id="summary-total" class="text-amber-800 dark:text-amber-400 text-lg font-black">Rp 2.000</span>
                    </div>
                </div>

                <!-- Pay Button -->
                <button type="button" onclick="submitOrder()" class="w-full py-4 bg-gray-900 dark:bg-gray-100 hover:bg-amber-700 dark:hover:bg-amber-600 text-white dark:text-gray-900 font-extrabold text-sm rounded-2xl shadow-xl transition-all duration-200">
                    Pesan Sekarang & Hubungi Admin →
                </button>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Leaflet.js Map Library -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    // Delivery Fee Settings passed from backend
    const deliveryFeeEnabled = {{ $deliveryFeeEnabled ? 'true' : 'false' }};
    const deliveryFeeAmount = {{ $deliveryFeeAmount }};

    // Cart State
    let cart = {};
    let products = {};

    // Load initial products list details into JS object
    @foreach($products as $product)
    products[{{ $product->id }}] = {
        name: '{{ $product->name }}',
        price: {{ $product->price }},
        stock: {{ $product->stock ?? 0 }}
    };
    @endforeach

    // Add product to Cart
    function addToCart(productId, name, price) {
        if (!cart[productId]) {
            cart[productId] = {
                id: productId,
                name: name,
                price: price,
                qty: 1
            };
        }
        updateCartState(productId);
    }

    // Increment qty (respect stock limit)
    function incrementQty(productId) {
        if (cart[productId]) {
            const maxStock = products[productId]?.stock || 0;
            if (cart[productId].qty >= maxStock) {
                // Show brief shake animation on the qty controls
                const container = document.getElementById('btn-container-' + productId);
                if (container) {
                    container.classList.add('animate-pulse');
                    setTimeout(() => container.classList.remove('animate-pulse'), 300);
                }
                return; // Don't exceed stock
            }
            cart[productId].qty++;
            updateCartState(productId);
        }
    }

    // Decrement qty
    function decrementQty(productId) {
        if (cart[productId]) {
            cart[productId].qty--;
            if (cart[productId].qty <= 0) {
                delete cart[productId];
                resetCardButton(productId);
            } else {
                updateCartState(productId);
            }
            updateCartState(null); // refresh main calculations
        }
    }

    // Update dynamic state in DOM
    function updateCartState(productId) {
        let totalItems = 0;
        let totalPrice = 0;
        let discount = 0;
        let shippingFee = (document.querySelector('input[name="type"]:checked')?.value === 'delivery' && deliveryFeeEnabled) ? deliveryFeeAmount : 0;
        let serviceTax = 2000;

        // Update target card inputs if a specific card was actioned
        if (productId && cart[productId]) {
            const cardQtySpan = document.getElementById('card-qty-' + productId);
            const container = document.getElementById('btn-container-' + productId);
            const addBtn = container.querySelector('.add-btn');
            const qtyControls = container.querySelector('.qty-controls');

            // Apply scaling animation
            addBtn.classList.add('scale-0', 'opacity-0');
            qtyControls.classList.remove('scale-0', 'opacity-0');
            qtyControls.classList.add('scale-100', 'opacity-100');
            
            if (cardQtySpan) cardQtySpan.textContent = cart[productId].qty;
        }

        // Loop over cart and sum
        Object.keys(cart).forEach(id => {
            totalItems += cart[id].qty;
            totalPrice += cart[id].price * cart[id].qty;
        });

        // 10% OFF discount
        discount = Math.round(totalPrice * 0.1);

        // Update Floating Cart Bar
        const floatingCart = document.getElementById('floating-cart');
        const itemCountSpan = document.getElementById('cart-item-count');
        const totalPriceSpan = document.getElementById('cart-total-price');

        if (totalItems > 0) {
            floatingCart.classList.remove('translate-y-32', 'opacity-0');
            floatingCart.classList.add('translate-y-0', 'opacity-100');
            itemCountSpan.textContent = totalItems + ' Item';
            totalPriceSpan.textContent = 'Rp ' + totalPrice.toLocaleString('id-ID');
        } else {
            floatingCart.classList.remove('translate-y-0', 'opacity-100');
            floatingCart.classList.add('translate-y-32', 'opacity-0');
        }

        // Update Checkout Drawer Summaries
        document.getElementById('summary-subtotal').textContent = 'Rp ' + totalPrice.toLocaleString('id-ID');
        document.getElementById('summary-discount').textContent = '-Rp ' + discount.toLocaleString('id-ID');
        document.getElementById('summary-shipping').textContent = 'Rp ' + shippingFee.toLocaleString('id-ID');
        
        let finalTotal = totalPrice - discount + shippingFee + serviceTax;
        if (totalItems === 0) finalTotal = 0; // reset total if nothing selected
        
        document.getElementById('summary-total').textContent = 'Rp ' + finalTotal.toLocaleString('id-ID');

        // Dynamically build hidden input fields inside the form
        const hiddenContainer = document.getElementById('hidden-cart-inputs');
        hiddenContainer.innerHTML = '';
        Object.keys(cart).forEach(id => {
            hiddenContainer.innerHTML += `
                <input type="hidden" name="items[${id}][product_id]" value="${id}">
                <input type="hidden" name="items[${id}][quantity]" value="${cart[id].qty}">
            `;
        });
    }

    // Reset card button back to + ADD
    function resetCardButton(productId) {
        const container = document.getElementById('btn-container-' + productId);
        const addBtn = container.querySelector('.add-btn');
        const qtyControls = container.querySelector('.qty-controls');

        qtyControls.classList.remove('scale-100', 'opacity-100');
        qtyControls.classList.add('scale-0', 'opacity-0');
        addBtn.classList.remove('scale-0', 'opacity-0');
        addBtn.classList.add('scale-100', 'opacity-100');

        const cardQtySpan = document.getElementById('card-qty-' + productId);
        if (cardQtySpan) cardQtySpan.textContent = '0';
    }

    // Search filter
    function filterProducts() {
        const query = document.getElementById('search-input').value.toLowerCase();
        document.querySelectorAll('.product-card').forEach(card => {
            const name = card.dataset.name;
            if (name.includes(query)) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }

    // Category filter
    function filterCategory(catId, btn) {
        // Highlight active button
        document.querySelectorAll('.category-pill').forEach(pill => {
            pill.classList.remove('bg-amber-600', 'text-white', 'shadow-sm');
            pill.classList.add('bg-white', 'dark:bg-gray-800', 'text-gray-700', 'dark:text-gray-300', 'hover:bg-amber-50', 'dark:hover:bg-gray-700', 'border', 'border-gray-100', 'dark:border-gray-700/50');
        });
        btn.classList.add('bg-amber-600', 'text-white', 'shadow-sm');
        btn.classList.remove('bg-white', 'dark:bg-gray-800', 'text-gray-700', 'dark:text-gray-300', 'hover:bg-amber-50', 'dark:hover:bg-gray-700', 'border', 'border-gray-100', 'dark:border-gray-700/50');

        document.querySelectorAll('.product-card').forEach(card => {
            if (catId === 'all' || card.dataset.category === catId) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }

    // Toggle checkout panel drawer (TikTok style slide over)
    function toggleCheckoutDrawer(open) {
        const overlay = document.getElementById('checkout-overlay');
        const drawer = overlay.querySelector('.drawer-transition');

        if (open) {
            overlay.classList.remove('opacity-0', 'pointer-events-none');
            overlay.classList.add('opacity-100');
            drawer.classList.remove('translate-x-full');
            drawer.classList.add('translate-x-0');
            
            // Trigger leaflet map resizing when drawer opens
            setTimeout(() => {
                if (map) {
                    map.invalidateSize();
                }
            }, 300);
        } else {
            overlay.classList.add('opacity-0', 'pointer-events-none');
            overlay.classList.remove('opacity-100');
            drawer.classList.add('translate-x-full');
            drawer.classList.remove('translate-x-0');
        }
    }

    // Close drawer when clicking backdrop overlay
    document.getElementById('checkout-overlay').addEventListener('click', function(e) {
        if (e.target === this) {
            toggleCheckoutDrawer(false);
        }
    });

    // Update delivery selection
    function updateDeliveryType(type) {
        const deliverySection = document.getElementById('delivery-details-section');
        const addressText = document.getElementById('address-text');
        
        if (type === 'delivery') {
            deliverySection.classList.remove('hidden');
            addressText.setAttribute('required', 'required');
            setTimeout(() => {
                if (map) map.invalidateSize();
            }, 100);
        } else {
            deliverySection.classList.add('hidden');
            addressText.removeAttribute('required');
        }
        updateCartState(null);
    }

    // Submit Order Form
    function submitOrder() {
        const form = document.getElementById('orderForm');
        // Validate inputs
        if (form.reportValidity()) {
            form.submit();
        }
    }

    // =========================================================================
    // Leaflet.js Map Integration
    // =========================================================================
    let map, marker;
    const storeLocation = [{{ $storeLat }}, {{ $storeLng }}]; // Mamitha Bakery Coordinate

    window.addEventListener('load', () => {
        initLeafletMap();

        // Auto-select product from URL if provided (e.g. ?product=3)
        const urlParams = new URLSearchParams(window.location.search);
        const preselectId = urlParams.get('product');
        if (preselectId) {
            const btn = document.querySelector(`[onclick^="addToCart(${preselectId}"]`);
            if (btn) btn.click();
        }
    });

    function initLeafletMap() {
        // Set default location to Store location
        map = L.map('map-container').setView(storeLocation, 14);

        // Add standard beautiful OpenStreetMap layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Set store marker
        L.marker(storeLocation).addTo(map)
            .bindPopup('🥐 Mamitha Bakery')
            .openPopup();

        // Set user/delivery draggable marker
        marker = L.marker(storeLocation, { draggable: true }).addTo(map);
        
        // Update lat/lng fields initially
        document.getElementById('latitude').value = storeLocation[0];
        document.getElementById('longitude').value = storeLocation[1];

        // Marker drag handler
        marker.on('dragend', function(e) {
            const latlng = marker.getLatLng();
            document.getElementById('latitude').value = latlng.lat;
            document.getElementById('longitude').value = latlng.lng;
            reverseGeocode(latlng.lat, latlng.lng);
        });

        // Map click handler to relocate pin
        map.on('click', function(e) {
            marker.setLatLng(e.latlng);
            document.getElementById('latitude').value = e.latlng.lat;
            document.getElementById('longitude').value = e.latlng.lng;
            reverseGeocode(e.latlng.lat, e.latlng.lng);
        });
    }

    // Free OpenStreetMap reverse geocoding via Nominatim API
    function reverseGeocode(lat, lng) {
        fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`)
            .then(res => res.json())
            .then(data => {
                if (data && data.display_name) {
                    document.getElementById('address-text').value = data.display_name;
                    document.getElementById('map-search').value = data.display_name;
                }
            })
            .catch(err => console.error('Geocoding error:', err));
    }

    // Free OpenStreetMap search address via Nominatim API
    function searchAddress() {
        const query = document.getElementById('map-search').value;
        if (!query) return;

        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=1`)
            .then(res => res.json())
            .then(results => {
                if (results && results.length > 0) {
                    const loc = results[0];
                    const lat = parseFloat(loc.lat);
                    const lon = parseFloat(loc.lon);
                    
                    // Update map focus and marker
                    map.setView([lat, lon], 16);
                    marker.setLatLng([lat, lon]);

                    document.getElementById('latitude').value = lat;
                    document.getElementById('longitude').value = lon;
                    document.getElementById('address-text').value = loc.display_name;
                } else {
                    alert('Lokasi tidak ditemukan. Silakan gerakkan pin manual di peta.');
                }
            })
            .catch(err => console.error('Search error:', err));
    }

    // =========================================================================
    // GPS Geolocation — Locate Customer's Device
    // =========================================================================
    function useMyLocation() {
        const btn = document.getElementById('gps-btn');
        const icon = document.getElementById('gps-icon');
        const text = document.getElementById('gps-text');

        // Check browser support
        if (!navigator.geolocation) {
            alert('Browser Anda tidak mendukung fitur GPS. Silakan cari alamat secara manual.');
            return;
        }

        // Set loading state
        btn.disabled = true;
        btn.classList.add('opacity-70');
        icon.classList.add('animate-spin');
        text.textContent = 'Mencari lokasi Anda...';

        navigator.geolocation.getCurrentPosition(
            // SUCCESS — got coordinates
            function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                const accuracy = Math.round(position.coords.accuracy);

                // Move map view and marker to GPS location
                map.setView([lat, lng], 17);
                marker.setLatLng([lat, lng]);

                // Update hidden form fields
                document.getElementById('latitude').value = lat;
                document.getElementById('longitude').value = lng;

                // Reverse geocode to fill address
                reverseGeocode(lat, lng);

                // Reset button to success state
                btn.disabled = false;
                btn.classList.remove('opacity-70');
                icon.classList.remove('animate-spin');
                text.textContent = `✅ Lokasi ditemukan (akurasi ${accuracy}m)`;

                // Reset text after 3 seconds
                setTimeout(() => {
                    text.textContent = '📍 Gunakan Lokasi Saya (GPS)';
                }, 3000);
            },
            // ERROR — permission denied or unavailable
            function(error) {
                btn.disabled = false;
                btn.classList.remove('opacity-70');
                icon.classList.remove('animate-spin');

                let msg = '📍 Gunakan Lokasi Saya (GPS)';
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        alert('Akses lokasi ditolak. Silakan izinkan akses lokasi di pengaturan browser Anda, lalu coba lagi.');
                        break;
                    case error.POSITION_UNAVAILABLE:
                        alert('Informasi lokasi tidak tersedia saat ini. Pastikan GPS perangkat Anda aktif.');
                        break;
                    case error.TIMEOUT:
                        alert('Permintaan lokasi timeout. Silakan coba lagi.');
                        break;
                    default:
                        alert('Terjadi kesalahan saat mengambil lokasi. Silakan cari alamat secara manual.');
                }
                text.textContent = msg;
            },
            // OPTIONS — high accuracy, timeout 10s
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    }
</script>
@endpush

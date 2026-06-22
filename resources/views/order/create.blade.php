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

    /* Product Image Clickable */
    .product-image-clickable {
        cursor: pointer;
        position: relative;
    }
    .product-image-clickable::after {
        content: '🔍 Pilih Varian';
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(0,0,0,0);
        color: transparent;
        font-size: 12px;
        font-weight: 800;
        transition: all 0.25s ease;
        border-radius: 0;
    }
    .product-image-clickable:hover::after {
        background: rgba(0,0,0,0.35);
        color: #fff;
    }

    /* Variant Modal overlay & bouncy sheet */
    #variant-modal-overlay {
        transition: opacity 0.3s ease;
    }
    #variant-modal-sheet {
        transition: transform 0.45s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .variant-chip {
        cursor: pointer;
        transition: all 0.18s ease;
        border: 2px solid #e5e7eb;
        border-radius: 9999px;
        padding: 6px 16px;
        font-size: 12px;
        font-weight: 700;
        background: white;
        color: #374151;
        user-select: none;
    }
    .dark .variant-chip {
        border-color: #374151;
        background: #1f2937;
        color: #d1d5db;
    }
    .variant-chip:hover {
        border-color: #d97706;
        background: #fffbeb;
        color: #92400e;
    }
    .dark .variant-chip:hover {
        border-color: #d97706;
        background: #451a03;
        color: #fbbf24;
    }
    .variant-chip.selected {
        border-color: #b45309;
        background: #b45309;
        color: #fff;
        box-shadow: 0 2px 8px rgba(180,83,9,0.3);
    }
    .variant-chip.out-of-stock {
        opacity: 0.45;
        cursor: not-allowed;
        text-decoration: line-through;
    }
    .variant-chip.out-of-stock:hover {
        border-color: #e5e7eb;
        background: white;
        color: #374151;
    }
    .dark .variant-chip.out-of-stock:hover {
        border-color: #374151;
        background: #1f2937;
        color: #d1d5db;
    }
    #modal-add-btn {
        transition: all 0.2s ease;
    }

    /* Drawer Tab Navigation (Inactive) */
    .drawer-tab {
        position: relative;
        padding: 8px 16px;
        font-size: 13px;
        font-weight: 800;
        cursor: pointer;
        user-select: none;
        display: flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
        color: #6b7280;
        transition: color 0.3s ease;
    }
    .dark .drawer-tab {
        color: #9ca3af;
    }
    .drawer-tab.active {
        color: #ffffff !important;
    }
    .drawer-tab .tab-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 20px;
        height: 20px;
        padding: 0 6px;
        border-radius: 9999px;
        font-size: 10px;
        font-weight: 900;
        line-height: 1;
        transition: background-color 0.3s ease, color 0.3s ease;
    }
    .drawer-tab.active .tab-badge {
        background: rgba(255,255,255,0.25);
        color: #fff;
    }
    .drawer-tab.inactive .tab-badge {
        background: #e5e7eb;
        color: #6b7280;
    }
    .dark .drawer-tab.inactive .tab-badge {
        background: #374151;
        color: #9ca3af;
    }
    /* Step indicator dots */
    .step-indicator {
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .step-dot {
        width: 8px;
        height: 8px;
        border-radius: 9999px;
        transition: all 0.3s ease;
    }
    .step-dot.active {
        background: #b45309;
        width: 24px;
    }
    .step-dot.inactive {
        background: #d1d5db;
    }
    .dark .step-dot.inactive {
        background: #4b5563;
    }
    .step-connector {
        width: 20px;
        height: 2px;
        background: #e5e7eb;
        border-radius: 9999px;
    }
    .dark .step-connector {
        background: #374151;
    }

    /* ========================================================================= */
    /* NEW STYLES FOR PREMIUM UI/UX                                             */
    /* ========================================================================= */
    
    /* Cascade Staggered Grid Entrance */
    @keyframes cascadeUp {
        0% {
            opacity: 0;
            transform: translateY(32px) scale(0.96);
        }
        100% {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }
    .product-card-cascade {
        opacity: 0;
        animation: cascadeUp 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    }

    /* Product Card Hover and Active Styles */
    .product-card {
        transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.4s cubic-bezier(0.34, 1.56, 0.64, 1), border-color 0.3s ease;
        will-change: transform, box-shadow;
    }
    .product-card:hover {
        transform: translateY(-8px) scale(1.015);
        box-shadow: 0 20px 32px -8px rgba(180, 83, 9, 0.16), 0 4px 12px -2px rgba(180, 83, 9, 0.08);
        border-color: rgba(217, 119, 6, 0.3);
    }
    .dark .product-card:hover {
        box-shadow: 0 20px 32px -8px rgba(0, 0, 0, 0.5), 0 4px 12px -2px rgba(217, 119, 6, 0.2);
        border-color: rgba(217, 119, 6, 0.45);
    }
    .product-card:active {
        transform: translateY(-2px) scale(0.985);
    }

    .add-btn {
        transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.3s ease;
    }
    .qty-controls {
        transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.3s ease;
    }

    /* Bouncy Popup for Quantity and Badges */
    @keyframes bouncyPop {
        0% { transform: scale(0.9); }
        50% { transform: scale(1.2); }
        100% { transform: scale(1); }
    }
    .qty-bounce {
        animation: bouncyPop 0.4s cubic-bezier(0.34, 1.56, 0.64, 1) forwards !important;
    }

    /* Slider tab for steps */
    #drawer-steps-slider {
        display: flex;
        width: 200%;
        height: 100%;
        transition: transform 0.5s cubic-bezier(0.25, 1, 0.4, 1);
    }

    /* Fix: Drawer body must clip slider overflow to prevent checkout form leaking into cart view */
    .drawer-body-container {
        flex: 1;
        overflow-x: hidden;
        overflow-y: auto;
        position: relative;
    }

    /* Fix: Each slider step must be exactly 50% of the 200% slider = 100% of viewport */
    .drawer-slider-step {
        width: 50%;
        flex-shrink: 0;
        padding: 16px 24px;
        overflow-y: auto;
    }

    /* Active Category & Tab Sliding Backgrounds */
    #category-active-bg {
        z-index: 0;
        transition: left 0.3s cubic-bezier(0.25, 1, 0.4, 1), width 0.3s cubic-bezier(0.25, 1, 0.4, 1), top 0.3s cubic-bezier(0.25, 1, 0.4, 1), height 0.3s cubic-bezier(0.25, 1, 0.4, 1);
    }
    .category-pill {
        position: relative;
        z-index: 10;
        transition: color 0.25s ease;
    }
    .category-pill.active {
        color: #ffffff !important;
    }

    /* Cart Drawer Sliding Tabs */
    #tab-container {
        position: relative;
    }
    #tab-active-bg {
        z-index: 0;
        position: absolute;
        transition: left 0.3s cubic-bezier(0.25, 1, 0.4, 1), width 0.3s cubic-bezier(0.25, 1, 0.4, 1);
    }

    /* Cart Item Deletion Animation */
    .cart-item-row {
        transition: max-height 0.4s cubic-bezier(0.25, 1, 0.4, 1), padding 0.4s cubic-bezier(0.25, 1, 0.4, 1), opacity 0.35s ease-out, transform 0.35s cubic-bezier(0.25, 1, 0.4, 1);
        overflow: hidden;
    }
    .cart-item-row.removing {
        max-height: 0 !important;
        padding-top: 0 !important;
        padding-bottom: 0 !important;
        opacity: 0 !important;
        transform: translateX(-40px);
    }

    /* Button Morphing Submit */
    #checkout-submit-btn {
        transition: width 0.4s cubic-bezier(0.34, 1.56, 0.64, 1), border-radius 0.4s ease, background-color 0.3s ease;
    }

    /* Member Login Required Modal Styling */
    #member-login-modal {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 999999;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.35s ease-out;
    }
    #member-login-modal.opacity-100 {
        opacity: 1;
        pointer-events: auto;
    }
    #member-login-sheet {
        background-color: #ffffff;
        border-radius: 28px;
        padding: 24px;
        max-width: 380px;
        width: 100%;
        margin-left: 16px;
        margin-right: 16px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        border: 1px solid rgba(245, 158, 11, 0.15);
        text-align: center;
        transform: scale(0.75);
        transition: transform 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .dark #member-login-sheet {
        background-color: #0b0f19;
        border-color: rgba(245, 158, 11, 0.2);
    }
    #member-login-modal.opacity-100 #member-login-sheet {
        transform: scale(1);
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
                            @if($discountEnabled)<span class="flex items-center gap-1 text-orange-600 dark:text-orange-400">🏷️ Diskon {{ $discountPercentage }}% Otomatis</span>@endif
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
        <div class="mb-8 space-y-5">
            <!-- Premium Location Indicator (TikTok Style) -->
            <div class="flex items-center gap-2.5 text-xs bg-gray-50 dark:bg-gray-800/40 p-3 rounded-2xl border border-gray-100 dark:border-gray-800/50 max-w-sm">
                <span class="text-xl">📍</span>
                <div>
                    <p class="text-[9px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Pengiriman Ke</p>
                    <button type="button" onclick="toggleCheckoutDrawer(true); goToStep('checkout');" class="font-extrabold text-gray-800 dark:text-gray-200 hover:text-amber-600 dark:hover:text-amber-500 transition flex items-center gap-1.5 mt-0.5 text-left">
                        <span id="selected-address-summary">Ambil di Outlet Mamitha (Sleman)</span>
                        <svg class="w-3.5 h-3.5 text-amber-600 dark:text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Main Heading (TikTok style) -->
            <h2 class="text-2xl md:text-3xl font-black text-gray-950 dark:text-white font-serif leading-tight">
                Mau makan roti hangat apa hari ini?
            </h2>

            <!-- Search Bar -->
            <div class="relative max-w-md">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </span>
                <input type="text" id="search-input" placeholder="Cari roti manis, cake, snack..." class="w-full pl-11 pr-4 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 dark:focus:ring-amber-600 transition" onkeyup="filterProducts()">
            </div>

            <!-- Categories Horizontal Scroll -->
            <div class="relative flex gap-2 overflow-x-auto no-scrollbar pb-2 mask-linear" id="categories-container">
                <div id="category-active-bg" class="absolute bg-amber-600 rounded-full pointer-events-none z-0" style="left:0; top:0; width:0; height:0;"></div>
                <button onclick="filterCategory('all', this)" class="category-pill active z-10 whitespace-nowrap px-5 py-2.5 rounded-full text-xs font-semibold text-white transition-colors duration-250">
                    Semua Menu
                </button>
                @foreach($categories as $cat)
                <button onclick="filterCategory('{{ $cat->id }}', this)" class="category-pill z-10 whitespace-nowrap px-5 py-2.5 rounded-full text-xs font-semibold text-gray-700 dark:text-gray-300 hover:text-amber-700 dark:hover:text-amber-500 transition-colors duration-250">
                    {{ $cat->name }}
                </button>
                @endforeach
            </div>
        </div>

        <!-- Product List (Pizza Mojo Style Cards) -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="product-grid">
            @foreach($products as $product)
            <div class="product-card bg-white dark:bg-gray-800 rounded-3xl border border-amber-100/40 dark:border-gray-700/40 overflow-hidden shadow-sm hover:shadow-md transition-all duration-300 flex flex-col justify-between cursor-pointer" data-category="{{ $product->category_id }}" data-name="{{ strtolower($product->name) }}" onclick="openVariantModal({{ $product->id }})">
                <div>
                    <!-- Product Image Section -->
                    <div class="relative aspect-[4/3] bg-amber-50/50 dark:bg-gray-900/50 overflow-hidden group product-image-clickable">
                        @if($product->image)
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover transition duration-500 group-hover:scale-105 {{ $product->stock <= 0 ? 'grayscale opacity-50' : '' }}">
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
                            @if($product->is_featured)
                            <span class="bg-amber-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider shadow-sm">
                                🔥 Best Seller
                            </span>
                            @endif
                            @if($product->stock > 0 && $discountEnabled)
                            <span class="bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider">
                                {{ $discountPercentage }}% OFF
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
                        <!-- Hover hint for available products -->
                        @if($product->stock > 0)
                        <div class="absolute bottom-3 inset-x-3 flex justify-center opacity-0 group-hover:opacity-100 transition-all duration-200 pointer-events-none">
                            <span class="bg-black/70 backdrop-blur-sm text-white text-[10px] font-bold px-3 py-1 rounded-full">
                                {{ $product->activeVariants->isNotEmpty() ? '✨ Pilih Varian' : '+ Tambah ke Keranjang' }}
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
                        @if($product->activeVariants->isNotEmpty())
                        {{-- Hidden select kept for JS compatibility --}}
                        <select class="variant-select" style="display: none !important;" data-product-id="{{ $product->id }}" onchange="onVariantChange({{ $product->id }}, this)">
                            <option value="">Pilih Varian</option>
                            @foreach($product->activeVariants as $v)
                            <option value="{{ $v->id }}" data-adjustment="{{ $v->price_adjustment }}" data-stock="{{ $v->stock }}">{{ $v->name }}@if($v->price_adjustment > 0) (+Rp {{ number_format($v->price_adjustment, 0, ',', '.') }}) @endif</option>
                            @endforeach
                        </select>
                        {{-- Visual Variant Indicator --}}
                        <div class="mt-3 flex items-center gap-1.5">
                            <span class="text-[10px] font-bold text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/30 px-2 py-0.5 rounded-full">✨ {{ $product->activeVariants->count() }} Varian</span>
                            <span id="selected-variant-badge-{{ $product->id }}" class="text-[10px] font-semibold text-gray-500 dark:text-gray-400 hidden"></span>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Footer Card -->
                <div class="p-5 pt-0 flex items-center justify-between mt-auto">
                    <!-- Price block -->
                    <div>
                        <span class="text-xs text-gray-400 line-through">Rp {{ number_format($product->price * (1 + $discountPercentage/100), 0, ',', '.') }}</span>
                        <p class="text-amber-800 dark:text-amber-400 font-extrabold text-lg -mt-1">
                            Rp {{ number_format($product->price, 0, ',', '.') }}
                        </p>
                    </div>

                    <!-- Interactive Add Button -->
                    @if($product->stock > 0)
                    <div class="relative w-28 h-10 flex items-center justify-end" id="btn-container-{{ $product->id }}">
                        <!-- Add Button -->
                        <button onclick="event.stopPropagation(); openVariantModal({{ $product->id }})" class="add-btn px-4 py-2 bg-gray-900 dark:bg-gray-100 hover:bg-amber-700 dark:hover:bg-amber-500 text-white dark:text-gray-900 text-xs font-extrabold rounded-full shadow-sm hover:shadow-md transition duration-200">
                            + ADD
                        </button>
                        
                        <!-- Quantity Selector Controls (Hidden initially) -->
                        <div class="qty-controls absolute inset-0 bg-gray-900 dark:bg-gray-100 rounded-full flex items-center justify-between px-2 text-white dark:text-gray-900 scale-0 opacity-0 pointer-events-none transition duration-200">
                            <button onclick="event.stopPropagation(); decrementQty({{ $product->id }})" class="w-8 h-8 rounded-full flex items-center justify-center hover:bg-gray-800 dark:hover:bg-gray-200 font-extrabold text-sm">-</button>
                            <span class="font-extrabold text-sm" id="card-qty-{{ $product->id }}">0</span>
                            <button onclick="event.stopPropagation(); incrementQty({{ $product->id }})" class="w-8 h-8 rounded-full flex items-center justify-center hover:bg-gray-800 dark:hover:bg-gray-200 font-extrabold text-sm">+</button>
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
    <div id="floating-cart" class="fixed bottom-20 md:bottom-6 inset-x-4 max-w-lg mx-auto bg-gray-900 dark:bg-gray-100 text-white dark:text-gray-900 rounded-3xl shadow-2xl p-4 flex items-center justify-between z-40 transform translate-y-32 opacity-0 transition-all duration-300">
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

    <!-- ================================================================ -->
    <!-- Variant Selection Modal (Bottom Sheet) -->
    <!-- ================================================================ -->
    <div id="variant-modal-overlay" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[60] opacity-0 pointer-events-none flex items-end justify-center">
        <div id="variant-modal-sheet" class="w-full max-w-lg bg-white dark:bg-gray-900 rounded-t-3xl shadow-2xl transform translate-y-full flex flex-col max-h-[90vh]">
            
            <!-- Drag Handle -->
            <div class="flex justify-center pt-3 pb-1 flex-shrink-0">
                <div class="w-10 h-1 bg-gray-300 dark:bg-gray-600 rounded-full"></div>
            </div>

            <!-- Close Button -->
            <div class="flex items-center justify-between px-5 pb-3 flex-shrink-0">
                <h2 class="text-base font-extrabold text-gray-800 dark:text-gray-100" id="modal-product-title">Pilih Varian</h2>
                <button onclick="closeVariantModal()" class="w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-500 hover:text-gray-800 dark:hover:text-gray-200 transition font-bold text-lg">&times;</button>
            </div>

            <!-- Scrollable Content -->
            <div class="overflow-y-auto flex-1">
                <!-- Product Preview Strip -->
                <div class="flex items-center gap-4 px-5 pb-4 border-b border-gray-100 dark:border-gray-800">
                    <div class="w-20 h-20 rounded-2xl overflow-hidden bg-amber-50 dark:bg-gray-800 flex-shrink-0">
                        <img id="modal-product-image" src="" alt="" class="w-full h-full object-cover">
                        <div id="modal-product-emoji" class="w-full h-full items-center justify-center text-4xl hidden">🍞</div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-gray-800 dark:text-gray-100 text-sm font-serif" id="modal-product-name"></p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 line-clamp-2" id="modal-product-desc"></p>
                        <div class="flex items-center gap-2 mt-1.5">
                            <p class="text-amber-700 dark:text-amber-400 font-extrabold text-sm" id="modal-product-price"></p>
                            <span id="modal-discount-badge" class="text-[10px] font-bold bg-red-500 text-white px-2 py-0.5 rounded-full hidden">{{ $discountPercentage }}% OFF</span>
                        </div>
                    </div>
                </div>

                <!-- Variant Chips Section -->
                <div id="modal-variants-section" class="px-5 py-4 hidden">
                    <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Pilih Varian</p>
                    <div id="modal-variant-chips" class="flex flex-wrap gap-2"></div>
                    <p class="text-xs text-amber-600 dark:text-amber-400 mt-2 font-medium" id="modal-variant-note"></p>
                </div>

                <!-- Per-item Notes -->
                <div id="modal-notes-section" class="px-5 py-3 border-t border-gray-100 dark:border-gray-800">
                    <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">📝 Catatan</p>
                    <textarea id="modal-item-note" rows="2" placeholder="Contoh: jangan terlalu manis, extra topping, dll" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl text-xs text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition resize-none" maxlength="200"></textarea>
                    <p class="text-[10px] text-gray-400 mt-1">Opsional — maks 200 karakter</p>
                </div>

                <!-- Quantity selector inside modal -->
                <div id="modal-qty-section" class="px-5 py-3 border-t border-gray-100 dark:border-gray-800">
                    <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Jumlah</p>
                    <div class="flex items-center gap-4">
                        <button onclick="modalDecQty()" id="modal-dec-btn" class="w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center font-extrabold text-lg text-gray-700 dark:text-gray-300 hover:bg-amber-100 dark:hover:bg-amber-900/30 transition">-</button>
                        <span id="modal-qty-display" class="font-extrabold text-xl text-gray-800 dark:text-gray-100 w-8 text-center">1</span>
                        <button onclick="modalIncQty()" id="modal-inc-btn" class="w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center font-extrabold text-lg text-gray-700 dark:text-gray-300 hover:bg-amber-100 dark:hover:bg-amber-900/30 transition">+</button>
                        <span class="text-xs text-gray-400" id="modal-stock-info"></span>
                    </div>
                </div>

                <!-- Reviews Section -->
                <div id="modal-reviews-section" class="px-5 py-4 border-t border-gray-100 dark:border-gray-800">
                    <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">💬 Ulasan Pelanggan</p>
                    <div id="modal-reviews-list" class="space-y-3 pb-2">
                        <!-- Populated by JS -->
                    </div>
                </div>
            </div>

            <!-- Footer Action -->
            <div id="modal-footer-action" class="px-5 py-4 border-t border-gray-100 dark:border-gray-800 bg-gray-50/70 dark:bg-gray-900/50 flex-shrink-0">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs text-gray-500 dark:text-gray-400">Total</span>
                    <span class="font-extrabold text-amber-700 dark:text-amber-400 text-base" id="modal-line-total">Rp 0</span>
                </div>
                <button id="modal-add-btn" onclick="confirmAddToCart()" class="w-full py-3.5 bg-gray-900 dark:bg-gray-100 hover:bg-amber-700 dark:hover:bg-amber-500 text-white dark:text-gray-900 font-extrabold text-sm rounded-2xl shadow-lg transition disabled:opacity-40 disabled:cursor-not-allowed">
                    + Tambah ke Keranjang
                </button>
            </div>
        </div>
    </div>

    <!-- Checkout Overlay Panel (Tiktok Checkout Style Drawer) -->
    <div id="checkout-overlay" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-50 opacity-0 pointer-events-none transition-all duration-300">
        <div class="absolute inset-y-0 right-0 max-w-lg w-full bg-white dark:bg-gray-900 shadow-2xl flex flex-col justify-between drawer-transition transform translate-x-full">
            
            <!-- Drawer Header with Tabs -->
            <div class="px-5 pt-4 pb-3 border-b border-gray-100 dark:border-gray-800 flex-shrink-0">
                <!-- Top row: back button + brand -->
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <button id="drawer-back-btn" onclick="handleDrawerBack()" class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-500 font-extrabold text-base transition">
                            ←
                        </button>
                        <h2 id="drawer-title" class="text-lg font-bold font-serif text-gray-800 dark:text-gray-100">Pesanan Anda</h2>
                    </div>
                    <span class="text-xs text-amber-700 dark:text-amber-400 font-bold bg-amber-50 dark:bg-amber-950/30 px-3 py-1 rounded-full">Mamitha Bakery</span>
                </div>

                <!-- Tab Navigation -->
                <div class="relative flex items-center bg-gray-100 dark:bg-gray-800 rounded-full p-1 gap-1" id="tab-container">
                    <div id="tab-active-bg" class="absolute top-1 bottom-1 bg-gradient-to-r from-amber-700 to-orange-700 rounded-full pointer-events-none z-0" style="left: 4px; width: 0;"></div>
                    <button id="tab-cart" onclick="goToStep('cart')" class="drawer-tab active z-10 flex-1 justify-center py-2 text-xs font-bold rounded-full">
                        🛒 Keranjang
                        <span class="tab-badge ml-1 px-1.5 py-0.5 rounded-full text-[10px]" id="tab-cart-badge">0</span>
                    </button>
                    <button id="tab-checkout" onclick="goToStep('checkout')" class="drawer-tab inactive z-10 flex-1 justify-center py-2 text-xs font-bold rounded-full">
                        📋 Checkout
                    </button>
                </div>

                <!-- Step Indicator Dots -->
                <div class="flex justify-center mt-3">
                    <div class="step-indicator">
                        <div id="step-dot-cart" class="step-dot active"></div>
                        <div class="step-connector"></div>
                        <div id="step-dot-checkout" class="step-dot inactive"></div>
                    </div>
                </div>
            </div>

            <!-- Drawer Body -->
            <div class="drawer-body-container">
                <div id="drawer-steps-slider" style="transform: translateX(0%);">
                    <!-- Step 1: Keranjang Belanja -->
                    <div id="drawer-step-cart" class="drawer-slider-step">
                        <div class="space-y-3">
                            <h3 class="font-bold text-gray-700 dark:text-gray-300 text-sm tracking-wide uppercase">Daftar Belanjaan</h3>
                            <div id="cart-items-list" class="space-y-1">
                                <p class="text-center text-gray-400 text-sm py-4">Belum ada item dipilih</p>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Form Checkout -->
                    <div id="drawer-step-checkout" class="drawer-slider-step">
                    <form id="orderForm" method="POST" action="{{ route('order.store') }}">
                        @csrf
                        <!-- Dynamic Hidden Inputs Container -->
                        <div id="hidden-cart-inputs"></div>

                        <!-- Personal Information -->
                        <div class="space-y-4">
                            <h3 class="font-bold text-gray-700 dark:text-gray-300 text-sm tracking-wide uppercase">Data Diri</h3>
                            @php
                                $isRegisteredMember = auth()->check() && auth()->user()->role === 'customer';
                                $customerPhone = auth()->check() && auth()->user()->customer ? auth()->user()->customer->phone : '';
                            @endphp
                            <div>
                                <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Nama Lengkap</label>
                                <input type="text" name="name" required placeholder="Masukkan nama Anda" value="{{ auth()->user()->name ?? '' }}" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 dark:text-white transition">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Nomor WhatsApp</label>
                                <input type="tel" name="phone" required placeholder="Contoh: 08123456789" value="{{ $customerPhone }}" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 dark:text-white transition">
                            </div>
                            
                            <!-- Become a Member Option -->
                            <div class="bg-gradient-to-r from-amber-500/10 to-orange-500/10 dark:from-amber-950/20 dark:to-orange-950/20 rounded-2xl p-4 border border-amber-500/20 dark:border-amber-900/30 mt-3 flex items-start gap-3 select-none animate-bounce-slow" style="animation-duration: 4s;">
                                <input type="checkbox" name="is_member" id="is_member" value="1" 
                                    class="w-5 h-5 mt-0.5 rounded-lg text-amber-600 focus:ring-amber-500 border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 transition {{ $isRegisteredMember ? 'cursor-not-allowed opacity-80' : 'cursor-pointer' }}"
                                    {{ $isRegisteredMember ? 'checked onclick=return(false);' : '' }}>
                                <label for="is_member" class="cursor-pointer">
                                    <span class="block text-xs font-black text-amber-800 dark:text-amber-400 uppercase tracking-wide">Gabung Member Mamitha</span>
                                    <span class="block text-[11px] text-gray-500 dark:text-gray-400 mt-0.5 leading-relaxed">Daftar sekarang untuk membuka Rank Gold, prioritas baking cepat, dan voucher diskon 10%!</span>
                                </label>
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
                                <input type="text" id="map-search" placeholder="Cari alamat di peta..." class="w-full pl-4 pr-10 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 dark:text-white transition" onkeydown="if(event.key==='Enter'){ searchAddress(); event.preventDefault(); }">
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
                                <div id="map-container" style="height: 200px;"></div>
                            </div>

                            <!-- Lat/Lng Hidden Inputs -->
                            <input type="hidden" name="latitude" id="latitude">
                            <input type="hidden" name="longitude" id="longitude">

                            <div>
                                <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Alamat Lengkap Pengiriman</label>
                                <textarea name="address" id="address-text" rows="3" placeholder="Tuliskan nama jalan, blok, nomor rumah..." class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 dark:text-white transition">{{ auth()->check() && auth()->user()->customer ? auth()->user()->customer->address : '' }}</textarea>
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
                            <input type="date" name="pickup_date" id="pickup-date-input" value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 dark:text-white transition">
                        </div>

                        <!-- Order Notes -->
                        <div class="space-y-4 mt-6">
                            <h3 class="font-bold text-gray-700 dark:text-gray-300 text-sm tracking-wide uppercase">Catatan Tambahan</h3>
                            <textarea name="notes" rows="2" placeholder="Catatan opsional (contoh: jangan terlalu manis, dll)" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 dark:text-white transition"></textarea>
                        </div>

                        <!-- Payment Methods (Tiktok Checkout Style) -->
                        <div class="space-y-4 mt-6">
                            <h3 class="font-bold text-gray-700 dark:text-gray-300 text-sm tracking-wide uppercase">Metode Pembayaran</h3>
                            <div class="space-y-2">
                                @auth
                                    @if(auth()->user()->customer && auth()->user()->customer->paymentMethods->isNotEmpty())
                                        @foreach(auth()->user()->customer->paymentMethods as $pm)
                                            <!-- Saved Payment Method Option -->
                                            <label class="border-2 border-gray-100 dark:border-gray-800 rounded-2xl p-4 cursor-pointer hover:border-amber-500 dark:hover:border-amber-600 transition flex items-center justify-between has-[:checked]:border-amber-600 has-[:checked]:bg-amber-50/35">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-10 h-10 bg-amber-50 dark:bg-amber-950/20 rounded-xl flex items-center justify-center text-lg">
                                                        @if($pm->type === 'credit_card') 💳 
                                                        @elseif($pm->type === 'e_wallet') 📱
                                                        @else 🏦 @endif
                                                    </div>
                                                    <div>
                                                        <p class="font-bold text-xs text-gray-800 dark:text-gray-200">
                                                            {{ $pm->provider }} - {{ $pm->account_name }}
                                                            @if($pm->is_default)
                                                                <span class="ml-1 text-[9px] text-[#ff6310] font-extrabold bg-orange-500/10 px-2 py-0.5 rounded-full select-none">Default</span>
                                                            @endif
                                                        </p>
                                                        <p class="text-[10px] text-gray-400">
                                                            @if($pm->type === 'credit_card')
                                                                **** **** **** {{ substr($pm->account_number, -4) }}
                                                            @else
                                                                {{ $pm->account_number }}
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>
                                                <input type="radio" name="payment_method" value="saved_{{ $pm->id }}" {{ $pm->is_default ? 'checked' : '' }} class="text-amber-600 focus:ring-amber-500">
                                            </label>
                                        @endforeach
                                    @endif
                                @endauth

                                <!-- Transfer Bank -->
                                <label class="border-2 border-gray-100 dark:border-gray-800 rounded-2xl p-4 cursor-pointer hover:border-amber-500 dark:hover:border-amber-600 transition flex items-center justify-between has-[:checked]:border-amber-600 has-[:checked]:bg-amber-50/35">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-amber-50 dark:bg-amber-950/20 rounded-xl flex items-center justify-center text-lg">🏦</div>
                                        <div>
                                            <p class="font-bold text-xs text-gray-800 dark:text-gray-200">Transfer Bank / QRIS</p>
                                            <p class="text-[10px] text-gray-400">Verifikasi otomatis & aman</p>
                                        </div>
                                    </div>
                                    <input type="radio" name="payment_method" value="transfer" {{ (!auth()->check() || !auth()->user()->customer || auth()->user()->customer->paymentMethods->where('is_default', true)->isEmpty()) ? 'checked' : '' }} class="text-amber-600 focus:ring-amber-500">
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
                </div>
            </div>

            <!-- Drawer Footer (Order Summary & Action Buttons) -->
            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800 space-y-4 bg-gray-50/70 dark:bg-gray-900/50 flex-shrink-0">
                <div class="space-y-1.5 text-xs text-gray-500 dark:text-gray-400">
                    <div class="flex justify-between">
                        <span>Harga Roti (Subtotal)</span>
                        <span id="summary-subtotal" class="font-medium text-gray-800 dark:text-gray-200">Rp 0</span>
                    </div>
                    <div class="flex justify-between text-green-600 dark:text-green-400" id="discount-row" @if(!$discountEnabled) style="display:none" @endif>
                        <span>Diskon Promo (<span id="discount-pct-label">{{ $discountPercentage }}</span>% OFF)</span>
                        <span id="summary-discount" class="font-bold">-Rp 0</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Biaya Pengiriman</span>
                        <span id="summary-shipping" class="font-medium text-gray-800 dark:text-gray-200">Rp 0</span>
                    </div>
                    <div class="flex justify-between text-base font-black text-gray-800 dark:text-gray-100 pt-2 border-t border-gray-200/50 dark:border-gray-700/50">
                        <span>Total Bayar</span>
                        <span id="summary-total" class="text-amber-800 dark:text-amber-400 text-lg font-black">Rp 0</span>
                    </div>
                </div>

                <!-- Footer Step Buttons -->
                <button type="button" id="cart-next-btn" onclick="goToStep('checkout')" class="w-full py-4 bg-gray-900 dark:bg-gray-100 hover:bg-amber-700 dark:hover:bg-amber-600 text-white dark:text-gray-900 font-extrabold text-sm rounded-2xl shadow-xl transition-all duration-200">
                    Lanjut ke Checkout →
                </button>
                <div id="checkout-submit-btn" class="hidden w-full h-16 bg-gray-100/85 dark:bg-gray-800/85 rounded-2xl flex items-center justify-center overflow-hidden border border-gray-200/80 dark:border-gray-700/80 select-none relative z-40 transition-all duration-300 shadow-inner">
                    <!-- Sliding Track background (filled when swiped) -->
                    <div id="swipe-track" class="absolute left-0 top-0 bottom-0 bg-gradient-to-r from-amber-600 to-orange-500 rounded-l-2xl pointer-events-none transition-all duration-75" style="width: 56px;"></div>
                    
                    <!-- Swipe Text -->
                    <span id="swipe-text" class="absolute z-10 text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest pointer-events-none transition-opacity duration-200">Geser untuk Pesan</span>
                    
                    <!-- Sliding Handle -->
                    <div id="swipe-handle" class="absolute left-1 w-14 h-14 bg-gradient-to-tr from-amber-600 to-orange-500 rounded-xl shadow-lg flex items-center justify-center cursor-grab active:cursor-grabbing z-20 transition-all duration-75" style="left: 4px;">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Member Login Required Prompt Modal -->
<div id="member-login-modal" class="opacity-0 pointer-events-none">
    <div id="member-login-sheet">
        <span class="text-3xl block mb-3">🔒</span>
        <h3 class="text-lg font-black text-gray-900 dark:text-white font-serif mb-2">Login Diperlukan</h3>
        <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed mb-6">
            Pendaftaran member hanya tersedia bagi pelanggan yang telah masuk. Silakan login untuk menikmati diskon member 10% dan keuntungan lainnya!
        </p>
        <div class="flex gap-3">
            <button type="button" onclick="closeMemberLoginPrompt()" class="flex-1 py-3 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 font-extrabold text-xs rounded-2xl transition">
                Batal
            </button>
            <a href="{{ route('login') }}?redirect={{ urlencode(url()->current()) }}" class="flex-1 py-3 bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-700 hover:to-orange-700 text-white font-extrabold text-xs rounded-2xl shadow-md text-center flex items-center justify-center">
                Login Sekarang
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Leaflet.js Map Library -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    // Delivery Fee & Discount Settings passed from backend
    const deliveryFeeEnabled = {{ $deliveryFeeEnabled ? 'true' : 'false' }};
    const deliveryFeeAmount = {{ $deliveryFeeAmount }};
    const discountEnabled = {{ $discountEnabled ? 'true' : 'false' }};
    const discountPercentage = {{ $discountPercentage }};
    const isLoggedIn = {{ auth()->check() ? 'true' : 'false' }};

    // Cart State
    let cart = {};
    try {
        const storedCart = localStorage.getItem('mamitha_cart');
        if (storedCart) {
            cart = JSON.parse(storedCart);
        }
    } catch (e) {
        console.error('Error loading cart from localStorage:', e);
    }
    let products = {};

    // Helper function to escape HTML
    function escapeHtml(text) {
        if (!text) return '';
        return text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    // Cascade Staggered Entrance Animation for product cards
    function staggerProductCards() {
        let delay = 0;
        document.querySelectorAll('.product-card').forEach(card => {
            if (card.style.display !== 'none') {
                card.classList.remove('product-card-cascade');
                // force reflow
                void card.offsetWidth;
                card.style.animationDelay = delay + 'ms';
                card.classList.add('product-card-cascade');
                delay += 40; // 40ms stagger spacing
            } else {
                card.classList.remove('product-card-cascade');
                card.style.animationDelay = '';
            }
        });
    }

    // Active Category Pill indicator positioning
    function moveCategoryActiveBg(btn) {
        const activeBg = document.getElementById('category-active-bg');
        if (!activeBg) return;
        activeBg.style.left = btn.offsetLeft + 'px';
        activeBg.style.top = btn.offsetTop + 'px';
        activeBg.style.width = btn.offsetWidth + 'px';
        activeBg.style.height = btn.offsetHeight + 'px';
    }

    // Tab indicator positioning inside checkout drawer
    function moveTabActiveBg(step) {
        const activeBg = document.getElementById('tab-active-bg');
        const activeTab = document.getElementById('tab-' + step);
        if (!activeBg || !activeTab) return;
        activeBg.style.left = activeTab.offsetLeft + 'px';
        activeBg.style.width = activeTab.offsetWidth + 'px';
    }

    // Particle Burst Animation (Canvas-less, pure HTML elements)
    function createParticleBurst(x, y) {
        const colors = ['#f5a623', '#d48b1a', '#ffbe5c', '#ff8700', '#ffffff'];
        const particleCount = 20;
        for (let i = 0; i < particleCount; i++) {
            const particle = document.createElement('span');
            particle.textContent = Math.random() > 0.45 ? '★' : '✨';
            particle.style.position = 'fixed';
            particle.style.left = x + 'px';
            particle.style.top = y + 'px';
            particle.style.color = colors[Math.floor(Math.random() * colors.length)];
            particle.style.fontSize = (Math.floor(Math.random() * 12) + 12) + 'px';
            particle.style.pointerEvents = 'none';
            particle.style.zIndex = '9999';
            particle.style.transition = 'transform 0.8s cubic-bezier(0.1, 0.8, 0.25, 1), opacity 0.8s ease';
            document.body.appendChild(particle);

            const angle = Math.random() * Math.PI * 2;
            const velocity = Math.random() * 120 + 60;
            const tx = Math.cos(angle) * velocity;
            const ty = Math.sin(angle) * velocity - 25; // upward float
            
            requestAnimationFrame(function() {
                particle.style.transform = 'translate(' + tx + 'px, ' + ty + 'px) rotate(' + (Math.random() * 360) + 'deg) scale(0.2)';
                particle.style.opacity = '0';
            });

            setTimeout(function() {
                particle.remove();
            }, 800);
        }
    }

    // Flying Mini Element animation to Cart
    function animateFlyToCart(startElement, endElement, imageUrl) {
        if (!startElement || !endElement) return;

        const startRect = startElement.getBoundingClientRect();
        const endRect = endElement.getBoundingClientRect();

        const flyer = document.createElement('div');
        flyer.style.position = 'fixed';
        flyer.style.zIndex = '9999';
        flyer.style.left = (startRect.left + startRect.width / 2 - 24) + 'px';
        flyer.style.top = (startRect.top + startRect.height / 2 - 24) + 'px';
        flyer.style.width = '48px';
        flyer.style.height = '48px';
        flyer.style.borderRadius = '50%';
        flyer.style.border = '2px solid #b45309';
        flyer.style.boxShadow = '0 8px 16px rgba(180, 83, 9, 0.35)';
        flyer.style.backgroundColor = '#ffffff';
        flyer.style.overflow = 'hidden';
        flyer.style.pointerEvents = 'none';
        
        if (imageUrl) {
            const img = document.createElement('img');
            img.src = imageUrl;
            img.style.width = '100%';
            img.style.height = '100%';
            img.style.objectFit = 'cover';
            flyer.appendChild(img);
        } else {
            flyer.textContent = '🍞';
            flyer.style.display = 'flex';
            flyer.style.alignItems = 'center';
            flyer.style.justifyContent = 'center';
            flyer.style.fontSize = '24px';
        }

        document.body.appendChild(flyer);

        flyer.style.transition = 'left 0.8s cubic-bezier(0.25, 1, 0.5, 1), top 0.8s cubic-bezier(0.5, -0.5, 0.75, 1), transform 0.8s ease, opacity 0.8s ease';
        
        requestAnimationFrame(function() {
            flyer.style.left = (endRect.left + endRect.width / 2 - 12) + 'px';
            flyer.style.top = (endRect.top + endRect.height / 2 - 12) + 'px';
            flyer.style.transform = 'scale(0.3) rotate(360deg)';
            flyer.style.opacity = '0.4';
        });

        setTimeout(function() {
            flyer.remove();
            
            // Trigger bouncy effect on the end element
            endElement.classList.remove('qty-bounce');
            void endElement.offsetWidth; // force reflow
            endElement.classList.add('qty-bounce');
            setTimeout(() => endElement.classList.remove('qty-bounce'), 450);
        }, 800);
    }

    // Swipe to Pay dragging functionality
    function initSwipeToPay() {
        const container = document.getElementById('checkout-submit-btn');
        const handle = document.getElementById('swipe-handle');
        const track = document.getElementById('swipe-track');
        const text = document.getElementById('swipe-text');
        if (!container || !handle || !track) return;

        let isDragging = false;
        let startX = 0;
        let maxDistance = 0;

        function onStart(e) {
            if (handle.disabled) return;
            isDragging = true;
            startX = (e.type === 'touchstart') ? e.touches[0].clientX : e.clientX;
            
            // Calculate max distance dynamically in case container width changed
            maxDistance = container.clientWidth - handle.clientWidth - 8; // 8px for margins
            
            handle.style.transition = 'none';
            track.style.transition = 'none';
        }

        function onMove(e) {
            if (!isDragging) return;
            const currentX = (e.type === 'touchmove') ? e.touches[0].clientX : e.clientX;
            let deltaX = currentX - startX;
            if (deltaX < 0) deltaX = 0;
            if (deltaX > maxDistance) deltaX = maxDistance;

            handle.style.left = (deltaX + 4) + 'px';
            track.style.width = (deltaX + 56) + 'px'; // include handle width
            
            const pct = deltaX / maxDistance;
            text.style.opacity = 1 - pct * 1.5;
        }

        function onEnd() {
            if (!isDragging) return;
            isDragging = false;
            const currentLeft = parseInt(handle.style.left) - 4;
            
            if (currentLeft >= maxDistance * 0.9) {
                // Success swipe
                handle.style.transition = 'left 0.15s ease-out';
                track.style.transition = 'width 0.15s ease-out';
                handle.style.left = (maxDistance + 4) + 'px';
                track.style.width = '100%';
                
                triggerCheckoutMorph();
            } else {
                // Snap back
                handle.style.transition = 'left 0.25s cubic-bezier(0.25, 1, 0.5, 1)';
                track.style.transition = 'width 0.25s cubic-bezier(0.25, 1, 0.5, 1)';
                handle.style.left = '4px';
                track.style.width = '56px';
                text.style.opacity = '1';
            }
        }

        handle.addEventListener('mousedown', onStart);
        handle.addEventListener('touchstart', onStart, { passive: true });

        window.addEventListener('mousemove', onMove);
        window.addEventListener('touchmove', onMove, { passive: false });

        window.addEventListener('mouseup', onEnd);
        window.addEventListener('touchend', onEnd);
    }

    // Morph "Swipe to Pay" slider into loading circle and then checkmark
    function triggerCheckoutMorph() {
        const form = document.getElementById('orderForm');
        if (!form.reportValidity()) {
            // Form validation failed, bounce slider back
            const handle = document.getElementById('swipe-handle');
            const track = document.getElementById('swipe-track');
            const text = document.getElementById('swipe-text');
            
            handle.style.transition = 'left 0.25s ease-out';
            track.style.transition = 'width 0.25s ease-out';
            handle.style.left = '4px';
            track.style.width = '56px';
            text.style.opacity = '1';
            return;
        }

        try {
            localStorage.removeItem('mamitha_cart');
        } catch (e) {}

        const container = document.getElementById('checkout-submit-btn');
        const handle = document.getElementById('swipe-handle');
        const track = document.getElementById('swipe-track');
        const text = document.getElementById('swipe-text');
        
        handle.disabled = true;
        text.style.opacity = '0';
        handle.style.opacity = '0';
        handle.style.pointerEvents = 'none';

        // Morph container
        container.style.transition = 'width 0.4s cubic-bezier(0.34, 1.56, 0.64, 1), border-radius 0.4s ease, border-color 0.3s ease';
        container.style.width = '64px';
        container.style.borderRadius = '9999px';
        
        let spinner = document.getElementById('swipe-spinner');
        if (!spinner) {
            spinner = document.createElement('div');
            spinner.id = 'swipe-spinner';
            spinner.className = 'absolute inset-0 flex items-center justify-center transition-opacity duration-200';
            spinner.innerHTML = `
                <svg class="animate-spin h-6 w-6 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            `;
            container.appendChild(spinner);
        }
        spinner.style.opacity = '1';

        setTimeout(function() {
            spinner.style.opacity = '0';
            
            setTimeout(function() {
                container.style.backgroundColor = '#16a34a'; // green-600
                container.style.borderColor = '#16a34a';
                track.style.backgroundColor = '#16a34a';
                
                let successCheck = document.getElementById('swipe-success');
                if (!successCheck) {
                    successCheck = document.createElement('div');
                    successCheck.id = 'swipe-success';
                    successCheck.className = 'absolute inset-0 flex items-center justify-center transition-opacity duration-200';
                    successCheck.innerHTML = `
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    `;
                    container.appendChild(successCheck);
                }
                successCheck.style.opacity = '1';
                container.classList.add('animate-ping-once');
                
                setTimeout(function() {
                    form.submit();
                }, 800);
            }, 150);
        }, 1500);
    }

    // Get composite cart key: productId-variantId (variantId = 0 for no variant)
    function cartKey(productId, variantId) {
        return productId + '-' + (variantId || 0);
    }

    // Get variant select for a product
    function getVariantSelect(productId) {
        return document.querySelector('.variant-select[data-product-id="' + productId + '"]');
    }

    // Get currently selected variant for a product
    function getSelectedVariant(productId) {
        var select = getVariantSelect(productId);
        if (!select || !select.value) return null;
        return {
            id: parseInt(select.value),
            name: select.options[select.selectedIndex].text.split('(+')[0].trim(),
            adjustment: parseFloat(select.options[select.selectedIndex].dataset.adjustment || 0),
            stock: parseInt(select.options[select.selectedIndex].dataset.stock || 0)
        };
    }

    // Load initial products list details into JS object
    @foreach($products as $product)
    products[{{ $product->id }}] = {
        name: '{{ addslashes(str_replace(["\r", "\n"], " ", $product->name)) }}',
        price: {{ $product->price }},
        stock: {{ $product->stock ?? 0 }},
        hasVariants: {{ $product->activeVariants->isNotEmpty() ? 'true' : 'false' }},
        imageUrl: '{{ $product->image ? $product->image_url : '' }}',
        description: '{{ addslashes(str_replace(["\r", "\n"], " ", $product->description ?? 'Roti hangat dan empuk yang dibuat fresh hari ini.')) }}',
        variants: [
            @foreach($product->activeVariants as $v)
            { id: {{ $v->id }}, name: '{{ addslashes($v->name) }}', price_adjustment: {{ $v->price_adjustment }}, stock: {{ $v->stock }} },
            @endforeach
        ],
        reviews: [
            @foreach($product->reviews as $rev)
            { name: '{{ addslashes($rev->name) }}', rating: {{ $rev->rating }}, comment: '{{ addslashes(str_replace(["\r", "\n"], " ", $rev->comment)) }}', date: '{{ $rev->created_at->format('d M Y') }}' },
            @endforeach
        ]
    };
    @endforeach

    // =========================================================================
    // Variant Selection Modal Logic
    // =========================================================================
    let modalState = {
        productId: null,
        selectedVariantId: null,
        selectedVariantName: null,
        selectedVariantPrice: 0,
        selectedVariantStock: 0,
        qty: 1
    };

    // HTML Escape Helper
    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
    }

    // Stars Generator Helper
    function generateStarsHtml(rating) {
        let html = '';
        for (let i = 1; i <= 5; i++) {
            if (i <= rating) {
                html += '<span class="text-amber-500">★</span>';
            } else {
                html += '<span class="text-gray-300 dark:text-gray-600">★</span>';
            }
        }
        return html;
    }

    function openVariantModal(productId) {
        var prod = products[productId];
        if (!prod) return;

        var isOutOfStock = (prod.stock <= 0);

        if (isOutOfStock) {
            document.getElementById('modal-notes-section').classList.add('hidden');
            document.getElementById('modal-qty-section').classList.add('hidden');
            document.getElementById('modal-footer-action').classList.add('hidden');
            document.getElementById('modal-variants-section').classList.add('hidden');
        } else {
            document.getElementById('modal-notes-section').classList.remove('hidden');
            document.getElementById('modal-qty-section').classList.remove('hidden');
            document.getElementById('modal-footer-action').classList.remove('hidden');
        }

        // Reset modal state
        modalState.productId = productId;
        modalState.selectedVariantId = null;
        modalState.selectedVariantName = null;
        modalState.selectedVariantPrice = prod.price;
        modalState.selectedVariantStock = prod.stock;
        modalState.qty = 1;

        // Populate product info
        document.getElementById('modal-product-title').textContent = isOutOfStock ? 'Detail Roti (Stok Habis)' : (prod.hasVariants ? 'Pilih Varian' : 'Tambah ke Keranjang');
        document.getElementById('modal-product-name').textContent = prod.name;
        document.getElementById('modal-product-desc').textContent = prod.description;
        document.getElementById('modal-product-price').textContent = 'Rp ' + prod.price.toLocaleString('id-ID');

        // Product image
        var imgEl = document.getElementById('modal-product-image');
        var emojiEl = document.getElementById('modal-product-emoji');
        if (prod.imageUrl) {
            imgEl.src = prod.imageUrl;
            imgEl.classList.remove('hidden');
            emojiEl.classList.add('hidden');
        } else {
            imgEl.classList.add('hidden');
            emojiEl.classList.remove('hidden');
            emojiEl.classList.add('flex');
        }

        // Discount badge
        var discBadge = document.getElementById('modal-discount-badge');
        if (discountEnabled && prod.stock > 0) {
            discBadge.classList.remove('hidden');
        } else {
            discBadge.classList.add('hidden');
        }

        // Variant chips
        var variantsSection = document.getElementById('modal-variants-section');
        var chipsContainer = document.getElementById('modal-variant-chips');
        chipsContainer.innerHTML = '';

        if (!isOutOfStock && prod.hasVariants && prod.variants.length > 0) {
            variantsSection.classList.remove('hidden');
            prod.variants.forEach(function(v) {
                var chip = document.createElement('button');
                chip.type = 'button';
                chip.className = 'variant-chip' + (v.stock <= 0 ? ' out-of-stock' : '');
                var priceLabel = v.price_adjustment > 0 ? ' (+Rp ' + v.price_adjustment.toLocaleString('id-ID') + ')' : (v.price_adjustment < 0 ? ' (-Rp ' + Math.abs(v.price_adjustment).toLocaleString('id-ID') + ')' : '');
                chip.innerHTML = v.name + '<span class="font-normal text-[10px] ml-1 opacity-75">' + priceLabel + '</span>';
                if (v.stock <= 0) {
                    chip.setAttribute('disabled', 'disabled');
                    chip.title = 'Stok habis';
                } else {
                    chip.onclick = function() { selectVariant(v.id, v.name, v.price_adjustment, v.stock, this); };
                }
                chipsContainer.appendChild(chip);
            });
            var noteEl = document.getElementById('modal-variant-note');
            if (noteEl) noteEl.textContent = 'Wajib pilih varian sebelum menambahkan ke keranjang';
            // Disable add button until variant selected
            document.getElementById('modal-add-btn').disabled = true;
        } else {
            variantsSection.classList.add('hidden');
            document.getElementById('modal-add-btn').disabled = isOutOfStock;
        }

        // Stock info
        document.getElementById('modal-stock-info').textContent = 'Stok: ' + prod.stock;
        document.getElementById('modal-qty-display').textContent = '1';
        updateModalTotal();

        // Populate customer reviews dynamically
        var reviewsContainer = document.getElementById('modal-reviews-list');
        if (reviewsContainer) {
            reviewsContainer.innerHTML = '';
            if (prod.reviews && prod.reviews.length > 0) {
                prod.reviews.forEach(function(r) {
                    var revDiv = document.createElement('div');
                    revDiv.className = 'bg-gray-50 dark:bg-gray-800/40 rounded-2xl p-3 border border-amber-50/20 dark:border-gray-700/20';
                    revDiv.innerHTML = `
                        <div class="flex items-center justify-between">
                            <p class="text-xs font-extrabold text-gray-800 dark:text-gray-200">${escapeHtml(r.name)}</p>
                            <span class="text-[10px] text-gray-400 font-medium">${r.date}</span>
                        </div>
                        <div class="flex items-center mt-1 text-xs">
                            ${generateStarsHtml(r.rating)}
                        </div>
                        <p class="text-xs text-gray-600 dark:text-gray-300 mt-1.5 leading-relaxed italic">"${escapeHtml(r.comment)}"</p>
                    `;
                    reviewsContainer.appendChild(revDiv);
                });
            } else {
                reviewsContainer.innerHTML = '<p class="text-xs text-gray-400 dark:text-gray-500 italic py-2 text-center">Belum ada ulasan untuk produk ini.</p>';
            }
        }

        // Clear or load existing notes
        var noteInput = document.getElementById('modal-item-note');
        if (noteInput) {
            var existingKey = cartKey(productId, 0);
            if (!prod.hasVariants && cart[existingKey]) {
                noteInput.value = cart[existingKey].note || '';
            } else {
                noteInput.value = '';
            }
        }

        // Hide floating cart bar so it doesn't overlap modal
        var floatingCart = document.getElementById('floating-cart');
        if (floatingCart) floatingCart.style.display = 'none';

        var whatsappFloat = document.getElementById('whatsapp-float');
        if (whatsappFloat) {
            whatsappFloat.classList.remove('bottom-44');
            whatsappFloat.classList.add('bottom-24');
        }

        // Show modal
        var overlay = document.getElementById('variant-modal-overlay');
        var sheet = document.getElementById('variant-modal-sheet');
        overlay.classList.remove('opacity-0', 'pointer-events-none');
        overlay.classList.add('opacity-100');
        sheet.classList.remove('translate-y-full');
        sheet.classList.add('translate-y-0');
        document.body.style.overflow = 'hidden';
    }

    function closeVariantModal() {
        var overlay = document.getElementById('variant-modal-overlay');
        var sheet = document.getElementById('variant-modal-sheet');
        overlay.classList.add('opacity-0', 'pointer-events-none');
        overlay.classList.remove('opacity-100');
        sheet.classList.add('translate-y-full');
        sheet.classList.remove('translate-y-0');
        document.body.style.overflow = '';

        // Restore floating cart bar if cart has items
        var floatingCart = document.getElementById('floating-cart');
        if (floatingCart) {
            floatingCart.style.display = '';
        }

        // Restore WhatsApp button position if cart has items
        var whatsappFloat = document.getElementById('whatsapp-float');
        if (whatsappFloat && Object.keys(cart).length > 0) {
            whatsappFloat.classList.remove('bottom-24');
            whatsappFloat.classList.add('bottom-44');
        }
    }

    function selectVariant(variantId, variantName, priceAdj, stock, chipEl) {
        // Deselect all chips
        document.querySelectorAll('.variant-chip').forEach(function(c) {
            c.classList.remove('selected');
        });
        chipEl.classList.add('selected');

        // Update modal state
        modalState.selectedVariantId = variantId;
        modalState.selectedVariantName = variantName;
        var prod = products[modalState.productId];
        modalState.selectedVariantPrice = prod.price + priceAdj;
        modalState.selectedVariantStock = stock;
        modalState.qty = 1;

        // Update price display
        document.getElementById('modal-product-price').textContent = 'Rp ' + modalState.selectedVariantPrice.toLocaleString('id-ID');
        document.getElementById('modal-stock-info').textContent = 'Stok: ' + stock;
        document.getElementById('modal-qty-display').textContent = '1';
        document.getElementById('modal-variant-note').textContent = '';

        // Load note if already in cart
        var noteInput = document.getElementById('modal-item-note');
        if (noteInput) {
            var key = cartKey(modalState.productId, variantId);
            noteInput.value = cart[key] ? (cart[key].note || '') : '';
        }

        // Enable add button
        document.getElementById('modal-add-btn').disabled = false;

        updateModalTotal();
    }

    function modalIncQty() {
        var maxStock = modalState.selectedVariantId ? modalState.selectedVariantStock : (products[modalState.productId] ? products[modalState.productId].stock : 1);
        if (modalState.qty >= maxStock) {
            document.getElementById('modal-inc-btn').classList.add('animate-pulse');
            setTimeout(function() { document.getElementById('modal-inc-btn').classList.remove('animate-pulse'); }, 300);
            return;
        }
        modalState.qty++;
        document.getElementById('modal-qty-display').textContent = modalState.qty;
        updateModalTotal();
    }

    function modalDecQty() {
        if (modalState.qty <= 1) return;
        modalState.qty--;
        document.getElementById('modal-qty-display').textContent = modalState.qty;
        updateModalTotal();
    }

    function updateModalTotal() {
        var price = modalState.selectedVariantId ? modalState.selectedVariantPrice : (products[modalState.productId] ? products[modalState.productId].price : 0);
        var total = price * modalState.qty;
        document.getElementById('modal-line-total').textContent = 'Rp ' + total.toLocaleString('id-ID');
    }

    function confirmAddToCart() {
        var productId = modalState.productId;
        var prod = products[productId];
        if (!prod) return;

        var variantId = modalState.selectedVariantId || 0;
        var variantName = modalState.selectedVariantName;
        var finalPrice = modalState.selectedVariantId ? modalState.selectedVariantPrice : prod.price;
        var maxStock = modalState.selectedVariantId ? modalState.selectedVariantStock : prod.stock;

        if (prod.hasVariants && !variantId) {
            document.getElementById('modal-variant-note').textContent = '⚠ Pilih varian terlebih dahulu!';
            return;
        }

        if (maxStock <= 0) {
            alert('Maaf, varian ini sedang habis.');
            return;
        }

        var key = cartKey(productId, variantId);
        var itemNote = document.getElementById('modal-item-note') ? document.getElementById('modal-item-note').value.trim() : '';

        // If already exists, add qty on top
        if (cart[key]) {
            var newQty = cart[key].qty + modalState.qty;
            if (newQty > maxStock) newQty = maxStock;
            cart[key].qty = newQty;
            cart[key].note = itemNote;
        } else {
            cart[key] = {
                product_id: productId,
                variant_id: variantId || null,
                name: prod.name,
                variant_name: variantName,
                price: finalPrice,
                qty: modalState.qty,
                note: itemNote
            };
        }

        // Sync hidden select for backward compat
        if (prod.hasVariants && variantId) {
            var hiddenSelect = getVariantSelect(productId);
            if (hiddenSelect) {
                hiddenSelect.value = variantId;
                // Trigger change silently
                var ev = new Event('change');
                hiddenSelect.dispatchEvent(ev);
            }
        }

        // Update selected variant badge on card
        if (variantName) {
            var badge = document.getElementById('selected-variant-badge-' + productId);
            if (badge) {
                badge.textContent = '✓ ' + variantName;
                badge.classList.remove('hidden');
            }
        }

        // Update cart state and show qty controls on card
        updateCartState(productId);

        // Also update card-qty span with the actual total qty in cart
        var cardQtySpan = document.getElementById('card-qty-' + productId);
        if (cardQtySpan) cardQtySpan.textContent = cart[key].qty;

        // Show a brief success animation on the card button
        var container = document.getElementById('btn-container-' + productId);
        if (container) {
            var addBtn = container.querySelector('.add-btn');
            var qtyControls = container.querySelector('.qty-controls');
            if (addBtn && qtyControls) {
                addBtn.classList.add('scale-0', 'opacity-0', 'pointer-events-none');
                qtyControls.classList.remove('scale-0', 'opacity-0', 'pointer-events-none');
                qtyControls.classList.add('scale-100', 'opacity-100', 'pointer-events-auto');
            }
        }

        // Trigger particle burst and flying animation
        const modalBtn = document.getElementById('modal-add-btn');
        if (modalBtn) {
            const rect = modalBtn.getBoundingClientRect();
            const x = rect.left + rect.width / 2;
            const y = rect.top + rect.height / 2;
            createParticleBurst(x, y);
        }

        const startEl = document.getElementById('modal-product-image').classList.contains('hidden') 
            ? document.getElementById('modal-product-emoji')
            : document.getElementById('modal-product-image');
        
        let endEl = document.getElementById('floating-cart');
        const overlay = document.getElementById('checkout-overlay');
        if (overlay && !overlay.classList.contains('pointer-events-none')) {
            endEl = document.getElementById('tab-cart');
        }
        
        if (startEl && endEl) {
            animateFlyToCart(startEl, endEl, prod.imageUrl);
        }

        closeVariantModal();
    }

    // Close modal on backdrop click
    document.getElementById('variant-modal-overlay').addEventListener('click', function(e) {
        if (e.target === this) closeVariantModal();
    });

    // Called when variant dropdown changes (hidden select)
    function onVariantChange(productId, select) {
        var key = cartKey(productId, select.value);
        var container = document.getElementById('btn-container-' + productId);
        if (!container) return;
        var qtyControls = container.querySelector('.qty-controls');
        var addBtn = container.querySelector('.add-btn');
        var qtySpan = document.getElementById('card-qty-' + productId);

        if (select.value && cart[key]) {
            // Already in cart, show qty controls
            addBtn.classList.add('scale-0', 'opacity-0', 'pointer-events-none');
            qtyControls.classList.remove('scale-0', 'opacity-0', 'pointer-events-none');
            qtyControls.classList.add('scale-100', 'opacity-100', 'pointer-events-auto');
            if (qtySpan) qtySpan.textContent = cart[key].qty;
        } else {
            // Not in cart, reset to ADD button
            qtyControls.classList.remove('scale-100', 'opacity-100', 'pointer-events-auto');
            qtyControls.classList.add('scale-0', 'opacity-0', 'pointer-events-none');
            addBtn.classList.remove('scale-0', 'opacity-0', 'pointer-events-none');
            addBtn.classList.add('scale-100', 'opacity-100');
            if (qtySpan) qtySpan.textContent = '0';
        }
    }

    // Add product to Cart (reads variant from select if has variants)
    function addToCart(productId) {
        var prod = products[productId];
        if (!prod) return;

        var variantId = 0;
        var variantName = null;
        var finalPrice = prod.price;
        var maxStock = prod.stock;

        if (prod.hasVariants) {
            var select = getVariantSelect(productId);
            if (!select || !select.value) {
                alert('Silakan pilih varian terlebih dahulu.');
                if (select) select.focus();
                return;
            }
            variantId = parseInt(select.value);
            var selected = select.options[select.selectedIndex];
            variantName = selected.text.split('(+')[0].trim();
            finalPrice = prod.price + parseFloat(selected.dataset.adjustment || 0);
            maxStock = parseInt(selected.dataset.stock || 0);
        }

        var key = cartKey(productId, variantId);

        if (!cart[key]) {
            if (maxStock <= 0) {
                alert('Maaf, varian ini sedang habis.');
                return;
            }
            cart[key] = {
                product_id: productId,
                variant_id: variantId || null,
                name: prod.name,
                variant_name: variantName,
                price: finalPrice,
                qty: 1,
                note: ''
            };
        }
        updateCartState(productId);

        // Trigger animations
        const container = document.getElementById('btn-container-' + productId);
        if (container) {
            const addBtn = container.querySelector('.add-btn');
            if (addBtn) {
                const rect = addBtn.getBoundingClientRect();
                createParticleBurst(rect.left + rect.width / 2, rect.top + rect.height / 2);
                
                const card = container.closest('.product-card');
                const startEl = card ? card.querySelector('img') : null;
                let endEl = document.getElementById('floating-cart');
                const overlay = document.getElementById('checkout-overlay');
                if (overlay && !overlay.classList.contains('pointer-events-none')) {
                    endEl = document.getElementById('tab-cart');
                }
                
                if (startEl && endEl) {
                    animateFlyToCart(startEl, endEl, prod.imageUrl);
                }
            }
        }
    }

    // Increment qty (respect stock limit)
    function incrementQty(productId) {
        var variantId = 0;
        var prod = products[productId];
        if (prod && prod.hasVariants) {
            var sel = getSelectedVariant(productId);
            if (!sel) return;
            variantId = sel.id;
        }
        var key = cartKey(productId, variantId);
        if (!cart[key]) return;

        var maxStock = prod.stock;
        if (prod.hasVariants) {
            var sel = getSelectedVariant(productId);
            if (sel) maxStock = sel.stock;
        }

        if (cart[key].qty >= maxStock) {
            var container = document.getElementById('btn-container-' + productId);
            if (container) {
                container.classList.add('animate-pulse');
                setTimeout(function() { container.classList.remove('animate-pulse'); }, 300);
            }
            return;
        }
        cart[key].qty++;
        updateCartState(productId);
    }

    // Decrement qty
    function decrementQty(productId) {
        var variantId = 0;
        var prod = products[productId];
        if (prod && prod.hasVariants) {
            var sel = getSelectedVariant(productId);
            if (!sel) return;
            variantId = sel.id;
        }
        var key = cartKey(productId, variantId);
        if (!cart[key]) return;

        cart[key].qty--;
        if (cart[key].qty <= 0) {
            delete cart[key];
            resetCardButton(productId);
        } else {
            updateCartState(productId);
        }
        updateCartState(null);
    }

    // Remove item from cart
    function removeFromCart(key) {
        if (!cart[key]) return;
        var productId = cart[key].product_id;
        var variantId = cart[key].variant_id || 0;

        const row = document.getElementById('cart-row-' + key);
        if (row) {
            row.classList.add('removing');
            setTimeout(function() {
                delete cart[key];

                // If the removed item matches the currently selected variant on the card, reset card button
                var currentVariantId = 0;
                var prod = products[productId];
                if (prod && prod.hasVariants) {
                    var sel = getSelectedVariant(productId);
                    if (sel) currentVariantId = sel.id;
                }

                if (variantId === currentVariantId) {
                    resetCardButton(productId);
                }

                updateCartState(null);
            }, 350);
        } else {
            delete cart[key];

            // If the removed item matches the currently selected variant on the card, reset card button
            var currentVariantId = 0;
            var prod = products[productId];
            if (prod && prod.hasVariants) {
                var sel = getSelectedVariant(productId);
                if (sel) currentVariantId = sel.id;
            }

            if (variantId === currentVariantId) {
                resetCardButton(productId);
            }

            updateCartState(null);
        }
    }

    // Update dynamic state in DOM
    function updateCartState(productId) {
        var totalItems = 0;
        var totalPrice = 0;
        var discount = 0;
        var shippingFee = (document.querySelector('input[name="type"]:checked')?.value === 'delivery' && deliveryFeeEnabled) ? deliveryFeeAmount : 0;

        // Persist cart to localStorage
        try {
            localStorage.setItem('mamitha_cart', JSON.stringify(cart));
        } catch (e) {
            console.error('Error saving cart to localStorage:', e);
        }

        // Sync all product card buttons in the grid dynamically
        Object.keys(products).forEach(function(pId) {
            var variantId = 0;
            var prod = products[pId];
            if (prod && prod.hasVariants) {
                var sel = getSelectedVariant(pId);
                if (sel) variantId = sel.id;
            }
            var key = cartKey(pId, variantId);
            var cardQtySpan = document.getElementById('card-qty-' + pId);
            var container = document.getElementById('btn-container-' + pId);
            
            if (container && cardQtySpan) {
                var addBtn = container.querySelector('.add-btn');
                var qtyControls = container.querySelector('.qty-controls');
                
                if (cart[key]) {
                    addBtn.classList.add('scale-0', 'opacity-0', 'pointer-events-none');
                    qtyControls.classList.remove('scale-0', 'opacity-0', 'pointer-events-none');
                    qtyControls.classList.add('scale-100', 'opacity-100', 'pointer-events-auto');
                    cardQtySpan.textContent = cart[key].qty;
                    
                    // Sync the variant badge
                    if (cart[key].variant_name) {
                        var badge = document.getElementById('selected-variant-badge-' + pId);
                        if (badge) {
                            badge.textContent = '✓ ' + cart[key].variant_name;
                            badge.classList.remove('hidden');
                        }
                    }
                } else {
                    qtyControls.classList.remove('scale-100', 'opacity-100', 'pointer-events-auto');
                    qtyControls.classList.add('scale-0', 'opacity-0', 'pointer-events-none');
                    addBtn.classList.remove('scale-0', 'opacity-0', 'pointer-events-none');
                    addBtn.classList.add('scale-100', 'opacity-100');
                    cardQtySpan.textContent = '0';
                    
                    // Hide the variant badge if no variant selected
                    if (prod && prod.hasVariants) {
                        var badge = document.getElementById('selected-variant-badge-' + pId);
                        if (badge) badge.classList.add('hidden');
                    }
                }
            }
        });

        // Loop over cart and sum
        var cartItemsHtml = '';
        var index = 0;
        Object.keys(cart).forEach(function(key) {
            var item = cart[key];
            totalItems += item.qty;
            totalPrice += item.price * item.qty;

            // Build cart items list HTML
            var displayName = item.name;
            if (item.variant_name) displayName += ' (' + item.variant_name + ')';
            cartItemsHtml += '' +
                '<div id="cart-row-' + key + '" class="cart-item-row flex items-center justify-between py-2.5 border-b border-gray-100 dark:border-gray-800 last:border-0">' +
                    '<div class="flex-1 min-w-0">' +
                        '<p class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate">' + displayName + '</p>' +
                        '<p class="text-xs text-gray-500">Rp ' + item.price.toLocaleString('id-ID') + ' x ' + item.qty + '</p>' +
                        (item.note ? '<p class="text-[11px] text-amber-700 dark:text-amber-400 mt-0.5 font-medium italic">" ' + item.note + ' "</p>' : '') +
                    '</div>' +
                    '<div class="flex items-center space-x-3 ml-2 flex-shrink-0">' +
                        '<p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Rp ' + (item.price * item.qty).toLocaleString('id-ID') + '</p>' +
                        '<button type="button" onclick="removeFromCart(\'' + key + '\')" class="p-1.5 text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 rounded-xl hover:bg-red-50 dark:hover:bg-red-950/20 transition-colors" title="Hapus item">' +
                            '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">' +
                                '<path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>' +
                            '</svg>' +
                        '</button>' +
                    '</div>' +
                '</div>';
            index++;
        });

        document.getElementById('cart-items-list').innerHTML = cartItemsHtml;

        // 10% OFF discount (if enabled)
        if (discountEnabled) {
            discount = Math.round(totalPrice * discountPercentage / 100);
        }

        // Update global cart badges
        if (typeof window.updateGlobalCartBadges === 'function') {
            window.updateGlobalCartBadges();
        }

        // Update Floating Cart Bar
        var floatingCart = document.getElementById('floating-cart');
        var itemCountSpan = document.getElementById('cart-item-count');
        var totalPriceSpan = document.getElementById('cart-total-price');
        var whatsappFloat = document.getElementById('whatsapp-float');

        // Update tab badge count
        var tabBadge = document.getElementById('tab-cart-badge');
        if (tabBadge) tabBadge.textContent = totalItems;

        if (totalItems > 0) {
            floatingCart.classList.remove('translate-y-32', 'opacity-0');
            floatingCart.classList.add('translate-y-0', 'opacity-100');
            itemCountSpan.textContent = totalItems + ' Item';
            totalPriceSpan.textContent = 'Rp ' + totalPrice.toLocaleString('id-ID');
            if (whatsappFloat) {
                whatsappFloat.classList.remove('bottom-24');
                whatsappFloat.classList.add('bottom-44');
            }
        } else {
            floatingCart.classList.remove('translate-y-0', 'opacity-100');
            floatingCart.classList.add('translate-y-32', 'opacity-0');
            // Clear cart items list when empty
            document.getElementById('cart-items-list').innerHTML = '<p class="text-center text-gray-450 text-sm py-4">Belum ada item dipilih</p>';
            if (whatsappFloat) {
                whatsappFloat.classList.remove('bottom-44');
                whatsappFloat.classList.add('bottom-24');
            }
        }

        // Update Checkout Drawer Summaries
        document.getElementById('summary-subtotal').textContent = 'Rp ' + totalPrice.toLocaleString('id-ID');
        document.getElementById('summary-discount').textContent = discountEnabled ? '-Rp ' + discount.toLocaleString('id-ID') : 'Rp 0';
        document.getElementById('summary-shipping').textContent = 'Rp ' + shippingFee.toLocaleString('id-ID');

        // Hide discount row if disabled
        var discountRow = document.getElementById('discount-row');
        if (discountRow) {
            discountRow.style.display = discountEnabled ? 'flex' : 'none';
        }
        
        var finalTotal = totalPrice - discount + shippingFee;
        if (totalItems === 0) finalTotal = 0;
        
        document.getElementById('summary-total').textContent = 'Rp ' + finalTotal.toLocaleString('id-ID');

        // Dynamically build hidden input fields inside the form
        var hiddenContainer = document.getElementById('hidden-cart-inputs');
        hiddenContainer.innerHTML = '';
        var idx = 0;
        Object.keys(cart).forEach(function(key) {
            var item = cart[key];
            hiddenContainer.innerHTML += '' +
                '<input type="hidden" name="items[' + idx + '][product_id]" value="' + item.product_id + '">' +
                '<input type="hidden" name="items[' + idx + '][variant_id]" value="' + (item.variant_id || '') + '">' +
                '<input type="hidden" name="items[' + idx + '][quantity]" value="' + item.qty + '">' +
                '<input type="hidden" name="items[' + idx + '][note]" value="' + (item.note || '') + '">';
            idx++;
        });
    }

    // Reset card button back to + ADD
    function resetCardButton(productId) {
        var container = document.getElementById('btn-container-' + productId);
        if (!container) return;
        var addBtn = container.querySelector('.add-btn');
        var qtyControls = container.querySelector('.qty-controls');

        qtyControls.classList.remove('scale-100', 'opacity-100', 'pointer-events-auto');
        qtyControls.classList.add('scale-0', 'opacity-0', 'pointer-events-none');
        addBtn.classList.remove('scale-0', 'opacity-0', 'pointer-events-none');
        addBtn.classList.add('scale-100', 'opacity-100');

        var cardQtySpan = document.getElementById('card-qty-' + productId);
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
        staggerProductCards();
    }

    // Category filter
    function filterCategory(catId, btn) {
        document.querySelectorAll('.category-pill').forEach(pill => {
            pill.classList.remove('active', 'text-white');
            pill.classList.add('text-gray-700', 'dark:text-gray-300');
        });
        btn.classList.add('active', 'text-white');
        btn.classList.remove('text-gray-700', 'dark:text-gray-300');
        moveCategoryActiveBg(btn);

        document.querySelectorAll('.product-card').forEach(card => {
            if (catId === 'all' || card.dataset.category === catId) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
        staggerProductCards();
    }

    // Toggle checkout panel drawer (TikTok style slide over)
    function toggleCheckoutDrawer(open) {
        const overlay = document.getElementById('checkout-overlay');
        const drawer = overlay.querySelector('.drawer-transition');

        if (open) {
            // Always start on the cart tab when opening
            goToStep('cart');

            overlay.classList.remove('opacity-0', 'pointer-events-none');
            overlay.classList.add('opacity-100');
            drawer.classList.remove('translate-x-full');
            drawer.classList.add('translate-x-0');
            
            // Trigger leaflet map resizing when drawer opens
            setTimeout(() => {
                if (map) {
                    map.invalidateSize();
                }
                moveTabActiveBg('cart');
            }, 300);
        } else {
            overlay.classList.add('opacity-0', 'pointer-events-none');
            overlay.classList.remove('opacity-100');
            drawer.classList.add('translate-x-full');
            drawer.classList.remove('translate-x-0');
        }
    }

    // =========================================================================
    // Drawer Step/Tab Navigation
    // =========================================================================
    let currentDrawerStep = 'cart';

    function goToStep(step) {
        currentDrawerStep = step;

        const cartNextBtn = document.getElementById('cart-next-btn');
        const checkoutSubmitBtn = document.getElementById('checkout-submit-btn');
        const tabCart = document.getElementById('tab-cart');
        const tabCheckout = document.getElementById('tab-checkout');
        const dotCart = document.getElementById('step-dot-cart');
        const dotCheckout = document.getElementById('step-dot-checkout');
        const slider = document.getElementById('drawer-steps-slider');

        if (step === 'cart') {
            if (slider) slider.style.transform = 'translateX(0%)';
            cartNextBtn.classList.remove('hidden');
            checkoutSubmitBtn.classList.add('hidden');

            // Update tabs
            tabCart.classList.remove('inactive');
            tabCart.classList.add('active');
            tabCheckout.classList.remove('active');
            tabCheckout.classList.add('inactive');
            moveTabActiveBg('cart');

            // Update dots
            dotCart.classList.remove('inactive');
            dotCart.classList.add('active');
            dotCheckout.classList.remove('active');
            dotCheckout.classList.add('inactive');
        } else if (step === 'checkout') {
            // Check if cart is empty
            if (Object.keys(cart).length === 0) {
                tabCart.classList.add('qty-bounce');
                setTimeout(() => tabCart.classList.remove('qty-bounce'), 450);
                return;
            }

            if (slider) slider.style.transform = 'translateX(-50%)';
            cartNextBtn.classList.add('hidden');
            checkoutSubmitBtn.classList.remove('hidden');

            // Update tabs
            tabCheckout.classList.remove('inactive');
            tabCheckout.classList.add('active');
            tabCart.classList.remove('active');
            tabCart.classList.add('inactive');
            moveTabActiveBg('checkout');

            // Update dots
            dotCheckout.classList.remove('inactive');
            dotCheckout.classList.add('active');
            dotCart.classList.remove('active');
            dotCart.classList.add('inactive');

            // Trigger map resize if delivery is selected
            setTimeout(() => {
                if (map) map.invalidateSize();
            }, 300);
        }
    }

    function handleDrawerBack() {
        if (currentDrawerStep === 'checkout') {
            // Go back to cart
            goToStep('cart');
        } else {
            // Close the drawer entirely
            toggleCheckoutDrawer(false);
        }
    }

    // Close drawer when clicking backdrop overlay
    document.getElementById('checkout-overlay').addEventListener('click', function(e) {
        if (e.target === this) {
            toggleCheckoutDrawer(false);
        }
    });

    // Helper to update location summary header label
    function updateAddressSummary(address) {
        const el = document.getElementById('selected-address-summary');
        if (!el) return;
        if (!address) {
            el.textContent = 'Tentukan Alamat Anda di Peta';
        } else {
            el.textContent = address.length > 32 ? address.substring(0, 32) + '...' : address;
        }
    }

    // Update delivery selection
    function updateDeliveryType(type) {
        const deliverySection = document.getElementById('delivery-details-section');
        const addressText = document.getElementById('address-text');
        const locLabel = document.getElementById('selected-address-summary');
        
        if (type === 'delivery') {
            deliverySection.classList.remove('hidden');
            addressText.setAttribute('required', 'required');
            if (locLabel) {
                updateAddressSummary(addressText.value);
            }
            setTimeout(() => {
                if (map) map.invalidateSize();
            }, 100);
        } else {
            deliverySection.classList.add('hidden');
            addressText.removeAttribute('required');
            if (locLabel) {
                locLabel.textContent = 'Ambil di Outlet Mamitha (Sleman)';
            }
        }
        updateCartState(null);
    }

    // Member Login Warning Dialog Handlers
    function showMemberLoginPrompt() {
        const modal = document.getElementById('member-login-modal');
        if (modal) {
            modal.classList.add('opacity-100');
            modal.classList.remove('opacity-0', 'pointer-events-none');
        }
    }

    function closeMemberLoginPrompt() {
        const modal = document.getElementById('member-login-modal');
        if (modal) {
            modal.classList.remove('opacity-100');
            modal.classList.add('opacity-0', 'pointer-events-none');
        }
    }

    // Submit Order Form
    function submitOrder() {
        const form = document.getElementById('orderForm');
        // Validate inputs
        if (form.reportValidity()) {
            try {
                localStorage.removeItem('mamitha_cart');
            } catch (e) {}
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

        // Initialize sliding active pill indicator and staggered grid entry
        const firstCategoryBtn = document.querySelector('.category-pill');
        if (firstCategoryBtn) {
            moveCategoryActiveBg(firstCategoryBtn);
        }
        staggerProductCards();
        initSwipeToPay();
        updateCartState(null); // Sync restored cart items to DOM

        // Listen for manual inputs on address text area
        const addressInput = document.getElementById('address-text');
        if (addressInput) {
            addressInput.addEventListener('input', function() {
                updateAddressSummary(this.value);
            });
        }

        // Check if member registration checkbox clicked and verify login status
        const isMemberCheckbox = document.getElementById('is_member');
        if (isMemberCheckbox) {
            isMemberCheckbox.addEventListener('change', function() {
                if (this.checked && !isLoggedIn) {
                    this.checked = false;
                    showMemberLoginPrompt();
                }
            });
        }
    });

    window.addEventListener('resize', () => {
        const activeCategoryBtn = document.querySelector('.category-pill.active');
        if (activeCategoryBtn) {
            moveCategoryActiveBg(activeCategoryBtn);
        }
        if (typeof currentDrawerStep !== 'undefined') {
            moveTabActiveBg(currentDrawerStep);
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

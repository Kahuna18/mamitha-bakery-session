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
</style>
@endpush

@section('content')
@php
    $storeLat = \App\Models\Setting::getValue('store_latitude', '-7.7609582');
    $storeLng = \App\Models\Setting::getValue('store_longitude', '110.2529556');
    $hasRoute = $order->type === 'delivery' && $order->latitude && $order->longitude;
@endphp

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
</script>
@endpush

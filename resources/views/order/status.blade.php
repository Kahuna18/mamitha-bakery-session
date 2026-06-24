@extends('layouts.app')

@section('title', 'Cek Status Pesanan')

@push('styles')
<!-- Leaflet CSS for Map -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<style>
    .status-map {
        height: 240px;
        z-index: 10;
    }
    .dark .leaflet-tile {
        filter: brightness(0.6) invert(1) contrast(3) hue-rotate(200deg) saturate(0.3);
    }
</style>
@endpush

@section('content')
@php
    $storeLat = \App\Models\Setting::getValue('store_latitude', '-7.7609582');
    $storeLng = \App\Models\Setting::getValue('store_longitude', '110.2529556');
    $storeWhatsapp = \App\Models\Setting::getValue('store_whatsapp');
@endphp

<div class="min-h-screen bg-cream-50 dark:bg-gray-900 py-12 px-4 transition-colors duration-200">
    <div class="max-w-2xl mx-auto space-y-6">
        
        <!-- Search lookup box -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl border border-amber-100/50 dark:border-gray-700/50 p-6 shadow-md">
            <h1 class="text-2xl font-bold font-serif text-amber-900 dark:text-amber-100 mb-2">Lacak Pesanan Roti</h1>
            <p class="text-gray-500 dark:text-gray-400 text-xs mb-4">Masukkan nomor WhatsApp yang Anda daftarkan saat memesan.</p>
            
            <form method="POST" action="{{ route('order.check-status') }}" class="flex flex-col sm:flex-row gap-3">
                @csrf
                <input type="tel" name="phone" required placeholder="Contoh: 08123456789" value="{{ old('phone', isset($customer) ? $customer->phone : '') }}" class="flex-1 px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-700 rounded-2xl text-sm focus:ring-2 focus:ring-amber-500 text-gray-800 dark:text-white">
                <button type="submit" class="px-6 py-3 bg-gray-900 dark:bg-gray-100 hover:bg-amber-700 dark:hover:bg-amber-600 text-white dark:text-gray-900 font-extrabold text-sm rounded-2xl transition shadow">
                    Lacak Sekarang
                </button>
            </form>
        </div>

        @if(isset($orders) && $orders->count() > 0)
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold font-serif text-gray-800 dark:text-gray-100">
                    Hasil Lacak: <span class="text-amber-700 dark:text-amber-400 font-sans font-extrabold">{{ $customer->name }}</span>
                </h2>
                <span class="text-xs text-gray-400 dark:text-gray-500 font-bold bg-white dark:bg-gray-800 px-3 py-1 rounded-full border border-gray-100 dark:border-gray-700/50">
                    {{ $orders->count() }} Pesanan
                </span>
            </div>

            <!-- List of Orders (TikTok Tracking style for each order found) -->
            <div class="space-y-8">
                @foreach($orders as $index => $order)
                @php
                    $hasRoute = $order->type === 'delivery' && $order->latitude && $order->longitude;
                @endphp
                
                <div class="bg-white dark:bg-gray-800 rounded-3xl border border-amber-100/50 dark:border-gray-700/50 shadow-lg overflow-hidden">
                    
                    <!-- Accordion header/title bar -->
                    <div class="p-6 border-b border-gray-100 dark:border-gray-700/50 bg-gray-50/50 dark:bg-gray-800/30 flex justify-between items-center gap-4">
                        <div>
                            <p class="text-[10px] text-gray-400 font-extrabold uppercase tracking-wider">No. Order</p>
                            <p class="font-serif font-black text-gray-800 dark:text-gray-200">{{ $order->order_number }}</p>
                        </div>
                        <div class="text-right flex items-center gap-3">
                            <span class="px-3 py-1 text-[10px] font-extrabold rounded-full tracking-wider uppercase
                                @if($order->status == 'pending') bg-yellow-100 text-yellow-700
                                @elseif($order->status == 'confirmed') bg-blue-100 text-blue-700
                                @elseif($order->status == 'producing') bg-orange-100 text-orange-700
                                @elseif($order->status == 'ready') bg-green-100 text-green-700
                                @elseif($order->status == 'done') bg-gray-100 text-gray-700
                                @else bg-red-100 text-red-700 @endif">
                                {{ $order->statusLabel() }}
                            </span>
                            <span class="text-xs text-gray-400">#{{ $index + 1 }}</span>
                        </div>
                    </div>

                    @if(($order->type === 'delivery' && $order->latitude && $order->longitude) || $order->type === 'pickup')
                    <!-- Map container -->
                    <div class="relative">
                        <div id="map-{{ $order->id }}" class="status-map w-full"></div>

                        @if($order->type === 'delivery')
                        <!-- GPS Track Me Button -->
                        <button type="button" onclick="trackMyLocationForOrder({{ $order->id }})" id="track-gps-btn-{{ $order->id }}" class="absolute bottom-4 right-4 z-20 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 p-2.5 rounded-full shadow-md hover:bg-gray-100 dark:hover:bg-gray-700 transition flex items-center justify-center border border-gray-200 dark:border-gray-700 animate-pulse hover:animate-none" style="animation-duration: 3s;" title="Lacak Lokasi Saya">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" id="track-gps-icon-{{ $order->id }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </button>
                        @endif
                    </div>
                    @endif

                    <!-- Courier Info -->
                    @php
                        $courierName = \App\Models\Setting::getValue('courier_name', 'Pak Budi (Mamitha Courier)');
                        $courierPhone = \App\Models\Setting::getValue('courier_phone') ?: $storeWhatsapp;
                    @endphp
                    <div class="p-6 border-b border-gray-100 dark:border-gray-700/50 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-amber-50 dark:bg-amber-950/20 rounded-2xl flex items-center justify-center text-2xl shadow-inner">
                                🛵
                            </div>
                            <div>
                                <h4 class="font-extrabold text-gray-800 dark:text-gray-200 text-sm">{{ $courierName }}</h4>
                                <p class="text-[10px] text-gray-400 mt-0.5">⭐ 4.9 • Delivery Driver</p>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $courierPhone) }}?text=Halo%20{{ urlencode($courierName) }},%20lacak%20order%20{{ $order->order_number }}" target="_blank" class="w-9 h-9 bg-green-500 text-white rounded-full flex items-center justify-center shadow-sm">
                                💬
                            </a>
                            <a href="tel:{{ preg_replace('/[^0-9]/', '', $courierPhone) }}" class="w-9 h-9 bg-gray-900 dark:bg-gray-100 text-white dark:text-gray-900 rounded-full flex items-center justify-center shadow-sm">
                                📞
                            </a>
                        </div>
                    </div>

                    <!-- Status Timeline -->
                    <div class="p-6 border-b border-gray-100 dark:border-gray-700/50">
                        <h4 class="font-extrabold text-gray-700 dark:text-gray-300 text-xs tracking-wider uppercase mb-5">Detail Perjalanan</h4>
                        <div class="relative pl-7 space-y-6 before:absolute before:inset-y-1 before:left-3 before:w-0.5 before:bg-gray-200 dark:before:bg-gray-700">
                            
                            <!-- Step 1 -->
                            <div class="relative">
                                <span class="absolute -left-7 top-1 w-5.5 h-5.5 rounded-full flex items-center justify-center text-[10px] font-black bg-green-500 text-white shadow-sm">
                                    ✔
                                </span>
                                <div>
                                    <p class="font-extrabold text-xs text-gray-800 dark:text-gray-200">Pesanan Diterima</p>
                                    <p class="text-[10px] text-gray-400">Order berhasil tersimpan.</p>
                                </div>
                            </div>

                            <!-- Step 2 -->
                            @php $step2 = in_array($order->status, ['confirmed', 'producing', 'ready', 'done']); @endphp
                            <div class="relative">
                                <span class="absolute -left-7 top-1 w-5.5 h-5.5 rounded-full flex items-center justify-center text-[10px] font-black 
                                    {{ $step2 ? 'bg-green-500 text-white shadow-sm' : 'bg-gray-200 dark:bg-gray-700 text-gray-400' }}">
                                    {{ $step2 ? '✔' : '2' }}
                                </span>
                                <div>
                                    <p class="font-extrabold text-xs {{ $step2 ? 'text-gray-800 dark:text-gray-200' : 'text-gray-400' }}">Dikonfirmasi Admin</p>
                                    <p class="text-[10px] text-gray-400">Terverifikasi oleh admin.</p>
                                </div>
                            </div>

                            <!-- Step 3 -->
                            @php $step3 = in_array($order->status, ['producing', 'ready', 'done']); @endphp
                            <div class="relative">
                                <span class="absolute -left-7 top-1 w-5.5 h-5.5 rounded-full flex items-center justify-center text-[10px] font-black 
                                    {{ $step3 ? 'bg-green-500 text-white shadow-sm' : 'bg-gray-200 dark:bg-gray-700 text-gray-400' }}">
                                    {{ $step3 ? '✔' : '3' }}
                                </span>
                                <div>
                                    <p class="font-extrabold text-xs {{ $step3 ? 'text-gray-800 dark:text-gray-200' : 'text-gray-400' }}">Sedang Diproses</p>
                                    <p class="text-[10px] text-gray-400">Roti sedang dipanggang hangat.</p>
                                </div>
                            </div>

                            <!-- Step 4 -->
                            @php $step4 = in_array($order->status, ['ready', 'done']); @endphp
                            <div class="relative">
                                <span class="absolute -left-7 top-1 w-5.5 h-5.5 rounded-full flex items-center justify-center text-[10px] font-black 
                                    {{ $step4 ? 'bg-green-500 text-white shadow-sm' : 'bg-gray-200 dark:bg-gray-700 text-gray-400' }}">
                                    {{ $step4 ? '✔' : '4' }}
                                </span>
                                <div>
                                    <p class="font-extrabold text-xs {{ $step4 ? 'text-gray-800 dark:text-gray-200' : 'text-gray-400' }}">
                                        {{ $order->type === 'delivery' ? 'Dalam Perjalanan Kurir' : 'Siap Diambil' }}
                                    </p>
                                    <p class="text-[10px] text-gray-400">Siap untuk dikirim / diambil.</p>
                                </div>
                            </div>

                            <!-- Step 5 -->
                            @php $step5 = $order->status === 'done'; @endphp
                            <div class="relative">
                                <span class="absolute -left-7 top-1 w-5.5 h-5.5 rounded-full flex items-center justify-center text-[10px] font-black 
                                    {{ $step5 ? 'bg-green-500 text-white shadow-sm' : 'bg-gray-200 dark:bg-gray-700 text-gray-400' }}">
                                    {{ $step5 ? '✔' : '5' }}
                                </span>
                                <div>
                                    <p class="font-extrabold text-xs {{ $step5 ? 'text-gray-800 dark:text-gray-200' : 'text-gray-400' }}">Pesanan Selesai</p>
                                    <p class="text-[10px] text-gray-400">Selesai diserahkan.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Items breakdown and summaries -->
                    <div class="p-6 bg-gray-50/50 dark:bg-gray-800/30 space-y-4">
                        <div class="divide-y divide-gray-100 dark:divide-gray-700/50">
                            @foreach($order->items as $item)
                            <div class="py-2.5 flex justify-between text-xs">
                                <span class="text-gray-500 dark:text-gray-400">{{ $item->product->name }} x {{ $item->quantity }}</span>
                                <span class="font-bold text-gray-800 dark:text-gray-200">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                            </div>
                            @endforeach
                        </div>
                        
                        <div class="pt-4 border-t border-gray-200 dark:border-gray-700 flex justify-between items-center text-xs font-bold">
                            <div class="text-gray-400 uppercase tracking-wider">Total</div>
                            <div class="text-lg font-black text-amber-800 dark:text-amber-400">Rp {{ number_format($order->total, 0, ',', '.') }}</div>
                        </div>

                        <div class="pt-4 border-t border-gray-200 dark:border-gray-700 space-y-2.5 text-xs">
                            <div class="flex justify-between items-start">
                                <span class="text-gray-400 uppercase tracking-wider font-bold">Alamat Toko</span>
                                <span class="text-gray-600 dark:text-gray-300 text-right">{{ \App\Models\Setting::getValue('store_address') }}</span>
                            </div>
                            @php
                                $gmapsLink = \App\Models\Setting::getValue('store_gmaps_link') ?: "https://www.google.com/maps?q={$storeLat},{$storeLng}";
                            @endphp
                            <div class="flex justify-end">
                                <a href="{{ $gmapsLink }}" target="_blank" class="text-amber-700 dark:text-amber-450 hover:underline flex items-center gap-1 font-bold">
                                    🗺️ Buka Lokasi Toko di Google Maps &rarr;
                                </a>
                            </div>
                        </div>
                    </div>

                </div>
                @endforeach
            </div>

        @elseif(isset($orders))
            <!-- Error message if not found -->
            <div class="bg-white dark:bg-gray-800 rounded-3xl border border-red-150 p-8 text-center shadow">
                <span class="text-4xl">🔍</span>
                <h3 class="text-lg font-extrabold text-gray-800 dark:text-gray-200 mt-3">Tidak Ada Pesanan Ditemukan</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Pastikan nomor WhatsApp yang Anda masukkan benar.</p>
            </div>
        @endif

    </div>
</div>
@endsection

@push('scripts')
<!-- Leaflet JS Library -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    const storeLocation = [{{ $storeLat }}, {{ $storeLng }}];
    const activeMaps = {};

    // Initialize maps for all result order cards
    @if(isset($orders) && $orders->count() > 0)
        @foreach($orders as $order)
        (() => {
            const mapId = 'map-{{ $order->id }}';
            const lat = {{ $order->latitude ?? 'null' }};
            const lng = {{ $order->longitude ?? 'null' }};
            const type = '{{ $order->type }}';
            
            // Only initialize map if container exists
            const mapContainer = document.getElementById(mapId);
            if (!mapContainer) return;

            const isPickup = type === 'pickup';
            const mapCenter = isPickup ? storeLocation : [lat, lng];

            const map = L.map(mapId, {
                zoomControl: false,
                attributionControl: false,
                scrollWheelZoom: isPickup ? false : true
            }).setView(mapCenter, 16);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19
            }).addTo(map);

            const bakeryMarker = L.marker(storeLocation).addTo(map)
                .bindPopup('🥐 <b>Mamitha Bakery Sleman</b><br>{{ addslashes(\App\Models\Setting::getValue('store_address', 'Jl. Magelang KM 14, Sleman, Yogyakarta')) }}');

            let customerMarker = null;
            if (isPickup) {
                bakeryMarker.openPopup();
            } else {
                customerMarker = L.marker([lat, lng]).addTo(map).bindPopup('📍 Lokasi Pengiriman').openPopup();
            }

            // Save references for live GPS tracking
            activeMaps[{{ $order->id }}] = {
                map: map,
                bakeryMarker: bakeryMarker,
                customerMarker: customerMarker,
                hasRoute: !isPickup,
                watchId: null,
                myLocationMarker: null,
                myLocationCircle: null
            };

            if (!isPickup) {
                fitMapBoundsForOrder({{ $order->id }});
            }
        })();
        @endforeach
    @endif

    // Function to calculate boundaries for a specific map
    function fitMapBoundsForOrder(orderId, userLat = null, userLng = null) {
        const orderData = activeMaps[orderId];
        if (!orderData) return;

        const markers = [];
        if (orderData.bakeryMarker) markers.push(orderData.bakeryMarker);
        if (orderData.hasRoute && orderData.customerMarker) {
            markers.push(orderData.customerMarker);
        }

        if (userLat !== null && userLng !== null) {
            // Include dummy coordinate to pad boundaries
            const tempUserMarker = L.marker([userLat, userLng]);
            markers.push(tempUserMarker);
        }

        if (markers.length > 0) {
            const group = new L.featureGroup(markers);
            orderData.map.fitBounds(group.getBounds().pad(0.25));
        }
    }

    // Live GPS tracking for a specific order map
    function trackMyLocationForOrder(orderId) {
        const orderData = activeMaps[orderId];
        if (!orderData) return;

        const btn = document.getElementById(`track-gps-btn-${orderId}`);
        const icon = document.getElementById(`track-gps-icon-${orderId}`);

        if (!navigator.geolocation) {
            alert('Browser Anda tidak mendukung GPS.');
            return;
        }

        if (orderData.watchId !== null) {
            // Stop tracking
            navigator.geolocation.clearWatch(orderData.watchId);
            orderData.watchId = null;
            if (orderData.myLocationMarker) orderData.map.removeLayer(orderData.myLocationMarker);
            if (orderData.myLocationCircle) orderData.map.removeLayer(orderData.myLocationCircle);
            orderData.myLocationMarker = null;
            orderData.myLocationCircle = null;
            icon.classList.remove('animate-pulse');
            fitMapBoundsForOrder(orderId);
            alert('Pelacakan GPS dinonaktifkan.');
            return;
        }

        icon.classList.add('animate-pulse');

        orderData.watchId = navigator.geolocation.watchPosition(
            function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                const accuracy = position.coords.accuracy;

                // Update/Create Blue Dot Marker
                if (orderData.myLocationMarker) {
                    orderData.myLocationMarker.setLatLng([lat, lng]);
                } else {
                    const blueDotIcon = L.divIcon({
                        className: 'relative flex items-center justify-center w-6 h-6',
                        html: `
                            <span class="absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75 animate-ping"></span>
                            <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-blue-600 border-2 border-white shadow-md"></span>
                        `
                    });
                    orderData.myLocationMarker = L.marker([lat, lng], { icon: blueDotIcon }).addTo(orderData.map)
                        .bindPopup('<b>Lokasi Anda saat ini</b>');
                }

                // Update/Create Accuracy Circle
                if (orderData.myLocationCircle) {
                    orderData.myLocationCircle.setLatLng([lat, lng]).setRadius(accuracy);
                } else {
                    orderData.myLocationCircle = L.circle([lat, lng], {
                        radius: accuracy,
                        color: '#3b82f6',
                        fillColor: '#3b82f6',
                        fillOpacity: 0.1,
                        weight: 1
                    }).addTo(orderData.map);
                }

                fitMapBoundsForOrder(orderId, lat, lng);
            },
            function(error) {
                console.error('GPS tracking error:', error);
                alert('Gagal mengambil lokasi GPS Anda. Silakan periksa izin lokasi browser.');
                icon.classList.remove('animate-pulse');
                if (orderData.watchId !== null) {
                    navigator.geolocation.clearWatch(orderData.watchId);
                    orderData.watchId = null;
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

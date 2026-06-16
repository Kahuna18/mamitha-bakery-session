@extends('layouts.admin')

@section('title', 'Detail Order')

@push('styles')
<!-- Leaflet CSS for Map -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<style>
    #map-order {
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
<div class="mb-6">
    <a href="{{ route('admin.orders.index') }}" class="text-amber-600 hover:text-amber-700 text-sm font-medium">&larr; Kembali</a>
    <h1 class="text-2xl font-bold text-gray-800 mt-1">Detail Pesanan #{{ $order->order_number }}</h1>
</div>

<div class="grid lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Status Pesanan</h2>
            <div class="flex items-center justify-between mb-4">
                <span class="px-3 py-1.5 text-sm font-semibold rounded-full
                    @if($order->status == 'pending') bg-yellow-100 text-yellow-700
                    @elseif($order->status == 'confirmed') bg-blue-100 text-blue-700
                    @elseif($order->status == 'producing') bg-orange-100 text-orange-700
                    @elseif($order->status == 'ready') bg-green-100 text-green-700
                    @elseif($order->status == 'done') bg-gray-100 text-gray-700
                    @else bg-red-100 text-red-700 @endif">
                    {{ $order->statusLabel() }}
                </span>
                <span class="text-sm text-gray-500">Pembayaran: <span class="font-medium {{ $order->payment_status == 'paid' ? 'text-green-600' : 'text-yellow-600' }}">{{ $order->payment_status == 'paid' ? 'Lunas' : 'Belum Bayar' }}</span></span>
            </div>

            <form action="{{ route('admin.orders.update-status', $order) }}" method="POST" class="flex flex-wrap gap-2">
                @csrf
                <input type="hidden" name="status" id="status-input">
                @if($order->status == 'pending')
                <button type="button" onclick="setStatus('confirmed')" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">Konfirmasi</button>
                @endif
                @if(in_array($order->status, ['confirmed', 'pending']))
                <button type="button" onclick="setStatus('producing')" class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-sm font-medium rounded-lg transition">Mulai Produksi</button>
                @endif
                @if($order->status == 'producing')
                <button type="button" onclick="setStatus('ready')" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition">Siap Diambil</button>
                @endif
                @if($order->status == 'ready')
                <button type="button" onclick="setStatus('done')" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition">Selesai</button>
                @endif
                @if(!in_array($order->status, ['done', 'cancelled']))
                <button type="button" onclick="if(confirm('Batalkan pesanan ini?')) setStatus('cancelled')" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition">Batalkan</button>
                @endif
            </form>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Item Pesanan</h2>
            <div class="space-y-3">
                @foreach($order->items as $item)
                <div class="flex justify-between items-center py-2 border-b border-gray-50 last:border-0">
                    <div>
                        <p class="font-medium">{{ $item->product->name }} @if($item->variant) <span class="text-xs text-purple-600 font-semibold">({{ $item->variant->name }})</span> @endif</p>
                        <p class="text-sm text-gray-500">Rp {{ number_format($item->price, 0, ',', '.') }} x {{ $item->quantity }}</p>
                        @if($item->note)
                        <p class="text-xs text-amber-700 font-medium italic mt-0.5">" {{ $item->note }} "</p>
                        @endif
                    </div>
                    <p class="font-semibold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                </div>
                @endforeach
            </div>
            <div class="border-t border-gray-200 mt-4 pt-4 flex justify-between">
                <span class="font-bold text-lg">Total</span>
                <span class="font-bold text-lg text-amber-700">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
            </div>
        </div>

        @if($order->notes)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-2">Catatan</h2>
            <p class="text-gray-600">{{ $order->notes }}</p>
        </div>
        @endif

        @if($order->payment_proof)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-2">Bukti Pembayaran</h2>
            <img src="{{ asset('storage/' . $order->payment_proof) }}" alt="Bukti Bayar" class="max-w-xs rounded-lg">
        </div>
        @endif
    </div>

    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Informasi Pelanggan</h2>
            <div class="space-y-2 text-sm">
                <p><span class="text-gray-500">Nama:</span> <span class="font-medium">{{ $order->customer->name }}</span></p>
                <p><span class="text-gray-500">WhatsApp:</span> <span class="font-medium">{{ $order->customer->phone }}</span></p>
                @if($order->address)
                <p><span class="text-gray-500">Alamat:</span> <span>{{ $order->address }}</span></p>
                @endif
                <p><span class="text-gray-500">Tipe:</span> <span class="font-medium">{{ $order->type == 'pickup' ? 'Ambil di Toko' : 'Diantar' }}</span></p>
                <p><span class="text-gray-500">Tgl Ambil:</span> <span class="font-medium">{{ $order->pickup_date->format('d/m/Y') }}</span></p>
                <p><span class="text-gray-500">Tgl Order:</span> <span>{{ $order->order_date->format('d/m/Y H:i') }}</span></p>
            </div>
        </div>

        @if($order->latitude && $order->longitude)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Lokasi Pengiriman</h2>
            <div id="map-order" class="w-full rounded-lg border border-gray-200 overflow-hidden bg-gray-100"></div>
            @if($order->address)
            <div class="mt-4">
                <p class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Alamat Terpilih</p>
                <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">{{ $order->address }}</p>
            </div>
            @endif
            
            <div class="mt-4 flex flex-col gap-2">
                <a href="https://www.google.com/maps?q={{ $order->latitude }},{{ $order->longitude }}" target="_blank" class="flex items-center justify-center gap-2 w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-750 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm font-semibold rounded-lg shadow-sm transition">
                    📍 Buka di Google Maps
                </a>
                @php
                    $storeLat = \App\Models\Setting::getValue('store_latitude', '-7.7609582');
                    $storeLng = \App\Models\Setting::getValue('store_longitude', '110.2529556');
                @endphp
                <a href="https://www.google.com/maps/dir/?api=1&origin={{ $storeLat }},{{ $storeLng }}&destination={{ $order->latitude }},{{ $order->longitude }}" target="_blank" class="flex items-center justify-center gap-2 w-full px-4 py-2.5 bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                    🚗 Rute Pengantaran (Kurir)
                </a>
                <a href="https://waze.com/ul?ll={{ $order->latitude }},{{ $order->longitude }}&navigate=yes" target="_blank" class="flex items-center justify-center gap-2 w-full px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                    🚙 Navigasi Waze
                </a>
            </div>
        </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Aksi</h2>
            <div class="space-y-2">
                <a href="{{ route('admin.orders.invoice', $order) }}" target="_blank" class="block w-full text-center px-4 py-2.5 bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium rounded-lg transition">
                    Print Invoice
                </a>
                <button type="button" onclick="printBluetooth(this)" class="block w-full text-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition flex items-center justify-center gap-1.5"
                    data-order='{!! json_encode([
                        "storeName" => \App\Models\Setting::getValue("store_name", "Mamitha Bakery"),
                        "storeAddress" => \App\Models\Setting::getValue("store_address"),
                        "storePhone" => \App\Models\Setting::getValue("store_phone"),
                        "type" => "invoice",
                        "orderNumber" => $order->order_number,
                        "date" => $order->order_date->format("d/m/Y H:i"),
                        "pickupDate" => $order->pickup_date->format("d/m/Y"),
                        "customerName" => $order->customer->name,
                        "customerPhone" => $order->customer->phone,
                        "orderType" => $order->type,
                        "address" => $order->address,
                        "notes" => $order->notes,
                        "total" => $order->total,
                        "status" => $order->statusLabel(),
                        "items" => $order->items->map(function($i) {
                            $name = $i->product->name . ($i->variant ? " ({$i->variant->name})" : "");
                            if ($i->note) {
                                $name .= "\n  * " . $i->note;
                            }
                            return [
                                "name" => $name,
                                "quantity" => $i->quantity,
                                "price" => $i->price,
                                "subtotal" => $i->subtotal,
                            ];
                        })->toArray(),
                    ]) !!}'>
                    🖨️ Print Struk Bluetooth
                </button>
                <button type="button" onclick="printBluetooth(this)" class="block w-full text-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition flex items-center justify-center gap-1.5"
                    data-order='{!! json_encode([
                        "storeName" => \App\Models\Setting::getValue("store_name", "Mamitha Bakery"),
                        "type" => "kitchen",
                        "orderNumber" => $order->order_number,
                        "pickupDate" => $order->pickup_date->format("d/m/Y"),
                        "customerName" => $order->customer->name,
                        "customerPhone" => $order->customer->phone,
                        "orderType" => $order->type,
                        "address" => $order->address,
                        "notes" => $order->notes,
                        "total" => $order->total,
                        "items" => $order->items->map(function($i) {
                            $name = $i->product->name . ($i->variant ? " ({$i->variant->name})" : "");
                            if ($i->note) {
                                $name .= "\n  * " . $i->note;
                            }
                            return [
                                "name" => $name,
                                "quantity" => $i->quantity,
                                "price" => $i->price,
                                "subtotal" => $i->subtotal,
                            ];
                        })->toArray(),
                    ]) !!}'>
                    🍳 Print Struk Dapur (BT)
                </button>
                <a href="https://wa.me/{{ $order->customer->phone }}" target="_blank" class="block w-full text-center px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition">
                    Hubungi WA
                </a>
                <form action="{{ route('admin.orders.destroy', $order) }}" method="POST" onsubmit="return confirm('Hapus pesanan ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="block w-full text-center px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition">
                        Hapus Pesanan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function setStatus(status) {
        document.getElementById('status-input').value = status;
        event.target.closest('form').submit();
    }
    
    async function printBluetooth(btn) {
        try {
            const data = JSON.parse(btn.getAttribute('data-order'));
            await ThermalPrinter.printReceipt(data);
        } catch (err) {
            if (err.name !== 'NotFoundError') {
                console.error('Bluetooth print error:', err);
            }
        }
    }
</script>
@if($order->latitude && $order->longitude)
<!-- Leaflet JS Library -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    function initOrderMap() {
        const customerLocation = [{{ $order->latitude }}, {{ $order->longitude }}];
        
        const map = L.map('map-order', {
            zoomControl: true,
            attributionControl: false
        }).setView(customerLocation, 16);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19
        }).addTo(map);
        
        L.marker(customerLocation).addTo(map).bindPopup('<b>Lokasi Pelanggan</b>').openPopup();
    }
    
    window.addEventListener('load', () => {
        initOrderMap();
    });
</script>
@endif
@endpush
@endsection
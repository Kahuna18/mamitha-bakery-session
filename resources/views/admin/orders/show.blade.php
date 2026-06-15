@extends('layouts.admin')

@section('title', 'Detail Order')

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
                        <p class="font-medium">{{ $item->product->name }}</p>
                        <p class="text-sm text-gray-500">Rp {{ number_format($item->price, 0, ',', '.') }} x {{ $item->quantity }}</p>
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

        @if($order->latitude && $order->longitude && $googleMapsKey)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Lokasi Pengiriman</h2>
            <div id="map-order" class="w-full h-48 rounded-lg border border-gray-200 overflow-hidden bg-gray-100"></div>
            @if($order->address)
            <p class="text-sm text-gray-600 mt-2">{{ $order->address }}</p>
            @endif
            <a href="https://www.google.com/maps?q={{ $order->latitude }},{{ $order->longitude }}" target="_blank" class="mt-2 inline-block text-sm text-amber-600 hover:text-amber-700 font-medium">Buka di Google Maps &rarr;</a>
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
                        "items" => $order->items->map(fn($i) => [
                            "name" => $i->product->name,
                            "quantity" => $i->quantity,
                            "price" => $i->price,
                            "subtotal" => $i->subtotal,
                        ])->toArray(),
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
                        "items" => $order->items->map(fn($i) => [
                            "name" => $i->product->name,
                            "quantity" => $i->quantity,
                            "price" => $i->price,
                            "subtotal" => $i->subtotal,
                        ])->toArray(),
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
@if($googleMapsKey && $order->latitude && $order->longitude)
<script src="https://maps.googleapis.com/maps/api/js?key={{ $googleMapsKey }}&callback=initOrderMap" async defer></script>
<script>
    function initOrderMap() {
        const loc = { lat: {{ $order->latitude }}, lng: {{ $order->longitude }} };
        const mapEl = document.getElementById('map-order');
        if (!mapEl) return;
        const map = new google.maps.Map(mapEl, {
            center: loc,
            zoom: 15,
            mapTypeControl: false,
            streetViewControl: false,
            fullscreenControl: false,
        });
        new google.maps.Marker({ position: loc, map: map, title: 'Lokasi Pelanggan' });
    }
</script>
@endif
@endpush
@endsection
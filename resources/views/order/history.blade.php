@extends('layouts.app')

@section('title', 'Riwayat Pesanan Saya')

@section('content')
<div class="min-h-screen bg-cream-50 dark:bg-gray-900 py-12 px-4 transition-colors duration-200">
    <div class="max-w-3xl mx-auto space-y-6">
        
        <!-- Header -->
        <div class="text-center sm:text-left space-y-2">
            <h1 class="text-3xl font-black text-amber-900 dark:text-amber-100 font-serif">Riwayat Pesanan</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Temukan dan lacak seluruh pesanan roti hangat Anda di Mamitha Bakery</p>
        </div>

        @forelse($orders as $order)
        <!-- Order Card -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl border border-amber-100/50 dark:border-gray-700/50 shadow-md p-6 hover:shadow-lg transition duration-200 space-y-4">
            
            <!-- Top Header: Date, Number & Status -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-gray-100 dark:border-gray-700/50 pb-4">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-400 uppercase font-bold tracking-wider">No. Pesanan:</span>
                        <span class="font-extrabold text-sm text-amber-800 dark:text-amber-400">{{ $order->order_number }}</span>
                    </div>
                    <p class="text-xs text-gray-450 dark:text-gray-400 mt-0.5">{{ $order->order_date->format('d M Y, H:i') }}</p>
                </div>
                
                <div class="flex items-center gap-2">
                    <!-- Tipe Badge -->
                    <span class="px-2.5 py-1 text-xs font-extrabold rounded-xl shadow-inner {{ $order->type === 'delivery' ? 'bg-blue-50 text-blue-700 dark:bg-blue-950/20 dark:text-blue-400' : 'bg-amber-50 text-amber-800 dark:bg-amber-950/20 dark:text-amber-300' }}">
                        {{ $order->type === 'delivery' ? '🚚 Delivery' : '🏪 Pickup' }}
                    </span>
                    
                    <!-- Status Badge -->
                    @if($order->status == 'pending')
                        <span class="px-3 py-1 bg-yellow-500/10 text-yellow-600 dark:text-yellow-400 text-xs font-black rounded-xl border border-yellow-500/20">⏳ Pending</span>
                    @elseif($order->status == 'confirmed')
                        <span class="px-3 py-1 bg-blue-500/10 text-blue-600 dark:text-blue-400 text-xs font-black rounded-xl border border-blue-500/20">👍 Dikonfirmasi</span>
                    @elseif($order->status == 'producing')
                        <span class="px-3 py-1 bg-orange-500/10 text-orange-600 dark:text-orange-400 text-xs font-black rounded-xl border border-orange-500/20">🔥 Dipanggang</span>
                    @elseif($order->status == 'ready')
                        <span class="px-3 py-1 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-xs font-black rounded-xl border border-emerald-500/20">🛵 Siap</span>
                    @elseif($order->status == 'done')
                        <span class="px-3 py-1 bg-green-500/10 text-green-600 dark:text-green-400 text-xs font-black rounded-xl border border-green-500/20">✅ Selesai</span>
                    @else
                        <span class="px-3 py-1 bg-red-500/10 text-red-600 dark:text-red-400 text-xs font-black rounded-xl border border-red-500/20">❌ Dibatalkan</span>
                    @endif
                </div>
            </div>

            <!-- Items List -->
            <div class="divide-y divide-gray-50 dark:divide-gray-700/30">
                @foreach($order->items as $item)
                <div class="py-3 flex items-center justify-between text-sm">
                    <div class="flex items-center gap-3">
                        @if($item->product && $item->product->image)
                            <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}" class="w-10 h-10 object-cover rounded-xl shadow-sm border border-gray-100 dark:border-gray-800">
                        @else
                            <div class="w-10 h-10 bg-amber-50 dark:bg-amber-950/20 rounded-xl flex items-center justify-center text-xl shadow-inner">🍞</div>
                        @endif
                        <div>
                            <p class="font-bold text-gray-800 dark:text-gray-200">
                                {{ $item->product->name }}
                                @if($item->variant)
                                    <span class="text-xs text-gray-500 dark:text-gray-400 font-normal">({{ $item->variant->name }})</span>
                                @endif
                            </p>
                            <p class="text-xs text-gray-400 mt-0.5">Rp {{ number_format($item->price, 0, ',', '.') }} x {{ $item->quantity }}</p>
                            @if($item->note)
                                <p class="text-[10px] text-amber-700 dark:text-amber-400 italic mt-0.5">Catatan: "{{ $item->note }}"</p>
                            @endif
                        </div>
                    </div>
                    <span class="font-bold text-gray-800 dark:text-gray-200">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                </div>
                @endforeach
            </div>

            <!-- Footer Summary: Total & Details/Track Action -->
            <div class="border-t border-gray-150/40 dark:border-gray-700/50 pt-4 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-center sm:text-left">
                    <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider">Total Pembayaran</p>
                    <p class="text-lg font-black text-amber-800 dark:text-amber-400 mt-0.5">Rp {{ number_format($order->total, 0, ',', '.') }}</p>
                </div>
                
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <!-- WhatsApp Admin Chat -->
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $storeWhatsapp) }}?text=Halo%20Mamitha%20Bakery,%20saya%20ingin%20tanya%20mengenai%20order%20{{ $order->order_number }}" target="_blank" class="flex-1 sm:flex-none px-4 py-2.5 bg-green-500 hover:bg-green-600 text-white font-extrabold text-xs rounded-xl shadow-sm text-center transition flex items-center justify-center gap-1.5 active:scale-95">
                        💬 Hubungi Toko
                    </a>
                    
                    <!-- Track/Detail Link -->
                    <a href="{{ route('order.success', $order->id) }}" class="flex-1 sm:flex-none px-4 py-2.5 bg-gray-900 dark:bg-gray-100 hover:bg-amber-700 dark:hover:bg-amber-500 text-white dark:text-gray-900 font-extrabold text-xs rounded-xl shadow-sm text-center transition flex items-center justify-center gap-1 active:scale-95">
                        📍 Lacak Status &rarr;
                    </a>
                </div>
            </div>
        </div>
        @empty
        <!-- Empty State -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl border border-amber-100/50 dark:border-gray-700/50 p-12 shadow-sm text-center space-y-4">
            <span class="text-5xl block select-none">🥐</span>
            <h3 class="text-xl font-bold text-gray-850 dark:text-gray-100 font-serif">Belum ada pesanan</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 max-w-xs mx-auto leading-relaxed">Anda belum pernah melakukan pemesanan roti hangat Mamitha Bakery. Mari mulai pesan roti pertama Anda!</p>
            <a href="{{ route('order.create') }}" class="inline-block px-6 py-3 bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-700 hover:to-orange-700 text-white font-extrabold text-xs rounded-2xl shadow-md transition transform active:scale-95">
                Pesan Sekarang
            </a>
        </div>
        @endforelse

    </div>
</div>
@endsection

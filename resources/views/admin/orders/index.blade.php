@extends('layouts.admin')

@section('title', 'Order Masuk')

@section('content')
<div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Order Masuk</h1>
        <p class="text-gray-500 text-sm">Kelola semua pesanan pelanggan</p>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
    <form method="GET" class="flex flex-col md:flex-row gap-3">
        <input type="text" name="search" placeholder="Cari nomor order/nama..." value="{{ request('search') }}" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-sm">
        <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg text-sm">
            <option value="">Semua Status</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu</option>
            <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Dikonfirmasi</option>
            <option value="producing" {{ request('status') == 'producing' ? 'selected' : '' }}>Diproses</option>
            <option value="ready" {{ request('status') == 'ready' ? 'selected' : '' }}>Siap</option>
            <option value="done" {{ request('status') == 'done' ? 'selected' : '' }}>Selesai</option>
            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
        </select>
        <select name="filter" class="px-4 py-2 border border-gray-300 rounded-lg text-sm">
            <option value="">Semua Waktu</option>
            <option value="today" {{ request('filter') == 'today' ? 'selected' : '' }}>Hari Ini</option>
            <option value="week" {{ request('filter') == 'week' ? 'selected' : '' }}>Minggu Ini</option>
            <option value="month" {{ request('filter') == 'month' ? 'selected' : '' }}>Bulan Ini</option>
        </select>
        <button type="submit" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium rounded-lg transition">Filter</button>
        <a href="{{ route('admin.orders.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition">Reset</a>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-left">
                    <th class="px-4 py-3 font-semibold text-gray-600">Order #</th>
                    <th class="px-4 py-3 font-semibold text-gray-600">Pelanggan</th>
                    <th class="px-4 py-3 font-semibold text-gray-600">Tanggal</th>
                    <th class="px-4 py-3 font-semibold text-gray-600">Total</th>
                    <th class="px-4 py-3 font-semibold text-gray-600">Status</th>
                    <th class="px-4 py-3 font-semibold text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($orders as $order)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium">{{ $order->order_number }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <p class="font-medium text-gray-900 dark:text-gray-100">{{ $order->customer->name }}</p>
                            @if($order->type == 'delivery')
                                <span class="inline-block px-1.5 py-0.5 text-[10px] font-semibold bg-orange-100 text-orange-850 dark:bg-orange-950/40 dark:text-orange-300 rounded">Delivery</span>
                            @else
                                <span class="inline-block px-1.5 py-0.5 text-[10px] font-semibold bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300 rounded">Pickup</span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500">{{ $order->customer->phone }}</p>
                        @if($order->type == 'delivery' && $order->address)
                            <div class="mt-1 text-xs text-gray-600 dark:text-gray-400 flex items-center gap-1 max-w-xs truncate" title="{{ $order->address }}">
                                📍 {{ $order->address }}
                                @if($order->latitude && $order->longitude)
                                    <a href="https://www.google.com/maps?q={{ $order->latitude }},{{ $order->longitude }}" target="_blank" class="text-amber-600 hover:text-amber-700 dark:text-amber-400 font-bold ml-1 hover:underline">
                                        (Peta)
                                    </a>
                                @endif
                            </div>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $order->order_date->format('d/m/Y H:i') }}</td>
                    <td class="px-4 py-3 font-semibold">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 text-xs font-medium rounded-full
                            @if($order->status == 'pending') bg-yellow-100 text-yellow-700
                            @elseif($order->status == 'confirmed') bg-blue-100 text-blue-700
                            @elseif($order->status == 'producing') bg-orange-100 text-orange-700
                            @elseif($order->status == 'ready') bg-green-100 text-green-700
                            @elseif($order->status == 'done') bg-gray-100 text-gray-700
                            @else bg-red-100 text-red-700 @endif">
                            {{ $order->statusLabel() }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <a href="{{ route('admin.orders.show', $order) }}" class="px-3 py-1.5 bg-amber-600 hover:bg-amber-700 text-white text-xs font-medium rounded-lg transition">Detail</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">Tidak ada pesanan</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-100">
        {{ $orders->appends(request()->query())->links() }}
    </div>
</div>
@endsection

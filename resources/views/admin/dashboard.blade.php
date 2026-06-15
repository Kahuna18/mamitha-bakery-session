@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
    <p class="text-gray-500 text-sm">Ringkasan bisnis hari ini</p>
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 font-medium">Order Hari Ini</p>
                <p class="text-3xl font-bold text-gray-800 mt-1">{{ $todayOrders }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-50 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 font-medium">Diproses</p>
                <p class="text-3xl font-bold text-gray-800 mt-1">{{ $processingOrders }}</p>
            </div>
            <div class="w-12 h-12 bg-orange-50 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 font-medium">Produk Terjual</p>
                <p class="text-3xl font-bold text-gray-800 mt-1">{{ $productsSold }}</p>
            </div>
            <div class="w-12 h-12 bg-green-50 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 font-medium">Pendapatan Hari Ini</p>
                <p class="text-2xl font-bold text-amber-700 mt-1">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</p>
            </div>
            <div class="w-12 h-12 bg-amber-50 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
    </div>
</div>

<div class="grid lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-bold text-gray-800 mb-4">Ringkasan</h2>
        <div class="space-y-3">
            <div class="flex justify-between py-2 border-b border-gray-50">
                <span class="text-gray-600">Total Produk</span>
                <span class="font-semibold">{{ $totalProducts }}</span>
            </div>
            <div class="flex justify-between py-2 border-b border-gray-50">
                <span class="text-gray-600">Total Pelanggan</span>
                <span class="font-semibold">{{ $totalCustomers }}</span>
            </div>
            <div class="flex justify-between py-2 border-b border-gray-50">
                <span class="text-gray-600">Pesanan Menunggu</span>
                <span class="font-semibold text-yellow-600">{{ $pendingOrders }}</span>
            </div>
            <div class="flex justify-between py-2">
                <span class="text-gray-600">Sedang Diproses</span>
                <span class="font-semibold text-orange-600">{{ $processingOrders }}</span>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-bold text-gray-800 mb-4">Pesanan Terbaru</h2>
        <div class="space-y-3">
            @forelse($recentOrders as $order)
            <a href="{{ route('admin.orders.show', $order) }}" class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 transition">
                <div>
                    <p class="font-medium text-sm text-gray-800">{{ $order->customer->name }}</p>
                    <p class="text-xs text-gray-500">{{ $order->order_number }}</p>
                </div>
                <span class="px-2 py-0.5 text-xs font-medium rounded-full
                    @if($order->status == 'pending') bg-yellow-100 text-yellow-700
                    @elseif($order->status == 'confirmed') bg-blue-100 text-blue-700
                    @elseif($order->status == 'producing') bg-orange-100 text-orange-700
                    @elseif($order->status == 'ready') bg-green-100 text-green-700
                    @else bg-gray-100 text-gray-700 @endif">
                    {{ $order->statusLabel() }}
                </span>
            </a>
            @empty
            <p class="text-gray-400 text-center py-4">Belum ada pesanan</p>
            @endforelse
        </div>
    </div>
</div>
@endsection

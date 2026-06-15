@extends('layouts.admin')

@section('title', 'Laporan Keuangan')

@section('content')
<div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Laporan Keuangan</h1>
        <p class="text-gray-500 dark:text-gray-400 text-sm">Analisis detail pendapatan, order, dan produk terlaris</p>
    </div>
    
    <div class="flex flex-wrap items-center gap-2">
        <div class="text-sm text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 px-4 py-2 rounded-lg border border-gray-100 dark:border-gray-700 shadow-sm flex items-center gap-2">
            <span class="inline-block w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
            Rentang Data: 
            <span class="font-semibold text-gray-800 dark:text-gray-200">
                {{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}
            </span>
        </div>
        
        <a href="{{ route('admin.reports.export.pdf', request()->query()) }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg shadow-sm transition duration-200">
            🖨️ Ekspor PDF
        </a>
        <a href="{{ route('admin.reports.export.csv', request()->query()) }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg shadow-sm transition duration-200">
            📊 Ekspor Excel (CSV)
        </a>
    </div>
</div>

<!-- Filter Form -->
<form action="{{ route('admin.reports.index') }}" method="GET" class="flex flex-col md:flex-row items-end gap-4 bg-white dark:bg-gray-800 p-5 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm mb-6 transition-all duration-300">
    <div class="w-full md:w-auto">
        <label for="period" class="block text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">Periode Analisis</label>
        <select name="period" id="period" class="w-full md:w-48 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm px-3 py-2 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-amber-500">
            <option value="today" {{ $period === 'today' ? 'selected' : '' }}>Hari Ini</option>
            <option value="week" {{ $period === 'week' ? 'selected' : '' }}>Minggu Ini</option>
            <option value="month" {{ $period === 'month' ? 'selected' : '' }}>Bulan Ini</option>
            <option value="year" {{ $period === 'year' ? 'selected' : '' }}>Tahun Ini</option>
            <option value="custom" {{ $period === 'custom' ? 'selected' : '' }}>Kustom...</option>
        </select>
    </div>
    
    <div id="custom-date-fields" class="{{ $period === 'custom' ? 'flex' : 'hidden' }} flex-col sm:flex-row gap-4 w-full md:w-auto">
        <div class="w-full sm:w-auto">
            <label for="start_date" class="block text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">Tanggal Mulai</label>
            <input type="date" name="start_date" id="start_date" value="{{ $startDate ? $startDate->format('Y-m-d') : '' }}" class="w-full bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm px-3 py-2 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-amber-500">
        </div>
        <div class="w-full sm:w-auto">
            <label for="end_date" class="block text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">Tanggal Selesai</label>
            <input type="date" name="end_date" id="end_date" value="{{ $endDate ? $endDate->format('Y-m-d') : '' }}" class="w-full bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm px-3 py-2 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-amber-500">
        </div>
    </div>
    
    <button type="submit" class="w-full md:w-auto px-5 py-2 bg-amber-600 hover:bg-amber-700 text-white font-medium rounded-lg text-sm shadow-sm transition-colors duration-200">
        Terapkan Filter
    </button>
</form>

<!-- Stats Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Revenue -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-100 dark:border-gray-700">
        <div class="flex items-center justify-between mb-3">
            <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Total Pendapatan</p>
            <div class="w-10 h-10 bg-amber-50 dark:bg-amber-950/30 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
        <div class="flex items-center mt-2 text-xs">
            @if($revenueGrowth >= 0)
                <span class="text-green-600 dark:text-green-400 font-semibold flex items-center bg-green-50 dark:bg-green-950/20 px-1.5 py-0.5 rounded">
                    <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    +{{ $revenueGrowth }}%
                </span>
            @else
                <span class="text-red-600 dark:text-red-400 font-semibold flex items-center bg-red-50 dark:bg-red-950/20 px-1.5 py-0.5 rounded">
                    <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6"/></svg>
                    {{ $revenueGrowth }}%
                </span>
            @endif
            <span class="text-gray-400 dark:text-gray-500 ml-2">vs periode lalu</span>
        </div>
    </div>

    <!-- Orders -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-100 dark:border-gray-700">
        <div class="flex items-center justify-between mb-3">
            <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Jumlah Pesanan</p>
            <div class="w-10 h-10 bg-blue-50 dark:bg-blue-950/30 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ number_format($totalOrders, 0, ',', '.') }}</p>
        <div class="flex items-center mt-2 text-xs">
            @if($ordersGrowth >= 0)
                <span class="text-green-600 dark:text-green-400 font-semibold flex items-center bg-green-50 dark:bg-green-950/20 px-1.5 py-0.5 rounded">
                    <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    +{{ $ordersGrowth }}%
                </span>
            @else
                <span class="text-red-600 dark:text-red-400 font-semibold flex items-center bg-red-50 dark:bg-red-950/20 px-1.5 py-0.5 rounded">
                    <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6"/></svg>
                    {{ $ordersGrowth }}%
                </span>
            @endif
            <span class="text-gray-400 dark:text-gray-500 ml-2">vs periode lalu</span>
        </div>
    </div>

    <!-- Average Value -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-100 dark:border-gray-700">
        <div class="flex items-center justify-between mb-3">
            <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Rata-rata Pesanan (AOV)</p>
            <div class="w-10 h-10 bg-green-50 dark:bg-green-950/30 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">Rp {{ number_format($averageOrderValue, 0, ',', '.') }}</p>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">Nilai rata-rata belanja per transaksi</p>
    </div>

    <!-- Items Sold -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-100 dark:border-gray-700">
        <div class="flex items-center justify-between mb-3">
            <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Volume & Pelanggan</p>
            <div class="w-10 h-10 bg-purple-50 dark:bg-purple-950/30 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ number_format($totalItemsSold, 0, ',', '.') }} pcs</p>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">
            Dengan <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $newCustomers }}</span> pelanggan baru
        </p>
    </div>
</div>

<!-- Charts Row 1 -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Main Revenue & Orders Chart -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 lg:col-span-2 flex flex-col justify-between">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100">Tren Pendapatan & Pesanan</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400">Grafik harian nilai penjualan</p>
            </div>
            
            <div class="flex gap-2">
                <span class="inline-flex items-center text-xs text-gray-500 font-medium px-2.5 py-1 bg-amber-50 dark:bg-amber-950/20 text-amber-700 dark:text-amber-300 rounded-full">
                    <span class="w-2 h-2 bg-amber-600 rounded-full mr-1.5"></span> Pendapatan
                </span>
            </div>
        </div>
        
        <div class="h-80 w-full relative">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    <!-- Status Breakdown (Pie Chart) -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex flex-col justify-between">
        <div>
            <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-1">Status Pesanan</h2>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-6">Rasio status order dalam periode terpilih</p>
        </div>
        
        <div class="h-60 w-full relative flex items-center justify-center">
            <canvas id="statusChart"></canvas>
        </div>
        
        <div class="grid grid-cols-2 gap-2 mt-4 text-xs">
            <div class="flex items-center gap-1.5">
                <span class="w-2.5 h-2.5 bg-green-500 rounded-full"></span>
                <span class="text-gray-600 dark:text-gray-400">Done ({{ $completedOrders }})</span>
            </div>
            <div class="flex items-center gap-1.5">
                <span class="w-2.5 h-2.5 bg-red-500 rounded-full"></span>
                <span class="text-gray-600 dark:text-gray-400">Cancelled ({{ $cancelledOrders }})</span>
            </div>
            <div class="flex items-center gap-1.5">
                <span class="w-2.5 h-2.5 bg-yellow-500 rounded-full"></span>
                <span class="text-gray-600 dark:text-gray-400">Pending</span>
            </div>
            <div class="flex items-center gap-1.5">
                <span class="w-2.5 h-2.5 bg-blue-500 rounded-full"></span>
                <span class="text-gray-600 dark:text-gray-400">Lainnya</span>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row 2 -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Peak Hours (Bar Chart) -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 lg:col-span-2 flex flex-col justify-between">
        <div>
            <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-1">Distribusi Jam Ramai</h2>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-6">Visualisasi jam operasional tersibuk berdasarkan jumlah transaksi</p>
        </div>
        
        <div class="h-64 w-full relative">
            <canvas id="hourlyChart"></canvas>
        </div>
    </div>

    <!-- Revenue by Order Type (Doughnut Chart) -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex flex-col justify-between">
        <div>
            <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-1">Metode Pemesanan</h2>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-6">Perbandingan order Delivery vs Pickup</p>
        </div>
        
        <div class="h-56 w-full relative flex items-center justify-center">
            <canvas id="typeChart"></canvas>
        </div>
        
        <div class="space-y-2.5 mt-4">
            @php
                $pickupRev = $revenueByType['pickup']['revenue'] ?? 0;
                $deliveryRev = $revenueByType['delivery']['revenue'] ?? 0;
                $totalRevType = $pickupRev + $deliveryRev;
                $pickupPercent = $totalRevType > 0 ? round(($pickupRev / $totalRevType) * 100) : 0;
                $deliveryPercent = $totalRevType > 0 ? round(($deliveryRev / $totalRevType) * 100) : 0;
            @endphp
            <div class="flex items-center justify-between text-xs">
                <div class="flex items-center gap-1.5">
                    <span class="w-3 h-3 bg-amber-600 rounded-full"></span>
                    <span class="text-gray-600 dark:text-gray-400">Ambil Sendiri (Pickup)</span>
                </div>
                <span class="font-bold text-gray-700 dark:text-gray-300">{{ $pickupPercent }}% (Rp {{ number_format($pickupRev, 0, ',', '.') }})</span>
            </div>
            <div class="flex items-center justify-between text-xs">
                <div class="flex items-center gap-1.5">
                    <span class="w-3 h-3 bg-blue-500 rounded-full"></span>
                    <span class="text-gray-600 dark:text-gray-400">Kirim Alamat (Delivery)</span>
                </div>
                <span class="font-bold text-gray-700 dark:text-gray-300">{{ $deliveryPercent }}% (Rp {{ number_format($deliveryRev, 0, ',', '.') }})</span>
            </div>
        </div>
    </div>
</div>

<!-- Tables Row -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Top Products Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex flex-col">
        <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">10 Produk Terlaris</h2>
        
        <div class="overflow-x-auto flex-1">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50 text-left border-b border-gray-100 dark:border-gray-700">
                        <th class="px-4 py-2.5 font-semibold text-gray-600 dark:text-gray-400 w-12">No</th>
                        <th class="px-4 py-2.5 font-semibold text-gray-600 dark:text-gray-400">Nama Produk</th>
                        <th class="px-4 py-2.5 font-semibold text-gray-600 dark:text-gray-400 text-center">Jumlah Terjual</th>
                        <th class="px-4 py-2.5 font-semibold text-gray-600 dark:text-gray-400 text-right">Total Pendapatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/40">
                    @forelse($topProducts as $index => $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                            <td class="px-4 py-3 text-gray-500 font-medium">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 flex items-center gap-3">
                                @if($item->product && $item->product->image)
                                    <img src="{{ asset('storage/' . $item->product->image) }}" class="w-8 h-8 object-cover rounded-lg">
                                @else
                                    <div class="w-8 h-8 bg-amber-50 dark:bg-amber-950/40 rounded-lg flex items-center justify-center text-amber-500 text-xs">🍞</div>
                                @endif
                                <span class="font-medium text-gray-800 dark:text-gray-200">
                                    {{ $item->product ? $item->product->name : 'Produk Terhapus' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center text-gray-700 dark:text-gray-300 font-semibold">{{ $item->total_qty }} pcs</td>
                            <td class="px-4 py-3 text-right text-gray-900 dark:text-gray-100 font-bold">Rp {{ number_format($item->total_revenue, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-400">Belum ada data penjualan produk</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Completed Orders -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex flex-col">
        <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">Aktivitas Transaksi Selesai (Terbaru)</h2>
        
        <div class="overflow-x-auto flex-1">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50 text-left border-b border-gray-100 dark:border-gray-700">
                        <th class="px-4 py-2.5 font-semibold text-gray-600 dark:text-gray-400">No. Order</th>
                        <th class="px-4 py-2.5 font-semibold text-gray-600 dark:text-gray-400">Pelanggan</th>
                        <th class="px-4 py-2.5 font-semibold text-gray-600 dark:text-gray-400 text-center">Tipe</th>
                        <th class="px-4 py-2.5 font-semibold text-gray-600 dark:text-gray-400 text-right">Total Belanja</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/40">
                    @forelse($recentCompletedOrders as $order)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                            <td class="px-4 py-3 font-semibold text-amber-700 dark:text-amber-400">
                                <a href="{{ route('admin.orders.show', $order) }}" class="hover:underline">
                                    {{ $order->order_number }}
                                </a>
                            </td>
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-800 dark:text-gray-200">{{ $order->customer->name }}</p>
                                <p class="text-xs text-gray-400">{{ $order->order_date->format('d/m/Y H:i') }}</p>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ $order->type === 'delivery' ? 'bg-blue-100 text-blue-800 dark:bg-blue-950/40 dark:text-blue-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-950/40 dark:text-amber-300' }}">
                                    {{ ucfirst($order->type) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right text-gray-900 dark:text-gray-100 font-bold">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-400">Belum ada transaksi selesai pada periode ini</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle custom date range fields
        const periodSelect = document.getElementById('period');
        const customDateFields = document.getElementById('custom-date-fields');
        
        periodSelect.addEventListener('change', function() {
            if (this.value === 'custom') {
                customDateFields.classList.remove('hidden');
                customDateFields.classList.add('flex');
            } else {
                customDateFields.classList.add('hidden');
                customDateFields.classList.remove('flex');
            }
        });
        
        // Helper to check dark mode
        const isDark = document.documentElement.classList.contains('dark') || document.documentElement.getAttribute('data-theme') === 'dark';
        const labelColor = isDark ? '#a0a3b1' : '#6b7280';
        const gridColor = isDark ? 'rgba(255, 255, 255, 0.08)' : 'rgba(0, 0, 0, 0.04)';

        // 1. Revenue daily trend chart
        const dailyRevenue = @json($dailyRevenue);
        const revenueLabels = dailyRevenue.map(item => {
            const date = new Date(item.date);
            return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
        });
        const revenueValues = dailyRevenue.map(item => item.revenue);
        const orderCounts = dailyRevenue.map(item => item.orders);

        const ctxRev = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctxRev, {
            type: 'line',
            data: {
                labels: revenueLabels,
                datasets: [
                    {
                        label: 'Pendapatan (Rp)',
                        data: revenueValues,
                        borderColor: '#d97706',
                        backgroundColor: 'rgba(217, 119, 6, 0.1)',
                        borderWidth: 3,
                        pointBackgroundColor: '#d97706',
                        pointHoverRadius: 6,
                        fill: true,
                        tension: 0.35,
                        yAxisID: 'y'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        padding: 12,
                        backgroundColor: isDark ? '#1e1e2e' : '#ffffff',
                        titleColor: isDark ? '#f1f1f4' : '#1f2937',
                        bodyColor: isDark ? '#a0a3b1' : '#4b5563',
                        borderColor: isDark ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.06)',
                        borderWidth: 1,
                        callbacks: {
                            label: function(context) {
                                return 'Pendapatan: Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: labelColor,
                            callback: function(value) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID', { notation: 'compact' }).format(value);
                            }
                        },
                        grid: {
                            color: gridColor
                        }
                    },
                    x: {
                        ticks: {
                            color: labelColor
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // 2. Order status chart (Doughnut)
        const statusBreakdown = @json($statusBreakdown);
        const statusLabels = Object.keys(statusBreakdown).map(k => k.charAt(0).toUpperCase() + k.slice(1));
        const statusValues = Object.values(statusBreakdown);
        
        const statusColors = {
            'pending': '#eab308', // yellow-500
            'confirmed': '#3b82f6', // blue-500
            'producing': '#f97316', // orange-500
            'ready': '#10b981', // emerald-500
            'done': '#22c55e', // green-500
            'cancelled': '#ef4444' // red-500
        };
        
        const backgroundColors = Object.keys(statusBreakdown).map(k => statusColors[k] || '#9ca3af');

        const ctxStatus = document.getElementById('statusChart').getContext('2d');
        new Chart(ctxStatus, {
            type: 'doughnut',
            data: {
                labels: statusLabels,
                datasets: [{
                    data: statusValues,
                    backgroundColor: backgroundColors,
                    borderWidth: isDark ? 2 : 1,
                    borderColor: isDark ? '#1e1e2e' : '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                cutout: '65%'
            }
        });

        // 3. Peak hours chart (Bar)
        const hourlyOrders = @json($hourlyOrders);
        
        // Populate all 24 hours with default 0 if missing
        const hours24 = Array.from({length: 24}, (_, i) => i);
        const hourlyValues = hours24.map(h => {
            const found = hourlyOrders.find(item => parseInt(item.hour) === h);
            return found ? found.count : 0;
        });
        
        const hourlyLabels = hours24.map(h => `${h.toString().padStart(2, '0')}:00`);

        const ctxHourly = document.getElementById('hourlyChart').getContext('2d');
        new Chart(ctxHourly, {
            type: 'bar',
            data: {
                labels: hourlyLabels,
                datasets: [{
                    label: 'Jumlah Transaksi',
                    data: hourlyValues,
                    backgroundColor: '#d97706',
                    borderRadius: 4,
                    hoverBackgroundColor: '#b45309'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: labelColor,
                            stepSize: 1
                        },
                        grid: {
                            color: gridColor
                        }
                    },
                    x: {
                        ticks: {
                            color: labelColor,
                            maxRotation: 45,
                            minRotation: 45
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // 4. Order type chart (Pie/Doughnut)
        const revenueByType = @json($revenueByType);
        const typeLabels = ['Pickup (Ambil)', 'Delivery (Kirim)'];
        const typeValues = [
            revenueByType.pickup ? revenueByType.pickup.count : 0,
            revenueByType.delivery ? revenueByType.delivery.count : 0
        ];

        const ctxType = document.getElementById('typeChart').getContext('2d');
        new Chart(ctxType, {
            type: 'pie',
            data: {
                labels: typeLabels,
                datasets: [{
                    data: typeValues,
                    backgroundColor: ['#d97706', '#3b82f6'],
                    borderWidth: isDark ? 2 : 1,
                    borderColor: isDark ? '#1e1e2e' : '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection

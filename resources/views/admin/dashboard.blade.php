@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
        <p class="text-gray-500 text-sm">Ringkasan bisnis hari ini</p>
    </div>
    
    <!-- Tab Navigation -->
    <div class="bg-gray-100 dark:bg-gray-800 p-1 rounded-xl flex gap-1 self-start sm:self-auto shadow-inner border border-gray-200 dark:border-gray-700">
        <a href="?tab=dashboard" class="px-4 py-2 rounded-lg text-xs font-bold transition-all {{ request('tab', 'dashboard') === 'dashboard' ? 'bg-white dark:bg-gray-700 text-gray-800 dark:text-white shadow-sm' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
            📊 Ringkasan Bisnis
        </a>
        <a href="?tab=member" class="px-4 py-2 rounded-lg text-xs font-bold transition-all {{ request('tab') === 'member' ? 'bg-white dark:bg-gray-700 text-gray-800 dark:text-white shadow-sm' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
            ⭐ Kelola & Diskon Member
        </a>
    </div>
</div>

@if(request('tab') === 'member')
    <!-- MEMBER TAB CONTENT -->
    <div class="space-y-6">
        <!-- Member Discount Settings Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4 font-serif">Pengaturan Diskon Member</h2>
            <form action="{{ route('admin.update-member-discount') }}" method="POST" class="space-y-4 max-w-md">
                @csrf
                <div class="flex items-center">
                    <input type="checkbox" name="discount_enabled" id="discount_enabled" value="1" {{ $discountEnabled ? 'checked' : '' }} class="w-4 h-4 text-amber-600 border-gray-300 rounded focus:ring-amber-500 transition cursor-pointer">
                    <label for="discount_enabled" class="ml-2 text-sm font-semibold text-gray-700 dark:text-gray-300 cursor-pointer">Aktifkan Potongan Diskon Otomatis untuk Member</label>
                </div>
                
                <div class="space-y-1">
                    <label for="discount_percentage" class="block text-xs font-bold text-gray-500 uppercase tracking-wider">Persentase Diskon (%)</label>
                    <div class="flex items-center gap-2">
                        <input type="number" name="discount_percentage" id="discount_percentage" value="{{ $discountPercentage }}" min="1" max="100" class="w-24 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-amber-500 focus:border-amber-500 dark:bg-gray-800" required>
                        <span class="text-sm font-semibold text-gray-600">%</span>
                    </div>
                </div>

                <button type="submit" class="px-4 py-2.5 bg-amber-600 hover:bg-amber-700 text-white text-xs font-black rounded-lg shadow-sm transition active:scale-95 cursor-pointer">
                    Simpan Pengaturan Diskon
                </button>
            </form>
        </div>

        <!-- Members Data List Card -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/60 overflow-hidden">
            <div class="p-5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between flex-wrap gap-4">
                <div>
                    <h2 class="text-lg font-bold text-gray-800 dark:text-white font-serif">Data Anggota Member</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Total member terdaftar: {{ count($members) }} pelanggan</p>
                </div>
                <button onclick="openAddMemberModal()" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-xs font-black rounded-lg shadow-sm transition active:scale-95 cursor-pointer">
                    + Tambah Member Baru
                </button>
            </div>

            <!-- Responsive Desktop Table -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-900/20 text-left">
                            <th class="px-5 py-3.5 font-bold text-gray-600 dark:text-gray-300 text-xs uppercase tracking-wider border-b border-gray-100 dark:border-gray-700">ID Member</th>
                            <th class="px-5 py-3.5 font-bold text-gray-600 dark:text-gray-300 text-xs uppercase tracking-wider border-b border-gray-100 dark:border-gray-700">Nama Lengkap</th>
                            <th class="px-5 py-3.5 font-bold text-gray-600 dark:text-gray-300 text-xs uppercase tracking-wider border-b border-gray-100 dark:border-gray-700">WhatsApp</th>
                            <th class="px-5 py-3.5 font-bold text-gray-600 dark:text-gray-300 text-xs uppercase tracking-wider border-b border-gray-100 dark:border-gray-700">Rank Status</th>
                            <th class="px-5 py-3.5 font-bold text-gray-600 dark:text-gray-300 text-xs uppercase tracking-wider border-b border-gray-100 dark:border-gray-700">Total Poin</th>
                            <th class="px-5 py-3.5 font-bold text-gray-600 dark:text-gray-300 text-xs uppercase tracking-wider border-b border-gray-100 dark:border-gray-700">Total Order</th>
                            <th class="px-5 py-3.5 font-bold text-gray-600 dark:text-gray-300 text-xs uppercase tracking-wider border-b border-gray-100 dark:border-gray-700">Kapan Bergabung</th>
                            <th class="px-5 py-3.5 font-bold text-gray-600 dark:text-gray-300 text-xs uppercase tracking-wider border-b border-gray-100 dark:border-gray-700">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($members as $mbr)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-900/10">
                            <td class="px-5 py-4 font-mono text-xs font-bold text-gray-400">MTH-MBR-{{ str_pad($mbr->id, 5, '0', STR_PAD_LEFT) }}</td>
                            <td class="px-5 py-4 font-semibold text-gray-800 dark:text-white">{{ $mbr->name }}</td>
                            <td class="px-5 py-4 text-gray-600 dark:text-gray-300">
                                @if($mbr->phone)
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $mbr->phone) }}" target="_blank" class="hover:text-amber-500 text-amber-600 dark:text-amber-400 transition inline-flex items-center gap-1 font-medium">
                                        💬 {{ $mbr->phone }}
                                    </a>
                                @else
                                    <span class="text-gray-400 dark:text-gray-500 font-medium">-</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <span class="bg-amber-100 dark:bg-amber-950/40 text-amber-800 dark:text-amber-300 text-[10px] font-black px-2.5 py-1 rounded-full uppercase tracking-wider">
                                    {{ $mbr->rank_badge }} {{ $mbr->rank_name }}
                                </span>
                            </td>
                            <td class="px-5 py-4 font-black text-amber-700 dark:text-amber-400">{{ $mbr->points }} pts</td>
                            <td class="px-5 py-4 font-semibold text-gray-700 dark:text-gray-200">{{ $mbr->orders_count }} order</td>
                            <td class="px-5 py-4 text-xs text-gray-500 dark:text-gray-400 font-medium">{{ $mbr->created_at->format('d M Y, H:i') }}</td>
                            <td class="px-5 py-4">
                                <div class="flex gap-2 items-center flex-wrap">
                                    <button onclick="openEditPointsModal({{ $mbr->id }}, '{{ addslashes($mbr->name) }}', {{ $mbr->points }})" class="px-2.5 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 dark:bg-amber-950/20 dark:text-amber-300 text-xs font-bold rounded-lg transition active:scale-95 cursor-pointer flex items-center gap-1" title="Edit Poin Manual">
                                        ✏️ Edit Poin
                                    </button>
                                    <form action="{{ route('admin.toggle-customer-member', $mbr) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menonaktifkan status keanggotaan {{ addslashes($mbr->name) }}?')">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1.5 bg-orange-50 hover:bg-orange-100 text-orange-700 dark:bg-orange-950/20 dark:text-orange-300 text-xs font-bold rounded-lg transition active:scale-95 cursor-pointer flex items-center gap-1" title="Nonaktifkan Member">
                                            🚫 Nonaktifkan
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.delete-member', $mbr) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus keanggotaan {{ addslashes($mbr->name) }}? Jika member memiliki riwayat transaksi, status member akan dinonaktifkan dan poin akan direset ke 0. Jika tidak ada transaksi, data pelanggan akan dihapus sepenuhnya.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 dark:bg-rose-950/20 dark:text-rose-300 text-xs font-bold rounded-lg transition active:scale-95 cursor-pointer flex items-center gap-1" title="Hapus Member">
                                            🗑️ Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-5 py-8 text-center text-gray-400">Belum ada pelanggan bergabung sebagai member</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Responsive Mobile Cards -->
            <div class="block md:hidden p-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @forelse($members as $mbr)
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/60 p-5 space-y-4 hover:shadow-md transition duration-300">
                        <!-- Card Header: ID and Rank Badge -->
                        <div class="flex justify-between items-center">
                            <span class="font-mono text-xs font-bold text-gray-400">MTH-MBR-{{ str_pad($mbr->id, 5, '0', STR_PAD_LEFT) }}</span>
                            <span class="bg-amber-100 dark:bg-amber-950/40 text-amber-800 dark:text-amber-300 text-[10px] font-black px-2.5 py-1 rounded-full uppercase tracking-wider">
                                {{ $mbr->rank_badge }} {{ $mbr->rank_name }}
                            </span>
                        </div>
                        
                        <!-- Card Body: Name & Phone -->
                        <div>
                            <h3 class="text-base font-bold text-gray-850 dark:text-white">{{ $mbr->name }}</h3>
                            @if($mbr->phone)
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $mbr->phone) }}" target="_blank" class="text-xs text-amber-600 dark:text-amber-400 hover:underline transition flex items-center gap-1 mt-1 font-medium">
                                    💬 {{ $mbr->phone }}
                                </a>
                            @else
                                <span class="text-xs text-gray-450 dark:text-gray-500 block mt-1">Tidak ada nomor WhatsApp</span>
                            @endif
                        </div>

                        <!-- Stats Grid -->
                        <div class="grid grid-cols-2 gap-2 bg-gray-50 dark:bg-gray-900/40 p-3 rounded-xl text-center">
                            <div>
                                <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Total Poin</p>
                                <p class="text-sm font-black text-amber-600 dark:text-amber-400 mt-0.5">{{ $mbr->points }} pts</p>
                            </div>
                            <div>
                                <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Total Order</p>
                                <p class="text-sm font-bold text-gray-800 dark:text-gray-200 mt-0.5">{{ $mbr->orders_count }} order</p>
                            </div>
                        </div>

                        <!-- Join Date -->
                        <div class="text-[11px] text-gray-400 flex justify-between items-center border-t border-gray-100 dark:border-gray-700/60 pt-3">
                            <span>Bergabung sejak:</span>
                            <span class="font-medium text-gray-600 dark:text-gray-300">{{ $mbr->created_at->format('d M Y, H:i') }}</span>
                        </div>

                        <!-- Action Group -->
                        <div class="grid grid-cols-3 gap-2 pt-2">
                            <button onclick="openEditPointsModal({{ $mbr->id }}, '{{ addslashes($mbr->name) }}', {{ $mbr->points }})" class="py-2.5 bg-amber-50 hover:bg-amber-100 text-amber-700 dark:bg-amber-950/20 dark:text-amber-300 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1 active:scale-95 cursor-pointer">
                                ✏️ Poin
                            </button>
                            
                            <form action="{{ route('admin.toggle-customer-member', $mbr) }}" method="POST" onsubmit="return confirm('Yakin ingin menonaktifkan status keanggotaan {{ addslashes($mbr->name) }}?')" class="w-full inline">
                                @csrf
                                <button type="submit" class="w-full py-2.5 bg-orange-50 hover:bg-orange-100 text-orange-700 dark:bg-orange-950/20 dark:text-orange-300 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1 active:scale-95 cursor-pointer">
                                    🚫 Nonaktif
                                </button>
                            </form>

                            <form action="{{ route('admin.delete-member', $mbr) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus keanggotaan {{ addslashes($mbr->name) }}? Jika member memiliki riwayat transaksi, status member akan dinonaktifkan dan poin akan direset ke 0. Jika tidak ada transaksi, data pelanggan akan dihapus sepenuhnya.')" class="w-full inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full py-2.5 bg-rose-50 hover:bg-rose-100 text-rose-700 dark:bg-rose-950/20 dark:text-rose-300 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1 active:scale-95 cursor-pointer">
                                    🗑️ Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-full py-8 text-center text-gray-400">Belum ada pelanggan bergabung sebagai member</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@else
    <!-- ORIGINAL DASHBOARD CONTENT -->
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
@endif

<!-- Modal 1: Edit Points Modal -->
<div id="edit-points-modal" class="fixed inset-0 bg-gray-900/60 dark:bg-black/85 flex items-center justify-center p-4 z-[9999] hidden opacity-0 transition-opacity duration-300">
    <div class="bg-white dark:bg-gray-800 rounded-3xl w-full max-w-md p-6 border border-gray-100 dark:border-gray-700 shadow-2xl transform scale-95 transition-all duration-300" id="edit-points-card">
        <div class="flex justify-between items-center border-b border-gray-100 dark:border-gray-750 pb-4 mb-4">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white">Ubah Poin Member</h3>
            <button onclick="closeEditPointsModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 font-bold text-lg">&times;</button>
        </div>
        <form id="edit-points-form" method="POST" class="space-y-4">
            @csrf
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400">Member: <span id="edit-points-member-name" class="font-bold text-gray-850 dark:text-white"></span></p>
            </div>
            <div class="space-y-1">
                <label for="points-input" class="block text-xs font-bold text-gray-500 uppercase tracking-wider">Total Poin Baru</label>
                <input type="number" name="points" id="points-input" min="0" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-750 dark:bg-gray-905 rounded-lg text-sm">
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="closeEditPointsModal()" class="px-4 py-2 bg-gray-100 dark:bg-gray-750 text-gray-700 dark:text-gray-300 text-xs font-bold rounded-lg">Batal</button>
                <button type="submit" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-xs font-black rounded-lg">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 2: Add Member Modal -->
<div id="add-member-modal" class="fixed inset-0 bg-gray-900/60 dark:bg-black/85 flex items-center justify-center p-4 z-[9999] hidden opacity-0 transition-opacity duration-300">
    <div class="bg-white dark:bg-gray-800 rounded-3xl w-full max-w-md p-6 border border-gray-100 dark:border-gray-700 shadow-2xl transform scale-95 transition-all duration-300" id="add-member-card">
        <div class="flex justify-between items-center border-b border-gray-100 dark:border-gray-750 pb-4 mb-4">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white">Tambah Member Baru</h3>
            <button onclick="closeAddMemberModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 font-bold text-lg">&times;</button>
        </div>
        <form action="{{ route('admin.add-member') }}" method="POST" class="space-y-4">
            @csrf
            <div class="space-y-1">
                <label for="customer_id" class="block text-xs font-bold text-gray-500 uppercase tracking-wider">Pilih Pelanggan (Guest)</label>
                <select name="customer_id" id="customer_id" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 dark:bg-gray-905 rounded-lg text-sm">
                    <option value="">-- Pilih Pelanggan --</option>
                    @foreach($guests as $guest)
                        <option value="{{ $guest->id }}">{{ $guest->name }} ({{ $guest->phone ?: 'Tidak ada WhatsApp' }})</option>
                    @endforeach
                </select>
            </div>
            <div class="space-y-1">
                <label for="add-points-input" class="block text-xs font-bold text-gray-500 uppercase tracking-wider">Poin Awal (Opsional)</label>
                <input type="number" name="points" id="add-points-input" value="0" min="0" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-750 dark:bg-gray-905 rounded-lg text-sm">
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="closeAddMemberModal()" class="px-4 py-2 bg-gray-100 dark:bg-gray-750 text-gray-700 dark:text-gray-300 text-xs font-bold rounded-lg">Batal</button>
                <button type="submit" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-xs font-black rounded-lg">Jadikan Member</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openEditPointsModal(id, name, points) {
        const modal = document.getElementById('edit-points-modal');
        const card = document.getElementById('edit-points-card');
        const form = document.getElementById('edit-points-form');
        const nameSpan = document.getElementById('edit-points-member-name');
        const pointsInput = document.getElementById('points-input');
        
        nameSpan.textContent = name;
        pointsInput.value = points;
        form.action = `/admin/customers/${id}/update-points`;
        
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            card.classList.remove('scale-95');
            card.classList.add('scale-100');
        }, 10);
    }
    
    function closeEditPointsModal() {
        const modal = document.getElementById('edit-points-modal');
        const card = document.getElementById('edit-points-card');
        card.classList.remove('scale-100');
        card.classList.add('scale-95');
        modal.classList.add('opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    function openAddMemberModal() {
        const modal = document.getElementById('add-member-modal');
        const card = document.getElementById('add-member-card');
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            card.classList.remove('scale-95');
            card.classList.add('scale-100');
        }, 10);
    }
    
    function closeAddMemberModal() {
        const modal = document.getElementById('add-member-modal');
        const card = document.getElementById('add-member-card');
        card.classList.remove('scale-100');
        card.classList.add('scale-95');
        modal.classList.add('opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }
</script>
@endpush
@endsection

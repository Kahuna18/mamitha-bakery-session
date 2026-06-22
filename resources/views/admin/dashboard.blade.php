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
                <div class="flex items-center gap-2 flex-wrap">
                    <form action="{{ route('admin.customers.reset-members') }}" method="POST" onsubmit="return confirm('PENTING: Tindakan ini akan menghapus SELURUH data pelanggan/member beserta riwayat transaksi mereka untuk mereset nomor urut member kembali ke 1. Apakah Anda yakin?')">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-black rounded-lg shadow-sm transition active:scale-95 cursor-pointer flex items-center gap-1">
                            🗑️ Reset Seluruh Member
                        </button>
                    </form>
                    <button onclick="openAddMemberModal()" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-xs font-black rounded-lg shadow-sm transition active:scale-95 cursor-pointer">
                        + Tambah Member Baru
                    </button>
                </div>
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
    <!-- REDESIGNED MODERN DASHBOARD CONTENT -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800 dark:text-white">Store Overview</h2>
            <p class="text-gray-500 dark:text-gray-400 text-xs mt-0.5">Here's what your store is performing today</p>
        </div>
        <div class="text-xs text-gray-400 dark:text-gray-500 font-medium">
            Terakhir diperbarui: {{ now()->format('d M Y, H:i') }}
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        <!-- Card 1: Revenue -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/60 p-5 glow-card">
            <div class="flex items-center justify-between">
                <div class="space-y-1">
                    <p class="text-xs text-gray-500 dark:text-gray-405 font-semibold uppercase tracking-wider">Pendapatan Hari Ini</p>
                    <p class="text-2xl font-black text-gray-800 dark:text-amber-500">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</p>
                    <div class="flex items-center gap-1 mt-1 text-[11px] font-bold text-emerald-650 dark:text-emerald-450 bg-emerald-50 dark:bg-emerald-950/20 px-2 py-0.5 rounded-full w-max">
                        <span>↑ 12.4%</span>
                        <span class="text-gray-405 dark:text-gray-500 font-medium">vs kemarin</span>
                    </div>
                </div>
                <div class="flex flex-col items-end gap-2">
                    <div class="w-10 h-10 bg-amber-50 dark:bg-amber-950/20 rounded-xl flex items-center justify-center text-amber-600 dark:text-amber-500 shadow-sm border border-amber-100/50 dark:border-amber-900/30">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <!-- Inline SVG sparkline (Warm Amber) -->
                    <svg class="w-16 h-8 text-amber-550 dark:text-amber-500 glow-line-chart" viewBox="0 0 100 30" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                        <path d="M 0 25 Q 20 15 40 22 T 80 8 T 100 5" />
                        <path d="M 0 25 Q 20 15 40 22 T 80 8 T 100 5 L 100 30 L 0 30 Z" fill="rgba(245, 158, 11, 0.05)" stroke="none" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Card 2: Orders -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/60 p-5 glow-card">
            <div class="flex items-center justify-between">
                <div class="space-y-1">
                    <p class="text-xs text-gray-500 dark:text-gray-450 font-semibold uppercase tracking-wider">Order Hari Ini</p>
                    <p class="text-2xl font-black text-gray-800 dark:text-white">{{ $todayOrders }}</p>
                    <div class="flex items-center gap-1 mt-1 text-[11px] font-bold text-emerald-650 dark:text-emerald-450 bg-emerald-50 dark:bg-emerald-950/20 px-2 py-0.5 rounded-full w-max">
                        <span>↑ 5.2%</span>
                        <span class="text-gray-405 dark:text-gray-500 font-medium">vs kemarin</span>
                    </div>
                </div>
                <div class="flex flex-col items-end gap-2">
                    <div class="w-10 h-10 bg-blue-50 dark:bg-blue-950/20 rounded-xl flex items-center justify-center text-blue-600 dark:text-blue-450 shadow-sm border border-blue-100/50 dark:border-blue-900/30">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                    <!-- Inline SVG sparkline (Blue) -->
                    <svg class="w-16 h-8 text-blue-500 dark:text-blue-450 glow-line-chart" viewBox="0 0 100 30" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                        <path d="M 0 20 Q 25 28 50 12 T 100 3" />
                        <path d="M 0 20 Q 25 28 50 12 T 100 3 L 100 30 L 0 30 Z" fill="rgba(59, 130, 246, 0.05)" stroke="none" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Card 3: Products Sold -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/60 p-5 glow-card">
            <div class="flex items-center justify-between">
                <div class="space-y-1">
                    <p class="text-xs text-gray-500 dark:text-gray-405 font-semibold uppercase tracking-wider">Produk Terjual</p>
                    <p class="text-2xl font-black text-gray-850 dark:text-white">{{ $productsSold }}</p>
                    <div class="flex items-center gap-1 mt-1 text-[11px] font-bold text-amber-655 dark:text-amber-450 bg-amber-50 dark:bg-amber-950/20 px-2 py-0.5 rounded-full w-max">
                        <span>Stabil</span>
                        <span class="text-gray-455 dark:text-gray-500 font-medium">hari ini</span>
                    </div>
                </div>
                <div class="flex flex-col items-end gap-2">
                    <div class="w-10 h-10 bg-emerald-50 dark:bg-emerald-950/20 rounded-xl flex items-center justify-center text-emerald-600 dark:text-emerald-450 shadow-sm border border-emerald-100/50 dark:border-emerald-900/30">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                    <!-- Inline SVG sparkline (Green) -->
                    <svg class="w-16 h-8 text-emerald-500 dark:text-emerald-450 glow-line-chart" viewBox="0 0 100 30" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                        <path d="M 0 15 Q 30 5 60 18 T 100 10" />
                        <path d="M 0 15 Q 30 5 60 18 T 100 10 L 100 30 L 0 30 Z" fill="rgba(16, 185, 129, 0.05)" stroke="none" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Card 4: Total Customers -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/60 p-5 glow-card">
            <div class="flex items-center justify-between">
                <div class="space-y-1">
                    <p class="text-xs text-gray-500 dark:text-gray-405 font-semibold uppercase tracking-wider">Total Pelanggan</p>
                    <p class="text-2xl font-black text-gray-800 dark:text-white">{{ $totalCustomers }}</p>
                    <div class="flex items-center gap-1 mt-1 text-[11px] font-bold text-blue-650 dark:text-blue-450 bg-blue-50 dark:bg-blue-950/20 px-2 py-0.5 rounded-full w-max">
                        <span>Aktif</span>
                        <span class="text-gray-405 dark:text-gray-500 font-medium">terdaftar</span>
                    </div>
                </div>
                <div class="flex flex-col items-end gap-2">
                    <div class="w-10 h-10 bg-teal-50 dark:bg-teal-950/20 rounded-xl flex items-center justify-center text-teal-600 dark:text-teal-400 shadow-sm border border-teal-100/50 dark:border-teal-900/30">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <!-- Inline SVG sparkline (Teal) -->
                    <svg class="w-16 h-8 text-teal-500 dark:text-teal-450 glow-line-chart" viewBox="0 0 100 30" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                        <path d="M 0 28 Q 20 18 45 22 T 90 6 T 100 4" />
                        <path d="M 0 28 Q 20 18 45 22 T 90 6 T 100 4 L 100 30 L 0 30 Z" fill="rgba(20, 184, 166, 0.05)" stroke="none" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics & Top Products Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Sales Analytics Card -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/60 p-6 glow-card flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-bold text-gray-800 dark:text-white">Sales Analytics</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Grafik tren penjualan 7 hari terakhir</p>
                </div>
                <div class="flex items-center gap-1 bg-gray-50 dark:bg-gray-900/60 px-3 py-1.5 rounded-lg border border-gray-100 dark:border-gray-750 text-xs font-semibold text-gray-600 dark:text-gray-300">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-500 animate-pulse"></span>
                    Live Data
                </div>
            </div>
            <div class="relative h-72 md:h-80 w-full">
                <canvas id="dashboardSalesChart"></canvas>
            </div>
        </div>

        <!-- Top Products Card -->
        <div class="lg:col-span-1 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/60 p-6 glow-card flex flex-col justify-between">
            <div>
                <div class="mb-4">
                    <h3 class="text-base font-bold text-gray-800 dark:text-white">Top Products</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Bakery dengan penjualan tertinggi</p>
                </div>
                <div class="space-y-4">
                    @forelse($topProducts as $top)
                    @if($top->product)
                    <div class="flex items-center justify-between group">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-900 flex-shrink-0 border border-gray-100 dark:border-gray-750">
                                @if($top->product->image_url)
                                    <img src="{{ $top->product->image_url }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-300" alt="{{ $top->product->name }}">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-xs font-bold text-amber-700 dark:text-amber-300">🍞</div>
                                @endif
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-800 dark:text-white group-hover:text-blue-550 dark:group-hover:text-blue-450 transition">{{ $top->product->name }}</h4>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $top->product->category->name ?? 'Bakery' }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-sm font-black text-gray-800 dark:text-white">{{ $top->total_sold }} terjual</span>
                            <p class="text-[10px] font-bold text-amber-700 dark:text-amber-450 mt-0.5">Rp {{ number_format($top->total_revenue, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    @endif
                    @empty
                    <div class="text-center py-8 text-gray-400 dark:text-gray-500">
                        <p class="text-sm">Belum ada data penjualan produk</p>
                    </div>
                    @endforelse
                </div>
            </div>
            <div class="border-t border-gray-100 dark:border-gray-750/60 pt-4 mt-4">
                <a href="{{ route('admin.products.index') }}" class="w-full py-2.5 bg-gray-50 hover:bg-gray-100 dark:bg-gray-900/40 dark:hover:bg-gray-900 text-gray-700 dark:text-gray-300 text-xs font-bold rounded-xl transition flex items-center justify-center gap-1 active:scale-98">
                    Kelola Produk
                </a>
            </div>
        </div>
    </div>

    <!-- Activity Heatmap & Target Tracker Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Order Activity Heatmap -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/60 p-6 glow-card">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-6">
                <div>
                    <h3 class="text-base font-bold text-gray-800 dark:text-white">Order Frequency Activity</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Kepadatan order harian dalam 35 hari terakhir</p>
                </div>
                <!-- Heatmap Legend -->
                <div class="flex items-center gap-1.5 text-[10px] text-gray-400 dark:text-gray-500 font-bold self-end sm:self-center">
                    <span>Kurang</span>
                    <div class="w-3 h-3 rounded-sm bg-gray-100 dark:bg-gray-700/60"></div>
                    <div class="w-3 h-3 rounded-sm bg-blue-100 dark:bg-blue-950/40"></div>
                    <div class="w-3 h-3 rounded-sm bg-blue-200 dark:bg-blue-900/50"></div>
                    <div class="w-3 h-3 rounded-sm bg-blue-400 dark:bg-blue-600/70"></div>
                    <div class="w-3 h-3 rounded-sm bg-blue-600 dark:bg-blue-500"></div>
                    <span>Banyak</span>
                </div>
            </div>

            <!-- Heatmap Scroll Wrapper (Responsive) -->
            <div class="overflow-x-auto pb-1 select-none">
                <!-- Heatmap Grid Layout: columns of weeks (5 weeks x 7 days) -->
                <div class="grid grid-flow-col grid-rows-7 gap-1.5 w-max">
                    @foreach($heatmapData as $idx => $item)
                        @php
                            $count = $item['count'];
                            $bgColorClass = 'bg-gray-100 dark:bg-gray-750/60';
                            if ($count == 1) {
                                $bgColorClass = 'bg-blue-100 dark:bg-blue-950/40';
                            } elseif ($count == 2) {
                                $bgColorClass = 'bg-blue-200 dark:bg-blue-900/50';
                            } elseif ($count >= 3 && $count <= 4) {
                                $bgColorClass = 'bg-blue-400 dark:bg-blue-600/70';
                            } elseif ($count >= 5) {
                                $bgColorClass = 'bg-blue-600 dark:bg-blue-500 glow-progress';
                            }
                        @endphp
                        <!-- Square Cell -->
                        <div class="relative group cursor-help">
                            <div class="w-[28px] h-[28px] rounded-md transition-all duration-300 hover:scale-105 {{ $bgColorClass }}"></div>
                            <!-- Premium Pure CSS Tooltip -->
                            <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 hidden group-hover:block bg-gray-900 dark:bg-gray-950 text-white text-[10px] px-2.5 py-1.5 rounded-lg whitespace-nowrap shadow-lg z-10 border border-gray-850 dark:border-gray-800 pointer-events-none transition duration-150">
                                <span class="font-black text-blue-400">{{ $count }} order</span> pada {{ $item['formatted_date'] }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            
            <div class="flex items-center justify-between text-[10px] text-gray-400 mt-4 border-t border-gray-100 dark:border-gray-750/60 pt-3">
                <div class="flex gap-4">
                    <span>Hari: Min - Sab</span>
                    <span>Tingkat Kepadatan: Mingguan</span>
                </div>
                <div class="font-bold text-gray-500">MAMITHA INSIGHTS</div>
            </div>
        </div>

        <!-- Target / Goal Progress Tracker -->
        <div class="lg:col-span-1 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/60 p-6 glow-card flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h3 class="text-base font-bold text-gray-800 dark:text-white">Target Tracker</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Target omset bulanan toko</p>
                    </div>
                    <button onclick="openEditTargetModal({{ $monthlyRevenueTarget }})" class="p-1.5 rounded-lg text-gray-400 hover:text-amber-500 hover:bg-gray-50 dark:hover:bg-gray-900 transition cursor-pointer flex items-center justify-center" title="Ubah Target Omset">
                        ✏️
                    </button>
                </div>
                
                @php
                    $pct = $monthlyRevenueTarget > 0 ? min(100, round(($monthlyRevenueProgress / $monthlyRevenueTarget) * 100)) : 0;
                @endphp

                <div class="space-y-6">
                    <div class="flex justify-between items-end">
                        <div>
                            <p class="text-xs text-gray-400 dark:text-gray-500 font-bold uppercase">Omset Bulan Ini</p>
                            <p class="text-xl font-black text-gray-800 dark:text-white">Rp {{ number_format($monthlyRevenueProgress, 0, ',', '.') }}</p>
                        </div>
                        <div class="text-right">
                            <span class="text-3xl font-black text-blue-600 dark:text-blue-450">{{ $pct }}%</span>
                        </div>
                    </div>

                    <!-- Glowing Progress Bar -->
                    <div class="w-full bg-gray-100 dark:bg-gray-900 rounded-full h-3.5 overflow-hidden border border-gray-100 dark:border-gray-750">
                        <div class="bg-gradient-to-r from-blue-600 to-blue-500 h-full rounded-full glow-progress transition-all duration-1000" style="width: {{ $pct }}%"></div>
                    </div>

                    <div class="flex justify-between text-[11px] font-bold text-gray-400 dark:text-gray-500">
                        <span>Target: Rp {{ number_format($monthlyRevenueTarget, 0, ',', '.') }}</span>
                        <span>Sisa: Rp {{ number_format(max(0, $monthlyRevenueTarget - $monthlyRevenueProgress), 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-blue-50/50 dark:bg-blue-950/10 border border-blue-100/30 dark:border-blue-900/20 rounded-xl p-3.5 text-xs text-blue-800 dark:text-blue-300 font-medium leading-relaxed mt-4">
                💡 <span class="font-bold">Info Target:</span> Omset diperoleh dari total order yang berhasil dikonfirmasi (tidak dibatalkan) dalam periode bulan ini. Semangat!
            </div>
        </div>
    </div>

    <!-- Reviews & Recent Orders Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Customer Reviews Panel -->
        <div class="lg:col-span-1 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/60 p-6 glow-card flex flex-col justify-between">
            <div>
                <div class="mb-4">
                    <h3 class="text-base font-bold text-gray-800 dark:text-white">Customer Feedback</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Ulasan dan saran dari pelanggan</p>
                </div>
                <div class="space-y-4">
                    @forelse($recentReviews as $rev)
                    <div class="space-y-2 border-b border-gray-50 dark:border-gray-750/30 pb-3 last:border-0 last:pb-0">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <!-- Avatar Placeholder using initials -->
                                <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 font-black text-xs flex items-center justify-center border border-blue-100/30 dark:border-blue-900/20">
                                    {{ strtoupper(substr($rev->name, 0, 2)) }}
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-gray-800 dark:text-white">{{ $rev->name }}</h4>
                                    <p class="text-[10px] text-gray-400 dark:text-gray-500">{{ $rev->product->name ?? 'Ulasan Toko' }}</p>
                                </div>
                            </div>
                            <div class="flex text-amber-400 text-xs">
                                @for($i = 1; $i <= 5; $i++)
                                    {{ $i <= $rev->rating ? '★' : '☆' }}
                                @endfor
                            </div>
                        </div>
                        <p class="text-xs text-gray-600 dark:text-gray-300 italic leading-relaxed">
                            "{{ Str::limit($rev->comment, 75, '...') }}"
                        </p>
                    </div>
                    @empty
                    <div class="text-center py-8 text-gray-400 dark:text-gray-500">
                        <p class="text-sm">Belum ada ulasan masuk</p>
                    </div>
                    @endforelse
                </div>
            </div>
            <div class="border-t border-gray-100 dark:border-gray-750/60 pt-4 mt-4">
                <a href="{{ route('admin.reviews.index') }}" class="w-full py-2.5 bg-gray-50 hover:bg-gray-100 dark:bg-gray-900/40 dark:hover:bg-gray-900 text-gray-700 dark:text-gray-300 text-xs font-bold rounded-xl transition flex items-center justify-center gap-1 active:scale-98">
                    Semua Ulasan
                </a>
            </div>
        </div>

        <!-- Recent Orders Panel -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/60 p-6 glow-card overflow-hidden">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-bold text-gray-800 dark:text-white">Recent Orders</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Order masuk terbaru pelanggan</p>
                </div>
                <a href="{{ route('admin.orders.index') }}" class="text-xs font-bold text-blue-600 dark:text-blue-450 hover:underline">Lihat Semua</a>
            </div>
            
            <!-- Table Wrapper (Responsive) -->
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr class="text-xs text-gray-400 dark:text-gray-500 font-bold uppercase border-b border-gray-100 dark:border-gray-750/60 pb-3">
                            <th class="py-2.5 font-bold">No. Order</th>
                            <th class="py-2.5 font-bold">Pelanggan</th>
                            <th class="py-2.5 font-bold hidden sm:table-cell">Metode</th>
                            <th class="py-2.5 font-bold">Status</th>
                            <th class="py-2.5 font-bold text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-750/30">
                        @forelse($recentOrders as $order)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-900/10 cursor-pointer transition" onclick="window.location='{{ route('admin.orders.show', $order) }}'">
                            <td class="py-3 font-mono text-xs font-bold text-blue-600 dark:text-blue-400">{{ $order->order_number }}</td>
                            <td class="py-3">
                                <span class="font-semibold text-gray-800 dark:text-white block">{{ $order->customer->name }}</span>
                                <span class="text-[10px] text-gray-400 block sm:hidden">
                                    {{ $order->type === 'pickup' ? 'Ambil Sendiri' : 'Delivery' }}
                                </span>
                            </td>
                            <td class="py-3 text-xs text-gray-500 dark:text-gray-405 hidden sm:table-cell uppercase">
                                {{ $order->type === 'pickup' ? 'Ambil Sendiri' : 'Delivery' }}
                            </td>
                            <td class="py-3">
                                <span class="px-2.5 py-1 text-[10px] font-black rounded-full uppercase tracking-wider
                                    @if($order->status == 'pending') bg-yellow-100/80 text-yellow-700 dark:bg-yellow-950/30 dark:text-yellow-300
                                    @elseif($order->status == 'confirmed') bg-blue-100/80 text-blue-700 dark:bg-blue-950/30 dark:text-blue-300
                                    @elseif($order->status == 'producing') bg-orange-100/80 text-orange-700 dark:bg-orange-950/30 dark:text-orange-300
                                    @elseif($order->status == 'ready') bg-emerald-100/80 text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-300
                                    @elseif($order->status == 'done') bg-gray-100/80 text-gray-700 dark:bg-gray-800 dark:text-gray-300
                                    @else bg-red-100/80 text-red-700 dark:bg-red-950/30 dark:text-red-300 @endif">
                                    {{ $order->statusLabel() }}
                                </span>
                            </td>
                            <td class="py-3 font-black text-gray-800 dark:text-white text-right">
                                Rp {{ number_format($order->total, 0, ',', '.') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-gray-450">Belum ada pesanan terbaru</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
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

<!-- Modal 3: Edit Target Modal -->
<div id="edit-target-modal" class="fixed inset-0 bg-gray-900/60 dark:bg-black/85 flex items-center justify-center p-4 z-[9999] hidden opacity-0 transition-opacity duration-300">
    <div class="bg-white dark:bg-gray-800 rounded-3xl w-full max-w-sm p-6 border border-gray-100 dark:border-gray-700 shadow-2xl transform scale-95 transition-all duration-300" id="edit-target-card">
        <div class="flex justify-between items-center border-b border-gray-100 dark:border-gray-750 pb-4 mb-4">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white">Ubah Target Omset</h3>
            <button onclick="closeEditTargetModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 font-bold text-lg">&times;</button>
        </div>
        <form action="{{ route('admin.update-revenue-target') }}" method="POST" class="space-y-4">
            @csrf
            <div class="space-y-1">
                <label for="target-input" class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Target Baru (Rupiah)</label>
                <input type="number" name="monthly_revenue_target" id="target-input" min="0" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-750 dark:bg-gray-905 rounded-lg text-sm">
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="closeEditTargetModal()" class="px-4 py-2 bg-gray-100 dark:bg-gray-750 text-gray-750 text-gray-700 dark:text-gray-300 text-xs font-bold rounded-lg">Batal</button>
                <button type="submit" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-xs font-black rounded-lg">Simpan Target</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Initialize Chart.js Sales Analytics
    document.addEventListener('DOMContentLoaded', () => {
        const ctx = document.getElementById('dashboardSalesChart')?.getContext('2d');
        if (!ctx) return;

        const isDark = document.documentElement.classList.contains('dark');
        const gridColor = isDark ? 'rgba(255, 255, 255, 0.05)' : 'rgba(0, 0, 0, 0.05)';
        const textColor = isDark ? '#94a3b8' : '#64748b';

        // Prepare data from PHP
        const salesData = @json($sevenDaysSales);
        const labels = salesData.map(item => item.label);
        const revenues = salesData.map(item => item.revenue);

        // Gradient background for glowing line chart
        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(59, 130, 246, 0.35)');
        gradient.addColorStop(0.5, 'rgba(59, 130, 246, 0.1)');
        gradient.addColorStop(1, 'rgba(59, 130, 246, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: revenues,
                    borderColor: '#3b82f6',
                    borderWidth: 3,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#3b82f6',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    backgroundColor: gradient,
                    tension: 0.35
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: isDark ? '#1f2937' : '#111827',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        borderColor: 'rgba(255, 255, 255, 0.1)',
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: textColor,
                            font: {
                                size: 11
                            }
                        }
                    },
                    y: {
                        grid: {
                            color: gridColor
                        },
                        ticks: {
                            color: textColor,
                            font: {
                                size: 11
                            },
                            callback: function(value) {
                                if (value >= 1000000) {
                                    return 'Rp ' + (value / 1000000) + 'jt';
                                } else if (value >= 1000) {
                                    return 'Rp ' + (value / 1000) + 'rb';
                                }
                                return 'Rp ' + value;
                            }
                        }
                    }
                }
            }
        });
        
        // Listen to theme change to update chart grid colors dynamically
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.attributeName === 'class') {
                    const isDarkTheme = document.documentElement.classList.contains('dark');
                    const updatedGridColor = isDarkTheme ? 'rgba(255, 255, 255, 0.05)' : 'rgba(0, 0, 0, 0.05)';
                    const updatedTextColor = isDarkTheme ? '#94a3b8' : '#64748b';
                    
                    const chartInstance = Chart.getChart("dashboardSalesChart");
                    if (chartInstance) {
                        chartInstance.options.scales.x.ticks.color = updatedTextColor;
                        chartInstance.options.scales.y.ticks.color = updatedTextColor;
                        chartInstance.options.scales.y.grid.color = updatedGridColor;
                        chartInstance.update();
                    }
                }
            });
        });
        observer.observe(document.documentElement, { attributes: true });
    });

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

    function openEditTargetModal(target) {
        const modal = document.getElementById('edit-target-modal');
        const card = document.getElementById('edit-target-card');
        const input = document.getElementById('target-input');
        
        input.value = target;
        
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            card.classList.remove('scale-95');
            card.classList.add('scale-100');
        }, 10);
    }
    
    function closeEditTargetModal() {
        const modal = document.getElementById('edit-target-modal');
        const card = document.getElementById('edit-target-card');
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

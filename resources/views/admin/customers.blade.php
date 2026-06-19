@extends('layouts.admin')

@section('title', 'Pelanggan')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Pelanggan</h1>
        <p class="text-gray-500 dark:text-gray-400 text-sm">Daftar pelanggan toko</p>
    </div>
    <form action="{{ route('admin.customers.reset') }}" method="POST" onsubmit="return confirm('Yakin reset semua pelanggan tanpa riwayat pesanan? Data tidak bisa dikembalikan.')">
        @csrf
        <button type="submit" class="w-full sm:w-auto px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-black rounded-lg shadow-sm transition active:scale-95 cursor-pointer flex items-center justify-center gap-1">
            🗑️ Reset Pelanggan Tanpa Order
        </button>
    </form>
</div>

<!-- Search Bar Card -->
<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/60 p-4 mb-6">
    <form method="GET" class="flex gap-3">
        <input type="text" name="search" placeholder="Cari nama atau telepon..." value="{{ request('search') }}" class="flex-1 px-4 py-2.5 border border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-xl text-sm focus:ring-amber-500 focus:border-amber-500">
        <button type="submit" class="px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white text-sm font-black rounded-xl shadow-sm transition active:scale-95 cursor-pointer">
            🔍 Cari
        </button>
    </form>
</div>

<!-- Customers Data List Card -->
<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/60 overflow-hidden">
    <!-- Responsive Desktop Table -->
    <div class="hidden md:block overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-900/20 text-left">
                    <th class="px-5 py-3.5 font-bold text-gray-600 dark:text-gray-300 text-xs uppercase tracking-wider border-b border-gray-100 dark:border-gray-700">Nama Pelanggan</th>
                    <th class="px-5 py-3.5 font-bold text-gray-600 dark:text-gray-300 text-xs uppercase tracking-wider border-b border-gray-100 dark:border-gray-700">Status Member</th>
                    <th class="px-5 py-3.5 font-bold text-gray-600 dark:text-gray-300 text-xs uppercase tracking-wider border-b border-gray-100 dark:border-gray-700">WhatsApp</th>
                    <th class="px-5 py-3.5 font-bold text-gray-600 dark:text-gray-300 text-xs uppercase tracking-wider border-b border-gray-100 dark:border-gray-700">Alamat</th>
                    <th class="px-5 py-3.5 font-bold text-gray-600 dark:text-gray-300 text-xs uppercase tracking-wider border-b border-gray-100 dark:border-gray-700">Total Order</th>
                    <th class="px-5 py-3.5 font-bold text-gray-600 dark:text-gray-300 text-xs uppercase tracking-wider border-b border-gray-100 dark:border-gray-700">Bergabung</th>
                    <th class="px-5 py-3.5 font-bold text-gray-600 dark:text-gray-300 text-xs uppercase tracking-wider border-b border-gray-100 dark:border-gray-700">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($customers as $customer)
                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-900/10">
                    <td class="px-5 py-4 font-semibold text-gray-800 dark:text-white">{{ $customer->name }}</td>
                    <td class="px-5 py-4">
                        @if($customer->is_member)
                            <span class="bg-amber-100 dark:bg-amber-950/40 text-amber-800 dark:text-amber-300 text-[10px] font-black px-2.5 py-1 rounded-full uppercase tracking-wider">
                                {{ $customer->rank_badge }} {{ $customer->rank_name }}
                            </span>
                        @else
                            <span class="text-[10px] text-gray-400 dark:text-gray-500 font-semibold uppercase tracking-wider">Guest</span>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-gray-600 dark:text-gray-300">
                        @if($customer->phone)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $customer->phone) }}" target="_blank" class="hover:text-amber-500 text-amber-600 dark:text-amber-400 transition inline-flex items-center gap-1 font-medium">
                                💬 {{ $customer->phone }}
                            </a>
                        @else
                            <span class="text-gray-400 dark:text-gray-500 font-medium">-</span>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-gray-500 dark:text-gray-450 max-w-xs truncate font-medium">{{ $customer->address ?? '-' }}</td>
                    <td class="px-5 py-4 font-bold text-gray-700 dark:text-gray-200">{{ $customer->orders_count }} order</td>
                    <td class="px-5 py-4 text-xs text-gray-500 dark:text-gray-450 font-medium">{{ $customer->created_at->format('d/m/Y') }}</td>
                    <td class="px-5 py-4">
                        @if($customer->orders_count > 0)
                            <span class="px-2.5 py-1 bg-gray-50 dark:bg-gray-900 text-gray-450 dark:text-gray-500 text-xs font-semibold rounded-lg italic">
                                Memiliki pesanan
                            </span>
                        @else
                            <form action="{{ route('admin.customers.destroy', $customer) }}" method="POST" onsubmit="return confirm('Yakin hapus pelanggan {{ $customer->name }}?')">
                                @csrf 
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 dark:bg-rose-950/20 dark:text-rose-300 text-xs font-black rounded-lg transition active:scale-95 cursor-pointer">
                                    🗑️ Hapus
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-8 text-center text-gray-400 dark:text-gray-500">Belum ada pelanggan terdaftar</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Responsive Mobile Cards -->
    <div class="block md:hidden p-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @forelse($customers as $customer)
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/60 p-5 space-y-4 hover:shadow-md transition duration-300">
                <!-- Card Header: Name and Status -->
                <div class="flex justify-between items-start gap-2">
                    <div>
                        <h3 class="text-base font-bold text-gray-850 dark:text-white">{{ $customer->name }}</h3>
                        <span class="text-[10px] text-gray-400 font-medium">Sejak: {{ $customer->created_at->format('d/m/Y') }}</span>
                    </div>
                    @if($customer->is_member)
                        <span class="bg-amber-100 dark:bg-amber-950/40 text-amber-800 dark:text-amber-300 text-[10px] font-black px-2.5 py-1 rounded-full uppercase tracking-wider whitespace-nowrap">
                            {{ $customer->rank_badge }} {{ $customer->rank_name }}
                        </span>
                    @else
                        <span class="bg-gray-50 dark:bg-gray-900/50 text-gray-400 dark:text-gray-500 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider whitespace-nowrap">
                            Guest
                        </span>
                    @endif
                </div>

                <!-- Info Grid -->
                <div class="space-y-2 text-xs">
                    <div class="flex justify-between">
                        <span class="text-gray-400 dark:text-gray-500">WhatsApp:</span>
                        @if($customer->phone)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $customer->phone) }}" target="_blank" class="text-amber-600 dark:text-amber-400 hover:underline font-semibold flex items-center gap-1">
                                💬 {{ $customer->phone }}
                            </a>
                        @else
                            <span class="text-gray-450 dark:text-gray-500">-</span>
                        @endif
                    </div>
                    <div class="flex flex-col gap-1 border-t border-gray-50 dark:border-gray-700/40 pt-2">
                        <span class="text-gray-400 dark:text-gray-500">Alamat:</span>
                        <p class="text-gray-600 dark:text-gray-300 font-medium leading-relaxed">{{ $customer->address ?? 'Tidak ada alamat' }}</p>
                    </div>
                </div>

                <!-- Stats Footer and Actions -->
                <div class="flex justify-between items-center border-t border-gray-100 dark:border-gray-700/60 pt-3">
                    <div>
                        <p class="text-[9px] uppercase font-bold text-gray-400 tracking-wider">Total Order</p>
                        <p class="text-sm font-black text-gray-800 dark:text-gray-100">{{ $customer->orders_count }} order</p>
                    </div>
                    <div>
                        @if($customer->orders_count > 0)
                            <span class="text-xs text-gray-400 dark:text-gray-500 italic font-semibold">
                                Memiliki pesanan
                            </span>
                        @else
                            <form action="{{ route('admin.customers.destroy', $customer) }}" method="POST" onsubmit="return confirm('Yakin hapus pelanggan {{ $customer->name }}?')">
                                @csrf 
                                @method('DELETE')
                                <button type="submit" class="px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 dark:bg-rose-950/20 dark:text-rose-300 text-xs font-black rounded-xl transition active:scale-95 cursor-pointer">
                                    🗑️ Hapus
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full py-8 text-center text-gray-400 dark:text-gray-500">Belum ada pelanggan terdaftar</div>
            @endforelse
        </div>
    </div>

    <!-- Pagination -->
    @if($customers->hasPages())
        <div class="p-4 border-t border-gray-100 dark:border-gray-700">
            {{ $customers->links() }}
        </div>
    @endif
</div>
@endsection

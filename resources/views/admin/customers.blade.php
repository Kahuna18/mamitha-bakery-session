@extends('layouts.admin')

@section('title', 'Pelanggan')

@section('content')
<div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Pelanggan</h1>
        <p class="text-gray-500 text-sm">Daftar pelanggan toko</p>
    </div>
    <form action="{{ route('admin.customers.reset') }}" method="POST" onsubmit="return confirm('Yakin reset semua pelanggan tanpa riwayat pesanan? Data tidak bisa dikembalikan.')">
        @csrf
        <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition">Reset Semua</button>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
    <form method="GET" class="flex gap-3">
        <input type="text" name="search" placeholder="Cari nama/telepon..." value="{{ request('search') }}" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-sm">
        <button type="submit" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium rounded-lg transition">Cari</button>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-gray-50 text-left">
                <th class="px-4 py-3 font-semibold text-gray-600">Nama</th>
                <th class="px-4 py-3 font-semibold text-gray-600">WhatsApp</th>
                <th class="px-4 py-3 font-semibold text-gray-600">Alamat</th>
                <th class="px-4 py-3 font-semibold text-gray-600">Total Order</th>
                <th class="px-4 py-3 font-semibold text-gray-600">Bergabung</th>
                <th class="px-4 py-3 font-semibold text-gray-600">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($customers as $customer)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-medium">{{ $customer->name }}</td>
                <td class="px-4 py-3">{{ $customer->phone }}</td>
                <td class="px-4 py-3 text-gray-500 max-w-xs truncate">{{ $customer->address ?? '-' }}</td>
                <td class="px-4 py-3 font-medium">{{ $customer->orders_count }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $customer->created_at->format('d/m/Y') }}</td>
                <td class="px-4 py-3">
                    @if($customer->orders_count > 0)
                    <span class="text-xs text-gray-400 italic">Memiliki pesanan</span>
                    @else
                    <form action="{{ route('admin.customers.destroy', $customer) }}" method="POST" onsubmit="return confirm('Yakin hapus pelanggan {{ $customer->name }}?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded-lg transition">Hapus</button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">Belum ada pelanggan</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4 border-t border-gray-100">{{ $customers->links() }}</div>
</div>
@endsection

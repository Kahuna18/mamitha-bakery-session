@extends('layouts.admin')

@section('title', 'Varian ' . $product->name)

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.products.index') }}" class="text-amber-600 hover:text-amber-700 text-sm font-medium">&larr; Kembali ke Produk</a>
    <h1 class="text-2xl font-bold text-gray-800 mt-1">Varian {{ $product->name }}</h1>
    <p class="text-gray-500 text-sm">Kelola varian untuk produk ini</p>
</div>

<div class="mb-4">
    <a href="{{ route('admin.products.variants.create', $product) }}" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium rounded-lg transition">+ Tambah Varian</a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-left">
                    <th class="px-4 py-3 font-semibold text-gray-600">Nama Varian</th>
                    <th class="px-4 py-3 font-semibold text-gray-600">Penyesuaian Harga</th>
                    <th class="px-4 py-3 font-semibold text-gray-600">Stok</th>
                    <th class="px-4 py-3 font-semibold text-gray-600">Status</th>
                    <th class="px-4 py-3 font-semibold text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($variants as $variant)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium">{{ $variant->name }}</td>
                    <td class="px-4 py-3">
                        @if($variant->price_adjustment > 0)
                        +Rp {{ number_format($variant->price_adjustment, 0, ',', '.') }}
                        @else
                        <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">{{ $variant->stock }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ $variant->is_available ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $variant->is_available ? 'Tersedia' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <a href="{{ route('admin.products.variants.edit', [$product, $variant]) }}" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg transition inline-block">Edit</a>
                        <form action="{{ route('admin.products.variants.destroy', [$product, $variant]) }}" method="POST" class="inline" onsubmit="return confirm('Hapus varian {{ $variant->name }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded-lg transition">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">Belum ada varian untuk produk ini</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-100">{{ $variants->links() }}</div>
</div>
@endsection

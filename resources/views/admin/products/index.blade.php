@extends('layouts.admin')

@section('title', 'Produk')

@section('content')
<div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Produk</h1>
        <p class="text-gray-500 text-sm">Kelola produk roti</p>
    </div>
    <a href="{{ route('admin.products.create') }}" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium rounded-lg transition">+ Tambah Produk</a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-left">
                    <th class="px-4 py-3 font-semibold text-gray-600">Nama</th>
                    <th class="px-4 py-3 font-semibold text-gray-600">Kategori</th>
                    <th class="px-4 py-3 font-semibold text-gray-600">Harga</th>
                    <th class="px-4 py-3 font-semibold text-gray-600">Status</th>
                    <th class="px-4 py-3 font-semibold text-gray-600">Unggulan</th>
                    <th class="px-4 py-3 font-semibold text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($products as $product)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium">{{ $product->name }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $product->category->name }}</td>
                    <td class="px-4 py-3 font-semibold">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ $product->is_available ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $product->is_available ? 'Tersedia' : 'Habis' }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        @if($product->is_featured)
                        <span class="text-yellow-500">★ Unggulan</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <a href="{{ route('admin.products.variants.index', $product) }}" class="px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white text-xs font-medium rounded-lg transition inline-block">Varian</a>
                        <a href="{{ route('admin.products.edit', $product) }}" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg transition inline-block">Edit</a>
                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Hapus produk ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded-lg transition">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">Belum ada produk</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-100">{{ $products->links() }}</div>
</div>
@endsection

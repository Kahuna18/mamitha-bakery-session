@extends('layouts.admin')

@section('title', 'Tambah Varian')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.products.variants.index', $product) }}" class="text-amber-600 hover:text-amber-700 text-sm font-medium">&larr; Kembali</a>
    <h1 class="text-2xl font-bold text-gray-800 mt-1">Tambah Varian {{ $product->name }}</h1>
</div>

<div class="max-w-2xl">
    <form action="{{ route('admin.products.variants.store', $product) }}" method="POST" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Varian <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm" placeholder="Contoh: Coklat Lumer">
            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Penyesuaian Harga (Rp) <span class="text-red-500">*</span></label>
            <input type="number" name="price_adjustment" value="{{ old('price_adjustment', 0) }}" required min="0" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
            <p class="text-xs text-gray-400 mt-1">Tambahan harga untuk varian ini. Isi 0 jika harga sama dengan produk utama.</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Stok <span class="text-red-500">*</span></label>
            <input type="number" name="stock" value="{{ old('stock', 0) }}" min="0" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
        </div>
        <div class="flex items-center">
            <label class="flex items-center">
                <input type="checkbox" name="is_available" value="1" checked class="rounded border-gray-300 text-amber-600">
                <span class="ml-2 text-sm text-gray-700">Tersedia</span>
            </label>
        </div>
        <button type="submit" class="px-6 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-medium rounded-lg transition">Simpan</button>
    </form>
</div>
@endsection

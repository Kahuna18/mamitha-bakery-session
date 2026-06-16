@extends('layouts.admin')

@section('title', 'Edit Varian')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.products.variants.index', $product) }}" class="text-amber-600 hover:text-amber-700 text-sm font-medium">&larr; Kembali</a>
    <h1 class="text-2xl font-bold text-gray-800 mt-1">Edit Varian {{ $variant->name }}</h1>
</div>

<div class="max-w-2xl">
    <form action="{{ route('admin.products.variants.update', [$product, $variant]) }}" method="POST" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
        @csrf @method('PUT')
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Varian <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name', $variant->name) }}" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Penyesuaian Harga (Rp) <span class="text-red-500">*</span></label>
            <input type="number" name="price_adjustment" value="{{ old('price_adjustment', $variant->price_adjustment) }}" required min="0" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Stok <span class="text-red-500">*</span></label>
            <input type="number" name="stock" value="{{ old('stock', $variant->stock) }}" min="0" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
        </div>
        <div class="flex items-center">
            <label class="flex items-center">
                <input type="checkbox" name="is_available" value="1" {{ $variant->is_available ? 'checked' : '' }} class="rounded border-gray-300 text-amber-600">
                <span class="ml-2 text-sm text-gray-700">Tersedia</span>
            </label>
        </div>
        <button type="submit" class="px-6 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-medium rounded-lg transition">Simpan Perubahan</button>
    </form>
</div>
@endsection

@extends('layouts.admin')

@section('title', 'Tambah Produk')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.products.index') }}" class="text-amber-600 hover:text-amber-700 text-sm font-medium">&larr; Kembali</a>
    <h1 class="text-2xl font-bold text-gray-800 mt-1">Tambah Produk</h1>
</div>

<div class="max-w-2xl">
    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Produk <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
            <select name="category_id" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
                <option value="">Pilih Kategori</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Harga <span class="text-red-500">*</span></label>
            <input type="number" name="price" value="{{ old('price') }}" required min="0" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
            <textarea name="description" rows="3" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm">{{ old('description') }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Foto Produk Utama</label>
            <input type="file" name="image" accept="image/*" class="w-full text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Foto Tambahan (Bisa pilih lebih dari 1)</label>
            <input type="file" name="additional_images[]" accept="image/*" multiple class="w-full text-sm block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100">
            <p class="text-xs text-gray-400 mt-1">Pilih beberapa foto sekaligus untuk menambah galeri foto produk (sangat cocok untuk Kue Ulang Tahun).</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Stok</label>
            <input type="number" name="stock" value="{{ old('stock', 0) }}" min="0" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Rating Default <span class="text-red-500">*</span></label>
            <input type="number" step="0.1" name="rating" value="{{ old('rating', 4.9) }}" min="0" max="5" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
            @error('rating') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Estimasi Waktu Penyajian (Card Menu) <span class="text-red-500">*</span></label>
            <input type="text" name="ready_time" value="{{ old('ready_time', '15-20 min') }}" required placeholder="Contoh: 15-20 min, 30 min, dll" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
            @error('ready_time') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="flex items-center gap-6">
            <label class="flex items-center">
                <input type="checkbox" name="is_available" value="1" checked class="rounded border-gray-300 text-amber-600">
                <span class="ml-2 text-sm text-gray-700">Tersedia</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" name="is_featured" value="1" class="rounded border-gray-300 text-amber-600">
                <span class="ml-2 text-sm text-gray-700">Produk Unggulan</span>
            </label>
        </div>
        <button type="submit" class="px-6 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-medium rounded-lg transition">Simpan</button>
    </form>
</div>
@endsection

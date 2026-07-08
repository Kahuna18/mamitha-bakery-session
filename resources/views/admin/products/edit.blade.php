@extends('layouts.admin')

@section('title', 'Edit Produk')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.products.index') }}" class="text-amber-600 hover:text-amber-700 text-sm font-medium">&larr; Kembali</a>
    <h1 class="text-2xl font-bold text-gray-800 mt-1">Edit Produk</h1>
</div>

<div class="max-w-2xl">
    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
        @csrf @method('PUT')
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Produk <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name', $product->name) }}" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
            <select name="category_id" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ (old('category_id', $product->category_id) == $cat->id) ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Harga <span class="text-red-500">*</span></label>
            <input type="number" name="price" value="{{ old('price', $product->price) }}" required min="0" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
            <textarea name="description" rows="3" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm">{{ old('description', $product->description) }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Foto Produk Utama</label>
            @if($product->image)
            <img src="{{ $product->image_url }}" class="w-24 h-24 object-cover rounded-xl mb-2 border border-gray-200 shadow-sm">
            @endif
            <input type="file" name="image" accept="image/*" class="w-full text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Foto Tambahan saat ini (Centang untuk menghapus)</label>
            @if($product->images->isNotEmpty())
            <div class="grid grid-cols-4 gap-3 mb-3">
                @foreach($product->images as $img)
                <div class="relative group border border-gray-100 rounded-xl overflow-hidden shadow-sm bg-gray-50 aspect-square">
                    <img src="{{ $img->image_url }}" class="w-full h-full object-cover">
                    <label class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center cursor-pointer text-white text-xs font-bold gap-1.5 p-1 text-center">
                        <input type="checkbox" name="delete_images[]" value="{{ $img->id }}" class="rounded text-red-600 focus:ring-red-500 w-4 h-4 cursor-pointer">
                        <span>Hapus</span>
                    </label>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-xs text-gray-400 mb-3 bg-gray-50 border border-gray-100 p-2.5 rounded-lg">Belum ada foto tambahan.</p>
            @endif
            
            <label class="block text-sm font-medium text-gray-700 mb-1">Tambah Foto Baru (Bisa pilih lebih dari 1)</label>
            <input type="file" name="additional_images[]" accept="image/*" multiple class="w-full text-sm block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100">
            <p class="text-xs text-gray-400 mt-1">Pilih beberapa foto sekaligus untuk menambah galeri foto produk (sangat cocok untuk Kue Ulang Tahun).</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Stok</label>
            <input type="number" name="stock" value="{{ old('stock', $product->stock) }}" min="0" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Rating Default <span class="text-red-500">*</span></label>
            <input type="number" step="0.1" name="rating" value="{{ old('rating', $product->rating) }}" min="0" max="5" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
            @error('rating') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Estimasi Waktu Penyajian (Card Menu) <span class="text-red-500">*</span></label>
            <input type="text" name="ready_time" value="{{ old('ready_time', $product->ready_time) }}" required placeholder="Contoh: 15-20 min, 30 min, dll" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
            @error('ready_time') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="flex items-center gap-6">
            <label class="flex items-center">
                <input type="checkbox" name="is_available" value="1" {{ $product->is_available ? 'checked' : '' }} class="rounded border-gray-300 text-amber-600">
                <span class="ml-2 text-sm text-gray-700">Tersedia</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" name="is_featured" value="1" {{ $product->is_featured ? 'checked' : '' }} class="rounded border-gray-300 text-amber-600">
                <span class="ml-2 text-sm text-gray-700">Produk Unggulan</span>
            </label>
        </div>
        <button type="submit" class="px-6 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-medium rounded-lg transition">Simpan Perubahan</button>
    </form>
</div>
@endsection

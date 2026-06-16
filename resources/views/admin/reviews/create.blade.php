@extends('layouts.admin')

@section('title', 'Tambah Ulasan Produk')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.reviews.index') }}" class="text-amber-600 hover:text-amber-700 text-sm font-medium">&larr; Kembali</a>
    <h1 class="text-2xl font-bold text-gray-800 mt-1">Tambah Ulasan Produk</h1>
</div>

<div class="max-w-2xl">
    <form action="{{ route('admin.reviews.store') }}" method="POST" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
        @csrf
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Produk <span class="text-red-500">*</span></label>
            <select name="product_id" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-amber-500">
                <option value="">-- Pilih Produk Roti --</option>
                @foreach($products as $prod)
                <option value="{{ $prod->id }}" {{ old('product_id') == $prod->id ? 'selected' : '' }}>{{ $prod->name }}</option>
                @endforeach
            </select>
            @error('product_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pengulas <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name') }}" required placeholder="Contoh: Ani Rahmawati" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-amber-500">
            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Rating Bintang <span class="text-red-500">*</span></label>
            <select name="rating" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-amber-500">
                <option value="5" {{ old('rating', '5') == '5' ? 'selected' : '' }}>5 Bintang (Sangat Lezat)</option>
                <option value="4" {{ old('rating') == '4' ? 'selected' : '' }}>4 Bintang (Lezat)</option>
                <option value="3" {{ old('rating') == '3' ? 'selected' : '' }}>3 Bintang (Cukup)</option>
                <option value="2" {{ old('rating') == '2' ? 'selected' : '' }}>2 Bintang (Kurang Pas)</option>
                <option value="1" {{ old('rating') == '1' ? 'selected' : '' }}>1 Bintang (Tidak Enak)</option>
            </select>
            @error('rating') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Isi Ulasan <span class="text-red-500">*</span></label>
            <textarea name="comment" required rows="4" placeholder="Tulis komentar ulasan produk di sini..." class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-amber-500">{{ old('comment') }}</textarea>
            @error('comment') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="px-6 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-medium rounded-lg transition">Simpan</button>
    </form>
</div>
@endsection

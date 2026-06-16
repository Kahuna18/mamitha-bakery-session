@extends('layouts.admin')

@section('title', 'Tambah Testimoni')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.testimonials.index') }}" class="text-amber-600 hover:text-amber-700 text-sm font-medium">&larr; Kembali</a>
    <h1 class="text-2xl font-bold text-gray-800 mt-1">Tambah Testimoni</h1>
</div>

<div class="max-w-2xl">
    <form action="{{ route('admin.testimonials.store') }}" method="POST" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
        @csrf
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pelanggan <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name') }}" required placeholder="Contoh: Ibu Sari" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-amber-500">
            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Rating Bintang <span class="text-red-500">*</span></label>
                <select name="rating" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-amber-500">
                    <option value="5" {{ old('rating', '5') == '5' ? 'selected' : '' }}>5 Bintang (Sangat Puas)</option>
                    <option value="4" {{ old('rating') == '4' ? 'selected' : '' }}>4 Bintang (Puas)</option>
                    <option value="3" {{ old('rating') == '3' ? 'selected' : '' }}>3 Bintang (Cukup)</option>
                    <option value="2" {{ old('rating') == '2' ? 'selected' : '' }}>2 Bintang (Buruk)</option>
                    <option value="1" {{ old('rating') == '1' ? 'selected' : '' }}>1 Bintang (Sangat Buruk)</option>
                </select>
                @error('rating') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Avatar / Emojii <span class="text-red-500">*</span></label>
                <select name="avatar" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-amber-500">
                    <option value="👩" {{ old('avatar') == '👩' ? 'selected' : '' }}>👩 (Wanita)</option>
                    <option value="👨" {{ old('avatar') == '👨' ? 'selected' : '' }}>👨 (Pria)</option>
                    <option value="👧" {{ old('avatar') == '👧' ? 'selected' : '' }}>👧 (Anak Perempuan)</option>
                    <option value="👦" {{ old('avatar') == '👦' ? 'selected' : '' }}>👦 (Anak Laki-laki)</option>
                    <option value="👵" {{ old('avatar') == '👵' ? 'selected' : '' }}>👵 (Ibu/Nenek)</option>
                    <option value="👴" {{ old('avatar') == '👴' ? 'selected' : '' }}>👴 (Bapak/Kakek)</option>
                    <option value="👤" {{ old('avatar') == '👤' ? 'selected' : '' }}>👤 (User Biasa)</option>
                </select>
                @error('avatar') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Isi Ulasan <span class="text-red-500">*</span></label>
            <textarea name="body" required rows="4" placeholder="Tulis testimoni/ulasan di sini..." class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-amber-500">{{ old('body') }}</textarea>
            @error('body') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <label class="flex items-center">
            <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-amber-600">
            <span class="ml-2 text-sm text-gray-700">Aktif (Tampilkan di Beranda)</span>
        </label>

        <button type="submit" class="px-6 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-medium rounded-lg transition">Simpan</button>
    </form>
</div>
@endsection

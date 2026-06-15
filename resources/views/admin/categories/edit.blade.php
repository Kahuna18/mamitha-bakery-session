@extends('layouts.admin')

@section('title', 'Edit Kategori')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.categories.index') }}" class="text-amber-600 hover:text-amber-700 text-sm font-medium">&larr; Kembali</a>
    <h1 class="text-2xl font-bold text-gray-800 mt-1">Edit Kategori</h1>
</div>

<div class="max-w-2xl">
    <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
        @csrf @method('PUT')
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kategori <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name', $category->name) }}" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Gambar Kategori</label>
            @if($category->image)
            <img src="{{ asset('storage/' . $category->image) }}" class="w-20 h-20 object-cover rounded mb-2">
            @endif
            <input type="file" name="image" accept="image/*" class="w-full text-sm">
        </div>
        <label class="flex items-center">
            <input type="checkbox" name="is_active" value="1" {{ $category->is_active ? 'checked' : '' }} class="rounded border-gray-300 text-amber-600">
            <span class="ml-2 text-sm text-gray-700">Aktif</span>
        </label>
        <button type="submit" class="px-6 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-medium rounded-lg transition">Simpan Perubahan</button>
    </form>
</div>
@endsection

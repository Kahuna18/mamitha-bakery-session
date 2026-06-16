@extends('layouts.admin')

@section('title', 'Testimoni & Ulasan')

@section('content')
<div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Testimoni & Ulasan</h1>
        <p class="text-gray-500 text-sm">Kelola testimoni pelanggan untuk halaman utama</p>
    </div>
    <a href="{{ route('admin.testimonials.create') }}" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium rounded-lg transition">+ Tambah Testimoni</a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-gray-50 text-left">
                <th class="px-4 py-3 font-semibold text-gray-600 w-16">Avatar</th>
                <th class="px-4 py-3 font-semibold text-gray-600 w-44">Nama</th>
                <th class="px-4 py-3 font-semibold text-gray-600 w-32">Rating</th>
                <th class="px-4 py-3 font-semibold text-gray-600">Ulasan</th>
                <th class="px-4 py-3 font-semibold text-gray-600 w-24">Status</th>
                <th class="px-4 py-3 font-semibold text-gray-600 w-36">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($testimonials as $t)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 text-2xl text-center">{{ $t->avatar }}</td>
                <td class="px-4 py-3 font-medium text-gray-800">{{ $t->name }}</td>
                <td class="px-4 py-3">
                    <span class="text-yellow-500 font-bold">
                        {{ str_repeat('★', $t->rating) }}{{ str_repeat('☆', 5 - $t->rating) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-gray-600 text-xs leading-relaxed max-w-md truncate md:whitespace-normal" title="{{ $t->body }}">{{ $t->body }}</td>
                <td class="px-4 py-3">
                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $t->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ $t->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </td>
                <td class="px-4 py-3">
                    <a href="{{ route('admin.testimonials.edit', $t) }}" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg transition inline-block">Edit</a>
                    <form action="{{ route('admin.testimonials.destroy', $t) }}" method="POST" class="inline" onsubmit="return confirm('Hapus testimoni ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded-lg transition">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">Belum ada testimoni</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($testimonials->hasPages())
    <div class="p-4 border-t border-gray-100">{{ $testimonials->links() }}</div>
    @endif
</div>
@endsection

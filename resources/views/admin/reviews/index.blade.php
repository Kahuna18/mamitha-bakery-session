@extends('layouts.admin')

@section('title', 'Ulasan Produk')

@section('content')
<div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Ulasan & Rating Produk</h1>
        <p class="text-gray-500 text-sm">Kelola ulasan dan rating pelanggan per produk roti</p>
    </div>
    <a href="{{ route('admin.reviews.create') }}" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium rounded-lg transition">+ Tambah Ulasan</a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-gray-50 text-left">
                <th class="px-4 py-3 font-semibold text-gray-600">Produk</th>
                <th class="px-4 py-3 font-semibold text-gray-600 w-44">Nama Pengulas</th>
                <th class="px-4 py-3 font-semibold text-gray-600 w-32">Rating</th>
                <th class="px-4 py-3 font-semibold text-gray-600">Isi Ulasan</th>
                <th class="px-4 py-3 font-semibold text-gray-600 w-28">Tanggal</th>
                <th class="px-4 py-3 font-semibold text-gray-600 w-36">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($reviews as $r)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-semibold text-amber-900">
                    @if($r->product)
                        {{ $r->product->name }}
                    @else
                        <span class="text-red-500 text-xs italic">Produk Dihapus</span>
                    @endif
                </td>
                <td class="px-4 py-3 font-medium text-gray-800">{{ $r->name }}</td>
                <td class="px-4 py-3">
                    <span class="text-yellow-500 font-bold">
                        {{ str_repeat('★', $r->rating) }}{{ str_repeat('☆', 5 - $r->rating) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-gray-600 text-xs leading-relaxed max-w-md truncate md:whitespace-normal" title="{{ $r->comment }}">{{ $r->comment }}</td>
                <td class="px-4 py-3 text-gray-400 text-xs">{{ $r->created_at->format('d M Y') }}</td>
                <td class="px-4 py-3">
                    <a href="{{ route('admin.reviews.edit', $r) }}" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg transition inline-block">Edit</a>
                    <form action="{{ route('admin.reviews.destroy', $r) }}" method="POST" class="inline" onsubmit="return confirm('Hapus ulasan ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded-lg transition">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">Belum ada ulasan produk</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($reviews->hasPages())
    <div class="p-4 border-t border-gray-100">{{ $reviews->links() }}</div>
    @endif
</div>
@endsection

@extends('layouts.app')

@section('title', $product->name)

@section('content')
<section class="py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <a href="{{ route('menu') }}" class="text-amber-600 hover:text-amber-700 text-sm font-medium">&larr; Kembali ke Menu</a>

        <div class="grid md:grid-cols-2 gap-8 mt-4">
            <div class="relative bg-amber-50 rounded-xl p-8 flex items-center justify-center aspect-square">
                @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover rounded-lg {{ $product->stock <= 0 ? 'grayscale opacity-50' : '' }}">
                @else
                    <span class="text-8xl opacity-30">🍞</span>
                @endif
                <!-- Stock Badge -->
                <div class="absolute top-4 right-4">
                    @if($product->stock <= 0)
                        <span class="bg-red-600 text-white text-xs font-bold px-3 py-1 rounded-full shadow-md">Habis</span>
                    @else
                        <span class="bg-white/90 text-gray-700 text-xs font-bold px-3 py-1 rounded-full shadow-sm">Stok: {{ $product->stock }}</span>
                    @endif
                </div>
                @if($product->stock <= 0)
                <div class="absolute inset-0 bg-gray-900/20 flex items-center justify-center rounded-xl">
                    <span class="bg-red-600 text-white text-lg font-bold px-6 py-2 rounded-full shadow-lg transform -rotate-12">SOLD OUT</span>
                </div>
                @endif
            </div>
            <div>
                <span class="text-sm text-amber-600 font-medium bg-amber-50 px-3 py-1 rounded-full">{{ $product->category->name }}</span>
                <h1 class="text-3xl font-bold text-gray-800 mt-3">{{ $product->name }}</h1>
                <p class="text-3xl font-bold text-amber-700 mt-3">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                <!-- Stock Info -->
                <div class="mt-2">
                    @if($product->stock > 0)
                        <span class="text-sm text-green-600 font-medium">✅ Stok tersedia: {{ $product->stock }} pcs</span>
                    @else
                        <span class="text-sm text-red-600 font-medium">❌ Stok habis</span>
                    @endif
                </div>
                @if($product->description)
                <p class="text-gray-600 mt-4 leading-relaxed">{{ $product->description }}</p>
                @endif
                <div class="mt-6 space-y-3">
                    @if($product->stock > 0)
                    <a href="{{ route('order.create') }}?product={{ $product->id }}" class="block w-full text-center px-6 py-3.5 bg-amber-600 hover:bg-amber-700 text-white font-semibold rounded-xl transition text-base">
                        Pesan Sekarang
                    </a>
                    @else
                    <span class="block w-full text-center px-6 py-3.5 bg-gray-300 text-gray-500 font-semibold rounded-xl cursor-not-allowed text-base">
                        Stok Habis - Tidak Bisa Dipesan
                    </span>
                    @endif
                    <a href="https://wa.me/{{ App\Models\Setting::getValue('store_whatsapp') }}?text=Saya%20ingin%20tanya%20tentang%20{{ urlencode($product->name) }}" target="_blank" class="block w-full text-center px-6 py-3.5 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl transition text-base">
                        Tanya via WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@extends('layouts.app')

@section('title', 'Toko Roti Fresh Setiap Hari')

@section('content')
{{-- HERO SECTION --}}
<section class="relative bg-gradient-to-br from-amber-50 via-cream-50 to-orange-50 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24">
        <div class="grid md:grid-cols-2 gap-8 items-center">
            <div class="text-center md:text-left">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-amber-900 leading-tight mb-4">
                    Roti Fresh Setiap Hari,<br>
                    <span class="text-amber-600">Bisa Pesan Mudah dari Rumah</span>
                </h1>
                <p class="text-lg text-gray-600 mb-8 max-w-lg mx-auto md:mx-0">
                    Nikmati roti hangat dan cake lezat tanpa harus antri. Pesan sekarang dan kami siapkan untuk Anda!
                </p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center md:justify-start">
                    <a href="{{ route('order.create') }}" class="px-8 py-4 bg-amber-600 hover:bg-amber-700 text-white font-semibold text-lg rounded-xl shadow-md transition text-center">
                        Pesan Sekarang
                    </a>
                    <a href="{{ route('menu') }}" class="px-8 py-4 bg-white hover:bg-amber-50 text-amber-700 font-semibold text-lg rounded-xl border-2 border-amber-200 shadow-sm transition text-center">
                        Lihat Menu
                    </a>
                </div>
            </div>
            <div class="relative hidden md:block">
                <div class="rounded-full w-96 h-96 mx-auto overflow-hidden shadow-xl border-4 border-white">
                    <img src="{{ asset('images/logo.jpeg') }}" class="w-full h-full object-cover" alt="Mamitha Logo">
                </div>
                <div class="absolute -bottom-4 -left-4 bg-white rounded-xl shadow-lg p-3">
                    <p class="text-sm font-semibold text-amber-800">Fresh Baked Daily</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- PRODUK UNGGULAN --}}
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
            <h2 class="text-3xl md:text-4xl font-bold text-amber-900 mb-3">Produk Unggulan Kami</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">Pilihan roti dan kue terbaik yang paling disukai pelanggan</p>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
            @forelse($featuredProducts as $product)
            <div class="bg-white rounded-xl border border-amber-100 overflow-hidden hover:shadow-md transition">
                <div class="relative aspect-square bg-amber-50 flex items-center justify-center p-6">
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover rounded-lg {{ $product->stock <= 0 ? 'grayscale opacity-50' : '' }}">
                    @else
                        <span class="text-6xl opacity-30 {{ $product->stock <= 0 ? 'grayscale' : '' }}">🍞</span>
                    @endif
                    <!-- Stock Badge -->
                    <div class="absolute top-2 right-2">
                        @if($product->stock <= 0)
                            <span class="bg-red-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm">Habis</span>
                        @else
                            <span class="bg-white/90 text-gray-700 text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm">Stok: {{ $product->stock }}</span>
                        @endif
                    </div>
                    @if($product->stock <= 0)
                    <div class="absolute inset-0 bg-gray-900/20 flex items-center justify-center rounded-lg">
                        <span class="bg-red-600 text-white text-xs font-bold px-3 py-1 rounded-full shadow-md transform -rotate-12">SOLD OUT</span>
                    </div>
                    @endif
                </div>
                <div class="p-3 md:p-4">
                    <span class="text-xs text-amber-600 font-medium bg-amber-50 px-2 py-0.5 rounded-full">{{ $product->category->name }}</span>
                    <h3 class="font-semibold text-gray-800 mt-2 text-sm md:text-base">{{ $product->name }}</h3>
                    <p class="text-amber-700 font-bold text-base md:text-lg mt-1">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                    @if($product->stock > 0)
                    <a href="{{ route('order.create') }}?product={{ $product->id }}" class="mt-2 block w-full text-center px-3 py-2 bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium rounded-lg transition">
                        Pesan
                    </a>
                    @else
                    <span class="mt-2 block w-full text-center px-3 py-2 bg-gray-300 text-gray-500 text-sm font-medium rounded-lg cursor-not-allowed">
                        Stok Habis
                    </span>
                    @endif
                </div>
            </div>
            @empty
            <p class="col-span-full text-center text-gray-500 py-8">Belum ada produk unggulan.</p>
            @endforelse
        </div>
    </div>
</section>

{{-- CARA PESAN --}}
<section class="py-16 bg-amber-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
            <h2 class="text-3xl md:text-4xl font-bold text-amber-900 mb-3">Cara Pesan</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">Cukup 4 langkah mudah untuk menikmati roti fresh kami</p>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <div class="text-center">
                <div class="w-20 h-20 mx-auto bg-amber-100 rounded-full flex items-center justify-center mb-4">
                    <span class="text-3xl">🥖</span>
                </div>
                <div class="w-10 h-10 mx-auto bg-amber-600 text-white rounded-full flex items-center justify-center text-lg font-bold -mt-12 mb-3 relative z-10 shadow-md">1</div>
                <h3 class="font-semibold text-gray-800">Pilih Roti</h3>
                <p class="text-sm text-gray-500 mt-1">Lihat menu dan pilih roti favorit Anda</p>
            </div>
            <div class="text-center">
                <div class="w-20 h-20 mx-auto bg-amber-100 rounded-full flex items-center justify-center mb-4">
                    <span class="text-3xl">📝</span>
                </div>
                <div class="w-10 h-10 mx-auto bg-amber-600 text-white rounded-full flex items-center justify-center text-lg font-bold -mt-12 mb-3 relative z-10 shadow-md">2</div>
                <h3 class="font-semibold text-gray-800">Isi Data Pesanan</h3>
                <p class="text-sm text-gray-500 mt-1">Nama, nomor WA, dan alamat lengkap</p>
            </div>
            <div class="text-center">
                <div class="w-20 h-20 mx-auto bg-amber-100 rounded-full flex items-center justify-center mb-4">
                    <span class="text-3xl">✅</span>
                </div>
                <div class="w-10 h-10 mx-auto bg-amber-600 text-white rounded-full flex items-center justify-center text-lg font-bold -mt-12 mb-3 relative z-10 shadow-md">3</div>
                <h3 class="font-semibold text-gray-800">Konfirmasi Admin</h3>
                <p class="text-sm text-gray-500 mt-1">Admin akan konfirmasi via WhatsApp</p>
            </div>
            <div class="text-center">
                <div class="w-20 h-20 mx-auto bg-amber-100 rounded-full flex items-center justify-center mb-4">
                    <span class="text-3xl">🎂</span>
                </div>
                <div class="w-10 h-10 mx-auto bg-amber-600 text-white rounded-full flex items-center justify-center text-lg font-bold -mt-12 mb-3 relative z-10 shadow-md">4</div>
                <h3 class="font-semibold text-gray-800">Roti Diproses</h3>
                <p class="text-sm text-gray-500 mt-1">Roti dibuat fresh, siap diambil/diantar</p>
            </div>
        </div>
    </div>
</section>

{{-- TESTIMONI --}}
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
            <h2 class="text-3xl md:text-4xl font-bold text-amber-900 mb-3">Apa Kata Pelanggan</h2>
        </div>
        <div class="grid md:grid-cols-3 gap-6">
            <div class="bg-amber-50 rounded-xl p-6">
                <div class="flex items-center mb-3">
                    <div class="w-12 h-12 bg-amber-200 rounded-full flex items-center justify-center text-xl">👩</div>
                    <div class="ml-3">
                        <p class="font-semibold text-gray-800">Ibu Sari</p>
                        <p class="text-yellow-500 text-sm">★★★★★</p>
                    </div>
                </div>
                <p class="text-gray-600 text-sm leading-relaxed">"Rotinya enak banget, fresh dan lembut. Anak-anak suka banget. Pesan juga gampang, tinggal WA."</p>
            </div>
            <div class="bg-amber-50 rounded-xl p-6">
                <div class="flex items-center mb-3">
                    <div class="w-12 h-12 bg-amber-200 rounded-full flex items-center justify-center text-xl">👨</div>
                    <div class="ml-3">
                        <p class="font-semibold text-gray-800">Pak Bambang</p>
                        <p class="text-yellow-500 text-sm">★★★★★</p>
                    </div>
                </div>
                <p class="text-gray-600 text-sm leading-relaxed">"Pesen snack box untuk rapat kantor, semua puas. Terima kasih Mamitha Bakery!"</p>
            </div>
            <div class="bg-amber-50 rounded-xl p-6">
                <div class="flex items-center mb-3">
                    <div class="w-12 h-12 bg-amber-200 rounded-full flex items-center justify-center text-xl">👩</div>
                    <div class="ml-3">
                        <p class="font-semibold text-gray-800">Ibu Dewi</p>
                        <p class="text-yellow-500 text-sm">★★★★★</p>
                    </div>
                </div>
                <p class="text-gray-600 text-sm leading-relaxed">"Cake ultahnya cantik banget, rasanya enak tidak terlalu manis. Recommended!"</p>
            </div>
        </div>
    </div>
</section>

{{-- KONTAK --}}
<section class="py-16 bg-amber-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
            <h2 class="text-3xl md:text-4xl font-bold text-amber-900 mb-3">Hubungi Kami</h2>
            <p class="text-gray-600">Senang mendengar dari Anda</p>
        </div>
        <div class="grid md:grid-cols-3 gap-6 max-w-4xl mx-auto">
            <div class="bg-white rounded-xl p-6 text-center shadow-sm">
                <div class="w-14 h-14 mx-auto bg-green-100 rounded-full flex items-center justify-center mb-3">
                    <svg class="w-7 h-7 text-green-600" fill="currentColor" viewBox="0 0 448 512"><path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6z"/></svg>
                </div>
                <h3 class="font-semibold text-gray-800 mb-2">WhatsApp</h3>
                <p class="text-gray-600 text-sm">{{ $storePhone }}</p>
                <a href="https://wa.me/{{ $storeWhatsapp }}" target="_blank" class="mt-3 inline-block px-5 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg transition">
                    Chat Sekarang
                </a>
            </div>
            <div class="bg-white rounded-xl p-6 text-center shadow-sm">
                <div class="w-14 h-14 mx-auto bg-amber-100 rounded-full flex items-center justify-center mb-3">
                    <svg class="w-7 h-7 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <h3 class="font-semibold text-gray-800 mb-2">Alamat Toko</h3>
                <p class="text-gray-600 text-sm">{{ $storeAddress }}</p>
            </div>
            <div class="bg-white rounded-xl p-6 text-center shadow-sm">
                <div class="w-14 h-14 mx-auto bg-amber-100 rounded-full flex items-center justify-center mb-3">
                    <svg class="w-7 h-7 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="font-semibold text-gray-800 mb-2">Jam Buka</h3>
                <p class="text-gray-600 text-sm">Senin - Sabtu: 07:00 - 20:00</p>
                <p class="text-gray-600 text-sm">Minggu: 08:00 - 18:00</p>
            </div>
        </div>
    </div>
</section>
@endsection

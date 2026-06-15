@extends('layouts.app')

@section('title', 'Tentang Kami')

@section('content')
<section class="bg-amber-50 py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl md:text-4xl font-bold text-amber-900 mb-2">Tentang Kami</h1>
        <p class="text-gray-600">Kenali lebih dekat Mamitha Bakery</p>
    </div>
</section>

<section class="py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-2 gap-8 items-center">
            <div>
                <div class="rounded-xl overflow-hidden shadow-lg border border-amber-200">
                    <img src="{{ asset('images/logo.jpeg') }}" class="w-full h-auto object-cover" alt="Mamitha Logo">
                </div>
            </div>
            <div>
                <h2 class="text-3xl font-bold text-amber-900 mb-4">Mamitha Bakery</h2>
                <p class="text-gray-600 leading-relaxed mb-4">{{ $aboutText }}</p>
                <p class="text-gray-600 leading-relaxed">Kami berkomitmen untuk selalu menyajikan produk terbaik dengan bahan-bahan berkualitas dan higienis. Setiap produk dibuat dengan penuh cinta dan perhatian terhadap detail.</p>
            </div>
        </div>
    </div>
</section>
@endsection

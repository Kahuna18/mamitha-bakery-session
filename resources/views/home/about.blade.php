@extends('layouts.app')

@section('title', 'Tentang Kami')

@section('content')
<section class="bg-amber-50 dark:bg-gray-900/50 py-10 transition-colors duration-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl md:text-4xl font-bold text-amber-900 dark:text-amber-400 mb-2 font-serif">Tentang Kami</h1>
        <p class="text-gray-600 dark:text-gray-400">Kenali lebih dekat Mamitha Bakery</p>
    </div>
</section>

<section class="py-12 bg-white dark:bg-gray-900 min-h-screen transition-colors duration-200">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-2 gap-8 items-center">
            <div>
                <div class="rounded-xl overflow-hidden shadow-lg border border-amber-200 dark:border-gray-700">
                    <img src="{{ asset('images/logo.jpeg') }}" class="w-full h-auto object-cover" alt="Mamitha Logo">
                </div>
            </div>
            <div>
                <h2 class="text-3xl font-bold text-amber-900 dark:text-amber-400 mb-4 font-serif">Mamitha Bakery</h2>
                <p class="text-gray-655 dark:text-gray-300 leading-relaxed mb-4">{{ $aboutText }}</p>
                <p class="text-gray-655 dark:text-gray-300 leading-relaxed">Kami berkomitmen untuk selalu menyajikan produk terbaik dengan bahan-bahan berkualitas dan higienis. Setiap produk dibuat dengan penuh cinta dan perhatian terhadap detail.</p>
            </div>
        </div>
    </div>
</section>
@endsection

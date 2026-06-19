@extends('layouts.app')

@section('title', 'Menu Roti')

@section('content')
<section class="bg-amber-50 dark:bg-gray-900/50 py-10 transition-colors duration-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl md:text-4xl font-bold text-amber-900 dark:text-amber-400 mb-2 font-serif">Menu Roti</h1>
        <p class="text-gray-600 dark:text-gray-450">Pilih roti favorit Anda, pesan dengan mudah</p>
    </div>
</section>

<section class="py-8 bg-white dark:bg-gray-900 transition-colors duration-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row gap-4 mb-8">
            <div class="flex-1">
                <input type="text" id="search-input" placeholder="Cari roti..." class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 dark:text-white dark:placeholder-gray-500 text-base">
            </div>
            <div class="flex gap-2 flex-wrap">
                <a href="{{ route('menu') }}" class="px-4 py-2.5 rounded-lg text-sm font-medium {{ !request('category') ? 'bg-amber-600 text-white' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700' }} transition">
                    Semua
                </a>
                @foreach($categories as $cat)
                <a href="{{ route('menu') }}?category={{ $cat->slug }}" class="px-4 py-2.5 rounded-lg text-sm font-medium {{ request('category') == $cat->slug ? 'bg-amber-600 text-white' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700' }} transition">
                    {{ $cat->name }}
                </a>
                @endforeach
            </div>
        </div>

        <div id="products-grid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
            @include('home.products-grid')
        </div>

        <div class="mt-8">
            {{ $products->links() }}
        </div>
    </div>
</section>

@push('scripts')
<script>
    let searchTimeout;
    const searchInput = document.getElementById('search-input');
    const productsGrid = document.getElementById('products-grid');

    searchInput?.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            const search = this.value;
            const params = new URLSearchParams(window.location.search);
            params.set('search', search);
            params.set('category', '{{ request('category') }}');

            fetch(`{{ route('menu') }}?${params.toString()}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.text())
            .then(html => {
                productsGrid.innerHTML = html;
            });
        }, 300);
    });
</script>
@endpush
@endsection

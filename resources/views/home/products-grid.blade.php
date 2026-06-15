@forelse($products as $product)
<div class="bg-white rounded-xl border border-amber-100 overflow-hidden hover:shadow-md transition">
    <div class="aspect-square bg-amber-50 flex items-center justify-center p-6">
        @if($product->image)
            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover rounded-lg">
        @else
            <span class="text-6xl opacity-30">🍞</span>
        @endif
    </div>
    <div class="p-3 md:p-4">
        <span class="text-xs text-amber-600 font-medium bg-amber-50 px-2 py-0.5 rounded-full">{{ $product->category->name }}</span>
        <h3 class="font-semibold text-gray-800 mt-2 text-sm md:text-base">{{ $product->name }}</h3>
        <p class="text-amber-700 font-bold text-base md:text-lg mt-1">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
        <a href="{{ route('order.create') }}?product={{ $product->id }}" class="mt-2 block w-full text-center px-3 py-2 bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium rounded-lg transition">
            Pesan
        </a>
    </div>
</div>
@empty
<p class="col-span-full text-center text-gray-500 py-12 text-lg">Produk tidak ditemukan.</p>
@endforelse

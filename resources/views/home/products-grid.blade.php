@forelse($products as $product)
<div class="bg-white dark:bg-gray-800 rounded-xl border border-amber-100 dark:border-gray-700 overflow-hidden hover:shadow-md transition">
    <div class="relative aspect-square bg-amber-50 dark:bg-gray-900 flex items-center justify-center p-6">
        @if($product->image)
            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover rounded-lg {{ $product->stock <= 0 ? 'grayscale opacity-50' : '' }}">
        @else
            <span class="text-6xl opacity-30 {{ $product->stock <= 0 ? 'grayscale' : '' }}">🍞</span>
        @endif
        <!-- Tags overlay -->
        @if($product->is_featured)
        <div class="absolute top-2 left-2" style="position: absolute; top: 8px; left: 8px; z-index: 10;">
            <span class="bg-amber-500 text-white text-[9px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider shadow-sm">
                🔥 Best Seller
            </span>
        </div>
        @endif
        <!-- Stock Badge -->
        <div class="absolute top-2 right-2" style="position: absolute; top: 8px; right: 8px; z-index: 10;">
            @if($product->stock <= 0)
                <span class="bg-red-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm">Habis</span>
            @else
                <span class="bg-white/90 dark:bg-gray-800/90 text-gray-700 dark:text-gray-200 text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm">Stok: {{ $product->stock }}</span>
            @endif
        </div>
        @if($product->stock <= 0)
        <div class="absolute inset-0 bg-gray-900/20 flex items-center justify-center rounded-lg" style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; background-color: rgba(17, 24, 39, 0.2); z-index: 5;">
            <span class="bg-red-600 text-white text-xs font-bold px-3 py-1 rounded-full shadow-md transform -rotate-12">SOLD OUT</span>
        </div>
        @endif
    </div>
    <div class="p-3 md:p-4">
        <span class="text-xs text-amber-600 dark:text-amber-400 font-medium bg-amber-50 dark:bg-amber-950/30 px-2 py-0.5 rounded-full">{{ $product->category->name }}</span>
        <h3 class="font-semibold text-gray-800 dark:text-gray-150 mt-2 text-sm md:text-base">{{ $product->name }}</h3>
        <p class="text-amber-700 dark:text-amber-400 font-bold text-base md:text-lg mt-1">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
        @if($product->stock > 0)
        <a href="{{ route('order.create') }}?product={{ $product->id }}" class="mt-2 block w-full text-center px-3 py-2 bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium rounded-lg transition">
            Pesan
        </a>
        @else
        <span class="mt-2 block w-full text-center px-3 py-2 bg-gray-300 dark:bg-gray-700 text-gray-500 dark:text-gray-400 text-sm font-medium rounded-lg cursor-not-allowed">
            Stok Habis
        </span>
        @endif
    </div>
</div>
@empty
<p class="col-span-full text-center text-gray-500 dark:text-gray-450 py-12 text-lg">Produk tidak ditemukan.</p>
@endforelse

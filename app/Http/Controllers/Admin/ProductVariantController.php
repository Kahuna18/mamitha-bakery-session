<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class ProductVariantController extends Controller
{
    public function index(Product $product)
    {
        $variants = $product->variants()->latest()->paginate(20);
        return view('admin.variants.index', compact('product', 'variants'));
    }

    public function create(Product $product)
    {
        return view('admin.variants.create', compact('product'));
    }

    public function store(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'price_adjustment' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        $validated['is_available'] = $request->boolean('is_available');

        $product->variants()->create($validated);

        return redirect()->route('admin.products.variants.index', $product)
            ->with('success', 'Varian berhasil ditambahkan.');
    }

    public function edit(Product $product, ProductVariant $variant)
    {
        return view('admin.variants.edit', compact('product', 'variant'));
    }

    public function update(Request $request, Product $product, ProductVariant $variant)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'price_adjustment' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        $validated['is_available'] = $request->boolean('is_available');

        $variant->update($validated);

        return redirect()->route('admin.products.variants.index', $product)
            ->with('success', 'Varian berhasil diperbarui.');
    }

    public function destroy(Product $product, ProductVariant $variant)
    {
        $variant->delete();

        return redirect()->route('admin.products.variants.index', $product)
            ->with('success', 'Varian berhasil dihapus.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Http\Request;

class ProductReviewController extends Controller
{
    public function index()
    {
        $reviews = ProductReview::with('product')->latest()->paginate(20);
        return view('admin.reviews.index', compact('reviews'));
    }

    public function create()
    {
        $products = Product::orderBy('name')->get();
        return view('admin.reviews.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'name' => 'required|string|max:100',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string',
        ]);

        ProductReview::create($validated);
        $this->updateProductRating($validated['product_id']);

        return redirect()->route('admin.reviews.index')->with('success', 'Ulasan produk berhasil ditambahkan.');
    }

    public function edit(ProductReview $review)
    {
        $products = Product::orderBy('name')->get();
        return view('admin.reviews.edit', compact('review', 'products'));
    }

    public function update(Request $request, ProductReview $review)
    {
        $oldProductId = $review->product_id;
        
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'name' => 'required|string|max:100',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string',
        ]);

        $review->update($validated);
        
        $this->updateProductRating($validated['product_id']);
        if ($oldProductId != $validated['product_id']) {
            $this->updateProductRating($oldProductId);
        }

        return redirect()->route('admin.reviews.index')->with('success', 'Ulasan produk berhasil diperbarui.');
    }

    public function destroy(ProductReview $review)
    {
        $productId = $review->product_id;
        $review->delete();
        $this->updateProductRating($productId);
        
        return redirect()->route('admin.reviews.index')->with('success', 'Ulasan produk berhasil dihapus.');
    }

    private function updateProductRating($productId)
    {
        $product = Product::find($productId);
        if ($product) {
            $averageRating = ProductReview::where('product_id', $productId)->avg('rating');
            $product->update(['rating' => $averageRating ? round($averageRating, 2) : 4.90]);
        }
    }
}

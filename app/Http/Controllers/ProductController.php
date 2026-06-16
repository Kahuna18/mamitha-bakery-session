<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        $categories = Category::where('is_active', true)->get();
        $categorySlug = request('category');
        $search = request('search');

        $query = Product::with('category')->where('is_available', true);

        if ($categorySlug) {
            $category = Category::where('slug', $categorySlug)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $products = $query->latest()->paginate(12);

        if (request()->ajax()) {
            return view('home.products-grid', compact('products'))->render();
        }

        return view('home.menu', compact('categories', 'products'));
    }

    public function show($slug)
    {
        $product = Product::with('category')
            ->where('slug', $slug)
            ->where('is_available', true)
            ->firstOrFail();

        return view('home.product-detail', compact('product'));
    }
}

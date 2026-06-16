<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->latest()->paginate(20);
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_available' => 'boolean',
            'is_featured' => 'boolean',
            'stock' => 'integer|min:0',
            'rating' => 'required|numeric|min:0|max:5',
            'ready_time' => 'required|string|max:50',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_available'] = $request->boolean('is_available');
        $validated['is_featured'] = $request->boolean('is_featured');

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            
            if (!file_exists(public_path('uploads/products'))) {
                mkdir(public_path('uploads/products'), 0755, true);
            }
            $file->move(public_path('uploads/products'), $filename);
            
            if (!file_exists(storage_path('app/public/products'))) {
                mkdir(storage_path('app/public/products'), 0755, true);
            }
            copy(public_path('uploads/products/' . $filename), storage_path('app/public/products/' . $filename));

            $validated['image'] = 'uploads/products/' . $filename;
        }

        Product::create($validated);

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_available' => 'boolean',
            'is_featured' => 'boolean',
            'stock' => 'integer|min:0',
            'rating' => 'required|numeric|min:0|max:5',
            'ready_time' => 'required|string|max:50',
        ]);

        $validated['is_available'] = $request->boolean('is_available');
        $validated['is_featured'] = $request->boolean('is_featured');

        if ($request->hasFile('image')) {
            if ($product->image) {
                if (str_starts_with($product->image, 'uploads/products/')) {
                    $oldPath = public_path($product->image);
                    if (file_exists($oldPath)) {
                        @unlink($oldPath);
                    }
                    $oldStoragePath = storage_path('app/public/products/' . basename($product->image));
                    if (file_exists($oldStoragePath)) {
                        @unlink($oldStoragePath);
                    }
                } elseif (str_starts_with($product->image, 'products/')) {
                    $oldStoragePath = storage_path('app/public/' . $product->image);
                    if (file_exists($oldStoragePath)) {
                        @unlink($oldStoragePath);
                    }
                    $oldPublicPath = public_path('uploads/products/' . basename($product->image));
                    if (file_exists($oldPublicPath)) {
                        @unlink($oldPublicPath);
                    }
                }
            }

            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            
            if (!file_exists(public_path('uploads/products'))) {
                mkdir(public_path('uploads/products'), 0755, true);
            }
            $file->move(public_path('uploads/products'), $filename);
            
            if (!file_exists(storage_path('app/public/products'))) {
                mkdir(storage_path('app/public/products'), 0755, true);
            }
            copy(public_path('uploads/products/' . $filename), storage_path('app/public/products/' . $filename));

            $validated['image'] = 'uploads/products/' . $filename;
        }

        $product->update($validated);

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        if ($product->image) {
            if (str_starts_with($product->image, 'uploads/products/')) {
                $oldPath = public_path($product->image);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
                $oldStoragePath = storage_path('app/public/products/' . basename($product->image));
                if (file_exists($oldStoragePath)) {
                    @unlink($oldStoragePath);
                }
            } elseif (str_starts_with($product->image, 'products/')) {
                $oldStoragePath = storage_path('app/public/' . $product->image);
                if (file_exists($oldStoragePath)) {
                    @unlink($oldStoragePath);
                }
                $oldPublicPath = public_path('uploads/products/' . basename($product->image));
                if (file_exists($oldPublicPath)) {
                    @unlink($oldPublicPath);
                }
            }
        }
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil dihapus.');
    }
}

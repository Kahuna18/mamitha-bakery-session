<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->latest()->paginate(20);
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            
            if (!file_exists(public_path('uploads/categories'))) {
                mkdir(public_path('uploads/categories'), 0755, true);
            }
            $file->move(public_path('uploads/categories'), $filename);
            
            if (!file_exists(storage_path('app/public/categories'))) {
                mkdir(storage_path('app/public/categories'), 0755, true);
            }
            copy(public_path('uploads/categories/' . $filename), storage_path('app/public/categories/' . $filename));

            $validated['image'] = 'uploads/categories/' . $filename;
        }

        Category::create($validated);

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name,' . $category->id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            if ($category->image) {
                if (str_starts_with($category->image, 'uploads/categories/')) {
                    $oldPath = public_path($category->image);
                    if (file_exists($oldPath)) {
                        @unlink($oldPath);
                    }
                    $oldStoragePath = storage_path('app/public/categories/' . basename($category->image));
                    if (file_exists($oldStoragePath)) {
                        @unlink($oldStoragePath);
                    }
                } elseif (str_starts_with($category->image, 'categories/')) {
                    $oldStoragePath = storage_path('app/public/' . $category->image);
                    if (file_exists($oldStoragePath)) {
                        @unlink($oldStoragePath);
                    }
                    $oldPublicPath = public_path('uploads/categories/' . basename($category->image));
                    if (file_exists($oldPublicPath)) {
                        @unlink($oldPublicPath);
                    }
                }
            }

            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            
            if (!file_exists(public_path('uploads/categories'))) {
                mkdir(public_path('uploads/categories'), 0755, true);
            }
            $file->move(public_path('uploads/categories'), $filename);
            
            if (!file_exists(storage_path('app/public/categories'))) {
                mkdir(storage_path('app/public/categories'), 0755, true);
            }
            copy(public_path('uploads/categories/' . $filename), storage_path('app/public/categories/' . $filename));

            $validated['image'] = 'uploads/categories/' . $filename;
        }

        $category->update($validated);

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        if ($category->products()->count() > 0) {
            return back()->with('error', 'Kategori tidak bisa dihapus karena masih memiliki produk.');
        }

        if ($category->image) {
            if (str_starts_with($category->image, 'uploads/categories/')) {
                $oldPath = public_path($category->image);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
                $oldStoragePath = storage_path('app/public/categories/' . basename($category->image));
                if (file_exists($oldStoragePath)) {
                    @unlink($oldStoragePath);
                }
            } elseif (str_starts_with($category->image, 'categories/')) {
                $oldStoragePath = storage_path('app/public/' . $category->image);
                if (file_exists($oldStoragePath)) {
                    @unlink($oldStoragePath);
                }
                $oldPublicPath = public_path('uploads/categories/' . basename($category->image));
                if (file_exists($oldPublicPath)) {
                    @unlink($oldPublicPath);
                }
            }
        }
        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil dihapus.');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    protected $fillable = [
        'product_id', 'image_path'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getImageUrlAttribute()
    {
        if (!$this->image_path) {
            return null;
        }

        if (str_starts_with($this->image_path, 'http://') || str_starts_with($this->image_path, 'https://')) {
            return $this->image_path;
        }

        if (str_starts_with($this->image_path, 'products/')) {
            if (file_exists(public_path('storage/' . $this->image_path))) {
                return asset('storage/' . $this->image_path);
            }
            $filename = basename($this->image_path);
            if (file_exists(public_path('uploads/products/' . $filename))) {
                return asset('uploads/products/' . $filename);
            }
            return asset('storage/' . $this->image_path);
        }

        return asset($this->image_path);
    }
}

<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $catRoti = Category::where('slug', 'roti-manis')->first();
        $catCake = Category::where('slug', 'cake')->first();
        $catSnack = Category::where('slug', 'snack-box')->first();
        $catDonat = Category::where('slug', 'donat')->first();
        $catPaket = Category::where('slug', 'paket-acara')->first();

        $products = [
            // Roti Manis
            ['category_id' => $catRoti->id, 'name' => 'Roti Coklat', 'price' => 15000, 'is_featured' => true, 'is_available' => true, 'description' => 'Roti manis isi coklat lezat', 'stock' => 50, 'image' => 'https://images.unsplash.com/photo-1607958996333-41aef7caefaa?auto=format&fit=crop&w=600&q=80'],
            ['category_id' => $catRoti->id, 'name' => 'Roti Keju', 'price' => 16000, 'is_featured' => true, 'is_available' => true, 'description' => 'Roti manis dengan taburan keju', 'stock' => 50, 'image' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?auto=format&fit=crop&w=600&q=80'],
            ['category_id' => $catRoti->id, 'name' => 'Roti Sosis', 'price' => 18000, 'is_featured' => false, 'is_available' => true, 'description' => 'Roti dengan isian sosis', 'stock' => 50, 'image' => 'https://images.unsplash.com/photo-1549931319-a545dcf3bc73?auto=format&fit=crop&w=600&q=80'],
            ['category_id' => $catRoti->id, 'name' => 'Roti Kacang', 'price' => 14000, 'is_featured' => false, 'is_available' => true, 'description' => 'Roti manis isi kacang hijau', 'stock' => 50, 'image' => 'https://images.unsplash.com/photo-1555507036-ab1f4038808a?auto=format&fit=crop&w=600&q=80'],
            ['category_id' => $catRoti->id, 'name' => 'Roti Nanas', 'price' => 15000, 'is_featured' => false, 'is_available' => true, 'description' => 'Roti manis dengan selai nanas', 'stock' => 0, 'image' => 'https://images.unsplash.com/photo-1608686207856-001b95cf60ca?auto=format&fit=crop&w=600&q=80'],

            // Cake
            ['category_id' => $catCake->id, 'name' => 'Black Forest', 'price' => 150000, 'is_featured' => true, 'is_available' => true, 'description' => 'Cake black forest premium', 'stock' => 50, 'image' => 'https://images.unsplash.com/photo-1606313564200-e75d5e30476c?auto=format&fit=crop&w=600&q=80'],
            ['category_id' => $catCake->id, 'name' => 'Red Velvet', 'price' => 160000, 'is_featured' => true, 'is_available' => true, 'description' => 'Red velvet cake with cream cheese', 'stock' => 50, 'image' => 'https://images.unsplash.com/photo-1616541823729-00fe0aacd32c?auto=format&fit=crop&w=600&q=80'],
            ['category_id' => $catCake->id, 'name' => 'Bolu Kukus', 'price' => 75000, 'is_featured' => false, 'is_available' => true, 'description' => 'Bolu kukus lembut klasik', 'stock' => 50, 'image' => 'https://images.unsplash.com/photo-1519869325930-281384150729?auto=format&fit=crop&w=600&q=80'],

            // Snack Box
            ['category_id' => $catSnack->id, 'name' => 'Snack Box Mini', 'price' => 25000, 'is_featured' => false, 'is_available' => true, 'description' => 'Box snack mini untuk acara', 'stock' => 50, 'image' => 'https://images.unsplash.com/photo-1551024601-bec78aea704b?auto=format&fit=crop&w=600&q=80'],
            ['category_id' => $catSnack->id, 'name' => 'Snack Box Besar', 'price' => 45000, 'is_featured' => false, 'is_available' => true, 'description' => 'Box snack besar lengkap', 'stock' => 50, 'image' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?auto=format&fit=crop&w=600&q=80'],

            // Donat
            ['category_id' => $catDonat->id, 'name' => 'Donat Gula', 'price' => 10000, 'is_featured' => true, 'is_available' => true, 'description' => 'Donat tabur gula halus', 'stock' => 50, 'image' => 'https://images.unsplash.com/photo-1551024601-bec78aea704b?auto=format&fit=crop&w=600&q=80'],
            ['category_id' => $catDonat->id, 'name' => 'Donat Coklat', 'price' => 12000, 'is_featured' => false, 'is_available' => true, 'description' => 'Donat topping coklat', 'stock' => 50, 'image' => 'https://images.unsplash.com/photo-1533089860892-a7c6f0a88666?auto=format&fit=crop&w=600&q=80'],
            ['category_id' => $catDonat->id, 'name' => 'Donat Keju', 'price' => 12000, 'is_featured' => false, 'is_available' => true, 'description' => 'Donat topping keju', 'stock' => 0, 'image' => 'https://images.unsplash.com/photo-1612240498936-65f5101365d2?auto=format&fit=crop&w=600&q=80'],

            // Paket Acara
            ['category_id' => $catPaket->id, 'name' => 'Paket Ulang Tahun', 'price' => 350000, 'is_featured' => true, 'is_available' => true, 'description' => 'Paket kue untuk ulang tahun', 'stock' => 50, 'image' => 'https://images.unsplash.com/photo-1530101121243-c99da9079418?auto=format&fit=crop&w=600&q=80'],
            ['category_id' => $catPaket->id, 'name' => 'Paket Arisan', 'price' => 200000, 'is_featured' => false, 'is_available' => true, 'description' => 'Paket snack untuk arisan', 'stock' => 50, 'image' => 'https://images.unsplash.com/photo-1516685018646-549198525c1b?auto=format&fit=crop&w=600&q=80'],
            ['category_id' => $catPaket->id, 'name' => 'Paket Rapat', 'price' => 150000, 'is_featured' => false, 'is_available' => true, 'description' => 'Paket kue untuk rapat kantor', 'stock' => 50, 'image' => 'https://images.unsplash.com/photo-1534080564583-6be75777b70a?auto=format&fit=crop&w=600&q=80'],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}

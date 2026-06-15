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
            ['category_id' => $catRoti->id, 'name' => 'Roti Coklat', 'price' => 15000, 'is_featured' => true, 'is_available' => true, 'description' => 'Roti manis isi coklat lezat'],
            ['category_id' => $catRoti->id, 'name' => 'Roti Keju', 'price' => 16000, 'is_featured' => true, 'is_available' => true, 'description' => 'Roti manis dengan taburan keju'],
            ['category_id' => $catRoti->id, 'name' => 'Roti Sosis', 'price' => 18000, 'is_featured' => false, 'is_available' => true, 'description' => 'Roti dengan isian sosis'],
            ['category_id' => $catRoti->id, 'name' => 'Roti Kacang', 'price' => 14000, 'is_featured' => false, 'is_available' => true, 'description' => 'Roti manis isi kacang hijau'],
            ['category_id' => $catRoti->id, 'name' => 'Roti Nanas', 'price' => 15000, 'is_featured' => false, 'is_available' => true, 'description' => 'Roti manis dengan selai nanas'],

            // Cake
            ['category_id' => $catCake->id, 'name' => 'Black Forest', 'price' => 150000, 'is_featured' => true, 'is_available' => true, 'description' => 'Cake black forest premium'],
            ['category_id' => $catCake->id, 'name' => 'Red Velvet', 'price' => 160000, 'is_featured' => true, 'is_available' => true, 'description' => 'Red velvet cake with cream cheese'],
            ['category_id' => $catCake->id, 'name' => 'Bolu Kukus', 'price' => 75000, 'is_featured' => false, 'is_available' => true, 'description' => 'Bolu kukus lembut klasik'],

            // Snack Box
            ['category_id' => $catSnack->id, 'name' => 'Snack Box Mini', 'price' => 25000, 'is_featured' => false, 'is_available' => true, 'description' => 'Box snack mini untuk acara'],
            ['category_id' => $catSnack->id, 'name' => 'Snack Box Besar', 'price' => 45000, 'is_featured' => false, 'is_available' => true, 'description' => 'Box snack besar lengkap'],

            // Donat
            ['category_id' => $catDonat->id, 'name' => 'Donat Gula', 'price' => 10000, 'is_featured' => true, 'is_available' => true, 'description' => 'Donat tabur gula halus'],
            ['category_id' => $catDonat->id, 'name' => 'Donat Coklat', 'price' => 12000, 'is_featured' => false, 'is_available' => true, 'description' => 'Donat topping coklat'],
            ['category_id' => $catDonat->id, 'name' => 'Donat Keju', 'price' => 12000, 'is_featured' => false, 'is_available' => true, 'description' => 'Donat topping keju'],

            // Paket Acara
            ['category_id' => $catPaket->id, 'name' => 'Paket Ulang Tahun', 'price' => 350000, 'is_featured' => true, 'is_available' => true, 'description' => 'Paket kue untuk ulang tahun'],
            ['category_id' => $catPaket->id, 'name' => 'Paket Arisan', 'price' => 200000, 'is_featured' => false, 'is_available' => true, 'description' => 'Paket snack untuk arisan'],
            ['category_id' => $catPaket->id, 'name' => 'Paket Rapat', 'price' => 150000, 'is_featured' => false, 'is_available' => true, 'description' => 'Paket kue untuk rapat kantor'],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Roti Manis', 'slug' => 'roti-manis', 'is_active' => true],
            ['name' => 'Cake', 'slug' => 'cake', 'is_active' => true],
            ['name' => 'Snack Box', 'slug' => 'snack-box', 'is_active' => true],
            ['name' => 'Donat', 'slug' => 'donat', 'is_active' => true],
            ['name' => 'Paket Acara', 'slug' => 'paket-acara', 'is_active' => true],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}

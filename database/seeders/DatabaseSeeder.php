<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin Bakery',
            'email' => 'admin@mamitha.com',
            'password' => bcrypt('admin123'),
            'role' => 'admin',
        ]);

        User::factory()->create([
            'name' => 'Staff Kitchen',
            'email' => 'kitchen@mamitha.com',
            'password' => bcrypt('kitchen123'),
            'role' => 'kitchen',
        ]);

        $this->call([
            CategorySeeder::class,
            ProductSeeder::class,
            SettingSeeder::class,
            TestimonialSeeder::class,
            ProductReviewSeeder::class,
        ]);
    }
}

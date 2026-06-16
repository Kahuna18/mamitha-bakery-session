<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        $testimonials = [
            [
                'name' => 'Ibu Sari',
                'rating' => 5,
                'body' => 'Rotinya enak banget, fresh dan lembut. Anak-anak suka banget. Pesan juga gampang, tinggal WA.',
                'avatar' => '👩',
                'is_active' => true,
            ],
            [
                'name' => 'Pak Bambang',
                'rating' => 5,
                'body' => 'Pesen snack box untuk rapat kantor, semua puas. Terima kasih Mamitha Bakery!',
                'avatar' => '👨',
                'is_active' => true,
            ],
            [
                'name' => 'Ibu Dewi',
                'rating' => 5,
                'body' => 'Cake ultahnya cantik banget, rasanya enak tidak terlalu manis. Recommended!',
                'avatar' => '👩',
                'is_active' => true,
            ],
        ];

        foreach ($testimonials as $t) {
            Testimonial::create($t);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Database\Seeder;

class ProductReviewSeeder extends Seeder
{
    public function run(): void
    {
        $reviewsData = [
            'Roti Coklat' => [
                'rating' => 4.9,
                'reviews' => [
                    ['name' => 'Budi Santoso', 'rating' => 5, 'comment' => 'Rotinya sangat lembut, coklatnya meleleh di mulut! Recommended banget.'],
                    ['name' => 'Siti Aminah', 'rating' => 5, 'comment' => 'Anak-anak suka sekali roti coklat ini. Teksturnya empuk.'],
                    ['name' => 'Adit Pratama', 'rating' => 4, 'comment' => 'Rasa coklatnya premium, tapi stocknya sering habis sore-sore. Overall mantap!']
                ]
            ],
            'Roti Keju' => [
                'rating' => 4.8,
                'reviews' => [
                    ['name' => 'Dewi Lestari', 'rating' => 5, 'comment' => 'Taburan kejunya melimpah sampai ke dalam. Gurih dan manisnya pas!'],
                    ['name' => 'Rian Hidayat', 'rating' => 4, 'comment' => 'Tekstur roti sangat empuk. Keju parut di atasnya juga garing enak.']
                ]
            ],
            'Roti Sosis' => [
                'rating' => 4.7,
                'reviews' => [
                    ['name' => 'Eko Prasetyo', 'rating' => 5, 'comment' => 'Sosisnya berasa dagingnya, bukan tepung doang. Saus dan mayonya juga pas.'],
                    ['name' => 'Fani Wijaya', 'rating' => 4, 'comment' => 'Enak buat sarapan pagi praktis, anak saya lahap sekali makan ini.']
                ]
            ],
            'Roti Kacang' => [
                'rating' => 4.6,
                'reviews' => [
                    ['name' => 'Gita Safitri', 'rating' => 5, 'comment' => 'Isian kacangnya banyak dan wangi. Manisnya tidak bikin eneg.'],
                    ['name' => 'Hendra', 'rating' => 4, 'comment' => 'Roti kacang klasik favorit keluarga sejak dulu. Enak ditemani kopi.']
                ]
            ],
            'Roti Nanas' => [
                'rating' => 4.7,
                'reviews' => [
                    ['name' => 'Indah Kusuma', 'rating' => 5, 'comment' => 'Selai nanasnya segar asam manis buatan sendiri sepertinya. Suka sekali!']
                ]
            ],
            'Black Forest' => [
                'rating' => 5.0,
                'reviews' => [
                    ['name' => 'Joko Widodo', 'rating' => 5, 'comment' => 'Kuenya sangat lembut dan krimnya tidak neg. Coklat serutnya melimpah. Mantap!'],
                    ['name' => 'Kiki Amelia', 'rating' => 5, 'comment' => 'Pesan buat ultah suami, dekorasinya cantik dan rasanya sangat premium. Terima kasih Mamitha!']
                ]
            ],
            'Red Velvet' => [
                'rating' => 4.9,
                'reviews' => [
                    ['name' => 'Lani Marlina', 'rating' => 5, 'comment' => 'Cream cheese frostingnya juara! Asam manis gurih berpadu sempurna.'],
                    ['name' => 'Mawan', 'rating' => 5, 'comment' => 'Warna merahnya cantik alami, rasa kuenya khas coklat lembut red velvet.']
                ]
            ],
            'Bolu Kukus' => [
                'rating' => 4.5,
                'reviews' => [
                    ['name' => 'Novianti', 'rating' => 4, 'comment' => 'Mekar dengan sempurna dan rasanya manis gurih santan. Lembut sekali.'],
                    ['name' => 'Oki', 'rating' => 5, 'comment' => 'Bolu kukus tradisonal yang rasanya sangat otentik. Enak banget.']
                ]
            ],
            'Snack Box Mini' => [
                'rating' => 4.8,
                'reviews' => [
                    ['name' => 'Putri Ayu', 'rating' => 5, 'comment' => 'Cocok banget buat acara rapat kantor kecil. Semua snacknya fresh dan lezat!'],
                    ['name' => 'Qomar', 'rating' => 4, 'comment' => 'Isian snack box rapi dan bervariasi. Sangat membantu untuk konsumsi dadakan.']
                ]
            ],
            'Snack Box Besar' => [
                'rating' => 4.9,
                'reviews' => [
                    ['name' => 'Riska', 'rating' => 5, 'comment' => 'Porsi pas untuk rapat setengah hari. Kue asin dan manisnya enak semua.'],
                    ['name' => 'Soni', 'rating' => 5, 'comment' => 'Pelayanan ramah, boxnya rapi dan estetik, isian roti sosis dan susnya juara.']
                ]
            ],
            'Donat Gula' => [
                'rating' => 4.6,
                'reviews' => [
                    ['name' => 'Tio Nugroho', 'rating' => 5, 'comment' => 'Donat kentang jadul yang super empuk! Taburan gula halusnya pas.'],
                    ['name' => 'Uli', 'rating' => 4, 'comment' => 'Donatnya empuk meskipun disimpan sampai sore. Favorit anak-anak.']
                ]
            ],
            'Donat Coklat' => [
                'rating' => 4.8,
                'reviews' => [
                    ['name' => 'Vina', 'rating' => 5, 'comment' => 'Meses coklatnya tebal sekali dan nempel sempurna. Donatnya empuk dan kenyal.'],
                    ['name' => 'Wawan', 'rating' => 4, 'comment' => 'Enak parah donat coklatnya, harga terjangkau tapi rasa ga murahan.']
                ]
            ],
            'Donat Keju' => [
                'rating' => 4.7,
                'reviews' => [
                    ['name' => 'Yanti', 'rating' => 5, 'comment' => 'Keju parutnya melimpah ruah sampai tumpah-tumpah. Enak sekali!']
                ]
            ],
            'Paket Ulang Tahun' => [
                'rating' => 5.0,
                'reviews' => [
                    ['name' => 'Zainal', 'rating' => 5, 'comment' => 'Sangat praktis! Sudah lengkap kue utama, lilin, pisau, dan piring mini. Sangat membantu.'],
                    ['name' => 'Anisa', 'rating' => 5, 'comment' => 'Kue ultahnya request tulisan rapi sekali dan rasanya enak semua tamu suka.']
                ]
            ],
            'Paket Arisan' => [
                'rating' => 4.8,
                'reviews' => [
                    ['name' => 'Bela', 'rating' => 5, 'comment' => 'Beli paket ini buat arisan keluarga besar. Ibu-ibu pada nanyain belinya di mana. Sukses terus Mamitha!']
                ]
            ],
            'Paket Rapat' => [
                'rating' => 4.9,
                'reviews' => [
                    ['name' => 'Candra', 'rating' => 5, 'comment' => 'Sajian kue kotak premium untuk tamu VVIP rapat dinas. Kemasan mewah dan rasa tidak mengecewakan.']
                ]
            ]
        ];

        foreach ($reviewsData as $productName => $data) {
            $product = Product::where('name', $productName)->first();
            if ($product) {
                // Update default rating
                $product->update(['rating' => $data['rating']]);

                // Create reviews
                foreach ($data['reviews'] as $rev) {
                    ProductReview::create([
                        'product_id' => $product->id,
                        'name' => $rev['name'],
                        'rating' => $rev['rating'],
                        'comment' => $rev['comment']
                    ]);
                }
            }
        }

        // Set remaining products to default rating 4.90 if any
        Product::whereNull('rating')->orWhere('rating', 0)->update(['rating' => 4.90]);
    }
}

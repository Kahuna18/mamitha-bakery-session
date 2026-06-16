<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'store_name', 'value' => 'Mamitha Bakery'],
            ['key' => 'store_address', 'value' => 'Ndukuh malangan, RT.005/RW.044, Malangan, Sumberagung, Kec. Moyudan, Sleman, Yogyakarta 55563'],
            ['key' => 'store_phone', 'value' => '0812-3456-7890'],
            ['key' => 'store_whatsapp', 'value' => '6281234567890'],
            ['key' => 'store_email', 'value' => 'info@mamithabakery.com'],
            ['key' => 'open_time', 'value' => '07:00'],
            ['key' => 'close_time', 'value' => '20:00'],
            ['key' => 'daily_order_limit', 'value' => '0'],
            ['key' => 'is_closed', 'value' => 'false'],
            ['key' => 'about_text', 'value' => 'Mamitha Bakery adalah toko roti rumahan yang menyajikan roti fresh setiap hari dengan bahan-bahan berkualitas. Kami siap melayani pesanan roti, cake, snack box, dan paket acara untuk Anda.'],
            ['key' => 'google_maps_api_key', 'value' => ''],
            ['key' => 'store_latitude', 'value' => '-7.7609582'],
            ['key' => 'store_longitude', 'value' => '110.2529556'],
            ['key' => 'store_gmaps_link', 'value' => 'https://maps.app.goo.gl/X5XV5KcBZou4mbQT8'],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }
}

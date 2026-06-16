<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Testimonial;

class HomeController extends Controller
{
    public function index()
    {
        $featuredProducts = Product::with('category')
            ->where('is_available', true)
            ->where('is_featured', true)
            ->take(8)
            ->get();

        $categories = Category::where('is_active', true)->get();
        $testimonials = Testimonial::where('is_active', true)->latest()->get();
        $storeName = Setting::getValue('store_name');
        $storePhone = Setting::getValue('store_phone');
        $storeWhatsapp = Setting::getValue('store_whatsapp');
        $storeAddress = Setting::getValue('store_address');

        return view('home.index', compact(
            'featuredProducts', 'categories', 'testimonials',
            'storeName', 'storePhone', 'storeWhatsapp', 'storeAddress'
        ));
    }

    public function howToOrder()
    {
        return view('home.how-to-order');
    }

    public function about()
    {
        $aboutText = Setting::getValue('about_text', 'Tentang kami...');
        return view('home.about', compact('aboutText'));
    }

    public function contact()
    {
        $storePhone = Setting::getValue('store_phone');
        $storeWhatsapp = Setting::getValue('store_whatsapp');
        $storeAddress = Setting::getValue('store_address');
        $storeEmail = Setting::getValue('store_email');
        $openTime = Setting::getValue('open_time', '07:00');
        $closeTime = Setting::getValue('close_time', '20:00');

        return view('home.contact', compact(
            'storePhone', 'storeWhatsapp', 'storeAddress', 'storeEmail', 'openTime', 'closeTime'
        ));
    }
}

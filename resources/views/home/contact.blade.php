@extends('layouts.app')

@section('title', 'Kontak')

@section('content')
<section class="bg-amber-50 dark:bg-gray-900 py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl md:text-4xl font-bold text-amber-900 dark:text-amber-450 mb-2 font-serif">Kontak Kami</h1>
        <p class="text-gray-600 dark:text-gray-400 text-sm">Hubungi kami untuk informasi lebih lanjut</p>
    </div>
</section>

<section class="py-12">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-2 gap-8">
            <div class="space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-amber-100 dark:border-gray-700 shadow-sm">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-green-100 dark:bg-green-950/30 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 448 512"><path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157z"/></svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800 dark:text-white">WhatsApp</h3>
                            <p class="text-gray-600 dark:text-gray-400 text-sm">{{ $storePhone }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-amber-100 dark:border-gray-700 shadow-sm">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-amber-100 dark:bg-amber-950/30 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-amber-600 dark:text-amber-450" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800 dark:text-white">Alamat</h3>
                            <p class="text-gray-600 dark:text-gray-400 text-sm">{{ $storeAddress }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-amber-100 dark:border-gray-700 shadow-sm">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-amber-100 dark:bg-amber-950/30 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-amber-600 dark:text-amber-455" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800 dark:text-white">Email</h3>
                            <p class="text-gray-600 dark:text-gray-400 text-sm">{{ $storeEmail }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-amber-100 dark:border-gray-700 shadow-sm">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-amber-100 dark:bg-amber-950/30 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-amber-600 dark:text-amber-455" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800 dark:text-white">Jam Operasional</h3>
                            <p class="text-gray-600 dark:text-gray-400 text-sm font-semibold">Senin - Sabtu: {{ $openTime }} - {{ $closeTime }}</p>
                            <p class="text-gray-600 dark:text-gray-400 text-sm">Minggu: {{ \App\Models\Setting::getValue('open_time_sunday', '08:00') }} - {{ \App\Models\Setting::getValue('close_time_sunday', '18:00') }}</p>
                        </div>
                    </div>
                </div>
                <a href="https://wa.me/{{ $storeWhatsapp }}" target="_blank" class="block w-full text-center px-6 py-3.5 bg-green-600 hover:bg-green-700 text-white font-extrabold rounded-xl transition text-base shadow-lg shadow-green-600/10 active:scale-95">
                    <svg class="w-5 h-5 inline mr-2" fill="currentColor" viewBox="0 0 448 512"><path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157z"/></svg>
                    Pesan via WhatsApp
                </a>
            </div>
            <div>
                <div class="bg-gray-200 dark:bg-gray-800 rounded-2xl h-80 flex items-center justify-center p-1 border border-amber-100/50 dark:border-gray-700/50 shadow-sm overflow-hidden">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3953.271986185861!2d110.25038067404924!3d-7.760952876966746!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7af7e11f6dcb4b%3A0x63158b6cfa3c0255!2sMamitha%20bakery!5e0!3m2!1sen!2sid!4v1781620147486!5m2!1sen!2sid" width="100%" height="100%" style="border:0; border-radius: 12px;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

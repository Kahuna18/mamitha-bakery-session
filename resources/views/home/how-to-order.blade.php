@extends('layouts.app')

@section('title', 'Cara Pesan')

@section('content')
<section class="bg-amber-50 py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl md:text-4xl font-bold text-amber-900 mb-2">Cara Pesan</h1>
        <p class="text-gray-600">Mudah dan cepat, ikuti langkah berikut</p>
    </div>
</section>

<section class="py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="space-y-8">
            <div class="flex items-start gap-6">
                <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="text-2xl">🥖</span>
                </div>
                <div class="bg-amber-50 rounded-xl p-6 flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="w-8 h-8 bg-amber-600 text-white rounded-full flex items-center justify-center text-sm font-bold">1</span>
                        <h3 class="text-xl font-bold text-gray-800">Pilih Roti Favorit Anda</h3>
                    </div>
                    <p class="text-gray-600">Lihat menu kami, pilih roti, cake, atau snack box yang Anda inginkan. Kami menyediakan berbagai pilihan roti fresh setiap hari.</p>
                </div>
            </div>

            <div class="flex items-start gap-6">
                <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="text-2xl">📝</span>
                </div>
                <div class="bg-amber-50 rounded-xl p-6 flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="w-8 h-8 bg-amber-600 text-white rounded-full flex items-center justify-center text-sm font-bold">2</span>
                        <h3 class="text-xl font-bold text-gray-800">Isi Data Pesanan</h3>
                    </div>
                    <p class="text-gray-600">Lengkapi data diri: nama, nomor WhatsApp, dan alamat. Pilih mau ambil di toko atau diantar. Tentukan juga tanggal pengambilan.</p>
                </div>
            </div>

            <div class="flex items-start gap-6">
                <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="text-2xl">✅</span>
                </div>
                <div class="bg-amber-50 rounded-xl p-6 flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="w-8 h-8 bg-amber-600 text-white rounded-full flex items-center justify-center text-sm font-bold">3</span>
                        <h3 class="text-xl font-bold text-gray-800">Admin Konfirmasi</h3>
                    </div>
                    <p class="text-gray-600">Admin kami akan menghubungi Anda via WhatsApp untuk konfirmasi pesanan dan memberikan informasi pembayaran.</p>
                </div>
            </div>

            <div class="flex items-start gap-6">
                <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="text-2xl">🎂</span>
                </div>
                <div class="bg-amber-50 rounded-xl p-6 flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="w-8 h-8 bg-amber-600 text-white rounded-full flex items-center justify-center text-sm font-bold">4</span>
                        <h3 class="text-xl font-bold text-gray-800">Roti Diproses & Siap</h3>
                    </div>
                    <p class="text-gray-600">Pesanan Anda akan segera diproses. Roti dibuat fresh, dan siap diambil di toko atau diantar ke alamat Anda.</p>
                </div>
            </div>
        </div>

        <div class="text-center mt-10">
            <a href="{{ route('order.create') }}" class="inline-block px-8 py-4 bg-amber-600 hover:bg-amber-700 text-white font-semibold text-lg rounded-xl shadow-md transition">
                Pesan Sekarang
            </a>
        </div>
    </div>
</section>
@endsection

<x-guest-layout>
    <!-- Tab Controls -->
    <div class="flex border-b border-gray-200 dark:border-gray-700 mb-6 bg-gray-50 dark:bg-gray-800 rounded-xl p-1 gap-1">
        <button type="button" id="tab-member" class="flex-1 py-2.5 text-xs font-black rounded-lg text-center transition-all duration-300 cursor-pointer select-none bg-gradient-to-r from-amber-600 to-orange-600 text-white shadow-md">
            👤 Member (Pelanggan)
        </button>
        <a href="{{ route('login') }}?role=staff" id="tab-staff" 
            class="flex-1 py-2.5 text-xs font-bold rounded-lg text-center transition-all duration-300 cursor-pointer select-none text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 bg-transparent hover:bg-gray-100 dark:hover:bg-gray-700/50 flex items-center justify-center">
            🧑‍🍳 Staff (Pengelola)
        </a>
    </div>

    <div class="text-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white font-serif">Gabung Member</h2>
        <p class="text-gray-500 text-xs mt-1 leading-relaxed">Nikmati prioritas panggangan cepat & diskon member otomatis 10%!</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wider">Nama Lengkap</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 dark:text-white transition @error('name') border-red-500 @enderror"
                placeholder="Masukkan nama lengkap Anda">
            @error('name')
                <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wider">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 dark:text-white transition @error('email') border-red-500 @enderror"
                placeholder="Masukkan email Anda">
            @error('email')
                <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wider">Password</label>
            <input id="password" type="password" name="password" required autocomplete="new-password"
                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 dark:text-white transition @error('password') border-red-500 @enderror"
                placeholder="Buat password minimal 8 karakter">
            @error('password')
                <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wider">Konfirmasi Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 dark:text-white transition @error('password_confirmation') border-red-500 @enderror"
                placeholder="Ulangi password Anda">
            @error('password_confirmation')
                <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit Button -->
        <button type="submit" class="w-full py-4 bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-700 hover:to-orange-700 text-white font-extrabold text-sm rounded-2xl shadow-xl transition-all duration-300 transform active:scale-95 cursor-pointer hover:shadow-orange-500/20">
            Daftar Member Sekarang
        </button>

        <div class="text-center pt-2">
            <p class="text-xs text-gray-500 dark:text-gray-400">
                Sudah punya akun member? 
                <a href="{{ route('login') }}?role=member" class="font-extrabold text-amber-700 hover:text-amber-800 dark:text-amber-500 transition">Login di sini</a>
            </p>
        </div>
    </form>

    <script>
        // Set layout header subtitle to Customer Portal
        window.addEventListener('load', () => {
            const layoutSubtitle = document.getElementById('layout-subtitle');
            if (layoutSubtitle) {
                layoutSubtitle.textContent = "Portal Pelanggan";
            }
        });
    </script>
</x-guest-layout>

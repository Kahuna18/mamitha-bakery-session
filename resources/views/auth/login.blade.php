<x-guest-layout>
    @if (session('status'))
        <div class="mb-4 text-sm text-green-600 bg-green-50 border border-green-200 rounded-lg px-4 py-3">{{ session('status') }}</div>
    @endif

    <div class="text-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800 font-serif">Selamat Datang</h2>
        <p class="text-gray-500 text-sm mt-1">Silakan login untuk melanjutkan</p>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-4">
            <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Email</label>
            <input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-base placeholder-gray-400 transition @error('email') border-red-500 @enderror"
                placeholder="Masukkan email Anda">
            @error('email')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">Password</label>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-base placeholder-gray-400 transition @error('password') border-red-500 @enderror"
                placeholder="Masukkan password">
            @error('password')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between mb-6">
            <label class="flex items-center">
                <input type="checkbox" name="remember" class="rounded border-gray-300 text-amber-600 focus:ring-amber-500" {{ old('remember') ? 'checked' : '' }}>
                <span class="ml-2 text-sm text-gray-600">Ingat saya</span>
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-sm text-amber-600 hover:text-amber-700 font-medium">Lupa password?</a>
            @endif
        </div>

        <button type="submit" class="w-full px-6 py-3.5 bg-amber-600 hover:bg-amber-700 text-white font-semibold text-base rounded-xl shadow-sm transition duration-200">
            Masuk
        </button>

        <div class="mt-6 text-center">
            <p class="text-xs text-gray-400">Hanya untuk admin & staff</p>
        </div>
    </form>
</x-guest-layout>

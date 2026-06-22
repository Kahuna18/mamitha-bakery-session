<x-guest-layout>
    <!-- Back to Home Button -->
    <div class="mb-5 flex justify-start">
        <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-400 hover:text-amber-700 dark:hover:text-amber-400 transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali ke Beranda
        </a>
    </div>

    <!-- Tab Controls -->
    <div class="flex border-b border-gray-200 dark:border-gray-700 mb-6 bg-gray-50 dark:bg-gray-800 rounded-xl p-1 gap-1">
        <button type="button" id="tab-member" onclick="switchTab('member')" 
            class="flex-1 py-2.5 text-xs font-black rounded-lg text-center transition-all duration-300 cursor-pointer select-none">
            👤 Member (Pelanggan)
        </button>
        <button type="button" id="tab-staff" onclick="switchTab('staff')" 
            class="flex-1 py-2.5 text-xs font-black rounded-lg text-center transition-all duration-300 cursor-pointer select-none">
            🧑‍🍳 Staff (Pengelola)
        </button>
    </div>

    @if (session('status'))
        <div class="mb-4 text-sm text-green-600 bg-green-50 border border-green-200 rounded-lg px-4 py-3">{{ session('status') }}</div>
    @endif

    <div class="text-center mb-6">
        <h2 id="login-title" class="text-2xl font-bold text-gray-800 dark:text-white font-serif">Selamat Datang</h2>
        <p id="login-subtitle" class="text-gray-500 text-xs mt-1 leading-relaxed">Silakan login untuk melanjutkan</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="login_role" id="login-role-input" value="member">

        <div>
            <label for="email" class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wider">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 dark:text-white transition @error('email') border-red-500 @enderror"
                placeholder="Masukkan email Anda">
            @error('email')
                <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wider">Password</label>
            <div class="relative">
                <input id="password" type="password" name="password" required autocomplete="current-password"
                    class="w-full pl-4 pr-12 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 dark:text-white transition @error('password') border-red-500 @enderror"
                    placeholder="Masukkan password">
                <button type="button" onclick="togglePasswordVisibility('password', this)" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 cursor-pointer focus:outline-none select-none">
                    <svg class="w-5 h-5 eye-open-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg class="w-5 h-5 eye-closed-icon hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                    </svg>
                </button>
            </div>
            @error('password')
                <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between">
            <label class="flex items-center cursor-pointer select-none">
                <input type="checkbox" name="remember" class="w-4 h-4 rounded text-amber-600 focus:ring-amber-500 border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 transition cursor-pointer" {{ old('remember') ? 'checked' : '' }}>
                <span class="ml-2 text-xs font-semibold text-gray-500 dark:text-gray-400">Ingat saya</span>
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-xs font-extrabold text-amber-700 hover:text-amber-800 dark:text-amber-500 transition">Lupa password?</a>
            @endif
        </div>

        <!-- Submit Button wrapper -->
        <button type="submit" id="submit-btn" class="w-full py-4 text-white font-extrabold text-sm rounded-2xl shadow-xl transition-all duration-300 transform active:scale-95 cursor-pointer">
            Masuk
        </button>

        <!-- Bottom Link / Hint wrapper -->
        <div id="auth-footer" class="text-center pt-2">
            <!-- Dynamic Footer Info -->
        </div>
    </form>

    <script>
        function switchTab(role) {
            const tabMember = document.getElementById('tab-member');
            const tabStaff = document.getElementById('tab-staff');
            const loginTitle = document.getElementById('login-title');
            const loginSubtitle = document.getElementById('login-subtitle');
            const layoutSubtitle = document.getElementById('layout-subtitle');
            const submitBtn = document.getElementById('submit-btn');
            const authFooter = document.getElementById('auth-footer');

            // Save role selection in localstorage
            localStorage.setItem('auth_role', role);

            // Update role hidden input
            const loginRoleInput = document.getElementById('login-role-input');
            if (loginRoleInput) {
                loginRoleInput.value = role;
            }

            // Update URL query parameter without page reload
            const url = new URL(window.location);
            url.searchParams.set('role', role);
            window.history.replaceState({}, '', url);

            if (role === 'member') {
                // Member Tab Active styles
                tabMember.className = "flex-1 py-2.5 text-xs font-black rounded-lg text-center transition-all duration-300 cursor-pointer select-none bg-gradient-to-r from-amber-600 to-orange-600 text-white shadow-md";
                tabStaff.className = "flex-1 py-2.5 text-xs font-bold rounded-lg text-center transition-all duration-300 cursor-pointer select-none text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 bg-transparent hover:bg-gray-100 dark:hover:bg-gray-700/50";
                
                // Content updates
                loginTitle.textContent = "Login Member";
                loginSubtitle.textContent = "Nikmati kemudahan pesan roti hangat & diskon otomatis!";
                if (layoutSubtitle) layoutSubtitle.textContent = "Portal Pelanggan";

                // Submit Button
                submitBtn.textContent = "Masuk sebagai Member";
                submitBtn.className = "w-full py-4 bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-700 hover:to-orange-700 text-white font-extrabold text-sm rounded-2xl shadow-xl transition-all duration-300 transform active:scale-95 cursor-pointer hover:shadow-orange-500/20";

                // Footer Link
                authFooter.innerHTML = `
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Belum punya akun member? 
                        <a href="{{ route('register') }}" class="font-extrabold text-amber-700 hover:text-amber-800 dark:text-amber-500 transition">Daftar Sekarang</a>
                    </p>
                `;
            } else {
                // Staff Tab Active styles
                tabStaff.className = "flex-1 py-2.5 text-xs font-black rounded-lg text-center transition-all duration-300 cursor-pointer select-none bg-gray-950 dark:bg-gray-50 text-white dark:text-gray-950 shadow-md";
                tabMember.className = "flex-1 py-2.5 text-xs font-bold rounded-lg text-center transition-all duration-300 cursor-pointer select-none text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 bg-transparent hover:bg-gray-100 dark:hover:bg-gray-700/50";

                // Content updates
                loginTitle.textContent = "Login Staff & Admin";
                loginSubtitle.textContent = "Sistem Manajemen internal Mamitha Bakery.";
                if (layoutSubtitle) layoutSubtitle.textContent = "Management System";

                // Submit Button
                submitBtn.textContent = "Masuk sebagai Staff";
                submitBtn.className = "w-full py-4 bg-gray-950 dark:bg-gray-50 hover:bg-gray-900 dark:hover:bg-white text-white dark:text-gray-950 font-extrabold text-sm rounded-2xl shadow-xl transition-all duration-300 transform active:scale-95 cursor-pointer hover:shadow-gray-500/10";

                // Footer Link
                authFooter.innerHTML = `
                    <span class="text-xs text-gray-500 dark:text-gray-400 font-semibold uppercase tracking-wider">Khusus Admin & Dapur</span>
                `;
            }
        }

        // Initialize default active tab on load
        window.addEventListener('load', () => {
            const urlParams = new URLSearchParams(window.location.search);
            let role = urlParams.get('role');
            if (!role) {
                role = localStorage.getItem('auth_role');
            }
            if (role !== 'staff') {
                role = 'member';
            }
            switchTab(role);
        });

        // Toggle password input type and eye icon visibility
        function togglePasswordVisibility(inputId, button) {
            const input = document.getElementById(inputId);
            const eyeOpen = button.querySelector('.eye-open-icon');
            const eyeClosed = button.querySelector('.eye-closed-icon');
            
            if (input.type === 'password') {
                input.type = 'text';
                eyeOpen.classList.add('hidden');
                eyeClosed.classList.remove('hidden');
            } else {
                input.type = 'password';
                eyeOpen.classList.remove('hidden');
                eyeClosed.classList.add('hidden');
            }
        }
    </script>
</x-guest-layout>

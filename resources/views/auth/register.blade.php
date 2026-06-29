<x-guest-layout>
    <style>
        .form-control { font-size: 0.9rem !important; padding: 0.7rem 1rem 0.7rem 2.5rem !important; border-radius: 8px !important; border: 1px solid #d1d9e6 !important; }
        .form-control:focus { border-color: #6366f1 !important; box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1) !important; }
        .input-wrapper { position: relative; display: flex; align-items: center; }
        .input-icon { position: absolute; left: 12px; color: #9ca3af; pointer-events: none; }
        .toggle-btn { position: absolute; right: 12px; cursor: pointer; color: #9ca3af; transition: color 0.2s; background: none; border: none; padding: 0; }
        .toggle-btn:hover { color: #6366f1; }
    </style>

    <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white shadow-xl overflow-hidden sm:rounded-3xl border border-gray-100">
        
        <div class="mb-6 text-center">
            <img src="{{ asset('dashui/assets/images/brand/logo-furniture.png') }}" 
                 alt="Logo" class="mx-auto mb-4" style="max-height: 70px;">
            <h4 class="font-bold text-gray-800">Buat Akun Baru</h4>
        </div>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="mb-4">
                <x-input-label for="name" :value="__('Nama Lengkap')" />
                <div class="input-wrapper">
                    <span class="input-icon"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg></span>
                    <x-text-input id="name" class="block mt-1 w-full form-control" type="text" name="name" :value="old('name')" required autofocus />
                </div>
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div class="mb-4">
                <x-input-label for="email" :value="__('Email')" />
                <div class="input-wrapper">
                    <span class="input-icon"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg></span>
                    <x-text-input id="email" class="block mt-1 w-full form-control" type="email" name="email" :value="old('email')" required />
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="mb-4">
                <x-input-label for="password" :value="__('Kata Sandi')" />
                <div class="input-wrapper">
                    <span class="input-icon"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg></span>
                    <x-text-input id="password" class="block mt-1 w-full form-control pr-10"
                                    type="password" name="password" required oninput="checkPasswordStrength(this.value)" />
                    <button type="button" onclick="togglePassword('password')" class="toggle-btn">
                        <svg class="eye-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                </div>
                <div class="mt-2 h-1 w-full bg-gray-200 rounded-full overflow-hidden">
                    <div id="strength-indicator" class="h-1 transition-all bg-gray-200" style="width: 0%"></div>
                </div>
                <span id="strength-text" class="text-xs text-gray-500 font-medium"></span>
            </div>

            <div class="mb-6">
                <x-input-label for="password_confirmation" :value="__('Konfirmasi Kata Sandi')" />
                <div class="input-wrapper">
                    <span class="input-icon"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg></span>
                    <x-text-input id="password_confirmation" class="block mt-1 w-full form-control pr-10"
                                    type="password" name="password_confirmation" required />
                    <button type="button" onclick="togglePassword('password_confirmation')" class="toggle-btn">
                        <svg class="eye-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="flex items-center justify-between mt-6">
                <a class="text-sm text-gray-600 hover:text-indigo-600 underline" href="{{ route('login') }}">
                    {{ __('Sudah punya akun?') }}
                </a>
                <x-primary-button class="ms-4 px-6 py-2 bg-indigo-600 hover:bg-indigo-700 rounded-lg">
                    {{ __('Daftar Sekarang') }}
                </x-primary-button>
            </div>
        </form>
    </div>

    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const button = input.parentElement.querySelector('.toggle-btn');
            const icon = button.querySelector('svg');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
            } else {
                input.type = 'password';
                icon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
            }
        }

        function checkPasswordStrength(password) {
            const indicator = document.getElementById('strength-indicator');
            const text = document.getElementById('strength-text');
            let strength = 0;
            if (password.length > 7) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;

            const colors = ['bg-red-500', 'bg-orange-500', 'bg-yellow-500', 'bg-green-500'];
            const labels = ['Lemah', 'Cukup', 'Kuat', 'Sangat Kuat'];
            
            indicator.style.width = (strength * 25) + '%';
            indicator.className = 'h-1 transition-all ' + (strength > 0 ? colors[strength - 1] : 'bg-gray-200');
            text.innerText = strength > 0 ? labels[strength - 1] : '';
        }
    </script>
</x-guest-layout>
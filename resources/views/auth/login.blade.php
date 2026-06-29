@extends('layouts.blank')

@section('title')
    Login
@endsection

@section('content')
<style>
    .form-control { font-size: 0.9rem !important; padding: 0.7rem 1rem 0.7rem 2.5rem !important; border-radius: 8px !important; border: 1px solid #d1d9e6 !important; }
    .form-control:focus { border-color: #6366f1 !important; box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1) !important; }
    .input-wrapper { position: relative; display: flex; align-items: center; }
    .input-icon { position: absolute; left: 12px; color: #9ca3af; pointer-events: none; }
    .toggle-btn { position: absolute; right: 12px; cursor: pointer; color: #9ca3af; transition: color 0.2s; background: none; border: none; padding: 0; }
    .toggle-btn:hover { color: #6366f1; }
    body { background-color: #f4f6f9 !important; }
</style>

<div class="container d-flex flex-column">
    <div class="row align-items-center justify-content-center g-0 min-vh-100">
        <div class="col-12 col-md-8 col-lg-5 col-xxl-4 py-8 py-xl-0">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-body p-6">
                    
                    <div class="mb-5 text-center"> 
                        <img src="{{ asset('dashui/assets/images/brand/logo-furniture.png') }}" 
                             alt="Logo Inventra" class="mb-3 img-fluid" style="max-height: 70px;">
                        <h4 class="fw-bold">Selamat Datang</h4>
                        <p class="text-muted">Masukkan detail akun Anda untuk melanjutkan.</p>
                    </div>

                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="email" class="form-label fw-medium">{{ __('Email') }}</label>
                            <div class="input-wrapper">
                                <span class="input-icon"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg></span>
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                            </div>
                            @error('email') <span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label fw-medium">{{ __('Kata Sandi') }}</label>
                            <div class="input-wrapper">
                                <span class="input-icon"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg></span>
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror pr-10" name="password" required>
                                <button type="button" onclick="togglePassword('password')" class="toggle-btn">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                </button>
                            </div>
                            @error('password') <span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span> @enderror
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label text-muted" for="remember">{{ __('Ingat saya') }}</label>
                            </div>
                            @if (Route::has('password.request'))
                                <a class="text-decoration-none small" href="{{ route('password.request') }}">{{ __('Lupa kata sandi?') }}</a>
                            @endif
                        </div>

                        <div class="d-grid mb-4">
                            <button type="submit" class="btn btn-primary btn-lg shadow-sm">
                                {{ __('Masuk ke Sistem') }}
                            </button>
                        </div>

                        <div class="text-center">
                            <p class="mb-0 text-muted">Belum punya akun? 
                                <a href="{{ route('register') }}" class="fw-bold text-decoration-none">Daftar sekarang</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePassword(inputId) {
        const input = document.getElementById(inputId);
        const icon = input.parentElement.querySelector('.toggle-btn svg');
        if (input.type === 'password') {
            input.type = 'text';
            icon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
        } else {
            input.type = 'password';
            icon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
        }
    }
</script>
@endsection
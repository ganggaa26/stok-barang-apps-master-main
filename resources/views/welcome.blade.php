<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OSS - Material Furniture</title>
    <link rel="stylesheet" href="{{ asset('dashui/assets/css/theme.css') }}">
    
    <style>
        /* Mengatur latar belakang halaman agar terlihat lebih lembut */
        body { 
            background-color: #f4f6f9 !important; 
        }
        
        /* Desain Kartu (Card) Modern */
        .login-card {
            background: #ffffff;
            border-radius: 25px;
            padding: 40px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.05);
            border: 1px solid #edf2f7;
            width: 100%;
            max-width: 400px;
            margin: auto;
        }
    </style>
</head>

<body class="d-flex align-items-center justify-content-center min-vh-100">
    
    <div class="container text-center">
        <div class="row justify-content-center">
            <div class="col-12">
                
                <div class="login-card">
                    <img src="{{ asset('dashui/assets/images/brand/logo-furniture.png') }}" 
                         alt="Logo Inventra" 
                         class="img-fluid mb-4" 
                         style="max-height: 100px;">
                    
                    <h5 class="fw-bold text-dark mb-4">Sistem Manajemen Material</h5>
                    
                    <div class="d-grid gap-3">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="btn btn-primary btn-lg shadow-sm">Buka Dashboard</a>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-primary btn-lg shadow-sm fw-bold">Log in</a>

                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="btn btn-outline-secondary btn-lg fw-bold">Register</a>
                                @endif
                            @endauth
                        @endif
                    </div>
                </div>
                
                <p class="mt-4 text-muted small">&copy; 2026 PKSD-Inventory. All rights reserved.</p>
            </div>
        </div>
    </div>

</body>
</html>
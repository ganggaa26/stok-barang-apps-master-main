<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>PT Pelangi Kreasi Solusi | @yield('title', 'Sistem Material')</title>

    <link rel="stylesheet" href="{{ asset('dashui/assets/css/theme.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.1/feather.min.css">

    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">

   <style>
        body { font-family: 'Inter', sans-serif; background-color: #f5f7fb; }
        .navbar-brand { font-weight: 700; color: #624bff !important; }

        .collapsing { transition: none !important; }
        #navBahanBaku.collapse.show {
            display: block !important;
            height: auto !important;
            visibility: visible !important;
        }

        /* ==========================================================================
           🎨 PERBAIKAN TOTAL VISUAL TOM SELECT (ANTI-BERTUMPUK & PREMIUM)
           ========================================================================== */
        
        /* Menyembunyikan select bawaan asli secara total agar tidak bertumpuk di belakang */
        select.ts-hidden-accessible {
            border: 0 !important;
            clip: rect(0 0 0 0) !important;
            clip-path: inset(50%) !important;
            height: 1px !important;
            overflow: hidden !important;
            padding: 0 !important;
            position: absolute !important;
            width: 1px !important;
            white-space: nowrap !important;
        }

        /* Kotak utama dropdown */
        .ts-wrapper .ts-control {
            border-radius: 0.375rem !important;
            padding: 0.6rem 1rem !important;
            border: 1px solid #cbd5e1 !important;
            background-color: #ffffff !important;
            color: #334155 !important;
            font-size: 0.9rem !important;
            display: flex !important;
            align-items: center !important;
            min-height: 42px !important;
            box-shadow: none !important;
        }

        /* Menghilangkan input bawaan yang mengacaukan teks pilihan */
        .ts-wrapper .ts-control input {
            display: none !important;
        }

        /* Sisi fokus saat diklik */
        .ts-wrapper.focus .ts-control {
            border-color: #624bff !important; /* Menyesuaikan tema DashUI kamu */
            box-shadow: 0 0 0 3px rgba(98, 75, 255, 0.15) !important;
        }

        /* PANEL FLOATING CARD (Wajib solid background putih agar tidak tembus pandang) */
        .ts-dropdown {
            background-color: #ffffff !important;
            border-radius: 0.5rem !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1) !important;
            margin-top: 4px !important;
            z-index: 1050 !important; /* Memastikan melayang di atas input lainnya */
        }

        /* GARIS PEMBATAS & GAYA HURUF DI SETIAP BARIS DATA */
        .ts-dropdown .option {
            padding: 0.75rem 1rem !important;
            border-bottom: 1px solid #f1f5f9 !important; /* Garis pembatas pesananmu */
            color: #475569 !important;
            font-size: 0.875rem !important;
            cursor: pointer;
            background-color: #ffffff !important;
        }

        .ts-dropdown .option:last-child {
            border-bottom: none !important;
        }

        /* EFEK HOVER KETIKA KURSOR DIGESER */
        .ts-dropdown .option:hover, .ts-dropdown .active {
            background-color: #f8fafc !important; /* Abu-abu terang sangat lembut */
            color: #624bff !important; /* Teks berubah ke warna tema utama */
            font-weight: 500 !important;
        }
       
           /*📱 RESPONSIVE SIDEBAR (HAMBURGER MENU UNTUK LAYAR SEMPIT / HP)*/
      @media (max-width: 991.98px) {
            .navbar-vertical {
                display: block !important;
                position: fixed !important;
                top: 0 !important;
                left: 0 !important;
                margin-left: 0 !important;
                width: 260px !important;
                height: 100vh !important;
                z-index: 1040 !important;
                transition: transform 0.25s ease-in-out !important;
                overflow-y: auto !important;
                background-color: #1a1d29 !important;
                transform: translateX(-260px) !important;
            }
            .navbar-vertical.mobile-show {
                margin-left: 0 !important;
                transform: translateX(0) !important;
            }
            #sidebarOverlay {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(15, 23, 42, 0.45);
                z-index: 1035;
            }
            #sidebarOverlay.mobile-show {
                display: block;
            }
            #page-content {
                width: 100%;
            }
        }

        #btnToggleSidebar {
            background: none;
            border: none;
            font-size: 1.4rem;
            color: #334155;
            margin-right: 0.75rem;
            cursor: pointer;
        }

    </style>
</head>

<body>
    <div id="db-wrapper">
        @include('layouts.sidebar')
        <div id="sidebarOverlay" onclick="toggleSidebarMobile()"></div>
        
        <div id="page-content">
           <nav class="navbar navbar-expand-lg navbar-white bg-white px-4 py-2 border-bottom shadow-sm">
                <div class="container-fluid">
                    <button type="button" id="btnToggleSidebar" class="d-lg-none" onclick="toggleSidebarMobile()" aria-label="Buka menu">
                        &#9776;
                    </button>
                    <span class="navbar-brand fw-bold text-primary mb-0 h1 fs-4">PKSD-Inventaris</span>
                    
                    <div class="ms-auto d-flex align-items-center">
                        <ul class="navbar-nav mb-0">
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle d-flex align-items-center text-gray-800" href="#" id="navbarDropdownProfile" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <div class="text-end me-3 d-none d-sm-block">
                                        <h6 class="mb-0 fw-bold text-dark">{{ Auth::user()->name }}</h6>
                                        <small class="text-muted fw-semi-bold">{{ ucfirst(Auth::user()->role) }}</small>
                                    </div>
                                    <div class="avatar avatar-md avatar-indicators avatar-online">
                                        <img alt="avatar" src="{{ asset('dashui/assets/images/avatar/' . (Auth::user()->role === 'admin' ? 'avatar-8.jpg' : 'avatar-5.jpg')) }}" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                    </div>
                                </a>
                                
                                <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="navbarDropdownProfile" style="min-width: 200px;">
                                    <li class="p-3 text-center">
                                        <img alt="avatar" src="{{ asset('dashui/assets/images/avatar/' . (Auth::user()->role === 'admin' ? 'avatar-8.jpg' : 'avatar-5.jpg')) }}" class="rounded-circle mb-2" style="width: 80px; height: 80px; object-fit: cover;">
                                    </li>
                                    <li class="dropdown-header border-bottom pb-2 mb-2">
                                        <h6 class="mb-0 fw-bold text-dark">{{ Auth::user()->name }}</h6>
                                        <small class="text-muted">{{ Auth::user()->email }}</small>
                                    </li>
                                    <li>
                                        <form action="{{ route('logout') }}" method="POST" class="m-0 p-0">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-danger fw-bold d-flex align-items-center border-0 bg-transparent w-100 py-2">
                                                <span class="me-2">Keluar Aplikasi</span> 
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <div class="container-fluid p-4">
                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('dashui/assets/js/sidebarMenu.js') }}"></script>
    <script src="{{ asset('dashui/assets/js/main.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

    <script>
        function toggleSidebarMobile() {
            document.querySelector('.navbar-vertical').classList.toggle('mobile-show');
            document.getElementById('sidebarOverlay').classList.toggle('mobile-show');
        }
    </script>
</body>
</html>
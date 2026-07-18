<nav class="navbar-vertical navbar">
    <div class="nav-scroller">
        <a class="navbar-brand fw-bold text-white fs-3" href="#">
            PKSD-Inventaris
        </a>
        
        <ul class="navbar-nav flex-column" id="sideNavbar">
            
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <i class="nav-icon icon-xs me-2">📊</i> Dashboard
                </a>
            </li>

            @if(auth()->user()->role === 'admin')
            <li class="nav-item">
                <div class="navbar-heading">Data Master</div>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('material.category') ? 'active' : '' }}" href="{{ route('material.category') }}">
                    <i class="nav-icon icon-xs me-2">🏷️</i> Kategori Material
                </a>
            </li>
            <li class="nav-item">
                <div class="navbar-heading">Manajemen Material</div>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ Request::is('material/pokok') || Request::is('material/pembantu') ? 'active' : 'collapsed' }}" 
                href="#!" 
                data-bs-toggle="collapse" 
                data-bs-target="#navBahanBaku" 
                aria-expanded="{{ Request::is('material/pokok') || Request::is('material/pembantu') ? 'true' : 'false' }}" 
                aria-controls="navBahanBaku">
                    <i class="nav-icon icon-xs me-2">📦</i> Input Stok
                </a>
                <div id="navBahanBaku" 
                class="collapse {{ Request::is('material/pokok') || Request::is('material/pembantu') ? 'show' : '' }}">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('material/pokok') ? 'active fw-bold' : '' }}" href="{{ route('material.pokok') }}">
                                Material Pokok
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('material/pembantu') ? 'active fw-bold' : '' }}" href="{{ route('material.pembantu') }}">
                                Material Pembantu
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

              <li class="nav-item">
                <div class="navbar-heading">Administrasi</div>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                    <i class="nav-icon icon-xs me-2">👤</i> Kelola Pengguna
                </a>
            </li>
            @endif

            <li class="nav-item">
                <div class="navbar-heading">Pelaporan</div>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('laporan.stok') ? 'active' : '' }}" href="{{ route('laporan.stok') }}">
                    <i class="nav-icon icon-xs me-2">📋</i> Laporan Stok
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('laporan.masuk') ? 'active' : '' }}" href="{{ route('laporan.masuk') }}">
                    <i class="nav-icon icon-xs me-2">📋</i> Laporan Barang Masuk
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('laporan.keluar') ? 'active' : '' }}" href="{{ route('laporan.keluar') }}">
                    <i class="nav-icon icon-xs me-2">📋</i> Laporan Barang Keluar
                </a>
            </li>

        </ul>
    </div>
</nav>
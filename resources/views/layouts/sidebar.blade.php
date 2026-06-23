<nav class="navbar-vertical navbar">
    <div class="nav-scroller">
        <a class="navbar-brand fw-bold text-white fs-3" href="#">
            PKSD-Inventory
        </a>
        
        <ul class="navbar-nav flex-column" id="sideNavbar">
            
            <li class="nav-item">
                <a class="nav-link active" href="{{ route('dashboard') }}">
                    <i class="nav-icon icon-xs me-2">📊</i> Dashboard
                </a>
            </li>

            <li class="nav-item">
                <div class="navbar-heading">Manajemen Material</div>
            </li>

            <li class="nav-item">
                <a class="nav-link collapsed" href="#!" data-bs-toggle="collapse" data-bs-target="#navBahanBaku" aria-expanded="false" aria-controls="navBahanBaku">
                    <i class="nav-icon icon-xs me-2">📦</i> Bahan Baku
                </a>
                <div id="navBahanBaku" class="collapse" data-bs-parent="#sideNavbar">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('material.pokok') }}">Material Pokok</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('material.pembantu') }}">Material Pembantu</a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="nav-item">
                <div class="navbar-heading">Data Master</div>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('material.category') }}">
                    <i class="nav-icon icon-xs me-2">🏷️</i> Kategori Material
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('material.supplier') }}">
                    <i class="nav-icon icon-xs me-2">📦</i> Supplier
                </a>
            </li>
            <!-- <li class="nav-item">

                <div class="navbar-heading">Aktivitas Gudang</div>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="nav-icon icon-xs me-2">📥</i> Barang Masuk
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="nav-icon icon-xs me-2">📤</i> Barang Keluar
                </a>
            </li> -->

            <li class="nav-item">
                <div class="navbar-heading">Pelaporan</div>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('laporan.stok') }}">
                    <i class="nav-icon icon-xs me-2">📋</i> Laporan Stok
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('laporan.masuk') }}">
                    <i class="nav-icon icon-xs me-2">📋</i> Laporan Barang Masuk
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('laporan.keluar') }}">
                    <i class="nav-icon icon-xs me-2">📋</i> Laporan Barang Keluar
                </a>

        </ul>
    </div>
</nav>
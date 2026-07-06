@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')

<style>
    .card { border-radius: 1rem; }
    .icon-shape {
        width: 55px; height: 55px;
        border-radius: 1rem;
        display: flex; align-items: center; justify-content: center;
    }
    .text-muted-xs { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700; }
    
    /* Styling Tabel */
    .table thead { background-color: #f1f5f9; }
    .badge-soft-danger { 
        background-color: #fee2e2; 
        color: #dc2626; 
        font-weight: bold;
        padding: 0.4rem 0.6rem;
        border-radius: 0.5rem;
    }
    .text-danger-bold { color: #dc2626; font-weight: 700; }
</style>

<div class="container-fluid px-4 py-4">
    {{-- Header Section --}}
    <div class="row mb-4">
        <div class="col-md-12">
            <h3 class="fw-bold text-dark mb-1">Halo, {{ ucfirst(Auth::user()->role) }}!</h3>
            <p class="text-muted">Selamat datang kembali. Berikut adalah ringkasan inventaris Anda.</p>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="row g-4 mb-5">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted-xs text-primary mb-1">Stok Terdaftar</div>
                        <h2 class="fw-bold mb-0">{{ $totalMaterial ?? '0' }}</h2>
                        <small class="text-muted">Material unik</small>
                    </div>
                    <div class="icon-shape" style="background-color: #e0e7ff; color: #4f46e5;">
                        <i data-feather="box"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted-xs text-warning mb-1">Aktivitas</div>
                        <h2 class="fw-bold mb-0">{{ $totalTransaksi ?? '0' }}</h2>
                        <small class="text-muted">Transaksi bulan ini</small>
                    </div>
                    <div class="icon-shape" style="background-color: #fef3c7; color: #d97706;">
                        <i data-feather="repeat"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted-xs text-danger mb-1">Peringatan</div>
                        <h2 class="fw-bold text-danger mb-0">{{ count($materialMenipis) }}</h2>
                        <small class="text-muted">Butuh restock</small>
                    </div>
                    <div class="icon-shape" style="background-color: #fee2e2; color: #dc2626;">
                        <i data-feather="alert-triangle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Judul Tabel (Terpisah) --}}
    <div class="row mb-3">
        <div class="col-12">
            <h5 class="fw-bold text-danger mb-0">
                <i data-feather="alert-circle" class="me-2"></i>Stok Material Menipis
            </h5>
        </div>
    </div>

    {{-- Tabel Peringatan --}}
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="text-uppercase" style="font-size: 0.75rem;">
                                <tr>
                                    <th class="ps-4">Kode</th>
                                    <th>Nama Bahan Baku</th>
                                    <th>Jenis</th>
                                    <th>Kualitas</th>
                                    <th>Qty</th>
                                    <th>Lokasi</th>
                                    <th>Sisa Stok</th>
                                    <th>Stok Minimum</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($materialMenipis as $item)
                                    <tr style="border-left: 4px solid #dc2626;">
                                        <td class="ps-4 fw-bold">{{ $item->kode_material }}</td>
                                        <td>{{ $item->nama_material }}</td>
                                        <td>{{ $item->jenis_material }}</td>
                                        <td>{{ $item->kualitas ?? '-' }}</td>
                                        <td>{{ $item->size }} {{ $item->satuan }}</td>
                                        <td>{{ $item->lokasi_gudang ?? '-' }}</td>
                                        <td class="text-danger-bold">{{ $item->stok_sekarang }}</td>
                                        <td class="text-muted">{{ $item->stok_minimum }}</td>
                                        <td><span class="badge badge-soft-danger">Butuh Restock</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-5">
                                            <div class="d-flex flex-column align-items-center text-success">
                                                <i data-feather="check-circle" style="width: 40px; height: 40px;" class="mb-2"></i>
                                                <h6 class="fw-bold mb-0">Stok Terkendali</h6>
                                                <p class="text-muted mb-0">Semua material dalam batas aman.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/feather-icons"></script>
<script>
    feather.replace();
</script>

@endsection
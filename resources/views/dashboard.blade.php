<style>
    .card {
        width: 100%;
    }

    .table-responsive {
        width: 100%;
    }

    .table {
        width: 100%;
    }

    .card-body {
        padding: 0;
    }

    /* Gaya tambahan agar ikon di dalam kartu summary terlihat cantik */
    .icon-shape {
        width: 48px;
        height: 48px;
        background-position: center;
        background-repeat: no-repeat;
        background-size: 24px;
        border-radius: 0.75rem;
    }
</style>

@extends('layouts.admin') {{-- Memanggil kerangka utama dashboard kamu --}}

@section('title', 'Dashboard')

@section('content')

<div class="container-fluid px-4 py-3">
    
    <!-- Ucapan Selamat Datang -->
    <div class="row mb-4">
        <div class="col-md-12">
            <h3 class="fw-bold text-dark mb-1">Selamat Datang, {{ ucfirst(Auth::user()->role) }}</h3>
            <p class="text-muted">Berikut adalah rangkuman aktivitas sistem PKSD-Inventory hari ini.</p>
        </div>
    </div>

    <!-- Ringkasan Grid Card Stats (Top) -->
    <div class="row g-4 mb-5">
        <div class="col-xl-3 col-md-6 col-12">
            <div class="card border-0 shadow-sm p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted text-uppercase small fw-bold tracking-wider">Material Terdaftar</span>
                        <h3 class="fw-bold mb-0 mt-1">{{ $totalMaterial ?? '0' }}</h3>
                        <small class="text-muted">Jumlah material unik</small>
                    </div>
                    <div class="icon-shape bg-light-primary text-primary d-flex align-items-center justify-content-center" style="background-color: #00FF00; color: #2f27ca;">
                        <i data-feather="box" style="width: 24px; height: 24px;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-12">
            <div class="card border-0 shadow-sm p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted text-uppercase small fw-bold tracking-wider">Supplier Aktif</span>
                        <h3 class="fw-bold mb-0 mt-1">{{ $totalSupplier ?? '0' }}</h3>
                        <small class="text-muted">Pemasok terjalin</small>
                    </div>
                    <div class="icon-shape bg-light-info text-info d-flex align-items-center justify-content-center" style="background-color: #0000FF; color: #059669;">
                        <i data-feather="users" style="width: 24px; height: 24px;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-12">
            <div class="card border-0 shadow-sm p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted text-uppercase small fw-bold tracking-wider">Transaksi Bulan Ini</span>
                        <h3 class="fw-bold mb-0 mt-1">{{ $totalTransaksi ?? '0' }}</h3>
                        <small class="text-muted">Barang masuk & keluar</small>
                    </div>
                    <div class="icon-shape bg-light-warning text-warning d-flex align-items-center justify-content-center" style="background-color: #fef3c7; color: #d97706;">
                        <i data-feather="shopping-cart" style="width: 24px; height: 24px;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-12">
            <div class="card border-0 shadow-sm p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted text-uppercase small fw-bold tracking-wider">Peringatan Hari Ini</span>
                        <h3 class="fw-bold text-danger mb-0 mt-1">{{ count($materialMenipis) }}</h3>
                        <small class="text-muted">Material butuh restock</small>
                    </div>
                    <div class="icon-shape bg-light-danger text-danger d-flex align-items-center justify-content-center" style="background-color: #fee2e2; color: #dc2626;">
                        <i data-feather="alert-triangle" style="width: 24px; height: 24px;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bagian Utama: Tabel Peringatan Stok & Grafik Aktivitas -->
    <div class="row g-4">
        
        <!-- Tabel Peringatan (Diperlebar menjadi col-12 agar pas di layar dashboard utama) -->
        <div class="col-xl-12 col-lg-12 col-md-12 col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex align-items-center border-bottom">
                    <div class="bg-danger-soft text-danger p-2 rounded-3 me-3" style="background-color: #fee2e2; border-radius: 0.5rem;">
                        <i data-feather="alert-triangle" class="nav-icon icon-xs text-danger"></i>
                    </div>
                    <div>
                        <h4 class="mb-0 fw-bold text-danger">Peringatan: Stok Material Menipis!</h4>
                        <small class="text-muted">Segera hubungi supplier untuk melakukan pemesanan ulang bahan baku di bawah ini.</small>
                    </div>
                </div>
                
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Kode</th>
                                    <th>Nama Bahan Baku</th>
                                    <th>Jenis</th>
                                    <th>Kualitas</th>
                                    <th>Ukuran</th>
                                    <th>Lokasi</th>
                                    <th>Sisa Stok</th>
                                    <th>Stok Minimum</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($materialMenipis as $item)
                                    <tr>
                                        <td class="ps-4 fw-bold text-secondary">{{ $item->kode_material }}</td>
                                        <td>{{ $item->nama_material }}</td>
                                        <td>
                                            <span class="badge {{ $item->jenis_material == 'Pokok' ? 'bg-primary' : 'bg-warning' }}">
                                                {{ $item->jenis_material }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-dark fw-medium">{{ $item->kualitas ?? '-' }}</span>
                                        </td>
                                        <td>{{ $item->size }} {{ $item->satuan }}</td>
                                        <td>
                                            @if($item->lokasi_gudang || $item->blok_area)
                                               <small class="text-muted">{{ $item->lokasi_gudang ?? '-' }} ({{ $item->blok_area ?? '-' }})</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-danger fw-bold">{{ $item->stok_sekarang }}</td>
                                        <td class="text-muted">{{ $item->stok_minimum }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-light-danger text-danger" style="background-color: #fee2e2;">Butuh Restock</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-5 text-muted">
                                            <i data-feather="check-circle" class="text-success mb-2" style="width: 40px; height: 40px;"></i>
                                            <p class="mb-0 fw-bold">Semua aman! Tidak ada material yang kehabisan stok.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bagian Grafik Aktivitas Gudang -->
        <div class="col-12 mt-4">
            <div class="card border-0 shadow-sm p-4">
                <div class="mb-3">
                    <h4 class="fw-bold text-dark mb-0">Aktivitas Gudang: 7 Hari Terakhir</h4>
                    <small class="text-muted">Perbandingan log barang masuk dan keluar</small>
                </div>
                <div style="position: relative; height: 150px; width: 100%;">
                    <canvas id="aktivitasGudangChart"></canvas>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Render ulang feather icons jika diperlukan
        if (typeof feather !== 'undefined') {
            feather.replace();
        }

        // Inisialisasi Chart.js (Grafik Batang Ganda)
        const ctx = document.getElementById('aktivitasGudangChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['1', '2', '3', '4', '5', '6', '7'], // Representasi hari/tanggal
                datasets: [
                    {
                        label: 'Barang Masuk',
                        data: [1100, 800, 1400, 1500, 700, 900, 1400], 
                        backgroundColor: '#3b82f6', // Biru terang
                        borderRadius: 4
                    },
                    {
                        label: 'Barang Keluar',
                        data: [1250, 600, 700, 800, 400, 0, 500], 
                        backgroundColor: '#f97316', // Orange terang
                        borderRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            boxWidth: 15,
                            font: { size: 12, weight: 'bold' }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false }
                    },
                    y: {
                        beginAtZero: true,
                        max: 1600,
                        ticks: { stepSize: 200 }
                    }
                }
            }
        });
    });
</script>
@endsection
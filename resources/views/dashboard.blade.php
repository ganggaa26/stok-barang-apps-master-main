@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')

<style>
    .table-professional {
    border-collapse: collapse;
    width: 100%;
}

.table-professional thead th {
    background-color: #f8fafc;
    color: #475569;
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: 14px 16px;
    border-bottom: 2px solid #e2e8f0;
    border-top: 1px solid #e2e8f0;
    white-space: nowrap;
}

.table-professional tbody td {
    padding: 14px 16px;
    border-bottom: 1px solid #eef1f5;
    vertical-align: middle;
    font-size: 0.9rem;
    color: #334155;
}

.table-professional tbody tr {
    transition: background-color 0.15s ease;
}

.table-professional tbody tr:hover {
    background-color: #fafbfc;
}

.table-professional tbody tr:last-child td {
    border-bottom: none;
}
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
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <a href="{{ route('laporan.stok') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm p-4 h-100">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted-xs text-primary mb-1">Stok Terdaftar</div>
                            <h2 class="fw-bold mb-0 text-dark">{{ $totalMaterial ?? '0' }}</h2>
                            <small class="text-muted">Material unik</small>
                        </div>
                        <div class="icon-shape" style="background-color: #e0e7ff; color: #4f46e5;">
                            <i data-feather="box"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-md-6">
            <a href="{{ route('laporan.masuk') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm p-4 h-100">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted-xs text-success mb-1">Barang Masuk</div>
                            <h2 class="fw-bold mb-0 text-dark">{{ $totalBarangMasuk ?? '0' }}</h2>
                            <small class="text-muted">Transaksi bulan ini</small>
                        </div>
                        <div class="icon-shape" style="background-color: #d1fae5; color: #059669;">
                            <i data-feather="arrow-down-circle"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-md-6">
            <a href="{{ route('laporan.keluar') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm p-4 h-100">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted-xs text-warning mb-1">Barang Keluar</div>
                            <h2 class="fw-bold mb-0 text-dark">{{ $totalBarangKeluar ?? '0' }}</h2>
                            <small class="text-muted">Transaksi bulan ini</small>
                        </div>
                        <div class="icon-shape" style="background-color: #fef3c7; color: #d97706;">
                            <i data-feather="arrow-up-circle"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-md-6">
            <a href="{{ route('laporan.stok') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm p-4 h-100">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted-xs text-danger mb-1">Peringatan</div>
                            {{-- $totalMaterialMenipis = angka total dari controller (COUNT sebelum take(5)) --}}
                            <h2 class="fw-bold text-danger mb-0">{{ $totalMaterialMenipis ?? 0 }}</h2>
                            <small class="text-muted">Butuh restock</small>
                        </div>
                        <div class="icon-shape" style="background-color: #fee2e2; color: #dc2626;">
                            <i data-feather="alert-triangle"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    {{-- Chart: Weekly Transaction Trends --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <h6 class="fw-bold text-dark mb-0">Weekly Transaction Trends</h6>
                    <div class="d-flex gap-3 small">
                        <span class="d-flex align-items-center gap-1">
                            <span style="width:10px;height:10px;border-radius:50%;background:#059669;display:inline-block;"></span>
                            Incoming
                        </span>
                        <span class="d-flex align-items-center gap-1">
                            <span style="width:10px;height:10px;border-radius:50%;background:#d97706;display:inline-block;"></span>
                            Outgoing
                        </span>
                    </div>
                </div>
                <div class="chart-wrapper">
                    <canvas id="weeklyTrendChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Judul Widget Stok Menipis --}}
    <div class="row mb-3" style="margin-top: 2rem;">
    <div class="col-12">
        @if(($totalMaterialMenipis ?? 0) > 0)
            <div class="d-flex align-items-center justify-content-between p-3 rounded-3"
                 style="background-color: #fee2e2; border: 1px solid #fca5a5;">
                <div class="d-flex align-items-center gap-2">
                    <i data-feather="alert-triangle" style="color:#dc2626; width:22px; height:22px;"></i>
                    <span class="fw-semibold" style="color:#dc2626; font-size:1rem;">Stok Material Menipis</span>
                    <span class="badge rounded-pill" style="background-color:#dc2626; color:#fff;">
                        {{ $totalMaterialMenipis }}
                    </span>
                </div>
                <a href="{{ route('laporan.stok') }}" class="btn btn-sm btn-outline-danger fw-bold">
                    Lihat Semua <i data-feather="arrow-right" style="width:14px;height:14px;"></i>
                </a>
            </div>
        @else
            <div class="d-flex align-items-center justify-content-between p-3 rounded-3"
                 style="background-color: #d1fae5; border: 1px solid #6ee7b7;">
                <div class="d-flex align-items-center gap-2">
                    <i data-feather="check-circle" style="color:#059669; width:22px; height:22px;"></i>
                    <span class="fw-semibold" style="color:#059669; font-size:1rem;">Stok Material Aman</span>
                </div>
                <a href="{{ route('laporan.stok') }}" class="btn btn-sm btn-outline-success fw-bold">
                    Lihat Semua <i data-feather="arrow-right" style="width:14px;height:14px;"></i>
                </a>
            </div>
        @endif
    </div>
</div>

    {{-- Widget: hanya 5 item paling kritis, tanpa pagination --}}
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-professional align-middle mb-0">
                           <thead class="text-uppercase" style="font-size: 0.75rem;">
                                <tr>
                                    <th class="ps-4">Kode</th>
                                    <th>Nama Bahan Baku</th>
                                    <th>Jenis</th>
                                    <th>Kualitas</th>
                                    <th>Sisa Stok</th>
                                    <th>Stok Minimum</th>
                                    <th>Selisih</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- $materialMenipis di controller HARUS sudah di-limit(5) dan diurutkan
                                     dari selisih (stok_minimum - stok_sekarang) paling besar.
                                     Format angka: Sisa Stok = 6 desimal, Stok Minimum & Selisih = 4 desimal --}}
                                @forelse($materialMenipis as $item)
                                    <tr style="border-left: 4px solid #dc2626;">
                                        <td class="ps-4 fw-bold">{{ $item->kode_material }}</td>
                                        <td class="fw-semibold">{{ $item->nama_material }}</td>
                                        <td>{{ $item->jenis_material ?? $item->kategori_nama ?? '-' }}</td>
                                        <td>{{ $item->kualitas ?? '-' }}</td>
                                        <td class="text-danger-bold">{{ number_format($item->stok_sekarang, 6) }}</td>
                                        <td class="text-muted">{{ number_format($item->stok_minimum, 4) }}</td>
                                        <td class="text-danger-bold">-{{ number_format($item->stok_minimum - $item->stok_sekarang, 4) }}</td>
                                        <td><span class="badge badge-soft-danger">Butuh Restock</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5">
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

                    @if(($totalMaterialMenipis ?? 0) > 5)
                        <div class="px-4 py-3 text-center border-top">
                            <small class="text-muted">
                                Menampilkan 5 dari {{ $totalMaterialMenipis }} material yang butuh restock.
                                <a href="{{ route('laporan.stok') }}" class="fw-bold text-danger">Lihat semua &rarr;</a>
                            </small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/feather-icons"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
@php
    // Siapkan data chart di sini (bukan langsung di dalam @json(...))
    // supaya argumen @json selalu berupa satu variabel tunggal yang aman.
    $chartLabels   = $weeklyLabels   ?? ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
    $chartIncoming = $weeklyIncoming ?? [30, 45, 25, 60, 75, 55, 40];
    $chartOutgoing = $weeklyOutgoing ?? [20, 35, 55, 30, 40, 25, 35];
@endphp

<script>
    feather.replace();

    const ctx = document.getElementById('weeklyTrendChart');

    // Guard: kalau chart sebelumnya masih hidup di canvas ini (misal karena
    // navigasi tanpa full reload), destroy dulu supaya tidak error
    // "Canvas is already in use" saat init ulang.
    const existingChart = Chart.getChart(ctx);
    if (existingChart) {
        existingChart.destroy();
    }

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($chartLabels),
            datasets: [
                {
                    label: 'Incoming',
                    data: @json($chartIncoming),
                    borderColor: '#059669',
                    backgroundColor: 'rgba(5, 150, 105, 0.08)',
                    tension: 0.35,
                    fill: true,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#059669',
                },
                {
                    label: 'Outgoing',
                    data: @json($chartOutgoing),
                    borderColor: '#d97706',
                    backgroundColor: 'rgba(217, 119, 6, 0.08)',
                    tension: 0.35,
                    fill: true,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#d97706',
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            layout: {
                padding: { top: 10, right: 16, bottom: 0, left: 8 }
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                }
            },
            interaction: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                x: {
                    offset: true,
                    grid: { display: false },
                    ticks: { padding: 8 }
                },
                y: {
                    beginAtZero: true,
                    suggestedMax: 100,
                    grid: { color: '#f1f5f9' }
                }
            }
        }
    });
</script>

@endsection
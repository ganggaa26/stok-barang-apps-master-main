@extends('layouts.admin')

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<nav aria-label="breadcrumb" class="mb-2">
    <ol class="breadcrumb mb-0" style="font-size: 0.8rem;">
        <li class="breadcrumb-item">
            <a href="{{ route('dashboard') }}" class="breadcrumb-link">Data Master</a>
        </li>
        <li class="breadcrumb-item active text-muted" aria-current="page">Kategori Material</li>
    </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Data Kategori Inventaris</h2>
    <a href="{{ route('material.category.create') }}" class="btn btn-primary">+ Tambah Kategori</a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-clean align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4" style="width: 6%">No</th>
                        <th style="width: 16%">Kategori</th>
                        <th style="width: 18%">Sub Kategori</th>
                        <th style="width: 14%">Kelompok Material</th>
                        <th style="width: 10%">Satuan Dasar</th>
                        <th style="width: 24%">Item Material</th>
                        <th style="width: 12%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = 1; @endphp
                    @forelse ($categories as $cat)
                        <tr>
                            <td class="ps-4">{{ $no++ }}</td>
                            <td>{{ $cat->kategori ?? '-' }}</td>
                            <td>{{ $cat->nama_Kategori }}</td>
                            <td>
                                <span class="badge {{ $cat->kelompok_material == 'Material Pokok' ? 'bg-primary' : 'bg-success' }}">
                                    {{ $cat->kelompok_material }}
                                </span>
                            </td>
                            <td>{{ $cat->satuan_dasar }}</td>
                            <td>
                                @if($cat->items->isEmpty())
                                    <span class="text-muted fst-italic">Belum ada item</span>
                                @else
                                    <ul class="item-list">
                                        @foreach($cat->items as $item)
                                            <li>{{ $item->nama_material }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('material.category.edit', $cat->id) }}" class="icon-edit" title="Edit">
                                    <i data-feather="edit-2" style="width:16px;height:16px;"></i>
                                </a>
                                <button type="button" class="icon-delete"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalHapusKategori"
                                    data-id="{{ $cat->id }}"
                                    data-nama="{{ $cat->nama_Kategori }}"
                                    data-jumlah-item="{{ $cat->items->count() }}"
                                    title="Hapus">
                                    <i data-feather="trash-2" style="width:16px;height:16px;"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Belum ada data kategori.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="modalHapusKategori" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body p-4">
                <div class="d-flex align-items-start gap-3">
                    <div class="text-warning fs-3">⚠️</div>
                    <div>
                        <h5 class="mb-2">Konfirmasi Hapus Kategori</h5>
                        <p class="mb-0 text-muted" id="modalHapusPesan">
                            Apakah Anda yakin ingin menghapus kategori ini? Tindakan ini bersifat permanen.
                        </p>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <form id="formHapusKategori" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Ya, Hapus Data</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .breadcrumb-link {
        color: #4f46e5; /* indigo-600, biru default */
        text-decoration: none;
        transition: color 0.15s ease;
    }
    .breadcrumb-link:hover {
        color: #6c757d; /* abu-abu pas hover */
        text-decoration: underline;
    }

    .table-clean {
        border-collapse: collapse;
        width: 100%;
        border: 1px solid #e2e8f0;
    }
    .table-clean thead th {
        background-color: #f8fafc;
        color: #64748b;
        font-size: 0.78rem;
        font-weight: 600;
        padding: 12px 16px;
        border-bottom: 1px solid #e2e8f0;
        border-right: 1px solid #e2e8f0;
    }
    .table-clean thead th:last-child {
        border-right: none;
    }
    .table-clean tbody td {
        padding: 14px 16px;
        border-top: 1px solid #eef1f5;
        border-right: 1px solid #eef1f5;
        font-size: 0.9rem;
        color: #334155;
        vertical-align: middle;
    }
    .table-clean tbody td:last-child {
        border-right: none;
    }
    .table-clean tbody tr:hover {
        background-color: #fafbfc;
    }

    .icon-edit,
    .icon-delete {
        font-size: 17px;
        cursor: pointer;
        border: none;
        background: none;
        padding: 0;
        display: inline-flex;
        align-items: center;
    }
    .icon-edit { color: #64748b; margin-right: 14px; }
    .icon-edit:hover { color: #334155; }
    .icon-delete { color: #dc2626; }
    .icon-delete:hover { color: #991b1b; }

    .item-list {
        list-style: none;
        margin: 0;
        padding: 0;
    }
    .item-list li {
        padding: 4px 0;
    }
    .item-list li:not(:last-child) {
        border-bottom: 1px solid #eef1f5;
    }
</style>

<script src="https://unpkg.com/feather-icons"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modalHapus = document.getElementById('modalHapusKategori');
    const pesanEl = document.getElementById('modalHapusPesan');
    const formEl = document.getElementById('formHapusKategori');

    modalHapus.addEventListener('show.bs.modal', function (event) {
        const btn = event.relatedTarget;
        const id = btn.getAttribute('data-id');
        const nama = btn.getAttribute('data-nama');
        const jumlahItem = parseInt(btn.getAttribute('data-jumlah-item'));

        formEl.action = `{{ url('material/category') }}/${id}`;

        if (jumlahItem > 0) {
            pesanEl.innerHTML = `Kategori <strong>${nama}</strong> tidak bisa dihapus karena masih terhubung dengan <strong>${jumlahItem} item material</strong>. Kategori hanya bisa dihapus jika sudah tidak memiliki item material di dalamnya.`;
            formEl.querySelector('button[type="submit"]').style.display = 'none';
        } else {
            pesanEl.innerHTML = `Apakah Anda yakin ingin menghapus kategori <strong>${nama}</strong>? Tindakan ini bersifat permanen.`;
            formEl.querySelector('button[type="submit"]').style.display = 'inline-block';
        }
    });

    feather.replace();
});
</script>

@endsection
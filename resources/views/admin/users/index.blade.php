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

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Kelola Pengguna</h2>
    <a href="{{ route('users.create') }}" class="btn btn-primary">+ Tambah Pengguna</a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-clean align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4" style="width: 6%">No</th>
                        <th style="width: 28%">Nama</th>
                        <th style="width: 32%">Email</th>
                        <th style="width: 16%">Role</th>
                        <th style="width: 18%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = 1; @endphp
                    @forelse ($users as $user)
                        <tr>
                            <td class="ps-4">{{ $no++ }}</td>
                            <td class="fw-semibold">{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <span class="badge {{ $user->role == 'admin' ? 'bg-primary' : 'bg-secondary' }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('users.edit', $user->id) }}" class="icon-edit" title="Edit">
                                    <i data-feather="edit-2" style="width:16px;height:16px;"></i>
                                </a>
                                <button type="button" class="icon-delete"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalHapusUser"
                                    data-id="{{ $user->id }}"
                                    data-nama="{{ $user->name }}"
                                    title="Hapus">
                                    <i data-feather="trash-2" style="width:16px;height:16px;"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Belum ada data pengguna.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="modalHapusUser" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body p-4">
                <div class="d-flex align-items-start gap-3">
                    <div class="text-warning fs-3">⚠️</div>
                    <div>
                        <h5 class="mb-2">Konfirmasi Hapus Pengguna</h5>
                        <p class="mb-0 text-muted" id="modalHapusPesan">
                            Apakah Anda yakin ingin menghapus pengguna ini?
                        </p>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <form id="formHapusUser" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Ya, Hapus Data</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
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
    .table-clean thead th:last-child { border-right: none; }
    .table-clean tbody td {
        padding: 14px 16px;
        border-top: 1px solid #eef1f5;
        border-right: 1px solid #eef1f5;
        font-size: 0.9rem;
        color: #334155;
        vertical-align: middle;
    }
    .table-clean tbody td:last-child { border-right: none; }
    .table-clean tbody tr:hover { background-color: #fafbfc; }

    .icon-edit, .icon-delete {
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
</style>

<script src="https://unpkg.com/feather-icons"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modalHapus = document.getElementById('modalHapusUser');
    const pesanEl = document.getElementById('modalHapusPesan');
    const formEl = document.getElementById('formHapusUser');

    modalHapus.addEventListener('show.bs.modal', function (event) {
        const btn = event.relatedTarget;
        const id = btn.getAttribute('data-id');
        const nama = btn.getAttribute('data-nama');

        formEl.action = `{{ url('users') }}/${id}`;
        pesanEl.innerHTML = `Apakah Anda yakin ingin menghapus pengguna <strong>${nama}</strong>?`;
    });

    feather.replace();
});
</script>

@endsection
@extends('layouts.admin') 

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Data Kategori Material Furnitur</h2>
        <a href="{{ route('material.category.create') }}" class="btn btn-primary">+ Tambah Kategori</a>
    </div>
        
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">No</th>
                            <th>Nama Kategori</th>
                            <th>Tipe Material</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Loop data dari controller --}}
                        @forelse ($categories as $index => $cat)
                        <tr>
                            <td class="ps-4">{{ $index + 1 }}</td>
                            <td>{{ $cat->nama_kategori }}</td>
                            <td>{{ $cat->tipe_material }}</td> {{-- Sudah disesuaikan dengan HeidiSQL --}}
                            <td>
                                <a href="{{ route('material.category.edit', $cat->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                <button class="btn btn-danger btn-sm">Hapus</button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                Belum ada data kategori. Silakan klik "+ Tambah Kategori" untuk mengisi data baru!
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
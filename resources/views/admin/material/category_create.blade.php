@extends('layouts.admin') 

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Tambah Kategori Material</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('material.category.store') }}" method="POST">
                    @csrf {{-- Token keamanan wajib Laravel --}}

                    {{-- 1. Input Nama Kategori (Sudah diubah ke nama_kategori huruf kecil) --}}
                    <div class="mb-3">
                        <label for="nama_kategori" class="form-label">Nama Kategori / Jenis Kayu</label>
                        <input type="text" class="form-control @error('nama_kategori') is-invalid @enderror" 
                               id="nama_kategori" name="nama_kategori" placeholder="Contoh: Kayu Ulin" required>
                        @error('nama_kategori')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- 2. Input Tipe Material (Sudah diubah dari jenis_material ke tipe_material sesuai HeidiSQL) --}}
                    <div class="mb-3">
                        <label for="tipe_material" class="form-label">Jenis Material</label>
                        <select class="form-select @error('tipe_material') is-invalid @enderror" id="tipe_material" name="tipe_material" required>
                            <option value="">Pilih Jenis Material</option>
                            <option value="Kayu">Kayu</option>
                        </select>
                        @error('tipe_material')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Sistem otomatis memilih 'Kayu' sebagai default.</small>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('material.category') }}" class="btn btn-secondary">Kembali</a>
                        <button type="submit" class="btn btn-success">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
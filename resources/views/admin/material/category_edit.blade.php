@extends('layouts.admin')
@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Edit Kategori Material</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('material.category.update', $category->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Kelompok Material</label>
                        <input type="text" class="form-control" value="{{ $category->kelompok_material }}" disabled>
                        <small class="text-muted">Kelompok material tidak bisa diubah di sini. Hapus & buat kategori baru jika perlu ganti kelompok.</small>
                    </div>

                    <div class="mb-3">
                        <label for="kategori" class="form-label">Kategori <span class="text-danger">*</span></label>
                        <select class="form-select @error('kategori') is-invalid @enderror" id="kategori" name="kategori" required>
                            <option value="" disabled>-- Pilih Kategori --</option>
                            @foreach($opsiKategori as $k)
                                <option value="{{ $k }}" {{ $category->kategori == $k ? 'selected' : '' }}>{{ $k }}</option>
                            @endforeach
                        </select>
                        @error('kategori')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="nama_Kategori" class="form-label">Sub Kategori <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nama_Kategori') is-invalid @enderror"
                               id="nama_Kategori" name="nama_Kategori"
                               placeholder="Contoh: Kayu Bengkirai"
                               value="{{ $category->nama_Kategori }}" required>
                        @error('nama_Kategori')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    @php
    $satuanBaku = ['M³', 'Lembar', 'Pcs', 'Kg', 'Liter'];
    $isManual = !in_array($category->satuan_dasar, $satuanBaku);
@endphp

<div class="mb-3">
    <label for="satuan_dasar" class="form-label">Satuan Dasar <span class="text-danger">*</span></label>
    <select class="form-select @error('satuan_dasar') is-invalid @enderror" id="satuan_dasar" name="satuan_dasar" required>
        <option value="M³" {{ $category->satuan_dasar == 'M³' ? 'selected' : '' }}>M³ (Kubik)</option>
        <option value="Lembar" {{ $category->satuan_dasar == 'Lembar' ? 'selected' : '' }}>Lembar</option>
        <option value="Pcs" {{ $category->satuan_dasar == 'Pcs' ? 'selected' : '' }}>Pcs</option>
        <option value="Kg" {{ $category->satuan_dasar == 'Kg' ? 'selected' : '' }}>Kg</option>
        <option value="Liter" {{ $category->satuan_dasar == 'Liter' ? 'selected' : '' }}>Liter</option>
        <option value="MANUAL" {{ $isManual ? 'selected' : '' }}>Ketik Manual...</option>
    </select>
    @error('satuan_dasar')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror

    <div id="kotak_satuan_manual" class="mt-2" style="display: {{ $isManual ? 'block' : 'none' }};">
        <label class="text-muted small mb-1">Ketik Satuan Manual:</label>
        <input type="text" name="satuan_kustom_input" id="satuan_kustom_input"
               class="form-control @error('satuan_kustom_input') is-invalid @enderror"
               placeholder="Contoh: Roll, Dus, dll."
               value="{{ $isManual ? $category->satuan_dasar : old('satuan_kustom_input') }}"
               {{ $isManual ? '' : 'disabled' }}>
        @error('satuan_kustom_input')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

                    <hr class="my-4">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Item Material</label>
                        <p class="text-muted small mb-2">Ubah nama item fisik yang terdaftar di kategori ini. Kode material tidak bisa diubah.</p>

                        @forelse($category->items as $item)
                            <div class="input-group mb-2">
                                <span class="input-group-text text-muted" style="min-width: 150px;">
                                    {{ $item->kode_material }}
                                </span>
                                <input type="text"
                                       name="items[{{ $item->id }}][nama_material]"
                                       value="{{ old('items.'.$item->id.'.nama_material', $item->nama_material) }}"
                                       class="form-control @error('items.'.$item->id.'.nama_material') is-invalid @enderror"
                                       required>
                                @error('items.'.$item->id.'.nama_material')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @empty
                            <p class="text-muted fst-italic">Belum ada item material di kategori ini.</p>
                        @endforelse
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
<script>
document.addEventListener('DOMContentLoaded', function () {
    const satuanDasar = document.getElementById('satuan_dasar');
    const kotakManual = document.getElementById('kotak_satuan_manual');
    const inputManual = document.getElementById('satuan_kustom_input');

    satuanDasar.addEventListener('change', function () {
        if (this.value === 'MANUAL') {
            kotakManual.style.display = 'block';
            inputManual.removeAttribute('disabled');
            inputManual.setAttribute('required', 'required');
        } else {
            kotakManual.style.display = 'none';
            inputManual.setAttribute('disabled', 'disabled');
            inputManual.removeAttribute('required');
        }
    });
});
</script>
@endsection
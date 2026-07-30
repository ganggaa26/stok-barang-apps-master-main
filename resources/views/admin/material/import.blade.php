@extends('layouts.admin')
@section('content')
<div class="container-fluid px-4 py-4">
    <h1 class="h3 font-bold mb-4">Import Stok Awal dari Excel</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('material.import.store') }}" method="POST" enctype="multipart/form-data" class="card p-4 shadow-sm">
        @csrf

        <div class="mb-3">
            <label class="form-label fw-semibold">Kelompok Material</label>
            <select name="kelompok_material" id="kelompok_material" class="form-select" required>
                <option value="">-- Pilih --</option>
                <option value="pokok">Material Pokok</option>
                <option value="pembantu">Material Pembantu</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Kategori</label>
            <select name="category_id" id="category_id" class="form-select" required disabled>
                <option value="">-- Pilih Kelompok Dulu --</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Item Material</label>
            <select name="item_id" id="item_id" class="form-select" required disabled>
                <option value="">-- Pilih Kategori Dulu --</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">File Excel (.xlsx)</label>
            <input type="file" name="file_excel" class="form-control" accept=".xlsx,.xls" required>
            <small class="text-muted d-block mt-1">
                Kolom wajib: Tanggal, Jenis Transaksi, Tebal, Lebar, Panjang, Qty Fisik, Kuantitas, Merk/Spesifikasi, Lokasi Gudang, Asal/Proyek, Catatan
            </small>
        </div>

        <button type="submit" class="btn btn-primary">Import Data</button>
    </form>
</div>

<script>
const daftarKategoriPerKelompok = @json($daftarKategoriPerKelompok);
const semuaMaterialPokok = @json($semuaMaterialPokok);
const semuaMaterialPembantu = @json($semuaMaterialPembantu);

document.getElementById('kelompok_material').addEventListener('change', function() {
    const catSelect = document.getElementById('category_id');
    const itemSelect = document.getElementById('item_id');
    catSelect.innerHTML = '<option value="">-- Pilih Kategori --</option>';
    itemSelect.innerHTML = '<option value="">-- Pilih Kategori Dulu --</option>';
    itemSelect.disabled = true;

    if (!this.value) { catSelect.disabled = true; return; }

    const daftar = daftarKategoriPerKelompok[this.value] || [];
    daftar.forEach(c => catSelect.add(new Option(c.nama_Kategori, c.id)));
    catSelect.disabled = false;
});

document.getElementById('category_id').addEventListener('change', function() {
    const itemSelect = document.getElementById('item_id');
    const kelompok = document.getElementById('kelompok_material').value;
    itemSelect.innerHTML = '<option value="">-- Pilih Item --</option>';

    const sumberData = kelompok === 'pokok' ? semuaMaterialPokok : semuaMaterialPembantu;
    const filtered = sumberData.filter(m => m.category_id == this.value);
    filtered.forEach(m => itemSelect.add(new Option(m.nama_material, m.id)));
    itemSelect.disabled = false;
});
</script>
@endsection
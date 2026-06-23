@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb text-sm text-slate-500 bg-transparent p-0 m-0">
                <li class="breadcrumb-item"><a href="#" class="text-slate-400 hover:text-indigo-600 transition">Manajemen Material</a></li>
                <li class="breadcrumb-item active text-slate-700 font-medium" aria-current="page">Tambah Kategori & Item Baru</li>
            </ol>
        </nav>
        <h1 class="h3 font-bold text-slate-800 mt-2">Form Tambah Kategori & Spesifikasi Material</h1>
        <p class="text-sm text-slate-500">Definisikan klasifikasi sub-kategori logistik sekaligus daftarkan spesifikasi item fisik pertamanya.</p>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background: #ffffff; border: 1px solid #f1f5f9 !important;">
        <div class="card-body p-4 p-md-5">

            <form action="{{ route('material.category.store') }}" method="POST">
                @csrf

                <div class="row g-4 mb-4">
                    <div class="col-12 col-md-6">
                        <label for="kelompok_material" class="form-label text-sm font-semibold text-slate-600 mb-2">
                            Kelompok Material<span class="text-danger">*</span>
                        </label>
                        <select name="kelompok_material" id="kelompok_material" class="form-select custom-input" required>
                            <option value="" disabled selected>-- Pilih Kelompok Material --</option>
                            <option value="Material Pokok">Material Pokok</option>
                            <option value="Material Pembantu">Material Pembantu</option>
                        </select>
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="nama_Kategori" class="form-label text-sm font-semibold text-slate-600 mb-2">
                            Nama Sub-Kategori / Item Rumpun <span class="text-danger">*</span>
                        </label>
                        <select name="nama_Kategori" id="nama_Kategori" class="form-select custom-input" required disabled>
                            <option value="" disabled selected>-- Pilih Kelompok Material Terlebih Dahulu --</option>
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="nama_item_fisik" class="form-label text-sm font-semibold text-slate-600 mb-2">
                        Nama Item Barang Fisik <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="nama_item_fisik" id="nama_item_fisik" class="form-control custom-input"
                           placeholder="Contoh: Kayu Jati, Lem Presto, Sekrup Fix" required>
                </div>

                <div class="mb-4">
                    <label for="tipe_kalkulasi" class="form-label text-sm font-semibold text-slate-600 mb-2">
                        Tipe Kalkulasi <span class="text-danger">*</span>
                    </label>
                    <select name="tipe_kalkulasi" id="tipe_kalkulasi" class="form-select custom-input" required>
                        <option value="" disabled selected>-- Pilih Tipe Kalkulasi --</option>
                        <optgroup label="Bahan Pokok">
                            <option value="volume_kayu">Volume Kayu (T x L x P, hasil M³) — Kayu Solid</option>
                            <option value="lembar_board">Lembar Board (Merk + Tebal mm) — MDF, Plywood, HMR</option>
                            <option value="lembar_hpl">Lembar HPL (Merk + Kode Warna) — HPL</option>
                            <option value="luas_veneer">Luas Veneer (L x P, hasil M²) — Veneer</option>
                        </optgroup>
                        <optgroup label="Bahan Pembantu">
                            <option value="satuan_lem">Satuan Bebas (Merk + Kilo/Liter/Pcs) — Lem</option>
                            <option value="satuan_sekrup">Satuan + Ukuran (Merk + Ukuran + Pcs/Kotak) — Sekrup</option>
                            <option value="volume_cairan">Volume Cairan (Merk + Jenis Kimia, Liter) — Cat, Thinner, H2O2</option>
                            <option value="konversi_amplas">Konversi Roll-Meter (Merk + Grit) — Amplas</option>
                        </optgroup>
                    </select>
                    <p class="text-xs text-slate-400 mt-1.5 mb-0">
                        Menentukan bentuk form spesifikasi yang akan muncul otomatis saat input transaksi barang ini.
                    </p>
                </div>

                <hr class="border-slate-100 my-4" style="border-top: 1px solid #f1f5f9;">

                <div class="row g-4 mb-5">
                    <div class="col-12 col-md-6">
                        <label for="satuan_dasar" class="form-label text-sm font-semibold text-slate-600 mb-2">
                            Satuan Dasar Pengukuran <span class="text-danger">*</span>
                        </label>
                        <select name="satuan_dasar" id="satuan_dasar" class="form-select custom-input" required>
                            <option value="" disabled selected>-- Pilih Satuan Pengukuran --</option>
                            <option value="M³">M³ (Kubik)</option>
                            <option value="Lembar">Lembar</option>
                            <option value="Pcs">Pcs</option>
                            <option value="Kg">Kg</option>
                            <option value="Liter">Liter</option>
                        </select>
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-end gap-2 pt-3" style="border-top: 1px solid #f1f5f9;">
                    <a href="{{ route('material.category') }}" class="btn btn-link text-slate-500 text-decoration-none px-4 py-2.5 rounded-3 hover-bg-slate transition" style="font-size: 0.875rem; font-weight: 500;">
                        Batal
                    </a>

                    <button type="submit" class="btn text-white px-4 py-2.5 rounded-3 shadow-sm transition d-flex align-items-center gap-2"
                            style="background-color: #5c5fc8; font-size: 0.875rem; font-weight: 500; border: none;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cursor-fill" viewBox="0 0 16 16">
                          <path d="M14.082 2.182a.5.5 0 0 1 .103.557L8.528 15.467a.5.5 0 0 1-.917-.007L5.57 10.694.803 8.652a.5.5 0 0 1-.006-.916l12.728-5.657a.5.5 0 0 1 .556.103z"/>
                        </svg>
                        <span>Simpan Kategori & Item</span>
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const kelompokSelect = document.getElementById('kelompok_material');
        const namaKategoriSelect = document.getElementById('nama_Kategori');
        const form = kelompokSelect.closest('form');

        const optionsData = {
            "Material Pokok": [
                { value: "Kayu Solid", text: "Kayu Solid (Solid Wood)" },
                { value: "Olahan Kayu", text: "Olahan Kayu (Engineered Wood & Board)" },
                { value: "Pelapis / Laminasi", text: "Pelapis / Laminasi (Laminate & Veneer)" }
            ],
            "Material Pembantu": [
                { value: "Cairan Finishing", text: "Cairan Finishing (Chemicals & Coatings)" },
                { value: "Bahan Pendukung Finishing", text: "Bahan Pendukung Finishing (Finishing Consumables)" },
                { value: "Perekat", text: "Perekat (Adhesives)" },
                { value: "Pengikat", text: "Pengikat (Fasteners)" }
            ]
        };

        kelompokSelect.addEventListener('change', function () {
            const selectedValue = this.value;

            namaKategoriSelect.innerHTML = '<option value="" disabled selected>-- Pilih Sub-Kategori Spesifik --</option>';

            if (optionsData[selectedValue]) {
                namaKategoriSelect.disabled = false;

                optionsData[selectedValue].forEach(function (item) {
                    const option = document.createElement('option');
                    option.value = item.value;
                    option.text = item.text;
                    namaKategoriSelect.appendChild(option);
                });
            } else {
                namaKategoriSelect.disabled = true;
            }
        });

        form.addEventListener('submit', function () {
            namaKategoriSelect.disabled = false;
        });
    });
</script>
@endsection

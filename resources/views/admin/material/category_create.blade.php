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
                        <label for="kategori" class="form-label text-sm font-semibold text-slate-600 mb-2">
                            Kategori<span class="text-danger">*</span>
                        </label>
                        <select name="kategori" id="kategori" class="form-select custom-input" required disabled>
                            <option value="" disabled selected>-- Pilih Kelompok Material Dulu --</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label text-sm font-semibold text-slate-600 mb-2">Nama Sub-Kategori / Item Rumpun <span class="text-danger">*</span></label>
                        <select class="form-select custom-input" id="nama_kategori" name="nama_kategori" required disabled>
                            <option value="" disabled selected>-- Pilih atau Ketik Sub-Kategori Baru --</option>
                            @if(isset($subCategories) && $subCategories->count() > 0)
                                @foreach($subCategories as $sub)
                                    <option value="{{ $sub->nama_Kategori }}">{{ $sub->nama_Kategori }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="nama_item_fisik" class="form-label text-sm font-semibold text-slate-600 mb-2">
                        Nama Item Barang Fisik <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="nama_item_fisik" id="nama_item_fisik" class="form-control custom-input"
                           placeholder="Contoh: Lem Alteco, Lem Johan, Lem Rajawali, Kayu Jati" required autocomplate="off">
                </div>

                <div class="row g-4 mb-4">
                   <div class="col-12 col-md-6">
                    <label class="form-label text-sm font-semibold text-slate-600 mb-2">Tipe Kalkulasi / Rumus Stok <span class="text-danger">*</span></label>
                    <select id="tipe_kalkulasi" name="tipe_kalkulasi" class="form-select custom-input" required>
                        <option value="">-- Pilih Tipe Kalkulasi --</option>
                        <option value="volume_kayu">Volume Kayu (T x L x P, hasil M³)</option>
                        <option value="lembar_board">Lembar Board (Merk + Tebal mm)</option>
                        <option value="lembar_hpl">Lembar HPL (Merk + Kode Warna)</option>
                        <option value="luas_veneer">Luas Veneer (L x P, hasil M²)</option>
                        <option value="volume_cairan">Volume Cairan (Merk + Jenis Kimia, Liter)</option>
                        <option value="konversi_amplas">Konversi Roll-Meter (Merk + Grit) — Amplas</option>
                        <option value="CUSTOM_RUMUS">📐 Custom Rumus Matematika Sendiri</option>
                        </select>
                        
                        <div id="kotak_rumus_custom" class="mt-2 card p-3 shadow-sm border-dashed" style="display: none; background-color: #f8fafc;">
                            <label class="text-xs font-semibold text-slate-500 mb-1">Masukkan Rumus Custom:</label>
                            <input type="text" id="rumus_custom_input" class="form-control" style="font-family: monospace;" placeholder="Contoh: panjang * lebar * tinggi">
                        </div>
                    </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label text-sm font-semibold text-slate-600 mb-2">Satuan Dasar Pengukuran <span class="text-danger">*</span></label>
                        <select name="satuan_dasar" id="satuan_dasar" class="form-select custom-input" required>
                            <option value="" disabled selected>-- Pilih Satuan Pengukuran --</option>
                            <option value="M³">M³ (Kubik)</option>
                            <option value="M²">M² (Luas)</option>
                            <option value="Lembar">Lembar</option>
                            <option value="Pcs">Pcs</option>
                            <option value="Kg">Kg</option>
                            <option value="Liter">Liter</option>
                            <option value="MANUAL">Ketik Manual...</option>
                            </select>
                        
                        <!-- Kotak input kustom dikondisikan default tanpa atribut name dan disabled -->
                        <div id="kotak_satuan_manual" class="mt-2" style="display: none;">
                            <label class="text-xs text-slate-500 mb-1">Ketik Satuan Manual:</label>
                            <input type="text" id="satuan_kustom_input" name="satuan_kustom_input" class="form-control" placeholder="Contoh: Roll, Dus, dll." disabled>
                        </div>
                    </div>

                <div class="d-flex align-items-center justify-content-end gap-2 pt-3" style="border-top: 1px solid #f1f5f9;">
                    <a href="{{ route('material.category') }}" class="btn btn-link text-slate-500 text-decoration-none px-4 py-2.5">Batal</a>
                    <button type="submit" class="btn text-white px-4 py-2.5 rounded-3" style="background-color: #5c5fc8; border: none;">
                        <span>Simpan Kategori & Item</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<style>
    /* Menjaga kursor pengetikan sub-kategori kustom tetap terlihat dan proporsional */
    .ts-wrapper.multi .ts-control > input, 
    .ts-wrapper .ts-control > input {
        display: inline-block !important;
        width: auto !important;
        position: relative !important;
        opacity: 1 !important;
        max-width: 100% !important;
        min-width: 150px !important;
        border: none !important;
        padding: 0 !important;
        margin: 0 !important;
        background: transparent !important;
    }
    .ts-dropdown .create {
        padding: 8px 12px;
        background-color: #f8fafc;
        color: #5c5fc8;
        font-weight: 600;
    }
    .ts-dropdown .active.create {
        background-color: #5c5fc8;
        color: #ffffff;
    }
    /* Animasi smooth saat kotak input custom muncul */
    #kotak_rumus_custom, #kotak_satuan_manual {
        transition: all 0.3s ease-in-out;
    }
</style>

<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const namaKategoriSelect = document.getElementById('nama_kategori');
    if (namaKategoriSelect) namaKategoriSelect.removeAttribute('disabled');

    var tsKelompok = new TomSelect('#kelompok_material', { create: false });
    var tsKategori = new TomSelect('#kategori', {
    create: true,
    createOnBlur: true
});
    var tsCategory = new TomSelect('#nama_kategori', {
        create: true,
        placeholder: "🔍 Pilih atau Ketik Sub-Kategori Baru..."
    });

    // Data dari controller
    const daftarKategoriPerKelompok = {!! json_encode($daftarKategori) !!};
    const allSubCategories = {!! json_encode($subCategories->map(function($s){
        return ['nama' => $s->nama_kategori, 'kategori' => $s->kategori, 'kelompok' => $s->kelompok_material];
    })) !!};

    // 1. Saat Kelompok Material dipilih -> isi opsi Kategori
    tsKelompok.on('change', function(value) {
        tsKategori.clear();
        tsKategori.clearOptions();
        tsCategory.clear();
        tsCategory.clearOptions();

        if (!value) {
            tsKategori.disable();
            tsCategory.disable();
            return;
        }

        const opsiKategori = daftarKategoriPerKelompok[value] || [];
        opsiKategori.forEach(k => tsKategori.addOption({ value: k, text: k }));
        tsKategori.refreshOptions(false);
        tsKategori.enable();

        tsCategory.disable(); // sub-kategori nunggu kategori dipilih
    });

    // 2. Saat Kategori dipilih -> isi opsi Sub-Kategori yang relevan + boleh ketik baru
    tsKategori.on('change', function(value) {
        tsCategory.clear();
        tsCategory.clearOptions();

        if (!value) {
            tsCategory.disable();
            return;
        }

        const kelompokAktif = tsKelompok.getValue();
        allSubCategories
            .filter(item => item.kategori === value && item.kelompok === kelompokAktif)
            .forEach(item => tsCategory.addOption({ value: item.nama, text: item.nama }));

        tsCategory.refreshOptions(false);
        tsCategory.enable();
    });

    // Kondisi awal: Kategori & Sub-Kategori dikunci sampai Kelompok dipilih
    tsKategori.disable();
    tsCategory.disable();

    // --- Logika Rumus Custom & Satuan Manual (TETAP SAMA, tidak berubah) ---
    const tipeKalkulasi = document.getElementById('tipe_kalkulasi');
    const kotakRumusCustom = document.getElementById('kotak_rumus_custom');
    const rumusCustomInput = document.getElementById('rumus_custom_input');

    if (tipeKalkulasi && kotakRumusCustom) {
        tipeKalkulasi.addEventListener('change', function() {
            if (this.value === 'CUSTOM_RUMUS') {
                kotakRumusCustom.style.display = 'block';
                if (rumusCustomInput) rumusCustomInput.setAttribute('required', 'required');
            } else {
                kotakRumusCustom.style.display = 'none';
                if (rumusCustomInput) {
                    rumusCustomInput.removeAttribute('required');
                    rumusCustomInput.value = '';
                }
            }
        });
    }

    const satuanDasar = document.getElementById('satuan_dasar');
    const kotakSatuanManual = document.getElementById('kotak_satuan_manual');
    const satuanKustomInput = document.getElementById('satuan_kustom_input');

    if (satuanDasar && kotakSatuanManual) {
        satuanDasar.addEventListener('change', function() {
            if (this.value === 'MANUAL') {
                kotakSatuanManual.style.display = 'block';
                if (satuanKustomInput) {
                    satuanKustomInput.removeAttribute('disabled');
                    satuanKustomInput.setAttribute('required', 'required');
                }
            } else {
                kotakSatuanManual.style.display = 'none';
                if (satuanKustomInput) {
                    satuanKustomInput.setAttribute('disabled', 'disabled');
                    satuanKustomInput.removeAttribute('required');
                    satuanKustomInput.value = '';
                }
            }
        });
    }
});
</script>
@endsection
@extends('layouts.admin')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>

<div class="w-full mx-auto my-2 text-slate-700">
    
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between border-b border-slate-200 pb-4">
        <div>
            <div class="flex items-center space-x-2 text-xs text-slate-400 font-medium mb-1">
                <span>Manajemen Material</span>
                <span>/</span>
                <span class="text-indigo-600">Material Pokok</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-800">Modul Inventaris: Bahan Pokok Utama</h1>
            <p class="text-sm text-slate-500 mt-0.5">Pengelolaan logistik Kayu Solid, Olahan Kayu, dan Pelapis Lapisan Struktur</p>
        </div>
    </div>

    <div class="space-y-6">
        <form id="formBahanPokok" class="space-y-6">
            
            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Sub-Kategori Bahan Pokok</label>
                    <select id="subKategori" class="w-full bg-white border border-slate-300 rounded-lg p-2.5 text-sm font-medium text-slate-700 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none transition-all" onchange="updateItemDropdown()">
                        <option value="">-- Pilih Sub-Kategori --</option>
                        <option value="kayu_solid">Kayu Solid (Solid Wood)</option>
                        <option value="olahan_kayu">Olahan Kayu (Engineered Wood & Board)</option>
                        <option value="pelapis">Pelapis / Laminasi (Laminate & Veneer)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Item Barang</label>
                    <select id="itemBarang" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-sm font-medium text-slate-400 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all" onchange="renderSpesifikasiForm()" disabled>
                        <option value="">-- Pilih Item --</option>
                    </select>
                </div>
            </div>

            <div id="wrapperSpesifikasi" class="hidden bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
                <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <span>📐</span> Parameter Spesifikasi Fisik
                </h3>
                <div id="areaFormDinamis" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4"></div>
            </div>

            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm space-y-5">
                <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-2">
                    <span>🚚</span> Status Logistik & Tujuan Proyek
                </h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Jenis Transaksi</label>
                        <select id="jenisTransaksi" class="w-full bg-white border border-slate-300 rounded-lg p-2 text-sm text-slate-700 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none" onchange="aturFormLogistik()">
                            <option value="Stok Awal">Stok Awal Gudang</option>
                            <option value="Barang Masuk">Barang Masuk (+)</option>
                            <option value="Barang Keluar">Barang Keluar (-)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Tanggal</label>
                        <input type="date" id="tglTransaksi" class="w-full bg-white border border-slate-300 rounded-lg p-2 text-sm text-slate-700 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    </div>
                    <div id="boxAsal">
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Asal / Supplier</label>
                        <input type="text" id="asalBarang" placeholder="Contoh: PT. Jati Permai" class="w-full bg-white border border-slate-300 rounded-lg p-2 text-sm text-slate-700 shadow-sm placeholder:text-slate-400 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    </div>
                    <div id="boxProyek" class="hidden">
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Nama Proyek / Produk Jadi</label>
                        <input type="text" id="namaProyek" placeholder="Contoh: Meja Resepsionis" class="w-full bg-white border border-slate-300 rounded-lg p-2 text-sm text-slate-700 shadow-sm placeholder:text-slate-400 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="button" onclick="simpanKeLogSistem()" class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm px-6 py-2.5 rounded-lg shadow-sm hover:shadow transition-all duration-150 flex items-center justify-center gap-2">
                    <span>💾</span> Amankan Data Stok Pokok
                </button>
            </div>
        </form>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 space-y-4">
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider flex items-center space-x-2">
                <span>📊</span> <span>Jurnal Riwayat Transaksi Material Pokok</span>
            </h3>
            <div class="overflow-x-auto rounded-lg border border-slate-200 bg-white">
                <table class="w-full text-sm text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider border-b border-slate-200">
                            <th class="p-3.5 font-semibold">Tanggal</th>
                            <th class="p-3.5 font-semibold">Item Material</th>
                            <th class="p-3.5 font-semibold text-center">Status</th>
                            <th class="p-3.5 font-semibold">Karakteristik & Lokasi Gudang</th>
                            <th class="p-3.5 font-semibold text-right">Kuantitas Log</th>
                            <th class="p-3.5 font-semibold">Pelacakan Logistik</th>
                            <th class="p-3.5 font-semibold text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="badanTabelLog" class="divide-y divide-slate-100">
                        <tr id="barisKosong">
                            <td colspan="7" class="p-8 text-center text-slate-400 italic bg-slate-50/50">
                                Belum ada mutasi material pokok yang tercatat.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById('tglTransaksi').valueAsDate = new Date();
    });

    const dataBahanPokok = {
        "kayu_solid": ["Kayu Jati", "Kayu Merbau", "Kayu Yellow Balau (Bangkirai)", "Kayu Ulin", "Kayu Sungkai"],
        "olahan_kayu": ["Plywood / Triplek", "MDF", "HMR"],
        "pelapis": ["HPL (High-Pressure Laminate)", "Veneer (Vinner)"]
    };

    function updateItemDropdown() {
        const sub = document.getElementById('subKategori').value;
        const itemSelect = document.getElementById('itemBarang');
        
        itemSelect.innerHTML = '<option value="">-- Pilih Item --</option>';
        document.getElementById('wrapperSpesifikasi').classList.add('hidden');

        if (!sub) {
            itemSelect.disabled = true;
            itemSelect.className = "w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-sm text-slate-400 shadow-sm cursor-not-allowed";
            return;
        }

        itemSelect.disabled = false;
        itemSelect.className = "w-full bg-white border border-slate-300 rounded-lg p-2.5 text-sm font-medium text-slate-700 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all";
        
        dataBahanPokok[sub].forEach(item => {
            itemSelect.innerHTML += `<option value="${item}">${item}</option>`;
        });
    }

    function aturFormLogistik() {
        const jenis = document.getElementById('jenisTransaksi').value;
        const boxAsal = document.getElementById('boxAsal');
        const boxProyek = document.getElementById('boxProyek');

        if (jenis === 'Barang Keluar') {
            boxAsal.classList.add('hidden');
            boxProyek.classList.remove('hidden');
        } else {
            boxAsal.classList.remove('hidden');
            boxProyek.classList.add('hidden');
        }
    }

    function renderSpesifikasiForm() {
        const sub = document.getElementById('subKategori').value;
        const item = document.getElementById('itemBarang').value;
        const wrapper = document.getElementById('wrapperSpesifikasi');
        const areaForm = document.getElementById('areaFormDinamis');

        if (!item) { wrapper.classList.add('hidden'); return; }
        wrapper.classList.remove('hidden');
        areaForm.innerHTML = "";

        const inputClass = "w-full border border-slate-300 p-2 text-sm rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all";
        const labelClass = "block text-xs font-medium text-slate-600 mb-1";

        if (sub === 'kayu_solid') {
            areaForm.innerHTML = `
                <div><label class="${labelClass}">Tebal (cm)</label><input type="number" id="k_tebal" value="5" class="${inputClass}" oninput="hitungKubikasi()"></div>
                <div><label class="${labelClass}">Lebar (cm)</label><input type="number" id="k_lebar" value="20" class="${inputClass}" oninput="hitungKubikasi()"></div>
                <div><label class="${labelClass}">Panjang (cm)</label><input type="number" id="k_panjang" value="200" class="${inputClass}" oninput="hitungKubikasi()"></div>
                <div><label class="${labelClass}">Kualitas / Grade Kayu</label><select id="k_grade" class="${inputClass}"><option value="A">Grade A (Bagus)</option><option value="B">Grade B (Biasa)</option><option value="C">Grade C (Kurang Bagus)</option></select></div>
                <div><label class="${labelClass}">Lokasi Gudang</label><input type="text" id="k_gudang" value="Gudang A Utama" placeholder="Nama Gudang/Rak" class="${inputClass}"></div>
                <div><label class="${labelClass}">Jumlah Batang / Pcs</label><input type="number" id="k_qty" value="1" class="${inputClass}" oninput="hitungKubikasi()"></div>
                <div class="md:col-span-3 bg-slate-50 text-slate-700 p-3 rounded-lg border border-slate-200 font-mono font-semibold text-center text-xs mt-2" id="calcPreviewM3">Volume Hasil Konversi: 0.0200 M³</div>
            `;
            hitungKubikasi();
        }
        else if (sub === 'olahan_kayu') {
            areaForm.innerHTML = `
                <div><label class="${labelClass}">Merek / Jenis Board</label><input type="text" id="b_merk" placeholder="Contoh: Mercy / Meranti" class="${inputClass}" required></div>
                <div><label class="${labelClass}">Tebal Board (MM)</label><input type="number" id="b_tebal" placeholder="Contoh: 18" class="${inputClass}" required></div>
                <div><label class="${labelClass}">Jumlah (Lembar)</label><input type="number" id="b_qty" value="1" class="${inputClass}" required></div>
            `;
        }
        else if (item.includes('HPL')) {
            areaForm.innerHTML = `
                <div><label class="${labelClass}">Merek HPL</label><select id="h_merk" class="${inputClass}"><option>Taco</option><option>Omega</option><option>Lamitak</option></select></div>
                <div><label class="${labelClass}">Kode Warna / Motif</label><input type="text" id="h_kode" placeholder="Contoh: TH 001 G" class="${inputClass}" required></div>
                <div><label class="${labelClass}">Jumlah (Lembar)</label><input type="number" id="h_qty" value="1" class="${inputClass}" required></div>
            `;
        }
        else if (item.includes('Veneer')) {
            areaForm.innerHTML = `
                <div><label class="${labelClass}">Jenis Kayu Veneer</label><input type="text" id="v_jenis" placeholder="Sungkai, Meranti" class="${inputClass}" required></div>
                <div><label class="${labelClass}">Nomor Bendel (Inti)</label><input type="text" id="v_bendel" placeholder="Wajib dari Supplier" class="${inputClass}" required></div>
                <div><label class="${labelClass}">Tebal Lembaran (mm)</label><input type="number" step="0.1" id="v_tebal" value="0.6" class="${inputClass}" required></div>
                <div><label class="${labelClass}">Lebar Lembaran (cm)</label><input type="number" id="v_lebar" value="20" class="${inputClass}" oninput="hitungLuasVeneer()"></div>
                <div><label class="${labelClass}">Panjang Lembaran (cm)</label><input type="number" id="v_panjang" value="120" class="${inputClass}" oninput="hitungLuasVeneer()"></div>
                <div><label class="${labelClass}">Jumlah (Lembar)</label><input type="number" id="v_qty" value="1" class="${inputClass}" oninput="hitungLuasVeneer()"></div>
                <div class="md:col-span-3 bg-slate-50 text-slate-700 p-3 rounded-lg border border-slate-200 font-mono font-semibold text-center text-xs mt-2" id="calcPreviewM2">Luas Hasil Konversi: 0.24 m²</div>
            `;
            hitungLuasVeneer();
        }
    }

    function hitungKubikasi() {
        const t = parseFloat(document.getElementById('k_tebal').value) || 0;
        const l = parseFloat(document.getElementById('k_lebar').value) || 0;
        const p = parseFloat(document.getElementById('k_panjang').value) || 0;
        const b = parseFloat(document.getElementById('k_qty').value) || 0;
        const totalM3 = (t * l * p / 1000000) * b;
        const preview = document.getElementById('calcPreviewM3');
        if(preview) preview.innerText = `Volume Hasil Konversi: ${totalM3.toFixed(4)} M³ (Berdasarkan total ${b} Batang)`;
        return `${totalM3.toFixed(4)} M³`;
    }

    function hitungLuasVeneer() {
        const l = parseFloat(document.getElementById('v_lebar').value) || 0;
        const p = parseFloat(document.getElementById('v_panjang').value) || 0;
        const lembar = parseFloat(document.getElementById('v_qty').value) || 0;
        const totalM2 = (l * p / 10000) * lembar;
        const preview = document.getElementById('calcPreviewM2');
        if(preview) preview.innerText = `Luas Hasil Konversi: ${totalM2.toFixed(2)} m² (Berdasarkan total ${lembar} Lembar)`;
        return `${totalM2.toFixed(2)} m²`;
    }

    function simpanKeLogSistem() {
        const item = document.getElementById('itemBarang').value;
        if (!item) { alert('Silakan pilih material terlebih dahulu!'); return; }

        const sub = document.getElementById('subKategori').value;
        const tgl = document.getElementById('tglTransaksi').value;
        const jenis = document.getElementById('jenisTransaksi').value;
        const emptyRow = document.getElementById('barisKosong');

        let spekTeknis = "";
        let kuantitasFinal = "";
        let pelacakanLogistik = "";

        // Objek untuk menyimpan metadata asli (digunakan saat trigger fitur Edit)
        let metaData = { sub: sub, item: item, jenis: jenis, tgl: tgl };

        if (jenis === 'Barang Keluar') {
            const proyek = document.getElementById('namaProyek').value || "Proyek Tidak Terdata";
            pelacakanLogistik = `<span class="text-rose-600 font-medium">🛑 Keluar ke:</span> ${proyek}`;
            metaData.tujuan = proyek;
        } else {
            const asal = document.getElementById('asalBarang').value || "Stok Gudang Awal";
            pelacakanLogistik = `<span class="text-emerald-600 font-medium">📦 Masuk dari:</span> ${asal}`;
            metaData.asal = asal;
        }

        if (sub === 'kayu_solid') {
            const grade = document.getElementById('k_grade').value;
            const gdg = document.getElementById('k_gudang').value || "Gudang Utama";
            const qty = document.getElementById('k_qty').value;
            kuantitasFinal = hitungKubikasi();
            spekTeknis = `Grade: ${grade} | Lokasi: ${gdg} (${qty} Pcs)`;
            metaData.spek = { tebal: document.getElementById('k_tebal').value, lebar: document.getElementById('k_lebar').value, panjang: document.getElementById('k_panjang').value, grade: grade, gudang: gdg, qty: qty };
        } 
        else if (sub === 'olahan_kayu') {
            const merk = document.getElementById('b_merk').value || "Generic";
            const tebal = document.getElementById('b_tebal').value || "0";
            const qty = document.getElementById('b_qty').value;
            kuantitasFinal = `${qty} Lbr`;
            spekTeknis = `${merk} (${tebal}mm)`;
            metaData.spek = { merk: merk, tebal: tebal, qty: qty };
        }
        else if (item.includes('HPL')) {
            const merk = document.getElementById('h_merk').value;
            const kode = document.getElementById('h_kode').value || "-";
            const qty = document.getElementById('h_qty').value;
            kuantitasFinal = `${qty} Lbr`;
            spekTeknis = `Merk: ${merk} | Kode: ${kode}`;
            metaData.spek = { merk: merk, kode: kode, qty: qty };
        }
        else if (item.includes('Veneer')) {
            const bndl = document.getElementById('v_bendel').value;
            if (!bndl) { alert("Data Nomor Bendel Veneer Wajib Diisi!"); return; }
            const jenisV = document.getElementById('v_jenis').value;
            const tebal = document.getElementById('v_tebal').value;
            const lebar = document.getElementById('v_lebar').value;
            const panjang = document.getElementById('v_panjang').value;
            const qty = document.getElementById('v_qty').value;
            kuantitasFinal = hitungLuasVeneer();
            spekTeknis = `No. Bendel: ${bndl} | ${jenisV} (${qty} Lbr)`;
            metaData.spek = { bndl: bndl, jenisV: jenisV, tebal: tebal, lebar: lebar, panjang: panjang, qty: qty };
        }

        if (emptyRow) emptyRow.remove();

        const tbody = document.getElementById('badanTabelLog');
        const row = document.createElement('tr');
        row.className = "hover:bg-slate-50 text-slate-700 border-b border-slate-100 transition-colors text-sm baris-log-data";
        
        // Simpan json string data asli ke elemen tr agar gampang dibaca saat fungsi edit dijalankan
        row.setAttribute('data-payload', JSON.stringify(metaData));

        let labelWarna = "bg-slate-100 text-slate-700";
        if (jenis === 'Barang Masuk') labelWarna = "bg-emerald-50 text-emerald-600 font-semibold";
        if (jenis === 'Barang Keluar') labelWarna = "bg-rose-50 text-rose-600 font-semibold";

        row.innerHTML = `
            <td class="p-3.5 text-xs text-slate-400 whitespace-nowrap">${tgl}</td>
            <td class="p-3.5 font-medium text-slate-800">${item}</td>
            <td class="p-3.5 text-center whitespace-nowrap"><span class="px-2.5 py-0.5 text-xs rounded-full ${labelWarna}">${jenis}</span></td>
            <td class="p-3.5 text-xs text-slate-500">${spekTeknis}</td>
            <td class="p-3.5 text-right font-mono font-bold text-slate-800 whitespace-nowrap">${kuantitasFinal}</td>
            <td class="p-3.5 text-xs text-slate-500">${pelacakanLogistik}</td>
            <td class="p-3.5 text-center whitespace-nowrap">
                <div class="flex items-center justify-center space-x-1.5">
                    <button type="button" onclick="editBarisLog(this)" class="bg-amber-50 text-amber-600 hover:bg-amber-100 p-1.5 rounded transition-colors text-xs font-medium flex items-center gap-0.5 shadow-sm">
                        ✏️ <span>Edit</span>
                    </button>
                    <button type="button" onclick="hapusBarisLog(this)" class="bg-rose-50 text-rose-600 hover:bg-rose-100 p-1.5 rounded transition-colors text-xs font-medium flex items-center gap-0.5 shadow-sm">
                        🗑️ <span>Hapus</span>
                    </button>
                </div>
            </td>
        `;

        tbody.insertBefore(row, tbody.firstChild);

        // Reset Form Setelah Disimpan
        resetFormSaja();
    }

    function resetFormSaja() {
        document.getElementById('formBahanPokok').reset();
        document.getElementById('itemBarang').disabled = true;
        document.getElementById('itemBarang').className = "w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-sm text-slate-400 shadow-sm cursor-not-allowed";
        document.getElementById('wrapperSpesifikasi').classList.add('hidden');
        document.getElementById('tglTransaksi').valueAsDate = new Date();
        aturFormLogistik();
    }

    function hapusBarisLog(btn) {
        if (confirm("Apakah Anda yakin ingin menghapus data riwayat transaksi ini?")) {
            const row = btn.closest('tr');
            const tbody = document.getElementById('badanTabelLog');
            row.remove();

            // Cek jika tabel kosong kembali, munculkan lagi baris default pesan kosong
            if (tbody.querySelectorAll('.baris-log-data').length === 0) {
                tbody.innerHTML = `
                    <tr id="barisKosong">
                        <td colspan="7" class="p-8 text-center text-slate-400 italic bg-slate-50/50">
                            Belum ada mutasi material pokok yang tercatat.
                        </td>
                    </tr>
                `;
            }
        }
    }

    function editBarisLog(btn) {
        const row = btn.closest('tr');
        const data = JSON.parse(row.getAttribute('data-payload'));

        // Kembalikan nilai drop-down sub kategori dan pemicu update item
        document.getElementById('subKategori').value = data.sub;
        updateItemDropdown();

        // Kembalikan nilai item barang dan buat form spesifikasinya render kembali
        document.getElementById('itemBarang').value = data.item;
        renderSpesifikasiForm();

        // Kembalikan form logistik & tanggal
        document.getElementById('jenisTransaksi').value = data.jenis;
        document.getElementById('tglTransaksi').value = data.tgl;
        aturFormLogistik();

        if (data.jenis === 'Barang Keluar') {
            document.getElementById('namaProyek').value = data.tujuan;
        } else {
            document.getElementById('asalBarang').value = data.asal;
        }

        // Kembalikan input spesifikasi dinamis sesuai tipenya masing-masing
        if (data.sub === 'kayu_solid') {
            document.getElementById('k_tebal').value = data.spek.tebal;
            document.getElementById('k_lebar').value = data.spek.lebar;
            document.getElementById('k_panjang').value = data.spek.panjang;
            document.getElementById('k_grade').value = data.spek.grade;
            document.getElementById('k_gudang').value = data.spek.gudang;
            document.getElementById('k_qty').value = data.spek.qty;
            hitungKubikasi();
        } 
        else if (data.sub === 'olahan_kayu') {
            document.getElementById('b_merk').value = data.spek.merk;
            document.getElementById('b_tebal').value = data.spek.tebal;
            document.getElementById('b_qty').value = data.spek.qty;
        }
        else if (data.item.includes('HPL')) {
            document.getElementById('h_merk').value = data.spek.merk;
            document.getElementById('h_kode').value = data.spek.kode;
            document.getElementById('h_qty').value = data.spek.qty;
        }
        else if (data.item.includes('Veneer')) {
            document.getElementById('v_bendel').value = data.spek.bndl;
            document.getElementById('v_jenis').value = data.spek.jenisV;
            document.getElementById('v_tebal').value = data.spek.tebal;
            document.getElementById('v_lebar').value = data.spek.lebar;
            document.getElementById('v_panjang').value = data.spek.panjang;
            document.getElementById('v_qty').value = data.spek.qty;
            hitungLuasVeneer();
        }

        // Hapus baris lama dari tabel, karena user akan menyimpan versi barunya kembali
        row.remove();
        
        // Cek jika kosong pasca diangkat datanya ke form atas
        const tbody = document.getElementById('badanTabelLog');
        if (tbody.querySelectorAll('.baris-log-data').length === 0) {
            tbody.innerHTML = `
                <tr id="barisKosong">
                    <td colspan="7" class="p-8 text-center text-slate-400 italic bg-slate-50/50">
                        Belum ada mutasi material pokok yang tercatat.
                    </td>
                </tr>
            `;
        }

        // Scroll halus otomatis ke form agar user tahu data sudah dimuat ke form atas
        document.getElementById('formBahanPokok').scrollIntoView({ behavior: 'smooth' });
    }
</script>
@endsection
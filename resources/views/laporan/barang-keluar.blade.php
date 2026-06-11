<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Barang Keluar - Furniture Inventory</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 font-sans p-4 md:p-8">

    <div class="max-w-5xl mx-auto bg-white shadow-xl rounded-xl overflow-hidden border border-slate-200">
        <div class="bg-gradient-to-r from-rose-800 to-rose-950 p-6 text-white">
            <div class="flex items-center space-x-3">
                <span class="text-2xl">📋</span>
                <div>
                    <h1 class="text-xl font-bold tracking-tight">LAPORAN BARANG KELUAR</h1>
                    <p class="text-xs text-rose-200/80">Log Transaksi Pengeluaran Bahan Baku & Bahan Pembantu Ke Proyek</p>
                </div>
            </div>
        </div>

        <div class="p-6">
            <div class="overflow-x-auto rounded-lg border border-slate-200 shadow-sm">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-800 text-slate-100 text-xs uppercase">
                        <tr>
                            <th class="p-3 border">Tanggal</th>
                            <th class="p-3 border">Nama Material</th>
                            <th class="p-3 border text-center">Status</th>
                            <th class="p-3 border">Detail Spesifikasi</th>
                            <th class="p-3 border text-right">Volume Keluar</th>
                            <th class="p-3 border">Alokasi Proyek</th>
                        </tr>
                    </thead>
                    <tbody id="tabelBarangKeluar" class="bg-white divide-y divide-slate-200">
                        </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function muatLaporanKeluar() {
            const tbody = document.getElementById('tabelBarangKeluar');
            // Mengambil database mutasi dari localStorage
            const databaseMutasi = JSON.parse(localStorage.getItem('database_mutasi_inventory')) || [];
            
            // Filter hanya untuk "Barang Keluar"
            const dataKeluar = databaseMutasi.filter(trx => trx.jenis === "Barang Keluar");

            if (dataKeluar.length === 0) {
                tbody.innerHTML = `<tr><td colspan="6" class="p-6 text-center text-slate-400 italic">Belum ada data barang keluar yang tercatat.</td></tr>`;
                return;
            }

            tbody.innerHTML = "";
            dataKeluar.forEach(trx => {
                const row = document.createElement('tr');
                row.className = "hover:bg-slate-50 text-slate-700 border-b transition";
                row.innerHTML = `
                    <td class="p-3 border text-xs">${trx.tanggal}</td>
                    <td class="p-3 border font-bold text-slate-900">${trx.item}</td>
                    <td class="p-3 border text-center">
                        <span class="px-2 py-0.5 text-[11px] font-bold rounded bg-rose-600 text-white">${trx.jenis}</span>
                    </td>
                    <td class="p-3 border text-xs text-slate-500">${trx.detail}</td>
                    <td class="p-3 border text-right font-mono font-bold text-rose-700">-${trx.kuantitasTeks}</td>
                    <td class="p-3 border text-xs font-semibold">${trx.keterangan}</td>
                `;
                tbody.appendChild(row);
            });
        }

        // Jalankan fungsi saat halaman diakses
        window.onload = muatLaporanKeluar;
    </script>
</body>
</html>
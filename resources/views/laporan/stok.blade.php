@extends('layouts.admin') 
@section('content')

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PKSD-Inventory | Jurnal & Rekapitulasi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-[#f8fafc] text-[#1e293b] min-h-screen">

    <div class="flex">
        <aside class="w-64 bg-[#1e2229] text-gray-400 min-h-screen p-4 flex flex-col justify-between">
            <div>
               
                
            
                    <div class="pt-4 text-xs font-semibold text-gray-500 uppercase tracking-wider px-3">
                        Manajemen Material
                    </div>
                    <a href="#" class="flex items-center justify-between px-3 py-2.5 rounded-md bg-[#262c36] text-white transition mt-1">
                        <div class="flex items-center space-x-3">
                            <span>📦</span> <span>Bahan Baku</span>
                        </div>
                        <span class="text-xs">▼</span>
                    </a>

                    <div class="pt-4 text-xs font-semibold text-gray-500 uppercase tracking-wider px-3">
                        Data Master
                    </div>
                    <a href="#" class="flex items-center space-x-3 px-3 py-2.5 rounded-md hover:bg-[#262c36] hover:text-white transition">
                        <span>🏷️</span> <span>Kategori Material</span>
                    </a>
                    <a href="#" class="flex items-center space-x-3 px-3 py-2.5 rounded-md hover:bg-[#262c36] hover:text-white transition">
                        <span>🤝</span> <span>Supplier</span>
                    </a>

                    <div class="pt-4 text-xs font-semibold text-gray-500 uppercase tracking-wider px-3">
                        Aktivitas Gudang
                    </div>
                    <a href="#" class="flex items-center space-x-3 px-3 py-2.5 rounded-md hover:bg-[#262c36] hover:text-white transition">
                        <span>📥</span> <span>Barang Masuk</span>
                    </a>
                    <a href="#" class="flex items-center space-x-3 px-3 py-2.5 rounded-md hover:bg-[#262c36] hover:text-white transition">
                        <span>📤</span> <span>Barang Keluar</span>
                    </a>

                    <div class="pt-4 text-xs font-semibold text-gray-500 uppercase tracking-wider px-3">
                        Pelaporan
                    </div>
                    <a href="#" class="flex items-center space-x-3 px-3 py-2.5 rounded-md bg-[#262c36] text-white font-medium">
                        <span>📋</span> <span>Laporan Stok</span>
                    </a>
                    <a href="#" class="flex items-center space-x-3 px-3 py-2.5 rounded-md hover:bg-[#262c36] hover:text-white transition">
                        <span>📉</span> <span>Laporan Barang Masuk</span>
                    </a>
                </nav>
            </div>
            
            <div class="text-xs text-gray-500 text-center py-2 border-t border-gray-700">
                v1.0.0 &copy; 2026 PT Pelangi Kreasi Solusi
            </div>
        </aside>

        <main class="flex-1 p-8">
            
        

            <div class="flex justify-between items-start mb-6">
                <div>
                    <nav class="text-xs text-gray-400 space-x-2 mb-1">
                        <a href="#" class="hover:underline">Pelaporan</a>
                        <span>/</span>
                        <a href="#" class="text-indigo-600 font-medium">Laporan Stok Aktual</a>
                    </nav>
                    <h1 class="text-2xl font-bold text-slate-900">Jurnal &amp; Rekapitulasi Stok Material</h1>
                    <p class="text-sm text-slate-500 mt-1">Monitoring real-time untuk saldo fisik Material Pokok dan Material Pembantu saat ini.</p>
                </div>
                
                <div class="flex space-x-3">
                    <select class="bg-white border border-slate-200 text-sm rounded-lg px-4 py-2.5 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option>Semua Jenis Bahan</option>
                        <option>Material Pokok</option>
                        <option>Bahan Pembantu</option>
                    </select>
                    <button class="bg-[#1e2229] hover:bg-slate-800 text-white text-sm px-5 py-2.5 rounded-lg font-medium shadow-sm flex items-center space-x-2 transition">
                        <span>🖨️</span> <span>Cetak Laporan</span>
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white border border-slate-200/80 rounded-xl p-5 flex justify-between items-center shadow-sm">
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Item Terdaftar</p>
                        <h3 class="text-2xl font-bold text-slate-800 mt-1">4 Item</h3>
                    </div>
                    <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center text-xl text-blue-500">📋</div>
                </div>
                <div class="bg-white border border-slate-200/80 rounded-xl p-5 flex justify-between items-center shadow-sm">
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Mutasi Material Pokok</p>
                        <h3 class="text-2xl font-bold text-indigo-600 mt-1">2 Log</h3>
                    </div>
                    <div class="w-12 h-12 bg-indigo-50 rounded-lg flex items-center justify-center text-xl text-indigo-500">🪵</div>
                </div>
                <div class="bg-white border border-slate-200/80 rounded-xl p-5 flex justify-between items-center shadow-sm">
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Mutasi Bahan Pembantu</p>
                        <h3 class="text-2xl font-bold text-amber-600 mt-1">2 Log</h3>
                    </div>
                    <div class="w-12 h-12 bg-amber-50 rounded-lg flex items-center justify-center text-xl text-amber-500">🧪</div>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-[#0f172a] text-white text-xs font-semibold uppercase tracking-wider">
                            <th class="py-4 px-6 text-center w-12">No</th>
                            <th class="py-4 px-6">Kelompok Modul</th>
                            <th class="py-4 px-6">Nama Item Material</th>
                            <th class="py-4 px-6">Spesifikasi Teknis / Lokasi</th>
                            <th class="py-4 px-6 text-right">Stok Akhir Aktual</th>
                            <th class="py-4 px-6 text-center">Asal / Supplier</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-4 px-6 text-center font-medium text-slate-400">1</td>
                            <td class="py-4 px-6">
                                <span class="bg-indigo-50 text-indigo-700 text-xs font-medium px-2.5 py-1 rounded-md">Material Pokok</span>
                            </td>
                            <td class="py-4 px-6 font-semibold text-slate-900">Kayu Jati</td>
                            <td class="py-4 px-6 text-xs text-slate-500">Grade: A | Lokasi: Gudang A Utama (10 Pcs)</td>
                            <td class="py-4 px-6 text-right font-bold text-emerald-600">+ 10.2000 M³</td>
                            <td class="py-4 px-6 text-center text-xs text-slate-500">PT. Jati Permai</td>
                        </tr>
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-4 px-6 text-center font-medium text-slate-400">2</td>
                            <td class="py-4 px-6">
                                <span class="bg-indigo-50 text-indigo-700 text-xs font-medium px-2.5 py-1 rounded-md">Material Pokok</span>
                            </td>
                            <td class="py-4 px-6 font-semibold text-slate-900">Kayu Mahoni</td>
                            <td class="py-4 px-6 text-xs text-slate-500">Grade: B | Lokasi: Gudang A Samping</td>
                            <td class="py-4 px-6 text-right font-bold text-emerald-600">+ 2.2500 M³</td>
                            <td class="py-4 px-6 text-center text-xs text-slate-500">CV. Rimba Raya</td>
                        </tr>
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-4 px-6 text-center font-medium text-slate-400">3</td>
                            <td class="py-4 px-6">
                                <span class="bg-amber-50 text-amber-800 text-xs font-medium px-2.5 py-1 rounded-md">Bahan Pembantu</span>
                            </td>
                            <td class="py-4 px-6 font-semibold text-slate-900">Lem Putih PVAc</td>
                            <td class="py-4 px-6 text-xs text-slate-500">Merk: Crona (1 Kg)</td>
                            <td class="py-4 px-6 text-right font-bold text-emerald-600">+ 65 Pcs</td>
                            <td class="py-4 px-6 text-center text-xs text-slate-500">Toko Kimia Utama</td>
                        </tr>
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-4 px-6 text-center font-medium text-slate-400">4</td>
                            <td class="py-4 px-6">
                                <span class="bg-amber-50 text-amber-800 text-xs font-medium px-2.5 py-1 rounded-md">Bahan Pembantu</span>
                            </td>
                            <td class="py-4 px-6 font-semibold text-slate-900">Paku Tembak F30</td>
                            <td class="py-4 px-6 text-xs text-slate-500">Box isi 5000 pcs | Lokasi: Rak Aksesoris B3</td>
                            <td class="py-4 px-6 text-right font-bold text-rose-600">+ 20 Pcs</td>
                            <td class="py-4 px-6 text-center text-xs text-slate-500">PT. Teknik Makmur</td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </main>
    </div>

</body>
</html>
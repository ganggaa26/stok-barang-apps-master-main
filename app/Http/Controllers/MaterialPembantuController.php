<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasterMaterialPembantu; 
use App\Models\Category;
use App\Models\MutasiMaterialPembantu; 
use Illuminate\Support\Facades\DB;

class MaterialPembantuController extends Controller
{
    public function index()
    {
        // 1. Ambil kategori khusus kelompok Material Pembantu
        $categories = Category::where('kelompok_material', 'Material Pembantu')->get();

        // 2. Ambil data dari tabel master_material_pembantus
        $materials = MasterMaterialPembantu::all();

        // 3. Ambil data riwayat transaksi langsung dari tabel barunya secara utuh tanpa whereIn yang berat
        $mutasiks = MutasiMaterialPembantu::with('masterMaterialPembantu')
            ->latest()
            ->get();

        return view('admin.material.pembantu', compact('categories', 'materials', 'mutasiks'));
    }

    public function store(Request $request)
    { 
        $request->validate([
            'material_pembantu_id' => 'required|exists:master_material_pembantus,id',
            'jenis_transaksi'      => 'required|string',
            'tanggal'              => 'required|date',
            'kuantitas'            => 'required|numeric',
            
            // Kolom spesifik pembantu yang baru
            'spesifikasi'          => 'nullable|string|max:255', 
            'merk'                 => 'nullable|string|max:255',
            'jenis_kimia'          => 'nullable|string|max:255',
            'grit'                 => 'nullable|string|max:255',
            
            'satuan_input'         => 'nullable|string|max:255',
            'asal_atau_proyek'     => 'nullable|string|max:255',
        ]);

        $spesifikasiMurni = $request->spesifikasi ?? '-';

        DB::transaction(function () use ($request, $spesifikasiMurni) {
            $material = MasterMaterialPembantu::findOrFail($request->material_pembantu_id);
            
            // PERBAIKAN: Disimpan langsung ke tabel mutasi_material_pembantus
            MutasiMaterialPembantu::create([
                'material_pembantu_id' => $material->id,
                'jenis_transaksi'      => $request->jenis_transaksi,
                'kuantitas'            => $request->kuantitas,
                'tanggal'              => $request->tanggal,

                // Input data murni spesifik pembantu
                'spesifikasi'          => $spesifikasiMurni,
                'merk'                 => $request->merk ?? '-',
                'jenis_kimia'          => $request->jenis_kimia,
                'grit'                 => $request->grit,
                
                'satuan_input'         => $request->satuan_input,
                'asal_atau_proyek'     => $request->asal_atau_proyek,
                'keterangan'           => $spesifikasiMurni,
            ]);

            $this->terapkanStokMaterial($material, $request->jenis_transaksi, $request->kuantitas);
    }); 

        return redirect()->back()->with('success', 'Data transaksi stok pembantu berhasil diamankan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'material_pembantu_id' => 'required|exists:master_material_pembantus,id',
            'jenis_transaksi'      => 'required|string',
            'tanggal'              => 'required|date',
            'kuantitas'            => 'required|numeric',
            'spesifikasi'          => 'nullable|string|max:255',
            'merk'                 => 'nullable|string|max:255',
        ]);

        $spesifikasiMurni = $request->spesifikasi ?? '-';

        DB::transaction(function () use ($request, $id, $spesifikasiMurni) {
            // Urus log dari model pembantu baru
            $log = MutasiMaterialPembantu::findOrFail($id);
            $materialLama = MasterMaterialPembantu::findOrFail($log->material_pembantu_id);
            $materialBaru = MasterMaterialPembantu::findOrFail($request->material_pembantu_id);

            $this->balikkanStokMaterial($materialLama, $log->jenis_transaksi, $log->kuantitas);

            $log->update([
                'material_pembantu_id' => $materialBaru->id,
                'jenis_transaksi'      => $request->jenis_transaksi,
                'kuantitas'            => $request->kuantitas,
                'tanggal'              => $request->tanggal,
                
                'spesifikasi'          => $spesifikasiMurni,
                'merk'                 => $request->merk ?? '-',
                'jenis_kimia'          => $request->jenis_kimia,
                'grit'                 => $request->grit,
                
                'satuan_input'         => $request->satuan_input,
                'asal_atau_proyek'     => $request->asal_atau_proyek,
                'keterangan'           => $spesifikasiMurni,
            ]);

            $this->terapkanStokMaterial($materialBaru, $request->jenis_transaksi, $request->kuantitas);
    });

        return redirect()->back()->with('success', 'Data transaksi stok pembantu berhasil diperbarui!');
    }

    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            $log = MutasiMaterialPembantu::findOrFail($id);
            $material = MasterMaterialPembantu::findOrFail($log->material_pembantu_id);

            $this->balikkanStokMaterial($material, $log->jenis_transaksi, $log->kuantitas);
            $log->delete();
        });

        return redirect()->back()->with('success', 'Data transaksi material pembantu berhasil dihapus!');
    }

    private function terapkanStokMaterial(MasterMaterialPembantu $material, string $jenisTransaksi, $kuantitas): void
    {
        if ($jenisTransaksi === 'Barang Keluar') {
            $material->decrement('stok_sekarang', $kuantitas);
        } else {
            $material->increment('stok_sekarang', $kuantitas);
        }
    }

    private function balikkanStokMaterial(MasterMaterialPembantu $material, string $jenisTransaksi, $kuantitas): void
    {
        if ($jenisTransaksi === 'Barang Keluar') {
            $material->increment('stok_sekarang', $kuantitas);
        } else {
            $material->decrement('stok_sekarang', $kuantitas);
        }
    }
}
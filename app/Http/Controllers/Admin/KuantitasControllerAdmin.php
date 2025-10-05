<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kuantitas;
use App\Models\StokWarung;
use App\Models\HargaJual; // Meskipun tidak dipakai di fungsi yang tersisa, ini tetap dipertahankan
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule; // 👈 Import Rule untuk Unique Validation
use Illuminate\Support\Facades\DB;

class KuantitasControllerAdmin extends Controller
{
    /**
     * Menampilkan view untuk membuat (create) kuantitas baru.
     * View ini juga menampilkan daftar dan menyediakan modal untuk edit.
     */
    public function create(Request $request)
    {
        $idStokWarung = $request->query('id_stok_warung');
        $stokWarung = StokWarung::with(['barang', 'warung'])->get();
        $selectedStokWarung = null;
        $hargaJualSatuanDasar = 0;
        $groupedKuantitas = collect();

        if ($idStokWarung) {
            $selectedStokWarung = StokWarung::with(['barang', 'warung'])->find($idStokWarung);

            if ($selectedStokWarung) {
                // Ambil harga jual dasar (asumsi harga satuan)
                $hargaDasar = HargaJual::where('id_barang', $selectedStokWarung->id_barang)
                    ->where('id_warung', $selectedStokWarung->id_warung)
                    ->latest('periode_awal')
                    ->first();

                $hargaJualSatuanDasar = $hargaDasar?->harga_jual_range_akhir ?? 0;

                // 🔹 Ambil semua kuantitas yang terkait dengan Stok Warung ini
                $groupedKuantitas = Kuantitas::with(['stokWarung.barang', 'stokWarung.warung'])
                    ->where('id_stok_warung', $selectedStokWarung->id)
                    ->get()
                    ->groupBy(fn($k) => $k->stokWarung->barang->id ?? 'tanpa_barang');
            }
        }

        return view('admin.kuantitas.create', compact(
            'stokWarung',
            'selectedStokWarung',
            'hargaJualSatuanDasar',
            'groupedKuantitas'
        ));
    }


    /**
     * Menyimpan kuantitas baru ke database.
     */
    public function store(Request $request)
    {
        $idStokWarung = $request->input('id_stok_warung');

        $request->validate([
            'id_stok_warung' => 'required|exists:stok_warung,id',
            'jumlah' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('kuantitas')->where(fn($q) => $q->where('id_stok_warung', $idStokWarung)),
            ],
            'harga_jual' => [
                'required',
                'numeric',
                'min:0',
                Rule::unique('kuantitas')->where(fn($q) => $q->where('id_stok_warung', $idStokWarung)),
            ],
        ]);

        Kuantitas::create($request->all());

        // 🔹 Recalculate harga_jual_range_awal
        $this->updateHargaRangeAwal($idStokWarung);

        return redirect()->route('admin.kuantitas.create', ['id_stok_warung' => $idStokWarung])
            ->with('success', 'Varian Kuantitas berhasil ditambahkan.');
    }


    public function update(Request $request, $id)
    {
        $kuantitas = Kuantitas::findOrFail($id);
        $idStokWarung = $request->input('id_stok_warung');

        $data = $request->validate([
            'id_stok_warung' => 'required|exists:stok_warung,id',
            'jumlah' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('kuantitas')->where(fn($q) => $q->where('id_stok_warung', $idStokWarung))->ignore($id),
            ],
            'harga_jual' => [
                'required',
                'numeric',
                'min:0',
                Rule::unique('kuantitas')->where(fn($q) => $q->where('id_stok_warung', $idStokWarung))->ignore($id),
            ],
        ]);

        $kuantitas->update($data);

        // 🔹 Recalculate harga_jual_range_awal
        $this->updateHargaRangeAwal($idStokWarung);

        return redirect()->route('admin.kuantitas.create', ['id_stok_warung' => $idStokWarung])
            ->with('success', 'Varian Kuantitas berhasil diperbarui.');
    }


    public function destroy($id)
    {
        try {
            $kuantitas = Kuantitas::findOrFail($id);
            $idStokWarung = $kuantitas->id_stok_warung;

            $kuantitas->delete();

            // 🔹 Recalculate harga_jual_range_awal
            $this->updateHargaRangeAwal($idStokWarung);

            return redirect()->route('admin.kuantitas.create', ['id_stok_warung' => $idStokWarung])
                ->with('success', 'Varian Kuantitas berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus kuantitas. Data mungkin sudah tidak tersedia.');
        }
    }
    private function updateHargaRangeAwal($idStokWarung)
    {
        $stok = StokWarung::with('warung', 'barang')->find($idStokWarung);
        // dd($stok);
        if (!$stok) return;

        $idWarung = $stok->id_warung;
        $idBarang = $stok->id_barang;

        // Ambil semua kuantitas dari stok warung ini
        $kuantitasList = $stok->kuantitas()->get();
        if ($kuantitasList->isEmpty()) {
            // Jika tidak ada kuantitas, reset harga range awal jadi sama dengan akhir
            HargaJual::where('id_warung', $idWarung)
                ->where('id_barang', $idBarang)
                ->update(['harga_jual_range_awal' => DB::raw('harga_jual_range_akhir')]);
            return;
        }

        // Hitung harga per item = harga_jual / jumlah
        $hargaPerItemTerkecil = $kuantitasList
            ->map(fn($k) => $k->harga_jual / max($k->jumlah, 1))
            ->min();
// dd($hargaPerItemTerkecil, $idWarung, $idBarang);

        // Update ke model HargaJual
        HargaJual::where('id_warung', $idWarung)
            ->where('id_barang', $idBarang)
            ->update(['harga_jual_range_awal' => $hargaPerItemTerkecil]);
    }
}

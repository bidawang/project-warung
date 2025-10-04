<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kuantitas;
use App\Models\StokWarung;
use App\Models\Laba;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KuantitasControllerAdmin extends Controller
{
    public function create(Request $request)
    {
        $selectedStokWarung = null;
        $hargaJualSatuanDasar = 0;
        $idStokWarung = $request->input('id_stok_warung');
        $barangId = null;

        // --- 1. Ambil data StokWarung yang dipilih & Hitung Harga Dasar ---
        if (!empty($idStokWarung)) {
            $selectedStokWarung = StokWarung::with(['barang.transaksiBarang' => function ($query) {
                $query->with('areaPembelian')->latest();
            }, 'warung'])
                ->find($idStokWarung);

            if ($selectedStokWarung) {
                $barangId = $selectedStokWarung->id_barang;

                if ($selectedStokWarung->barang) {
                    $transaksi = $selectedStokWarung->barang->transaksiBarang->first();

                    if ($transaksi) {
                        $hargaDasarPerSatuan = ($transaksi->harga ?? 0) / max($transaksi->jumlah, 1);
                        $markupPercent = optional(optional($transaksi)->areaPembelian)->markup ?? 0;
                        $hargaSetelahMarkup = $hargaDasarPerSatuan * (1 + $markupPercent / 100);

                        $laba = Laba::where('input_minimal', '<=', $hargaSetelahMarkup)
                            ->where('input_maksimal', '>=', $hargaSetelahMarkup)
                            ->first();

                        $hargaJualSatuanDasar = $laba ? $laba->harga_jual : 0;
                    }
                }
            }
        }

        // --- 2. Ambil & Kelompokkan Data Kuantitas (Kolom Kiri) ---
        // Jika ada barang yang dipilih, ambil SEMUA kuantitas yang terkait dengan ID BARANG tersebut.
        if ($barangId) {
            $listKuantitas = Kuantitas::whereHas('stokWarung', function ($query) use ($barangId) {
                $query->where('id_barang', $barangId);
            })
                ->with('stokWarung.barang', 'stokWarung.warung')
                ->latest()
                ->get();
        } else {
            // Jika tidak ada stok warung yang dipilih, kosongkan list kuantitas
            $listKuantitas = collect([]);
        }

        // Kelompokkan list kuantitas berdasarkan ID Barang (Walaupun sudah difilter, ini untuk memastikan struktur yang benar)
        $groupedKuantitas = $listKuantitas->groupBy('stokWarung.barang_id');

        // --- 3. Ambil data Stok Warung untuk dropdown ---
        $stokWarung = StokWarung::with('barang', 'warung')->get();

        return view('admin.kuantitas.create', compact('stokWarung', 'selectedStokWarung', 'hargaJualSatuanDasar', 'groupedKuantitas'));
    }

    /**
     * Menyimpan kuantitas baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_stok_warung' => 'required|exists:stok_warung,id',
            'jumlah' => 'required|integer|min:1',
            'harga_jual' => 'required|numeric|min:0',
        ]);

        Kuantitas::create($request->all());

        return redirect()->route('admin.stokbarang.index')->with('success', 'Kuantitas berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit kuantitas yang ada.
     */
    public function edit($id)
    {
        $kuantitas = Kuantitas::with(['stokWarung.barang.transaksiBarang.areaPembelian'])->findOrFail($id);
        $hargaJualSatuanDasar = null;

        $stok = $kuantitas->stokWarung;

        if ($stok && $stok->barang) {
            // Ambil transaksi barang terbaru untuk menghitung harga satuan dasar
            $transaksi = $stok->barang->transaksiBarang()->latest()->first();

            if ($transaksi) {
                // 1. Hitung harga beli satuan setelah markup
                $hargaDasarPerSatuan = ($transaksi->harga ?? 0) / max($transaksi->jumlah, 1);
                $markupPercent = optional($transaksi->areaPembelian)->markup ?? 0;
                $hargaSetelahMarkup = $hargaDasarPerSatuan * (1 + $markupPercent / 100);

                // 2. Ambil harga jual dasar dari tabel Laba
                $laba = Laba::where('input_minimal', '<=', $hargaSetelahMarkup)
                    ->where('input_maksimal', '>=', $hargaSetelahMarkup)
                    ->first();

                $hargaJualSatuanDasar = $laba ? $laba->harga_jual : 0;
            }
        }

        return view('admin.kuantitas.edit', compact('kuantitas', 'hargaJualSatuanDasar'));
    }

    /**
     * Memperbarui kuantitas yang ada di database.
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'id_stok_warung' => 'required|exists:stok_warung,id',
            'jumlah' => 'required|integer|min:1',
            'harga_jual' => 'required|numeric|min:0',
        ]);

        $kuantitas = Kuantitas::findOrFail($id);
        $kuantitas->update($data);

        return redirect()->route('admin.stokbarang.index')
            ->with('success', 'Kuantitas berhasil diperbarui.');
    }


    /**
     * Menghapus kuantitas dari database.
     */
    public function destroy($id)
    {
        $kuantitas = Kuantitas::findOrFail($id);
        $kuantitas->delete();

        return redirect()->back()->with('success', 'Kuantitas berhasil dihapus.');
    }
}

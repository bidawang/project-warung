<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Kuantitas;
use App\Models\StokWarung;
use App\Models\Laba;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KuantitasController extends Controller
{
    /**
     * Menampilkan daftar semua kuantitas yang tersedia.
     */
    public function index()
    {
        $kuantitas = Kuantitas::with(['stokWarung.warung', 'stokWarung.barang'])->get();
        return view('kasir.kuantitas.index', compact('kuantitas'));
    }

    /**
     * Menampilkan form untuk membuat kuantitas baru, dengan
     * opsi untuk mengisi ID stok warung secara otomatis.
     */
    public function create(Request $request)
    {
        $selectedStokWarung = null;
        $hargaJualSatuanDasar = null;

        // Ambil ID stok warung dari query string (atau input tersembunyi)
        $idStokWarung = $request->input('id_stok_warung');
        if (!empty($idStokWarung)) {
            $selectedStokWarung = StokWarung::with(['barang.transaksiBarang' => function ($query) {
                $query->with('areaPembelian')->latest();
            }, 'warung'])
                ->findOrFail($idStokWarung);

            if ($selectedStokWarung && $selectedStokWarung->barang) {
                // Ambil transaksi barang terbaru untuk menghitung harga satuan dasar
                $transaksi = $selectedStokWarung->barang->transaksiBarang->first();

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
        }

        $stokWarung = StokWarung::with('barang', 'warung')->get();

        return view('kasir.kuantitas.create', compact('stokWarung', 'selectedStokWarung', 'hargaJualSatuanDasar'));
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

        return redirect()->route('kasir.stokbarang.index')->with('success', 'Kuantitas berhasil ditambahkan.');
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

        return view('kasir.kuantitas.edit', compact('kuantitas', 'hargaJualSatuanDasar'));
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

    return redirect()->route('kasir.stokbarang.index')
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

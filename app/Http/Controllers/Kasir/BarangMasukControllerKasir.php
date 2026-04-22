<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\BarangMasuk;
use App\Events\BarangMasukUpdated;
use App\Models\StokWarung;
use App\Models\TransaksiBarangMasuk;
use App\Models\HargaJual;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\Log; // Pastikan ini diimpor jika menggunakan Log::error
use Illuminate\Support\Facades\DB;
use App\Models\RencanaBelanja; // Pastikan model ini diimpor

class BarangMasukControllerKasir extends Controller
{

    /**
     * Simpan data barang masuk baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_transaksi_barang' => 'required|exists:transaksi_barang,id',
            'id_stok_warung' => 'required|exists:stok_warung,id',
            'jumlah' => 'required|integer|min:1',
            'status' => 'required|string|max:100',
            'keterangan' => 'nullable|string',
        ]);

        BarangMasuk::create($validated);
        BarangMasukUpdated::dispatch(Auth::id());

        return redirect()->route('barangmasuk.index')->with('success', 'Barang masuk berhasil ditambahkan');
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'barangMasuk' => 'required|array',
            'barangMasuk.*' => 'exists:barang_masuk,id',
            'status_baru' => 'required|in:terima,tolak',
        ]);

        try {
            DB::beginTransaction();

            // 1. Update status barang masuk
            BarangMasuk::whereIn('id', $request->barangMasuk)
                ->update(['status' => $request->status_baru]);

            $message = 'Status barang masuk berhasil diperbarui menjadi ' . $request->status_baru;

            if ($request->status_baru === 'terima') {

                $barangMasukItems = BarangMasuk::with('stokWarung')
                    ->whereIn('id', $request->barangMasuk)
                    ->where('status', 'terima')
                    ->get();

                foreach ($barangMasukItems as $item) {

                    $jumlahMasuk = (float) $item->jumlah;

                    if ($jumlahMasuk > 0) {
                        // ✅ Tambah stok
                        StokWarung::where('id', $item->id_stok_warung)
                            ->increment('jumlah', $jumlahMasuk);
                    }

                    // ✅ Update status transaksi sumber
                    TransaksiBarangMasuk::where('id', $item->id_transaksi_barang_masuk)
                        ->update(['status' => 'terima']);

                    // ==================================================
                    // 🔥 LOGIKA BARU: UPDATE RENCANA BELANJA
                    // ==================================================

                    $stokWarung = $item->stokWarung;

                    if ($stokWarung) {

                        $rencanas = RencanaBelanja::where('id_warung', $stokWarung->id_warung)
                            ->where('id_barang', $stokWarung->id_barang)
                            ->where('status', 'pending')
                            ->get();

                        // 👉 kalau ADA rencana → berarti ini belanja tambahan
                        if ($rencanas->isNotEmpty()) {

                            foreach ($rencanas as $rencana) {
                                $rencana->update([
                                    'jumlah_diterima' => $item->jumlah,
                                    'status'          => 'selesai',
                                ]);
                            }
                        }
                    }
// dd('cek',$item->id, $jumlahMasuk, $stokWarung->id_warung, $stokWarung->id_barang);
                    // ==================================================
                    // 🔥 UPDATE STATUS HARGA JUAL (AKTIF / NONAKTIF)
                    // ==================================================

                    $stokWarung = $item->stokWarung;

                    if ($stokWarung) {

                        // Ambil semua harga jual untuk barang & warung ini (urut terbaru)
                        $hargaJualList = HargaJual::where('id_warung', $stokWarung->id_warung)
                            ->where('id_barang', $stokWarung->id_barang)
                            ->orderByDesc('id')
                            ->get();
// dd($hargaJualList);
                        if ($hargaJualList->isNotEmpty()) {

                            // 👉 harga jual TERBARU = true
                            $hargaJualTerbaru = $hargaJualList->first();
                            // dd($hargaJualTerbaru->id, $hargaJualTerbaru->status);
                            $hargaJualTerbaru->update([
                                'status' => true
                            ]);
// dd($hargaJualTerbaru->id, $hargaJualTerbaru->status);
                            // 👉 harga jual SEBELUMNYA = false
                            $hargaJualSebelumnya = $hargaJualList->skip(1);

                            foreach ($hargaJualSebelumnya as $harga) {
                                $harga->update([
                                    'status' => false
                                ]);
                            }
                        }
                        // dd('cek');
                        // dd($hargaJualList->pluck('id', 'status')->toArray(), $hargaJualTerbaru->id, $hargaJualTerbaru->status, $hargaJualSebelumnya->status);
                    }


                    // ==================================================
                }

                $message .= ' dan stok barang berhasil ditambahkan.';
            }

            DB::commit();

            return redirect()->route('kasir.stokbarang.index')->with('success', $message);
        } catch (Exception $e) {

            DB::rollBack();

            Log::error("Gagal update status dan stok: " . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memperbarui status dan/atau stok.');
        }
    }
}

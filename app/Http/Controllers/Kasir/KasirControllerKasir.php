<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\StokWarung;
use App\Models\Laba;
use App\Models\User;

use Illuminate\Http\Request;

class KasirControllerKasir extends Controller
{
    public function index()
    {
        $idWarung = session('id_warung');

        if (!$idWarung) {
            return redirect()->route('kasir.kasir')
                ->with('error', 'ID warung tidak ditemukan di sesi.');
        }

        $stok_warungs = StokWarung::where('id_warung', $idWarung)
            ->with(['barang.transaksiBarang.areaPembelian', 'kuantitas'])
            ->get();

        $stok_warungs->transform(function ($stok) {
            // --- 1. Ambil stok langsung dari tabel stok_warung ---
            $stok->stok_saat_ini = $stok->jumlah;

            // --- 2. Ambil transaksi terbaru untuk hitung harga ---
            $transaksi = $stok->barang->transaksiBarang()->latest()->first();

            if (!$transaksi || $transaksi->jumlah == 0) {
                $stok->harga_jual = 0;
                $stok->kuantitas_list = [];
                return $stok;
            }

            // Harga dasar per satuan
            $hargaDasar = $transaksi->harga / max($transaksi->jumlah, 1);

            // Markup dari area pembelian
            $markupPercent = optional($transaksi->areaPembelian)->markup ?? 0;
            $hargaSatuan = $hargaDasar + ($hargaDasar * $markupPercent / 100);

            // Harga jual dasar dari tabel Laba
            $laba = Laba::where('input_minimal', '<=', $hargaSatuan)
                ->where('input_maksimal', '>=', $hargaSatuan)
                ->first();

            $hargaJualDasar = $laba ? $laba->harga_jual : 0;

            // Ambil kuantitas (bundle) dan urutkan descending
            $kuantitasList = $stok->kuantitas->sortByDesc('jumlah')->values();

            // Hitung harga jual sesuai kuantitas pembelian
            $stok->calculateHargaJual = function (int $beliQty) use ($kuantitasList, $hargaJualDasar) {
                $sisaQty = $beliQty;
                $totalHarga = 0;

                foreach ($kuantitasList as $bundle) {
                    $bundleQty = $bundle->jumlah;
                    $bundleHarga = $bundle->harga_jual;

                    if ($bundleQty <= 0) continue;

                    $kelipatan = intdiv($sisaQty, $bundleQty);
                    if ($kelipatan > 0) {
                        $totalHarga += $kelipatan * $bundleHarga;
                        $sisaQty -= $kelipatan * $bundleQty;
                    }
                }

                if ($sisaQty > 0) {
                    $totalHarga += $sisaQty * $hargaJualDasar;
                }

                return $totalHarga;
            };

            $stok->kuantitas_list = $kuantitasList->map(fn($k) => [
                'jumlah' => $k->jumlah,
                'harga_jual' => $k->harga_jual,
            ]);

            $stok->harga_jual = $hargaJualDasar;

            return $stok;
        });

        // Filter produk yang stoknya > 0
        $products = $stok_warungs->filter(fn($stok) => $stok->stok_saat_ini > 0);

        // Ambil daftar pengguna dengan role 'member'
        $members = User::where('role', 'member')->get();

        return view('kasir.kasir.index', compact('products', 'members'));
    }
}

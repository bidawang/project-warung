<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Warung;
use App\Models\HargaJual;
use App\Models\Barang;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HargaJualControllerAdmin extends Controller
{
    /**
     * Menampilkan detail harga untuk SEMUA Barang di SEMUA Warung.
     * Data dikelompokkan per Barang, dan di dalamnya terdapat daftar harga per Warung.
     * * @return \Illuminate\View\View
     */
    public function indexAllBarangPrices()
    {
        // 1. Ambil semua data Harga Jual yang aktif (periode-aware) dan eager load Warung/Barang
        $allHargaJual = HargaJual::with(['warung.area', 'warung.user', 'barang'])
            ->where(function ($q) {
                $q->whereNull('periode_awal')->orWhere('periode_awal', '<=', Carbon::now());
            })
            ->where(function ($q) {
                $q->whereNull('periode_akhir')->orWhere('periode_akhir', '>=', Carbon::now());
            })
            ->get();

        // 2. Ambil semua Barang dan Warung yang aktif
        $allBarang = Barang::all()->keyBy('id');
        // Eager load stokWarung hanya untuk barang yang ada
        $allWarung = Warung::with(['area', 'user', 'stokWarung' => fn($q) => $q->whereIn('id_barang', $allBarang->keys())])
                        ->get()
                        ->keyBy('id');


        // 3. Atur data ke dalam struktur yang mudah diolah di view: $BarangId => [Collection Warung Prices]
        // KUNCI PERBAIKAN: Masukkan $allHargaJual ke dalam use() statement closure
        $barangPricesByWarung = $allBarang->map(function ($barang) use ($allWarung, $allHargaJual) {

            // Inisialisasi daftar warung untuk barang ini
            $warungPrices = collect();

            // Loop melalui semua warung
            foreach ($allWarung as $warung) {
                // Ambil stok saat ini
                $stokSaatIni = $warung->stokWarung->where('id_barang', $barang->id)->first()->jumlah ?? 0;

                // Cari data harga jual yang sesuai untuk Barang dan Warung ini dari data yang sudah di-load
                // Sort berdasarkan ID (asumsi ID yang lebih tinggi adalah data terbaru)
                $hargaJual = $allHargaJual
                    ->where('id_barang', $barang->id)
                    ->where('id_warung', $warung->id)
                    ->sortByDesc('id')
                    ->first();

                // Hanya tampilkan Warung jika ada STOK ATAU HARGA JUAL yang tercatat
                if ($stokSaatIni > 0 || $hargaJual) {

                    $hargaModal = $hargaJual->harga_modal ?? 0;
                    $hargaJualAwal = $hargaJual->harga_jual_range_awal ?? 0;
                    $hargaJualAkhir = $hargaJual->harga_jual_range_akhir ?? 0;

                    // Hitung persentase dan laba
                    $persentaseLaba = 'N/A';
                    $labaRange = 'N/A';

                    if ($hargaModal > 0 && $hargaJualAwal > 0) {
                        $persenAwal = (($hargaJualAwal - $hargaModal) / $hargaModal) * 100;
                        $persenAkhir = (($hargaJualAkhir - $hargaModal) / $hargaModal) * 100;

                        if (number_format($persenAwal, 2) === number_format($persenAkhir, 2)) {
                            $persentaseLaba = number_format($persenAwal, 2) . ' %';
                        } else {
                            $persentaseLaba = number_format($persenAwal, 2) . ' - ' . number_format($persenAkhir, 2) . ' %';
                        }

                        $labaAwal = $hargaJualAwal - $hargaModal;
                        $labaAkhir = $hargaJualAkhir - $hargaModal;

                        if ($labaAwal === $labaAkhir) {
                            $labaRange = number_format($labaAwal, 0, ',', '.');
                        } else {
                            $labaRange = number_format($labaAwal, 0, ',', '.') . ' - ' . number_format($labaAkhir, 0, ',', '.');
                        }
                    }

                    $warungPrices->push((object)[
                        'warung_id' => $warung->id,
                        'nama_warung' => $warung->nama_warung,
                        'area' => $warung->area->area ?? '-',
                        'stok_saat_ini' => $stokSaatIni,
                        'harga_modal' => $hargaModal,
                        'harga_jual_range_awal' => $hargaJualAwal,
                        'harga_jual_range_akhir' => $hargaJualAkhir,
                        'persentase_laba' => $persentaseLaba,
                        'laba_range' => $labaRange,
                    ]);
                }
            }

            // Hanya kembalikan barang jika ada setidaknya satu warung yang memiliki data harga/stok
            if ($warungPrices->isNotEmpty()) {
                return (object)[
                    'barang' => $barang,
                    'prices' => $warungPrices
                ];
            }
            return null;

        })->filter()->values(); // Hapus item null dan reset keys

        // 4. Kirim data yang sudah terstruktur ke view
        // Sesuaikan nama view jika perlu, di sini menggunakan monitor_all_prices_detail
        return view('admin.harga_jual.monitor_all_prices', compact('barangPricesByWarung'));
    }

    /**
     * Menampilkan perbandingan harga per Warung untuk SATU Barang tertentu (mempertahankan fungsi yang lama).
     * * @param int $id ID Barang
     * @return \Illuminate\View\View
     */
    public function showWarungPrices($id)
    {
        // Cukup ambil data yang diperlukan dari Warung dan HargaJual
        $barang = Barang::findOrFail($id);
        $allWarung = Warung::with(['user', 'area'])->get();

        $warungWithPriceData = $allWarung->map(function ($warung) use ($barang) {

            $hargaJual = HargaJual::where('id_warung', $warung->id)
                ->where('id_barang', $barang->id)
                ->where(function ($q) {
                    $q->whereNull('periode_awal')->orWhere('periode_awal', '<=', Carbon::now());
                })
                ->where(function ($q) {
                    $q->whereNull('periode_akhir')->orWhere('periode_akhir', '>=', Carbon::now());
                })
                ->latest('id')
                ->first();

            // Asumsi relasi stokWarung() ada di model Warung
            $stok = $warung->stokWarung()->where('id_barang', $barang->id)->first();

            // Inisialisasi data
            $warung->harga_modal = $hargaJual->harga_modal ?? 0;
            $warung->harga_jual_range_awal = $hargaJual->harga_jual_range_awal ?? 0;
            $warung->harga_jual = $hargaJual->harga_jual_range_akhir ?? 0;
            $warung->persentase_laba = 'N/A';
            $warung->laba_range = 'N/A';
            $warung->stok_saat_ini = $stok->jumlah ?? 0;

            if ($hargaJual && $warung->harga_modal > 0) {
                $persenAwal = (($warung->harga_jual_range_awal - $warung->harga_modal) / $warung->harga_modal) * 100;
                $persenAkhir = (($warung->harga_jual - $warung->harga_modal) / $warung->harga_modal) * 100;

                if (number_format($persenAwal, 2) === number_format($persenAkhir, 2)) {
                    $warung->persentase_laba = number_format($persenAwal, 2) . ' %';
                } else {
                    $warung->persentase_laba = number_format($persenAwal, 2) . ' - ' . number_format($persenAkhir, 2) . ' %';
                }

                $labaAwal = $warung->harga_jual_range_awal - $warung->harga_modal;
                $labaAkhir = $warung->harga_jual - $warung->harga_modal;

                if ($labaAwal === $labaAkhir) {
                    $warung->laba_range = number_format($labaAwal, 0, ',', '.');
                } else {
                    $warung->laba_range = number_format($labaAwal, 0, ',', '.') . ' - ' . number_format($labaAkhir, 0, ',', '.');
                }
            }

            return $warung;
        })->filter(fn($w) => $w->stok_saat_ini > 0 || $w->harga_modal > 0)->values(); // Filter jika tidak ada stok atau harga

        return view('admin.harga_jual.show_warung_prices', compact('barang', 'warungWithPriceData'));
    }
    public function updateHargaJual(Request $request)
    {
        $request->validate([
            'id_warung' => 'required|exists:warung,id',
            'id_barang' => 'required|exists:barang,id',
            'harga_jual_range_awal' => 'required|min:0',
            'harga_jual_range_akhir' => 'required|min:0|gte:harga_jual_range_awal',
        ]);

        $id_warung = $request->id_warung;
        $id_barang = $request->id_barang;
// dd('here');
        // Cari HargaJual aktif terbaru.
        $hargaJual = HargaJual::where('id_warung', $id_warung)
            ->where('id_barang', $id_barang)
            ->where(function ($q) {
                $q->whereNull('periode_awal')->orWhere('periode_awal', '<=', Carbon::now());
            })
            ->where(function ($q) {
                $q->whereNull('periode_akhir')->orWhere('periode_akhir', '>=', Carbon::now());
            })
            ->latest('id')
            ->first();


        if ($hargaJual && $hargaJual->harga_modal > 0) {
            // Update record yang sudah ada
            $hargaJual->update([
                'harga_jual_range_awal' => $request->harga_jual_range_awal,
                'harga_jual_range_akhir' => $request->harga_jual_range_akhir,
            ]);

            return redirect()->route('admin.harga_jual.monitor_all_prices')
                    ->with('success', "Harga jual untuk Barang '{$hargaJual->barang->nama_barang}' di Warung '{$hargaJual->warung->nama_warung}' berhasil diperbarui.");
        }

        // Handle jika tidak ada harga modal, atau record tidak ditemukan
        return redirect()->route('admin.harga_jual.monitor_all_prices')
                ->with('error', 'Gagal memperbarui harga. Pastikan Harga Modal untuk barang ini sudah terdaftar dan aktif.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\TransaksiBarang;
use App\Models\TransaksiKas;
use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\AreaPembelian;
use App\Models\Warung;
use App\Models\Laba;
use App\Models\StokWarung;
use App\Models\TransaksiAwal;
use App\Models\TransaksiLainLain;
use App\Models\HutangBarangMasuk;
use App\Models\RencanaBelanja;
use App\Models\HargaJual; // ⭐ Model baru

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransaksiBarangController extends Controller
{
    // public function index(Request $request)
    // {
    //     $status = $request->query('status', 'pending');

    //     $query = TransaksiBarang::with(['transaksiKas', 'barang', 'barangMasuk']);

    //     if ($status === 'pending') {
    //         // Logika eksklusif untuk 'pending': Transaksi Barang (Stok Sumber) yang belum pernah dikirim.
    //         $query->doesntHave('barangMasuk');
    //     } elseif ($status === 'kirim') {
    //         $query->whereHas('barangMasuk', function ($q) {
    //             $q->where('status', 'pending');
    //         });
    //     } elseif ($status === 'terima') {
    //         $query->whereHas('barangMasuk', function ($q) {
    //             $q->where('status', 'terima');
    //         });
    //     } elseif ($status === 'tolak') {
    //         $query->whereHas('barangMasuk', function ($q) {
    //             $q->where('status', 'tolak');
    //         });
    //     }

    //     $transaksibarangs = $query->paginate(10)->appends(['status' => $status]);

    //     $warungs = Warung::all();

    //     $stokWarungData = StokWarung::select('id_warung', 'id_barang')
    //         ->withSum(['barangMasuk as stok' => function ($q) {
    //             $q->where('status', 'terima');
    //         }], 'jumlah')
    //         ->get()
    //         ->map(fn($item) => [
    //             'id_warung' => $item->id_warung,
    //             'id_barang' => $item->id_barang,
    //             'stok' => $item->stok ?? 0,
    //         ]);

    //     // =======================================================
    //     // Tambahan: data untuk frontend (stockByBarang)
    //     // =======================================================
    //     $allTransactions = $transaksibarangs->getCollection()->map(function ($trx) {
    //         return [
    //             'id'          => $trx->id,
    //             'barang_id'   => $trx->barang->id ?? null,
    //             'barang_nama' => $trx->barang->nama_barang ?? '-',
    //             'jumlah_awal' => $trx->jumlah_awal,
    //             'harga'       => $trx->harga,
    //         ];
    //     });

    //     // Group by barang_id -> list stok
    //     $stockByBarang = $allTransactions->groupBy('barang_id')->map(function ($items) {
    //         return $items->map(fn($i) => [
    //             'id'          => $i['id'],
    //             'jumlah_awal' => $i['jumlah_awal'],
    //             'harga'       => $i['harga'],
    //         ])->values();
    //     });

    //     // =======================================================
    //     // Tambahan: Rencana Belanja
    //     // =======================================================
    //     $rencanaBelanjas = RencanaBelanja::with(['barang', 'warung'])
    //         ->whereColumn('jumlah_dibeli', '<', 'jumlah_awal')
    //         ->get();

    //     $rencanaBelanjaByWarung = $rencanaBelanjas->groupBy('warung.nama_warung');
    //     $rencanaBelanjaByBarang = $rencanaBelanjas->groupBy('barang.nama_barang');
    //     $rencanaBelanjaTotalByBarang = $rencanaBelanjas
    //         ->groupBy('barang.nama_barang')
    //         ->map(fn($items) => $items->sum(fn($item) => $item->jumlah_awal - $item->jumlah_dibeli));
    //     // dd($transaksibarangs->take(2));
    //     // dd($transaksibarangs);
    //     return view('admin.transaksibarang.index', compact(
    //         'transaksibarangs',
    //         'status',
    //         'warungs',
    //         'stokWarungData',
    //         'rencanaBelanjaByWarung',
    //         'rencanaBelanjaByBarang',
    //         'rencanaBelanjaTotalByBarang',
    //         'stockByBarang',       // 👈 baru
    //         'allTransactions'      // 👈 baru
    //     ));
    // }


    protected function getStockData()
    {
        // Mengambil semua TransaksiBarang (Stok Sumber) yang masih memiliki stok > 0
        $allTransactions = TransaksiBarang::with('barang')
            ->where('jumlah', '>', 0)
            ->get() // Ambil semua (tidak dipaginasi)
            ->map(function ($trx) {
                return [
                    'id'          => $trx->id,
                    'id_barang'   => $trx->id_barang,
                    'barang_nama' => $trx->barang->nama_barang ?? '-',
                    'jumlah'      => $trx->jumlah,
                    'harga'       => $trx->harga,
                ];
            });

        // Group by id_barang -> list stok sumber
        $stockByBarang = $allTransactions->groupBy('id_barang')->map(function ($items) {
            return $items->map(fn($i) => [
                'id'          => $i['id'],
                'jumlah_awal' => $i['jumlah'],
                'harga'       => $i['harga'],
            ])->values();
        });

        return [
            'stockByBarang'     => $stockByBarang,
            'warungs'           => Warung::all(),
            // Tambahkan koleksi transaksi mentah untuk JS
            'allTransactions'   => $allTransactions,
        ];
    }

    // --- FUNGSI 1: DAFTAR STOK PENGIRIMAN (Kolom Kiri) ---
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending');
        $query = TransaksiBarang::with(['transaksiKas', 'barang', 'barangMasuk']);

        // Logika Filter Status (Sama seperti sebelumnya)
        if ($status === 'pending') {
            // Tampilkan semua stok yang masih memiliki jumlah > 0
            $query = TransaksiBarang::with(['transaksiKas', 'barang'])->where('jumlah', '>', 0);
        } elseif ($status === 'kirim') {
            $query->whereHas('barangMasuk', fn($q) => $q->where('status', 'pending'));
        } elseif ($status === 'terima') {
            $query->whereHas('barangMasuk', fn($q) => $q->where('status', 'terima'));
        } elseif ($status === 'tolak') {
            $query->whereHas('barangMasuk', fn($q) => $q->where('status', 'tolak'));
        }

        $transaksibarangs = $query->paginate(10)->appends(['status' => $status]);

        // Ambil data stok dan warung
        $data = $this->getStockData();
        $warungs = $data['warungs'];
        $stockByBarang = $data['stockByBarang'];

        return view('admin.transaksibarang.index', compact(
            'transaksibarangs',
            'status',
            'warungs',
            'stockByBarang'
        ));
    }

    // --- FUNGSI 2: RENCANA BELANJA PER WARUNG (Kolom Kanan) ---
    public function indexRencana(Request $request)
    {
        // Ambil data stok dan warung, termasuk 'allTransactions'
        $data = $this->getStockData();
        $warungs = $data['warungs'];
        $stockByBarang = $data['stockByBarang'];
        // Ambil variabel ini untuk di-passing ke JS
        $allTransactionsForJs = $data['allTransactions'];

        // Ambil Rencana Belanja yang belum selesai
        $rencanaBelanjas = RencanaBelanja::with(['barang', 'warung'])
            ->whereColumn('jumlah_dibeli', '<', 'jumlah_awal')
            ->get();

        // Grouping berdasarkan ID Warung
        $rencanaBelanjaByWarung = $rencanaBelanjas->groupBy('id_warung');

        return view('admin.rencanabelanja.index', compact(
            'warungs',
            'stockByBarang',
            'rencanaBelanjaByWarung',
            'allTransactionsForJs' // Kirim data ini untuk digunakan oleh JS
        ));
    }


    /**
     * Update status massal untuk transaksi barang (dari checkbox di pending)
     */
    public function updateStatusMassal(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'status' => 'required|in:diterima,ditolak',
        ]);

        TransaksiBarang::whereIn('id', $request->ids)->update(['status' => $request->status]);

        return redirect()->route('transaksibarang.index', ['status' => 'pending'])
            ->with('success', 'Status transaksi berhasil diperbarui.');
    }


    public function create()
    {
        // Ambil data yang sudah ada
        // Asumsi model-model ini sudah didefinisikan dengan benar
        $transaksis = TransaksiKas::all();
        $barangs = Barang::all();
        $areas = AreaPembelian::all();

        // Ambil semua data Rencana Belanja, eager load relasi yang dibutuhkan
        $rencanaBelanjas = RencanaBelanja::with(['barang', 'warung'])
            ->whereColumn('jumlah_dibeli', '<', 'jumlah_awal') // Lebih aman menggunakan whereColumn
            ->get();

        // Opsi 1: Pengelompokan berdasarkan Warung
        $rencanaBelanjaByWarung = $rencanaBelanjas->groupBy('warung.nama_warung');

        // Opsi 2: Pengelompokan berdasarkan Barang (Detail Warung)
        $rencanaBelanjaByBarang = $rencanaBelanjas->groupBy('barang.nama_barang');

        // Opsi 3: Menghitung TOTAL KEBUTUHAN untuk setiap jenis Barang (Sesuai Permintaan)
        $rencanaBelanjaTotalByBarang = $rencanaBelanjas
            ->groupBy('barang.nama_barang') // Kelompokkan berdasarkan nama barang
            ->map(function ($items, $namaBarang) {
                // Hitung total kebutuhan (jumlah_awal - jumlah_dibeli) untuk barang ini
                return $items->sum(function ($item) {
                    return $item->jumlah_awal - $item->jumlah_dibeli;
                });
            });
        // dd($areas);
        return view('admin.transaksibarang.create', compact(
            'transaksis',
            'barangs',
            'areas',
            'rencanaBelanjaByWarung',
            'rencanaBelanjaByBarang',
            'rencanaBelanjaTotalByBarang' // Tambahkan variabel baru ini
        ));
    }



    public function store(Request $request)
    {
        // dd($request->all());
        // Simpan transaksi awal
        $transaksi = TransaksiAwal::create([
            'tanggal' => now(),
            'keterangan' => $request->keterangan,
            'total' => 0, // akan dihitung di bawah
        ]);

        $grandTotal = 0;

        // Loop area pembelian
        if ($request->id_area) {
            foreach ($request->id_area as $areaIndex => $areaId) {
                if (isset($request->id_barang[$areaIndex])) {
                    foreach ($request->id_barang[$areaIndex] as $i => $barangId) {
                        $jumlah = $request->jumlah[$areaIndex][$i] ?? 0;
                        $harga  = $request->total_harga[$areaIndex][$i] ?? 0;
                        $tanggalKadaluarsa = $request->tanggal_kadaluarsa[$areaIndex][$i] ?? null;

                        TransaksiBarang::create([
                            'id_transaksi_awal'   => $transaksi->id,
                            'id_area_pembelian'   => $areaId,
                            'id_barang'           => $barangId,
                            'jumlah'              => $jumlah,
                            'harga'               => $harga,
                            'tanggal_kadaluarsa'  => $tanggalKadaluarsa, // Menambahkan kolom baru
                            'jenis'               => 'masuk',
                            // 'status'              => 'pending',
                        ]);

                        $grandTotal += $harga;
                    }
                }
            }
        }

        // Loop transaksi lain-lain (opsional)
        if ($request->lain_keterangan && $request->lain_harga) {
            foreach ($request->lain_keterangan as $i => $ket) {
                $harga = $request->lain_harga[$i] ?? 0;

                // Lewati jika kosong
                if (!$ket && !$harga) {
                    continue;
                }

                TransaksiLainLain::create([
                    'id_transaksi_awal' => $transaksi->id,
                    'keterangan'        => $ket,
                    'harga'             => $harga,
                ]);

                $grandTotal += $harga;
            }
        }

        // Update total transaksi awal
        $transaksi->update(['total' => $grandTotal]);

        return redirect()->route('transaksibarang.index')
            ->with('success', 'Transaksi berhasil ditambahkan!');
    }


    public function kirimMassalProses(Request $request)
    {
        // ... (Filter dan Validasi tetap sama)
        $allData = $request->all();
        $transaksiFiltered = collect($allData['transaksi'] ?? [])
            ->filter(function ($trx) {
                return isset($trx['barang_id']) && !empty($trx['details']);
            })
            ->toArray();

        $request->merge(['transaksi' => $transaksiFiltered]);
        $data = $request->validate([
            'transaksi' => 'required|array',
            'transaksi.*.barang_id' => 'required|exists:barang,id',
            'transaksi.*.details' => 'required|array',
            'transaksi.*.details.*.warung_id' => 'required|exists:warung,id',
            'transaksi.*.details.*.jumlah' => 'required|integer|min:1',
        ], [
            // ... (Pesan Validasi)
            'transaksi.required' => 'Data transaksi wajib diisi.',
            'transaksi.*.barang_id.required' => 'Barang harus dipilih.',
            'transaksi.*.barang_id.exists' => 'Barang tidak valid.',
            'transaksi.*.details.required' => 'Detail pengiriman harus ada.',
            'transaksi.*.details.*.warung_id.required' => 'Warung tujuan wajib dipilih.',
            'transaksi.*.details.*.warung_id.exists' => 'Warung tujuan tidak valid.',
            'transaksi.*.details.*.jumlah.required' => 'Jumlah pengiriman wajib diisi.',
            'transaksi.*.details.*.jumlah.integer' => 'Jumlah pengiriman harus berupa angka.',
            'transaksi.*.details.*.jumlah.min' => 'Jumlah minimal adalah 1.',
        ]);

        $transaksiIdsDiproses = [];

        DB::transaction(function () use ($data, &$transaksiIdsDiproses) {

            // ⭐ Fetch Warung data with Area and Laba relations outside the loop (optimization)
            $warungIds = collect($data['transaksi'])->pluck('details.*.warung_id')->flatten()->unique()->toArray();
            $warungs = Warung::with('area.laba')->whereIn('id', $warungIds)->get()->keyBy('id');

            foreach ($data['transaksi'] as $transaksiId => $transaksiData) {
                $transaksiBarang = TransaksiBarang::with('areaPembelian')->findOrFail($transaksiId);
                $totalDikirim = 0;
                $hargaBeliPerUnit = $transaksiBarang->harga / $transaksiBarang->jumlah ?? 0;
                $markupPercent = optional($transaksiBarang->areaPembelian)->markup ?? 0;
                $hargaModalWarung = $hargaBeliPerUnit * (1 + ($markupPercent / 100)); // Ini adalah 'harga modal' untuk Warung

                foreach ($transaksiData['details'] as $detail) {
                    $warungId = $detail['warung_id'];
                    $jumlahKirim = $detail['jumlah'];

                    // Dapatkan data Warung
                    $warung = $warungs->get($warungId);
                    if (!$warung) continue; // Skip jika warung tidak ditemukan

                    $totalDikirim += $jumlahKirim;

                    // 1. Cari atau buat Stok Warung
                    $stokWarung = StokWarung::firstOrCreate(
                        [
                            'id_warung' => $warungId,
                            'id_barang' => $transaksiBarang->id_barang,
                        ],
                        ['jumlah' => 0]
                    );

                    // 2. Buat Barang Masuk
                    $barangMasuk = BarangMasuk::create([
                        'id_transaksi_barang' => $transaksiBarang->id,
                        'id_stok_warung' => $stokWarung->id,
                        'id_barang' => $transaksiBarang->id_barang,
                        'jumlah' => $jumlahKirim,
                        'status' => 'pending',
                    ]);

                    // 3. Logika Markup dan Hutang (sama seperti sebelumnya)
                    $hargaTotalJual = $hargaModalWarung * $jumlahKirim;

                    HutangBarangMasuk::create([
                        'id_warung' => $warungId,
                        'id_barang_masuk' => $barangMasuk->id,
                        'total' => $hargaTotalJual,
                        'jumlah_unit' => $jumlahKirim,
                        'status_pembayaran' => 'belum_lunas',
                    ]);

                    // ⭐ 4. LOGIKA TAMBAHAN: INSERT KE HARGAJUAL
                    $laba = Laba::where('id_area', optional($warung->area)->id)
                        ->where('input_minimal', '<=', $hargaModalWarung)
                        ->where('input_maksimal', '>=', $hargaModalWarung)
                        ->first();

                    // Ambil nilai harga_jual dari entri laba yang cocok
                    $hargaJualSatuan = optional($laba)->harga_jual ?? 0;

                    HargaJual::create([
                        'id_warung' => $warungId,
                        'id_barang' => $transaksiBarang->id_barang,
                        'harga_sebelum_markup' => $hargaBeliPerUnit,
                        'harga_modal' => round($hargaModalWarung),
                        // ⭐ Mengisi kedua range dengan harga jual yang ditemukan
                        'harga_jual_range_awal' => $hargaJualSatuan,
                        'harga_jual_range_akhir' => $hargaJualSatuan,
                        'periode_awal' => now(),
                        'periode_akhir' => null,
                    ]);
                }

                // 5. Perbarui status TransaksiBarang (Stok Sumber)
                $transaksiBarang->status = 'dikirim';
                $transaksiBarang->save();
                $transaksiIdsDiproses[] = $transaksiId;
            }
        });

        return redirect()->route('transaksibarang.index', ['status' => 'pending'])
            ->with('success', count($transaksiIdsDiproses) . ' item stok berhasil dikirim ke warung!');
    }

    // ... di dalam TransaksiBarangController Anda

    public function kirimRencanaProses(Request $request)
    {
        // ... (Filter dan Validasi tetap sama)
        $allData = $request->all();
        $rencanaFiltered = collect($allData['rencana'] ?? [])
            ->map(function ($warungRencana) {
                return collect($warungRencana)
                    ->filter(function ($item) {
                        return isset($item['rencana_id'], $item['jumlah_kirim'], $item['barang_id'], $item['transaksi_id'])
                            && $item['jumlah_kirim'] !== null
                            && $item['jumlah_kirim'] !== '';
                    })
                    ->toArray();
            })
            ->filter(fn($items) => !empty($items))
            ->toArray();

        $request->merge(['rencana' => $rencanaFiltered]);
        $data = $request->validate([
            'rencana' => 'required|array',
            'rencana.*.*.rencana_id' => 'required|exists:rencana_belanja,id',
            'rencana.*.*.jumlah_kirim' => 'required|integer|min:1',
            'rencana.*.*.barang_id' => 'required|exists:barang,id',
            'rencana.*.*.transaksi_id' => 'required|exists:transaksi_barang,id',
        ]);

        $rencanaIdsDiproses = [];
        $updateDataRencana = [];

        DB::transaction(function () use ($data, &$rencanaIdsDiproses, &$updateDataRencana) {

            // ⭐ Fetch Warung data with Area and Laba relations outside the loop (optimization)
            $warungIds = array_keys($data['rencana']);
            $warungs = Warung::with('area.laba')->whereIn('id', $warungIds)->get()->keyBy('id');

            // =========================================================
            // A. PROSES PENGIRIMAN, PENGURANGAN STOK SUMBER, DAN HUTANG
            // =========================================================
            foreach ($data['rencana'] as $warungId => $rencanaItems) {
                $warung = $warungs->get($warungId);
                if (!$warung) continue; // Skip jika warung tidak ditemukan

                foreach ($rencanaItems as $item) {
                    $rencanaId = $item['rencana_id'];
                    $jumlahKirim = $item['jumlah_kirim'];
                    $transaksiId = $item['transaksi_id'];

                    $transaksiBarang = TransaksiBarang::with('areaPembelian')->findOrFail($transaksiId);

                    if ($transaksiBarang->jumlah < $jumlahKirim) {
                        throw new \Exception("Stok sumber #{$transaksiId} tidak mencukupi untuk rencana #{$rencanaId}.");
                    }

                    $hargaBeliPerUnit = $transaksiBarang->harga / $transaksiBarang->jumlah ?? 0;
                    $markupPercent = optional($transaksiBarang->areaPembelian)->markup ?? 0;
                    $hargaModalWarung = $hargaBeliPerUnit * (1 + ($markupPercent / 100));
                    $hargaModalWarung = ceil($hargaModalWarung / 500) * 500;

                    // 1. Cari atau buat Stok Warung
                    $stokWarung = StokWarung::firstOrCreate(
                        ['id_warung' => $warungId, 'id_barang' => $transaksiBarang->id_barang],
                        ['jumlah' => 0]
                    );

                    // 2. Buat Barang Masuk
                    $barangMasuk = BarangMasuk::create([
                        'id_transaksi_barang' => $transaksiBarang->id,
                        'id_stok_warung' => $stokWarung->id,
                        'id_barang' => $transaksiBarang->id_barang,
                        'id_rencana_belanja' => $rencanaId,
                        'jumlah' => $jumlahKirim,
                        'status' => 'pending',
                    ]);

                    // 3. Catat Hutang Barang Masuk (Logika Markup)
                    $hargaTotalJual = $hargaModalWarung * $jumlahKirim;

                    HutangBarangMasuk::create([
                        'id_warung' => $warungId,
                        'id_barang_masuk' => $barangMasuk->id,
                        'total' => $hargaTotalJual,
                        'jumlah_unit' => $jumlahKirim,
                        'status_pembayaran' => 'belum_lunas',
                    ]);

                    // ⭐ 4. LOGIKA TAMBAHAN: INSERT KE HARGAJUAL
                    $laba = Laba::where('id_area', optional($warung->area)->id)
                        ->where('input_minimal', '<=', $hargaModalWarung)
                        ->where('input_maksimal', '>=', $hargaModalWarung)
                        ->first();

                    // Ambil nilai harga_jual dari entri laba yang cocok
                    $hargaJualSatuan = optional($laba)->harga_jual ?? 0;

                    HargaJual::create([
                        'id_warung' => $warungId,
                        'id_barang' => $transaksiBarang->id_barang,
                        'harga_sebelum_markup' => $hargaBeliPerUnit, // Harga dari TransaksiBarang
                        'harga_modal' => round($hargaModalWarung), // Harga Beli + Markup
                        'harga_jual_range_awal' => $hargaJualSatuan ?? 0,
                        'harga_jual_range_akhir' => $hargaJualSatuan ?? 0,
                        'periode_awal' => now(),
                        'periode_akhir' => null,
                    ]);

                    // 5. Perbarui TransaksiBarang (Stok Sumber)
                    $transaksiBarang->status = 'dikirim';
                    $transaksiBarang->save();

                    $rencanaIdsDiproses[] = $rencanaId;

                    // Siapkan data untuk update RencanaBelanja
                    if (!isset($updateDataRencana[$rencanaId])) {
                        $updateDataRencana[$rencanaId] = 0;
                    }
                    $updateDataRencana[$rencanaId] += $jumlahKirim;
                }
            }

            // =========================================================
            // B. UPDATE JUMLAH DIBELI DI RENCANABELANJA (SESUAI PERMINTAAN)
            // =========================================================
            foreach ($updateDataRencana as $rencanaId => $jumlahKirimTotal) {
                $rencana = RencanaBelanja::findOrFail($rencanaId);
                $rencana->update([
                    'jumlah_dibeli' => $rencana->jumlah_dibeli + $jumlahKirimTotal
                ]);
            }
        });

        return redirect()->route('transaksibarang.index', ['status' => 'pending'])
            ->with('success', count(array_unique($rencanaIdsDiproses)) . ' item rencana belanja berhasil dikirim dan diperbarui!');
    }




    // public function show(TransaksiBarang $transaksibarang)
    // {
    //     return view('admin.transaksibarang.show', compact('transaksibarang'));
    // }

    public function edit(TransaksiBarang $transaksibarang)
    {
        $transaksis = TransaksiKas::all();
        $barangs = Barang::all();
        return view('admin.transaksibarang.edit', compact('transaksibarang', 'transaksis', 'barangs'));
    }

    public function update(Request $request, TransaksiBarang $transaksibarang)
    {
        $request->validate([
            'id_transaksi_kas' => 'required|exists:transaksi_kas,id',
            'id_barang' => 'required|exists:barang,id',
            'jumlah' => 'required|integer|min:1',
            'status' => 'required|string',
            'jenis' => 'required|string|in:masuk,keluar',
            'keterangan' => 'nullable|string'
        ]);

        $transaksibarang->update($request->all());

        return redirect()->route('transaksibarang.index')->with('success', 'Transaksi barang berhasil diperbarui.');
    }

    public function destroy(TransaksiBarang $transaksibarang)
    {
        $transaksibarang->delete();
        return redirect()->route('transaksibarang.index')->with('success', 'Transaksi barang berhasil dihapus.');
    }
}

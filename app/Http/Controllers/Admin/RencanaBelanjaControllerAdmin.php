<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RencanaBelanja;
use App\Models\TransaksiBarangMasuk;
use App\Models\Barang;
use App\Models\TransaksiAwal;
use App\Models\StokWarung;
use App\Models\BarangMasuk;
use App\Models\HutangBarangMasuk;
use App\Models\HargaJual;
use App\Models\HutangWarung;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class RencanaBelanjaControllerAdmin extends Controller
{
    protected function getStockData()
    {
        $allTransactions = TransaksiBarangMasuk::with(['barang.satuan', 'areaPembelian'])
            ->where('jenis', 'rencana')
            ->where('status', 'pending')
            ->get()
            ->map(function ($trx) {
                // 1. Ambil semua satuan milik barang ini
                // Kita ambil nama_satuan dan jumlah (konversi)
                $listSatuan = [];
                if ($trx->barang && $trx->barang->satuan) {
                    $listSatuan = $trx->barang->satuan->map(function ($s) {
                        return [
                            'nama'   => $s->nama_satuan,
                            'jumlah' => (int) $s->jumlah,
                        ];
                    })
                        ->sortBy('jumlah') // Urutkan dari yang terkecil (biasanya pcs/isi 1)
                        ->values()
                        ->toArray();
                }

                // 2. Identifikasi satuan dasar (paling kecil) untuk label sisa stok
                $satuanDasar = !empty($listSatuan) ? $listSatuan[0]['nama'] : 'pcs';

                return [
                    'id'            => $trx->id,
                    'id_barang'     => $trx->id_barang,
                    'nama_barang'   => $trx->barang->nama_barang ?? '-',
                    'jumlah'        => (int) ($trx->jumlah - $trx->jumlah_terpakai), // Stok riil
                    'harga'         => $trx->harga,
                    'area'          => $trx->areaPembelian->area ?? '-',
                    'satuans'       => $listSatuan, // Semua daftar satuan untuk cek modulo di JS
                    'satuan_dasar'  => $satuanDasar, // Digunakan untuk teks sisa stok
                ];
            });

        return [
            'stockByBarang'     => $allTransactions->groupBy('id_barang'),
            'allTransactions'   => $allTransactions->values(),
            'warungs'           => \App\Models\Warung::all(),
            'areaPembelians'    => \App\Models\AreaPembelian::all()
        ];
    }

    public function index()
    {
        $data = $this->getStockData();
        dd($data);
        $rencanaCollection = RencanaBelanja::with(['barang', 'warung'])
            ->where('status', 'pending')
            ->get();

        // 1. Kelompokkan untuk tampilan Blade
        $rencanaBelanjaByWarung = $rencanaCollection->groupBy('id_warung');

        // 2. Buat mapping ID untuk Alpine.js
        // Hasilnya: [ '1' => [10, 11, 12], '2' => [15, 16] ]
        $rencanaMapping = $rencanaCollection->groupBy('id_warung')
            ->map(function ($items) {
                return $items->pluck('id')->toArray();
            });
        // dd($data['warungs']);
        return view('admin.rencanabelanja.index', [
            'rencanaBelanjaByWarung' => $rencanaBelanjaByWarung,
            'rencanaMapping'         => $rencanaMapping, // Tambahkan ini
            'allTransactionsForJs'   => $data['allTransactions'],
            'stockByBarang'          => $data['stockByBarang'],
            'warungs'                => $data['warungs'],
        ]);
    }


    /**
     * Memproses pengiriman rencana belanja ke warung.
     * Mengalokasikan stok sumber dan memperbarui RencanaBelanja.
     * Route: POST admin.transaksibarang.kirim.rencana.proses
     */


    public function createRencana()
    {
        // dd('create rencana');
        $rencanaBelanjas = RencanaBelanja::with(['barang', 'warung'])
            ->where('status', 'pending')
            ->get();

        $totalKebutuhan = $rencanaBelanjas
            ->groupBy('id_barang')
            ->map(function ($groupedItems, $id_barang) {

                $detailKebutuhanWarung = [];
                $totalQty = 0;
                $allRencanaIds = [];

                foreach ($groupedItems as $rencana) {
                    $kebutuhan = $rencana->jumlah_awal - $rencana->jumlah_dibeli;

                    if ($kebutuhan > 0) {
                        $totalQty += $kebutuhan;
                        $allRencanaIds[] = $rencana->id;

                        $detailKebutuhanWarung[] = [
                            'warung'    => $rencana->warung->nama_warung ?? '-',
                            'kebutuhan' => $kebutuhan,
                        ];
                    }
                }

                if ($totalQty === 0) return null;

                $barang = Barang::find($id_barang);

                // 🔹 Ambil harga beli awal
                $hargaAwal = HargaJual::where('id_barang', $id_barang)
                    ->latest('id')
                    ->value('harga_sebelum_markup') ?? 0;

                return [
                    'id_barang'        => $id_barang,
                    'nama_barang'      => $barang->nama_barang,
                    'total_kebutuhan'  => $totalQty,
                    'rencana_ids'      => array_unique($allRencanaIds),
                    'detail_warung'    => $detailKebutuhanWarung,
                    'valid_areas'      => $barang->areaPembelian()->get(['area_pembelian.id', 'area_pembelian.area']),
                    'count_areas'      => $barang->areaPembelian()->count(),
                    'harga_awal'       => $hargaAwal,
                    'estimasi_total'   => $hargaAwal * $totalQty,
                ];
            })
            ->filter()
            ->values()
            ->sort(function ($a, $b) {
                if ($a['count_areas'] == 0 && $b['count_areas'] > 0) return 1;
                if ($b['count_areas'] == 0 && $a['count_areas'] > 0) return -1;
                if ($a['count_areas'] != $b['count_areas']) return $a['count_areas'] <=> $b['count_areas'];
                return strcmp($a['nama_barang'], $b['nama_barang']);
            });

        return view('admin.rencanabelanja.pembelian', compact('totalKebutuhan'));
    }


    public function createByArea()
    {
        // dd('asu');
        $rencanaBelanjas = RencanaBelanja::with(['barang.areaPembelian', 'warung'])
            ->whereColumn('jumlah_dibeli', '<', 'jumlah_awal')
            ->get();

        $dataPerArea = [];

        foreach ($rencanaBelanjas as $rencana) {
            $kebutuhan = $rencana->jumlah_awal - $rencana->jumlah_dibeli;
            if ($kebutuhan <= 0) continue;

            $barang = $rencana->barang;
            $areas = $barang->areaPembelian;

            // 🔹 Ambil harga beli awal (referensi)
            $hargaAwal = DB::table('harga_jual')
                ->where('id_barang', $barang->id)
                ->latest('id')
                ->value('harga_sebelum_markup') ?? 0;

            if ($areas->isEmpty()) {
                $this->pushToAreaData($dataPerArea, 'Tanpa Area', 0, $barang, $rencana, $kebutuhan, $hargaAwal);
            } else {
                foreach ($areas as $area) {
                    $this->pushToAreaData($dataPerArea, $area->area, $area->id, $barang, $rencana, $kebutuhan, $hargaAwal);
                }
            }
        }

        $totalKebutuhan = collect($dataPerArea)->sortBy(fn($item, $key) => $key);

        return view('admin.rencanabelanja.pembelianPerArea', compact('totalKebutuhan'));
    }

    private function pushToAreaData(&$data, $areaName, $areaId, $barang, $rencana, $qty, $hargaAwal)
    {
        $barangId = $barang->id;

        if (!isset($data[$areaName])) {
            $data[$areaName] = [
                'area_id' => $areaId, // 0 untuk Tanpa Area
                'items' => []
            ];
        }

        if (!isset($data[$areaName]['items'][$barangId])) {
            $data[$areaName]['items'][$barangId] = [
                'id_barang'       => $barangId,
                'nama_barang'     => $barang->nama_barang,
                'total_kebutuhan' => 0,
                'rencana_ids'     => [],
                'detail_warung'   => [],
                'harga_awal'      => $hargaAwal,
            ];
        }

        $data[$areaName]['items'][$barangId]['total_kebutuhan'] += $qty;
        $data[$areaName]['items'][$barangId]['rencana_ids'][] = $rencana->id;
        $data[$areaName]['items'][$barangId]['detail_warung'][] = [
            'warung' => $rencana->warung->nama_warung ?? 'N/A',
            'kebutuhan' => $qty
        ];
    }

    public function store(Request $request)
    {
        // 1. Ambil data asli & Bersihkan (Sama seperti sebelumnya)
        $items = $request->input('items', []);
        foreach ($items as $i => $item) {
            if (isset($item['skip']) && $item['skip'] == "1") {
                unset($items[$i]['purchases']);
            } else if (isset($item['purchases'])) {
                foreach ($item['purchases'] as $j => $purchase) {
                    if (isset($purchase['area_pembelian_id']) && $purchase['area_pembelian_id'] !== "") {
                        $items[$i]['purchases'][$j]['area_pembelian_id'] = (int) $purchase['area_pembelian_id'];
                    }
                }
            }
        }
        $request->merge(['items' => $items]);

        // 2. Validasi
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id_barang'   => 'required|integer|exists:barang,id',
            'items.*.rencana_ids' => 'required|string',
            'items.*.skip'        => 'nullable|boolean',
            'items.*.purchases'   => 'required_without:items.*.skip|array',
            'items.*.purchases.*.area_pembelian_id' => 'required_without:items.*.skip|integer|exists:area_pembelian,id',
            'items.*.purchases.*.jumlah_beli'       => 'required_without:items.*.skip|numeric|min:1',
            'items.*.purchases.*.harga'             => 'required_without:items.*.skip|numeric|min:0',
            'items.*.purchases.*.tanggal_kadaluarsa' => 'nullable|date',
        ]);

        $grandTotal = 0;
        DB::beginTransaction();
        try {
            $transaksi = TransaksiAwal::create([
                'tanggal' => now(),
                'keterangan' => "Pembelian Berdasarkan Rencana Belanja Warung",
                'total' => 0
            ]);

            foreach ($request->items as $itemData) {
                if (!empty($itemData['skip']) && $itemData['skip'] == 1) {
                    continue;
                }

                $idBarang = $itemData['id_barang'];

                // --- BAGIAN INPUT DATA KE STOK (TransaksiBarangMasuk) ---
                foreach ($itemData['purchases'] ?? [] as $purchase) {
                    $jumlahBeli = (int) $purchase['jumlah_beli'];
                    $hargaUnit  = (float) $purchase['harga'];
                    $totalHargaBaris = $jumlahBeli * $hargaUnit;

                    // Ini adalah perintah yang memasukkan data belanja menjadi stok riil
                    TransaksiBarangMasuk::create([
                        'id_transaksi_awal' => $transaksi->id,
                        'id_area_pembelian' => $purchase['area_pembelian_id'],
                        'id_barang'         => $idBarang,
                        'jumlah'            => $jumlahBeli,
                        'jumlah_terpakai'   => 0, // Awalnya 0 karena baru dibeli
                        'harga'             => $hargaUnit,
                        'tanggal_kadaluarsa' => $purchase['tanggal_kadaluarsa'] ?? null,
                        'jenis'             => 'rencana',
                    ]);

                    $grandTotal += $totalHargaBaris;
                }
                // -------------------------------------------------------
            }

            $transaksi->update(['total' => $grandTotal]);
            DB::table('dana_utama')->where('jenis_dana', 'wrb_old')->decrement('saldo', $grandTotal);

            DB::commit();
            return redirect()->route('admin.rencana.index')->with('success', 'Transaksi berhasil diproses.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return back()->withErrors(['process_error' => "Gagal: " . $e->getMessage()]);
        }
    }

    public function kirimRencanaProses(Request $request)
    {
        // 1. Filtering dan Sanitasi Input
        $allData = $request->all();
        // dd('Kirim Rencana Proses', $allData);
        $itemsFiltered = collect($allData['items'] ?? [])
            ->filter(function ($item) {
                return !empty($item['transactions']);
            })
            ->toArray();
        $request->merge(['items' => $itemsFiltered]);

        // 2. Validasi Input
        $data = $request->validate([
            'items' => 'required|array',
            'items.*' => ['array', function ($attribute, $value, $fail) {
                $rencanaId = explode('.', $attribute)[1];
                if (!RencanaBelanja::where('id', $rencanaId)->exists()) {
                    $fail("Rencana Belanja dengan ID {$rencanaId} tidak valid.");
                }
            }],
            'items.*.transactions' => 'required|array',
            'items.*.transactions.*.id_transaksi_barang_masuk' => 'required|exists:transaksi_barang_masuk,id',
            'items.*.transactions.*.jumlah' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($data) {
            $rencanaIds = array_keys($data['items']);
            $rencanas = RencanaBelanja::with(['barang', 'warung.area.laba'])
                ->whereIn('id', $rencanaIds)
                ->get()
                ->keyBy('id');

            $trxIds = collect($data['items'])->pluck('transactions.*.id_transaksi_barang_masuk')->flatten()->unique()->toArray();
            $trxSources = TransaksiBarangMasuk::with('areaPembelian')->whereIn('id', $trxIds)->get()->keyBy('id');

            // Menampung induk hutang per warung agar tidak duplikat dalam satu request
            $rekapHutangWarung = [];

            foreach ($data['items'] as $rencanaId => $rencanaData) {
                $rencana = $rencanas->get($rencanaId);
                $warung = $rencana->warung;
                $warungId = $rencana->id_warung;
                $barangId = $rencana->id_barang;

                $totalKirimItemRencana = 0;

                foreach ($rencanaData['transactions'] as $trxDetail) {
                    $trxSourceId = $trxDetail['id_transaksi_barang_masuk'];
                    $jumlahKirim = (int) $trxDetail['jumlah'];
                    $trxBarang = $trxSources->get($trxSourceId);

                    // Hitung harga modal & total
                    $hargaBeliPerUnit = $trxBarang->jumlah > 0 ? $trxBarang->harga / $trxBarang->jumlah : 0;
                    $markupPercent = optional($trxBarang->areaPembelian)->markup ?? 0;
                    $hargaModalWarung = $hargaBeliPerUnit * (1 + ($markupPercent / 100));
                    $totalHargaBarang = round($hargaModalWarung * $jumlahKirim);

                    $totalKirimItemRencana += $jumlahKirim;

                    // 1. Stok Warung
                    $stokWarung = StokWarung::firstOrCreate(
                        ['id_warung' => $warungId, 'id_barang' => $barangId],
                        ['jumlah' => 0]
                    );

                    // 2. Buat Barang Masuk (Skema Baru: ada 'total')
                    $barangMasuk = BarangMasuk::create([
                        'id_transaksi_barang_masuk' => $trxSourceId,
                        'id_stok_warung'      => $stokWarung->id,
                        // 'id_barang'           => $barangId,
                        'jumlah'              => $jumlahKirim,
                        'total'               => $totalHargaBarang, // Perbaikan: isi total
                        'status'              => 'kirim',
                        'jenis'               => 'rencana',
                        'tanggal_kadaluarsa'  => $trxBarang->tanggal_kadaluarsa,
                    ]);

                    // 3. Kelola Hutang Warung (Induk/Master)
                    if (!isset($rekapHutangWarung[$warungId])) {
                        $rekapHutangWarung[$warungId] = HutangWarung::create([
                            'id_warung' => $warungId,
                            'total'  => 0,
                            'jenis'  => 'barang masuk',
                            'status' => 'belum lunas'
                        ]);
                    }

                    $hutangInduk = $rekapHutangWarung[$warungId];
                    $hutangInduk->increment('total', $totalHargaBarang);
                    $warung->increment('hutang', $totalHargaBarang);


                    // 4. Buat Hutang Barang Masuk (Detail refer ke Induk)
                    HutangBarangMasuk::create([
                        'id_hutang_warung' => $hutangInduk->id, // Perbaikan: simpan ID induk
                        'id_warung'        => $warungId,
                        'id_barang_masuk'  => $barangMasuk->id,
                        'total'            => $totalHargaBarang,
                        'status'           => 'belum lunas',
                    ]);

                    // 5. Update Harga Jual (TUTUP PERIODE LAMA, BUAT BARU)
                    $laba = optional($warung->area)->laba()
                        ->where('input_minimal', '<=', $hargaModalWarung)
                        ->where('input_maksimal', '>=', $hargaModalWarung)
                        ->first();

                    // 5.a Tutup harga jual aktif sebelumnya (jika ada)

                    HargaJual::where('id_warung', $warungId)
                        ->where('id_barang', $barangId)
                        ->whereNull('periode_akhir')
                        ->orderByDesc('id')   // data TERAKHIR di-input
                        ->limit(1)
                        ->update([
                            'periode_akhir' => now()
                        ]);



                    // 5.b Buat harga jual baru
                    HargaJual::create([
                        'id_warung'              => $warungId,
                        'id_barang'              => $barangId,
                        'harga_sebelum_markup'   => round($hargaBeliPerUnit),
                        'harga_modal'            => round($hargaModalWarung),
                        'harga_jual_range_awal'  => optional($laba)->harga_jual ?? 0,
                        'harga_jual_range_akhir' => optional($laba)->harga_jual ?? 0,
                        'periode_awal'           => now(),
                        'periode_akhir'          => null,
                        'barang_terjual'         => 0,
                        'total_barang'           => $jumlahKirim,
                    ]);


                    // 6. Update TransaksiBarang sumber (jumlah_terpakai)
                    $trxBarang->increment('jumlah_terpakai', $jumlahKirim);
                }

                // 7. Update Status Rencana Belanja
                $rencana->update([
                    'status' => 'dikirim',
                    'jumlah_dibeli' => DB::raw("jumlah_dibeli + {$totalKirimItemRencana}")
                ]);
            }

            // 8. LOGIKA SPLITTING STOK SISA (TransaksiBarang)
            foreach ($trxSources as $transaksiBarang) {
                $transaksiBarang->refresh(); // Ambil data terbaru setelah increment
                $sisaStok = $transaksiBarang->jumlah - $transaksiBarang->jumlah_terpakai;

                if ($sisaStok > 0) {
                    $hargaBeliPerUnit = $transaksiBarang->harga / $transaksiBarang->jumlah;

                    $dataSisa = $transaksiBarang->toArray();
                    unset($dataSisa['id']);

                    $dataSisa['jumlah'] = $sisaStok;
                    $dataSisa['jumlah_terpakai'] = 0;
                    $dataSisa['harga'] = round($hargaBeliPerUnit * $sisaStok);
                    $dataSisa['status'] = 'pending';

                    TransaksiBarangMasuk::create($dataSisa);
                }

                $transaksiBarang->update(['status' => 'dikirim']);
            }
        });

        return redirect()->route('admin.rencana.index')
            ->with('success', 'Rencana belanja berhasil dikirim dan hutang telah dicatat.');
    }
}

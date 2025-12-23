<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RencanaBelanja;
use App\Models\TransaksiBarang;
use App\Models\Warung;
use App\Models\Barang;
use App\Models\AreaPembelian;
use App\Models\TransaksiAwal;
use App\Models\StokWarung;
use App\Models\BarangMasuk;
use App\Models\HutangBarangMasuk;
use App\Models\HargaJual;
use App\Models\Area;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class RencanaBelanjaControllerAdmin extends Controller
{
    protected function getStockData()
    {
        $allTransactions = TransaksiBarang::with(['barang', 'areaPembelian'])->where('jenis', 'rencana')
            ->whereColumn('jumlah', '>', 'jumlah_terpakai')
            ->get()
            ->map(function ($trx) {
                return [
                    'id'        => $trx->id,
                    'id_barang' => $trx->id_barang,
                    'nama_barang' => $trx->barang->nama_barang ?? '-',
                    'jumlah'    => $trx->jumlah - $trx->jumlah_terpakai, // stok real
                    'harga'     => $trx->harga,
                    'area'      => $trx->areaPembelian->area ?? '-',
                ];
            });

        return [
            'stockByBarang'     => $allTransactions->groupBy('id_barang'),
            'allTransactions'   => $allTransactions->values(),   // penting
            'warungs'           => Warung::all(),
            'areaPembelians'    => AreaPembelian::all()
        ];
    }

    public function index()
    {
        $data = $this->getStockData();
        $rencana = RencanaBelanja::with(['barang', 'warung'])
            ->where('status', 'pending')
            ->get()
            ->groupBy('id_warung');
        // dd($rencana);
        return view('admin.rencanabelanja.index', [
            'rencanaBelanjaByWarung' => $rencana,
            'allTransactionsForJs'   => $data['allTransactions'], // dipakai JS
            'stockByBarang'          => $data['stockByBarang'],
            'warungs' => $data['warungs'],
        ]);
    }


    /**
     * Memproses pengiriman rencana belanja ke warung.
     * Mengalokasikan stok sumber dan memperbarui RencanaBelanja.
     * Route: POST admin.transaksibarang.kirim.rencana.proses
     */


    public function create()
    {
        $rencanaBelanjas = RencanaBelanja::with(['barang', 'warung'])
            ->whereColumn('jumlah_dibeli', '<', 'jumlah_awal')
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
                'area_id' => $areaId,
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
                'harga_awal'      => $hargaAwal, // Simpan harga referensi
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
        // 1. Ambil data asli
        // dd($request->all());
        $items = $request->input('items', []);

        // 2. Bersihkan & Konversi Data
        foreach ($items as $i => $item) {
            // Jika item di-skip, hapus data purchases agar tidak divalidasi
            if (isset($item['skip']) && $item['skip'] == "1") {
                unset($items[$i]['purchases']);
            } else if (isset($item['purchases'])) {
                foreach ($item['purchases'] as $j => $purchase) {
                    // Pastikan ID area adalah integer atau NULL jika kosong
                    if (isset($purchase['area_pembelian_id']) && $purchase['area_pembelian_id'] !== "") {
                        $items[$i]['purchases'][$j]['area_pembelian_id'] = (int) $purchase['area_pembelian_id'];
                    }
                }
            }
        }

        // 3. Timpa request dengan data yang sudah bersih
        $request->merge(['items' => $items]);

        // 4. Validasi (Gunakan 'numeric' untuk ID jika masih bandel, tapi 'integer' seharusnya bisa setelah merge)
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id_barang'   => 'required|integer|exists:barang,id',
            'items.*.rencana_ids' => 'required|string',
            'items.*.skip'        => 'nullable|boolean',

            // Validasi bersyarat: Jika 'skip' tidak ada, maka 'purchases' wajib
            'items.*.purchases' => 'required_without:items.*.skip|array',
            'items.*.purchases.*.area_pembelian_id' => 'required_without:items.*.skip|integer|exists:area_pembelian,id',
            'items.*.purchases.*.jumlah_beli'       => 'required_without:items.*.skip|numeric|min:1',
            'items.*.purchases.*.harga'             => 'required_without:items.*.skip|numeric|min:0',
            'items.*.purchases.*.tanggal_kadaluarsa' => 'nullable|date',
        ]);
        dd($validated);


        $grandTotal = 0;
        $keteranganTransaksi = "Pembelian Berdasarkan Rencana Belanja Warung";
        $rencanaUpdates = [];

        DB::beginTransaction();
        try {

            $transaksi = TransaksiAwal::create([
                'tanggal' => now(),
                'keterangan' => $keteranganTransaksi,
                'total' => 0
            ]);

            foreach ($request->items as $itemData) {

                /** SKIP barang — lewati total proses */
                if (!empty($itemData['skip']) && $itemData['skip'] == 1) {
                    continue;
                }

                $idBarang = $itemData['id_barang'];
                $rencanaIds = array_map('intval', explode(',', $itemData['rencana_ids']));
                $totalDibeliPerGroup = 0;

                /** Purchases bisa kosong → gunakan null-safe */
                foreach ($itemData['purchases'] ?? [] as $purchase) {

                    $jumlahBeli = (int) $purchase['jumlah_beli'];
                    $hargaUnit  = (float) $purchase['harga'];
                    $totalHargaBaris = $jumlahBeli * $hargaUnit;

                    TransaksiBarang::create([
                        'id_transaksi_awal' => $transaksi->id,
                        'id_area_pembelian' => $purchase['area_pembelian_id'],
                        'id_barang'         => $idBarang,
                        'jumlah'            => $jumlahBeli,
                        'jumlah_terpakai'    => 0,
                        'harga'             => $hargaUnit,
                        'tanggal_kadaluarsa' => $purchase['tanggal_kadaluarsa'] ?? null,
                        'jenis'             => 'rencana',
                    ]);

                    $grandTotal += $totalHargaBaris;
                    $totalDibeliPerGroup += $jumlahBeli;
                }

                /** update semua rencana yang terkait */
                foreach ($rencanaIds as $rencanaId) {
                    $rencanaUpdates[$rencanaId] = ($rencanaUpdates[$rencanaId] ?? 0) + $totalDibeliPerGroup;
                }
            }


            dd($rencanaUpdates);
            $transaksi->update(['total' => $grandTotal]);

            DB::table('dana_utama')->where('jenis_dana', 'wrb_old')->decrement('saldo', $grandTotal);

            DB::commit();
            return redirect()->route('admin.rencana.index')->with('success', 'Transaksi rencana berhasil diproses.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error($e);
            return back()->withErrors(['process_error' => "Gagal memproses transaksi. Error: " . $e->getMessage()]);
        }
    }

    public function kirimRencanaProses(Request $request)
    {
        // 1. Filtering dan Sanitasi Input Awal (TIDAK BERUBAH)
        $allData = $request->all();
        // dd($allData);
        $itemsFiltered = collect($allData['items'] ?? [])
            ->filter(function ($item) {
                return !empty($item['transactions']);
            })
            ->toArray();
        $request->merge(['items' => $itemsFiltered]);
        // dd($allData, $itemsFiltered);
        // 2. Validasi Input (TIDAK BERUBAH)
        try {
            $data = $request->validate([
                'items' => 'required|array',
                'items.*' => ['array', function ($attribute, $value, $fail) {
                    $rencanaId = explode('.', $attribute)[1];
                    if (!RencanaBelanja::where('id', $rencanaId)->exists()) {
                        $fail("Rencana Belanja dengan ID {$rencanaId} tidak valid atau tidak ditemukan.");
                    }
                }],
                'items.*.transactions' => 'required|array',
                'items.*.transactions.*.id_transaksi_barang' => 'required|exists:transaksi_barang,id',
                'items.*.transactions.*.jumlah' => 'required|integer|min:1',
            ], [
                // ... Pesan Validasi
                'items.required' => 'Tidak ada Rencana Belanja yang dipilih.',
                'items.*.transactions.required' => 'Setiap rencana harus memiliki minimal satu sumber stok.',
                'items.*.transactions.*.id_transaksi_barang.required' => 'Sumber Transaksi (TRX) wajib dipilih.',
                'items.*.transactions.*.id_transaksi_barang.exists' => 'Sumber Transaksi (TRX) tidak valid.',
                'items.*.transactions.*.jumlah.required' => 'Jumlah kirim wajib diisi.',
                'items.*.transactions.*.jumlah.min' => 'Jumlah minimal kirim adalah 1.',
            ]);
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        $rencanaIdsDiproses = [];
        $transaksiIdsDiproses = [];

        // Gunakan array untuk menyimpan ID TransaksiBarang Sumber yang perlu dicek stok sisanya
        $trxSourcesToUpdate = [];

        DB::transaction(function () use ($data, &$rencanaIdsDiproses, &$transaksiIdsDiproses, &$trxSourcesToUpdate) {

            $rencanaIds = array_keys($data['items']);
            $rencanas = RencanaBelanja::with(['barang', 'warung', 'warung.area.laba'])
                ->whereIn('id', $rencanaIds)
                ->get()
                ->keyBy('id');

            $trxIds = collect($data['items'])->pluck('transactions.*.id_transaksi_barang')->flatten()->unique()->toArray();
            $trxSources = TransaksiBarang::with('areaPembelian')->whereIn('id', $trxIds)->get()->keyBy('id');

            // Total alokasi per TransaksiBarang sumber (untuk cek stok)
            $totalAlokasiPerTrx = [];
            foreach ($data['items'] as $rencanaData) {
                foreach ($rencanaData['transactions'] as $trxDetail) {
                    $trxSourceId = $trxDetail['id_transaksi_barang'];
                    $jumlahKirim = (int) $trxDetail['jumlah'];
                    $totalAlokasiPerTrx[$trxSourceId] = ($totalAlokasiPerTrx[$trxSourceId] ?? 0) + $jumlahKirim;
                }
            }

            // Cek ketersediaan stok aktual di TransaksiBarang (TIDAK BERUBAH)
            foreach ($totalAlokasiPerTrx as $trxSourceId => $totalKirim) {
                $trxBarang = $trxSources->get($trxSourceId);
                $stokTersedia = $trxBarang->jumlah - $trxBarang->jumlah_terpakai;

                if ($totalKirim > $stokTersedia) {
                    throw ValidationException::withMessages([
                        "items.{$trxSourceId}" => "Total pengiriman ({$totalKirim}) untuk barang {$trxBarang->barang->nama_barang} (TRX #{$trxSourceId}) melebihi stok yang tersedia ({$stokTersedia})."
                    ]);
                }
            }

            // dd($data, $rencanas, $trxSources);
            // --- PROSES KIRIM PER RENCANA BELANJA ---
            foreach ($data['items'] as $rencanaId => $rencanaData) {
                $rencana = $rencanas->get($rencanaId);

                $warung = $rencana->warung;
                $barangId = $rencana->id_barang;
                $warungId = $rencana->id_warung;

                $totalKirimItemRencana = 0;

                foreach ($rencanaData['transactions'] as $trxDetail) {
                    $trxSourceId = $trxDetail['id_transaksi_barang'];
                    $jumlahKirim = (int) $trxDetail['jumlah'];

                    $trxBarang = $trxSources->get($trxSourceId);

                    // Hitung harga modal per unit
                    $hargaBeliPerUnit = $trxBarang->jumlah > 0 ? $trxBarang->harga / $trxBarang->jumlah : 0;
                    $markupPercent = optional($trxBarang->areaPembelian)->markup ?? 0;
                    $hargaModalWarung = $hargaBeliPerUnit * (1 + ($markupPercent / 100));

                    $totalKirimItemRencana += $jumlahKirim;

                    // --- LOGIKA UTAMA PENGIRIMAN (SAMA) ---

                    // 1. Cari atau buat Stok Warung
                    $stokWarung = StokWarung::firstOrCreate(
                        [
                            'id_warung' => $warungId,
                            'id_barang' => $barangId,
                        ],
                        ['jumlah' => 0]
                    );
                    // dd($stokWarung);
                    // 2. Buat Barang Masuk (Status 'kirim', Jenis 'tambahan')
                    $barangMasuk = BarangMasuk::create([
                        'id_transaksi_barang' => $trxSourceId, // ID TransaksiBarang sumber
                        'id_stok_warung' => $stokWarung->id,
                        'id_barang' => $barangId,
                        'jumlah' => $jumlahKirim,
                        'status' => 'kirim',
                        'jenis' => 'tambahan', // Jenis wajib 'tambahan'
                        'tanggal_kadaluarsa' => $trxBarang->tanggal_kadaluarsa,
                    ]);

                    // 3. Catat Hutang
                    $hargaTotalHutang = $hargaModalWarung * $jumlahKirim;
                    HutangBarangMasuk::create([
                        'id_warung' => $warungId,
                        'id_barang_masuk' => $barangMasuk->id,
                        'total' => round($hargaTotalHutang),
                        'jumlah_unit' => $jumlahKirim,
                        'status_pembayaran' => 'belum lunas',
                        'tanggal_hutang' => now(),
                    ]);

                    // 4. Logika INSERT KE HARGAJUAL (SAMA)
                    $laba = optional($warung->area)->laba()
                        ->where('input_minimal', '<=', $hargaModalWarung)
                        ->where('input_maksimal', '>=', $hargaModalWarung)
                        ->first();

                    $hargaJualSatuan = optional($laba)->harga_jual ?? 0;

                    HargaJual::create([
                        'id_warung' => $warungId,
                        'id_barang' => $barangId,
                        'harga_sebelum_markup' => $hargaBeliPerUnit,
                        'harga_modal' => round($hargaModalWarung),
                        'harga_jual_range_awal' => $hargaJualSatuan,
                        'harga_jual_range_akhir' => $hargaJualSatuan,
                        'periode_awal' => now(),
                        'periode_akhir' => null,
                    ]);

                    // 5. Update TransaksiBarang sumber (jumlah_terpakai)
                    // Kita akan kumpulkan ID TRX Sumber yang terpakai
                    $trxSource = TransaksiBarang::find($trxSourceId);
                    $trxSource->jumlah_terpakai += $jumlahKirim;
                    $trxSource->save();

                    // Tambahkan ID TRX ke array untuk diproses splitting nanti
                    $trxSourcesToUpdate[$trxSourceId] = $trxSource;
                }

                // 6. Update RencanaBelanja (status & jumlah_kirim)
                RencanaBelanja::where('id_warung', $warungId)
                    ->where('id_barang', $barangId)
                    ->where('status', 'pending')
                    ->update(['status' => 'dikirim', 'jumlah_dibeli' => DB::raw("jumlah_dibeli + {$totalKirimItemRencana}")]);

                $rencanaIdsDiproses[] = $rencanaId . " (Kirim: {$totalKirimItemRencana})";
            }

            // --- LOGIKA PEMBAGIAN STOK SISA (SPLITTING) ---

            // Loop melalui setiap TransaksiBarang sumber yang telah digunakan
            foreach (array_unique(array_keys($trxSourcesToUpdate)) as $trxId) {
                $transaksiBarang = $trxSourcesToUpdate[$trxId];

                $sisaStok = $transaksiBarang->jumlah - $transaksiBarang->jumlah_terpakai;

                if ($sisaStok > 0) {
                    // Jika ada sisa, buat TransaksiBarang baru untuk sisa stok

                    // Hitung harga per unit (diambil dari proses di atas, harus dihitung ulang jika loop di luar)
                    $hargaBeliPerUnit = $transaksiBarang->jumlah > 0 ? $transaksiBarang->harga / $transaksiBarang->jumlah : 0;
                    $hargaSisa = $hargaBeliPerUnit * $sisaStok;

                    $dataSisa = $transaksiBarang->toArray();

                    // Hapus ID lama agar Laravel tahu ini data baru
                    unset($dataSisa['id']);

                    // Update kolom yang relevan untuk data sisa
                    $dataSisa['jumlah'] = $sisaStok;
                    $dataSisa['jumlah_terpakai'] = 0; // Sisa stok belum terpakai
                    $dataSisa['harga'] = round($hargaSisa); // Harga total untuk sisa stok
                    $dataSisa['status'] = 'pending'; // Status kembali pending

                    // Buat entri TransaksiBarang baru untuk sisa stok
                    $newTransaksiBarang = TransaksiBarang::create($dataSisa);

                    // Catat transaksi lama sebagai 'parsial' (atau 'selesai')
                    $transaksiBarang->status = 'parsial';
                    $transaksiBarang->save();

                    $transaksiIdsDiproses[] = $trxId . " (parsial, sisa stok dibuat dengan ID: {$newTransaksiBarang->id})";
                } else {
                    // Jika stok habis, ubah status menjadi 'dikirim' (selesai)
                    $transaksiBarang->status = 'dikirim';
                    $transaksiBarang->save();
                    $transaksiIdsDiproses[] = $trxId . " (dikirim penuh)";
                }
            }
            // dd($data, 'asu');
        });

        // 3. Redirect dan Notifikasi
        $totalRencanaDiproses = count($data['items'] ?? []);
        $successMessage = $totalRencanaDiproses . ' Rencana Belanja berhasil didistribusikan. ';

        // Tambahkan notifikasi khusus untuk pengiriman parsial
        if (collect($transaksiIdsDiproses)->contains(fn($id) => str_contains($id, 'parsial'))) {
            $successMessage .= 'Beberapa sumber stok diproses parsial dan sisa stok telah dibuat sebagai entri baru.';
        } else {
            $successMessage .= 'Semua sumber stok dikirim penuh.';
        }

        return redirect()->route('admin.rencana.index') // Redirect kembali ke halaman rencana
            ->with('success', $successMessage);
    }
}

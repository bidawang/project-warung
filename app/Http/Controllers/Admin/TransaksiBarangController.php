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
use Illuminate\Validation\ValidationException;
use App\Models\HargaJual; // ⭐ Model baru

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransaksiBarangController extends Controller
{

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

    public function index(Request $request)
    {
        // dd(123);
        $status = $request->query('status', 'pending');
        $query = TransaksiBarang::with(['transaksiKas', 'barang'])->where('jenis', 'tambahan');

        // Logika Filter Status (Sama seperti sebelumnya)
        if ($status === 'pending') {
            // Tampilkan semua stok yang masih memiliki jumlah > 0
            $query->where('status','pending') ;
        } elseif ($status === 'kirim') {
            $query->where('status', 'dikirim'); ;
        } elseif ($status === 'terima') {
            $query->where('status', 'terima'); ;
        } elseif ($status === 'tolak') {
            $query->where('status', 'tolak'); ;
        }

        $transaksibarangs = $query->paginate(10)->appends(['status' => $status]);
// dd($transaksibarangs);
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

        return redirect()->route('admin.transaksibarang.index', ['status' => 'pending'])
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
        // dd($request->all()); // Hapus dd() setelah pengujian

        // 1. Validasi Input (Tambahkan validasi sesuai kebutuhan)
        // dd($request->all());
        $request->validate([
            // Tambahkan validasi untuk id_area, id_barang, jumlah, total_harga, dll.
            'id_area.*' => 'nullable|integer|exists:area_pembelian,id',
            'id_barang.*.*' => 'nullable|integer|exists:barang,id',
            'jumlah.*.*' => 'nullable|integer|min:0',
            'total_harga.*.*' => 'nullable|numeric|min:0',
            'tanggal_kadaluarsa.*.*' => 'nullable|date',
            'keterangan.*' => 'nullable|string',
            'lain_harga.*' => 'nullable|numeric|min:0',
        ]);
        $grandTotal = 0;
        
        DB::beginTransaction();
        try {
            // 2. Simpan transaksi awal
            $transaksi = TransaksiAwal::create([
                'tanggal' => now(),
                'keterangan' => $request->keterangan,
                'total' => 0, // akan dihitung di bawah
            ]);

            // 3. Loop area pembelian (Transaksi Barang)
            if ($request->id_area) {
                foreach ($request->id_area as $areaIndex => $areaId) {
                    if (isset($request->id_barang[$areaIndex])) {
                        foreach ($request->id_barang[$areaIndex] as $i => $barangId) {
                            $jumlah = $request->jumlah[$areaIndex][$i] ?? 0;
                            // Asumsi $harga di sini adalah total harga per baris (jumlah * harga_satuan)
                            $hargaTotalBaris = $request->total_harga[$areaIndex][$i] ?? 0;
                            $tanggalKadaluarsa = $request->tanggal_kadaluarsa[$areaIndex][$i] ?? null;

                            // Lewati jika data tidak lengkap atau nol
                            if (!$barangId || ($jumlah == 0 && $hargaTotalBaris == 0)) {
                                continue;
                            }

                            // Kita asumsikan kolom 'harga' di TransaksiBarang menyimpan harga total baris ini
                            // Jika 'harga' menyimpan harga satuan, logika ini harus disesuaikan.
                            TransaksiBarang::create([
                                'id_transaksi_awal' 	=> $transaksi->id,
                                'id_area_pembelian' 	=> $areaId,
                                'id_barang' 	 		=> $barangId,
                                'jumlah' 		 		=> $jumlah,
                                'harga' 		 		=> $hargaTotalBaris,
                                'tanggal_kadaluarsa' 	=> $tanggalKadaluarsa,
                                                'jumlah_terpakai' => 0, // akan dihitung di bawah
                                'jenis' 		 		=> 'tambahan', // Jenis Transaksi adalah tambahan/manual
                            ]);

                            $grandTotal += $hargaTotalBaris;
                        }
                    }
                }
            }

            // 4. Loop transaksi lain-lain (opsional)
            if ($request->lain_keterangan && $request->lain_harga) {
                foreach ($request->lain_keterangan as $i => $ket) {
                    $harga = $request->lain_harga[$i] ?? 0;

                    // Lewati jika keterangan atau harga kosong
                    if (empty($ket) && $harga == 0) {
                        continue;
                    }

                    TransaksiLainLain::create([
                        'id_transaksi_awal' => $transaksi->id,
                        'keterangan' 		=> $ket,
                        'harga' 		 	=> $harga,
                    ]);

                    $grandTotal += $harga;
                }
            }

            // 5. Update total transaksi awal
            $transaksi->update(['total' => $grandTotal]);

            // 6. Kurangi Saldo di Dana Utama
            // Kurangi saldo dari dana_utama yang memiliki jenis_dana = 'wrb_old'
            // Lakukan pengecekan agar grandTotal > 0
            if ($grandTotal > 0) {
                $affected = DB::table('dana_utama')
                    ->where('jenis_dana', 'wrb_old')
                    ->decrement('saldo', $grandTotal);

                if ($affected === 0) {
                    // Opsional: Berikan peringatan jika tidak ada saldo yang dikurangi
                    \Log::warning("Tidak ada saldo 'wrb_old' yang ditemukan/dikurangi untuk transaksi ID: " . $transaksi->id);
                }
            }

            DB::commit();

            return redirect()->route('admin.transaksibarang.index')
                ->with('success', 'Transaksi manual berhasil ditambahkan dan saldo dana telah dikurangi!');

        } catch (\Exception $e) {
            DB::rollBack();

            // Log error
            \Log::error('Gagal memproses transaksi manual: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['process_error' => 'Gagal memproses transaksi. Silakan coba lagi. Error: ' . $e->getMessage()]);
        }
    }


    public function kirimMassalProses(Request $request)
{
    // 1. Filtering dan Sanitasi Input
    $allData = $request->all();
    // dd($allData, 'asu');
    $transaksiFiltered = collect($allData['transaksi'] ?? [])
        ->filter(function ($trx) {
            return !empty($trx['details']);
        })
        ->toArray();
    $request->merge(['transaksi' => $transaksiFiltered]);

    // Validasi
    try {
        $data = $request->validate([
            'transaksi' => 'required|array',
            'transaksi.*' => ['array', function ($attribute, $value, $fail) use ($request) {
                $transaksiId = explode('.', $attribute)[1];
                if (!TransaksiBarang::where('id', $transaksiId)->exists()) {
                    $fail("Transaksi dengan ID {$transaksiId} tidak valid atau tidak ditemukan.");
                }
            }],
            'transaksi.*.barang_id' => 'nullable|exists:barang,id',
            'transaksi.*.details' => 'required|array',
            'transaksi.*.details.*.warung_id' => 'required|exists:warung,id',
            'transaksi.*.details.*.jumlah' => 'required|integer|min:1',
        ], [
            // ... Pesan Validasi
            'transaksi.required' => 'Data transaksi wajib diisi.',
            'transaksi.*.details.required' => 'Detail pengiriman harus ada.',
            'transaksi.*.details.*.warung_id.required' => 'Warung tujuan wajib dipilih.',
            'transaksi.*.details.*.warung_id.exists' => 'Warung tujuan tidak valid.',
            'transaksi.*.details.*.jumlah.required' => 'Jumlah pengiriman wajib diisi.',
            'transaksi.*.details.*.jumlah.integer' => 'Jumlah pengiriman harus berupa angka.',
            'transaksi.*.details.*.jumlah.min' => 'Jumlah minimal adalah 1.',
        ]);
    } catch (ValidationException $e) {
        return redirect()->back()->withErrors($e->errors())->withInput();
    }
// dd($data);

    $transaksiIdsDiproses = [];

    DB::transaction(function () use ($data, &$transaksiIdsDiproses) {

        // Fetch Warung data dengan Area dan Laba relations
        $warungIds = collect($data['transaksi'])->pluck('details.*.warung_id')->flatten()->unique()->toArray();
        $warungs = Warung::with('area.laba')->whereIn('id', $warungIds)->get()->keyBy('id');

        foreach ($data['transaksi'] as $transaksiId => $transaksiData) {
            $transaksiBarang = TransaksiBarang::with('areaPembelian')->findOrFail($transaksiId);

            $stokTersedia = $transaksiBarang->jumlah - $transaksiBarang->jumlah_terpakai;
            $totalPengiriman = collect($transaksiData['details'])->sum('jumlah');

            // Cek ketersediaan stok yang sebenarnya
            if ($totalPengiriman > $stokTersedia) {
                throw ValidationException::withMessages([
                    "transaksi.{$transaksiId}" => "Total jumlah pengiriman ({$totalPengiriman}) untuk barang {$transaksiBarang->barang->nama_barang} melebihi stok yang tersedia ({$stokTersedia}).",
                ]);
            }

            // Hitung harga per unit dari stok sumber
            $hargaBeliPerUnit = $transaksiBarang->jumlah > 0 ? $transaksiBarang->harga / $transaksiBarang->jumlah : 0;
            $markupPercent = optional($transaksiBarang->areaPembelian)->markup ?? 0;
            $hargaModalWarung = $hargaBeliPerUnit * (1 + ($markupPercent / 100));

            // --- PROSES PENGIRIMAN KE WARUNG ---
            foreach ($transaksiData['details'] as $detail) {
                $warungId = $detail['warung_id'];
                $jumlahKirim = $detail['jumlah'];

                $warung = $warungs->get($warungId);
                if (!$warung) continue;

                // 1. Cari atau buat Stok Warung
                $stokWarung = StokWarung::firstOrCreate(
                    [
                        'id_warung' => $warungId,
                        'id_barang' => $transaksiBarang->id_barang,
                    ],
                    ['jumlah' => 0]
                );

                // 2. Buat Barang Masuk (Status 'kirim')
                $barangMasuk = BarangMasuk::create([
                    'id_transaksi_barang' => $transaksiBarang->id,
                    'id_stok_warung' => $stokWarung->id,
                    'id_barang' => $transaksiBarang->id_barang,
                    'jumlah' => $jumlahKirim,
                    'status' => 'kirim',
                    'jenis' => 'tambahan',
                    'tanggal_kadaluarsa' => $transaksiBarang->tanggal_kadaluarsa,
                ]);
// dd($barangMasuk);
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

                // 4. Logika INSERT KE HARGAJUAL
                $laba = optional($warung->area)->laba()
                    ->where('input_minimal', '<=', $hargaModalWarung)
                    ->where('input_maksimal', '>=', $hargaModalWarung)
                    ->first();

                $hargaJualSatuan = optional($laba)->harga_jual ?? 0;

                HargaJual::create([
                    'id_warung' => $warungId,
                    'id_barang' => $transaksiBarang->id_barang,
                    'harga_sebelum_markup' => $hargaBeliPerUnit,
                    'harga_modal' => round($hargaModalWarung),
                    'harga_jual_range_awal' => $hargaJualSatuan,
                    'harga_jual_range_akhir' => $hargaJualSatuan,
                    'periode_awal' => now(),
                    'periode_akhir' => null,
                ]);
            }
            
            // --- LOGIKA PEMBARUAN STOK SUMBER (TransaksiBarang) ---
            
            // 5. Update jumlah_terpakai pada TransaksiBarang sumber
            $transaksiBarang->jumlah_terpakai += $totalPengiriman;
            $transaksiBarang->save(); // Simpan perubahan jumlah_terpakai

            $sisaStok = $transaksiBarang->jumlah - $transaksiBarang->jumlah_terpakai;

            if ($sisaStok > 0) {
                // 6. Jika ada sisa, buat TransaksiBarang baru untuk sisa stok
                
                // Hitung ulang harga total untuk sisa stok
                $hargaSisa = $hargaBeliPerUnit * $sisaStok; 
                
                $dataSisa = $transaksiBarang->toArray();
                
                // Hapus ID lama agar Laravel tahu ini data baru
                unset($dataSisa['id']); 
                
                // Update kolom yang relevan untuk data sisa
                $dataSisa['jumlah'] = $sisaStok;
                $dataSisa['jumlah_terpakai'] = 0; // Sisa stok belum terpakai
                $dataSisa['harga'] = round($hargaSisa); // Harga total untuk sisa stok
                $dataSisa['status'] = 'pending'; // Status kembali pending
                $dataSisa['id_transaksi_awal'] = $transaksiBarang->id_transaksi_awal; // Opsional: catat ID sumber aslinya
                
                // Buat entri TransaksiBarang baru untuk sisa stok
                $newTransaksiBarang = TransaksiBarang::create($dataSisa);
                
                // Catat transaksi lama sebagai 'parsial' (atau 'selesai')
                $transaksiBarang->status = 'pending'; 
                $transaksiBarang->save();
                
                $transaksiIdsDiproses[] = $transaksiId . " (parsial, sisa stok dibuat dengan ID: {$newTransaksiBarang->id})";

            } else {
                // 7. Jika stok habis, ubah status menjadi 'dikirim' (selesai)
                $transaksiBarang->status = 'dikirim';
                $transaksiBarang->save();
                $transaksiIdsDiproses[] = $transaksiId . " (selesai)";
            }
        }
    });

    // Redirect
    $totalItemDiproses = count($data['transaksi'] ?? []);
    $successMessage = $totalItemDiproses . ' item stok berhasil didistribusikan. ';
    
    // Tambahkan notifikasi khusus untuk pengiriman parsial
    if (collect($transaksiIdsDiproses)->contains(fn($id) => str_contains($id, 'parsial'))) {
        $successMessage .= 'Beberapa item diproses parsial dan sisa stok telah dibuat sebagai entri baru.';
    } else {
        $successMessage .= 'Semua item dikirim penuh.';
    }

    return redirect()->route('admin.transaksibarang.index', ['status' => 'kirim'])
        ->with('success', $successMessage);
}



    //KEMUNGKINAN KDA TEPAKAI !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!1

    public function kirimRencanaProses(Request $request)
    {
        // ... (Filter dan Validasi tetap sama)
        $allData = $request->all();
        dd($allData);
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

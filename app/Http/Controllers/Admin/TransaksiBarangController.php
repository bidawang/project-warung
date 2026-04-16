<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\TransaksiBarangMasuk;
use App\Models\TransaksiKas;
use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\AreaPembelian;
use App\Models\Warung;
use Illuminate\Support\Facades\Log;
use App\Models\StokWarung;
use App\Models\TransaksiAwal;
use App\Models\TransaksiLainLain;
use App\Models\HutangBarangMasuk;
use App\Models\RencanaBelanja;
use Illuminate\Validation\ValidationException;
use App\Models\HargaJual;
use App\Models\AsalBarang;
use App\Models\HutangWarung;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransaksiBarangController extends Controller
{

    protected function getStockData()
    {
        // Mengambil semua TransaksiBarang (Stok Sumber) yang masih memiliki stok > 0
        $allTransactions = TransaksiBarangMasuk::with('barang')
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
        $status = $request->query('status', 'pending');

        $query = TransaksiBarangMasuk::with(['transaksiKas', 'barang'])
            ->where('jenis', 'tambahan');

        // FILTER STATUS
        if ($status === 'pending') {
            $query->where('status', 'pending');
        } elseif ($status === 'kirim') {
            $query->where('status', 'dikirim');
        } elseif ($status === 'terima') {
            $query->where('status', 'terima');
        } elseif ($status === 'tolak') {
            $query->where('status', 'tolak');
        }

        $transaksibarangs = $query->paginate(10)->appends(['status' => $status]);

        // ================= DATA STOCK =================
        $data = $this->getStockData();
        $warungs = $data['warungs'];
        $stockByBarang = $data['stockByBarang'];

        // ================= RENCANA BELANJA (AMBIL DARI CREATE) =================
        $rencanaBelanjas = RencanaBelanja::with(['barang', 'warung'])
            ->where('status', 'pending')
            ->get();

        $rencanaBelanjaByWarung = $rencanaBelanjas
            ->groupBy(fn($item) => $item->warung->nama_warung ?? 'Tanpa Warung');

        $rencanaBelanjaByBarang = $rencanaBelanjas
            ->groupBy(fn($item) => $item->barang->nama_barang ?? 'Tanpa Barang');

        $rencanaBelanjaTotalByBarang = $rencanaBelanjas
            ->groupBy(fn($item) => $item->barang->nama_barang ?? 'Tanpa Barang')
            ->map(function ($items) {
                return $items->sum(function ($item) {
                    return $item->jumlah_awal - $item->jumlah_dibeli;
                });
            });

        // ================= RETURN =================
        return view('admin.transaksibarang.index', compact(
            'transaksibarangs',
            'status',
            'warungs',
            'stockByBarang',
            'rencanaBelanjaByWarung',
            'rencanaBelanjaByBarang',
            'rencanaBelanjaTotalByBarang'
        ));
    }
    public function riwayat(Request $request)
    {
        $status = $request->query('status', 'semua');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        // Ambil data warung untuk cards
        $data = $this->getStockData();
        $warungs = $data['warungs'];

        $query = TransaksiBarangMasuk::with([
            'barang',
            'detailTransaksiBarangMasuk.stokwarung.warung'
        ])
            ->where('status', '!=', 'pending');

        // Filter Periode Tanggal
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        }

        // Filter Status - PERBAIKAN: mapping yang konsisten
        if ($status !== 'semua' && in_array($status, ['kirim', 'terima', 'tolak'])) {
            $dbStatus = $status === 'kirim' ? 'dikirim' : $status;
            $query->where('status', $dbStatus);
        }

        // Ambil data dengan eager loading optimal
        $transaksibarangs = $query->latest()->get();

        // Stats untuk dashboard (optional)
        $stats = [
            'total_terima' => TransaksiBarangMasuk::where('status', 'terima')->count(),
            'total_kirim'  => TransaksiBarangMasuk::where('status', 'dikirim')->count(),
            'total_tolak'  => TransaksiBarangMasuk::where('status', 'tolak')->count(),
        ];

        return view('admin.transaksibarang.riwayat', compact(
            'transaksibarangs',
            'warungs',
            'status',
            'stats'
        ));
    }

    public function updateStatusMassal(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'status' => 'required|in:diterima,ditolak',
        ]);

        TransaksiBarangMasuk::whereIn('id', $request->ids)->update(['status' => $request->status]);

        return redirect()->route('admin.transaksibarang.index', ['status' => 'pending'])
            ->with('success', 'Status transaksi berhasil diperbarui.');
    }


    public function create()
    {

        // Ambil data yang sudah ada
        $transaksis = TransaksiKas::all();
        $barangs = Barang::all(); // Tetap ambil semua barang untuk referensi harga
        $areas = AreaPembelian::all();
        // dd(123);
        // Ambil data AsalBarang dengan relasi barang
        $asalBarangs = AsalBarang::with('barang')->get();

        // Kelompokkan barang berdasarkan area, dan sertakan id, nama, dan harga barang
        $barangByArea = $asalBarangs->groupBy('id_area_pembelian')->map(function ($items) use ($barangs) {
            // Buat array barang dengan key id_barang untuk akses cepat harga
            $barangMap = $barangs->keyBy('id');

            return $items->map(function ($item) use ($barangMap) {
                $barang = $barangMap[$item->id_barang];
                return [
                    'id' => $item->id_barang,
                    'nama' => $barang->nama_barang ?? 'Nama Tidak Ditemukan',
                    'harga' => $barang->harga ?? 0, // Ambil harga dari model Barang
                ];
            });
        });

        // Ambil data Rencana Belanja (tetap sama)
        $rencanaBelanjas = RencanaBelanja::with(['barang', 'warung'])
            ->where('status', 'pending')
            ->get();

        $rencanaBelanjaByWarung = $rencanaBelanjas->groupBy('warung.nama_warung');
        $rencanaBelanjaByBarang = $rencanaBelanjas->groupBy('barang.nama_barang');
        $rencanaBelanjaTotalByBarang = $rencanaBelanjas
            ->groupBy('barang.nama_barang')
            ->map(function ($items) {
                return $items->sum(function ($item) {
                    return $item->jumlah_awal - $item->jumlah_dibeli;
                });
            });
        $rencanaBelanjaRaw = RencanaBelanja::with(['barang', 'warung'])
            ->where('status', 'pending')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'id_barang' => $item->id_barang,
                    'id_warung' => $item->id_warung,
                    'nama_barang' => $item->barang->nama_barang ?? '-',
                    'nama_warung' => $item->warung->nama_warung ?? '-',
                    'jumlah_awal' => $item->jumlah_awal,
                    'jumlah_dibeli' => $item->jumlah_dibeli ?? 0,
                    'sisa' => $item->jumlah_awal - ($item->jumlah_dibeli ?? 0),
                ];
            });

        return view('admin.transaksibarang.create', compact(
            'transaksis',
            'barangs',
            'areas',
            'barangByArea', // Tambahkan ini
            'rencanaBelanjaByWarung',
            'rencanaBelanjaByBarang',
            'rencanaBelanjaTotalByBarang',
            'rencanaBelanjaRaw' // Tambahkan ini untuk data mentah
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
        // dd($request->all());
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
                            TransaksiBarangMasuk::create([
                                'id_transaksi_awal'     => $transaksi->id,
                                'id_area_pembelian'     => $areaId,
                                'id_barang'              => $barangId,
                                'jumlah'                  => $jumlah,
                                'harga'                  => $hargaTotalBaris,
                                'tanggal_kadaluarsa'     => $tanggalKadaluarsa,
                                'jumlah_terpakai' => 0, // akan dihitung di bawah
                                'jenis'                  => 'tambahan', // Jenis Transaksi adalah tambahan/manual
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
                        'keterangan'         => $ket,
                        'harga'              => $harga,
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
                    Log::warning("Tidak ada saldo 'wrb_old' yang ditemukan/dikurangi untuk transaksi ID: " . $transaksi->id);
                }
            }

            DB::commit();

            return redirect()->route('admin.transaksibarang.index')
                ->with('success', 'Transaksi manual berhasil ditambahkan dan saldo dana telah dikurangi!');
        } catch (\Exception $e) {
            DB::rollBack();

            // Log error
            Log::error('Gagal memproses transaksi manual: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->withErrors(['process_error' => 'Gagal memproses transaksi. Silakan coba lagi. Error: ' . $e->getMessage()]);
        }
    }


    public function kirimMassalProses(Request $request)
    {
        // dd(('asw'), $request->all());
        // 1. Filtering input kosong
        $transaksiFiltered = collect($request->transaksi ?? [])
            ->filter(fn($trx) => !empty($trx['details']))
            ->toArray();

        $request->merge(['transaksi' => $transaksiFiltered]);

        // 2. Validasi
        $data = $request->validate([
            'transaksi' => 'required|array',
            'transaksi.*' => ['array', function ($attribute, $value, $fail) {
                $transaksiId = explode('.', $attribute)[1];
                if (!TransaksiBarangMasuk::where('id', $transaksiId)->exists()) {
                    $fail("Transaksi dengan ID {$transaksiId} tidak valid.");
                }
            }],
            'transaksi.*.details' => 'required|array',
            'transaksi.*.details.*.id_warung' => 'required|exists:warung,id',
            'transaksi.*.details.*.jumlah' => 'required|integer|min:1',
        ]);
        // dd( $data);
        DB::transaction(function () use ($data) {

            // 🔥 Ambil semua warung
            $warungIds = collect($data['transaksi'])
                ->pluck('details.*.id_warung')
                ->flatten()
                ->unique();

            $warungs = Warung::with('area.laba')
                ->whereIn('id', $warungIds)
                ->get()
                ->keyBy('id');

            // 🔥 Ambil rencana belanja (pending saja)
            $rencanaMap = RencanaBelanja::where('status', 'pending')
                ->get()
                ->groupBy(function ($item) {
                    return $item->id_warung . '-' . $item->id_barang;
                });

            $rekapHutangWarung = [];

            foreach ($data['transaksi'] as $transaksiId => $transaksiData) {

                $transaksiBarang = TransaksiBarangMasuk::with('areaPembelian', 'barang')
                    ->findOrFail($transaksiId);

                $totalPengiriman = collect($transaksiData['details'])->sum('jumlah');
                $stokTersedia = $transaksiBarang->jumlah - $transaksiBarang->jumlah_terpakai;

                if ($totalPengiriman > $stokTersedia) {
                    throw ValidationException::withMessages([
                        "transaksi.{$transaksiId}" => "Stok {$transaksiBarang->barang->nama_barang} tidak cukup."
                    ]);
                }

                $hargaBeliPerUnit = $transaksiBarang->jumlah > 0
                    ? $transaksiBarang->harga / $transaksiBarang->jumlah
                    : 0;

                $markupPercent = optional($transaksiBarang->areaPembelian)->markup ?? 0;
                $hargaModalWarung = $hargaBeliPerUnit * (1 + ($markupPercent / 100));

                foreach ($transaksiData['details'] as $detail) {

                    $warungId = $detail['id_warung'];
                    $jumlahKirim = (int) $detail['jumlah'];
                    $warung = $warungs->get($warungId);

                    $barangId = $transaksiBarang->id_barang;

                    // 🔥 DETEKSI BELANJA TAMBAHAN
                    $key = $warungId . '-' . $barangId;
                    $isTambahan = isset($rencanaMap[$key]);

                    // 🔥 Update rencana jika ada
                    if ($isTambahan) {
                        foreach ($rencanaMap[$key] as $rencana) {
                            // dd($rencana);
                            // 🔥 langsung override
                            $rencana->update([
                                'jumlah_dibeli' => $detail['jumlah'],
                                'keterangan'    => 'Belanja Tambahan',
                            ]);
                        }
                    }
                    // dd('gagal');
                    $jumlahFinal = (int) $detail['jumlah'];

                    $totalHargaBarang = ceil($hargaModalWarung * $jumlahFinal) * 500;

                    // 1. Stok Warung
                    $stokWarung = StokWarung::firstOrCreate(
                        ['id_warung' => $warungId, 'id_barang' => $barangId],
                        ['jumlah' => 0]
                    );

                    // 2. Barang Masuk
                    $barangMasuk = BarangMasuk::create([
                        'id_transaksi_barang_masuk' => $transaksiBarang->id,
                        'id_stok_warung'      => $stokWarung->id,
                        'jumlah'              => $jumlahFinal,
                        'total'               => $totalHargaBarang,
                        'status'              => 'kirim',
                        // 'jenis'               => 'tambahan',
                        'jenis'               => $isTambahan ? 'rencana' : 'tambahan', // 🔥 FIX
                        'tanggal_kadaluarsa'  => $transaksiBarang->tanggal_kadaluarsa,
                    ]);

                    // 3. Hutang Warung (induk)
                    if (!isset($rekapHutangWarung[$warungId])) {
                        $rekapHutangWarung[$warungId] = HutangWarung::create([
                            'id_warung' => $warungId,
                            'total'     => 0,
                            'jenis'     => 'barang masuk',
                        ]);
                    }

                    $hutangInduk = $rekapHutangWarung[$warungId];
                    $hutangInduk->increment('total', $totalHargaBarang);

                    // 4. Update hutang warung
                    $warung->increment('hutang', $totalHargaBarang);

                    // 5. Detail hutang
                    HutangBarangMasuk::create([
                        'id_hutang_warung' => $hutangInduk->id,
                        'id_warung'        => $warungId,
                        'id_barang_masuk'  => $barangMasuk->id,
                        'total'            => $totalHargaBarang,
                    ]);

                    // 6. Harga jual
                    $laba = optional($warung->area)->laba()
                        ->where('input_minimal', '<=', $hargaModalWarung)
                        ->where('input_maksimal', '>=', $hargaModalWarung)
                        ->first();

                    $hargaJualSatuan = optional($laba)->harga_jual ?? 0;

                    HargaJual::where('id_warung', $warungId)
                        ->where('id_barang', $barangId)
                        ->whereNull('periode_akhir')
                        ->orderByDesc('id')
                        ->limit(1)
                        ->update(['periode_akhir' => now()]);

                    HargaJual::create([
                        'id_warung'              => $warungId,
                        'id_barang'              => $barangId,
                        'harga_sebelum_markup'   => $hargaBeliPerUnit,
                        'harga_modal'            => ceil($hargaModalWarung) * 1000,
                        'harga_jual_range_awal'  => $hargaJualSatuan,
                        'harga_jual_range_akhir' => $hargaJualSatuan,
                        'total_barang'           => $jumlahFinal,
                        'barang_terjual'         => 0,
                        'periode_awal'           => now(),
                    ]);
                }

                // 7. Update stok sumber
                $transaksiBarang->jumlah_terpakai += $totalPengiriman;

                if (($transaksiBarang->jumlah - $transaksiBarang->jumlah_terpakai) > 0) {
                    $sisa = $transaksiBarang->jumlah - $transaksiBarang->jumlah_terpakai;

                    $dataSisa = $transaksiBarang->toArray();
                    unset($dataSisa['id']);

                    $dataSisa['jumlah'] = $sisa;
                    $dataSisa['jumlah_terpakai'] = 0;
                    $dataSisa['harga'] = ceil($hargaBeliPerUnit * $sisa) * 500;
                    $dataSisa['status'] = 'pending';

                    TransaksiBarangMasuk::create($dataSisa);
                }

                $transaksiBarang->status = 'dikirim';
                $transaksiBarang->save();
            }
        });

        return redirect()->route('admin.transaksibarang.index', ['status' => 'kirim'])
            ->with('success', 'Distribusi stok & belanja tambahan berhasil diproses.');
    }
}

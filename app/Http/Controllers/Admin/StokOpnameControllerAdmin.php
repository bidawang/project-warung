<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\StokWarung;
use App\Models\StokOpname;
use App\Models\Warung;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\KasWarung;
use App\Models\HutangBarangMasuk;
use App\Models\HutangOpname;
use App\Models\TransaksiKas;

class StokOpnameControllerAdmin extends Controller
{
    public function index(Request $request)
    {
        // dd($request->all());
        $listWarung = Warung::all();
        $activeWarungId = $request->get('warung_id');
        $activeWarung = null;

        // --- 1. Logika Penentuan Warung Aktif ---
        if (Auth::user()->role === 'admin') {
            if ($listWarung->isEmpty()) {
                $activeWarungId = null; // Tidak ada warung sama sekali
            } elseif ($listWarung->count() == 1) {
                // Auto-select jika hanya ada satu warung
                $activeWarung = $listWarung->first();
                $activeWarungId = $activeWarung->id;
            } elseif ($activeWarungId) {
                // Warung dipilih via URL
                $activeWarung = $listWarung->firstWhere('id', $activeWarungId);
            } else {
                // Admin memiliki banyak warung tapi belum memilih -> Tampilkan menu pilih
                $activeWarungId = null;
            }
        } else {
            // Non-admin (User Warung)
            $activeWarung = Warung::where('id_user', Auth::id())->first();
            $activeWarungId = $activeWarung->id ?? null;
        }

        // Jika tidak ada warung aktif (misal Admin harus memilih dulu atau tidak ada warung terdaftar)
        if (!$activeWarungId) {
            return view('admin.stok_opname.index', [
                'stokSekarang' => collect(),
                'riwayatTanggal' => collect(),
                'tanggalFilter' => null,
                'activeTab' => 'input',
                'canInputToday' => false,
                'lastOpnameDate' => null,
                'daysSinceLastOpname' => null,
                'listWarung' => $listWarung,
                'activeWarungId' => null, // Ini akan memicu tampilan pemilihan warung di View
            ]);
        }

        // --- 2. Logika Pemrosesan Data (Hanya berjalan jika Warung Aktif sudah ditentukan) ---

        $tanggalFilter = $request->get('tanggal');
        $activeTab = $request->get('tab', 'input');

        // Batasan Input 2 Hari (difilter per warung)
        $lastOpname = StokOpname::whereHas('stokWarung', function ($query) use ($activeWarungId) {
            $query->where('id_warung', $activeWarungId);
        })
            ->latest('created_at')
            ->first();

        // =========================================================================
        // >>> LOGIKA BARU: AUTO-SELECT TANGGAL TERBARU UNTUK TAB RIWAYAT <<<
        // =========================================================================
        if ($activeTab === 'riwayat' && !$tanggalFilter && $lastOpname) {
            // Jika di tab riwayat dan belum ada filter tanggal, gunakan tanggal opname terbaru
            $tanggalFilter = Carbon::parse($lastOpname->created_at)->toDateString();
        }
        // =========================================================================

        $canInputToday = true;
        $lastOpnameDate = null;
        $daysSinceLastOpname = null;

        if ($lastOpname) {
            $lastOpnameDate = Carbon::parse($lastOpname->created_at);
            $today = Carbon::now()->startOfDay();
            $lastOpnameStartOfDay = $lastOpnameDate->startOfDay();

            $daysSinceLastOpname = $lastOpnameStartOfDay->diffInDays($today);

            if ($daysSinceLastOpname < 2) {
                $canInputToday = false;
            }
        }

        // Riwayat tanggal hanya untuk warung yang aktif
        $riwayatTanggal = StokOpname::selectRaw('DATE(stok_opname.created_at) as tanggal')
            ->join('stok_warung', 'stok_opname.id_stok_warung', '=', 'stok_warung.id')
            ->where('stok_warung.id_warung', $activeWarungId)
            ->groupBy('tanggal')
            ->orderByDesc('tanggal')
            ->get();

        // Ambil stok warung hanya untuk warung yang aktif (untuk list barang)
        $stokWarung = StokWarung::with(['barang'])
            ->where('id_warung', $activeWarungId)
            ->get();

        $tanggalTampil = $tanggalFilter ?: Carbon::now()->toDateString();

        // Flag untuk menentukan apakah sedang di mode riwayat
        $isViewingHistory = $tanggalFilter !== null;

        $opnameTanggal = collect();
        if ($tanggalTampil) {
            // Ambil data opname untuk tanggal yang dilihat
            $opnameTanggal = StokOpname::with('stokWarung')
                ->whereDate('created_at', $tanggalTampil)
                ->whereHas('stokWarung', function ($query) use ($activeWarungId) {
                    $query->where('id_warung', $activeWarungId);
                })
                ->get()
                ->keyBy('id_stok_warung');
        }

        // Gabungkan stok warung dan hasil opname
        $dataGabung = $stokWarung->map(function ($stok) use ($opnameTanggal, $isViewingHistory) {
            $barang = $stok->barang;
            $opname = $opnameTanggal->get($stok->id);

            // Default: Ambil stok current dari StokWarung (untuk list Input Terbaru)
            $stokSistemTampil = $stok->jumlah ?? 0;
            $jumlahOpnameTampil = null;
            $selisih = null;
            $status = '❌ Tidak Diperiksa';

            if ($opname) {
                $jumlahOpnameTampil = $opname->jumlah_sesudah;

                if ($isViewingHistory) {
                    // RIWAYAT MODE: Gunakan jumlah_sebelum dari riwayat sebagai 'Stok Sistem (Sebelum)'
                    $stokSistemTampil = $opname->jumlah_sebelum ?? 0;
                }
                // Jika sedang Input Terbaru, $stokSistemTampil tetap menggunakan stok current dari StokWarung

                $selisih = $jumlahOpnameTampil - $stokSistemTampil;
                $status = '✅ Sudah dicek';
            }

            return [
                'id_stok_warung' => $stok->id,
                'nama_barang' => $barang->nama_barang ?? '-',
                'stok_sistem' => $stokSistemTampil, // In History: jumlah_sebelum. In Input: current StokWarung::jumlah
                'jumlah_opname' => $jumlahOpnameTampil, // jumlah_sesudah
                'status' => $status,
                'selisih' => $selisih,
            ];
        });

        // PENGURUTAN BERDASARKAN MINUS TERTINGGI (Selisih terkecil/negatif)
        if ($activeTab === 'riwayat' || $tanggalFilter) {
            $dataGabung = $dataGabung->sortBy('selisih')->values();
        }

        return view('admin.stok_opname.index', [
            'stokSekarang' => $dataGabung,
            'riwayatTanggal' => $riwayatTanggal,
            'tanggalFilter' => $tanggalFilter, // $tanggalFilter sekarang bisa berisi tanggal terbaru
            'activeTab' => $activeTab,
            'canInputToday' => $canInputToday,
            'lastOpnameDate' => $lastOpnameDate,
            'daysSinceLastOpname' => $daysSinceLastOpname,
            'listWarung' => $listWarung,
            'activeWarungId' => $activeWarungId,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_warung'      => 'required|integer|exists:warung,id',
            'id_stok_warung' => 'required|array|min:1',
            'jumlah'         => 'required|array|min:1',
            'jumlah.*'       => 'nullable|integer|min:0',
        ]);

        $idWarung = $validated['id_warung'];

        // Pastikan ada minimal satu input
        $hasAnyInput = false;
        foreach ($validated['jumlah'] as $jumlah) {
            if ($jumlah !== null && $jumlah !== '') {
                $hasAnyInput = true;
                break;
            }
        }

        if (!$hasAnyInput) {
            return redirect()
                ->route('admin.stokopname.index', ['warung_id' => $idWarung])
                ->with('error', 'Silakan masukkan jumlah fisik untuk setidaknya satu barang.');
        }

        try {
            DB::beginTransaction();

            // =========================
            // KAS WARUNG (LOGIKA LAMA)
            // =========================
            // dd('debug');
            $kasWarung = KasWarung::where('id_warung', $idWarung)
                ->where('jenis_kas', 'cash')
                ->firstOrFail();
            // Ambil input yang diisi saja
            $inputJumlah = array_filter(
                $validated['jumlah'],
                fn($value) => $value !== null && $value !== ''
            );

            $stokIds = array_keys($inputJumlah);

            $stokDetails = StokWarung::whereIn('id', $stokIds)
                ->where('id_warung', $idWarung)
                ->with('barang', 'hargaJual')
                ->get()
                ->keyBy('id');

            $isOpnamePerformed = false;
            $totalItemsChanged = 0;

            foreach ($inputJumlah as $stokId => $stokFisikInput) {

                $stok = $stokDetails->get($stokId);
                if (!$stok) {
                    continue;
                }

                $stokFisik = (int) $stokFisikInput;
                $stokSistemSaatIni = (int) $stok->jumlah;
                $selisih = $stokFisik - $stokSistemSaatIni;

                // =========================
                // 1️⃣ SIMPAN RIWAYAT OPNAME
                // =========================
                $stokOpname = StokOpname::create([
                    'id_stok_warung' => $stok->id,
                    'jumlah_sebelum' => $stokSistemSaatIni,
                    'jumlah_sesudah' => $stokFisik,
                    'selisih'        => $selisih,
                    'harga'          => optional($stok->hargaJual)->harga_jual_range_akhir ?? 0,
                    'keterangan'     => $selisih > 0
                        ? 'Kelebihan Stok'
                        : ($selisih < 0 ? 'Kekurangan Stok' : 'Stok Sesuai'),
                    'dibuat_oleh'    => Auth::id(),
                ]);

                $isOpnamePerformed = true;

                // =========================
                // 2️⃣ JIKA ADA SELISIH
                // =========================
                if ($selisih !== 0) {
                    $totalItemsChanged++;

                    // Update stok ke fisik
                    $stok->update([
                        'jumlah' => $stokFisik,
                    ]);

                    $harga = optional($stok->hargaJual)->harga_jual_range_akhir ?? 0;
                    $nominal = abs($selisih * $harga);

                    // =========================
                    // TRANSAKSI KAS (LOGIKA LAMA)
                    // =========================
                    $nominalKas = $selisih > 0 ? $nominal : -$nominal;
                    $jenisKas = $nominalKas > 0 ? 'opname +' : 'opname -';

                    $namaBarang = optional($stok->barang)->nama_barang ?? 'Barang ID: ' . $stok->id;

                    TransaksiKas::create([
                        'id_kas_warung' => $kasWarung->id,
                        'total'         => $nominalKas,
                        'jenis'         => $jenisKas,
                        'keterangan'    => "Penyesuaian Kas Opname: {$namaBarang} ({$selisih} Pcs)",
                    ]);

                    // =========================
                    // 🔥 LOGIKA BARU: HUTANG OPNAME
                    // =========================

                    // stok kurang  => hutang +
                    // stok lebih   => hutang -
                    $totalHutang = $selisih < 0 ? $nominal : -$nominal;

                    $hutang = HutangBarangMasuk::create([
                        'id_warung' => $idWarung,
                        'id_barang_masuk' => null,
                        'total' => $totalHutang,
                        'status' => 'belum lunas',
                    ]);

                    HutangOpname::create([
                        'id_hutang_barang_masuk' => $hutang->id,
                        'id_stok_opname' => $stokOpname->id,
                    ]);
                }
            }

            if (!$isOpnamePerformed) {
                DB::rollBack();
                return redirect()
                    ->route('admin.stokopname.index', ['warung_id' => $idWarung])
                    ->with('warning', 'Tidak ada stok yang diinput. Opname tidak disimpan.');
            }

            DB::commit();

            return redirect()
                ->route('admin.stokopname.index', ['warung_id' => $idWarung])
                ->with(
                    'success',
                    "Hasil opname berhasil disimpan. Ditemukan {$totalItemsChanged} item dengan selisih."
                );
        } catch (\Throwable $e) {
            DB::rollBack();

            \Log::error('Error menyimpan opname', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);

            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}

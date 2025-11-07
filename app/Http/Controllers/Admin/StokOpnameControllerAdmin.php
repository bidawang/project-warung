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
        // dd($riwayatTanggal);
        // Ambil stok warung hanya untuk warung yang aktif
        $stokWarung = StokWarung::with(['barang'])
            ->where('id_warung', $activeWarungId)
            ->get();

        $tanggalTampil = $tanggalFilter ?: Carbon::now()->toDateString();

        $opnameTanggal = collect();
        if ($tanggalTampil) {
            $opnameTanggal = StokOpname::with('stokWarung')
                ->whereDate('created_at', $tanggalTampil)
                ->whereHas('stokWarung', function ($query) use ($activeWarungId) {
                    $query->where('id_warung', $activeWarungId);
                })
                ->get()
                ->keyBy('id_stok_warung');
        }

        // Gabungkan stok warung dan hasil opname
        $dataGabung = $stokWarung->map(function ($stok) use ($opnameTanggal) {
            $barang = $stok->barang;
            $opname = $opnameTanggal->get($stok->id);

            $jumlahOpname = $opname ? $opname->jumlah : null;
            $stokSistem = $stok->jumlah ?? 0;
            $selisih = $jumlahOpname !== null ? $jumlahOpname - $stokSistem : null;

            if ($jumlahOpname !== null) {
                $status = '✅ Sudah dicek';
            } else {
                $status = '❌ Tidak Diperiksa';
            }

            return [
                'id_stok_warung' => $stok->id,
                'nama_barang' => $barang->nama_barang ?? '-',
                'stok_sistem' => $stokSistem,
                'jumlah_opname' => $jumlahOpname,
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
            'tanggalFilter' => $tanggalFilter,
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
        // dd($request->all()); // Hapus dd() ini setelah pengujian

        $validated = $request->validate([
            'id_warung'      => 'required|integer|exists:warung,id',
            'id_stok_warung' => 'required|array|min:1',
            'jumlah'         => 'required|array|min:1',
            'jumlah.*'       => 'nullable|integer|min:0',
        ]);

        $idWarung = $validated['id_warung'];

        $hasAnyInput = false;
        foreach ($validated['jumlah'] as $jumlah) {
            if ($jumlah !== null && $jumlah !== '') {
                $hasAnyInput = true;
                break;
            }
        }
        // dd($hasAnyInput);

        if (!$hasAnyInput) {
            return redirect()->route('admin.stokopname.index', ['warung_id' => $idWarung])
                ->with('error', 'Silakan masukkan jumlah fisik untuk setidaknya satu barang.');
        }

        try {
            DB::beginTransaction();

            $kasWarung = KasWarung::where('id_warung', $idWarung)
                ->where('jenis_kas', 'cash')
                ->firstOrFail();

            // Ambil ID dari input yang diisi (filter input kosong/null)
            $inputJumlah = array_filter($validated['jumlah'], fn($value) => $value !== null && $value !== '');
            $stokIds = array_keys($inputJumlah);

            // Ambil stok warung yang diinput
            $stokDetails = StokWarung::whereIn('id', $stokIds)
                ->where('id_warung', $idWarung)
                ->with('barang', 'hargaJual')
                ->get()->keyBy('id');

            $isOpnamePerformed = false;
            $totalItemsChanged = 0; // Menghitung item yang memiliki selisih (akan memicu TransaksiKas)

            // Loop melalui input yang DIIISI saja
            foreach ($inputJumlah as $stokId => $stokFisikInput) {

                $stok = $stokDetails->get($stokId);
                if (!$stok) {
                    continue;
                }

                $stokFisik = (int)$stokFisikInput;
                $stokSistemSaatIni = (int)$stok->jumlah; // Jumlah sebelum update = jumlah_sebelum
                $selisih = $stokFisik - $stokSistemSaatIni;

                // --- 1. SELALU REKAM DI STOKOPNAME (RIWAYAT) ---
                StokOpname::create([
                    'id_stok_warung' => $stok->id,
                    'jumlah_sebelum' => $stokSistemSaatIni,
                    'jumlah_sesudah' => $stokFisik,
                    'selisih'        => $selisih,
                    'harga'          => optional($stok->hargaJual)->harga_jual_range_akhir ?? 0,
                    'keterangan'     => $selisih > 0 ? 'Kelebihan Stok' : ($selisih < 0 ? 'Kekurangan Stok' : 'Stok Sesuai'),
                    'dibuat_oleh'    => Auth::id(),
                ]);
                $isOpnamePerformed = true;

                // --- 2. HANYA PROSES JIKA SELISIH TIDAK NOL ---
                if ($selisih !== 0) {
                    $totalItemsChanged++;

                    // a. Update stok ke jumlah fisik
                    $stok->update(['jumlah' => $stokFisik]);

                    // b. Buat Transaksi Kas
                    $harga = optional($stok->hargaJual)->harga_jual_range_akhir ?? 0;
                    $nominalSelisihItem = abs($selisih * $harga);

                    // Tentukan arah transaksi
                    $nominalPenyesuaianKas = $selisih > 0 ? $nominalSelisihItem : -$nominalSelisihItem;
                    $jenisTransaksi = $nominalPenyesuaianKas > 0 ? 'opname +' : 'opname -';
                    $namaBarang = optional($stok->barang)->nama_barang ?? 'Barang ID: ' . $stok->id;

                    $keterangan = "Penyesuaian Kas Opname: {$namaBarang} ({$selisih} Pcs)";

                    TransaksiKas::create([
                        'id_kas_warung' => $kasWarung->id,
                        'total'         => $nominalPenyesuaianKas,
                        'jenis'         => $jenisTransaksi,
                        'keterangan'    => $keterangan,
                    ]);
                }
            }

            // Jika tidak ada stok yang di-opname (semua input kosong), batalkan proses.
            if (!$isOpnamePerformed) {
                DB::rollBack();
                return redirect()->route('admin.stokopname.index', ['warung_id' => $idWarung])
                    ->with('warning', 'Tidak ada stok yang diinput. Opname tidak disimpan.');
            }

            DB::commit();
            $message = "Hasil opname berhasil disimpan dan stok disesuaikan. Ditemukan **{$totalItemsChanged} item** yang selisihnya memerlukan penyesuaian kas.";
            return redirect()->route('admin.stokopname.index', ['warung_id' => $idWarung])
                ->with('success', $message);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Error menyimpan opname: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}

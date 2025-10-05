<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\StokWarung;
use App\Models\StokOpname;
use App\Models\Warung;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;

class StokOpnameControllerAdmin extends Controller
{
    public function index(Request $request)
    {
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
        // 1. Ambil ID Warung dari salah satu id_stok_warung
        $firstStokWarungId = $request->id_stok_warung[0] ?? null;

        if (!$firstStokWarungId) {
             return back()->with('error', 'Tidak ada barang yang dipilih untuk diopname.')->withInput();
        }

        $stokWarungSample = StokWarung::find($firstStokWarungId);
        $activeWarungId = $stokWarungSample->id_warung ?? null;

        // Pengecekan Batasan 2 Hari saat proses penyimpanan (Penting!)
        $lastOpname = StokOpname::whereHas('stokWarung', function ($query) use ($activeWarungId) {
                $query->where('id_warung', $activeWarungId);
            })
            ->latest('created_at')
            ->first();

        $canInputToday = true;

        if ($lastOpname) {
            $lastOpnameDate = Carbon::parse($lastOpname->created_at)->startOfDay();
            if (Carbon::now()->startOfDay()->lessThanOrEqualTo($lastOpnameDate->addDays(1))) {
                $canInputToday = false;
            }
        }

        if (!$canInputToday) {
            return redirect()->route('admin.stokopname.index', ['warung_id' => $activeWarungId])
                             ->with('error', 'Stok opname hanya dapat dilakukan minimal 2 hari sekali setelah opname terakhir.');
        }

        $inputData = [];
        foreach ($request->id_stok_warung as $id) {
            if (isset($request->jumlah[$id]) && is_numeric($request->jumlah[$id])) {
                $inputData[] = [
                    'id_stok_warung' => $id,
                    'jumlah' => (int)$request->jumlah[$id],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        if (!empty($inputData)) {
            StokOpname::insert($inputData);
            return redirect()->route('admin.stokopname.index', ['warung_id' => $activeWarungId])->with('success', 'Stok opname berhasil disimpan.');
        }

        return redirect()->route('admin.stokopname.index', ['warung_id' => $activeWarungId])->with('warning', 'Tidak ada data stok fisik yang diisi.');
    }
}

<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Warung;
use App\Models\User;
use App\Models\HargaJual;
use App\Models\Barang;
// use App\Models\Kuantitas;
use App\Models\Area;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\PengeluaranPokokWarung;

class WarungControllerAdmin extends Controller
{
    public function index()
    {
        $warungs = Warung::with(['user', 'area'])->get();
        return view('admin.warung.index', compact('warungs'));
    }

    public function create()
    {
        $usedUserIds = Warung::pluck('id_user');

        $users = User::where('role', 'kasir')
            ->whereNotIn('id', $usedUserIds)
            ->get();

        $areas = Area::all();

        return view('admin.warung.create', compact('users', 'areas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_user' => 'required|exists:users,id',
            'id_area' => 'required|exists:area,id',
            'nama_warung' => 'required|string|max:255',
            'modal' => 'required|numeric',
            'keterangan' => 'nullable|string',
        ]);

        Warung::create($request->all());

        return redirect()->route('admin.warung.index')->with('success', 'Warung berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $warung = Warung::findOrFail($id);
        $users = User::where('role', 'kasir')->get();
        $areas = Area::all();
        return view('admin.warung.edit', compact('warung', 'users', 'areas'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_user' => 'required|exists:users,id',
            'id_area' => 'required|exists:area,id',
            'nama_warung' => 'required|string|max:255',
            'modal' => 'required|numeric',
            'keterangan' => 'nullable|string',
        ]);

        $warung = Warung::findOrFail($id);
        $warung->update($request->all());

        return redirect()->route('admin.warung.index')->with('success', 'Warung berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $warung = Warung::findOrFail($id);
        $warung->delete();

        return redirect()->route('admin.warung.index')->with('success', 'Warung berhasil dihapus.');
    }

    public function pengeluaranPokokByMonth(Request $request, $id)
    {
        $bulan = $request->bulan ?? now()->format('Y-m');

        $tanggal = Carbon::parse($bulan);

        $data = PengeluaranPokokWarung::where('id_warung', $id)
            ->whereMonth('date', $tanggal->month)
            ->whereYear('date', $tanggal->year)
            ->orderBy('date')
            ->get();

        return response()->json([
            'data' => $data,
            'total' => $data->sum('jumlah'),
            'terpenuhi' => $data->where('status', 'terpenuhi')->sum('jumlah'),
            'belum' => $data->where('status', 'belum terpenuhi')->sum('jumlah'),
        ]);
    }

    public function show($id)
    {
        if (Auth::user()->role === 'admin') {
            $warung = Warung::with(['user', 'area'])->findOrFail($id);
        } else {
            $warung = Warung::with(['user', 'area'])
                ->where('id_user', Auth::id())
                ->findOrFail($id);
        }

        $allBarang = Barang::with(['transaksiBarang' => function ($q) {
            $q->latest()->with('areaPembelian');
        }])->get();

        $stokWarung = $warung->stokWarung()->with(['barang', 'kuantitas'])->get();
        $stokByBarangId = $stokWarung->keyBy('id_barang');

        $barangWithStok = $allBarang->map(function ($barang) use ($stokByBarangId, $warung) {

            $stok = $stokByBarangId->get($barang->id);

            $hargaJual = HargaJual::where('id_warung', $warung->id)
                ->where('id_barang', $barang->id)
                ->where(function ($q) {
                    $q->whereNull('periode_awal')->orWhere('periode_awal', '<=', now());
                })
                ->where(function ($q) {
                    $q->whereNull('periode_akhir')->orWhere('periode_akhir', '>=', now());
                })
                ->latest('id')
                ->first();

            $barang->persentase_laba = $hargaJual ? $hargaJual->persentase_laba : 'N/A';

            $sortingKey = 999999.0;

            if ($hargaJual && $hargaJual->harga_modal > 0) {
                $persenAwal = (($hargaJual->harga_jual_range_awal - $hargaJual->harga_modal) / $hargaJual->harga_modal) * 100;
                $sortingKey = $persenAwal;
            }

            $barang->persentase_laba_sort_key = $sortingKey;

            if ($stok) {

                $tanggalKadaluarsa = $stok->tanggal_kadaluarsa
                    ?? optional($barang->transaksiBarang->first())->tanggal_kadaluarsa
                    ?? null;

                $barang->stok_saat_ini = $stok->jumlah ?? 0;
                $barang->kuantitas = $stok->kuantitas ?? collect();
                $barang->keterangan = $stok->keterangan ?? '-';
                $barang->tanggal_kadaluarsa = $tanggalKadaluarsa;
                $barang->id_stok_warung = $stok->id;
            } else {

                $tanggalKadaluarsa = optional($barang->transaksiBarang->first())->tanggal_kadaluarsa ?? null;

                $barang->stok_saat_ini = 0;
                $barang->kuantitas = collect();
                $barang->keterangan = '-';
                $barang->tanggal_kadaluarsa = $tanggalKadaluarsa;
                $barang->id_stok_warung = null;
            }

            if ($hargaJual) {

                $barang->harga_sebelum_markup = $hargaJual->harga_sebelum_markup ?? 0;
                $barang->harga_satuan = $hargaJual->harga_modal ?? 0;
                $barang->harga_jual_range_awal = $hargaJual->harga_jual_range_awal ?? 0;
                $barang->harga_jual_range_akhir = $hargaJual->harga_jual_range_akhir ?? 0;
                $barang->harga_jual = $hargaJual->harga_jual_range_akhir ?? 0;
            } else {

                $barang->harga_sebelum_markup = 0;
                $barang->harga_satuan = 0;
                $barang->harga_jual_range_awal = 0;
                $barang->harga_jual_range_akhir = 0;
                $barang->harga_jual = 0;
            }

            return $barang;
        });

        $barangWithStok = $barangWithStok->sortBy('persentase_laba_sort_key')->values();

        // ===============================
        // HITUNG LABA WARUNG
        // ===============================

        $warungWithLaba = Warung::with([
            'stokWarung.barangKeluar' => function ($q) {
                $q->where('jenis', 'penjualan barang');
            },
            'stokWarung.hargaJual'
        ])->find($warung->id);

        $labaKotor  = 0;
        $labaBersih = 0;

        foreach ($warungWithLaba->stokWarung as $stok) {

            $hargaModal = $stok->hargaJual->harga_modal ?? 0;

            foreach ($stok->barangKeluar as $keluar) {

                $subtotalJual  = $keluar->jumlah * $keluar->harga_jual;
                $subtotalModal = $keluar->jumlah * $hargaModal;

                $labaKotor  += $subtotalJual;
                $labaBersih += ($subtotalJual - $subtotalModal);
            }
        }

        // ===============================
        // ASSET WARUNG
        // ===============================

        $assets = \App\Models\Asset::with('pelunasan')
            ->where('id_warung', $warung->id)
            ->latest()
            ->get();

        foreach ($assets as $asset) {
            $asset->volume_pelunasan = $asset->pelunasan->count();
        }

        // ===============================
        // PENGELUARAN POKOK BULAN INI
        // ===============================

        $now = Carbon::now();

        $pengeluaranPokokBulanIni = PengeluaranPokokWarung::where('id_warung', $warung->id)
            ->whereMonth('date', $now->month)
            ->whereYear('date', $now->year)
            ->latest()
            ->get();

        $totalPengeluaranPokok = $pengeluaranPokokBulanIni->sum('jumlah');

        $totalModal = $labaKotor - $labaBersih;

        return view('admin.warung.show', compact(
            'warung',
            'barangWithStok',
            'stokWarung',
            'labaKotor',
            'labaBersih',
            'totalModal',
            'assets',
            'pengeluaranPokokBulanIni',
            'totalPengeluaranPokok'
        ));
    }

    public function setting($id)
    {
        $warung = Warung::findOrFail($id);
        $users = User::all();
        $areas = Area::all();

        $barangs = Barang::with('subKategori.kategori')->get();

        return view('admin.warung.setting', compact(
            'warung',
            'users',
            'areas',
            'barangs'
        ));
    }
}

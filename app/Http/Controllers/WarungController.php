<?php

namespace App\Http\Controllers;

use App\Models\Warung;
use App\Models\User;
use App\Models\Laba;
use App\Models\Area;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class WarungController extends Controller
{
    public function index()
    {
        $warungs = Warung::with(['user', 'area'])->get();
        return view('warung.index', compact('warungs'));
    }

    public function create()
    {
        $users = User::where('role', 'kasir')->get();
        $areas = Area::all();
        return view('warung.create', compact('users', 'areas'));
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

        return redirect()->route('warung.index')->with('success', 'Warung berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $warung = Warung::findOrFail($id);
        $users = User::where('role', 'kasir')->get();
        $areas = Area::all();
        return view('warung.edit', compact('warung', 'users', 'areas'));
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

        return redirect()->route('warung.index')->with('success', 'Warung berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $warung = Warung::findOrFail($id);
        $warung->delete();

        return redirect()->route('warung.index')->with('success', 'Warung berhasil dihapus.');
    }

    public function show($id)
    {
        $warung = Warung::with([
            'user',
            'area',
            'stokWarung.barang.transaksiBarang.areaPembelian',
        ])
            ->where('id_user', Auth::id())
            ->findOrFail($id);

        $warung->stokWarung->transform(function ($stok) {
            // Hitung stok secara manual di controller
            $stokMasuk = $stok->barangMasuk()
                ->where('status', 'terima')
                ->whereHas('stokWarung', function ($q) {
                    $q->where('id_warung', session('id_warung'));
                })
                ->sum('jumlah');

            $stokKeluar = $stok->barangKeluar()
                ->whereHas('stokWarung', function ($q) {
                    $q->where('id_warung', session('id_warung'));
                })
                ->sum('jumlah');

            $mutasiMasuk = $stok->mutasiBarang()
                ->where('status', 'terima')
                ->whereHas('stokWarung', function ($q) {
                    $q->where('id_warung', session('id_warung'));
                })
                ->sum('jumlah');

            $mutasiKeluar = $stok->mutasiBarang()
                ->where('status', 'keluar')
                ->whereHas('stokWarung', function ($q) {
                    $q->where('id_warung', session('id_warung'));
                })
                ->sum('jumlah');

            $stokSaatIni = $stokMasuk + $mutasiMasuk - $mutasiKeluar - $stokKeluar;

            $transaksi = $stok->barang->transaksiBarang()->latest()->first();

            if (!$transaksi) {
                $stok->harga_satuan = 0;
                $stok->harga_jual = 0;
                $stok->stok_saat_ini = $stokSaatIni; // tambahkan stok ke objek
                return $stok;
            }

            // Harga dasar = total beli / jumlah
            $hargaDasar = $transaksi->harga / max($transaksi->jumlah, 1);

            // Tambahkan markup dari area pembelian
            $markupPercent = optional($transaksi->areaPembelian)->markup ?? 0;
            $hargaSatuan   = $hargaDasar + ($hargaDasar * $markupPercent / 100);
            $stok->harga_satuan = $hargaSatuan;

            // Ambil harga_jual dari tabel laba berdasarkan range input_minimal dan input_maksimal
            $laba = Laba::where('input_minimal', '<=', $hargaSatuan)
                ->where('input_maksimal', '>=', $hargaSatuan)
                ->first();
            $stok->harga_jual = $laba ? $laba->harga_jual : 0;

            $stok->stok_saat_ini = $stokSaatIni; // tambahkan stok ke objek

            return $stok;
        });

        return view('warung.show', compact('warung'));
    }
}

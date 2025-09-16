<?php

namespace App\Http\Controllers;

use App\Models\Warung;
use App\Models\User;
use App\Models\Laba;
use App\Models\Barang;
// use App\Models\Kuantitas;
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
        $warung = Warung::with(['user', 'area'])
            ->where('id_user', Auth::id())
            ->findOrFail($id);

        // Ambil semua barang
        $allBarang = Barang::with(['transaksiBarang.areaPembelian'])->get();

        // Ambil stok warung untuk warung ini, dengan relasi barang dan kuantitas
        $stokWarung = $warung->stokWarung()->with(['barang.transaksiBarang.areaPembelian', 'kuantitas'])->get();
        // Buat koleksi stok indexed by id_barang untuk akses cepat
        $stokByBarangId = $stokWarung->keyBy('id_barang');

        // Gabungkan semua barang dengan stok warung jika ada
        $barangWithStok = $allBarang->map(function ($barang) use ($stokByBarangId) {
            $stok = $stokByBarangId->get($barang->id);

            if ($stok) {
                // Hitung stok saat ini sama seperti sebelumnya
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
                        $q->where('warung_tujuan', session('id_warung'));
                    })
                    ->sum('jumlah');

                $mutasiKeluar = $stok->mutasiBarang()
                    ->where('status', 'terima')
                    ->whereHas('stokWarung', function ($q) {
                        $q->where('warung_asal', session('id_warung'));
                    })
                    ->sum('jumlah');

                $stokSaatIni = $stokMasuk + $mutasiMasuk - $mutasiKeluar - $stokKeluar;

                $transaksi = $stok->barang->transaksiBarang()->latest()->first();

                if (!$transaksi) {
                    $hargaSatuan = 0;
                    $hargaJual = 0;
                } else {
                    $hargaDasar = $transaksi->harga / max($transaksi->jumlah, 1);
                    $markupPercent = optional($transaksi->areaPembelian)->markup ?? 0;
                    $hargaSatuan = $hargaDasar + ($hargaDasar * $markupPercent / 100);

                    $laba = Laba::where('input_minimal', '<=', $hargaSatuan)
                        ->where('input_maksimal', '>=', $hargaSatuan)
                        ->first();
                    $hargaJual = $laba ? $laba->harga_jual : 0;
                }

                // Gabungkan data stok ke barang
                $barang->stok_saat_ini = $stokSaatIni;
                $barang->harga_satuan = $hargaSatuan;
                $barang->harga_jual = $hargaJual;
                $barang->kuantitas = $stok->kuantitas;
                $barang->keterangan = $stok->keterangan ?? '-';
                $barang->id_stok_warung = $stok->id; // <--- ini yang penting

            } else {
                // Barang tidak ada stok di warung ini
                $barang->stok_saat_ini = 0;
                $barang->harga_satuan = 0;
                $barang->harga_jual = 0;
                $barang->kuantitas = collect();
                $barang->keterangan = '-';

            }

            return $barang;
        });

        return view('warung.show', compact('warung', 'barangWithStok', 'stokWarung'));
    }
}

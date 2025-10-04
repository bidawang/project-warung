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
        return view('admin.warung.index', compact('warungs'));
    }

    public function create()
    {
        $users = User::where('role', 'kasir')->get();
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

    public function show($id)
    {
        // akses warung sesuai role
        if (Auth::user()->role === 'admin') {
            $warung = Warung::with(['user', 'area'])->findOrFail($id);
        } else {
            $warung = Warung::with(['user', 'area'])
                ->where('id_user', Auth::id())
                ->findOrFail($id);
        }

        // ambil barang + transaksi terbaru (jika ada) untuk fallback tanggal kadaluarsa
        $allBarang = Barang::with(['transaksiBarang' => function ($q) {
            $q->latest()->with('areaPembelian');
        }])->get();

        // ambil stok warung (relasi stokWarung di model Warung)
        $stokWarung = $warung->stokWarung()->with(['barang', 'kuantitas'])->get();
        $stokByBarangId = $stokWarung->keyBy('id_barang');

        $barangWithStok = $allBarang->map(function ($barang) use ($stokByBarangId, $warung) {
            $stok = $stokByBarangId->get($barang->id);

            // cari harga_jual aktif untuk warung ini (periode-aware)
            $hargaJual = \App\Models\HargaJual::where('id_warung', $warung->id)
                ->where('id_barang', $barang->id)
                ->where(function ($q) {
                    $q->whereNull('periode_awal')->orWhere('periode_awal', '<=', now());
                })
                ->where(function ($q) {
                    $q->whereNull('periode_akhir')->orWhere('periode_akhir', '>=', now());
                })
                ->latest('id')
                ->first();

            if ($stok) {
                // tanggal kadaluarsa prioritas ke stok, fallback ke transaksi terbaru barang
                $tanggalKadaluarsa = $stok->tanggal_kadaluarsa
                    ?? optional($barang->transaksiBarang->first())->tanggal_kadaluarsa
                    ?? null;

                $barang->stok_saat_ini = $stok->jumlah ?? 0;
                $barang->harga_sebelum_markup = $hargaJual->harga_sebelum_markup ?? 0;
                $barang->harga_satuan = $hargaJual->harga_modal ?? 0;
                $barang->harga_jual_range_awal = $hargaJual->harga_jual_range_awal ?? 0;
                $barang->harga_jual = $hargaJual->harga_jual_range_akhir ?? 0;
                $barang->kuantitas = $stok->kuantitas ?? collect(); // pastikan koleksi, bukan null
                $barang->keterangan = $stok->keterangan ?? '-';
                $barang->tanggal_kadaluarsa = $tanggalKadaluarsa;
                $barang->id_stok_warung = $stok->id;
            } else {
                // tidak ada stok di warung ini
                $tanggalKadaluarsa = optional($barang->transaksiBarang->first())->tanggal_kadaluarsa ?? null;

                $barang->stok_saat_ini = 0;
                $barang->harga_sebelum_markup = $hargaJual->harga_sebelum_markup ?? 0;
                $barang->harga_satuan = $hargaJual->harga_modal ?? 0;
                $barang->harga_jual_range_awal = $hargaJual->harga_jual_range_awal ?? 0;
                $barang->harga_jual = $hargaJual->harga_jual_range_akhir ?? 0;
                $barang->kuantitas = collect();
                $barang->keterangan = '-';
                $barang->tanggal_kadaluarsa = $tanggalKadaluarsa;
                $barang->id_stok_warung = null;
            }

            return $barang;
        });

        return view('admin.warung.show', compact('warung', 'barangWithStok', 'stokWarung'));
    }
}

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
        if (Auth::user()->role === 'admin') {
            // Admin can access all stalls
            $warung = Warung::with(['user', 'area'])->findOrFail($id);
        } else {
            // Cashier/regular user can only access their own stalls
            $warung = Warung::with(['user', 'area'])
                ->where('id_user', Auth::id())
                ->findOrFail($id);
        }

        // Fetch all items, with eager-loaded relations for the latest transaction and its purchasing area
        $allBarang = Barang::with(['transaksiBarang' => function ($query) {
            $query->with('areaPembelian')->latest();
        }])->get();

        // Fetch the stall's stock for this specific stall, with relations for item and quantity
        $stokWarung = $warung->stokWarung()->with(['barang', 'kuantitas'])->get();
// dd($stokWarung);
        // Create a collection of stock indexed by item ID for quick access
        $stokByBarangId = $stokWarung->keyBy('id_barang');

        // Merge all items with stall stock if available
        $barangWithStok = $allBarang->map(function ($barang) use ($stokByBarangId) {
            $stok = $stokByBarangId->get($barang->id);

            if ($stok) {
                // Calculate current stock just as before
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

                // Get the latest transaction from the eager-loaded relation
                $transaksi = $barang->transaksiBarang->first();

                if (!$transaksi) {
                    $hargaSatuan = 0;
                    $hargaJual = 0;
                    $tanggalKadaluarsa = null; // Set expiration date to null
                } else {
                    $hargaDasar = $transaksi->harga / max($transaksi->jumlah, 1);
                    $markupPercent = optional($transaksi->areaPembelian)->markup ?? 0;
                    $hargaSatuan = $hargaDasar + ($hargaDasar * $markupPercent / 100);

                    $laba = Laba::where('input_minimal', '<=', $hargaSatuan)
                        ->where('input_maksimal', '>=', $hargaSatuan)
                        ->first();
                    $hargaJual = $laba ? $laba->harga_jual : 0;
                    $tanggalKadaluarsa = $transaksi->tanggal_kadaluarsa; // Get the expiration date from the latest transaction
                }

                // Merge stock data into the item
                $barang->stok_saat_ini = $stokSaatIni;
                $barang->harga_satuan = $hargaSatuan;
                $barang->harga_jual = $hargaJual;
                $barang->kuantitas = $stok->kuantitas;
                $barang->keterangan = $stok->keterangan ?? '-';
                $barang->tanggal_kadaluarsa = $tanggalKadaluarsa; // Add the expiration date
                $barang->id_stok_warung = $stok->id;
            } else {
                // Item has no stock in this stall
                $barang->stok_saat_ini = 0;
                $barang->harga_satuan = 0;
                $barang->harga_jual = 0;
                $barang->kuantitas = collect();
                $barang->keterangan = '-';
                $barang->tanggal_kadaluarsa = null; // Set expiration date to null
            }

            return $barang;
        });

        return view('admin.warung.show', compact('warung', 'barangWithStok', 'stokWarung'));
    }
}

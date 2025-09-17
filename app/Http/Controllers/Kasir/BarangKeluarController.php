<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\BarangKeluar;
use App\Models\StokWarung;
use App\Models\Laba;
use App\Models\User;
use Illuminate\Http\Request;

class BarangKeluarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $barang_keluar = BarangKeluar::whereHas('stokWarung', function ($q) {
            $q->where('id_warung', session('id_warung'));
        })
            ->with(['stokWarung.barang']) // biar langsung load barangnya juga
            ->latest()
            ->get();

        return view('barangkeluar.index', compact('barang_keluar'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::where('role', 'member')->get();
        $stok_warungs = StokWarung::where('id_warung', session('id_warung'))
            ->with(['barang.transaksiBarang.areaPembelian', 'kuantitas'])
            ->get();

        $stok_warungs->transform(function ($stok) {
            // Hitung stok saat ini
            $stokMasuk = $stok->barangMasuk()
                ->where('status', 'terima')
                ->whereHas('stokWarung', fn($q) => $q->where('id_warung', session('id_warung')))
                ->sum('jumlah');

            $stokKeluar = $stok->barangKeluar()
                ->whereHas('stokWarung', fn($q) => $q->where('id_warung', session('id_warung')))
                ->sum('jumlah');

            $mutasiMasuk = $stok->mutasiBarang()
                ->where('status', 'terima')
                ->whereHas('stokWarung', fn($q) => $q->where('id_warung', session('id_warung')))
                ->sum('jumlah');

            $mutasiKeluar = $stok->mutasiBarang()
                ->where('status', 'keluar')
                ->whereHas('stokWarung', fn($q) => $q->where('id_warung', session('id_warung')))
                ->sum('jumlah');

            $stokSaatIni = $stokMasuk + $mutasiMasuk - $mutasiKeluar - $stokKeluar;
            $stok->stok_saat_ini = $stokSaatIni;

            // Ambil transaksi terbaru
            $transaksi = $stok->barang->transaksiBarang()->latest()->first();

            if (!$transaksi) {
                $stok->harga_jual = 0;
                $stok->kuantitas_list = [];
                return $stok;
            }

            // Harga dasar = total beli / jumlah
            $hargaDasar = $transaksi->harga / max($transaksi->jumlah, 1);

            // Tambahkan markup dari area pembelian
            $markupPercent = optional($transaksi->areaPembelian)->markup ?? 0;
            $hargaSatuan = $hargaDasar + ($hargaDasar * $markupPercent / 100);

            // Ambil harga_jual dari tabel Laba
            $laba = Laba::where('input_minimal', '<=', $hargaSatuan)
                ->where('input_maksimal', '>=', $hargaSatuan)
                ->first();

            $stok->harga_jual = $laba ? $laba->harga_jual : 0;

            // Daftar kuantitas (bundle)
            $stok->kuantitas_list = $stok->kuantitas->map(fn($k) => [
                'jumlah' => $k->jumlah,
                'harga_jual' => $k->harga_jual,
            ]);

            return $stok;
        });

        return view('barangkeluar.create', compact('stok_warungs', 'users'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // validasi
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id_stok_warung' => 'required|exists:stok_warung,id',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.harga' => 'required|numeric|min:0',
            'jenis' => 'required|in:penjualan,hutang',
            'id_user_member' => 'nullable|exists:users,id',
            'bayar' => 'nullable|numeric|min:0',
            'total_harga' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
            'tenggat' => 'nullable|date',
        ]);

        $idWarung = session('id_warung');
        if (! $idWarung) {
            return redirect()->route('kasir.kasir')->with('error', 'ID warung tidak ditemukan di sesi.');
        }

        try {
            // mulai DB transaction
            \Illuminate\Support\Facades\DB::beginTransaction();

            // ambil kas warung jenis cash
            $kasWarung = \App\Models\KasWarung::where('id_warung', $idWarung)
                ->where('jenis_kas', 'cash')
                ->firstOrFail();

            // buat transaksi kas (total ambil dari input)
            $transaksiKas = \App\Models\TransaksiKas::create([
                'id_kas_warung'     => $kasWarung->id,
                'total'             => $validated['total_harga'],
                'metode_pembayaran' => $request->jenis === 'penjualan' ? 'cash' : null,
                'jenis'             => $validated['jenis'],
                'keterangan'        => $validated['keterangan'] ?? null,
            ]);

            // jika hutang, buat record hutang dulu (untuk di-link ke barang)
            $hutang = null;
            if ($validated['jenis'] === 'hutang' && ! empty($validated['id_user_member'])) {
                $hutang = \App\Models\Hutang::create([
                    'id_warung'    => $idWarung,
                    'id_user'      => $validated['id_user_member'],
                    'jumlah_pokok' => $validated['total_harga'],
                    'tenggat'      => $validated['tenggat'] ?? now()->addDays(7),
                    'status'       => 'belum lunas',
                    'keterangan'   => $validated['keterangan'] ?? null,
                ]);
            }

            // loop simpan barang keluar + transaksi barang keluar
            foreach ($validated['items'] as $item) {
                $barangKeluar = \App\Models\BarangKeluar::create([
                    'id_stok_warung' => $item['id_stok_warung'],
                    'jumlah'         => $item['jumlah'],
                    'jenis'          => $validated['jenis'],
                    'keterangan'     => $validated['keterangan'] ?? null,
                ]);

                $transaksiBarangKeluar = \App\Models\TransaksiBarangKeluar::create([
                    'id_transaksi_kas' => $transaksiKas->id,
                    'id_barang_keluar' => $barangKeluar->id,
                    'jumlah'           => $item['jumlah'], // qty, sesuai request
                ]);

                // kalau hutang, insert relasi ke barang_hutang menggunakan id_barang_keluar
                if ($hutang) {
                    \App\Models\BarangHutang::create([
                        'id_hutang'        => $hutang->id,
                        'id_barang_keluar' => $barangKeluar->id, // <- sesuai perubahanmu
                    ]);
                }
            }

            \Illuminate\Support\Facades\DB::commit();

            return redirect()->route('kasir.kasir')->with('success', 'Transaksi berhasil disimpan!');
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            \Log::error('Error menyimpan transaksi kasir: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan transaksi: ' . $e->getMessage());
        }
    }

    public function show(BarangKeluar $barangKeluar)
    {
        return view('barangkeluar.show', compact('barangKeluar'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BarangKeluar $barangKeluar)
    {
        $barangKeluar->delete();

        return redirect()->route('barangkeluar.index')
            ->with('success', 'Transaksi barang keluar berhasil dihapus!');
    }
}

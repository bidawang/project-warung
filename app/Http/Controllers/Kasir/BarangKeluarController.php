<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\BarangKeluar;
use App\Models\StokWarung;
use App\Models\Laba;
use App\Models\User;
use App\Models\KasWarung;
use App\Models\TransaksiKas;
use App\Models\Hutang;
use App\Models\TransaksiBarangKeluar;
use Illuminate\Support\Facades\DB;
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
        // dd('masuk sini');
        // Validasi data input
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id_stok_warung' => 'required|exists:stok_warung,id',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.harga' => 'required|numeric|min:0',
            'jenis' => 'required|in:penjualan barang,hutang barang',
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

        // --- LOGIKA GENERASI KETERANGAN OTOMATIS DIMULAI DI SINI ---

        // 1. Ambil ID Stok Warung yang terlibat
        $stokWarungIds = collect($validated['items'])->pluck('id_stok_warung')->all();

        // 2. Ambil detail barang dari StokWarung (termasuk nama barang)
        // ASUMSI: StokWarung memiliki relasi 'barang' ke model Barang yang memiliki kolom 'nama'.
        $stokDetails = StokWarung::whereIn('id', $stokWarungIds)
            ->with('barang') // Memuat relasi Barang
            ->get();

        // 3. Buat string deskripsi barang
        $itemDescriptions = $stokDetails->map(function ($stok) use ($validated) {
            // Cari jumlah barang yang terjual/dihutang dari array items
            $itemData = collect($validated['items'])->firstWhere('id_stok_warung', $stok->id);
            $jumlah = $itemData['jumlah'];

            // Ambil nama barang (menggunakan fallback jika relasi barang tidak ada atau nama kosong)
            $namaBarang = optional($stok->barang)->nama_barang ?? 'Barang ID: ' . $stok->id;

            return "{$namaBarang} ({$jumlah} Pcs)";
        })->implode(', ');
// dd($itemDescriptions);
        // 4. Tentukan Keterangan Otomatis
        $jenisTransaksi = $validated['jenis'];
        $tipe = $jenisTransaksi === 'penjualan barang' ? 'penjualan barang' : 'hutang barang';
        $defaultKeterangan = "{$tipe} Barang: {$itemDescriptions}";

        // 5. Tentukan Keterangan Final: Gunakan input user, jika kosong gunakan yang otomatis.
        $finalKeterangan = $validated['keterangan'] ?? $defaultKeterangan;

        // --- LOGIKA GENERASI KETERANGAN OTOMATIS SELESAI DI SINI ---

        try {
            // Mulai DB transaction
            DB::beginTransaction();

            // Ambil kas warung jenis cash
            $kasWarung = KasWarung::where('id_warung', $idWarung)
                ->where('jenis_kas', 'cash')
                ->firstOrFail();

            // Buat transaksi kas
            $transaksiKas = TransaksiKas::create([
                'id_kas_warung'     => $kasWarung->id,
                'total'             => $validated['total_harga'],
                // Hanya set metode_pembayaran jika jenisnya penjualan (bukan hutang)
                'metode_pembayaran' => $validated['jenis'] === 'penjualan barang' ? 'cash' : null,
                'jenis'             => $validated['jenis'],
                // Keterangan menggunakan hasil otomatis/manual
                'keterangan'        => $finalKeterangan,
            ]);

            // Jika hutang, buat record Hutang.
            $hutang = null;
            if ($validated['jenis'] === 'hutang barang' && ! empty($validated['id_user_member'])) {
                $hutang = Hutang::create([
                    'id_warung'      => $idWarung,
                    'id_user'        => $validated['id_user_member'],
                    'jumlah_hutang_awal' => $validated['total_harga'],
                    'jumlah_sisa_hutang'   => $validated['total_harga'],
                    'tenggat'        => $validated['tenggat'] ?? now()->addDays(7),
                    'status'         => 'belum lunas',
                    // Keterangan Hutang menggunakan hasil otomatis/manual
                    'keterangan'     => $finalKeterangan,
                ]);
            }

            // Loop untuk menyimpan barang keluar + transaksi barang keluar
            foreach ($validated['items'] as $item) {
                $barangKeluar = BarangKeluar::create([
                    'id_stok_warung' => $item['id_stok_warung'],
                    'jumlah'         => $item['jumlah'],
                    'jenis'          => $validated['jenis'],
                    'keterangan'     => $validated['keterangan'] ?? null, // Keterangan di BarangKeluar tetap menggunakan input user asli (bisa null) atau disamakan dengan finalKeterangan
                ]);

                TransaksiBarangKeluar::create([
                    'id_transaksi_kas' => $transaksiKas->id,
                    'id_barang_keluar' => $barangKeluar->id,
                    'jumlah'           => $item['jumlah'],
                ]);

                // Kurangi stok di tabel stok_warung
                StokWarung::where('id', $item['id_stok_warung'])
                    ->where('id_warung', $idWarung)
                    ->decrement('jumlah', $item['jumlah']);

                // Jika hutang, buat relasi ke barang_hutang
                if ($hutang) {
                    \App\Models\BarangHutang::create([
                        'id_hutang'          => $hutang->id,
                        'id_barang_keluar'   => $barangKeluar->id,
                    ]);
                }
            }
            DB::commit();

            return redirect()->route('kasir.kasir')->with('success', 'Transaksi berhasil disimpan!');
        } catch (\Throwable $e) {
            DB::rollBack();
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

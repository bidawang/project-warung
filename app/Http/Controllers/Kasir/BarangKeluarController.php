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
use App\Models\UangPelanggan;
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
        $validated = $request->validate([
            'items'                     => 'required|array|min:1',
            'items.*.id_stok_warung'     => 'required|exists:stok_warung,id',
            'items.*.jumlah'            => 'required|integer|min:1',
            'items.*.harga'             => 'required|numeric|min:0',

            'jenis'                     => 'required|in:penjualan barang,hutang barang',
            'id_user_member'            => 'nullable|exists:users,id',

            'total_harga'               => 'required|numeric|min:0',
            'uang_dibayar'              => 'nullable|numeric|min:0',
            'uang_kembalian'            => 'nullable|numeric|min:0',

            'keterangan'                => 'nullable|string',
            'tenggat'                   => 'nullable|date',
        ]);

        $idWarung = session('id_warung');
        if (! $idWarung) {
            return redirect()->route('kasir.kasir')
                ->with('error', 'ID warung tidak ditemukan di sesi.');
        }

        /**
         * ==========================================
         * GENERATE KETERANGAN OTOMATIS
         * ==========================================
         */
        $stokIds = collect($validated['items'])->pluck('id_stok_warung');

        $stokDetails = StokWarung::with('barang')
            ->whereIn('id', $stokIds)
            ->get();

        $itemDescriptions = $stokDetails->map(function ($stok) use ($validated) {
            $item = collect($validated['items'])
                ->firstWhere('id_stok_warung', $stok->id);

            $nama = optional($stok->barang)->nama_barang ?? 'Barang';
            return "{$nama} ({$item['jumlah']} pcs)";
        })->implode(', ');

        $defaultKeterangan = ($validated['jenis'] === 'penjualan barang'
            ? 'Penjualan Barang'
            : 'Hutang Barang') . ': ' . $itemDescriptions;

        $finalKeterangan = $validated['keterangan'] ?? $defaultKeterangan;

        /**
         * ==========================================
         * TRANSAKSI DATABASE
         * ==========================================
         */
        try {
            DB::beginTransaction();

            // Ambil kas warung CASH
            $kasWarung = KasWarung::where('id_warung', $idWarung)
                ->where('jenis_kas', 'cash')
                ->firstOrFail();

            // Simpan transaksi kas
            $transaksiKas = TransaksiKas::create([
                'id_kas_warung'     => $kasWarung->id,
                'total'             => $validated['total_harga'],
                'metode_pembayaran' => $validated['jenis'] === 'penjualan barang' ? 'cash' : null,
                'jenis'             => $validated['jenis'],
                'keterangan'        => $finalKeterangan,
            ]);

            /**
             * ==========================================
             * SIMPAN UANG PELANGGAN (KHUSUS PENJUALAN)
             * ==========================================
             */
            if ($validated['jenis'] === 'penjualan barang') {
                UangPelanggan::create([
                    'transaksi_id'   => $transaksiKas->id,
                    'uang_dibayar'   => $validated['uang_dibayar'] ?? 0,
                    'uang_kembalian' => $validated['uang_kembalian'] ?? 0,
                ]);
            }

            /**
             * ==========================================
             * HUTANG (JIKA ADA)
             * ==========================================
             */
            $hutang = null;

            if (
                $validated['jenis'] === 'hutang barang' &&
                !empty($validated['id_user_member'])
            ) {
                $hariIni = now()->day;

                $aturan = \App\Models\AturanTenggat::where('id_warung', $idWarung)
                    ->where('tanggal_awal', '<=', $hariIni)
                    ->where('tanggal_akhir', '>=', $hariIni)
                    ->first();

                $tenggat = $aturan
                    ? now()->addDays($aturan->jatuh_tempo_hari)
                    : now()->addDays(7);

                $hutang = Hutang::create([
                    'id_warung'          => $idWarung,
                    'id_user'            => $validated['id_user_member'],
                    'jumlah_hutang_awal' => $validated['total_harga'],
                    'jumlah_sisa_hutang' => $validated['total_harga'],
                    'tenggat'            => $tenggat,
                    'status'             => 'belum lunas',
                    'keterangan'         => $finalKeterangan,
                ]);
            }

            /**
             * ==========================================
             * BARANG KELUAR (SIMPAN HARGA JUAL 🔑)
             * ==========================================
             */
            foreach ($validated['items'] as $item) {

                $barangKeluar = BarangKeluar::create([
                    'id_stok_warung' => $item['id_stok_warung'],
                    'jumlah'         => $item['jumlah'],
                    'harga_jual'     => $item['harga'], // 🔥 PENTING
                    'jenis'          => $validated['jenis'],
                    'keterangan'     => $validated['keterangan'] ?? null,
                ]);

                TransaksiBarangKeluar::create([
                    'id_transaksi_kas' => $transaksiKas->id,
                    'id_barang_keluar' => $barangKeluar->id,
                    'jumlah'           => $item['jumlah'],
                ]);

                // Kurangi stok
                StokWarung::where('id', $item['id_stok_warung'])
                    ->where('id_warung', $idWarung)
                    ->decrement('jumlah', $item['jumlah']);

                // Relasi ke hutang (jika ada)
                if ($hutang) {
                    \App\Models\BarangHutang::create([
                        'id_hutang'        => $hutang->id,
                        'id_barang_keluar' => $barangKeluar->id,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('kasir.kasir')
                ->with('success', 'Transaksi berhasil disimpan!');
        } catch (\Throwable $e) {
            DB::rollBack();

            \Log::error('Error transaksi kasir', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return back()->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}

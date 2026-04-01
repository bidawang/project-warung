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
use App\Models\TransaksiKas;
use App\Models\HutangWarung;

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

    public function show(Request $request, $id)
    {
        // 1. Ambil Data Warung
        if (Auth::user()->role === 'admin') {
            $warung = Warung::with(['user', 'area'])->findOrFail($id);
        } else {
            $warung = Warung::with(['user', 'area'])
                ->where('id_user', Auth::id())
                ->findOrFail($id);
        }

        // 2. Ambil Semua Barang + relasi
        $allBarang = Barang::with(['transaksiBarang' => function ($q) {
            $q->latest()->with('areaPembelian');
        }])->get();

        $stokWarung = $warung->stokWarung()->with(['barang', 'kuantitas'])->get();
        $stokByBarangId = $stokWarung->keyBy('id_barang');

        // 3. Mapping Barang + Stok + Harga + Inflasi
        $barangWithStok = $allBarang->map(function ($barang) use ($stokByBarangId, $warung) {

            $stok = $stokByBarangId->get($barang->id);

            $hargaJual = HargaJual::where('id_warung', $warung->id)
                ->where('id_barang', $barang->id)
                ->where(fn($q) => $q->whereNull('periode_awal')->orWhere('periode_awal', '<=', now()))
                ->where(fn($q) => $q->whereNull('periode_akhir')->orWhere('periode_akhir', '>=', now()))
                ->latest('id')
                ->first();

            $barang->persentase_laba = $hargaJual ? $hargaJual->persentase_laba : 'N/A';

            // =========================
            // HITUNG INFLASI LABA
            // =========================
            $inflasiLaba = 0;

            if ($hargaJual) {
                $hargaSebelumnya = HargaJual::where('id_warung', $warung->id)
                    ->where('id_barang', $barang->id)
                    ->where('id', '<', $hargaJual->id)
                    ->latest('id')
                    ->first();

                if ($hargaSebelumnya) {
                    $marginNow = $hargaJual->harga_modal > 0
                        ? (($hargaJual->harga_jual_range_akhir - $hargaJual->harga_modal) / $hargaJual->harga_modal) * 100
                        : 0;

                    $marginOld = $hargaSebelumnya->harga_modal > 0
                        ? (($hargaSebelumnya->harga_jual_range_akhir - $hargaSebelumnya->harga_modal) / $hargaSebelumnya->harga_modal) * 100
                        : 0;

                    $inflasiLaba = $marginNow - $marginOld;
                }
            }

            $barang->inflasi_laba = $inflasiLaba;

            // Sorting
            $barang->persentase_laba_sort_key = ($hargaJual && $hargaJual->harga_modal > 0)
                ? (($hargaJual->harga_jual_range_awal - $hargaJual->harga_modal) / $hargaJual->harga_modal) * 100
                : 999999;

            // =========================
            // AMBIL RIWAYAT HARGA (MAX 3)
            // =========================
            $riwayatHarga = HargaJual::where('id_warung', $warung->id)
                ->where('id_barang', $barang->id)
                ->latest('id')
                ->take(3)
                ->get()
                ->reverse(); // biar urut lama → baru

            $barang->riwayat_harga = $riwayatHarga;

            // =========================
            // DATA STOK
            // =========================
            if ($stok) {
                $barang->stok_saat_ini = $stok->jumlah ?? 0;
                $barang->kuantitas = $stok->kuantitas ?? collect();
                $barang->keterangan = $stok->keterangan ?? '-';
                $barang->tanggal_kadaluarsa =
                    $stok->tanggal_kadaluarsa ??
                    optional($barang->transaksiBarang->first())->tanggal_kadaluarsa;
                $barang->id_stok_warung = $stok->id;
            } else {
                $barang->stok_saat_ini = 0;
                $barang->kuantitas = collect();
                $barang->keterangan = '-';
                $barang->tanggal_kadaluarsa = optional($barang->transaksiBarang->first())->tanggal_kadaluarsa;
                $barang->id_stok_warung = null;
            }

            // =========================
            // DATA HARGA
            // =========================
            $barang->harga_satuan = $hargaJual->harga_modal ?? 0;
            $barang->harga_jual = $hargaJual->harga_jual_range_akhir ?? 0;

            // TAMBAHKAN INI
            $barang->harga_jual_range_awal = $hargaJual->harga_jual_range_awal ?? 0;
            $barang->harga_jual_range_akhir = $hargaJual->harga_jual_range_akhir ?? 0;

            return $barang;
        })->sortBy('persentase_laba_sort_key')->values();

        // =========================
        // 4. HITUNG LABA (OPTIMIZED)
        // =========================
        $queryLaba = \App\Models\BarangKeluar::whereHas('stokWarung', function ($q) use ($warung) {
            $q->where('id_warung', $warung->id);
        })
            ->where('jenis', 'penjualan barang')
            ->with(['stokWarung.barang', 'stokWarung.hargaJual']);

        // 👉 ambil semua untuk summary (bukan paginate!)
        $allLaba = (clone $queryLaba)->get();
        // dd($allLaba);
        $labaKotor = $allLaba->sum(
            fn($item) =>
            $item->harga_jual
        );
        // dd($labaKotor);
        $labaBersih = $allLaba->sum(
            fn($item) =>
            $item->laba_bersih
        );
        // dd($labaBersih);
        $totalModal = $labaKotor - $labaBersih;

        // 👉 history (paginate untuk infinite scroll)
        $historyLaba = (clone $queryLaba)
            ->latest()
            ->paginate(10);

        if ($request->ajax()) {
            return view('admin.laporanlaba._item_history', compact('historyLaba'))->render();
        }


        $totalModal = $labaKotor - $labaBersih;

        // =========================
        // 5. ASSET
        // =========================
        $assets = \App\Models\Asset::with('pelunasan')
            ->where('id_warung', $warung->id)
            ->latest()
            ->get()
            ->map(function ($asset) {
                $asset->volume_pelunasan = $asset->pelunasan->count();
                return $asset;
            });

        // =========================
        // 6. PENGELUARAN BULAN INI
        // =========================
        $now = now();

        $pengeluaranPokokBulanIni = \App\Models\PengeluaranPokokWarung::where('id_warung', $warung->id)
            ->whereMonth('date', $now->month)
            ->whereYear('date', $now->year)
            ->latest()
            ->get();

        $totalPengeluaranPokok = $pengeluaranPokokBulanIni->sum('jumlah');
        // dd($labaBersih, $labaKotor, $totalModal);

        // =========================
        // 7. HUTANG PELANGGAN (30 USER TERAKHIR - PER WARUNG)
        // =========================
        $queryHutang = \App\Models\Hutang::with(['user'])
            ->where('id_warung', $warung->id)

            ->select('id_user')
            ->selectRaw('SUM(jumlah_hutang_awal) as total_awal')
            ->selectRaw('SUM(jumlah_sisa_hutang) as total_sisa')
            ->selectRaw('COUNT(*) as total_nota')

            // 🔥 penting: ambil aktivitas terakhir
            ->selectRaw('MAX(created_at) as last_trx')

            ->groupBy('id_user')

            // 🔥 urut berdasarkan transaksi terakhir
            ->orderByDesc('last_trx')

            ->limit(30); // 🔥 ambil 30 user saja


        $hutangList = $queryHutang->get();


        // =========================
        // STATISTIK
        // =========================
        $totalHutang = $hutangList->sum('total_awal');
        $totalSisa   = $hutangList->sum('total_sisa');
        $totalLunas  = $totalHutang - $totalSisa;
        // =========================
        // 8. RIWAYAT TRANSAKSI (50 TERAKHIR)
        // =========================
        $kasWarung = $warung->kasWarung()->first();

        $riwayatTransaksi = collect();

        if ($kasWarung) {
            $riwayatTransaksi = \App\Models\TransaksiKas::with([
                'transaksiBarangKeluar.barangKeluar.stokWarung.barang',
                'hutang',
                'uangPelanggan',
                'barangKeluar'
            ])
                ->where('id_kas_warung', $kasWarung->id)
                ->latest()
                ->limit(50) // 🔥 ini kuncinya
                ->get()
                ->map(function ($trx) use ($warung) {
                    $data = $this->transformTransaksi($trx);
                    $data->id_warung   = $warung->id;
                    $data->nama_warung = $warung->nama_warung;
                    return $data;
                });
        }

        // =========================
        // 9. HUTANG BARANG MASUK (DASHBOARD)
        // =========================
        $hutangBarangMasuk = HutangWarung::with([
            'hutangBarangMasuk.barangMasuk.transaksiBarang.barang'
        ])
            ->where('jenis', 'barang masuk')
            ->where('id_warung', $warung->id)
            ->latest()
            ->limit(10)
            ->get();
            
        $totalHutangBarangMasuk = HutangWarung::where('jenis', 'barang masuk')
            ->where('id_warung', $warung->id)
            ->sum('total');

        // dd($hutangBarangMasuk);
        // =========================
        // RETURN
        // =========================
        return view('admin.warung.show', compact(
            'warung',
            'barangWithStok',
            'stokWarung',
            'labaKotor',
            'labaBersih',
            'totalModal',
            'historyLaba',
            'assets',
            'pengeluaranPokokBulanIni',
            'totalPengeluaranPokok',
            'hutangList',
            'totalHutang',
            'totalSisa',
            'totalLunas',
            'riwayatTransaksi',
            'hutangBarangMasuk',
            'totalHutangBarangMasuk'
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

    protected function transformTransaksi(TransaksiKas $trx)
    {
        // LABEL DEFAULT
        $jenisLabel = 'Kas Umum';
        $metode     = $trx->metode_pembayaran ?? 'N/A';
        $deskripsi  = $trx->keterangan ?? '-';

        // JENIS TRANSAKSI
        switch ($trx->jenis) {
            case 'penjualan barang':
                $jenisLabel = 'Penjualan Barang';
                $metode     = $trx->metode_pembayaran ?? 'Cash';
                break;

            case 'hutang barang':
                $jenisLabel = 'Piutang Barang';
                $metode     = 'Piutang';
                break;

            case 'masuk':
                if ($trx->hutang) {
                    $jenisLabel = 'Pelunasan Piutang';
                    $metode     = 'Pelunasan';
                } else {
                    $jenisLabel = 'Kas Masuk';
                }
                break;

            case 'keluar':
                $jenisLabel = 'Kas Keluar';
                break;

            case 'expayet':
            case 'hilang':
                $jenisLabel = 'Kerugian Stok';
                break;
        }

        // TOTAL (+ / -)
        $isKeluar = in_array($trx->jenis, ['keluar', 'expayet', 'hilang']);
        $total    = $isKeluar ? -$trx->total : $trx->total;

        // ITEMS STRUK
        $items = [];

        foreach ($trx->transaksiBarangKeluar as $tbk) {
            $bk     = $tbk->barangKeluar;
            $barang = optional(optional($bk->stokWarung)->barang);

            $qty   = $bk->jumlah ?? 1;
            $harga = $bk->harga_jual ?? 0;

            $items[] = (object) [
                'nama_barang' => $barang->nama_barang ?? '-',
                'jumlah'      => $qty,
                'harga'       => $harga,
                'subtotal'    => $harga,
            ];
        }

        // DESKRIPSI OTOMATIS
        if (empty($trx->keterangan) && count($items)) {
            $namaBarang = collect($items)->pluck('nama_barang')->implode(', ');
            $deskripsi  = "{$jenisLabel}: {$namaBarang}";
        }

        // UANG PELANGGAN
        $uangDibayar   = optional($trx->uangPelanggan)->uang_dibayar;
        $uangKembalian = optional($trx->uangPelanggan)->uang_kembalian;

        return (object) [
            'id_ref'            => 'TK-' . $trx->id,
            'tanggal'           => $trx->created_at,
            'jenis_transaksi'   => $jenisLabel,
            'deskripsi'         => $deskripsi,
            'items'             => $items,
            'total'             => number_format($total, 2, '.', ''),
            'uang_dibayar'      => $uangDibayar !== null
                ? number_format($uangDibayar, 2, '.', '')
                : null,
            'uang_kembalian'    => $uangKembalian !== null
                ? number_format($uangKembalian, 2, '.', '')
                : null,
            'metode_pembayaran' => $metode,
            'tipe_sumber'       => 'TransaksiKas',
        ];
    }
}

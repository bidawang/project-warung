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
use App\Models\BarangKeluar;
use App\Models\Asset;
use App\Models\Hutang;
use App\Models\TransaksiPulsaKeluar;
use App\Models\DetailKasWarung;
use App\Models\TransaksiPulsaMasuk;


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
        // ==================================================
        // FILTER PERIODE (SIKLUS 7 s/d 6)
        // ==================================================
        // Kita ambil referensi bulan/tahun dari request atau saat ini
        $refDate = $request->periode ? Carbon::parse($request->periode) : now();

        // Logika: Jika user pilih "Mei 2024", maka periode adalah 7 Mei 2024 s/d 6 Juni 2024
        $startDate = Carbon::createFromDate($refDate->year, $refDate->month, 7)->startOfDay();
        $endDate = $startDate->copy()->addMonth()->subDay()->endOfDay();

        // ==================================================
        // 1. AMBIL WARUNG
        // ==================================================
        $warungQuery = Warung::with(['user', 'area', 'kasWarung']);
        if (Auth::user()->role !== 'admin') {
            $warungQuery->where('id_user', Auth::id());
        }
        $warung = $warungQuery->findOrFail($id);
        // ==================================================
        // 2. AMBIL DATA DASAR
        // ==================================================
        $allBarang = Barang::with([
            'transaksiBarang' => function ($q) {
                $q->latest()->with('areaPembelian');
            }
        ])->get();

        $stokWarung = $warung->stokWarung()
            ->with(['barang', 'kuantitas'])
            ->get();

        $stokByBarangId = $stokWarung->keyBy('id_barang');

        // ==================================================
        // 3. PRELOAD SEMUA HARGA JUAL
        // ==================================================
        $allHargaJual = HargaJual::where('id_warung', $warung->id)
            ->where(
                fn($q) =>
                $q->whereNull('periode_awal')
                    ->orWhere('periode_awal', '<=', now())
            )
            ->where(
                fn($q) =>
                $q->whereNull('periode_akhir')
                    ->orWhere('periode_akhir', '>=', now())
            )
            ->latest('id')
            ->get()
            ->groupBy('id_barang');

        // ==================================================
        // 4. MAPPING BARANG
        // ==================================================
        $barangWithStok = $allBarang->map(function ($barang) use (
            $stokByBarangId,
            $allHargaJual
        ) {

            $stok = $stokByBarangId->get($barang->id);

            // ==============================================
            // HARGA
            // ==============================================
            $hargaList = $allHargaJual->get($barang->id, collect());

            $hargaJual = $hargaList->first();

            $hargaSebelumnya = $hargaList->skip(1)->first();

            $riwayatHarga = $hargaList
                ->take(3)
                ->reverse()
                ->values();

            // ==============================================
            // INFLASI LABA
            // ==============================================
            $inflasiLaba = 0;

            if ($hargaJual && $hargaSebelumnya) {

                $marginNow = $hargaJual->harga_modal > 0
                    ? (
                        ($hargaJual->harga_jual_range_akhir - $hargaJual->harga_modal)
                        / $hargaJual->harga_modal
                    ) * 100
                    : 0;

                $marginOld = $hargaSebelumnya->harga_modal > 0
                    ? (
                        ($hargaSebelumnya->harga_jual_range_akhir - $hargaSebelumnya->harga_modal)
                        / $hargaSebelumnya->harga_modal
                    ) * 100
                    : 0;

                $inflasiLaba = $marginNow - $marginOld;
            }

            // ==============================================
            // SORT KEY
            // ==============================================
            $persentaseLabaSortKey = ($hargaJual && $hargaJual->harga_modal > 0)
                ? (
                    ($hargaJual->harga_jual_range_awal - $hargaJual->harga_modal)
                    / $hargaJual->harga_modal
                ) * 100
                : 999999;

            // ==============================================
            // STOK
            // ==============================================
            $stokSaatIni = $stok->jumlah ?? 0;

            $tanggalKadaluarsa =
                $stok->tanggal_kadaluarsa
                ?? optional($barang->transaksiBarang->first())->tanggal_kadaluarsa;

            // ==============================================
            // ASSIGN
            // ==============================================
            $barang->stok_saat_ini = $stokSaatIni;

            $barang->kuantitas = $stok->kuantitas ?? collect();

            $barang->keterangan = $stok->keterangan ?? '-';

            $barang->tanggal_kadaluarsa = $tanggalKadaluarsa;

            $barang->id_stok_warung = $stok->id ?? null;

            $barang->harga_satuan = $hargaJual->harga_modal ?? 0;

            $barang->harga_jual = $hargaJual->harga_jual_range_akhir ?? 0;

            $barang->harga_jual_range_awal =
                $hargaJual->harga_jual_range_awal ?? 0;

            $barang->harga_jual_range_akhir =
                $hargaJual->harga_jual_range_akhir ?? 0;

            $barang->persentase_laba = (float) (
                $hargaJual->persentase_laba ?? 0
            );

            $barang->inflasi_laba = $inflasiLaba;

            $barang->persentase_laba_sort_key =
                $persentaseLabaSortKey;

            $barang->riwayat_harga = $riwayatHarga;

            return $barang;
        })
            ->sortBy('persentase_laba_sort_key')
            ->values();

        // ==================================================
        // 5. QUERY LABA (GANTI whereMonth JADI whereBetween)
        // ==================================================
        $queryLaba = BarangKeluar::whereHas('stokWarung', function ($q) use ($warung) {
            $q->where('id_warung', $warung->id);
        })
            ->whereIn('jenis', ['penjualan barang', 'hutang barang'])
            ->whereBetween('created_at', [$startDate, $endDate]) // Diubah
            ->with(['stokWarung.barang', 'stokWarung.hargaJual']);

        $allLaba = (clone $queryLaba)->get();

        // ==================================================
        // LABA PULSA
        // ==================================================
        $allLabaPulsa = TransaksiPulsaKeluar::whereHas('transaksiKas.kasWarung', function ($q) use ($warung) {
            $q->where('id_warung', $warung->id);
        })
            ->whereBetween('created_at', [$startDate, $endDate]) // Diubah
            ->get();
        // dd($allLabaPulsa, $warung->id);
        // ==================================================
        // CASH BARANG
        // ==================================================
        $labaCashBarang = $allLaba
            ->where('jenis', 'penjualan barang');

        $totalPenjualanCashBarang =
            $labaCashBarang->sum('harga_jual');

        $totalLabaCashBarang =
            $labaCashBarang->sum('laba_bersih');

        $totalModalCashBarang =
            $totalPenjualanCashBarang
            - $totalLabaCashBarang;
        // ==================================================
        // CASH PULSA
        // ==================================================
        $labaCashPulsa = $allLabaPulsa
            ->where('jenis_pembayaran', 'cash');

        $totalPenjualanCashPulsa =
            $labaCashPulsa->sum('total');

        $totalLabaCashPulsa =
            $labaCashPulsa->sum('laba_pulsa');

        $totalAdjustmentCashPulsa =
            $labaCashPulsa->sum('laba_adjustment');

        $totalModalCashPulsa =
            $totalPenjualanCashPulsa
            - $totalLabaCashPulsa;
        // dd($totalPenjualanCashPulsa, $totalLabaCashPulsa, $totalModalCashPulsa);
        // ==================================================
        // TOTAL CASH
        // ==================================================
        $totalPenjualanCash =
            $totalPenjualanCashBarang + $totalPenjualanCashPulsa;

        $totalLabaCash =
            $totalLabaCashBarang + $totalLabaCashPulsa;

        $totalModalCash =
            $totalModalCashBarang + $totalModalCashPulsa;


        // ==================================================
        // 7. HUTANG
        // ==================================================
        $labaHutang = $allLaba->where('jenis', 'hutang barang');

        $totalPenjualanHutangBarang = $labaHutang->sum('harga_jual');

        $totalLabaHutangBarang = $labaHutang->sum('laba_bersih');

        $totalModalHutangBarang =
            $totalPenjualanHutangBarang - $totalLabaHutangBarang;

        // ==================================================
        // HUTANG PULSA
        // ==================================================
        $labaHutangPulsa = $allLabaPulsa
            ->where('jenis_pembayaran', 'hutang');
        // dd($labaHutangPulsa);
        $totalPenjualanHutangPulsa =
            $labaHutangPulsa->sum('total');

        $totalLabaHutangPulsa =
            $labaHutangPulsa->sum('laba_pulsa');

        $totalAdjustmentHutangPulsa =
            $labaHutangPulsa->sum('laba_adjustment');

        $totalModalHutangPulsa =
            $totalPenjualanHutangPulsa
            - $totalLabaHutangPulsa;
        // ==================================================
        // TOTAL HUTANG
        // ==================================================
        $totalPenjualanHutang =
            $totalPenjualanHutangBarang + $totalPenjualanHutangPulsa;

        $totalLabaHutang =
            $totalLabaHutangBarang + $totalLabaHutangPulsa;

        $totalModalHutang =
            $totalModalHutangBarang + $totalModalHutangPulsa;

        // ==================================================
        // RINGKASAN LABA PULSA
        // ==================================================
        $totalLabaPulsa =
            $totalLabaCashPulsa + $totalLabaHutangPulsa;

        $totalPenjualanPulsa =
            $totalPenjualanCashPulsa + $totalPenjualanHutangPulsa;

        $totalModalPulsa =
            $totalPenjualanPulsa - $totalLabaPulsa;


        // ==================================================
        // 8. TOTAL
        // ==================================================
        $labaKotor = $totalPenjualanCash + $totalPenjualanHutang;

        $labaBersih = $totalLabaCash + $totalLabaHutang;

        $totalModal = $labaKotor - $labaBersih;

        $margin = $totalPenjualanCash > 0
            ? ($totalLabaCash / $totalPenjualanCash) * 100
            : 0;

        // ==================================================
        // 9. ASSET
        // ==================================================
        $assets = Asset::with('pelunasan')
            ->where('id_warung', $warung->id)
            ->latest()
            ->get()
            ->map(function ($asset) {

                $asset->volume_pelunasan =
                    $asset->pelunasan->count();

                return $asset;
            });

        // ==================================================
        // 10. PENGELUARAN BULAN INI
        // ==================================================
        $pengeluaranPokokBulanIni = PengeluaranPokokWarung::where('id_warung', $warung->id)
            ->whereBetween('date', [$startDate, $endDate]) // Diubah (pastikan kolom 'date' sesuai)
            ->latest()
            ->get();

        $totalPengeluaranPokok = $pengeluaranPokokBulanIni->sum('jumlah');

        // ==================================================
        // 11. HUTANG PELANGGAN
        // ==================================================
        $hutangList = Hutang::with(['user'])
            ->where('id_warung', $warung->id)
            ->whereBetween('created_at', [$startDate, $endDate]) // Diubah
            ->select('id_user')
            ->selectRaw('SUM(jumlah_hutang_awal) as total_awal')
            ->selectRaw('SUM(jumlah_sisa_hutang) as total_sisa')
            ->selectRaw('COUNT(*) as total_nota')
            ->selectRaw('MAX(created_at) as last_trx')
            ->groupBy('id_user')
            ->orderByDesc('last_trx')
            ->limit(30)
            ->get();

        $totalHutang = $hutangList->sum('total_awal');

        $totalSisa = $hutangList->sum('total_sisa');

        $totalLunas = $totalHutang - $totalSisa;

        // ==================================================
        // 12. RIWAYAT TRANSAKSI
        // ==================================================
        $kasWarung = $warung->kasWarung()->first();
        $riwayatTransaksi = collect();

        if ($kasWarung) {
            $riwayatTransaksi = TransaksiKas::with([
                'transaksiBarangKeluar.barangKeluar.stokWarung.barang',
                'hutang',
                'uangPelanggan',
                'barangKeluar'
            ])
                ->where('id_kas_warung', $kasWarung->id)
                ->whereBetween('created_at', [$startDate, $endDate]) // Diubah
                ->latest()
                ->limit(50)
                ->get()
                ->map(function ($trx) use ($warung) {
                    $data = $this->transformTransaksi($trx);
                    $data->id_warung = $warung->id;
                    $data->nama_warung = $warung->nama_warung;
                    return $data;
                });
        }

        // ==================================================
        // 13. HUTANG BARANG & PULSA MASUK
        // ==================================================
        $hutangBarangMasuk = HutangWarung::with(['hutangBarangMasuk.barangMasuk.transaksiBarang.barang'])
            ->where('jenis', 'barang masuk')
            ->where('id_warung', $warung->id)
            ->whereBetween('created_at', [$startDate, $endDate]) // Diubah
            ->latest()
            ->limit(10)
            ->get();

        $totalHutangBarangMasuk = HutangWarung::where('jenis', 'barang masuk')
            ->where('id_warung', $warung->id)
            ->whereBetween('created_at', [$startDate, $endDate]) // Diubah
            ->sum('total');

        $hutangPulsaMasuk = TransaksiPulsaMasuk::with(['pulsa.jenisPulsa', 'hutangWarung'])
            ->whereHas('pulsa', function ($q) use ($warung) {
                $q->where('id_warung', $warung->id);
            })
            ->whereNotNull('id_hutang_warung')
            ->whereBetween('created_at', [$startDate, $endDate]) // Diubah
            ->latest()
            ->limit(10)
            ->get();

        $totalHutangPulsaMasuk = $hutangPulsaMasuk->sum('total');

        // ==================================================
        // 14. STATISTIK KAS
        // ==================================================
        $kasCash = $warung->kasWarung->where('jenis_kas', 'cash')->first();
        $kasBank = $warung->kasWarung->where('jenis_kas', 'bank')->first();

        $pendapatanCashPeriode = 0;
        $pengeluaranCashPeriode = 0;
        $totalUangFisik = 0;
        $pecahanKas = collect();

        if ($kasCash) {
            $pendapatanCashPeriode = TransaksiKas::where('id_kas_warung', $kasCash->id)
                ->whereIn('jenis', ['penjualan barang', 'penjualan pulsa', 'masuk', 'inject'])
                ->whereBetween('created_at', [$startDate, $endDate]) // Diubah
                ->sum('total');

            $pengeluaranCashPeriode = TransaksiKas::where('id_kas_warung', $kasCash->id)
                ->whereIn('jenis', ['expayet', 'hilang', 'keluar', 'hutang barang', 'hutang pulsa'])
                ->whereBetween('created_at', [$startDate, $endDate]) // Diubah
                ->sum('total');

            $pecahanKas = DetailKasWarung::where('id_kas_warung', $kasCash->id)
                ->orderBy('pecahan', 'desc')->get();
            $totalUangFisik = $pecahanKas->sum(fn($item) => $item->pecahan * $item->jumlah);
        }

        // -- STATISTIK BANK --
        $pendapatanBankPeriode = 0;
        $pengeluaranBankPeriode = 0;

        // ==================================================
        // RETURN
        // ==================================================
        $periode = $startDate->format('d M') . ' - ' . $endDate->format('d M Y');

        return view('admin.warung.show', compact(
            'warung',
            'barangWithStok',
            'stokWarung',
            'periode',
            'startDate',
            'endDate',
            'labaKotor',
            'labaBersih',
            'totalModal',
            'totalPenjualanCash',
            'totalLabaCash',
            'totalModalCash',
            'totalPenjualanCashBarang',
            'totalLabaCashBarang',
            'totalModalCashBarang',
            'totalPenjualanHutang',
            'totalLabaHutang',
            'totalModalHutang',
            'totalPenjualanHutangBarang',
            'totalLabaHutangBarang',
            'totalModalHutangBarang',
            'margin',
            'assets',
            'pengeluaranPokokBulanIni',
            'totalPengeluaranPokok',
            'hutangList',
            'totalHutang',
            'totalSisa',
            'totalLunas',
            'riwayatTransaksi',
            'hutangBarangMasuk',
            'totalHutangBarangMasuk',
            'kasCash',
            'kasBank',
            'pendapatanCashPeriode',
            'pengeluaranCashPeriode',
            'pecahanKas',
            'totalUangFisik',
            'totalLabaPulsa',
            'totalPenjualanPulsa',
            'totalModalPulsa',
            'totalLabaCashPulsa',
            'totalPenjualanCashPulsa',
            'totalModalCashPulsa',
            'totalLabaHutangPulsa',
            'totalPenjualanHutangPulsa',
            'totalModalHutangPulsa',
            'hutangPulsaMasuk',
            'totalHutangPulsaMasuk',
            'totalAdjustmentCashPulsa',
            'totalAdjustmentHutangPulsa'
        ));
    }

    //Kemungkinan kda tepakai
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

<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\BarangKeluar; // Ditambahkan
use App\Models\BarangHutang; // Ditambahkan
use App\Models\HargaJual; // Ditambahkan (Jika belum ada)
use App\Models\StokWarung;
use App\Models\Laba;
use App\Models\User;
use App\Models\MutasiBarang; // Ditambahkan: Untuk menampilkan mutasi barang keluar

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Penting untuk memastikan semua proses transaksi berhasil (atomicity)

class KasirControllerKasir extends Controller
{
    /**
     * Menampilkan halaman kasir dengan daftar produk yang tersedia dan riwayat barang keluar.
     */
    public function index()
    {
        $idWarung = session('id_warung');

        if (!$idWarung) {
            return redirect()->route('kasir.kasir')
                ->with('error', 'ID warung tidak ditemukan di sesi.');
        }

        // --- DATA KANAN: Daftar Produk untuk Dijual (Stok Aktif) ---
        $stok_warungs = StokWarung::where('id_warung', $idWarung)
            ->with([
                'barang.transaksiBarang.areaPembelian',
                'kuantitas',
                'barang.hargaJual' // relasi ke tabel harga_jual
            ])
            ->get();

        $stok_warungs->transform(function ($stok) use ($idWarung) {
            // --- 1. Ambil stok langsung ---
            $stok->stok_saat_ini = $stok->jumlah;

            // --- 2. Ambil transaksi terbaru (opsional untuk tanggal kadaluarsa) ---
            $transaksi = $stok->barang->transaksiBarang()->latest()->first();

            // --- 3. Ambil harga jual langsung dari tabel harga_jual ---
            $hargaJual = HargaJual::where('id_warung', $idWarung)
                ->where('id_barang', $stok->barang->id)
                ->first();

            $stok->harga_jual = $hargaJual ? $hargaJual->harga_jual_range_akhir : 0;

            // --- 4. Tambahkan tanggal kadaluarsa (jika ada dari transaksi terakhir) ---
            $stok->tanggal_kadaluarsa = $transaksi->tanggal_kadaluarsa ?? null;

            // --- 5. Ambil daftar kuantitas (bundle) ---
            $stok->kuantitas_list = $stok->kuantitas
                ->sortByDesc('jumlah')
                ->values()
                ->map(fn($k) => [
                    'jumlah' => $k->jumlah,
                    'harga_jual' => $k->harga_jual,
                ]);

            return $stok;
        });

        // Filter hanya stok yang masih tersedia
        $products = $stok_warungs->filter(fn($stok) => $stok->stok_saat_ini > 0);

        // Ambil daftar member
        $members = User::where('role', 'member')->get();


        // --- DATA KIRI: Barang Keluar Terbaru (Penjualan, Mutasi, dll.) ---

        // 1. Ambil Barang Keluar (Penjualan/Rusak) dari warung ini
        $barangKeluars = BarangKeluar::whereHas('stokWarung', function ($query) use ($idWarung) {
            $query->where('id_warung', $idWarung);
        })
            ->with(['stokWarung.barang'])
            ->latest()
            ->limit(10) // Batasi 10 item terbaru untuk ringkasan di kasir
            ->get()
            ->map(function ($bk) {
                return [
                    'tanggal' => $bk->created_at,
                    'jenis' => $bk->jenis === 'penjualan' ? 'Penjualan' : ucwords($bk->jenis),
                    'nama_barang' => $bk->stokWarung->barang->nama_barang ?? 'N/A',
                    'jumlah' => $bk->jumlah,
                    'keterangan' => $bk->keterangan,
                ];
            });

        // 2. Ambil Mutasi Barang (sebagai barang keluar dari warung ini)
        $mutasiKeluars = MutasiBarang::where('warung_asal', $idWarung)
            ->where('status', 'disetujui') // Hanya mutasi yang sukses/disetujui
            ->with(['stokWarung.barang', 'warungTujuan'])
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($mk) {
                return [
                    'tanggal' => $mk->created_at,
                    'jenis' => 'Mutasi Keluar',
                    'nama_barang' => $mk->stokWarung->barang->nama_barang ?? 'N/A',
                    'jumlah' => $mk->jumlah,
                    'keterangan' => 'Ke: ' . ($mk->warungTujuan->nama_warung ?? 'Warung Lain'),
                ];
            });

        // 3. Gabungkan dan urutkan 10 transaksi gabungan terbaru
        $outgoingItems = $barangKeluars->merge($mutasiKeluars)
            ->sortByDesc('tanggal')
            ->take(10);

        // Melewatkan semua data ke view
        return view('kasir.kasir.index', compact('products', 'members', 'outgoingItems'));
    }

    /**
     * Memproses transaksi penjualan (Barang Keluar) dan memperbarui stok.
     * Menggunakan model BarangKeluar, TransaksiBarangKeluar, dan Laba.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $idWarung = session('id_warung');
        $items = $request->input('items');
        $totalBayar = $request->input('total_bayar');
        $idMember = $request->input('id_member');
        $jenisPembayaran = $request->input('jenis_pembayaran', 'tunai'); // 'tunai' atau 'hutang'
// dd($request->all());
        if (!$idWarung) {
            return back()->with('error', 'ID warung tidak ditemukan di sesi.');
        }

        // Memulai Database Transaction untuk memastikan data konsisten
        DB::beginTransaction();

        try {
            // --- 1. Logika Transaksi Kas Umum (Harus ada model TransaksiKas) ---
            // Contoh sederhana, Anda perlu mengimplementasikan model TransaksiKas
            // $transaksiKas = TransaksiKas::create([...]);
            // $idTransaksiKas = $transaksiKas->id;
            // $idTransaksiKas = 1; // ID sementara

            foreach ($items as $item) {
                $stokWarung = StokWarung::findOrFail($item['stok_warung_id']);
                $jumlahJual = $item['jumlah'];
                $hargaJualSatuan = $item['harga_jual'];

                // Dapatkan Harga Pokok Penjualan (HPP)
                // Ini penting untuk perhitungan Laba. HPP biasanya diambil dari harga beli/masuk.
                $hargaBeliTerakhir = $stokWarung->barang->transaksiBarang()->latest()->first()->harga_satuan ?? 0;
                $hppTotal = $hargaBeliTerakhir * $jumlahJual;
                $penjualanTotal = $hargaJualSatuan * $jumlahJual;
                $labaKotor = $penjualanTotal - $hppTotal;


                // 2. Catat Barang Keluar
                $barangKeluar = BarangKeluar::create([
                    'id_stok_warung' => $stokWarung->id,
                    'jumlah' => $jumlahJual,
                    'jenis' => 'penjualan', // Jenis: penjualan, rusak, dll.
                    'keterangan' => 'Penjualan kasir',
                ]);

                // 3. Catat detail transaksi berdasarkan Jenis Pembayaran
                if ($jenisPembayaran === 'tunai') {
                    // Transaksi tunai / non-hutang dicatat ke TransaksiBarangKeluar
                    // TransaksiBarangKeluar::create([
                    //     'id_transaksi_kas' => $idTransaksiKas,
                    //     'id_barang_keluar' => $barangKeluar->id,
                    //     'jumlah' => $penjualanTotal,
                    // ]);
                } elseif ($jenisPembayaran === 'hutang') {
                    // Validasi member wajib ada jika hutang
                    if (!$idMember) {
                        DB::rollBack();
                        return back()->with('error', 'Member harus dipilih untuk transaksi hutang.');
                    }
                    // Catat Hutang
                    // $hutang = Hutang::create(['id_user' => $idMember, 'total_hutang' => $penjualanTotal, ...]);
                    // BarangHutang::create([
                    //     'id_hutang' => $hutang->id,
                    //     'id_barang_keluar' => $barangKeluar->id,
                    // ]);
                }

                // 4. Catat Laba
                Laba::create([
                    'id_warung' => $idWarung,
                    'id_barang' => $stokWarung->id_barang,
                    'jenis' => 'penjualan',
                    'jumlah_laba' => $labaKotor,
                    'jumlah_barang' => $jumlahJual,
                    'harga_jual' => $hargaJualSatuan,
                    'hpp_satuan' => $hargaBeliTerakhir,
                ]);

                // 5. Update Stok Warung
                // Periksa stok sebelum dikurangi
                if ($stokWarung->jumlah < $jumlahJual) {
                    DB::rollBack();
                    return back()->with('error', 'Stok ' . $stokWarung->barang->nama . ' tidak mencukupi.');
                }
                $stokWarung->decrement('jumlah', $jumlahJual);
            }
            DB::commit();

            return redirect()->route('kasir.kasir')
                ->with('success', 'Transaksi penjualan berhasil diproses!');
        } catch (\Exception $e) {
            DB::rollBack();
            // Logging error untuk proses debug
            \Log::error('Kasir Transaction Failed: ' . $e->getMessage());

            return back()->with('error', 'Gagal memproses transaksi. Silakan coba lagi. Error: ' . $e->getMessage());
        }
    }
}

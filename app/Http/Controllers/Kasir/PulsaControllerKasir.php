<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pulsa;
use App\Models\HargaPulsa;
use Illuminate\Support\Facades\Log;
use App\Models\TransaksiPulsa;
use App\Models\User;
use App\Models\KasWarung;
use App\Models\Hutang;
use App\Models\TransaksiKas;
use Illuminate\Support\Facades\DB;

class PulsaControllerKasir extends Controller
{
    public function index()
    {
        $idWarung = session('id_warung');
        if (! $idWarung) {
            return redirect()->route('dashboard')->with('error', 'ID warung tidak ditemukan di sesi.');
        }


        // 1. Ambil Data Transaksi Pulsa (Transaksi yang sudah terjadi)
        // Disarankan menggunakan pagination jika data banyak:
        $pulsa = Pulsa::where('id_warung', $idWarung)->first();

        // 2. Ambil Daftar Harga Pulsa (Master data)
        $harga_pulsas = HargaPulsa::orderBy('jumlah_pulsa', 'asc')->get();
        $transaksi_pulsa = TransaksiPulsa::whereHas('pulsa', function ($query) use ($idWarung) {
            $query->where('id_warung', $idWarung);
        })->orderBy('created_at', 'desc')->get();

        // Untuk contoh ini, kita set nilai dummy atau 0 jika tidak ada
        $saldo_kas = 500000; // Contoh saldo 500.000

        // Kirim semua data ke view 'pulsa.index'
        return view('kasir.pulsa.index', compact('pulsa', 'harga_pulsas', 'saldo_kas', 'transaksi_pulsa'));
    }

    public function createHargaPulsa()
    {
        return view('kasir.pulsa.create_harga_pulsa');
    }

    public function storeHargaPulsa(Request $request)
    {
        // 1. Bersihkan nilai input dari titik atau koma pemisah ribuan
        // Nilai '20.000' atau '20,000' akan diubah menjadi '20000'.
        $jumlahPulsaBersih = str_replace(['.', ','], '', $request->jumlah_pulsa);
        $hargaBersih = str_replace(['.', ','], '', $request->harga);

        // Gabungkan nilai yang sudah bersih kembali ke Request untuk validasi
        $request->merge([
            'jumlah_pulsa' => $jumlahPulsaBersih,
            'harga'        => $hargaBersih,
        ]);

        // 2. Validasi Input
        // Sekarang, validasi akan menggunakan nilai numerik murni.
        $request->validate([
            // Pastikan Anda memvalidasi nilai yang sudah di-merge/bersih
            'jumlah_pulsa' => 'required|numeric|min:1000',
            'harga'        => 'required|numeric|min:1000',
        ]);

        // 3. Simpan ke Database
        // Menggunakan nilai yang sudah bersih (melalui $request->all())
        \App\Models\HargaPulsa::create($request->all());

        // 4. Redirect dengan Pesan Sukses
        return redirect()->route('kasir.pulsa.index')->with('success', 'Harga Pulsa baru berhasil ditambahkan!');
    }

    public function editHargaPulsa($id)
    {
        $hargaPulsa = HargaPulsa::findOrFail($id);

        $operators = ['TELKOMSEL', 'INDOSAT', 'XL', 'AXIS', 'TRI', 'SMARTFREN', 'UMUM'];

        return view('kasir.pulsa.edit_harga_pulsa', compact('hargaPulsa', 'operators'));
    }

    public function updateHargaPulsa(Request $request, $id)
    {
        // 1. Cari data yang akan diupdate
        $hargaPulsa = HargaPulsa::findOrFail($id);

        // 2. Bersihkan nilai input dari titik atau koma pemisah ribuan
        $jumlahPulsaBersih = str_replace(['.', ','], '', $request->jumlah_pulsa);
        $hargaBersih = str_replace(['.', ','], '', $request->harga);

        // Gabungkan nilai yang sudah bersih kembali ke Request untuk validasi
        $request->merge([
            'jumlah_pulsa' => $jumlahPulsaBersih,
            'harga'        => $hargaBersih,
        ]);

        // 3. Validasi Input
        $request->validate([
            'jumlah_pulsa' => 'required|numeric|min:1000',
            'harga'        => 'required|numeric|min:1000',
        ]);

        // 4. Update data di Database
        // Menggunakan nilai yang sudah bersih (melalui $request->all())
        $hargaPulsa->update($request->all());

        // 5. Redirect dengan Pesan Sukses
        return redirect()->route('kasir.pulsa.index')->with('success', 'Harga Pulsa berhasil diperbarui!');
    }

    public function createSaldoPulsa()
    {
        return view('kasir.pulsa.create_saldo_pulsa');
    }

    public function storeSaldoPulsa(Request $request)
    {
        $idWarung = session('id_warung');
        if (! $idWarung) {
            return redirect()->route('dashboard')->with('error', 'ID warung tidak ditemukan di sesi.');
        }

        // 1. Bersihkan nilai input dari titik atau koma pemisah ribuan
        $nominalBersih = str_replace(['.', ','], '', $request->nominal);

        // Gabungkan nilai yang sudah bersih kembali ke Request untuk validasi
        $request->merge([
            'nominal' => $nominalBersih,
        ]);

        // 2. Validasi Input
        $request->validate([
            'nominal' => 'required|numeric|min:1000',
        ]);

        // 3. Cari atau buat entri Pulsa untuk Warung ini
        // Asumsi model Pulsa memiliki kolom 'saldo'
        $pulsa = Pulsa::firstOrCreate(
            ['id_warung' => $idWarung],
            ['saldo' => 0] // Saldo awal jika baru dibuat
        );

        // 4. Tambahkan saldo
        $pulsa->increment('saldo', $request->nominal);

        // 5. Redirect dengan Pesan Sukses
        return redirect()->route('kasir.pulsa.index')->with('success', 'Saldo Pulsa Warung berhasil ditambahkan sebesar Rp ' . number_format($request->nominal, 0, ',', '.'));
    }

    public function createJualPulsa()
    {
        // Ambil daftar harga pulsa yang tersedia untuk dipilih
        $harga_pulsas = HargaPulsa::orderBy('jumlah_pulsa', 'asc')->get();

        // Ambil daftar pelanggan untuk opsi hutang
        $idWarung = session('id_warung');
        $pelanggans = User::where('role', 'member')->get();

        return view('kasir.pulsa.jual_pulsa', compact('harga_pulsas', 'pelanggans'));
    }

    // ... (di dalam PulsaControllerKasir.php)

    // ... (di dalam PulsaControllerKasir.php)

    public function storeJualPulsa(Request $request)
    {
        $idWarung = session('id_warung');
        if (! $idWarung) {
            return redirect()->route('dashboard')->with('error', 'ID warung tidak ditemukan di sesi.');
        }

        // 1. Validasi Input
        $rules = [
            'nomor_hp' => 'required|string|min:10|max:15',
            'harga_pulsa_id' => 'required|exists:harga_pulsa,id',
            'jenis_pembayaran' => 'required|in:penjualan pulsa,hutang pulsa', // OK, asumsikan ENUM di DB sudah diubah
            'bayar' => 'required_if:jenis_pembayaran,penjualan pulsa|numeric|min:0',
            // Asumsi 'pelanggan_id' adalah 'id_user' dari tabel 'users'
            'pelanggan_id' => 'required_if:jenis_pembayaran,hutang|nullable|exists:users,id',
        ];

        $request->validate($rules, [
            'pelanggan_id.required_if' => 'Pelanggan (User Member) wajib dipilih untuk transaksi hutang.',
            'bayar.required_if' => 'Uang bayar wajib diisi untuk transaksi tunai.',
        ]);

        $hargaPulsa = HargaPulsa::findOrFail($request->harga_pulsa_id);
        $hargaJual = $hargaPulsa->harga;
        $nominalPulsa = $hargaPulsa->jumlah_pulsa;
        $jenisPembayaran = $request->jenis_pembayaran;

        // Cek pembayaran kurang HANYA jika tunai
        if ($jenisPembayaran == 'penjualan pulsa' && $request->bayar < $hargaJual) {
            return back()->withInput()->withErrors(['bayar' => 'Jumlah bayar kurang dari harga jual pulsa (Rp ' . number_format($hargaJual, 0, ',', '.') . ')']);
        }

        // 2. Cek Saldo Pulsa Warung (Stok)
        $pulsaWarung = Pulsa::firstOrCreate(
            ['id_warung' => $idWarung],
            ['saldo' => 0]
        );

        if ($pulsaWarung->saldo < $nominalPulsa) {
            return back()->withInput()->with('error', 'Transaksi Gagal: Saldo Pulsa Warung tidak mencukupi untuk nominal Rp ' . number_format($nominalPulsa, 0, ',', '.'));
        }

        try {
            // Mulai DB transaction
            DB::beginTransaction();

            // Ambil kas warung jenis cash
            $kasWarung = KasWarung::where('id_warung', $idWarung)
                ->where('jenis_kas', 'cash')
                ->firstOrFail();

            // 3. Catat Hutang (Piutang Warung) jika jenis pembayaran adalah 'hutang'
            $hutang = null;
            if ($jenisPembayaran === 'hutang pulsa') {
                $hutang = Hutang::create([
                    'id_warung'          => $idWarung,
                    'id_user'            => $request->pelanggan_id,
                    'jumlah_hutang_awal' => $hargaJual,
                    'jumlah_sisa_hutang' => $hargaJual,
                    'tenggat'            => now()->addDays(7),
                    'status'             => 'belum lunas',
                    'keterangan'         => 'Hutang pulsa ' . number_format($nominalPulsa, 0, ',', '.') . ' ke ' . $request->nomor_hp,
                ]);
            }

            // 4. Buat Transaksi Kas
            $transaksiKas = TransaksiKas::create([
                'id_kas_warung'     => $kasWarung->id,
                'total'             => $hargaJual,
                'metode_pembayaran' => $jenisPembayaran === 'tunai' ? 'cash' : 'piutang',
                'jenis'             => $jenisPembayaran === 'tunai' ? 'penjualan pulsa' : 'hutang pulsa', // Sesuai ENUM transaksi_kas
                'keterangan'        => 'Penjualan pulsa ' . number_format($nominalPulsa, 0, ',', '.') . ' ke ' . $request->nomor_hp . ' (' . ucfirst($jenisPembayaran) . ')',
            ]);

            // 5. Kurangi Saldo Pulsa Warung (Stok Keluar)
            $pulsaWarung->decrement('saldo', $nominalPulsa);

            // 6. Hitung Profit dan Kembalian
            $profit = $hargaJual - $nominalPulsa;
            $uangBayar = $jenisPembayaran == 'tunai' ? $request->bayar : 0;
            $kembalian = $jenisPembayaran == 'tunai' ? ($uangBayar - $hargaJual) : 0;

            // 7. Simpan ke tabel transaksi_pulsa
            // 🚨 PERBAIKAN ERROR: id_kas_warung dan id_transaksi_kas diisi
            $tipeTransaksiPulsa = $jenisPembayaran === 'hutang pulsa' ? 'hutang_pulsa' : 'penjualan_pulsa';
            $transaksiPulsa = TransaksiPulsa::create([
                'id_pulsa' => $pulsaWarung->id,
                'id_kas_warung' => $kasWarung->id,
                'id_transaksi_kas' => $transaksiKas->id,
                'jumlah' => $nominalPulsa,
                'total' => $hargaJual,
                'jenis_pembayaran' => $jenisPembayaran, // Akan menjadi 'tunai' atau 'hutang'
                'jenis' => 'keluar',
                'tipe' => $tipeTransaksiPulsa, // <-- PERUBAHAN DI SINI
                'id_hutang' => $hutang ? $hutang->id : null,
            ]);

            // 8. UPDATE SALDO KAS (Hanya jika Tunai)
            if ($jenisPembayaran === 'tunai') {
                $kasWarung->increment('saldo', $hargaJual);
            }

            // Commit transaksi
            DB::commit();

            // 9. Redirect dengan Pesan Sukses
            $message = "Penjualan Pulsa ke " . $request->nomor_hp . " berhasil diproses. Nominal: Rp " . number_format($nominalPulsa, 0, ',', '.') .
                ", Harga Jual: Rp " . number_format($hargaJual, 0, ',', '.') .
                ". Jenis Pembayaran: **" . ucfirst($jenisPembayaran) . "**" .
                ($jenisPembayaran == 'tunai' ? ". Bayar: Rp " . number_format($uangBayar, 0, ',', '.') .
                    ". Kembalian: Rp " . number_format($kembalian, 0, ',', '.') : '') .
                ". Profit: Rp " . number_format($profit, 0, ',', '.');

            return redirect()->route('kasir.pulsa.index')->with('success', $message);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("Gagal melakukan transaksi pulsa: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            return back()->withInput()->with('error', 'Terjadi kesalahan saat memproses transaksi. Silakan coba lagi.');
        }
    }
}

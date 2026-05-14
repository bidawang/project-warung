<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pulsa;
use App\Models\HargaPulsa;
use Illuminate\Support\Facades\Log;
use App\Models\TransaksiPulsaKeluar;
use App\Models\User;
use App\Models\KasWarung;
use App\Models\Hutang;
use App\Models\TransaksiKas;
use Illuminate\Support\Facades\DB;
use App\Models\JenisPulsa;
use App\Models\SaldoPulsa;
use App\Models\Warung;


class PulsaControllerKasir extends Controller
{
    public function index()
    {
        $idWarung = session('id_warung');

        if (!$idWarung) {
            return redirect()->route('dashboard')->with('error', 'ID warung tidak ditemukan.');
        }

        // Ambil saldo pulsa per provider berdasarkan gambar ERD (id_jenis & jumlah)
        $saldoPulsas = SaldoPulsa::with('jenisPulsa')
            ->where('id_warung', $idWarung)
            ->get();

        $harga_pulsas = HargaPulsa::with('jenisPulsa')
            ->orderBy('jumlah_pulsa', 'asc')
            ->get();

        // Riwayat transaksi pulsa keluar
        $transaksi_pulsa = TransaksiPulsaKeluar::with(['saldoPulsa.jenisPulsa', 'transaksiKas'])
            ->whereHas('saldoPulsa', function ($query) use ($idWarung) {
                $query->where('id_warung', $idWarung);
            })
            ->latest()
            ->get();

        // Contoh saldo kas (sebaiknya ambil dari database KasWarung)
        $kas = KasWarung::where('id_warung', $idWarung)->where('jenis_kas', 'cash')->first();
        $saldo_kas = $kas ? $kas->saldo : 0;

        return view('kasir.pulsa.index', compact(
            'saldoPulsas',
            'harga_pulsas',
            'saldo_kas',
            'transaksi_pulsa'
        ));
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
        $idWarung = session('id_warung');

        // Ambil daftar harga pulsa beserta jenisnya (provider)
        $harga_pulsas = HargaPulsa::with('jenisPulsa')
            ->orderBy('jumlah_pulsa', 'asc') // Sesuai kolom 'jumlah' di ERD
            ->get();

        // Ambil daftar pelanggan untuk opsi hutang
        $pelanggans = User::where('role', 'member')->get();

        $jenisPulsa = JenisPulsa::all();

        return view('kasir.pulsa.jual_pulsa', compact('harga_pulsas', 'pelanggans', 'jenisPulsa'));
    }

    public function storeJualPulsa(Request $request)
    {
        $idWarung = session('id_warung');

        if (!$idWarung) {
            return redirect()->route('dashboard')->with('error', 'ID warung tidak ditemukan.');
        }

        // 1. Validasi Input (Gunakan Enum: cash, hutang sesuai permintaan sebelumnya)
        $request->validate([
            'nomor_hp'         => 'required|string|max:15',
            'harga_pulsa_id'   => 'required|exists:harga_pulsa,id',
            'jenis_pembayaran' => 'required|in:cash,hutang',
            'bayar'            => 'required_if:jenis_pembayaran,cash|nullable|numeric|min:0',
            'pelanggan_id'     => 'required_if:jenis_pembayaran,hutang|nullable|exists:users,id',
        ]);

        // 2. Ambil Data Harga & Provider
        $hargaData = HargaPulsa::findOrFail($request->harga_pulsa_id);
        $nominalPulsa = $hargaData->jumlah_pulsa;
        $idJenis = $hargaData->id_jenis;

        // Tentukan harga jual (Cash pakai harga_jual, Hutang pakai harga_hutang)
        $totalHargaJual = ($request->jenis_pembayaran === 'cash')
            ? $hargaData->harga_jual
            : $hargaData->harga_hutang;

        // 3. Cek Saldo berdasarkan Provider (id_jenis) dan Warung
        $saldo = SaldoPulsa::where('id_warung', $idWarung)
            ->where('id_jenis', $idJenis)
            ->first();

        if (!$saldo || $saldo->jumlah < $nominalPulsa) {
            return back()->withInput()->with('error', 'Saldo Provider tidak mencukupi.');
        }

        // 4. Validasi Uang Bayar jika Cash
        if ($request->jenis_pembayaran === 'cash' && $request->bayar < $totalHargaJual) {
            return back()->withInput()->withErrors([
                'bayar' => 'Uang bayar kurang. Harga: Rp ' . number_format($totalHargaJual, 0, ',', '.')
            ]);
        }

        try {
            DB::beginTransaction();

            $kas = KasWarung::where('id_warung', $idWarung)->where('jenis_kas', 'cash')->firstOrFail();

            // 5. Logika Hutang
            $idHutang = null;
            if ($request->jenis_pembayaran === 'hutang') {
                $hutang = Hutang::create([
                    'id_warung'          => $idWarung,
                    'id_user'            => $request->pelanggan_id,
                    'jumlah_hutang_awal' => $totalHargaJual,
                    'jumlah_sisa_hutang' => $totalHargaJual,
                    'tenggat'            => now()->addDays(7),
                    'status'             => 'belum lunas',
                    'keterangan'         => "Hutang pulsa {$nominalPulsa} ke {$request->nomor_hp}",
                ]);
                $idHutang = $hutang->id;
            }

            // 6. Transaksi Kas
            $transaksiKas = TransaksiKas::create([
                'id_kas_warung'     => $kas->id,
                'total'             => $totalHargaJual,
                'metode_pembayaran' => ($request->jenis_pembayaran === 'cash') ? 'cash' : 'piutang',
                'jenis'             => 'masuk',
                'keterangan'        => "Jual pulsa {$nominalPulsa} ke {$request->nomor_hp} ({$request->jenis_pembayaran})",
            ]);

            // 7. Kalkulasi Laba (Harga Jual - Modal Alomogada)
            $profitTotal = $totalHargaJual - $hargaData->harga_alomogada;
            $warung = Warung::findOrFail($idWarung);
            $pembagian = explode('|', $warung->pembagian_laba);

            $labaOwner = ceil($profitTotal * (($pembagian[0] ?? 50) / 100));
            $labaWarung = ceil($profitTotal * (($pembagian[1] ?? 50) / 100));

            // 8. Simpan ke TransaksiPulsaKeluar (Sesuai Model & ERD terbaru)
            TransaksiPulsaKeluar::create([
                'id_pulsa'         => $saldo->id, // Mengarah ke ID di tabel saldo_pulsa
                'id_transaksi_kas' => $transaksiKas->id,
                'id_harga_pulsa'   => $hargaData->id,
                'jenis_pembayaran' => $request->jenis_pembayaran, // cash / hutang
                'total'            => $totalHargaJual,
                'laba_pulsa'       => $profitTotal,
                'laba_owner'       => $labaOwner,
                'laba_warung'      => $labaWarung,
                // Jika Anda menambahkan kolom id_hutang di migrasi TransaksiPulsaKeluar:
                // 'id_hutang'     => $idHutang, 
            ]);

            // 9. Potong Saldo di tabel saldo_pulsa
            $saldo->decrement('jumlah', $nominalPulsa);

            // 10. Update Kas Fisik jika Cash
            if ($request->jenis_pembayaran === 'cash') {
                $kas->increment('saldo', $totalHargaJual);
            }

            DB::commit();

            return redirect()->route('kasir.pulsa.index')->with('success', 'Transaksi Pulsa Berhasil Disimpan!');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("Gagal Jual Pulsa: " . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan sistem.');
        }
    }
}

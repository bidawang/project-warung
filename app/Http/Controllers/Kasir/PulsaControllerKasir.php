<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pulsa;
use App\Models\HargaPulsa;

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

        // 3. Ambil Saldo Kas Warung (Asumsi ini diambil dari tabel Warung atau Kas)
        // **CONTOH SIMPLIFIKASI:** Anda harus menyesuaikan ini dengan cara Anda menyimpan saldo.
        // Misalnya, ambil saldo dari tabel Warung yang sedang login.
        // $saldo_kas = Warung::find(auth()->user()->id_warung)->saldo;

        // Untuk contoh ini, kita set nilai dummy atau 0 jika tidak ada
        $saldo_kas = 500000; // Contoh saldo 500.000

        // Kirim semua data ke view 'pulsa.index'
        return view('kasir.pulsa.index', compact('pulsa', 'harga_pulsas', 'saldo_kas'));
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
}

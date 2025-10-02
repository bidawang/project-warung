<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\StokWarung;
use App\Models\RencanaBelanja;
use App\Models\Barang;

class RencanaBelanjaControllerKasir extends Controller
{
    /**
     * Tampilkan view index yang berisi stok barang di warung kasir saat ini.
     *
     * @return \Illuminate\View\View
     */
    public function rencanaBelanja()
    {
        // dd(Auth::user());
        // PENTING: ID Warung harus diambil secara dinamis dari user yang login (Kasir).
        $idWarung = session('id_warung');

        $rencanaBelanjaAktif = RencanaBelanja::with('barang')
            ->where('id_warung', $idWarung)
            ->where(function ($query) {
                $query->whereNull('jumlah_dibeli')
                    ->orWhere('jumlah_dibeli', 0);
                //   ->orWhereColumn('status','pending');
            })
            ->get();

        return view('kasir.rencana_belanja.index', compact('rencanaBelanjaAktif'));
    }

    /**
     * Tampilkan view history (riwayat) Rencana Belanja yang sudah selesai (completed).
     *
     * @return \Illuminate\View\View
     */
    public function history()
    {
        // ID Warung ambil dari session kasir login
        $idWarung = session('id_warung');

        $historyRencanaBelanja = RencanaBelanja::with('barang')
            ->where('id_warung', $idWarung)
            ->where('jumlah_dibeli', '>', 0)
            ->get();

        return view('kasir.rencana_belanja.history', compact('historyRencanaBelanja'));
    }


    /**
     * Tampilkan form untuk membuat Rencana Belanja baru.
     * Mengambil daftar semua barang yang tersedia di stok warung, diurutkan dari stok 0.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $idWarung = session('id_warung');

        // Ambil semua barang, lalu join stok warung berdasarkan id_warung
        $stokBarang = Barang::with(['stokWarung' => function ($q) use ($idWarung) {
            $q->where('id_warung', $idWarung);
        }])
            ->orderBy('nama_barang', 'asc') // urut sesuai nama barang
            ->get();

        // dd($stokBarang);
        return view('kasir.rencana_belanja.create', compact('stokBarang'));
    }


    /**
     * Simpan Rencana Belanja baru ke database (Multiple Items).
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // dd($request->all());
        // PENTING: ID Warung harus diambil secara dinamis dari user yang login (Kasir).
        $idWarung = session('id_warung');

        // Validasi array input dari form
        $validatedData = $request->validate([
            'rencana' => 'required|array',
            'rencana.*.id_barang' => 'required|exists:barang,id',
            'rencana.*.jumlah_awal' => 'nullable|min:0', // Izinkan 0 untuk item yang tidak dipilih
        ]);
        // dd($validatedData);
        $itemsToInsert = [];
        $totalItems = 0;

        // Loop melalui data yang tervalidasi
        foreach ($validatedData['rencana'] as $item) {
            $jumlahAwal = (int) $item['jumlah_awal'] ?? 0;

            // Logika "nge lock": Hanya masukkan item yang memiliki jumlah_awal > 0
            if ($jumlahAwal > 0) {
                $itemsToInsert[] = [
                    'id_warung' => $idWarung,
                    'id_barang' => $item['id_barang'],
                    'jumlah_awal' => $jumlahAwal,
                    'jumlah_dibeli' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $totalItems++;
            }
        }

        if ($totalItems === 0) {
            return redirect()->back()->withInput()->with('error', 'Tidak ada barang dengan jumlah rencana beli yang valid (minimal 1) yang disubmit.');
        }

        try {
            // Gunakan Mass Insert untuk efisiensi
            RencanaBelanja::insert($itemsToInsert);

            return redirect()->route('kasir.rencanabelanja.index')->with('success', $totalItems . ' Rencana belanja berhasil ditambahkan!');
        } catch (\Exception $e) {
            \Log::error("Gagal menyimpan Rencana Belanja Massal: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan rencana belanja. Silakan coba lagi.');
        }
    }
}

<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\StokWarung;
use App\Models\RencanaBelanja;
use App\Models\BarangMasuk; // Pastikan model ini diimport
use App\Models\Barang;
use App\Models\Warung;


class RencanaBelanjaControllerKasir extends Controller
{
    /**
     * Tampilkan view index yang berisi stok barang di warung kasir saat ini.
     *
     * @return \Illuminate\View\View
     */
    public function rencanaBelanja(Request $request)
{
    $idWarung = session('id_warung');
    $status = $request->query('status', 'pending');

    // Memuat relasi 'barang' dan 'barangMasuk'
    $query = RencanaBelanja::with(['barang'])
        ->where('id_warung', $idWarung);

    switch ($status) {
        case 'pending':
            $query->where(function ($q) {
                $q->whereNull('jumlah_dibeli')
                  ->orWhere('jumlah_dibeli', 0);
            });
            break;

        case 'dibeli':
            $query->where('jumlah_dibeli', '>', 0)
                  ->where('status', 'dibeli');
            break;

        case 'dikirim':
            $query->where('status', 'dikirim');
            break;

        case 'selesai':
            // Pada status selesai, kita pastikan data barang masuk juga ikut terambil
            $query->where('status', 'selesai');
            break;
    }

    $data = $query->get();
// dd($data);
    return view('kasir.rencana_belanja.index', [
        'data' => $data,
        'status' => $status
    ]);
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
// dd($idWarung);
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
                    'jumlah_diterima' => 0,
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

    public function konfirmasiSelesai(Request $request)
{
    // 1. Validasi Input
    $request->validate([
        'barangMasuk' => 'required|array',
        'barangMasuk.*' => 'exists:rencana_belanja,id',
        'status_baru' => 'required|in:selesai,tolak',
    ]);
// dd($request->All());
    try {
        DB::beginTransaction();

        // Ambil data rencana belanja yang dipilih
        $items = RencanaBelanja::whereIn('id', $request->barangMasuk)->get();
// dd($request->All());
        if ($request->status_baru === 'selesai') {
            foreach ($items as $item) {
                // A. Update status di tabel rencana_belanja
                $item->update(['status' => 'selesai']);

                $jumlahDibeli = (float) $item->jumlah_dibeli;
// dd($jumlahDibeli, $item->id_stok_warung);
                if ($jumlahDibeli > 0) {
                    // B. Tambahkan ke tabel barang_masuk (Histori Barang Masuk)
                    
    //                 BarangMasuk::where('id_stok_warung', $request->idstokwarung)
    // ->update([
    //     'status'     => 'terima',
    //     // 'keterangan' => 'Masuk dari Rencana Belanja',
    // ]);

                    // C. Update/Increment stok di tabel stok_warung
                    StokWarung::where('id_warung', session('id_warung'))
                                ->where('id_barang', $item->id_barang)
                        ->increment('jumlah', $jumlahDibeli);
                }
            }
            // dd($request->All(), $item->jumlah_dibeli,session('id_warung'));
            $message = 'Rencana belanja diselesaikan, histori barang masuk dicatat, dan stok berhasil ditambahkan.';
        } else {
            // Jika status_baru adalah 'tolak'
            RencanaBelanja::whereIn('id', $request->barangMasuk)->update(['status' => 'tolak']);
            $message = 'Rencana belanja yang dipilih telah ditolak.';
        }

        DB::commit();
        
        // Redirect kembali ke tab selesai
        return redirect()->route('kasir.rencanabelanja.index', ['status' => 'selesai'])
                         ->with('success', $message);

    } catch (Exception $e) {
        DB::rollBack();
        Log::error("Gagal konfirmasi rencana belanja: " . $e->getMessage());

        return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}
}
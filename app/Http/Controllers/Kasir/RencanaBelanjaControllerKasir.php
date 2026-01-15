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
        // dd($status);
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

        $barangPage = Barang::with([
            'stokWarung' => fn($s) => $s->where('id_warung', $idWarung),
            'satuan',
            'subKategori'
        ])
            ->orderBy('nama_barang')
            ->paginate(100);

        return view('kasir.rencana_belanja.create', compact('barangPage'));
    }


    public function searchBarang(Request $request)
    {
        $idWarung = session('id_warung');
        $q = trim($request->q ?? '');

        return Barang::with([
            'stokWarung' => fn($s) => $s->where('id_warung', $idWarung),
            'satuan',
            'subKategori'
        ])
            ->when(
                $q !== '',
                fn($qq) =>
                $qq->where('nama_barang', 'like', "%{$q}%")
            )
            ->orderBy('nama_barang')
            ->limit(40)
            ->get()
            ->map(fn($b) => [
                'id'   => $b->id,
                'nama' => $b->nama_barang,
                'stok' => optional($b->stokWarung->first())->jumlah ?? 0,
                'kategori' => $b->subKategori->sub_kategori ?? '-',
                'satuan' => $b->satuan->map(fn($s) => [
                    'nama' => $s->nama_satuan,
                    'qty'  => $s->jumlah
                ])
            ]);
    }



    /**
     * Simpan Rencana Belanja baru ke database (Multiple Items).
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $idWarung = session('id_warung');

        if (!$idWarung) {
            return back()->with('error', 'ID warung tidak ditemukan.');
        }

        // 1️⃣ Ambil & decode JSON
        $rencana = json_decode($request->rencana_json, true);

        if (!is_array($rencana) || count($rencana) === 0) {
            return back()->withInput()->with('error', 'Tidak ada rencana belanja.');
        }

        $itemsToInsert = [];

        // 2️⃣ Loop data JSON
        foreach ($rencana as $idBarang => $item) {

            $jumlahAwal = (int) ($item['total'] ?? 0);

            if ($jumlahAwal <= 0) {
                continue;
            }

            $itemsToInsert[] = [
                'id_warung'         => $idWarung,
                'id_barang'         => $idBarang,
                'jumlah_awal'       => $jumlahAwal,
                'jumlah_dibeli'     => 0,
                'jumlah_diterima'   => 0,
                'created_at'        => now(),
                'updated_at'        => now(),
            ];
        }

        // 3️⃣ Guard
        if (count($itemsToInsert) === 0) {
            return back()->withInput()->with('error', 'Jumlah rencana harus lebih dari 0.');
        }

        // 4️⃣ Simpan mass insert
        try {
            RencanaBelanja::insert($itemsToInsert);

            return redirect()
                ->route('kasir.rencanabelanja.index')
                ->with('success', count($itemsToInsert) . ' rencana belanja berhasil disimpan.');
        } catch (\Throwable $e) {
            \Log::error('Rencana Belanja Error: ' . $e->getMessage());

            return back()->withInput()->with('error', 'Gagal menyimpan rencana belanja.');
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

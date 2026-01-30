<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KasWarung;
use App\Models\TransaksiKas;
use App\Models\Warung;
use Illuminate\Support\Facades\DB;

class InjectKasControllerAdmin extends Controller
{
    public function index()
    {

        // Menampilkan riwayat inject kas terbaru
        $riwayat = TransaksiKas::with('kasWarung.warung')
            ->where('jenis', 'inject')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.inject_kas.index', compact('riwayat'));
    }

    public function create()
    {
        $warungs = Warung::all();
        return view('admin.inject_kas.create', compact('warungs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_warung' => 'required|exists:warung,id',
            'jenis_kas' => 'required|in:cash,bank',
            'total' => 'required|numeric',
            'keterangan' => 'required|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            // 1. Cari atau Buat KasWarung tersebut
            $kas = KasWarung::firstOrCreate(
                ['id_warung' => $request->id_warung, 'jenis_kas' => $request->jenis_kas],
                ['saldo' => 0]
            );

            if($request->total > 0){
                $numerik = '[Inject';
            } else {
                $numerik = '[Pengeluaran';
            }

            if($request->jenis_kas === 'bank'){
                $keterangan = $numerik . ' Saldo Bank]';
            } else {
                $keterangan = $numerik . ' Saldo Cash]';
            }
            // 2. Buat Log Transaksi
            TransaksiKas::create([
                'id_kas_warung' => $kas->id,
                'total' => $request->total,
                'metode_pembayaran' => $request->jenis_kas,
                'jenis' => 'inject',
                'keterangan' => $keterangan . ' ' . $request->keterangan,
            ]);

            // 3. Update Saldo di Tabel Kas Warung
            $kas->updateSaldo($request->total, 'tambah');

            DB::commit();
            return redirect()->route('admin.inject-kas.index')->with('success', 'Saldo berhasil disuntikkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KasWarung;
use App\Models\TransaksiKas;
use App\Models\Warung;
use App\Models\HutangWarung;
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
// dd('asu');
        DB::beginTransaction();
        try {
            // 1. Cari atau Buat KasWarung tersebut
            $kas = KasWarung::firstOrCreate(
                ['id_warung' => $request->id_warung, 'jenis_kas' => $request->jenis_kas],
                ['saldo' => 0]
            );

            // Penentuan prefix keterangan
            $prefix = $request->total > 0 ? '[Inject' : '[Pengeluaran';
            $keteranganLengkap = $prefix . ($request->jenis_kas === 'bank' ? ' Saldo Bank] ' : ' Saldo Cash] ') . $request->keterangan;

            // 2. Buat Log Transaksi Kas
            TransaksiKas::create([
                'id_kas_warung'     => $kas->id,
                'total'             => $request->total,
                'metode_pembayaran' => $request->jenis_kas,
                'jenis'             => 'inject',
                'keterangan'        => $keteranganLengkap,
            ]);

            // 3. Update Saldo di Tabel Kas Warung
            $kas->updateSaldo($request->total, 'tambah');

            // 4. TAMBAHAN: Buat Hutang Warung (Jenis: inject)
            // Kita catat total inject sebagai hutang yang berstatus 'belum lunas'
            HutangWarung::create([
                'id_warung' => $request->id_warung,
                'total'     => $request->total,
                'jenis'     => 'inject '.$request->jenis_kas,
                'status'    => 'belum lunas', // Status awal saat saldo disuntikkan
                // Jika ada kolom keterangan di tabel hutang_warung, tambahkan di sini:
                // 'keterangan' => $keteranganLengkap,
            ]);

            DB::commit();
            return redirect()->route('admin.inject-kas.index')->with('success', 'Saldo berhasil disuntikkan dan dicatat sebagai hutang!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }
}

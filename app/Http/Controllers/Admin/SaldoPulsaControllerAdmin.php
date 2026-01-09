<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pulsa;
use App\Models\Warung;
use App\Models\TransaksiPulsa;
use App\Models\HutangBarangMasuk;
use Illuminate\Support\Facades\DB;

class SaldoPulsaControllerAdmin extends Controller
{
    public function index()
    {
        $pulsas = Pulsa::with('warung')->paginate(10);
        return view('admin.saldo_pulsa.index', compact('pulsas'));
    }

    public function create()
    {
        // Mengambil semua data warung untuk ditampilkan di dropdown.
        $warungs = Warung::orderBy('nama_warung')->get();
        return view('admin.saldo_pulsa.create', compact('warungs'));
    }

    public function store(Request $request)
    {
        // 1. Validasi Data
        $request->validate([
            'id_warung' => 'required|exists:warung,id',
            'jenis' => 'required|in:hp,listrik',
            'nominal' => 'required|integer|min:1',
            'harga_beli' => 'required|numeric|min:1', // Input baru dari form
        ]);
        // dd('Validated');
        try {
            DB::beginTransaction();

            // 2. Cari atau Buat Record Saldo Pulsa
            $pulsa = Pulsa::firstOrNew([
                'id_warung' => $request->id_warung,
                'jenis' => $request->jenis,
            ]);

            $pulsa->saldo += $request->nominal;
            $pulsa->save();

            // 3. Simpan ke tabel transaksi_pulsa
            TransaksiPulsa::create([
                'id_pulsa' => $pulsa->id,
                'id_kas_warung' => null, // Kosong karena ini transaksi admin/top-up
                // 'id_transaksi_kas' => null,
                'jumlah' => $request->nominal,
                'total' => $request->harga_beli,
                'jenis' => 'masuk',
                'tipe' => 'Top Up Pulsa', // Instruksi: tipe Top Up Pulsa
            ]);
            // dd('TransaksiPulsa created');
            // 4. Catat ke HutangBarangMasuk (Kewajiban warung ke pusat)
            HutangBarangMasuk::create([
                'id_warung' => $request->id_warung,
                'id_barang_masuk' => null, // Ini pulsa, bukan barang fisik
                'total' => $request->harga_beli,
                'status' => 'belum lunas',
            ]);
            // dd('HutangBarangMasuk created');
            DB::commit();

            $jenisLabel = $request->jenis == 'hp' ? 'Handphone' : 'Listrik';
            return redirect()->route('admin.saldo-pulsa.index')->with(
                'success',
                "Saldo pulsa **{$jenisLabel}** sebesar Rp" . number_format($request->nominal, 0, ',', '.') . " berhasil ditambahkan."
            );
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses top-up: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        // Ambil data pulsa beserta info warungnya
        $pulsa = Pulsa::with('warung')->findOrFail($id);

        // Ambil riwayat transaksi khusus tipe 'Top Up Pulsa' untuk id_pulsa ini
        $riwayatTopUp = TransaksiPulsa::where('id_pulsa', $id)
            ->where('tipe', 'Top Up Pulsa')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.saldo_pulsa.show', compact('pulsa', 'riwayatTopUp'));
    }
}

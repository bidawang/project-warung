<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pulsa;
use App\Models\Warung;
use App\Models\TransaksiPulsa;
use App\Models\HutangBarangMasuk;
use Illuminate\Support\Facades\DB;
use App\Models\JenisPulsa;

class SaldoPulsaControllerAdmin extends Controller
{
    public function index()
    {
        $pulsas = Pulsa::with('warung')->paginate(10);

        return view('admin.saldo_pulsa.index', compact('pulsas'));
    }

    public function create()
    {
        $jenisPulsa = JenisPulsa::orderBy('nama_jenis')->get();
        // Mengambil semua data warung untuk ditampilkan di dropdown.
        $warungs = Warung::orderBy('nama_warung')->get();
        return view('admin.saldo_pulsa.create', compact('warungs', 'jenisPulsa'));
    }

    public function store(Request $request)
    {
        // 1. Validasi
        $request->validate([
            'id_warung'        => 'required|exists:warung,id',
            'jenis_pulsa_id'   => 'required|exists:jenis_pulsa,id',
            'nominal'          => 'required|integer|min:1',
            'harga_beli'       => 'required|numeric|min:1',
        ]);

        try {
            DB::beginTransaction();

            // 2. Cari atau buat saldo pulsa per WARUNG + JENIS PULSA
            $pulsa = Pulsa::firstOrNew([
                'id_warung'       => $request->id_warung,
                'jenis_pulsa_id'  => $request->jenis_pulsa_id,
            ]);

            // default saldo kalau record baru
            if (! $pulsa->exists) {
                $pulsa->saldo = 0;
                $pulsa->harga_pulsa = 0;
            }

            $pulsa->saldo += $request->nominal;
            $pulsa->harga_pulsa = $request->harga_beli;
            $pulsa->save();

            // 3. Simpan transaksi pulsa
            TransaksiPulsa::create([
                'id_pulsa'        => $pulsa->id,
                'id_kas_warung'   => null,
                'jumlah'          => $request->nominal,
                'total'           => $request->harga_beli,
                'jenis'           => 'masuk',
                'tipe'            => 'Top Up Pulsa',
            ]);

            // 4. Catat hutang warung ke pusat
            HutangBarangMasuk::create([
                'id_warung'         => $request->id_warung,
                'id_barang_masuk'   => null,
                'total'             => $request->harga_beli,
                'status'            => 'belum lunas',
            ]);

            DB::commit();

            // ambil nama jenis pulsa (buat notifikasi)
            $jenisPulsa = JenisPulsa::find($request->jenis_pulsa_id);

            return redirect()
                ->route('admin.saldo-pulsa.index')
                ->with(
                    'success',
                    "Saldo pulsa **{$jenisPulsa->nama_jenis}** sebesar Rp" .
                        number_format($request->nominal, 0, ',', '.') .
                        " berhasil ditambahkan."
                );
        } catch (\Throwable $e) {
            DB::rollBack();
            dd($e);
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

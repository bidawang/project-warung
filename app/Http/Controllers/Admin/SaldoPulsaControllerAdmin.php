<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\SaldoPulsa;
use App\Models\Warung;
use App\Models\JenisPulsa;
use App\Models\HargaPulsa;
use App\Models\Pulsa;
use App\Models\HutangWarung;
use App\Models\TransaksiPulsaMasuk;
use App\Models\TransaksiPulsaKeluar;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SaldoPulsaControllerAdmin extends Controller
{
    /**
     * =========================================================================
     * LIST SALDO PULSA WARUNG
     * =========================================================================
     */
    public function index(Request $request)
    {
        $search = $request->search;

        $pulsas = SaldoPulsa::with([
            'warung',
            'jenisPulsa'
        ])
            ->when($search, function ($query) use ($search) {

                $query->whereHas('warung', function ($q) use ($search) {
                    $q->where('nama_warung', 'like', "%{$search}%");
                })

                    ->orWhereHas('jenisPulsa', function ($q) use ($search) {
                        $q->where('nama_jenis', 'like', "%{$search}%");
                    });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view(
            'admin.saldo_pulsa.index',
            compact('pulsas')
        );
    }

    /**
     * =========================================================================
     * FORM TAMBAH SALDO
     * =========================================================================
     */
    public function create()
    {
        $jenisPulsa = JenisPulsa::orderBy('nama_jenis')
            ->get();

        $warungs = Warung::orderBy('nama_warung')
            ->get();

        return view(
            'admin.saldo_pulsa.create',
            compact('warungs', 'jenisPulsa')
        );
    }

    /**
     * =========================================================================
     * STORE
     * =========================================================================
     * SUDAH DIPERBAIKI USER
     */
    public function store(Request $request)
    {
        // Bersihkan format angka
        $nominal = (int) str_replace(['.', ','], '', $request->nominal);
        $hargaBeli = (int) str_replace(['.', ','], '', $request->harga_beli);
        $hargaJual = (int) str_replace(['.', ','], '', $request->harga_jual);

        // Validasi
        $request->validate([
            'id_warung'       => 'required|exists:warung,id',
            'jenis_pulsa_id' => 'required|exists:jenis_pulsa,id',
            'nominal'         => 'required|numeric|min:1000',
            'harga_beli'      => 'required|numeric|min:1000',
            'harga_jual'      => 'required|numeric|min:1000',
        ]);

        try {

            DB::beginTransaction();

            /*
        |--------------------------------------------------------------------------
        | 1. CARI / BUAT DATA PULSA
        |--------------------------------------------------------------------------
        | tabel: pulsa
        */

            $pulsa = Pulsa::firstOrCreate([
                'id_warung' => $request->id_warung,
                'id_jenis'  => $request->jenis_pulsa_id,
            ]);

            /*
        |--------------------------------------------------------------------------
        | 2. UPDATE / CREATE SALDO PULSA
        |--------------------------------------------------------------------------
        | tabel: saldo_pulsa
        */

            $saldoPulsa = SaldoPulsa::where('id_warung', $request->id_warung)
                ->where('id_jenis', $request->jenis_pulsa_id)
                ->lockForUpdate()
                ->first();

            if ($saldoPulsa) {

                $saldoPulsa->increment('jumlah', $nominal);
            } else {

                $saldoPulsa = SaldoPulsa::create([
                    'id_warung' => $request->id_warung,
                    'id_jenis'  => $request->jenis_pulsa_id,
                    'jumlah'    => $nominal,
                ]);
            }

            /*
        |--------------------------------------------------------------------------
        | 3. UPDATE / CREATE HARGA PULSA
        |--------------------------------------------------------------------------
        | tabel: harga_pulsa
        */

            // HargaPulsa::updateOrCreate(
            //     [
            //         'id_jenis' => $request->jenis_pulsa_id,
            //         'jumlah_pulsa'   => $nominal,
            //     ],
            //     [
            //         'harga_alomogada' => $hargaBeli,
            //         'harga_jual'      => $hargaJual,
            //         'harga_hutang'    => $hargaJual,
            //     ]
            // );

            /*
        |--------------------------------------------------------------------------
        | 4. BUAT HUTANG WARUNG
        |--------------------------------------------------------------------------
        */

            $hutangWarung = HutangWarung::create([
                'id_warung' => $request->id_warung,
                'total'     => $hargaBeli,
                'jenis'     => 'pulsa',
            ]);

            /*
        |--------------------------------------------------------------------------
        | 5. TRANSAKSI PULSA MASUK
        |--------------------------------------------------------------------------
        */

            TransaksiPulsaMasuk::create([
                'id_pulsa'          => $pulsa->id,
                'id_hutang_warung' => $hutangWarung->id,
                'jumlah'            => $nominal,
                'harga_alomogada'  => $hargaBeli,
                'total'             => $hargaJual,
            ]);

            DB::commit();

            $jenis = JenisPulsa::find($request->jenis_pulsa_id);

            return redirect()
                ->route('admin.saldo-pulsa.index')
                ->with(
                    'success',
                    'Saldo ' . $jenis->nama_jenis .
                        ' berhasil ditambahkan sebesar Rp ' .
                        number_format($nominal, 0, ',', '.')
                );
        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error('Topup Pulsa Error: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * =========================================================================
     * DETAIL SALDO PULSA
     * =========================================================================
     */
    public function show($id)
    {
        /*
    |--------------------------------------------------------------------------
    | AMBIL DATA SALDO PULSA
    |--------------------------------------------------------------------------
    */

        $saldoPulsa = SaldoPulsa::with([
            'warung',
            'jenisPulsa'
        ])
            ->findOrFail($id);

        /*
    |--------------------------------------------------------------------------
    | CARI DATA PULSA
    |--------------------------------------------------------------------------
    */

        $pulsa = Pulsa::where('id_warung', $saldoPulsa->id_warung)
            ->where('id_jenis', $saldoPulsa->id_jenis)
            ->first();

        /*
    |--------------------------------------------------------------------------
    | DEFAULT PAGINATION KOSONG
    |--------------------------------------------------------------------------
    */

        $riwayatTransaksi = TransaksiPulsaMasuk::whereRaw('1 = 0')
            ->paginate(10);

        /*
    |--------------------------------------------------------------------------
    | AMBIL RIWAYAT TRANSAKSI PULSA MASUK
    |--------------------------------------------------------------------------
    */

        if ($pulsa) {

            $riwayatTransaksi = TransaksiPulsaMasuk::with([
                'hutangWarung'
            ])
                ->where('id_pulsa', $pulsa->id)
                ->latest()
                ->paginate(10);
        }

        /*
    |--------------------------------------------------------------------------
    | RETURN VIEW
    |--------------------------------------------------------------------------
    */

        return view(
            'admin.saldo_pulsa.show',
            [
                'pulsa' => $saldoPulsa,
                'riwayatTransaksi' => $riwayatTransaksi
            ]
        );
    }
}

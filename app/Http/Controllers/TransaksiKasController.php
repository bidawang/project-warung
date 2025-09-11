<?php

namespace App\Http\Controllers;

use App\Models\TransaksiKas;
use App\Models\KasWarung;
use Illuminate\Http\Request;
use App\Models\DetailTransaksi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\DetailKasWarung;

class TransaksiKasController extends Controller
{
    public function index()
    {
        // $transaksis = TransaksiKas::with('kasWarung')->latest()->paginate(10);
        $transaksis = TransaksiKas::with('kasWarung')
            ->where('id_kas_warung', 1) // Ganti 1 dengan ID kas_warung yang sesuai
            ->latest()
            ->paginate(10);
        return view('transaksikas.index', compact('transaksis'));
    }

    // public function index()
    // {
    //     $id_warung = 1;
    //     // $id_warung = Auth::user()->warung->id ?? null;
    //     $kasWarung = KasWarung::where('id_warung', $id_warung)->first();
    //     $transaksiKas = TransaksiKas::with('kasWarung.warung')->where('id_kas_warung', $kasWarung->id)->latest()->get();
    //     return view('transaksikas.index', compact('transaksiKas'));
    // }

    // public function index()
    // {
    //     if (Auth::check() && Auth::user()->role === 'kasir') {
    //         $idKasWarung = session('id_kas_warung');

    //         $transaksis = TransaksiKas::with('kasWarung')
    //             ->where('id_kas_warung', $idKasWarung)
    //             ->latest()
    //             ->paginate(10);
    //     } else {
    //         $transaksis = TransaksiKas::with('kasWarung')
    //             ->latest()
    //             ->paginate(10);
    //     }

    //     return view('transaksikas.index', compact('transaksis'));
    // }


    public function create()
    {
        $id_warung = 1;
        // $id_warung = Auth::user()->warung->id ?? null;
        $kasWarungs = KasWarung::with(['warung', 'detailKasWarung'])->where('id_warung', $id_warung)->get();
        return view('transaksikas.create', compact('kasWarungs'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        // Validasi input
        $request->validate([
            // 'id_kas_warung' => 'required|exists:kas_warung,id',
            'metode_pembayaran' => 'required|string|max:100',
            'keterangan' => 'nullable|string',
            'pecahan' => 'nullable|array',
            // 'pecahan.*' => 'nullable|integer|min:0',
        ]);
        // dd($request->all());

        // Mulai transaksi database
        DB::beginTransaction();

        try {
            $total = 0;
            $detailTransaksiData = [];
            $jenisTransaksi = $request->metode_pembayaran === 'masuk' ? 'masuk' : 'keluar';

            // Ambil detail kas warung yang sudah ada
            $detailKasWarungs = DetailKasWarung::where('id_kas_warung', 1)->get()->keyBy('pecahan');
            // $detailKasWarungs = DetailKasWarung::where('id_kas_warung', $request->id_kas_warung)->get()->keyBy('pecahan');

            foreach ($request->input('pecahan') as $pecahan => $jumlah) {
                $jumlah = (int) $jumlah;
                if ($jumlah > 0) {
                    $total += $pecahan * $jumlah;
                    $detailTransaksiData[] = [
                        'pecahan' => $pecahan,
                        'jumlah' => $jumlah,
                        'keterangan' => null
                    ];

                    // Perbarui jumlah di tabel detail_kas_warung
                    if ($detailKasWarungs->has($pecahan)) {
                        $detail = $detailKasWarungs->get($pecahan);
                        if ($jenisTransaksi === 'masuk') {
                            $detail->jumlah += $jumlah;
                        } else {
                            $detail->jumlah -= $jumlah;
                        }
                        $detail->save();
                    }
                }
            }

            // Buat entri baru di tabel transaksi_kas
            $transaksi = TransaksiKas::create([
                'id_kas_warung' => 1,
                // 'id_kas_warung' => $request->id_kas_warung,
                'total' => $total,
                'metode_pembayaran' => $request->metode_pembayaran,
                'keterangan' => $request->keterangan,
            ]);

            // Simpan detail pecahan ke tabel detail_transaksi
            foreach ($detailTransaksiData as $data) {
                $data['id_transaksi'] = $transaksi->id;
                DetailTransaksi::create($data);
            }

            // Commit transaksi jika semua berhasil
            DB::commit();

            return redirect()->route('transaksikas.index')->with('success', 'Transaksi berhasil ditambahkan');
        } catch (\Exception $e) {
            // Rollback transaksi jika ada yang gagal
            DB::rollBack();
            Log::error('Transaksi gagal: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Transaksi gagal. Silakan coba lagi.')->withInput();
        }
    }

    public function show(TransaksiKas $transaksika)
    {
        $transaksika->load('kasWarung');
        return view('transaksikas.show', compact('transaksika'));
    }

    public function edit(TransaksiKas $transaksika)
    {
        $kasWarungs = KasWarung::all();
        return view('transaksikas.edit', compact('transaksika', 'kasWarungs'));
    }

    public function update(Request $request, TransaksiKas $transaksika)
    {
        $validated = $request->validate([
            'id_kas_warung' => 'required|exists:kas_warung,id',
            'total' => 'required|numeric',
            'metode_pembayaran' => 'required|string|max:100',
            'keterangan' => 'nullable|string',
        ]);

        $transaksika->update($validated);

        return redirect()->route('transaksikas.index')->with('success', 'Transaksi berhasil diperbarui');
    }

    public function destroy(TransaksiKas $transaksika)
    {
        $transaksika->delete();
        return redirect()->route('transaksikas.index')->with('success', 'Transaksi berhasil dihapus');
    }
}

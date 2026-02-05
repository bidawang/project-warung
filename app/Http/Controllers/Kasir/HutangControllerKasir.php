<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hutang;
use App\Models\LogPembayaranHutang;

class HutangControllerKasir extends Controller
{
    public function index(Request $request)
    {
        $idWarung = session('id_warung');

        if (!$idWarung) {
            return redirect()->route('dashboard')
                ->with('error', 'ID warung tidak ditemukan di sesi.');
        }

        $status = $request->get('status');

        $query = Hutang::with('user')
            ->where('id_warung', $idWarung)
            ->selectRaw('
            id_user,
            SUM(jumlah_hutang_awal) as total_hutang,
            SUM(jumlah_sisa_hutang) as total_sisa_hutang
        ')
            ->groupBy('id_user');

        // filter status pelanggan (GLOBAL)
        if ($status === 'belum_lunas') {
            $query->having('total_sisa_hutang', '>', 0);
        } elseif ($status === 'lunas') {
            $query->having('total_sisa_hutang', '=', 0);
        }

        // search nama pelanggan
        if ($request->filled('q')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->q . '%');
            });
        }

        $hutangList = $query->paginate(10);

        return view('kasir.hutang.index', compact('hutangList', 'status'));
    }


    public function detail($idUser)
    {
        $idWarung = session('id_warung');

        if (!$idWarung) {
            return redirect()->route('dashboard')
                ->with('error', 'ID warung tidak ditemukan di sesi.');
        }

        $hutangList = Hutang::with('user')
            ->where('id_warung', $idWarung)
            ->where('id_user', $idUser)
            ->orderBy('tenggat', 'asc')
            ->get();

        if ($hutangList->isEmpty()) {
            return redirect()->back()
                ->with('info', 'Tidak ada hutang untuk pelanggan ini.');
        }

        $pelanggan = $hutangList->first()->user;

        return view('kasir.hutang.detail', compact('pelanggan', 'hutangList'));
    }



    public function bayar(Request $request, $id)
    {
        $idWarung = session('id_warung');

        if (!$idWarung) {
            return redirect()->route('dashboard')->with('error', 'ID warung tidak ditemukan di sesi.');
        }

        $hutang = Hutang::where('id_warung', $idWarung)->findOrFail($id);

        if ($hutang->status == 'lunas') {
            return redirect()->back()->with('info', 'Hutang sudah lunas.');
        }



        // Validasi input jumlah bayar
        $request->validate([
            'jumlah_bayar' => 'required|numeric|min:1|max:' . $hutang->jumlah_sisa_hutang,
        ]);

        $jumlahBayar = $request->input('jumlah_bayar');

        // Kurangi jumlah hutang
        $hutang->jumlah_sisa_hutang = $hutang->jumlah_sisa_hutang - $jumlahBayar;

        // Simpan log pembayaran
        LogPembayaranHutang::create([
            'id_hutang' => $hutang->id,
            'jumlah_pembayaran' => $jumlahBayar,
        ]);

        // Jika hutang sudah lunas
        if ($hutang->jumlah_sisa_hutang <= 0) {
            $hutang->status = 'lunas';
        }

        $hutang->save();

        return redirect()->route('kasir.hutang.detail', $hutang->id)
            ->with('success', 'Pembayaran berhasil.');
    }

    public function show($id)
    {
        $idWarung = session('id_warung');

        // Gunakan .with('logs') untuk mengambil riwayat pembayaran sekaligus
        $hutang = Hutang::with('logs')
            ->where('id_warung', $idWarung)
            ->findOrFail($id);

        return view('kasir.hutang.show', compact('hutang'));
    }
}

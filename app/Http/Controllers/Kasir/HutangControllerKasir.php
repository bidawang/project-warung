<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hutang;

class HutangControllerKasir extends Controller
{
    public function index(Request $request)
    {
        $idWarung = session('id_warung');

        if (! $idWarung) {
            return redirect()->route('dashboard')->with('error', 'ID warung tidak ditemukan di sesi.');
        }

        // filter status
        $status = $request->get('status'); // 'belum lunas', 'lunas', atau null
        $query = Hutang::with('user')
            ->where('id_warung', $idWarung);

        if ($status) {
            $query->where('status', $status);
        }

        // pencarian nama user
        if ($request->filled('q')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->q . '%');
            });
        }

        $hutangList = $query->orderBy('tenggat', 'asc')->paginate(10);

        return view('kasir.hutang.index', compact('hutangList', 'status'));
    }

    public function detail($id)
    {
        $idWarung = session('id_warung');

        if (! $idWarung) {
            return redirect()->route('dashboard')->with('error', 'ID warung tidak ditemukan di sesi.');
        }

        $hutang = Hutang::with('user')->where('id_warung', $idWarung)->findOrFail($id);

        return view('kasir.hutang.detail', compact('hutang'));
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

            'jumlah_bayar' => 'required|numeric|min:1|max:' . $hutang->jumlah_pokok,
        ]);

        $jumlahBayar = $request->input('jumlah_bayar');

        // Kurangi jumlah hutang
        $hutang->jumlah_pokok -= $jumlahBayar;

        // Jika hutang sudah lunas
        if ($hutang->jumlah_pokok <= 0) {
            $hutang->jumlah_pokok = 0;
            $hutang->status = 'lunas';
        }

        $hutang->save();

        return redirect()->route('kasir.hutang.detail', $hutang->id)
            ->with('success', 'Pembayaran berhasil.');
    }
}

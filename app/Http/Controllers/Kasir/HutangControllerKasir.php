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
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\HargaPulsa;
use App\Models\JenisPulsa;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HargaPulsaControllerAdmin extends Controller
{
    /**
     * =========================================================================
     * LIST HARGA PULSA
     * =========================================================================
     */
    public function index(Request $request)
    {
        $search = $request->search;
        $jenisPulsaId = $request->jenis_pulsa_id;

        $jenisPulsa = JenisPulsa::orderBy('nama_jenis')
            ->get();

        $hargaPulsas = HargaPulsa::with([
            'jenisPulsa'
        ])
            ->when($search, function ($query) use ($search) {

                $query->where(function ($q) use ($search) {

                    $q->where('jumlah_pulsa', 'like', "%{$search}%")

                        ->orWhere('harga_jual', 'like', "%{$search}%")

                        ->orWhere('harga_hutang', 'like', "%{$search}%");
                });
            })
            ->when($jenisPulsaId, function ($query) use ($jenisPulsaId) {

                $query->where('id_jenis', $jenisPulsaId);
            })
            ->orderBy('id_jenis')
            ->orderBy('jumlah_pulsa')
            ->paginate(15)
            ->withQueryString();
// dd($hargaPulsas);
// dd($hargaPulsas);
        return view(
            'admin.harga_pulsa.index',
            compact('hargaPulsas', 'jenisPulsa')
        );
    }

    /**
     * =========================================================================
     * FORM CREATE
     * =========================================================================
     */
    public function create()
    {
        $jenisPulsa = JenisPulsa::orderBy('nama_jenis')
            ->get();

        return view(
            'admin.harga_pulsa.create',
            compact('jenisPulsa')
        );
    }

    /**
     * =========================================================================
     * STORE
     * =========================================================================
     */
    public function store(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | BERSIHKAN FORMAT RUPIAH
        |--------------------------------------------------------------------------
        */

        $hargaAlomogada = (int) str_replace(['.', ','], '', $request->harga_alomogada);
        $hargaModal = (int) str_replace(['.', ','], '', $request->harga_modal);
        $hargaJual = (int) str_replace(['.', ','], '', $request->harga_jual);
        $hargaHutang = (int) str_replace(['.', ','], '', $request->harga_hutang);
// dd($request->all());
        /*
        |--------------------------------------------------------------------------
        | VALIDASI
        |--------------------------------------------------------------------------
        */

        $request->validate([
            'jumlah_pulsa'    => 'required|integer|min:1',
            'harga_alomogada' => 'required',
            'harga_modal'     => 'required',
            'harga_jual'      => 'required',
            'harga_hutang'    => 'required',
            'jenis_pulsa_id'  => 'required|exists:jenis_pulsa,id',
        ]);

        try {

            DB::beginTransaction();

            /*
            |--------------------------------------------------------------------------
            | CEK DUPLIKAT
            |--------------------------------------------------------------------------
            */
// dd($request->all);
            $exists = HargaPulsa::where('id_jenis', $request->jenis_pulsa_id)
                ->where('jumlah_pulsa', $request->jumlah_pulsa)
                ->exists();
// dd($exists);
            if ($exists) {

                return back()
                    ->withInput()
                    ->with('error', 'Nominal pulsa sudah ada pada jenis tersebut.');
            }

            /*
            |--------------------------------------------------------------------------
            | INSERT
            |--------------------------------------------------------------------------
            */

            HargaPulsa::create([
                'jumlah_pulsa'    => $request->jumlah_pulsa,
                'harga_alomogada' => $hargaAlomogada,
                'harga_modal'     => $hargaModal,
                'harga_jual'      => $hargaJual,
                'harga_hutang'    => $hargaHutang,
                'id_jenis'  => $request->jenis_pulsa_id
            ]);

            DB::commit();

            return redirect()
                ->route('admin.harga-pulsa.index')
                ->with('success', 'Harga pulsa berhasil ditambahkan.');
        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error('Store Harga Pulsa Error: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * =========================================================================
     * FORM EDIT
     * =========================================================================
     */
    public function edit($id)
    {
        $hargaPulsa = HargaPulsa::findOrFail($id);

        $jenisPulsa = JenisPulsa::orderBy('nama_jenis')
            ->get();

        return view(
            'admin.harga_pulsa.edit',
            compact('hargaPulsa', 'jenisPulsa')
        );
    }

    /**
     * =========================================================================
     * UPDATE
     * =========================================================================
     */
    public function update(Request $request, $id)
    {
        /*
        |--------------------------------------------------------------------------
        | BERSIHKAN FORMAT RUPIAH
        |--------------------------------------------------------------------------
        */

        $hargaAlomogada = (int) str_replace(['.', ','], '', $request->harga_alomogada);
        $hargaJual = (int) str_replace(['.', ','], '', $request->harga_jual);
        $hargaHutang = (int) str_replace(['.', ','], '', $request->harga_hutang);

        /*
        |--------------------------------------------------------------------------
        | VALIDASI
        |--------------------------------------------------------------------------
        */

        $request->validate([
            'jumlah_pulsa'    => 'required|integer|min:1',
            'harga_alomogada' => 'required',
            'harga_jual'      => 'required',
            'harga_hutang'    => 'required',
            'jenis_pulsa_id'  => 'required|exists:jenis_pulsa,id',
        ]);

        try {

            DB::beginTransaction();

            $hargaPulsa = HargaPulsa::findOrFail($id);

            /*
            |--------------------------------------------------------------------------
            | CEK DUPLIKAT
            |--------------------------------------------------------------------------
            */

            $exists = HargaPulsa::where('jenis_pulsa_id', $request->jenis_pulsa_id)
                ->where('jumlah_pulsa', $request->jumlah_pulsa)
                ->where('id', '!=', $id)
                ->exists();

            if ($exists) {

                return back()
                    ->withInput()
                    ->with('error', 'Nominal pulsa sudah ada pada jenis tersebut.');
            }

            /*
            |--------------------------------------------------------------------------
            | UPDATE
            |--------------------------------------------------------------------------
            */

            $hargaPulsa->update([
                'jumlah_pulsa'    => $request->jumlah_pulsa,
                'harga_alomogada' => $hargaAlomogada,
                'harga_jual'      => $hargaJual,
                'harga_hutang'    => $hargaHutang,
                'jenis_pulsa_id'  => $request->jenis_pulsa_id
            ]);

            DB::commit();

            return redirect()
                ->route('admin.harga-pulsa.index')
                ->with('success', 'Harga pulsa berhasil diperbarui.');
        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error('Update Harga Pulsa Error: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * =========================================================================
     * DELETE
     * =========================================================================
     */
    public function destroy($id)
    {
        try {

            $hargaPulsa = HargaPulsa::findOrFail($id);

            $hargaPulsa->delete();

            return redirect()
                ->route('admin.harga-pulsa.index')
                ->with('success', 'Harga pulsa berhasil dihapus.');
        } catch (\Throwable $e) {

            Log::error('Delete Harga Pulsa Error: ' . $e->getMessage());

            return back()
                ->with('error', $e->getMessage());
        }
    }
}

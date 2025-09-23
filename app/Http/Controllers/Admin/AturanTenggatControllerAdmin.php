<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AturanTenggat;
use App\Models\Area;
use Illuminate\Validation\ValidationException;

class AturanTenggatControllerAdmin extends Controller
{
    /**
     * Menampilkan daftar aturan tenggat berdasarkan area.
     */
    public function index(Request $request)
    {
        $id_area = $request->query('id_area');
        $area = Area::findOrFail($id_area);
        // dd($area);
        $aturanTenggat = AturanTenggat::where('id_area', $id_area)->get();

        return view('admin.area.aturanTenggat.index', compact('area', 'aturanTenggat'));
    }

    /**
     * Menampilkan form untuk membuat aturan tenggat baru.
     */
    public function create(Request $request)
    {
        $id_area = $request->query('id_area');
        $area = Area::findOrFail($id_area);

        return view('admin.area.aturanTenggat.create', compact('area'));
    }

    /**
     * Menyimpan aturan tenggat baru ke database.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        try {
            $validatedData = $request->validate([
                'id_area' => 'required|exists:area,id',
                'tanggal_awal' => 'required|integer|min:1|max:31',
                'tanggal_akhir' => 'required|integer|min:1|max:31',
                'jatuh_tempo_hari' => 'required|integer|min:0',
                'bunga' => 'required|numeric|min:0',
                'keterangan' => 'nullable|string',
            ]);
            // dd($validatedData);
            AturanTenggat::create($validatedData);

            return redirect()->route('admin.aturanTenggat.index', ['id_area' => $validatedData['id_area']])
                ->with('success', 'Aturan tenggat berhasil ditambahkan!');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan aturan tenggat.')->withInput();
        }
    }

    /**
     * Menampilkan form edit aturan tenggat.
     */
    public function edit(AturanTenggat $aturanTenggat)
    {
        $area = $aturanTenggat->area;
        return view('admin.area.aturanTenggat.edit', compact('area', 'aturanTenggat'));
    }

    /**
     * Memperbarui aturan tenggat.
     */
    public function update(Request $request, AturanTenggat $aturanTenggat)
    {
        try {
            $validatedData = $request->validate([
                'tanggal_awal' => 'required|integer|min:1|max:31',
                'tanggal_akhir' => 'required|integer|min:1|max:31',
                'jatuh_tempo_hari' => 'required|integer|min:0',
                'bunga' => 'required|numeric|min:0',
                'keterangan' => 'nullable|string',
            ]);

            $aturanTenggat->update($validatedData);

            return redirect()->route('admin.aturanTenggat.index', ['id_area' => $aturanTenggat->id_area])
                ->with('success', 'Aturan tenggat berhasil diperbarui!');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui aturan tenggat.')->withInput();
        }
    }

    /**
     * Menghapus aturan tenggat.
     */
    public function destroy(AturanTenggat $aturanTenggat)
    {
        try {
            $id_area = $aturanTenggat->id_area;
            $aturanTenggat->delete();
            return redirect()->route('admin.aturanTenggat.index', ['id_area' => $id_area])
                ->with('success', 'Aturan tenggat berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus aturan tenggat.');
        }
    }
}

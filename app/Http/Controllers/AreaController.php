<?php

namespace App\Http\Controllers;

use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AreaController extends Controller
{
    /**
     * Menampilkan daftar semua area.
     */
    public function index()
    {
        $areas = Area::all();
        return view('area.index', compact('areas'));
    }

    /**
     * Menampilkan formulir untuk membuat area baru.
     */
    public function create()
    {
        return view('area.create');
    }

    /**
     * Menyimpan area baru ke database.
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'area' => 'required|string|max:255|unique:area,area',
                'keterangan' => 'nullable|string',
            ]);

            Area::create($validatedData);

            return redirect()->route('area.index')->with('success', 'Area berhasil ditambahkan!');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan area. Silakan coba lagi.')->withInput();
        }
    }

    /**
     * Menampilkan formulir untuk mengedit area tertentu.
     */
    public function edit(Area $area)
    {
        return view('area.edit', compact('area'));
    }

    /**
     * Memperbarui area yang ada di database.
     */
    public function update(Request $request, Area $area)
    {
        try {
            $validatedData = $request->validate([
                'area' => 'required|string|max:255|unique:area,area,' . $area->id,
                'keterangan' => 'nullable|string',
            ]);

            $area->update($validatedData);

            return redirect()->route('area.index')->with('success', 'Area berhasil diperbarui!');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui area. Silakan coba lagi.')->withInput();
        }
    }

    /**
     * Menghapus area dari database.
     */
    public function destroy(Area $area)
    {
        try {
            $area->delete();
            return redirect()->route('area.index')->with('success', 'Area berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus area. Silakan coba lagi.');
        }
    }
}

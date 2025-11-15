<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View; // Pastikan untuk mengimpor kelas View
use App\Models\Area;
use Illuminate\Validation\ValidationException;

class AreaControllerAdmin extends Controller
{
    /**
     * Menampilkan daftar semua area dengan fitur pencarian dan paginasi.
     */
    public function index(Request $request)
    {
        // Mendapatkan kata kunci pencarian dari request
        $searchKeyword = $request->input('search');

        // Memulai query builder untuk model Area
        $areas = Area::query();

        // Jika ada kata kunci pencarian, tambahkan kondisi WHERE
        if ($searchKeyword) {
            $areas->where('area', 'like', '%' . $searchKeyword . '%');
        }

        // Terapkan paginasi dengan 10 item per halaman dan tambahkan parameter pencarian ke URL paginasi
        $areas = $areas->paginate(10)->appends(['search' => $searchKeyword]);
// dd($areas);
        // Mengirimkan data area dan kata kunci pencarian ke view
        return view('admin.area.index', compact('areas', 'searchKeyword'));
    }

    /**
     * Menampilkan formulir untuk membuat area baru.
     */
    public function create()
    {

        return view('admin.area.create');
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

            return redirect()->route('admin.area.index')->with('success', 'Area berhasil ditambahkan!');
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
        // dd($area);
        return view('admin.area.edit', compact('area'));
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

            return redirect()->route('admin.area.index')->with('success', 'Area berhasil diperbarui!');
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

    public function show(Area $area)
    {
        // Load relasi aturan tenggat, laba, dan warung
        $area->load(['laba', 'warung']);
// dd($area);
        return view('admin.area.show', compact('area'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Laba;
use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LabaController extends Controller
{
    /**
     * Menampilkan daftar semua laba.
     */
    public function index()
    {
        $labas = Laba::with('area')->get();
        return view('laba.index', compact('labas'));
    }

    /**
     * Menampilkan formulir untuk membuat laba baru.
     */
    public function create()
    {
        $areas = Area::all();
        return view('laba.create', compact('areas'));
    }

    /**
     * Menyimpan laba baru ke database.
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'id_area' => 'required|exists:area,id',
                'input_minimal' => 'required|numeric',
                'input_maksimal' => 'required|numeric|gt:input_minimal',
                'harga_jual' => 'required|numeric',
                'jenis' => 'nullable|string',
                'keterangan' => 'nullable|string',
            ]);

            Laba::create($validatedData);

            return redirect()->route('laba.index')->with('success', 'Data laba berhasil ditambahkan!');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan data laba. Silakan coba lagi.')->withInput();
        }
    }

    /**
     * Menampilkan formulir untuk mengedit laba tertentu.
     */
    public function edit(Laba $laba)
    {
        $areas = Area::all();
        return view('laba.edit', compact('laba', 'areas'));
    }

    /**
     * Memperbarui laba yang ada di database.
     */
    public function update(Request $request, Laba $laba)
    {
        try {
            $validatedData = $request->validate([
                'id_area' => 'required|exists:area,id',
                'input_minimal' => 'required|numeric',
                'input_maksimal' => 'required|numeric|gt:input_minimal',
                'harga_jual' => 'required|numeric',
                'jenis' => 'nullable|string',
                'keterangan' => 'nullable|string',
            ]);

            $laba->update($validatedData);

            return redirect()->route('laba.index')->with('success', 'Data laba berhasil diperbarui!');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui data laba. Silakan coba lagi.')->withInput();
        }
    }

    /**
     * Menghapus laba dari database.
     */
    public function destroy(Laba $laba)
    {
        try {
            $laba->delete();
            return redirect()->route('laba.index')->with('success', 'Data laba berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus data laba. Silakan coba lagi.');
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\AturanTenggat;
use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AturanTenggatController extends Controller
{
    /**
     * Menampilkan daftar semua aturan tenggat.
     */
    public function index()
    {
        $aturanTenggats = AturanTenggat::with('area')->get();
        return view('aturanTenggat.index', compact('aturanTenggats'));
    }

    /**
     * Menampilkan formulir untuk membuat aturan tenggat baru.
     */
    public function create()
    {
        $areas = Area::all();
        return view('aturantenggat.create', compact('areas'));
    }

    /**
     * Menyimpan aturan tenggat baru ke database.
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'id_area' => 'required|exists:area,id',
                'tanggal_awal' => 'required|date',
                'tanggal_akhir' => 'required|date|after_or_equal:tanggal_awal',
                'jatuh_tempo_hari' => 'required|integer',
                'jatuh_tempo_bulan' => 'required|integer',
                'bunga' => 'required|numeric',
                'keterangan' => 'nullable|string',
            ]);

            AturanTenggat::create($validatedData);

            return redirect()->route('aturanTenggat.index')->with('success', 'Aturan tenggat berhasil ditambahkan!');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan aturan tenggat. Silakan coba lagi.')->withInput();
        }
    }

    /**
     * Menampilkan formulir untuk mengedit aturan tenggat tertentu.
     */
    public function edit(AturanTenggat $aturanTenggat)
    {
        $areas = Area::all();
        return view('aturantenggat.edit', compact('aturanTenggat', 'areas'));
    }

    /**
     * Memperbarui aturan tenggat yang ada di database.
     */
    public function update(Request $request, AturanTenggat $aturanTenggat)
    {
        try {
            $validatedData = $request->validate([
                'id_area' => 'required|exists:area,id',
                'tanggal_awal' => 'required|date',
                'tanggal_akhir' => 'required|date|after_or_equal:tanggal_awal',
                'jatuh_tempo_hari' => 'required|integer',
                'jatuh_tempo_bulan' => 'required|integer',
                'bunga' => 'required|numeric',
                'keterangan' => 'nullable|string',
            ]);

            $aturanTenggat->update($validatedData);

            return redirect()->route('aturanTenggat.index')->with('success', 'Aturan tenggat berhasil diperbarui!');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui aturan tenggat. Silakan coba lagi.')->withInput();
        }
    }

    /**
     * Menghapus aturan tenggat dari database.
     */
    public function destroy(AturanTenggat $aturanTenggat)
    {
        try {
            $aturanTenggat->delete();
            return redirect()->route('aturanTenggat.index')->with('success', 'Aturan tenggat berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus aturan tenggat. Silakan coba lagi.');
        }
    }
}

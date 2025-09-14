<?php

namespace App\Http\Controllers;

use App\Models\Laba;
use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

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

    public function formImport()
    {
        return view('laba.import');
    }

    public function import(Request $request)
    {
        $file = $request->file('file');

        // Ambil sheet pertama
        $rows = Excel::toArray([], $file)[0];

        // Buang header (modal,harga)
        array_shift($rows);

        // Bikin array data sederhana
        $data = [];
        foreach ($rows as $row) {
            if (count($row) >= 2) {
                $data[] = [
                    'modal' => (int) $row[0],
                    'harga' => (int) $row[1],
                ];
            }
        }

        if (empty($data)) {
            return back()->with('error', 'File kosong atau format salah.');
        }

        // Proses jadi range
        $ranges = [];
        $start = $data[0]['modal'];
        $end   = $data[0]['modal'];
        $harga = $data[0]['harga'];

        for ($i = 1; $i < count($data); $i++) {
            if ($data[$i]['harga'] === $harga) {
                // masih harga sama → lanjut extend range
                $end = $data[$i]['modal'];
            } else {
                // harga berubah → simpan range lama
                $ranges[] = [
                    'input_minimal' => $start,
                    'input_maksimal' => $end,
                    'harga_jual' => $harga,
                    'jenis' => 'otomatis',
                    'keterangan' => 'import excel',
                    'id_area' => 1, // sesuaikan
                ];

                // mulai range baru
                $start = $data[$i]['modal'];
                $end   = $data[$i]['modal'];
                $harga = $data[$i]['harga'];
            }
        }

        // Masukkan range terakhir
        $ranges[] = [
            'input_minimal' => $start,
            'input_maksimal' => $end,
            'harga_jual' => $harga,
            'jenis' => 'otomatis',
            'keterangan' => 'import excel',
            'id_area' => 1,
        ];

        // Simpan ke database
        Laba::insert($ranges);

        return redirect()->route('laba.formImport')->with('success', 'Data berhasil diimport');
    }
}

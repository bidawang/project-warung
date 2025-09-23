<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Laba;
use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class LabaControllerAdmin extends Controller
{
    /**
     * Menampilkan daftar semua laba, bisa difilter berdasarkan area.
     */
    public function index(Request $request)
    {
        $id_area = $request->query('id_area');
// dd($id_area);
        $labas = Laba::with('area')
            ->when($id_area, fn($q) => $q->where('id_area', $id_area))
            ->get();
dd($labas);
        return view('laba.index', compact('labas', 'id_area'));
    }

    /**
     * Menampilkan formulir untuk membuat laba baru.
     */
    public function create(Request $request)
    {
        $areas = Area::all();
        $id_area = $request->query('id_area');
        return view('laba.create', compact('areas', 'id_area'));
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

            return redirect()->route('admin.laba.index', ['id_area' => $validatedData['id_area']])
                             ->with('success', 'Data laba berhasil ditambahkan!');
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

            return redirect()->route('admin.laba.index', ['id_area' => $validatedData['id_area']])
                             ->with('success', 'Data laba berhasil diperbarui!');
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
            $id_area = $laba->id_area;
            $laba->delete();
            return redirect()->route('admin.laba.index', ['id_area' => $id_area])
                             ->with('success', 'Data laba berhasil dihapus!');
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
        $rows = Excel::toArray([], $file)[0];
        array_shift($rows);

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

        $ranges = [];
        $start = $data[0]['modal'];
        $end   = $data[0]['modal'];
        $harga = $data[0]['harga'];

        for ($i = 1; $i < count($data); $i++) {
            if ($data[$i]['harga'] === $harga) {
                $end = $data[$i]['modal'];
            } else {
                $ranges[] = [
                    'input_minimal' => $start,
                    'input_maksimal' => $end,
                    'harga_jual' => $harga,
                    'jenis' => 'otomatis',
                    'keterangan' => 'import excel',
                    'id_area' => 1,
                ];
                $start = $data[$i]['modal'];
                $end   = $data[$i]['modal'];
                $harga = $data[$i]['harga'];
            }
        }

        $ranges[] = [
            'input_minimal' => $start,
            'input_maksimal' => $end,
            'harga_jual' => $harga,
            'jenis' => 'otomatis',
            'keterangan' => 'import excel',
            'id_area' => 1,
        ];

        Laba::insert($ranges);

        return redirect()->route('laba.formImport')->with('success', 'Data berhasil diimport');
    }
}

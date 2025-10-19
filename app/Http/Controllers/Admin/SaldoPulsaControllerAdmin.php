<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pulsa;
use App\Models\Warung;

class SaldoPulsaControllerAdmin extends Controller
{
    public function index()
    {
        $pulsas = Pulsa::with('warung')->paginate(10);
        return view('admin.saldo_pulsa.index', compact('pulsas'));
    }

    public function create()
    {
        // Mengambil semua data warung untuk ditampilkan di dropdown.
        $warungs = Warung::orderBy('nama_warung')->get();
        return view('admin.saldo_pulsa.create', compact('warungs'));
    }

    public function store(Request $request)
    {
        // 1. Validasi Data
        $request->validate([
            'id_warung' => 'required|exists:warung,id',
            'jenis' => 'required|in:hp,listrik', // Sesuai enum di tabel pulsa
            'nominal' => 'required|integer|min:1', // Saldo adalah INT dan harus positif
        ], [
            'nominal.min' => 'Nominal top-up harus lebih besar dari 0.',
            'id_warung.required' => 'Warung wajib dipilih.',
            'jenis.required' => 'Jenis pulsa wajib dipilih.',
        ]);

        // 2. Cari atau Buat Record Saldo Pulsa
        // Gunakan updateOrCreate untuk efisiensi
        $pulsa = Pulsa::firstOrNew(
            [
                'id_warung' => $request->id_warung,
                'jenis' => $request->jenis,
            ]
        );

        // 3. Tambahkan Nominal Saldo
        // Jika record baru, saldo awal adalah 0, lalu ditambahkan nominal.
        // Jika record sudah ada, saldo lama akan ditambahkan nominal.
        $pulsa->saldo += $request->nominal;

        // 4. Simpan Perubahan ke Database
        $pulsa->save();

        // 5. Redirect dengan Pesan Sukses
        $jenisLabel = $request->jenis == 'hp' ? 'Handphone' : 'Listrik';
        return redirect()->route('admin.saldo-pulsa.index')->with('success',
            "Saldo pulsa **{$jenisLabel}** sebesar Rp".number_format($request->nominal, 0, ',', '.')." berhasil ditambahkan ke warung."
        );
    }

    public function edit(Pulsa $pulsa)
    {
        // Load relasi warung untuk ditampilkan di form edit
        $pulsa->load('warung');

        return view('admin.saldo_pulsa.edit', compact('pulsa'));
    }

    public function update(Request $request, Pulsa $pulsa)
    {
        // 1. Validasi Data
        $request->validate([
            // Hanya kolom saldo yang boleh diubah, id_warung dan jenis harusnya tersembunyi/disabled di form edit
            'saldo' => 'required|integer|min:0',
        ], [
            'saldo.min' => 'Nominal saldo tidak boleh kurang dari 0.',
        ]);

        // 2. Perbarui Nilai Saldo
        // Ini akan menimpa nilai saldo lama dengan nilai baru dari form
        $pulsa->saldo = $request->saldo;

        // 3. Simpan Perubahan
        $pulsa->save();

        // 4. Redirect dengan Pesan Sukses
        $jenisLabel = $pulsa->jenis == 'hp' ? 'Handphone' : 'Listrik';
        return redirect()->route('admin.saldo_pulsa.index')->with('success',
            "Saldo pulsa **{$jenisLabel}** pada warung **{$pulsa->warung->nama_warung}** berhasil diperbarui menjadi Rp".number_format($pulsa->saldo, 0, ',', '.')."."
        );
    }
}

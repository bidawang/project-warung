<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AturanTenggat;


class AturanTenggatControllerAdmin extends Controller
{
    /**
     * Menampilkan daftar aturan tenggat berdasarkan area.
     */

// CREATE
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'id_warung' => 'required',
    //         'tanggal_awal' => 'required|numeric',
    //         'tanggal_akhir' => 'required|numeric',
    //         'jatuh_tempo_hari' => 'required|numeric|min:1',
    //         'bunga' => 'required|numeric|min:0',
    //     ]);

    //     AturanTenggat::create($request->all());

    //     return back()->with('success', 'Aturan tenggat berhasil ditambahkan.');
    // }

    // // UPDATE
    // public function update(Request $request, $id)
    // {
    //     $request->validate([
    //         'id_warung' => 'required',
    //         'tanggal_awal' => 'required|numeric',
    //         'tanggal_akhir' => 'required|numeric',
    //         'jatuh_tempo_hari' => 'required|numeric|min:1',
    //         'bunga' => 'required|numeric|min:0',
    //     ]);

    //     AturanTenggat::findOrFail($id)->update($request->all());

    //     return back()->with('success', 'Aturan tenggat berhasil diperbarui.');
    // }

    // // DELETE
    // public function destroy($id)
    // {
    //     AturanTenggat::findOrFail($id)->delete();

    //     return back()->with('success', 'Aturan tenggat berhasil dihapus.');
    // }

    // --- FUNGSI BARU UNTUK MANAJEMEN ATURAN TENGGAT ---

    /**
     * Menyimpan aturan tenggat baru dengan validasi bentrok tanggal.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'id_warung' => 'required|exists:warung,id',
            'tanggal_awal' => 'required|date',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal_awal',
            'jatuh_tempo_hari' => 'required|integer|min:1',
            'bunga' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:255',
        ]);

        // Pengecekan Bentrok Tanggal (Overlap Validation)
        $this->validateDateOverlap($request, null);

        // Simpan data
        AturanTenggat::create($data);

        return back()->with('success', 'Aturan tenggat baru berhasil ditambahkan.');
    }

    /**
     * Memperbarui aturan tenggat dengan validasi bentrok tanggal.
     */
    public function update(Request $request, AturanTenggat $aturanTenggat)
    {
        $data = $request->validate([
            'id_warung' => 'required|exists:warung,id',
            'tanggal_awal' => 'required|date',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal_awal',
            'jatuh_tempo_hari' => 'required|integer|min:1',
            'bunga' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:255',
        ]);

        // Pengecekan Bentrok Tanggal (Overlap Validation)
        // Lewatkan ID aturan yang sedang diedit agar validasi mengabaikan dirinya sendiri
        $this->validateDateOverlap($request, $aturanTenggat->id);

        // Update data
        $aturanTenggat->update($data);

        return back()->with('success', 'Aturan tenggat berhasil diperbarui.');
    }

    /**
     * Menghapus aturan tenggat.
     */
    public function destroy(AturanTenggat $aturanTenggat)
    {
        $aturanTenggat->delete();
        return back()->with('success', 'Aturan tenggat berhasil dihapus.');
    }


    /**
     * Fungsi untuk validasi Bentrok Tanggal.
     * Tidak boleh ada rentang tanggal yang saling bertindihan (overlap) untuk warung yang sama.
     */
    protected function validateDateOverlap(Request $request, $ignoreId = null)
    {
        $idWarung = $request->id_warung;
        $tglAwal = $request->tanggal_awal;
        $tglAkhir = $request->tanggal_akhir;

        // Query untuk mencari aturan tenggat yang berbenturan
        $query = AturanTenggat::where('id_warung', $idWarung)
            ->where(function ($q) use ($tglAwal, $tglAkhir) {
                // Bentrok terjadi jika:
                $q->where(function ($q2) use ($tglAwal, $tglAkhir) {
                    // 1. Rentang baru berada di antara rentang lama:
                    //    Tgl Awal Baru <= Tgl Akhir Lama AND Tgl Akhir Baru >= Tgl Awal Lama
                    $q2->whereDate('tanggal_awal', '<=', $tglAkhir)
                        ->whereDate('tanggal_akhir', '>=', $tglAwal);
                });
            });

        // Abaikan aturan yang sedang diedit (jika mode update)
        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        if ($query->exists()) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'tanggal_awal' => 'Rentang tanggal yang Anda masukkan berbenturan dengan aturan tenggat yang sudah ada untuk warung ini.',
                'tanggal_akhir' => 'Rentang tanggal yang Anda masukkan berbenturan dengan aturan tenggat yang sudah ada untuk warung ini.'
            ]);
        }
    }

}

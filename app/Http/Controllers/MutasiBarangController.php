<?php

namespace App\Http\Controllers;

use App\Models\MutasiBarang;
use App\Models\StokWarung;
use App\Models\Warung;
use Illuminate\Http\Request;

class MutasiBarangController extends Controller
{
    /**
     * Tampilkan daftar mutasi
     */
    // public function index()
    // {
    //     $query = MutasiBarang::with(['stokWarung.barang', 'warungAsal', 'warungTujuan'])->latest();

    //     $idStokWarung = 1;
    //     $query->where('id_stok_warung', $idStokWarung);

    //     // if (Auth::check() && Auth::user()->role === 'kasir') {
    //     //     $idStokWarung = session('id_stok_warung');
    //     //     $query->where('id_stok_warung', $idStokWarung);
    //     // }

    //     $mutasi = $query->get();

    //     return view('mutasibarang.index', compact('mutasi'));
    // }

    public function index()
{
    $query = MutasiBarang::with(['stokWarung.barang', 'warungAsal', 'warungTujuan'])->latest();

    // if (Auth::check() && Auth::user()->role === 'kasir') {
        $idWarungTujuan = 1; // Ganti dengan ID warung tujuan yang sesuai, misalnya dari session atau Auth
        $query->where('warung_tujuan', $idWarungTujuan);
    // }

    $mutasi = $query->get();

    return view('mutasibarang.index', compact('mutasi'));
}



    /**
     * Form tambah mutasi
     */
    public function create()
    {
        // if (Auth::check() && Auth::user()->role === 'kasir') {
        //     $idStokWarung = session('id_stok_warung');
        //     $stokWarung = StokWarung::with(['barang', 'warung'])
        //         ->where('id', $idStokWarung)
        //         ->get();
        // } else {
        //     $stokWarung = StokWarung::with(['barang', 'warung'])->get();
        // }

        $idStokWarung = 1;
        $stokWarung = StokWarung::with(['barang', 'warung'])
            ->where('id', $idStokWarung)
            ->get();

        $warung = Warung::all();

        return view('mutasibarang.create', compact('stokWarung', 'warung'));
    }


    /**
     * Simpan mutasi baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_stok_warung' => 'required|exists:stok_warung,id',
            // 'warung_asal'    => 'required|exists:warung,id',
            'warung_tujuan'  => 'required|exists:warung,id|different:warung_asal',
            'warung_tujuan'  => 'required|exists:warung,id',
            'jumlah'         => 'required|integer|min:1',
            // 'status'         => 'required|in:pending,disetujui,ditolak',
            'keterangan'     => 'nullable|string'
        ]);
        $validated['warung_asal'] = 1; // Ganti dengan ID warung asal yang sesuai, misalnya dari session atau Auth
        MutasiBarang::create($validated);

        return redirect()->route('mutasibarang.index')->with('success', 'Mutasi berhasil ditambahkan.');
    }

    /**
     * Detail mutasi
     */
    public function show($id)
    {
        $mutasi = MutasiBarang::with(['stokWarung.barang', 'warungAsal', 'warungTujuan'])->findOrFail($id);

        return view('mutasibarang.show', compact('mutasi'));
    }

    /**
     * Form edit mutasi
     */
    public function edit($id)
    {
        $mutasi = MutasiBarang::findOrFail($id);
        $stokWarung = StokWarung::with('barang', 'warung')->get();
        $warung = Warung::all();

        return view('mutasibarang.edit', compact('mutasi', 'stokWarung', 'warung'));
    }

    /**
     * Update mutasi
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'id_stok_warung' => 'required|exists:stok_warung,id',
            'warung_asal'    => 'required|exists:warung,id',
            'warung_tujuan'  => 'required|exists:warung,id|different:warung_asal',
            'jumlah'         => 'required|integer|min:1',
            'status'         => 'required|in:pending,disetujui,ditolak',
            'keterangan'     => 'nullable|string'
        ]);

        $mutasi = MutasiBarang::findOrFail($id);
        $mutasi->update($request->all());

        return redirect()->route('mutasibarang.index')->with('success', 'Mutasi berhasil diperbarui.');
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'mutasiBarang' => 'required|array',
            'mutasiBarang.*' => 'exists:mutasi_barang,id',
            'status_baru' => 'required|string|in:terima,tolak',
        ]);
        $ids = $request->input('mutasiBarang');
        $statusBaru = $request->input('status_baru');
        // Update status untuk semua mutasi barang yang dipilih
        MutasiBarang::whereIn('id', $ids)->update(['status' => $statusBaru]);
        return redirect()->route('mutasibarang.index')
            ->with('success', 'Status mutasi barang berhasil diperbarui.');
    }

    /**
     * Hapus mutasi
     */
    public function destroy($id)
    {
        $mutasi = MutasiBarang::findOrFail($id);
        $mutasi->delete();

        return redirect()->route('mutasibarang.index')->with('success', 'Mutasi berhasil dihapus.');
    }
}

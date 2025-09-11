<?php

namespace App\Http\Controllers;

use App\Models\BarangMasuk;
use App\Models\StokWarung;
use App\Models\TransaksiBarang;
use Illuminate\Http\Request;

class BarangMasukController extends Controller
{
    /**
     * Menampilkan semua barang masuk
     */
    // public function index()
    // {
    //     // $barangMasuk = BarangMasuk::with(['transaksiBarang', 'stokWarung.barang'])->latest()->paginate(10);
    //     $barangMasuk = BarangMasuk::with(['transaksiBarang', 'stokWarung.barang', 'stokWarung.warung.user'])->latest()->paginate(10);

    //     return view('barangmasuk.index', compact('barangMasuk'));
    // }

    public function index()
    {
        // Menyesuaikan eager loading untuk memuat data warung dan user
        // Menambahkan filter berdasarkan id_user dari user yang sedang login
        $barangMasuk = BarangMasuk::with(['transaksiBarang', 'stokWarung.barang', 'stokWarung.warung.user'])
            ->whereHas('stokWarung.warung', function ($query) {
                $query->where('id_user', 1);
                // $query->where('id_user', Auth::id());
            })
            ->latest()
            ->paginate(10);
        return view('barangmasuk.index', compact('barangMasuk'));
    }

    /**
     * Form tambah barang masuk
     */
    public function create()
    {
        $transaksiBarang = TransaksiBarang::all();
        $stokWarung = StokWarung::with('barang')->get();
        return view('barangmasuk.create', compact('transaksiBarang', 'stokWarung'));
    }

    /**
     * Simpan data barang masuk baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_transaksi_barang' => 'required|exists:transaksi_barang,id',
            'id_stok_warung' => 'required|exists:stok_warung,id',
            'jumlah' => 'required|integer|min:1',
            'status' => 'required|string|max:100',
            'keterangan' => 'nullable|string',
        ]);

        BarangMasuk::create($validated);

        return redirect()->route('barangmasuk.index')->with('success', 'Barang masuk berhasil ditambahkan');
    }

    /**
     * Tampilkan detail barang masuk
     */
    public function show($id)
    {
        $barangMasuk = BarangMasuk::with(['transaksiBarang', 'stokWarung.barang'])->findOrFail($id);
        return view('barangmasuk.show', compact('barangMasuk'));
    }

    /**
     * Form edit barang masuk
     */
    public function edit($id)
    {
        $barangMasuk = BarangMasuk::findOrFail($id);
        $transaksiBarang = TransaksiBarang::all();
        $stokWarung = StokWarung::with('barang')->get();

        return view('barangmasuk.edit', compact('barangMasuk', 'transaksiBarang', 'stokWarung'));
    }

    /**
     * Update barang masuk
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'id_transaksi_barang' => 'required|exists:transaksi_barang,id',
            'id_stok_warung' => 'required|exists:stok_warung,id',
            'jumlah' => 'required|integer|min:1',
            'status' => 'required|string|max:100',
            'keterangan' => 'nullable|string',
        ]);

        $barangMasuk = BarangMasuk::findOrFail($id);
        $barangMasuk->update($validated);

        return redirect()->route('barangmasuk.index')->with('success', 'Barang masuk berhasil diperbarui');
    }

    public function updateStatus(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'barangMasuk' => 'required|array',
            'barangMasuk.*' => 'exists:barang_masuk,id',
            'status_baru' => 'required|in:terima,tolak',
        ]);
        try {
            BarangMasuk::whereIn('id', $request->barangMasuk)->update(['status' => $request->status_baru]);
            $message = 'Status barang masuk berhasil diperbarui menjadi ' . $request->status_baru;
            return redirect()->route('barangmasuk.index')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui status.');
        }
    }

    /**
     * Hapus barang masuk
     */
    public function destroy($id)
    {
        $barangMasuk = BarangMasuk::findOrFail($id);
        $barangMasuk->delete();

        return redirect()->route('barangmasuk.index')->with('success', 'Barang masuk berhasil dihapus');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AreaPembelian;
use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Subkategori;
use Illuminate\Http\Request;

class AsalBarangControllerAdmin extends Controller
{
    // Index + Filter
    public function index(Request $request)
    {
        $areaId = $request->area_id;

        // Ambil semua Area, Kategori, dan Subkategori untuk dropdown filter
        $areas = AreaPembelian::all();
        $kategoris = Kategori::all();
        $subkategoris = Subkategori::all(); // Untuk filter subkategori

        // Query Barang
        $query = Barang::query()->with('areaPembelian', 'subKategori.kategori');

        // Filter: Area Pembelian (Nav Tab)
        if ($areaId) {
            $query->whereHas('areaPembelian', function ($q) use ($areaId) {
                $q->where('area_pembelian.id', $areaId);
            });
        }

        // filter: Kategori (Dropdown Filter)
        if ($request->kategori_id) {
            $query->whereHas('subKategori', function ($q) use ($request) {
                $q->where('id_kategori', $request->kategori_id);
            });
        }

        // filter: Subkategori (Dropdown Filter)
        if ($request->subkategori_id) {
            $query->where('id_sub_kategori', $request->subkategori_id);
        }

        // search: Nama Barang (Input Search)
        if ($request->search) {
            $query->where('nama_barang', 'LIKE', '%' . $request->search . '%');
        }

        $barangs = $query->get();

        return view('admin.asalbarang.index', [
            'areas' => $areas,
            'barangs' => $barangs,
            'kategoris' => $kategoris, // Tambahkan ini
            'subkategoris' => $subkategoris, // Tambahkan ini
            'areaId' => $areaId,
            'kategoriId' => $request->kategori_id,
            'subkategoriId' => $request->subkategori_id,
            'search' => $request->search,
        ]);
    }

    // Create
    public function create(Request $request)
    {
        // Ambil semua data yang diperlukan untuk form
        $areas = AreaPembelian::all();
        $kategoris = Kategori::all();
        // Ambil semua subkategori (untuk opsi "Pilih Subkategori" yang lengkap)
        // Kita juga butuh semua subkategori untuk dinamisasi di JS
        $allSubkategoris = Subkategori::all();

        // Data barang akan diambil secara AJAX setelah area dipilih,
        // Tapi kita tetap butuh list barang dan selected array jika area_id sudah ada di request.
        $barangs = collect(); // Default: koleksi kosong
        $selected = [];
        $areaId = $request->area_id;

        if ($areaId) {
            $area = AreaPembelian::find($areaId);
            if ($area) {
                // Ambil semua barang untuk area tertentu (belum difilter)
                $barangs = Barang::with('subKategori.kategori')->get();
                // Otomatis centang barang berdasarkan area
                $selected = $area->barangs->pluck('id')->toArray();
            }
        }

        return view('admin.asalbarang.create', compact(
            'areas',
            'barangs',
            'selected',
            'kategoris',
            'allSubkategoris', // Kirim semua subkategori ke view
            'areaId' // Kirim areaId agar bisa digunakan di JS untuk memicu pemuatan barang
        ));
    }

    // Metode baru untuk AJAX filtering
    public function filterBarang(Request $request)
{
    $areaId = $request->area_id;
    $kategoriId = $request->kategori_id;
    $subkategoriId = $request->subkategori_id;
    $search = $request->search;

    // Tambahkan withCount untuk menghitung berapa banyak area yang nge-claim
    $query = Barang::query()->with(['subKategori.kategori', 'areaPembelian'])
                            ->withCount('areaPembelian');

    if ($kategoriId) {
        $query->whereHas('subKategori', function ($q) use ($kategoriId) {
            $q->where('id_kategori', $kategoriId);
        });
    }

    if ($subkategoriId) {
        $query->where('id_sub_kategori', $subkategoriId);
    }

    if ($search) {
        $query->where('nama_barang', 'LIKE', '%' . $search . '%');
    }

    // Ambil data dan urutkan menggunakan Collection
    $barangs = $query->get()->sortBy(function($barang) {
        // Logika urutan:
        // 1. Barang dengan count 0 akan di paling atas
        // 2. Semakin besar count area_pembelian_count, semakin bawah posisinya
        return $barang->area_pembelian_count;
    });

    $selected = [];
    if ($areaId) {
        $area = AreaPembelian::find($areaId);
        if ($area) {
            $selected = $area->barangs->pluck('id')->toArray();
        }
    }

    return view('admin.asalbarang.partials.barang-list', [
        'barangs' => $barangs,
        'selected' => $selected,
        'currentAreaId' => $areaId
    ])->render();
}

    // Store
    public function store(Request $request)
    {
        $request->validate([
            'id_area_pembelian' => 'required|exists:area_pembelian,id',
            'barangs' => 'array',
        ]);

        $area = AreaPembelian::findOrFail($request->id_area_pembelian);

        $area->barangs()->sync($request->barangs ?? []);

        return redirect()
            ->route('admin.asalbarang.index')
            ->with('success', 'Asal barang berhasil diperbarui!');
    }

    // Edit
    public function edit($idArea)
    {
        $area = AreaPembelian::findOrFail($idArea);
        $barangs = Barang::with('subKategori.kategori')->get();
        $kategoris = \App\Models\Kategori::all();
        $subkategoris = \App\Models\Subkategori::all();

        $selected = $area->barangs->pluck('id')->toArray();

        return view('admin.asalbarang.edit', compact(
            'area',
            'barangs',
            'selected',
            'kategoris',
            'subkategoris'
        ));
    }

    // Update + konfirmasi perubahan
    public function update(Request $request, $idArea)
    {
        $request->validate([
            'barangs' => 'array',
        ]);

        $area = AreaPembelian::findOrFail($idArea);

        $old = $area->barangs->pluck('id')->toArray();
        $new = $request->barangs ?? [];

        $removed = array_diff($old, $new);
        $added = array_diff($new, $old);

        // Simpan
        $area->barangs()->sync($new);

        return redirect()
            ->route('admin.asalbarang.index')
            ->with('success', "Perubahan disimpan.
                Barang ditambahkan: " . implode(', ', $added) . "
                Barang dihapus: " . implode(', ', $removed));
    }
}

@extends('layouts.admin')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen" x-data="{ isModalOpen: false }">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ $barang->nama_barang }}</h1>
            <p class="text-sm text-gray-500">Detail satuan yang tersedia untuk barang ini.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.satuan-barang.index') }}" class="bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 font-bold py-2 px-6 rounded-full transition shadow-sm">
                Kembali
            </a>
            <button @click="isModalOpen = true" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-full shadow-md transition">
                + Tambah Satuan
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Info Barang Card --}}
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-200 h-fit">
            <h2 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4">Informasi Barang</h2>
            <div class="space-y-3">
                <div>
                    <label class="text-xs text-gray-500">Kode Barang</label>
                    <p class="font-semibold text-gray-800">{{ $barang->kode_barang ?? '-' }}</p>
                </div>
                <div>
                    <label class="text-xs text-gray-500">Kategori / Sub</label>
                    <p class="font-semibold text-gray-800">
                        {{ $barang->subKategori->kategori->kategori ?? 'N/A' }} 
                        <span class="text-blue-500">/</span> 
                        {{ $barang->subKategori->sub_kategori ?? 'N/A' }}
                    </p>
                </div>
                <div>
                    <label class="text-xs text-gray-500">Keterangan</label>
                    <p class="text-sm text-gray-600 italic">{{ $barang->keterangan ?? 'Tidak ada keterangan.' }}</p>
                </div>
            </div>
        </div>

        {{-- Tabel Satuan Card --}}
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Nama Satuan</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-center">Isi (Qty)</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($satuan_terpasang as $sb)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <span class="font-bold text-gray-700">{{ $sb->satuan->nama_satuan }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-bold">
                                    {{ $sb->satuan->jumlah }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <form action="{{ route('admin.satuan-barang.destroy', $sb->id) }}" method="POST" onsubmit="return confirm('Hapus satuan ini dari barang?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 font-medium text-sm transition">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-10 text-center text-gray-400 italic">
                                Belum ada satuan yang diatur untuk barang ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL TAMBAH (Alpine JS) --}}
    <div x-show="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" x-cloak>
        <div class="bg-white rounded-2xl w-full max-w-md p-6 shadow-2xl" @click.away="isModalOpen = false">
            <h2 class="text-xl font-bold mb-4 text-gray-800">Tambah Satuan Baru</h2>
            
            <form action="{{ route('admin.satuan-barang.store') }}" method="POST">
                @csrf
                <input type="hidden" name="id_barang" value="{{ $barang->id }}">
                
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Satuan</label>
                    <select name="id_satuan" class="w-full border-gray-300 rounded-xl p-3 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="">-- Pilih Satuan --</option>
                        @foreach($list_satuan as $s)
                            <option value="{{ $s->id }}" 
                                {{ $satuan_terpasang->pluck('id_satuan')->contains($s->id) ? 'disabled' : '' }}>
                                {{ $s->nama_satuan }} (Isi: {{ $s->jumlah }})
                                @if($satuan_terpasang->pluck('id_satuan')->contains($s->id)) [Sudah Ada] @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" @click="isModalOpen = false" class="px-5 py-2 text-gray-500 font-bold">Batal</button>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-full font-bold hover:bg-blue-700 transition">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection
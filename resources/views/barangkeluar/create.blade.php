@extends('layouts.admin')

@section('title', 'Tambah Barang Keluar')

@section('content')

{{-- Main Content --}}
<div class="flex-1 flex flex-col overflow-hidden">
    {{-- Header --}}
    <header class="flex justify-between items-center p-6 bg-white border-b-2 border-gray-200">
        <h1 class="text-2xl font-bold text-gray-800">Tambah Barang Keluar</h1>
        <div class="flex items-center">
            <span class="mr-4 font-semibold hidden sm:inline">Admin User</span>
            <div class="w-10 h-10 bg-blue-500 rounded-full"></div>
        </div>
    </header>

    {{-- Main Content --}}
    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6 md:p-10">
        <div class="container mx-auto">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-4 md:mb-0">Form Barang Keluar</h1>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md">
                <form action="{{ route('barangkeluar.store') }}" method="POST" id="formBarangKeluar">
                    @csrf

                    {{-- Barang Keluar --}}
                    <div class="mb-6">
                        <h2 class="font-semibold mb-3 text-gray-800">Daftar Barang Keluar</h2>
                        <div id="barangContainer" class="space-y-6">
                            {{-- Barang block --}}
                            <div class="barang-block border rounded-lg p-4 bg-gray-50">
                                <div class="flex justify-end items-center mb-3">
                                    <button type="button" class="btn-remove-barang text-red-600 hover:text-red-800 font-bold text-xl leading-none">&times;</button>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    <div class="col-span-1">
                                        <label class="block text-gray-700 font-semibold mb-2">Stok Warung</label>
                                        <select name="id_stok_warung[]" class="select-stok w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                                            <option value="">-- Pilih Stok Warung --</option>
                                            @foreach($stok_warungs as $stok)
                                                <option value="{{ $stok->id }}">{{ $stok->barang->nama_barang }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-span-1">
                                        <label class="block text-gray-700 font-semibold mb-2">Jumlah</label>
                                        <input type="number" name="jumlah[]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" min="1" required />
                                    </div>
                                    <div class="col-span-1">
                                        <label class="block text-gray-700 font-semibold mb-2">Jenis</label>
                                        <select name="jenis[]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                                            <option value="penjualan" selected>Penjualan</option>
                                            <option value="hutang">Hutang</option>
                                            <option value="expayet">Expayet</option>
                                            <option value="hilang">Hilang</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="button" id="btnAddBarang" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg mt-4 font-semibold">
                            + Tambah Barang
                        </button>
                    </div>

                    {{-- Keterangan --}}
                    <div class="mb-6">
                        <label for="keterangan" class="block text-gray-700 font-semibold mb-2">Keterangan (Opsional)</label>
                        <textarea name="keterangan" id="keterangan" rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('keterangan') }}</textarea>
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="flex justify-end space-x-4">
                        <a href="{{ route('barangkeluar.index') }}"
                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded-lg transition-colors duration-200">
                            Batal
                        </a>
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition-colors duration-200">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const barangContainer = document.getElementById('barangContainer');
        const btnAddBarang = document.getElementById('btnAddBarang');
        
        // Tambah Barang baru
        btnAddBarang.addEventListener('click', () => {
            const newBarang = barangContainer.querySelector('.barang-block').cloneNode(true);
            
            // Reset input
            newBarang.querySelector('select[name="id_stok_warung[]"]').value = "";
            newBarang.querySelector('input[name="jumlah[]"]').value = "";
            newBarang.querySelector('select[name="jenis[]"]').value = "penjualan";
            
            barangContainer.appendChild(newBarang);
        });
        
        // Delegasi event untuk tombol hapus
        document.addEventListener('click', e => {
            if (e.target.classList.contains('btn-remove-barang')) {
                const parentBlock = e.target.closest('.barang-block');
                if (barangContainer.querySelectorAll('.barang-block').length > 1) {
                    parentBlock.remove();
                } else {
                    alert('Minimal harus ada 1 barang keluar.');
                }
            }
        });
    });
</script>
@endsection
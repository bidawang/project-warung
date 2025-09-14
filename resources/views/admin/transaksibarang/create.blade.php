@extends('layouts.admin')

@section('title', 'Tambah Transaksi Barang')

@section('content')

{{-- Main Content --}}
<div class="flex-1 flex flex-col overflow-hidden">
    {{-- Header --}}
    <header class="flex justify-between items-center p-6 bg-white border-b-2 border-gray-200">
        <h1 class="text-2xl font-bold text-gray-800">Tambah Transaksi Barang</h1>
        <div class="flex items-center">
            <span class="mr-4 font-semibold hidden sm:inline">Admin User</span>
            <div class="w-10 h-10 bg-blue-500 rounded-full"></div>
        </div>
    </header>

    {{-- Main Content --}}
    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6 md:p-10">
        <div class="container mx-auto">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-4 md:mb-0">Form Transaksi Barang</h1>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md">
                <form action="{{ route('transaksibarang.store') }}" method="POST" id="formTransaksiBarang">
                    @csrf

                    {{-- Area Pembelian --}}
                    <div class="mb-6">
                        <h2 class="font-semibold mb-3 text-gray-800">Daftar Area Pembelian</h2>
                        <div id="areaContainer" class="space-y-6">
                            {{-- Area block --}}
                            <div class="area-block border rounded-lg p-4 bg-gray-50">
                                <div class="flex justify-between items-center mb-3">
                                    <h3 class="font-semibold text-gray-700">Area Pembelian</h3>
                                    <button type="button"
                                        class="btn-remove-area text-red-600 hover:text-red-800 font-bold text-xl leading-none">&times;</button>
                                </div>
                                <select name="id_area[]" class="select-area w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 mb-4" required>
                                    <option value="">-- Pilih Area --</option>
                                    @foreach($areas as $area)
                                    <option value="{{ $area->id }}">{{ $area->area }}</option>
                                    @endforeach
                                </select>

                                {{-- Barang --}}
                                <div>
                                    <h4 class="font-medium mb-2 text-gray-700">Barang di Area Ini</h4>
                                    <table class="w-full border-collapse border mb-3 barangTable">
                                        <thead class="bg-gray-100 text-gray-700">
                                            <tr>
                                                <th class="border px-2 py-1 text-left">Barang</th>
                                                <th class="border px-2 py-1 text-left">Jumlah</th>
                                                <th class="border px-2 py-1 text-left">Total Harga (Rp)</th>
                                                <th class="border px-2 py-1 w-10">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="border px-2 py-1">
                                                    <select name="id_barang[0][]" class="select-barang w-full border rounded-lg px-2 py-1" required>
                                                        <option value="">Pilih Barang</option>
                                                        @foreach($barangs as $barang)
                                                        <option value="{{ $barang->id }}" data-harga="{{ $barang->harga ?? 0 }}">
                                                            {{ $barang->nama_barang }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td class="border px-2 py-1">
                                                    <input type="number" name="jumlah[0][]" class="input-jumlah w-full border rounded-lg px-2 py-1" min="1" required />
                                                </td>
                                                <td class="border px-2 py-1">
                                                    <input type="text" name="total_harga[0][]" class="total-harga w-full border rounded-lg px-2 py-1" />
                                                </td>
                                                <td class="border px-2 py-1 text-center">
                                                    <button type="button" class="btn-remove-row text-red-600 hover:text-red-800 font-bold text-xl leading-none">&times;</button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <button type="button" class="btn-add-row bg-gray-200 hover:bg-gray-300 px-3 py-1 rounded-lg text-sm text-gray-700">+ Tambah Barang</button>
                                </div>
                            </div>
                        </div>

                        <button type="button" id="btnAddArea" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg mt-4 font-semibold">
                            + Tambah Area Pembelian
                        </button>
                    </div>

                    {{-- Transaksi Lain-Lain --}}
                    <div class="mb-6">
                        <h2 class="font-semibold mb-3 text-gray-800">Transaksi Lain-Lain</h2>
                        <div id="lainContainer" class="space-y-3 hidden"></div> {{-- Awalnya kosong/hidden --}}

                        <button type="button" id="btnAddLain"
                            class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg mt-4 font-semibold">
                            + Tambah Transaksi Lain-Lain
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
                        <a href="{{ route('transaksibarang.index') }}"
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

{{-- Script --}}
<script>
    document.addEventListener('DOMContentLoaded', () => {
        let areaIndex = 0;
        const areaContainer = document.getElementById('areaContainer');
        const btnAddArea = document.getElementById('btnAddArea');
        const allAreas = @json($areas -> pluck('id')); // semua ID area

        // Fungsi cek area duplikat & update tombol
        function updateAreaOptions() {
            const selected = Array.from(document.querySelectorAll('.select-area'))
                .map(sel => sel.value).filter(v => v);

            // Disable opsi yang sudah dipilih di select lain
            document.querySelectorAll('.select-area').forEach(sel => {
                sel.querySelectorAll('option').forEach(opt => {
                    if (opt.value && selected.includes(opt.value) && sel.value !== opt.value) {
                        opt.disabled = true;
                    } else {
                        opt.disabled = false;
                    }
                });
            });

            // Disable tombol tambah area jika semua sudah dipilih
            if (selected.length >= allAreas.length) {
                btnAddArea.disabled = true;
                btnAddArea.classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                btnAddArea.disabled = false;
                btnAddArea.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        }

        // Tambah Area baru
        btnAddArea.addEventListener('click', () => {
            areaIndex++;
            const newArea = areaContainer.querySelector('.area-block').cloneNode(true);

            // Reset select & input
            newArea.querySelector('select[name="id_area[]"]').value = "";
            newArea.querySelectorAll('tbody tr').forEach((tr, i) => {
                if (i > 0) tr.remove();
            });

            // Update name barang sesuai area index
            newArea.querySelectorAll('select[name^="id_barang"], input[name^="jumlah"], input[name^="total_harga"]').forEach(el => {
                if (el.name.includes('id_barang')) el.name = `id_barang[${areaIndex}][]`;
                if (el.name.includes('jumlah')) el.name = `jumlah[${areaIndex}][]`;
                if (el.name.includes('total_harga')) el.name = `total_harga[${areaIndex}][]`;
                el.value = "";
            });

            areaContainer.appendChild(newArea);
            updateAreaOptions();
        });

        // Delegasi event
        document.addEventListener('change', e => {
            if (e.target.classList.contains('select-area')) {
                updateAreaOptions();
            }
        });

        document.addEventListener('click', e => {
            if (e.target.classList.contains('btn-remove-area')) {
                if (areaContainer.querySelectorAll('.area-block').length > 1) {
                    e.target.closest('.area-block').remove();
                    updateAreaOptions();
                } else {
                    alert('Minimal harus ada 1 area pembelian.');
                }
            }
            if (e.target.classList.contains('btn-remove-row')) {
                const tbody = e.target.closest('tbody');
                if (tbody.rows.length > 1) {
                    e.target.closest('tr').remove();
                } else {
                    alert('Minimal harus ada 1 barang di area ini.');
                }
            }
            if (e.target.classList.contains('btn-add-row')) {
                const tbody = e.target.closest('.area-block').querySelector('tbody');
                const areaBlock = e.target.closest('.area-block');
                const areaIndex = Array.from(areaContainer.children).indexOf(areaBlock);
                const newRow = tbody.querySelector('tr').cloneNode(true);

                newRow.querySelectorAll('select, input').forEach(el => {
                    if (el.name.includes('id_barang')) el.name = `id_barang[${areaIndex}][]`;
                    if (el.name.includes('jumlah')) el.name = `jumlah[${areaIndex}][]`;
                    if (el.name.includes('total_harga')) el.name = `total_harga[${areaIndex}][]`;
                    el.value = "";
                });

                tbody.appendChild(newRow);
            }
        });

        // Transaksi Lain-Lain
        const lainContainer = document.getElementById('lainContainer');
        const btnAddLain = document.getElementById('btnAddLain');

        btnAddLain.addEventListener('click', () => {
            if (lainContainer.classList.contains('hidden')) {
                lainContainer.classList.remove('hidden');
            }

            const newLain = document.createElement('div');
            newLain.className = "lain-block border rounded-lg p-4 bg-gray-50";
            newLain.innerHTML = `
                <div class="flex justify-between items-center mb-3">
                    <h3 class="font-semibold text-gray-700">Item Lain-Lain</h3>
                    <button type="button"
                        class="btn-remove-lain text-red-600 hover:text-red-800 font-bold text-xl leading-none">&times;</button>
                </div>
                <div class="mb-3">
                    <label class="block text-sm text-gray-700 mb-1">Keterangan</label>
                    <input type="text" name="lain_keterangan[]" class="w-full border rounded-lg px-3 py-2"
                        placeholder="Misal: Ongkos kirim">
                </div>
                <div class="mb-3">
                    <label class="block text-sm text-gray-700 mb-1">Harga (Rp)</label>
                    <input type="number" name="lain_harga[]" class="w-full border rounded-lg px-3 py-2" min="0">
                </div>
            `;
            lainContainer.appendChild(newLain);
        });

        document.addEventListener('click', e => {
            if (e.target.classList.contains('btn-remove-lain')) {
                e.target.closest('.lain-block').remove();
                if (!lainContainer.querySelector('.lain-block')) {
                    lainContainer.classList.add('hidden');
                }
            }
        });

        // Inisialisasi awal
        updateAreaOptions();
    });
</script>
@endsection
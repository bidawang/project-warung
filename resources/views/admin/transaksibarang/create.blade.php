@extends('layouts.admin')

@section('title', 'Rencana Belanja')

@section('content')

{{-- Main Content Container --}}
<div class="flex-1 flex flex-col overflow-hidden">
    {{-- Header --}}
    <header class="flex justify-between items-center p-6 bg-white border-b-2 border-gray-200">
        <h1 class="text-2xl font-bold text-gray-800">Rencana Belanja</h1>
        <div class="flex items-center">
            <span class="mr-4 font-semibold hidden sm:inline">Admin User</span>
            <div class="w-10 h-10 bg-blue-500 rounded-full"></div>
        </div>
    </header>

    {{-- Main Content --}}
    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6 md:p-10">
        <div class="container mx-auto">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">Form Rencana Belanja</h1>

            {{-- START: Layout 2 Kolom --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                {{-- Kolom Kiri: Data Rencana Belanja (1/3 lebar) --}}
                <div class="md:col-span-1 bg-white p-6 rounded-lg shadow-md h-fit sticky top-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Rencana Belanja 📝</h2>

                    {{-- Tombol Opsi Tampilan --}}
                    <div class="flex justify-around mb-4 space-x-2">
                        <button type="button" data-view="warung" class="btn-view-toggle flex-1 bg-blue-600 text-white px-3 py-2 rounded-lg text-sm font-semibold transition-colors duration-200">
                            Berdasarkan Warung
                        </button>
                        <button type="button" data-view="barang" class="btn-view-toggle flex-1 bg-gray-300 text-gray-800 px-3 py-2 rounded-lg text-sm font-semibold transition-colors duration-200">
                            Berdasarkan Barang
                        </button>
                    </div>

                    {{-- Input Search JS --}}
                    <input type="text" id="searchRencana" onkeyup="filterRencanaBelanja()" placeholder="Cari Warung atau Barang..." class="w-full px-3 py-2 mb-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">

                    {{-- Konten Rencana Belanja --}}
                    <div id="rencanaBelanjaContainer" class="space-y-4 max-h-[70vh] overflow-y-auto pr-2">

                        {{-- Tampilan Berdasarkan Warung --}}
                        <div id="viewWarung" class="view-content space-y-4">
                            @forelse ($rencanaBelanjaByWarung as $namaWarung => $items)
                                <div class="item-block border p-3 rounded-lg bg-indigo-50" data-nama-utama="{{ $namaWarung }}">
                                    <h3 class="font-bold text-indigo-700 mb-2">{{ $namaWarung }}</h3>
                                    <ul class="list-disc list-inside text-sm text-gray-700 space-y-1">
                                        @foreach ($items as $item)
                                            <li data-item-nama="{{ $item->barang->nama_barang }}">
                                                {{ $item->barang->nama_barang }}: {{ $item->jumlah_awal}} pcs
                                                {{-- Tombol [+] DIHILANGKAN sesuai permintaan --}}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @empty
                                <p class="text-center text-gray-500">Tidak ada rencana belanja yang tertunda.</p>
                            @endforelse
                        </div>

                        {{-- Tampilan Berdasarkan Barang (Detail Warung) --}}
                        <div id="viewBarang" class="view-content space-y-4 hidden">
                            {{-- 1. Tampilan Total Barang --}}
                            <div id="viewBarangTotal" class="border p-4 rounded-lg bg-yellow-100 mb-6">
                                <h3 class="font-bold text-lg text-yellow-800 mb-3 border-b pb-2">Total Kebutuhan Barang</h3>
                                <ul class="list-none text-sm space-y-1" id="listTotalBarang">
                                    {{-- Anda perlu menyediakan variabel $rencanaBelanjaTotalByBarang di Controller Anda --}}
                                    @isset($rencanaBelanjaTotalByBarang)
                                        @forelse ($rencanaBelanjaTotalByBarang as $namaBarang => $totalKebutuhan)
                                            <li class="flex justify-between items-center py-1 border-b border-yellow-200" data-item-nama="{{ $namaBarang }}">
                                                <span class="font-semibold">{{ $namaBarang }}</span>
                                                <span class="text-yellow-700">{{ $totalKebutuhan }} pcs</span>
                                            </li>
                                        @empty
                                            <li><p class="text-gray-500">Tidak ada total kebutuhan barang.</p></li>
                                        @endforelse
                                    @endisset
                                </ul>
                            </div>

                            {{-- 2. Tampilan Detail Per Warung (untuk keperluan search) --}}
                            @forelse ($rencanaBelanjaByBarang as $namaBarang => $items)
                                <div class="item-block border p-3 rounded-lg bg-green-50" data-nama-utama="{{ $namaBarang }}">
                                    <h3 class="font-bold text-green-700 mb-2">{{ $namaBarang }}</h3>
                                    <ul class="list-disc list-inside text-sm text-gray-700 space-y-1">
                                        @foreach ($items as $item)
                                            <li data-item-nama="{{ $item->warung->nama_warung }}">
                                                {{ $item->warung->nama_warung }}: {{ $item->jumlah_awal}} pcs
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @empty
                                <p class="text-center text-gray-500">Tidak ada rencana belanja yang tertunda.</p>
                            @endforelse
                        </div>

                    </div>
                </div>

                {{-- Kolom Kanan: Form Transaksi (2/3 lebar) --}}
                <div class="md:col-span-2 bg-white p-6 rounded-lg shadow-md">
                    {{-- ... Form Transaksi Tetap Sama ... --}}
                    <form action="{{ route('admin.transaksibarang.store') }}" method="POST" id="formTransaksiBarang">
                        @csrf
                        {{-- Area Pembelian --}}
                        <div class="mb-6">
                            <h2 class="font-semibold mb-3 text-gray-800">Daftar Area Pembelian</h2>
                            <div id="areaContainer" class="space-y-6">
                                {{-- Area block (Template) --}}
                                <div class="area-block border rounded-lg p-4 bg-gray-50">
                                    <div class="flex justify-between items-center mb-3">
                                        <h3 class="font-semibold text-gray-700">Area Pembelian <span class="area-index-label">1</span></h3>
                                        <button type="button"
                                            class="btn-remove-area text-red-600 hover:text-red-800 font-bold text-xl leading-none">&times;</button>
                                    </div>
                                    <select name="id_area[]" class="select-area w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 mb-4" required>
                                        <option value="">-- Pilih Area --</option>
                                        @foreach($areas as $area)
                                        <option value="{{ $area->id }}">{{ $area->area }} {{$area['markup']}}%</option>
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
                                                    <th class="border px-2 py-1 text-left">Tgl Kadaluarsa</th>
                                                    <th class="border px-2 py-1 w-10">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {{-- Template Baris Barang --}}
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
                                                        <input type="text" name="total_harga[0][]" class="total-harga w-full border rounded-lg px-2 py-1" required  />
                                                    </td>
                                                    <td class="border px-2 py-1">
                                                        <input type="date" name="tanggal_kadaluarsa[0][]" class="input-tgl-kadaluarsa w-full border rounded-lg px-2 py-1" />
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
                            <a href="{{ route('admin.transaksibarang.index') }}"
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
            {{-- END: Layout 2 Kolom --}}

        </div>
    </main>
</div>

<script>
    // Pastikan jQuery sudah dimuat sebelum Select2
    document.addEventListener('DOMContentLoaded', () => {
        let areaIndex = 0;
        const areaContainer = document.getElementById('areaContainer');
        const btnAddArea = document.getElementById('btnAddArea');
        const allAreas = @json($areas->pluck('id')); // semua ID area
        const searchInput = document.getElementById('searchRencana');

        // --- FUNGSI SELECT2 BARU ---
        // Fungsi untuk inisialisasi Select2 pada semua select-barang
        function initializeSelect2() {
            // Hancurkan Select2 yang mungkin sudah ada sebelum inisialisasi ulang
            $('.select-barang').each(function() {
                if ($(this).data('select2')) {
                    $(this).select2('destroy');
                }
            });

            // Inisialisasi Select2 pada semua select-barang
            $('.select-barang').select2({
                placeholder: 'Pilih Barang',
                allowClear: true,
                width: '100%'
            });
        }

        // Panggil inisialisasi saat DOMContentLoaded
        initializeSelect2();
        // --- END FUNGSI SELECT2 BARU ---


        // Inisialisasi index area pada blok pertama
        areaContainer.querySelector('.area-index-label').textContent = '1';

        // Fungsi JS untuk Pencarian/Filter Rencana Belanja (TETAP SAMA)
        // ... (window.filterRencanaBelanja function) ...
        window.filterRencanaBelanja = function() {
            // (Isi fungsi filterRencanaBelanja() tetap sama)
            const filter = searchInput.value.toLowerCase();

            // Dapatkan view yang sedang aktif
            const activeViewId = document.querySelector('.view-content:not(.hidden)').id;

            // Jika view-nya adalah viewBarang, tampilkan/sembunyikan juga Total Kebutuhan
            const viewBarangTotal = document.getElementById('viewBarangTotal');
            if (activeViewId === 'viewBarang') {
                // Untuk view Barang, kita filter total list-nya dan juga detailnya

                // Filter Total Kebutuhan Barang
                const listTotalBarang = document.getElementById('listTotalBarang');
                let totalFoundInTotalList = 0;
                listTotalBarang.querySelectorAll('li').forEach(item => {
                    const namaBarang = item.dataset.itemNama.toLowerCase();
                    if (namaBarang.includes(filter)) {
                        item.classList.remove('hidden');
                        totalFoundInTotalList++;
                    } else {
                        item.classList.add('hidden');
                    }
                });
                // Sembunyikan/tampilkan blok Total Kebutuhan jika tidak ada hasil
                if (filter.length > 0 && totalFoundInTotalList === 0) {
                    viewBarangTotal.classList.add('hidden');
                } else {
                    viewBarangTotal.classList.remove('hidden');
                }
            } else if (activeViewId === 'viewWarung') {
                 // Pastikan Total Kebutuhan tersembunyi jika view Warung
                 if(viewBarangTotal) viewBarangTotal.classList.add('hidden');
            }


            // Filter Item Block (viewWarung atau viewBarang detail)
            document.querySelectorAll('.view-content:not(.hidden) .item-block').forEach(block => {
                const namaUtama = block.dataset.namaUtama.toLowerCase(); // Warung atau Barang
                let found = false;

                // Cek nama utama (Warung atau Barang)
                if (namaUtama.includes(filter)) {
                    found = true;
                }

                // Cek item detail di dalam blok (Barang atau Warung)
                block.querySelectorAll('li').forEach(listItem => {
                    const itemNama = listItem.dataset.itemNama.toLowerCase(); // Barang atau Warung
                    if (itemNama.includes(filter)) {
                        found = true;
                        listItem.classList.remove('hidden');
                    } else {
                        listItem.classList.add('hidden');
                    }
                });

                // Tampilkan/Sembunyikan blok
                if (found) {
                    block.classList.remove('hidden');
                } else {
                    block.classList.add('hidden');
                }
            });

        };
        // END: Fungsi JS untuk Pencarian
    $(document).ready(function() {
        $('.select-barang').select2();
    });

        // Fungsi update nama input untuk index area yang benar (TETAP SAMA)
        function reindexAreaBlocks() {
            areaContainer.querySelectorAll('.area-block').forEach((areaBlock, index) => {
                areaBlock.querySelector('.area-index-label').textContent = (index + 1);

                areaBlock.querySelectorAll('select[name^="id_barang"], input[name^="jumlah"], input[name^="total_harga"], input[name^="tanggal_kadaluarsa"]').forEach(el => {
                    if (el.name.includes('id_barang')) el.name = `id_barang[${index}][]`;
                    else if (el.name.includes('jumlah')) el.name = `jumlah[${index}][]`;
                    else if (el.name.includes('total_harga')) el.name = `total_harga[${index}][]`;
                    else if (el.name.includes('tanggal_kadaluarsa')) el.name = `tanggal_kadaluarsa[${index}][]`;
                });
            });
            // Update areaIndex global
            areaIndex = areaContainer.querySelectorAll('.area-block').length - 1;
        }

        // Fungsi cek area duplikat & update tombol (TETAP SAMA)
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

        // Fungsi untuk mengkalkulasi total harga (TETAP SAMA)
        function calculateTotalHarga(row) {
            const selectBarang = row.querySelector('.select-barang');
            const inputJumlah = row.querySelector('.input-jumlah');
            const totalHargaInput = row.querySelector('.total-harga');

            // Ambil harga dari data-harga attribute pada option yang terpilih
            const selectedOption = selectBarang.options[selectBarang.selectedIndex];
            const hargaPerBarang = selectedOption ? selectedOption.getAttribute('data-harga') : null;
            const jumlah = inputJumlah.value;

            if (hargaPerBarang && jumlah && jumlah > 0) {
                const total = parseFloat(hargaPerBarang) * parseInt(jumlah);
                totalHargaInput.value = total;
            } else {
                totalHargaInput.value = 0;
            }
        }

        // Tambah Area baru
        btnAddArea.addEventListener('click', () => {
            // Hancurkan Select2 sebelum kloning
            areaContainer.querySelectorAll('.select-barang').forEach(sel => {
                if ($(sel).data('select2')) {
                    $(sel).select2('destroy');
                }
            });

            const newArea = areaContainer.querySelector('.area-block').cloneNode(true);

            // Inisialisasi ulang Select2 pada semua select (termasuk yang dikloning)
            setTimeout(initializeSelect2, 0);
            // Timeout 0ms untuk memastikan kloning selesai dan DOM diperbarui sebelum inisialisasi

            // Reset select & input
            newArea.querySelector('select[name="id_area[]"]').value = "";
            newArea.querySelectorAll('tbody tr').forEach((tr, i) => {
                if (i > 0) tr.remove(); // Hapus baris barang selain baris pertama
            });

            // Reset dan atur ulang baris barang pertama
            const firstRow = newArea.querySelector('tbody tr');
            firstRow.querySelector('.select-barang').value = "";
            firstRow.querySelector('.input-jumlah').value = "";
            firstRow.querySelector('.total-harga').value = "";
            firstRow.querySelector('.input-tgl-kadaluarsa').value = "";

            areaContainer.appendChild(newArea);
            reindexAreaBlocks();
            updateAreaOptions();

            // Panggil inisialisasi Select2 lagi setelah penambahan area
            // Perlu dipanggil lagi karena kloning merusak Select2, dan reindex
            initializeSelect2();
        });

        // Delegasi event untuk perubahan area, hapus area, tambah/hapus row, dan input barang (TETAP SAMA)
        document.addEventListener('change', e => {
            if (e.target.classList.contains('select-area')) {
                updateAreaOptions();
            } else if (e.target.classList.contains('select-barang')) {
                // Select2 memicu event change pada select asli.
                calculateTotalHarga(e.target.closest('tr'));
            }
        });

        document.addEventListener('input', e => {
            if (e.target.classList.contains('input-jumlah')) {
                calculateTotalHarga(e.target.closest('tr'));
            }
        });

        document.addEventListener('click', e => {
            if (e.target.classList.contains('btn-remove-area')) {
                if (areaContainer.querySelectorAll('.area-block').length > 1) {
                    // Hancurkan Select2 sebelum remove
                    e.target.closest('.area-block').querySelectorAll('.select-barang').forEach(sel => {
                        if ($(sel).data('select2')) {
                            $(sel).select2('destroy');
                        }
                    });

                    e.target.closest('.area-block').remove();
                    reindexAreaBlocks(); // Perlu re-index setelah dihapus
                    updateAreaOptions();

                    // Inisialisasi ulang Select2 pada area yang tersisa
                    initializeSelect2();

                } else {
                    alert('Minimal harus ada 1 area pembelian.');
                }
            }
            if (e.target.classList.contains('btn-remove-row')) {
                const tbody = e.target.closest('tbody');
                if (tbody.rows.length > 1) {
                    // Hancurkan Select2 sebelum remove
                    const selectBarang = e.target.closest('tr').querySelector('.select-barang');
                    if ($(selectBarang).data('select2')) {
                        $(selectBarang).select2('destroy');
                    }

                    e.target.closest('tr').remove();
                    // Tidak perlu initializeSelect2 karena hanya 1 baris yang dihapus
                } else {
                    alert('Minimal harus ada 1 barang di area ini.');
                }
            }
            if (e.target.classList.contains('btn-add-row')) {
                const tbody = e.target.closest('.area-block').querySelector('tbody');
                const areaBlock = e.target.closest('.area-block');
                const areaIndex = Array.from(areaContainer.children).indexOf(areaBlock);

                // Clone the row using jQuery to handle Select2 properly
                // Hancurkan Select2 pada baris template sebelum kloning
                const templateRow = tbody.querySelector('tr');
                const selectBarangTemplate = templateRow.querySelector('.select-barang');
                if ($(selectBarangTemplate).data('select2')) {
                    $(selectBarangTemplate).select2('destroy');
                }

                // Clone baris template
                const newRow = templateRow.cloneNode(true);

                // Reset values and update names for the new row
                newRow.querySelectorAll('select, input').forEach(el => {
                    if (el.name.includes('id_barang')) el.name = `id_barang[${areaIndex}][]`;
                    else if (el.name.includes('jumlah')) el.name = `jumlah[${areaIndex}][]`;
                    else if (el.name.includes('total_harga')) el.name = `total_harga[${areaIndex}][]`;
                    else if (el.name.includes('tanggal_kadaluarsa')) el.name = `tanggal_kadaluarsa[${areaIndex}][]`;

                    el.value = (el.tagName === 'SELECT') ? "" : (el.type === 'number' ? "" : "");
                    if(el.classList.contains('total-harga')) el.value = "0"; // Pastikan 0 atau kosong
                });

                tbody.appendChild(newRow);

                // Panggil inisialisasi Select2 untuk semua select-barang di area ini
                initializeSelect2();
            }

            // ... (Aksi Lain-Lain dan Toggle View tetap sama) ...

            // Aksi Tambah Transaksi Lain-Lain
            if (e.target.classList.contains('btn-remove-lain')) {
                e.target.closest('.lain-block').remove();
                const lainContainer = document.getElementById('lainContainer');
                if (!lainContainer.querySelector('.lain-block')) {
                    lainContainer.classList.add('hidden');
                }
            }

            // Toggle Tampilan Rencana Belanja
            if (e.target.classList.contains('btn-view-toggle')) {
                document.querySelectorAll('.btn-view-toggle').forEach(btn => {
                    btn.classList.remove('bg-blue-600', 'text-white');
                    btn.classList.add('bg-gray-300', 'text-gray-800');
                });

                e.target.classList.remove('bg-gray-300', 'text-gray-800');
                e.target.classList.add('bg-blue-600', 'text-white');

                const targetView = e.target.dataset.view;

                document.querySelectorAll('.view-content').forEach(content => {
                    content.classList.add('hidden');
                });

                document.getElementById('view' + targetView.charAt(0).toUpperCase() + targetView.slice(1)).classList.remove('hidden');

                // Panggil filter ulang setelah ganti view
                filterRencanaBelanja();
            }

            // Menambahkan item rencana belanja ke form
            // CATATAN: Karena tombol [+] DIHILANGKAN, bagian ini (btn-add-item-to-form) seharusnya tidak akan terpanggil
            // Namun, jika Anda ingin mengaktifkannya kembali, pastikan Anda menambahkan tombol [+] ke Blade.
            if (e.target.classList.contains('btn-add-item-to-form')) {
                const itemId = e.target.dataset.itemId;
                const itemNama = e.target.dataset.itemNama;
                const itemHarga = e.target.dataset.itemHarga;

                // Temukan area block pertama atau area yang sesuai
                const firstAreaBlock = areaContainer.querySelector('.area-block');
                const tbody = firstAreaBlock.querySelector('tbody');
                const areaIndex = Array.from(areaContainer.children).indexOf(firstAreaBlock);

                // ... (Logika penambahan item ke form tetap sama) ...
            }
        });


        // Transaksi Lain-Lain (tetap seperti sebelumnya)
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

        // Inisialisasi awal
        updateAreaOptions();
    });
</script>
@endsection

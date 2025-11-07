@extends('layouts.admin')

@section('title', 'Manajemen Varian Kuantitas Barang')

@section('content')
<div class="flex-1 flex flex-col overflow-hidden">
    {{-- Header --}}
    <header class="p-6 bg-white border-b-2 border-gray-200 shadow-sm">
        <h1 class="text-2xl font-bold text-gray-800">Manajemen Varian Kuantitas</h1>
    </header>

    {{-- Notifikasi & Errors --}}
    <div class="px-6 pt-3">
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 p-4 mb-4 rounded-xl shadow-md">
                <p class="font-bold mb-1">Terjadi Kesalahan Validasi di Server:</p>
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if(session('success'))
            <div class="bg-green-100 border border-green-300 text-green-700 p-4 mb-4 rounded-xl shadow-md">
                {{ session('success') }}
            </div>
        @endif
        {{-- Tempat pesan error JS akan muncul untuk FORM CREATE --}}
        <div id="js-error-message" class="hidden bg-red-100 border border-red-400 text-red-700 p-4 mb-4 rounded-xl shadow-md"></div>
    </div>

    {{-- Main Content - Split View --}}
    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-6 md:p-10">
        <div class="container mx-auto grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- Kolom Kanan: Form Penambahan Kuantitas (1/3) --}}
            <div class="lg:col-span-1">
                <div class="bg-white p-6 rounded-xl shadow-xl h-fit sticky top-6 border border-gray-100">
                    <h2 class="text-xl font-bold text-gray-800 mb-6 border-b pb-3">
                        ➕ Buat Varian Kuantitas Baru
                    </h2>

                    {{-- Form Pilihan Stok Warung (Selalu Ada) --}}
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            1. Stok Warung & Harga Dasar
                        </label>
                        @if(isset($selectedStokWarung))
                            @php
                                // Ambil hanya data jumlah (unique key) dan harga_jual (value) untuk validasi create
                                // Menggunakan $kuantitasRecords yang sudah difilter
                                $kuantitasMapForCreate = $kuantitasRecords->pluck('harga_jual', 'jumlah');
                            @endphp
                            {{-- Info Stok Warung Terpilih (Data Attribute di sini lebih rapi) --}}
                            <div id="stok-info-box"
                                data-base-price="{{ $hargaJualSatuanDasar }}"
                                data-existing-jumlah-map="{{ json_encode($kuantitasMapForCreate->toArray()) }}"
                                class="p-4 border border-indigo-400 rounded-lg bg-indigo-50 text-indigo-800 text-sm shadow-inner">
                                <span class="block text-base font-semibold">📦 {{ $selectedStokWarung->barang->nama_barang }}</span>
                                <span class="block mt-1 text-xs">@ {{ $selectedStokWarung->warung->nama_warung }}</span>
                                <span class="block mt-2 font-bold text-green-700">
                                    Harga Satuan Dasar: Rp {{ number_format($hargaJualSatuanDasar, 0, ',', '.') }}
                                </span>
                            </div>
                            <input type="hidden" name="id_stok_warung" value="{{ $selectedStokWarung->id }}">
                        @endif
                    </div>

                    @if(isset($selectedStokWarung))
                    <form id="kuantitasForm" action="{{ route('admin.kuantitas.store') }}" method="POST" onsubmit="return validateForm()">
                        @csrf
                        <input type="hidden" name="id_stok_warung" value="{{ $selectedStokWarung->id }}">

                        {{-- Form Input Detail Varian --}}
                        <div class="space-y-5 border-t pt-5 border-gray-200">
                            <h3 class="font-bold text-lg text-gray-700 mb-3">2. Detail Varian Harga</h3>

                            <div>
                                <label for="jumlah" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Barang per Kuantitas (pcs)</label>
                                <input type="number" name="jumlah" id="jumlah" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 shadow-sm"
                                    value="{{ old('jumlah', 2) }}" min="2" required
                                    oninput="calculatePriceSuggestion()"
                                    data-old-value="{{ old('jumlah', 2) }}">
                                <small class="text-xs text-gray-500 mt-1 block">Contoh: 10 untuk harga khusus jika beli 10 pcs.</small>
                            </div>

                            <div>
                                <label for="harga_jual" class="block text-sm font-medium text-gray-700 mb-1">Harga Jual Kuantitas (Rp)</label>
                                <input type="number" name="harga_jual" id="harga_jual" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 shadow-sm font-semibold" min="0" required
                                    value="{{ old('harga_jual', $hargaJualSatuanDasar > 0 ? $hargaJualSatuanDasar * old('jumlah', 2) : '') }}"
                                    placeholder="Total Harga untuk Jumlah Kuantitas di atas">

                                <small id="price-suggestion-note" class="mt-2 text-xs text-blue-700 block p-2 bg-blue-50 border border-blue-200 rounded">
                                    Harga Satuan Asumsi: **Rp {{ number_format($hargaJualSatuanDasar, 0, ',', '.') }}**
                                </small>
                            </div>
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="flex items-center mt-8 pt-5 border-t border-gray-200">
                            <button type="submit" class="w-full px-6 py-3 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 shadow-md">
                                Simpan Varian
                            </button>
                        </div>
                    </form>
                    @endif
                </div>
            </div>

            {{-- Kolom Kiri: Riwayat Kuantitas (2/3) - LIST (BAGIAN PERBAIKAN UTAMA) --}}
            <div class="lg:col-span-2">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Daftar Varian Kuantitas Terdaftar</h2>

                <div class="space-y-6">
                    @if(isset($selectedStokWarung) && $kuantitasRecords->isNotEmpty())
                        @php
                            $barangName = $selectedStokWarung->barang->nama_barang ?? 'Barang Tidak Ditemukan';
                            $kuantitasBasePrice = $hargaJualSatuanDasar;

                            // Collect all existing (id, jumlah, harga_jual) pairs for client-side EDIT validation
                            $existingKuantitasJson = json_encode(
                                $kuantitasRecords->mapWithKeys(function ($kuantitas) {
                                    return [$kuantitas->id => ['jumlah' => $kuantitas->jumlah, 'harga_jual' => $kuantitas->harga_jual]];
                                })->toArray()
                            );
                        @endphp

                        <div class="border border-gray-300 rounded-xl p-5 bg-white shadow-lg">
                            <h3 class="font-bold text-xl text-indigo-700 mb-3 border-b pb-2 flex justify-between items-center">
                                <span>{{ $barangName }}</span>
                                <span class="text-sm font-medium text-gray-500">{{ $kuantitasRecords->count() }} Varian</span>
                            </h3>

                            {{-- LIST Varian Kuantitas --}}
                            <div class="mt-4 border-t border-gray-100">
                                @foreach($kuantitasRecords->sortByDesc('created_at') as $kuantitas)
                                    @php
                                        $kuantitasId = $kuantitas->id;
                                        $hargaSatuanHitung = $kuantitas->harga_jual / max($kuantitas->jumlah, 1);
                                        // Karena kita hanya menampilkan yang dipilih, ini selalu true (untuk styling)
                                        $isFromSelectedWarung = true;
                                        $isDiscounted = $kuantitasBasePrice * $kuantitas->jumlah > $kuantitas->harga_jual;
                                    @endphp

                                    <div class="flex items-center justify-between p-3 border-b border-gray-100 text-sm {{ $isFromSelectedWarung ? 'bg-green-50/70 font-semibold' : 'hover:bg-gray-50' }}"
                                        data-kuantitas-id="{{ $kuantitasId }}"
                                        data-stok-warung-id="{{ $kuantitas->id_stok_warung }}"
                                        data-barang-name="{{ $barangName }}"
                                        data-base-price="{{ $kuantitasBasePrice }}">

                                        {{-- Data Varian --}}
                                        <div class="flex-1 min-w-0 grid grid-cols-2 md:grid-cols-4 gap-2">

                                            {{-- Jumlah --}}
                                            <div class="font-bold text-gray-800 flex items-center">
                                                <svg class="inline-block w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                                {{ number_format($kuantitas->jumlah) }} pcs
                                            </div>

                                            {{-- Harga Total --}}
                                            <div class="font-extrabold flex items-center {{ $isDiscounted ? 'text-red-600' : 'text-blue-700' }}">
                                                Rp {{ number_format($kuantitas->harga_jual, 0, ',', '.') }}
                                            </div>

                                            {{-- Harga Satuan Efektif --}}
                                            <div class="text-gray-600 text-xs md:text-sm flex items-center">
                                                <span class="text-xs italic mr-1 hidden md:inline">Satuan Eff:</span> Rp {{ number_format($hargaSatuanHitung, 0, ',', '.') }}
                                            </div>

                                            {{-- Warung --}}
                                            <div class="text-xs text-gray-500 flex items-center">
                                                <span class="hidden md:inline">Warung:</span> {{ $kuantitas->stokWarung->warung->nama_warung ?? 'N/A' }}
                                            </div>

                                        </div>

                                        {{-- Tombol Aksi --}}
                                        <div class="flex space-x-2 ml-4 flex-shrink-0">
                                            {{-- Tombol Edit: Menggunakan target ID modal unik. Data JSON untuk Edit dipanggil di JS --}}
                                            <button type="button"
                                                onclick="setupEditModal('editModal-{{ $kuantitasId }}', {{ $kuantitasId }}, {{ $kuantitasBasePrice }}, '{{ $existingKuantitasJson }}')"
                                                class="text-xs text-blue-500 hover:text-blue-700 font-medium p-1 rounded-md hover:bg-blue-50">
                                                Edit
                                            </button>

                                            {{-- Tombol Delete (trigger custom modal) --}}
                                            <button type="button"
                                                onclick="setDeleteTarget('{{ route('admin.kuantitas.destroy', $kuantitasId) }}', '{{ $kuantitas->jumlah }} pcs')"
                                                class="text-xs text-red-500 hover:text-red-700 font-medium p-1 rounded-md hover:bg-red-50">
                                                Hapus
                                            </button>
                                        </div>
                                    </div>

                                    {{-- MODAL EDIT KUANTITAS (Dibiarkan di dalam perulangan) --}}
                                    <div id="editModal-{{ $kuantitasId }}" class="modal-template fixed inset-0 bg-gray-600 bg-opacity-75 hidden items-center justify-center z-50 opacity-0 transition-opacity duration-300">
                                        <div class="modal-content bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4 p-6 transform transition-transform duration-300 scale-95">
                                            <h3 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Edit Varian Kuantitas</h3>

                                            <div id="modal-js-error-{{ $kuantitasId }}" class="hidden bg-red-100 border border-red-400 text-red-700 p-3 mb-3 rounded-lg text-sm"></div>

                                            <form id="editKuantitasForm-{{ $kuantitasId }}" action="{{ route('admin.kuantitas.update', $kuantitasId) }}" method="POST" onsubmit="return validateEditForm(event, {{ $kuantitasId }})">
                                                @csrf
                                                @method('PUT')

                                                {{-- Hidden fields untuk membawa data ke JS validation --}}
                                                <input type="hidden" name="id_stok_warung" value="{{ $kuantitas->id_stok_warung }}">
                                                <input type="hidden" id="edit_base_price_{{ $kuantitasId }}" value="{{ $kuantitasBasePrice }}">
                                                <input type="hidden" id="edit_existing_data_{{ $kuantitasId }}" value="{{ $existingKuantitasJson }}">

                                                <p class="text-sm text-gray-600 mb-4">Barang: <span class="font-semibold text-indigo-700">{{ $barangName }}</span></p>

                                                <div class="mb-4">
                                                    <label for="modal_jumlah_{{ $kuantitasId }}" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Barang (pcs)</label>
                                                    <input type="number" name="jumlah" id="modal_jumlah_{{ $kuantitasId }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" min="2" required value="{{ $kuantitas->jumlah }}">
                                                </div>

                                                <div class="mb-6">
                                                    <label for="modal_harga_jual_{{ $kuantitasId }}" class="block text-sm font-medium text-gray-700 mb-1">Harga Jual Kuantitas (Rp)</label>
                                                    <input type="number" name="harga_jual" id="modal_harga_jual_{{ $kuantitasId }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 font-semibold" min="0" required value="{{ $kuantitas->harga_jual }}">
                                                    <small class="text-xs text-gray-500 mt-1 block">Harga ini bisa lebih rendah dari harga standar ({{ number_format($kuantitasBasePrice) }} x {{ $kuantitas->jumlah }} = {{ number_format($kuantitasBasePrice * $kuantitas->jumlah) }}) untuk diskon.</small>
                                                </div>

                                                <div class="flex justify-end space-x-3">
                                                    <button type="button" onclick="toggleModal('editModal-{{ $kuantitasId }}')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-medium">Batal</button>
                                                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-bold">Simpan Perubahan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    {{-- AKHIR MODAL EDIT KUANTITAS --}}
                                @endforeach
                            </div>
                        </div>
                    {{-- KONDISI JIKA SELECTED TAPI KOSONG --}}
                    @elseif(isset($selectedStokWarung) && $kuantitasRecords->isEmpty())
                        <div class="p-5 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 rounded-lg shadow-md">
                            <p class="text-base font-semibold">⚠️ Belum Ada Varian Kuantitas Terdaftar</p>
                            <p class="text-sm mt-1">Gunakan formulir di sebelah kanan untuk menambahkan varian harga untuk **{{ $selectedStokWarung->barang->nama_barang ?? 'Barang' }}** di **{{ $selectedStokWarung->warung->nama_warung ?? 'Warung' }}**.</p>
                        </div>
                    {{-- KONDISI DEFAULT (TIDAK ADA YANG DIPILIH) --}}
                    @else
                        <div class="p-5 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 rounded-lg shadow-md">
                            <p class="text-base font-semibold">⚠️ Belum Ada Stok Warung Dipilih</p>
                            <p class="text-sm mt-1">Pilih Stok Warung (Barang dan Warung) untuk mulai menambahkan varian harga.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </main>
</div>

{{-- MODAL KONFIRMASI PENGHAPUSAN (Global) --}}
<div id="deleteConfirmModal" class="modal-template fixed inset-0 bg-gray-600 bg-opacity-75 hidden items-center justify-center z-50 opacity-0 transition-opacity duration-300">
    <div class="modal-content bg-white rounded-xl shadow-2xl w-full max-w-sm mx-4 p-6 transform transition-transform duration-300 scale-95">
        <h3 class="text-xl font-bold text-red-700 mb-4 border-b pb-2">Konfirmasi Penghapusan</h3>
        <p class="text-gray-700 mb-6">Apakah Anda yakin ingin menghapus varian <strong id="delete-item-name"></strong> ini?</p>
        <p class="text-sm text-gray-500 mb-6">Aksi ini tidak dapat dibatalkan.</p>
        <div class="flex justify-end space-x-3">
            <button type="button" onclick="toggleModal('deleteConfirmModal')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-medium">Batal</button>
            <form id="global-delete-form" method="POST" class="inline-block">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-bold">Ya, Hapus Permanen</button>
            </form>
        </div>
    </div>
</div>
@include('admin.kuantitas.script')
@endsection

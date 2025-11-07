@extends('layouts.admin')

@section('title', 'Monitor Detail Harga Seluruh Barang di Semua Warung')

@section('content')

<div class="flex-1 flex flex-col overflow-hidden">
    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6 md:p-10">
        <div class="container mx-auto">

            {{-- Header halaman --}}
            <div class="mb-6 flex justify-between items-center">
                <h1 class="text-3xl font-bold text-gray-800">
                    📈 Detail Harga Seluruh Barang per Warung
                </h1>
                <a href="{{ route('admin.warung.index') }}"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none">
                    Kembali ke Menu Utama
                </a>
            </div>

            {{-- Alert Notifikasi --}}
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            {{-- Deskripsi Peringatan --}}
            <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6 rounded-md">
                <p class="text-sm text-red-700 font-semibold">
                    ⚠️ Halaman ini memuat **SEMUA** harga barang di **SEMUA** warung aktif. Proses loading mungkin memerlukan waktu jika data sangat banyak.
                </p>
            </div>

            {{-- Loop per Barang --}}
            @forelse ($barangPricesByWarung as $item)
                <div class="bg-white rounded-xl shadow-lg p-6 mb-8 break-inside-avoid-page">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4 border-b pb-2">
                        Barang: {{ $item->barang->nama_barang }}
                        <span class="text-sm font-normal text-gray-500 ml-2">({{ $item->prices->count() }} Warung Terdampak)</span>
                    </h2>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Warung (Area)</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Stok</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Harga Modal</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Harga Jual Range</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Laba (%)</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Laba (Rp)</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($item->prices as $priceData)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-2 text-sm">{{ $loop->iteration }}</td>
                                        <td class="px-3 py-2 text-sm font-semibold">
                                            {{ $priceData->nama_warung }}
                                            <span class="text-xs text-gray-500">({{ $priceData->area }})</span>
                                        </td>
                                        <td class="px-3 py-2 text-sm {{ $priceData->stok_saat_ini <= 0 ? 'text-red-600 font-bold' : '' }}">
                                            {{ $priceData->stok_saat_ini }}
                                        </td>
                                        <td class="px-3 py-2 text-sm">
                                            Rp {{ number_format($priceData->harga_modal, 0, ',', '.') }}
                                        </td>
                                        <td class="px-3 py-2 text-sm">
                                            @if ($priceData->harga_jual_range_awal > 0)
                                                Rp {{ number_format($priceData->harga_jual_range_awal, 0, ',', '.') }} - Rp {{ number_format($priceData->harga_jual_range_akhir, 0, ',', '.') }}
                                            @else
                                                <span class="text-gray-400 italic">Belum Diatur</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2 text-sm font-bold">
                                            <span class="{{ str_contains($priceData->persentase_laba, '-') ? 'text-red-600' : 'text-green-600' }}">
                                                {{ $priceData->persentase_laba }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-2 text-sm font-bold">
                                            <span class="{{ str_contains($priceData->laba_range, '-') ? 'text-red-600' : 'text-green-600' }}">
                                                Rp {{ $priceData->laba_range }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-2 text-sm">
                                            {{-- Tombol Edit Harga (Memanggil Modal) --}}
                                            <button type="button"
                                                class="edit-price-btn text-white bg-yellow-500 hover:bg-yellow-600 px-3 py-1 rounded-md text-xs font-medium transition duration-150"
                                                data-warung-id="{{ $priceData->warung_id }}"
                                                data-warung-name="{{ $priceData->nama_warung }}"
                                                data-barang-id="{{ $item->barang->id }}"
                                                data-barang-name="{{ $item->barang->nama_barang }}"
                                                data-modal="{{ $priceData->harga_modal }}"
                                                data-awal="{{ $priceData->harga_jual_range_awal }}"
                                                data-akhir="{{ $priceData->harga_jual_range_akhir }}"
                                            >
                                                Ubah Harga
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div class="text-center py-10 bg-white rounded-xl shadow-lg">
                    <p class="text-xl text-gray-500">Tidak ada barang yang memiliki data harga jual atau stok di warung manapun saat ini.</p>
                </div>
            @endforelse
            {{-- End Loop per Barang --}}
        </div>
    </main>
</div>

{{-- MODAL EDIT HARGA JUAL --}}
<div id="editPriceModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4 p-6">
        <div class="flex justify-between items-center border-b pb-3 mb-4">
            <h3 class="text-xl font-bold text-gray-800">Ubah Harga Jual</h3>
            <button type="button" class="text-gray-400 hover:text-gray-600 modal-close-btn">&times;</button>
        </div>

        <form id="updatePriceForm" action="{{ route('admin.harga_jual.update') }}" method="POST">
            @csrf
            @method('PUT')

            <input type="hidden" name="id_warung" id="modal_id_warung">
            <input type="hidden" name="id_barang" id="modal_id_barang">

            <div class="mb-4">
                <p class="text-sm text-gray-600">
                    <span class="font-semibold">Barang:</span> <span id="modal_barang_name" class="text-indigo-600"></span><br>
                    <span class="font-semibold">Warung:</span> <span id="modal_warung_name"></span><br>
                    <span class="font-semibold">Harga Modal:</span> <span id="modal_harga_modal"></span>
                </p>
            </div>

            {{-- Harga Jual Range Awal --}}
            <div class="mb-4">
                <label for="harga_jual_range_awal" class="block text-sm font-medium text-gray-700">Harga Jual Range Awal (Rp)</label>
                <input type="number" name="harga_jual_range_awal" id="harga_jual_range_awal"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-3 focus:ring-indigo-500 focus:border-indigo-500"
                        min="0" required>
            </div>

            {{-- Harga Jual Range Akhir --}}
            <div class="mb-6">
                <label for="harga_jual_range_akhir" class="block text-sm font-medium text-gray-700">Harga Jual Range Akhir (Rp)</label>
                <input type="number" name="harga_jual_range_akhir" id="harga_jual_range_akhir"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-3 focus:ring-indigo-500 focus:border-indigo-500"
                        min="0" required>
            </div>

            {{-- Tombol Aksi --}}
            <div class="flex justify-end space-x-3">
                <button type="button" class="modal-close-btn inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none">
                    Batal
                </button>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // FUNGSI JAVASCRIPT UNTUK MODAL
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('editPriceModal');
        const form = document.getElementById('updatePriceForm');

        // Fungsi untuk membuka modal dan mengisi data
        document.querySelectorAll('.edit-price-btn').forEach(button => {
            button.addEventListener('click', function () {
                const warungId = this.dataset.warungId;
                const warungName = this.dataset.warungName;
                const barangId = this.dataset.barangId;
                const barangName = this.dataset.barangName;
                const modalPrice = this.dataset.modal;
                const hargaAwal = this.dataset.awal;
                const hargaAkhir = this.dataset.akhir;

                // Isi input hidden dan label
                document.getElementById('modal_id_warung').value = warungId;
                document.getElementById('modal_id_barang').value = barangId;
                document.getElementById('modal_barang_name').textContent = barangName;
                document.getElementById('modal_warung_name').textContent = warungName;
                document.getElementById('modal_harga_modal').textContent = `Rp ${new Intl.NumberFormat('id-ID').format(modalPrice)}`;
                document.getElementById('harga_jual_range_awal').value = hargaAwal;
                document.getElementById('harga_jual_range_akhir').value = hargaAkhir;

                // Tampilkan modal
                modal.style.display = 'flex';
            });
        });

        // Fungsi untuk menutup modal
        document.querySelectorAll('.modal-close-btn').forEach(button => {
            button.addEventListener('click', function () {
                modal.style.display = 'none';
            });
        });

        // Tutup modal jika klik di luar area modal
        window.addEventListener('click', function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        });
    });
</script>

@endsection

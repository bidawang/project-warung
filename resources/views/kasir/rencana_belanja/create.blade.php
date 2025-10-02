@extends('layouts.app')

@section('title', 'Buat Rencana Belanja Baru')

@section('content')

<div class="">
    <div class="card shadow-lg">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">Form Rencana Pembelian Barang</h5>
        </div>
        <div class="card-body">

            {{-- Pesan Feedback --}}
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    <p class="mb-0">Mohon perbaiki kesalahan input berikut:</p>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="rencanaBelanjaForm" action="{{ route('kasir.rencanabelanja.store') }}" method="POST">
                @csrf

                <div class="row">
                    {{-- ======================================================= --}}
                    {{-- KOLOM KIRI: DAFTAR BARANG & INPUT (7/12) --}}
                    {{-- ======================================================= --}}
                    <div class="col-md-7">
                        <h6 class="mb-3 text-success">Daftar Stok & Kebutuhan (Warung Anda)</h6>

                        <div class="mb-3">
                            <input type="text" id="searchInput" class="form-control" placeholder="Cari Nama Barang...">
                        </div>

                        <div class="table-responsive" style="max-height: 70vh; overflow-y: auto;">
                            <table class="table table-striped table-hover align-middle mb-0" id="barangTable">
                                <thead class="bg-light sticky-top" style="top: -1px; z-index: 10;">
                                    <tr>
                                        <th style="width: 5%;">#</th>
                                        <th>Nama Barang</th>
                                        <th style="width: 15%;">Stok Saat Ini</th>
                                        <th style="width: 25%;">Jumlah Rencana Beli</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($stokBarang as $barang)
                                        @php
                                            $stokWarung = $barang->stokWarung->first();
                                            $jumlahStok = $stokWarung ? $stokWarung->jumlah : 0;
                                            $stokKey = $barang->id;
                                        @endphp
                                        <tr data-nama="{{ Str::lower($barang->nama_barang ?? '') }}">
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <strong>{{ $barang->nama_barang ?? 'Barang Tidak Dikenal' }}</strong>
                                                <input type="hidden"
                                                    name="rencana[{{ $stokKey }}][id_barang]"
                                                    value="{{ $barang->id }}">
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $jumlahStok < 5 ? 'danger' : ($jumlahStok < 20 ? 'warning text-dark' : 'success') }}" data-stok-saat-ini="{{ $jumlahStok }}">
                                                    {{ number_format($jumlahStok, 0, ',', '.') }}
                                                </span>
                                            </td>
                                            <td>
                                                {{-- Input Kuantitas di Kiri --}}
                                                <input type="number"
                                                    name="rencana[{{ $stokKey }}][jumlah_awal]"
                                                    class="form-control form-control-sm rencana-input"
                                                    value="{{ old('rencana.' . $stokKey . '.jumlah_awal', 0) }}"
                                                    min="0"
                                                    placeholder="0"
                                                    data-id="{{ $barang->id }}"
                                                    data-nama="{{ $barang->nama_barang ?? 'Barang Tidak Dikenal' }}">
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">Tidak ada barang yang terdaftar.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- ======================================================= --}}
                    {{-- KOLOM KANAN: RINGKASAN RENCANA (5/12) --}}
                    {{-- ======================================================= --}}
                    <div class="col-md-5">
                        <div class="sticky-top" style="top: 20px;">
                            <div class="card bg-info text-white shadow">
                                <div class="card-header">
                                    <h5 class="mb-0">Ringkasan Rencana Belanja</h5>
                                </div>
                                <div class="card-body bg-light text-dark">
                                    <p class="text-muted">Daftar barang yang akan dipesan (Jumlah > 0).</p>

                                    <div class="table-responsive" style="max-height: 40vh; overflow-y: auto;">
                                        <table class="table table-bordered table-sm mb-0">
                                            <thead>
                                                <tr class="table-primary">
                                                    <th style="width: 50%;">Barang</th>
                                                    <th style="width: 25%;" class="text-center">Stok Saat Ini</th>
                                                    <th style="width: 25%;" class="text-center">Jml Rencana</th>
                                                </tr>
                                            </thead>
                                            <tbody id="rencanaListBody">
                                                <tr><td colspan="3" class="text-center text-muted">Belum ada barang dipilih.</td></tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div id="ringkasanError" class="alert alert-danger d-none mt-3"></div>

                                    <div class="d-grid mt-3">
                                        <button type="button" class="btn btn-success" id="btnKonfirmasi">
                                            <i class="fas fa-check-circle"></i> **Konfirmasi & Simpan Rencana**
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> {{-- End Row --}}
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('rencanaBelanjaForm');
    const btnKonfirmasi = document.getElementById('btnKonfirmasi');
    const rencanaInputLeft = document.querySelectorAll('.rencana-input'); // Semua input di kolom Kiri
    const rencanaListBody = document.getElementById('rencanaListBody');
    const ringkasanError = document.getElementById('ringkasanError');

    // Variabel untuk Search
    const searchInput = document.getElementById('searchInput');
    const barangTableBody = document.querySelector('#barangTable tbody');
    const allTableRows = barangTableBody.querySelectorAll('tr');

    // =======================================================
    // FUNGSI BARU: PENCARIAN BARANG
    // =======================================================
    const filterTable = () => {
        const searchTerm = searchInput.value.toLowerCase().trim();

        allTableRows.forEach(row => {
            const rowData = row.dataset.nama || '';

            if (rowData.includes(searchTerm)) {
                row.style.display = ''; // Tampilkan baris
            } else {
                row.style.display = 'none'; // Sembunyikan baris
            }
        });
    };

    // =======================================================
    // FUNGSI UTAMA: MEMPERBARUI DAFTAR RINGKASAN KANAN
    // =======================================================
    const updateRencanaList = () => {
        // Ambil data dari input Kiri, termasuk ID dan Stok
        const selectedItems = Array.from(rencanaInputLeft)
            .map(input => {
                const row = input.closest('tr');
                const stokSpan = row.querySelector('[data-stok-saat-ini]');

                return {
                    id: input.dataset.id,
                    nama: input.dataset.nama,
                    jumlah: parseInt(input.value) || 0,
                    stok: parseInt(stokSpan.dataset.stokSaatIni) || 0
                };
            })
            .filter(item => item.jumlah > 0);

        // Bersihkan dan isi ulang daftar
        rencanaListBody.innerHTML = '';
        ringkasanError.classList.add('d-none');

        if (selectedItems.length === 0) {
            rencanaListBody.innerHTML = '<tr><td colspan="3" class="text-center text-muted">Belum ada barang dipilih.</td></tr>';
            return false;
        }

        selectedItems.forEach(item => {
            const row = rencanaListBody.insertRow();
            row.innerHTML = `
                <td>${item.nama}</td>
                <td class="text-center">${item.stok.toLocaleString('id-ID')}</td>
                <td class="text-center">
                    <input type="number"
                        class="form-control form-control-sm rencana-input-right"
                        value="${item.jumlah}"
                        min="0"
                        data-id="${item.id}"
                        data-nama="${item.nama}">
                </td>
            `;
        });

        // ⭐ PENTING: Attach listener ke input yang baru dibuat di Kolom Kanan
        document.querySelectorAll('.rencana-input-right').forEach(inputRight => {
            inputRight.addEventListener('change', syncQuantities);
            inputRight.addEventListener('blur', syncQuantities);
        });

        return true;
    };

    // =======================================================
    // FUNGSI BARU: SYNC QUANTITIES (Kiri <--> Kanan)
    // =======================================================
    const syncQuantities = (event) => {
        const inputChanged = event.target;
        const barangId = inputChanged.dataset.id;
        const newQuantity = parseInt(inputChanged.value) || 0;

        // Cari input yang sesuai di Kolom Kiri
        const inputLeft = document.querySelector(`.rencana-input[data-id="${barangId}"]`);

        if (inputLeft) {
            // Update nilai input Kiri
            inputLeft.value = newQuantity;
        }

        // Selalu panggil updateRencanaList untuk merefresh daftar (menghilangkan item jika diubah ke 0)
        updateRencanaList();
    };

    // =======================================================
    // EVENT LISTENERS
    // =======================================================

    // Event Listener untuk Search
    searchInput.addEventListener('keyup', filterTable);
    searchInput.addEventListener('change', filterTable);

    // Event Listener untuk Input Rencana Belanja (Kolom Kiri)
    rencanaInputLeft.forEach(input => {
        input.addEventListener('change', updateRencanaList);
        input.addEventListener('blur', updateRencanaList);
    });

    // Panggil sekali saat load
    updateRencanaList();


    // =======================================================
    // EVENT LISTENER UNTUK TOMBOL SUBMIT FINAL (Konfirmasi)
    // =======================================================
    btnKonfirmasi.addEventListener('click', () => {
        // Panggil updateRencanaList terakhir kali untuk menyinkronkan data final
        if (!updateRencanaList()) {
            ringkasanError.textContent = 'Anda belum memilih barang. Masukkan jumlah minimal 1 untuk melanjutkan.';
            ringkasanError.classList.remove('d-none');
            return;
        }

        // Logic pembersihan payload: Hapus semua input yang nilainya <= 0 dari pengiriman
        document.querySelectorAll('.rencana-input').forEach(input => {
            const jumlah = parseInt(input.value) || 0;
            const parentRow = input.closest('tr');
            const idBarangInput = parentRow.querySelector('input[type="hidden"][name^="rencana"][name$="[id_barang]"]');

            if (jumlah <= 0) {
                input.removeAttribute('name');
                if (idBarangInput) {
                    idBarangInput.removeAttribute('name');
                }
            } else {
                // Pastikan name dikembalikan jika sebelumnya dihapus (Penting untuk keamanan)
                input.setAttribute('name', `rencana[${input.dataset.id}][jumlah_awal]`);
                if (idBarangInput) {
                    idBarangInput.setAttribute('name', `rencana[${input.dataset.id}][id_barang]`);
                }
            }
        });

        // Submit form setelah membersihkan input
        form.submit();
    });
});
</script>

@endsection

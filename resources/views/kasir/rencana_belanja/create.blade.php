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
    const rencanaListBody = document.getElementById('rencanaListBody');
    const ringkasanError = document.getElementById('ringkasanError');

    const searchInput = document.getElementById('searchInput');
    const allLeftInputs = document.querySelectorAll('.rencana-input');

    /* =========================
       SEARCH
    ========================== */
    searchInput.addEventListener('input', () => {
        const q = searchInput.value.toLowerCase();
        document.querySelectorAll('#barangTable tbody tr').forEach(tr => {
            tr.style.display = tr.dataset.nama.includes(q) ? '' : 'none';
        });
    });

    /* =========================
       UPDATE / CREATE ROW RINGKASAN
    ========================== */
    const upsertRingkasanRow = (data) => {
        let row = rencanaListBody.querySelector(`tr[data-id="${data.id}"]`);

        if (data.jumlah <= 0) {
            if (row) row.remove();
            return;
        }

        if (!row) {
            row = document.createElement('tr');
            row.dataset.id = data.id;
            row.innerHTML = `
                <td>${data.nama}</td>
                <td class="text-center">${data.stok.toLocaleString('id-ID')}</td>
                <td class="text-center">
                    <input type="number"
                        class="form-control form-control-sm input-kanan"
                        min="0"
                        value="${data.jumlah}"
                        data-id="${data.id}">
                </td>
            `;
            rencanaListBody.appendChild(row);
        } else {
            row.querySelector('.input-kanan').value = data.jumlah;
        }
    };

    /* =========================
       INPUT KIRI → RINGKASAN
    ========================== */
    allLeftInputs.forEach(input => {
        input.addEventListener('input', () => {
            const row = input.closest('tr');
            const stok = parseInt(row.querySelector('[data-stok-saat-ini]').dataset.stokSaatIni) || 0;

            upsertRingkasanRow({
                id: input.dataset.id,
                nama: input.dataset.nama,
                stok,
                jumlah: parseInt(input.value) || 0
            });

            toggleEmptyState();
        });
    });

    /* =========================
       EVENT DELEGATION
       RINGKASAN → INPUT KIRI
    ========================== */
    rencanaListBody.addEventListener('input', (e) => {
        if (!e.target.classList.contains('input-kanan')) return;

        const id = e.target.dataset.id;
        const val = parseInt(e.target.value) || 0;

        const leftInput = document.querySelector(`.rencana-input[data-id="${id}"]`);
        if (leftInput) leftInput.value = val;

        if (val <= 0) {
            e.target.closest('tr').remove();
        }

        toggleEmptyState();
    });

    /* =========================
       EMPTY STATE
    ========================== */
    const toggleEmptyState = () => {
        if (rencanaListBody.children.length === 0) {
            rencanaListBody.innerHTML =
                '<tr class="empty"><td colspan="3" class="text-center text-muted">Belum ada barang dipilih.</td></tr>';
        } else {
            const empty = rencanaListBody.querySelector('.empty');
            if (empty) empty.remove();
        }
    };

    /* =========================
       SUBMIT
    ========================== */
    btnKonfirmasi.addEventListener('click', () => {
        if (rencanaListBody.children.length === 0) {
            ringkasanError.textContent = 'Pilih minimal 1 barang.';
            ringkasanError.classList.remove('d-none');
            return;
        }

        allLeftInputs.forEach(input => {
            const qty = parseInt(input.value) || 0;
            const row = input.closest('tr');
            const hidden = row.querySelector('input[type="hidden"]');

            if (qty <= 0) {
                input.removeAttribute('name');
                hidden?.removeAttribute('name');
            }
        });

        form.submit();
    });
});
</script>

@endsection

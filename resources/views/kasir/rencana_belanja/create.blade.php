@extends('layouts.app')

@section('title', 'Buat Rencana Belanja Baru')

@section('content')
<div class="container-fluid">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-success text-white py-3">
            <h5 class="mb-0"><i class="fas fa-cart-plus me-2"></i>Form Rencana Pembelian Barang</h5>
        </div>
        <div class="card-body">

            {{-- Pesan Feedback --}}
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="rencanaBelanjaForm" action="{{ route('kasir.rencanabelanja.store') }}" method="POST">
                @csrf

                <div class="row g-4">
                    {{-- KOLOM KIRI: DAFTAR BARANG --}}
                    <div class="col-md-8">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0 text-success fw-bold">Daftar Stok & Kebutuhan</h6>
                            <div class="input-group input-group-sm" style="width: 250px;">
                                <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                                <input type="text" id="searchInput" class="form-control border-start-0" placeholder="Cari barang...">
                            </div>
                        </div>

                        <div class="table-responsive rounded shadow-sm" style="max-height: 70vh; overflow-y: auto; border: 1px solid #dee2e6;">
                            <table class="table table-hover align-middle mb-0" id="barangTable">
                                <thead class="bg-light sticky-top" style="z-index: 10;">
                                    <tr>
                                        <th style="width: 40px;" class="text-center">#</th>
                                        <th style="width: 80px;" class="text-center">Stok</th>
                                        <th>Nama Barang</th>
                                        <th style="width: 250px;">Rencana Beli</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($stokBarang as $barang)
                                        @php
                                            $stokWarung = $barang->stokWarung->first();
                                            $jumlahStok = $stokWarung ? $stokWarung->jumlah : 0;
                                            $stokKey = $barang->id;
                                            $hasSatuan = $barang->satuan && $barang->satuan->count() > 0;
                                        @endphp
                                        <tr data-nama="{{ Str::lower($barang->nama_barang ?? '') }}" class="barang-row">
                                            <td class="text-center text-muted small">{{ $loop->iteration }}</td>
                                            <td class="text-center">
                                                <span class="badge {{ $jumlahStok < 10 ? 'bg-danger' : 'bg-secondary' }}" data-stok-saat-ini="{{ $jumlahStok }}">
                                                    {{ number_format($jumlahStok, 0, ',', '.') }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="fw-bold text-dark text-truncate" style="max-width: 280px;" title="{{ $barang->nama_barang }}">{{ $barang->nama_barang }}</div>
                                                <small class="text-muted small">{{ $barang->subKategori->sub_kategori ?? 'Tanpa Kategori' }}</small>
                                                <input type="hidden" name="rencana[{{ $stokKey }}][id_barang]" value="{{ $barang->id }}">
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    {{-- Input Angka User --}}
                                                    <input type="number" 
                                                        class="form-control qty-input text-center" 
                                                        placeholder="0" min="0" value="0"
                                                        data-id="{{ $barang->id }}"
                                                        data-nama="{{ $barang->nama_barang }}">
                                                    
                                                    {{-- Pilihan Satuan --}}
                                                    <select class="form-select satuan-select w-50" {{ !$hasSatuan ? 'disabled' : '' }}>
                                                        <option value="1">Pcs (1)</option>
                                                        @if($hasSatuan)
                                                            @foreach($barang->satuan as $s)
                                                                <option value="{{ $s->jumlah }}">{{ $s->nama_satuan }}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                                {{-- Hidden Input untuk Total Real (Qty * Satuan) --}}
                                                <input type="hidden" 
                                                    name="rencana[{{ $stokKey }}][jumlah_awal]" 
                                                    class="final-qty-hidden" 
                                                    value="0">
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-5 text-muted italic">Data barang tidak ditemukan.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- KOLOM KANAN: RINGKASAN --}}
                    <div class="col-md-4">
                        <div class="sticky-top" style="top: 20px;">
                            <div class="card border-success shadow-sm overflow-hidden">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0 fw-bold"><i class="fas fa-list-ul me-2"></i>Ringkasan Rencana</h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive" style="max-height: 45vh; overflow-y: auto;">
                                        <table class="table table-sm mb-0">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th class="ps-3">Barang</th>
                                                    <th class="text-center">Total (Pcs)</th>
                                                    <th style="width: 40px;"></th>
                                                </tr>
                                            </thead>
                                            <tbody id="rencanaListBody">
                                                <tr class="empty-row">
                                                    <td colspan="3" class="text-center py-5 text-muted">Belum ada barang dipilih.</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <div class="p-3 border-top bg-light">
                                        <div id="ringkasanError" class="alert alert-danger d-none py-2 small mb-3"></div>
                                        <div class="d-grid">
                                            <button type="button" class="btn btn-success btn-lg fw-bold shadow-sm" id="btnKonfirmasi">
                                                Konfirmasi & Simpan
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('rencanaBelanjaForm');
    const btnKonfirmasi = document.getElementById('btnKonfirmasi');
    const rencanaListBody = document.getElementById('rencanaListBody');
    const searchInput = document.getElementById('searchInput');
    const ringkasanError = document.getElementById('ringkasanError');

    // --- LOGIKA PENCARIAN ---
    searchInput.addEventListener('input', () => {
        const q = searchInput.value.toLowerCase();
        document.querySelectorAll('.barang-row').forEach(tr => {
            tr.style.display = tr.dataset.nama.includes(q) ? '' : 'none';
        });
    });

    // --- LOGIKA HITUNG TOTAL ---
    const updateRowCalculation = (row) => {
        const qtyInput = row.querySelector('.qty-input');
        const satuanSelect = row.querySelector('.satuan-select');
        const finalHidden = row.querySelector('.final-qty-hidden');
        
        const qtyValue = parseInt(qtyInput.value) || 0;
        // multiplier tetap 1 jika select disabled
        const multiplier = parseInt(satuanSelect.value) || 1;
        const totalFinal = qtyValue * multiplier;

        finalHidden.value = totalFinal;
        upsertRingkasan(qtyInput.dataset.id, qtyInput.dataset.nama, totalFinal);
    };

    // --- UPDATE DAFTAR RINGKASAN DI KANAN ---
    const upsertRingkasan = (id, nama, total) => {
        let existingRow = rencanaListBody.querySelector(`tr[data-ringkas-id="${id}"]`);

        if (total <= 0) {
            if (existingRow) existingRow.remove();
            checkEmpty();
            return;
        }

        if (existingRow) {
            existingRow.querySelector('.total-display').textContent = total.toLocaleString('id-ID');
        } else {
            const row = document.createElement('tr');
            row.dataset.ringkasId = id;
            row.innerHTML = `
                <td class="ps-3 py-2">
                    <div class="fw-bold small text-truncate" style="max-width: 150px;">${nama}</div>
                </td>
                <td class="text-center py-2">
                    <span class="badge bg-primary total-display">${total.toLocaleString('id-ID')}</span>
                </td>
                <td class="text-center py-2">
                    <button type="button" class="btn btn-sm text-danger remove-item" data-id="${id}">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            `;
            rencanaListBody.appendChild(row);
        }
        checkEmpty();
    };

    const checkEmpty = () => {
        const rows = rencanaListBody.querySelectorAll('tr:not(.empty-row)');
        const emptyMsg = rencanaListBody.querySelector('.empty-row');
        if (rows.length === 0) {
            if (!emptyMsg) {
                rencanaListBody.innerHTML = '<tr class="empty-row"><td colspan="3" class="text-center py-5 text-muted small">Belum ada barang dipilih.</td></tr>';
            }
        } else if (emptyMsg) {
            emptyMsg.remove();
        }
    };

    // --- EVENT LISTENERS ---
    document.querySelectorAll('.qty-input, .satuan-select').forEach(el => {
        el.addEventListener('input', (e) => {
            const row = e.target.closest('tr');
            updateRowCalculation(row);
        });
    });

    rencanaListBody.addEventListener('click', (e) => {
        const btn = e.target.closest('.remove-item');
        if (btn) {
            const id = btn.dataset.id;
            const leftRow = document.querySelector(`.qty-input[data-id="${id}"]`).closest('tr');
            leftRow.querySelector('.qty-input').value = 0;
            updateRowCalculation(leftRow);
        }
    });

    // --- SUBMIT VALIDATION ---
    btnKonfirmasi.addEventListener('click', () => {
        const finalRows = rencanaListBody.querySelectorAll('tr:not(.empty-row)');
        
        if (finalRows.length === 0) {
            ringkasanError.textContent = 'Pilih minimal 1 barang.';
            ringkasanError.classList.remove('d-none');
            return;
        }

        document.querySelectorAll('.barang-row').forEach(row => {
            const finalQty = parseInt(row.querySelector('.final-qty-hidden').value) || 0;
            if (finalQty <= 0) {
                row.querySelectorAll('input, select').forEach(input => input.removeAttribute('name'));
            } else {
                // Pastikan select yang disabled tidak dikirim datanya, 
                // tapi hidden input tetap ada
                const sel = row.querySelector('.satuan-select');
                if (sel.disabled) sel.removeAttribute('name');
            }
        });

        form.submit();
    });
});
</script>

<style>
    .table-responsive::-webkit-scrollbar { width: 4px; }
    .table-responsive::-webkit-scrollbar-thumb { background: #cbd5e0; border-radius: 10px; }
    .barang-row:hover { background-color: #f8fafc; }
    .qty-input:focus { border-color: #28a745; box-shadow: none; }
    .qty-input { width: 70px !important; flex: none !important; }
    .sticky-top { top: -1px; }
    .text-truncate { display: block; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; }
</style>
@endsection
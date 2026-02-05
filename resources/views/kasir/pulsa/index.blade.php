@extends('layouts.app')

@section('title', 'Manajemen Pulsa Warung')

@section('content')
<div class="container-fluid py-4" style="background-color: #f4f7f6; min-height: 100vh;">

    @if (session('success'))
        <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill fs-4 me-2"></i>
                <div>{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        {{-- KOLOM KIRI: DAFTAR HARGA JUAL PULSA --}}
        <div class="col-lg-7 col-xl-8">
            <div class="card border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
                <div class="card-header bg-white py-3 border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-dark">
                            <i class="fas fa-list-ul me-2 text-primary"></i>Daftar Harga Jual
                        </h5>
                        <span class="badge bg-soft-primary text-primary rounded-pill">{{ count($harga_pulsas) }} Produk</span>
                    </div>
                </div>

                <div class="card-body p-0">
                    {{-- Search Bar Modern --}}
                    <div class="px-4 py-3 bg-light border-bottom border-top">
                        <div class="input-group input-group-merge shadow-sm rounded-pill overflow-hidden bg-white border">
                            <span class="input-group-text bg-white border-0">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" id="searchInputHarga" class="form-control border-0 ps-0 form-control-lg fs-6"
                                placeholder="Cari nominal atau operator (contoh: 10000 Telkomsel)">
                        </div>
                    </div>

                    {{-- Tabel Daftar Harga --}}
                    <div class="table-responsive">
                        <table class="table align-middle mb-0 table-hover" id="hargaTable">
                            <thead class="bg-white">
                                <tr>
                                    <th class="ps-4 text-uppercase fs-7 text-muted">Nominal Produk</th>
                                    <th class="text-uppercase fs-7 text-muted">Harga Jual</th>
                                    <th class="text-uppercase fs-7 text-muted text-center">Operator</th>
                                    <th class="text-uppercase fs-7 text-muted text-center pe-4">Kategori</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($harga_pulsas as $harga)
                                    <tr class="harga-item" data-nominal="{{ $harga->jumlah_pulsa }}"
                                        data-operator="{{ strtolower($harga->operator ?? '-') }}">
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm me-3 bg-soft-success text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px; background-color: #e7f7ed;">
                                                    <i class="fas fa-phone"></i>
                                                </div>
                                                <span class="fw-bold text-dark">Pulsa {{ number_format($harga->jumlah_pulsa, 0, ',', '.') }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-bold fs-6 text-primary">Rp {{ number_format($harga->harga, 0, ',', '.') }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge rounded-pill border fw-bold px-3 py-2 text-dark" style="background-color: #f8f9fa;">
                                                {{ strtoupper($harga->operator ?? 'UMUM') }}
                                            </span>
                                        </td>
                                        <td class="text-center pe-4">
                                            <span class="badge bg-soft-info text-info rounded-pill px-3 py-2" style="background-color: #e0f7fa;">
                                                {{ ucfirst(str_replace('_', ' ', $harga->nama_jenis)) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5">
                                            <img src="https://illustrations.popsy.co/gray/data-report.svg" style="width: 150px;" class="mb-3 opacity-50">
                                            <p class="text-muted fw-bold">Belum ada daftar harga pulsa.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: SALDO & AKSI --}}
        <div class="col-lg-5 col-xl-4">

            {{-- CARD SALDO PULSA --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px; background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                <div class="card-body p-4 text-white">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-wallet fs-4 me-2"></i>
                        <h6 class="mb-0 fw-bold">Saldo Pulsa Tersedia</h6>
                    </div>

                    @forelse ($saldoPulsas as $pulsa)
                        <div class="bg-white bg-opacity-25 rounded-3 p-3 mb-2 d-flex justify-content-between align-items-center border border-white border-opacity-25">
                            <div>
                                <small class="text-uppercase fw-bold d-block text-white-50" style="letter-spacing: 1px; font-size: 10px;">{{ $pulsa->jenisPulsa->nama_jenis }}</small>
                                <span class="fw-bold fs-5">Rp {{ number_format($pulsa->saldo, 0, ',', '.') }}</span>
                            </div>
                            <div class="rounded-circle bg-white text-success d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                <i class="fas fa-check small"></i>
                            </div>
                        </div>
                    @empty
                        <p class="text-white-50 small mb-0">Belum ada saldo yang tercatat.</p>
                    @endforelse
                </div>
            </div>

            {{-- TOMBOL AKSI --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                <div class="card-body p-3">
                    <a href="{{ route('kasir.pulsa.jual.create') }}"
                       class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-sm mb-0">
                        <i class="fas fa-mobile-screen me-2"></i>JUAL PULSA SEKARANG
                    </a>
                </div>
            </div>

            {{-- RIWAYAT TRANSAKSI TERAKHIR --}}
            <div class="card border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
                <div class="card-header bg-white py-3 border-0 border-bottom">
                    <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-history me-2 text-muted"></i>Riwayat Terakhir</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 400px;">
                        <table class="table align-middle mb-0 table-sm">
                            <tbody class="small">
                                @forelse ($transaksi_pulsa as $pulsa)
                                    <tr class="border-bottom">
                                        <td class="ps-3 py-3">
                                            <div class="fw-bold text-dark">Pulsa {{ number_format($pulsa->jumlah, 0, ',', '.') }}</div>
                                            <div class="text-muted" style="font-size: 11px;">{{ $pulsa->created_at->diffForHumans() }}</div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-soft-danger text-danger rounded-pill" style="font-size: 10px; background-color: #fce8e8;">
                                                {{ strtoupper($pulsa->tipe) }}
                                            </span>
                                        </td>
                                        <td class="text-end pe-3">
                                            <span class="fw-bold text-danger">Rp {{ number_format($pulsa->total, 0, ',', '.') }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted small">Belum ada transaksi.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-light border-0 py-2 text-center">
                    <a href="#" class="text-decoration-none small fw-bold">Lihat Semua Riwayat</a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .fs-7 { font-size: 0.7rem; letter-spacing: 0.05em; font-weight: 800; }
    .bg-soft-primary { background-color: #e7f1ff; }
    .bg-soft-info { background-color: #e0f7fa; }
    .bg-soft-success { background-color: #e7f7ed; }
    .bg-soft-danger { background-color: #fce8e8; }
    .table thead th { border-top: none; }
    .harga-item { transition: all 0.2s; }
    .harga-item:hover { background-color: #f8f9fa; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInputHarga');
        const rows = document.querySelectorAll('.harga-item');

        searchInput.addEventListener('input', function() {
            const term = this.value.toLowerCase().trim();
            rows.forEach(row => {
                const text = (row.dataset.nominal + ' ' + row.dataset.operator).toLowerCase();
                row.style.display = text.includes(term) ? '' : 'none';
            });
        });
    });
</script>
@endsection

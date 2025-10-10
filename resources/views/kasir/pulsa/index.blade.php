@extends('layouts.app')

@section('title', 'Manajemen Pulsa Warung')

@section('content')
    <div class="container-fluid mt-4">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row g-4">

            {{-- KOLOM KIRI: DAFTAR HARGA JUAL PULSA (MASTER) --}}
            <div class="col-lg-8 col-xl-7">
                <div class="card shadow-lg border-0 h-100">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Daftar Harga Jual Pulsa</h5>
                        <a href="{{ url('kasir.pulsa.harga-pulsa.create') }}" class="btn btn-sm btn-light text-primary fw-bold">
                            <i class="fas fa-plus me-1"></i> Atur Harga Baru
                        </a>
                    </div>
                    <div class="card-body p-3">
                        {{-- Search & Filter --}}
                        <div class="input-group mb-3 sticky-top p-0 bg-white" style="top: -16px; z-index: 10;">
                            <span class="input-group-text bg-light"><i class="fas fa-search"></i></span>
                            <input type="text" id="searchInputHarga" class="form-control form-control-lg"
                                placeholder="Cari harga berdasarkan nominal atau operator...">
                        </div>

                        {{-- Tabel Daftar Harga --}}
                        <div class="table-responsive">
                            <table class="table table-striped table-hover small" id="hargaTable">
                                <thead>
                                    <tr>
                                        <th>Nominal / Produk</th>
                                        <th>Harga Jual</th>
                                        <th>Operator</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($harga_pulsas as $harga)
                                    <tr class="harga-item" data-nominal="{{ $harga->jumlah_pulsa }}" data-operator="{{ strtolower($harga->operator ?? '-') }}">
                                        <td class="fw-bold text-success">
                                            Pulsa {{ number_format($harga->jumlah_pulsa, 0, ',', '.') }}
                                        </td>
                                        <td class="fw-bold">
                                            Rp. {{ number_format($harga->harga, 0, ',', '.') }}
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ strtoupper($harga->operator ?? 'UMUM') }}</span>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ url('kasir.pulsa.harga-pulsa.edit', $harga->id) }}" class="btn btn-sm btn-warning p-1" title="Edit Harga">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                            {{-- Tombol Hapus (Opsional, pastikan ada url destroy) --}}
                                            {{-- <form action="{{ url('kasir.pulsa.harga-pulsa.destroy', $harga->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger p-1" onclick="return confirm('Hapus harga ini?')" title="Hapus Harga">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form> --}}
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">Belum ada daftar harga pulsa yang diatur.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: SALDO, AKSI, DAN RIWAYAT TRANSAKSI --}}
            <div class="col-lg-4 col-xl-5">

                {{-- CARD SALDO KAS PULSA --}}
                <div class="card shadow-lg border-0 mb-4">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0 fw-bold"><i class="fas fa-wallet me-2"></i>Saldo Kas Pulsa</h6>
                    </div>
                    <div class="card-body p-3 text-center">
                        <div class="h3 mb-0 fw-bold text-success">
                            Rp. {{ number_format($pulsa->saldo ?? 0, 0, ',', '.') }}
                        </div>
                        <small class="text-muted">Modal warung untuk transaksi pulsa.</small>
                    </div>
                </div>

                {{-- CARD TOMBOL AKSI UTAMA (DIPISAHKAN) --}}
                <div class="card shadow-lg border-0 mb-4">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0 fw-bold"><i class="fas fa-hand-holding-usd me-2"></i>Aksi Cepat</h6>
                    </div>
                    <div class="card-body d-flex gap-2 p-3">
                        {{-- 1. Tombol JUAL PULSA (Primary Action) --}}
                        <a href="{{ url('kasir.pulsa.create') }}" class="btn btn-primary flex-fill d-flex align-items-center justify-content-center fw-bold p-2">
                            <i class="fas fa-mobile-alt me-1"></i> Jual Pulsa
                        </a>

                        {{-- 2. Tombol TAMBAH SALDO PULSA (Modal Action) --}}
                        <a href="{{ url('kasir.saldo.create') }}" class="btn btn-success flex-fill d-flex align-items-center justify-content-center fw-bold p-2">
                            <i class="fas fa-money-check-alt me-1"></i> Tambah Saldo
                        </a>
                    </div>
                </div>

                {{-- CARD RIWAYAT TRANSAKSI PULSA TERAKHIR --}}
                <div class="card shadow-lg border-0 h-100">
                    <div class="card-header bg-dark text-white">
                        <h6 class="mb-0 fw-bold"><i class="fas fa-history me-2"></i>Riwayat Transaksi Terakhir</h6>
                    </div>
                    {{-- <div class="card-body p-3">
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-sm table-striped small mb-0">
                                <thead>
                                    <tr>
                                        <th>Nominal</th>
                                        <th>Bayar</th>
                                        <th>Status</th>
                                        <th>Waktu</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($pulsas as $pulsa)
                                    <tr>
                                        <td class="fw-bold">
                                            Pulsa {{ number_format($pulsa->nominal, 0, ',', '.') }}
                                        </td>
                                        <td class="text-danger">
                                            Rp. {{ number_format($pulsa->harga_jual, 0, ',', '.') }}
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $pulsa->status == 'Sukses' ? 'success' : 'danger' }}">
                                                {{ $pulsa->status }}
                                            </span>
                                        </td>
                                        <td>{{ $pulsa->created_at->diffForHumans() }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-3 text-muted">Belum ada transaksi pulsa.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div> --}}
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInputHarga');
            const hargaTable = document.getElementById('hargaTable');
            const rows = hargaTable.querySelectorAll('.harga-item');

            // Pencarian Produk (Filter)
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();

                rows.forEach(row => {
                    const nominal = row.dataset.nominal;
                    const operator = row.dataset.operator;
                    const textContent = (nominal + ' ' + operator).toLowerCase();

                    if (textContent.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        });
    </script>
@endsection

@extends('layouts.app')

@section('title', 'Kas Warung')

@section('content')
<div class="container mt-4">
    <h3 class="mb-4">Ringkasan Kas Warung</h3>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-success text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Total Pendapatan</h5>
                            <h2 class="card-text">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h2>
                        </div>
                        <i class="fas fa-dollar-sign fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-danger text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Total Pengeluaran</h5>
                            <h2 class="card-text">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</h2>
                        </div>
                        <i class="fas fa-shopping-cart fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-primary text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Saldo Bersih</h5>
                            <h2 class="card-text">Rp {{ number_format($saldoBersih, 0, ',', '.') }}</h2>
                        </div>
                        <i class="fas fa-wallet fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center bg-info text-white">
            <h5 class="mb-0">Riwayat Transaksi</h5>
            <a href="#" class="btn btn-light">
                <i class="fas fa-plus-circle me-2"></i> Tambah Transaksi
            </a>
        </div>
        <div class="card-body">
            <div class="input-group mb-4">
                <input type="text" class="form-control" placeholder="Cari transaksi...">
                <button class="btn btn-outline-secondary" type="button">Cari</button>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Tanggal</th>
                            <th scope="col">Tipe</th>
                            <th scope="col">Keterangan</th>
                            <th scope="col">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($riwayatTransaksi as $transaksi)
                            <tr>
                                <th scope="row">{{ $loop->iteration }}</th>
                                <td>{{ \Carbon\Carbon::parse($transaksi->created_at)->format('Y-m-d') }}</td>
                                <td>
                                    @if ($transaksi->jenis === 'penjualan')
                                        <span class="badge bg-success">Pemasukan</span>
                                    @else
                                        <span class="badge bg-danger">Pengeluaran</span>
                                    @endif
                                </td>
                                <td>{{ $transaksi->keterangan }}</td>
                                <td>
                                    @if ($transaksi->jenis === 'penjualan')
                                        + Rp {{ number_format($transaksi->total, 0, ',', '.') }}
                                    @else
                                        - Rp {{ number_format($transaksi->total, 0, ',', '.') }}
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">Tidak ada riwayat transaksi kas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Fitur Pagination (jika diperlukan) --}}
            {{-- <nav aria-label="Page navigation example">
                <ul class="pagination justify-content-center">
                    <li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item"><a class="page-link" href="#">Next</a></li>
                </ul>
            </nav> --}}
        </div>
    </div>
</div>
@endsection

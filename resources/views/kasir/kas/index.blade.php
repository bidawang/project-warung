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
                            <h2 class="card-text">Rp 1.500.000</h2>
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
                            <h2 class="card-text">Rp 750.000</h2>
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
                            <h2 class="card-text">Rp 750.000</h2>
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
                        <tr>
                            <th scope="row">1</th>
                            <td>2025-09-17</td>
                            <td><span class="badge bg-success">Pemasukan</span></td>
                            <td>Penjualan harian</td>
                            <td>+ Rp 150.000</td>
                        </tr>
                        <tr>
                            <th scope="row">2</th>
                            <td>2025-09-17</td>
                            <td><span class="badge bg-danger">Pengeluaran</span></td>
                            <td>Beli sabun cuci</td>
                            <td>- Rp 25.000</td>
                        </tr>
                        </tbody>
                </table>
            </div>

            <nav aria-label="Page navigation example">
                <ul class="pagination justify-content-center">
                    <li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item"><a class="page-link" href="#">Next</a></li>
                </ul>
            </nav>
        </div>
    </div>
</div>
@endsection

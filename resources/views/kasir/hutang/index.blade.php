@extends('layouts.app')

@section('title', 'Data Hutang')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-warning text-white">
            <h5 class="mb-0">Daftar Hutang Pelanggan</h5>
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="input-group" style="max-width: 300px;">
                    <input type="text" class="form-control" placeholder="Cari nama pelanggan...">
                    <button class="btn btn-outline-secondary" type="button">Cari</button>
                </div>
                <div class="btn-group" role="group" aria-label="Filter status hutang">
                    <a href="#" class="btn btn-outline-secondary">Semua</a>
                    <a href="#" class="btn btn-outline-secondary active">Belum Lunas</a>
                    <a href="#" class="btn btn-outline-secondary">Lunas</a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Nama Pelanggan</th>
                            <th scope="col">Jumlah Hutang</th>
                            <th scope="col">Tanggal Jatuh Tempo</th>
                            <th scope="col">Status</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th scope="row">1</th>
                            <td>Andi Sucipto</td>
                            <td>Rp 50.000</td>
                            <td>2025-10-01</td>
                            <td><span class="badge bg-danger">Belum Lunas</span></td>
                            <td>
                                <a href="#" class="btn btn-sm btn-info text-white">Detail</a>
                                <a href="#" class="btn btn-sm btn-success">Bayar</a>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">2</th>
                            <td>Citra Dewi</td>
                            <td>Rp 15.000</td>
                            <td>2025-09-25</td>
                            <td><span class="badge bg-danger">Belum Lunas</span></td>
                            <td>
                                <a href="#" class="btn btn-sm btn-info text-white">Detail</a>
                                <a href="#" class="btn btn-sm btn-success">Bayar</a>
                            </td>
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

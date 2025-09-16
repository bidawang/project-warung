@extends('layouts.app')

@section('title', 'Halaman Mutasi')

@section('content')
<div class="container-fluid mt-4">

    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center bg-success text-white">
                    <h5 class="mb-0">Notifikasi Mutasi Masuk</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">No. Mutasi</th>
                                    <th scope="col">Asal</th>
                                    <th scope="col">Tanggal</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th scope="row">1</th>
                                    <td>MT-20250917-002</td>
                                    <td>Warung Pusat</td>
                                    <td>2025-09-17</td>
                                    <td><span class="badge bg-info text-white">Menunggu Konfirmasi</span></td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-primary">Konfirmasi</a>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">2</th>
                                    <td>MT-20250916-004</td>
                                    <td>Warung Cabang 1</td>
                                    <td>2025-09-16</td>
                                    <td><span class="badge bg-success">Dikonfirmasi</span></td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-secondary disabled">Konfirmasi</a>
                                    </td>
                                </tr>
                                </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
                    <h5 class="mb-0">Mutasi Barang Keluar</h5>
                    <a href="#" class="btn btn-light">
                        <i class="fas fa-plus-circle me-2"></i> Buat Mutasi Baru
                    </a>
                </div>
                <div class="card-body">
                    <div class="input-group mb-3" style="max-width: 300px;">
                        <input type="text" class="form-control" placeholder="Cari nomor mutasi...">
                        <button class="btn btn-outline-secondary" type="button">Cari</button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">No. Mutasi</th>
                                    <th scope="col">Tujuan</th>
                                    <th scope="col">Tanggal</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th scope="row">1</th>
                                    <td>MT-20250917-001</td>
                                    <td>Warung Cabang 2</td>
                                    <td>2025-09-17</td>
                                    <td><span class="badge bg-success">Terkirim</span></td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-info text-white">Lihat Detail</a>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">2</th>
                                    <td>MT-20250916-003</td>
                                    <td>Warung Cabang 3</td>
                                    <td>2025-09-16</td>
                                    <td><span class="badge bg-warning text-dark">Dalam Perjalanan</span></td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-info text-white">Lihat Detail</a>
                                    </td>
                                </tr>
                                </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>


    </div>

</div>
@endsection

@extends('layouts.app')

@section('title', 'Data Stok Warung')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
            <h5 class="mb-0">Daftar Stok Barang</h5>
            {{-- Tombol Notifikasi Barang Masuk --}}
            <a href="{{ url('/kasir/stok-barang/barang-masuk/') }}" class="btn btn-primary position-relative" title="Lihat barang masuk dari owner">
                <i class="fas fa-bell fa-lg"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    3+
                    <span class="visually-hidden">notifikasi barang baru</span>
                </span>
            </a>
        </div>
        <div class="card-body">
            <div class="input-group mb-4">
                <input type="text" class="form-control" placeholder="Cari nama barang...">
                <button class="btn btn-outline-secondary" type="button">Cari</button>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Nama Barang</th>
                            <th scope="col">Stok</th>
                            <th scope="col">Harga Beli</th>
                            <th scope="col">Harga Jual</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th scope="row">1</th>
                            <td>Mie Instan Rasa Ayam Bawang</td>
                            <td>150</td>
                            <td>Rp 2.000</td>
                            <td>Rp 2.500</td>
                        </tr>
                        <tr>
                            <th scope="row">2</th>
                            <td>Minuman Dingin Teh Botol</td>
                            <td>85</td>
                            <td>Rp 3.500</td>
                            <td>Rp 4.000</td>
                        </tr>
                        <tr>
                            <th scope="row">3</th>
                            <td>Kopi Sachet</td>
                            <td>200</td>
                            <td>Rp 1.000</td>
                            <td>Rp 1.500</td>
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

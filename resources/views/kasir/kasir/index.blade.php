@extends('layouts.app')

@section('title', 'Halaman Kasir')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Daftar Produk</h5>
                </div>
                <div class="card-body">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" placeholder="Cari produk...">
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <div class="card product-card h-100">
                                <img src="https://via.placeholder.com/150" class="card-img-top" alt="Gambar Produk">
                                <div class="card-body text-center">
                                    <h6 class="card-title">Nama Produk</h6>
                                    <p class="card-text">Rp 15.000</p>
                                    <button class="btn btn-sm btn-success">Tambah</button>
                                </div>
                            </div>
                        </div>
                        {{--
                        @foreach($products as $product)
                            <div class="col-md-4 mb-4">
                                <div class="card product-card h-100">
                                    <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top" alt="{{ $product->name }}">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">{{ $product->name }}</h6>
                                        <p class="card-text">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                        <button class="btn btn-sm btn-success">Tambah</button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        --}}

                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Keranjang Belanja</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group mb-3">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="my-0">Nama Produk 1</h6>
                                <small class="text-muted">1 x Rp 15.000</small>
                            </div>
                            <span class="text-muted">Rp 15.000</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="my-0">Nama Produk 2</h6>
                                <small class="text-muted">2 x Rp 20.000</small>
                            </div>
                            <span class="text-muted">Rp 40.000</span>
                        </li>
                        </ul>

                    <div class="d-flex justify-content-between font-weight-bold">
                        <span>Total:</span>
                        <span>Rp 55.000</span>
                    </div>

                    <hr class="my-3">

                    <div class="form-group mb-3">
                        <label for="payment">Bayar:</label>
                        <input type="number" class="form-control" id="payment" placeholder="Jumlah uang pembayaran">
                    </div>
                    <div class="form-group mb-3">
                        <label for="change">Kembalian:</label>
                        <input type="text" class="form-control" id="change" value="Rp 0" readonly>
                    </div>

                    <div class="d-grid gap-2">
                        <button class="btn btn-success btn-lg">Selesaikan Transaksi</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

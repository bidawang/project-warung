@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">Dashboard</h1>
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-users"></i> Total Pengguna</h5>
                    <p class="card-text fs-2">1,250</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-box"></i> Total Produk</h5>
                    <p class="card-text fs-2">5,432</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-info mb-3">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-chart-line"></i> Penjualan Hari Ini</h5>
                    <p class="card-text fs-2">Rp 12,500,000</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@extends('layouts.app')

@section('title', 'Data Stok Warung')

@section('content')
<div class="container">
    <h1 class="mb-4">Data Stok Warung</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead class="">
                    <tr>
                        <th>No</th>
                        <th>Nama Barang</th>
                        <th>Kode Barang</th>
                        <th>Stok</th>
                        <th>Harga Beli</th>
                        <th>Harga Jual</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $stokBarang = [
                            ['id' => 1, 'nama_barang' => 'Beras 5kg', 'kode_barang' => 'BR001', 'stok' => 20, 'harga_beli' => 55000, 'harga_jual' => 60000],
                            ['id' => 2, 'nama_barang' => 'Minyak Goreng 1L', 'kode_barang' => 'MG002', 'stok' => 35, 'harga_beli' => 14000, 'harga_jual' => 16000],
                            ['id' => 3, 'nama_barang' => 'Gula Pasir 1kg', 'kode_barang' => 'GP003', 'stok' => 40, 'harga_beli' => 12000, 'harga_jual' => 14000],
                        ];
                    @endphp

                    @foreach($stokBarang as $index => $barang)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $barang['nama_barang'] }}</td>
                            <td>{{ $barang['kode_barang'] }}</td>
                            <td>{{ $barang['stok'] }}</td>
                            <td>Rp {{ number_format($barang['harga_beli'], 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($barang['harga_jual'], 0, ',', '.') }}</td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

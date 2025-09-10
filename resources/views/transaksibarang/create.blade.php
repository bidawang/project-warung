@extends('layouts.app')

@section('title', 'Tambah Transaksi Barang')

@section('content')
<div class="container">
    <h2 class="mb-4">Tambah Transaksi Barang</h2>

    <form action="{{ route('transaksibarang.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Transaksi Kas</label>
            <select name="id_transaksi_kas" class="form-control" required>
                @foreach($transaksis as $trx)
                    <option value="{{ $trx->id }}">#{{ $trx->id }} - {{ ucfirst($trx->jenis) }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label>Barang</label>
            <select name="id_barang" class="form-control" required>
                @foreach($barangs as $brg)
                    <option value="{{ $brg->id }}">{{ $brg->nama_barang }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label>Jumlah</label>
            <input type="number" name="jumlah" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Status</label>
            <input type="text" name="status" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Jenis</label>
            <select name="jenis" class="form-control" required>
                <option value="masuk">Masuk</option>
                <option value="keluar">Keluar</option>
            </select>
        </div>
        <div class="mb-3">
            <label>Keterangan</label>
            <textarea name="keterangan" class="form-control"></textarea>
        </div>
        <button class="btn btn-success">Simpan</button>
    </form>
</div>
@endsection

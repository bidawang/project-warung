@extends('layouts.app')

@section('title', 'Tambah Barang Masuk')

@section('content')
<div class="container">
    <h1 class="mb-4">Tambah Barang Masuk</h1>
    <form action="{{ route('barangmasuk.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Transaksi Barang</label>
            <select name="id_transaksi_barang" class="form-control" required>
                <option value="">-- Pilih Transaksi --</option>
                @foreach($transaksiBarang as $t)
                <option value="{{ $t->id }}">{{ $t->id }} - {{ $t->barang->nama_barang }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label>Stok Warung</label>
            <select name="id_stok_warung" class="form-control" required>
                <option value="">-- Pilih Stok Warung --</option>
                @foreach($stokWarung as $s)
                <option value="{{ $s->id }}">{{ $s->id }} - {{ $s->barang->nama ?? 'Barang' }}</option>
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
            <label>Keterangan</label>
            <textarea name="keterangan" class="form-control"></textarea>
        </div>
        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="{{ route('barangmasuk.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
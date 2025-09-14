@extends('layouts.app')

@section('title', 'Edit Transaksi Barang')

@section('content')
<div class="container">
    <h2 class="mb-4">Edit Transaksi Barang</h2>

    <form action="{{ route('transaksibarang.update', $transaksibarang->id) }}" method="POST">
        @csrf @method('PUT')
        <div class="mb-3">
            <label>Transaksi Kas</label>
            <select name="id_transaksi_kas" class="form-control" required>
                @foreach($transaksis as $trx)
                    <option value="{{ $trx->id }}" {{ $trx->id == $transaksibarang->id_transaksi_kas ? 'selected' : '' }}>
                        #{{ $trx->id }} - {{ ucfirst($trx->jenis) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label>Barang</label>
            <select name="id_barang" class="form-control" required>
                @foreach($barangs as $brg)
                    <option value="{{ $brg->id }}" {{ $brg->id == $transaksibarang->id_barang ? 'selected' : '' }}>
                        {{ $brg->nama_barang }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label>Jumlah</label>
            <input type="number" name="jumlah" class="form-control" value="{{ $transaksibarang->jumlah }}" required>
        </div>
        <div class="mb-3">
            <label>Status</label>
            <input type="text" name="status" class="form-control" value="{{ $transaksibarang->status }}" required>
        </div>
        <div class="mb-3">
            <label>Jenis</label>
            <select name="jenis" class="form-control" required>
                <option value="masuk" {{ $transaksibarang->jenis == 'masuk' ? 'selected' : '' }}>Masuk</option>
                <option value="keluar" {{ $transaksibarang->jenis == 'keluar' ? 'selected' : '' }}>Keluar</option>
            </select>
        </div>
        <div class="mb-3">
            <label>Keterangan</label>
            <textarea name="keterangan" class="form-control">{{ $transaksibarang->keterangan }}</textarea>
        </div>
        <button class="btn btn-success">Update</button>
    </form>
</div>
@endsection

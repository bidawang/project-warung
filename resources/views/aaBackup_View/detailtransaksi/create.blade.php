@extends('layouts.app')

@section('title', 'Tambah Detail Transaksi')

@section('content')
<div class="container">
    <h2 class="mb-4">Tambah Detail Transaksi</h2>

    <form action="{{ route('detailtransaksi.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Transaksi</label>
            <select name="id_transaksi" class="form-control">
                @foreach($transaksis as $trx)
                    <option value="{{ $trx->id }}">#{{ $trx->id }} - {{ ucfirst($trx->jenis) }} - Rp{{ number_format($trx->total, 0, ',', '.') }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label>Pecahan</label>
            <input type="number" name="pecahan" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Jumlah</label>
            <input type="number" name="jumlah" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Keterangan</label>
            <textarea name="keterangan" class="form-control"></textarea>
        </div>
        <button class="btn btn-success">Simpan</button>
    </form>
</div>
@endsection

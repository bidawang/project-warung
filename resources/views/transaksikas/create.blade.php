@extends('layouts.app')

@section('title', 'Tambah Transaksi Kas')

@section('content')
<div class="container">
    <h2 class="mb-4">Tambah Transaksi Kas</h2>

    <form action="{{ route('transaksikas.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Kas Warung</label>
            <select name="id_kas_warung" class="form-control">
                @foreach($kasWarungs as $kas)
                    <option value="{{ $kas->id }}">{{ $kas->warung->nama_warung ?? '-' }} - {{ $kas->jenis_kas }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label>Total</label>
            <input type="number" name="total" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Metode Pembayaran</label>
            <input type="text" name="metode_pembayaran" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Keterangan</label>
            <textarea name="keterangan" class="form-control"></textarea>
        </div>
        <button class="btn btn-success">Simpan</button>
    </form>
</div>
@endsection

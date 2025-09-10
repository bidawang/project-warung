@extends('layouts.app')

@section('title', 'Edit Transaksi Kas')

@section('content')
<div class="container">
    <h2 class="mb-4">Edit Transaksi Kas</h2>

    <form action="{{ route('transaksikas.update', $transaksika->id) }}" method="POST">
        @csrf @method('PUT')
        <div class="mb-3">
            <label>Kas Warung</label>
            <select name="id_kas_warung" class="form-control">
                @foreach($kasWarungs as $kas)
                    <option value="{{ $kas->id }}" {{ $kas->id == $transaksika->id_kas_warung ? 'selected' : '' }}>
                        {{ $kas->warung->nama_warung ?? '-' }} - {{ $kas->jenis_kas }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label>Total</label>
            <input type="number" name="total" class="form-control" value="{{ $transaksika->total }}" required>
        </div>
        <div class="mb-3">
            <label>Metode Pembayaran</label>
            <input type="text" name="metode_pembayaran" class="form-control" value="{{ $transaksika->metode_pembayaran }}" required>
        </div>
        <div class="mb-3">
            <label>Keterangan</label>
            <textarea name="keterangan" class="form-control">{{ $transaksika->keterangan }}</textarea>
        </div>
        <button class="btn btn-success">Update</button>
    </form>
</div>
@endsection

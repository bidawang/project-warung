@extends('layouts.app')

@section('title', 'Tambah Pembayaran Hutang')

@section('content')
<div class="container">
    <h2 class="mb-4">Tambah Pembayaran Hutang</h2>

    <form action="{{ route('pembayaranhutang.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Hutang</label>
            <select name="id_hutang" class="form-select" required>
                <option value="">-- Pilih Hutang --</option>
                @foreach($hutang as $h)
                <option value="{{ $h->id }}">ID: {{ $h->id }} - {{ $h->user->name ?? '-' }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Transaksi Kas</label>
            <select name="id_transaksi_kas" class="form-select" required>
                <option value="">-- Pilih Transaksi Kas --</option>
                @foreach($transaksiKas as $t)
                <option value="{{ $t->id }}">ID: {{ $t->id }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Keterangan</label>
            <textarea name="keterangan" class="form-control"></textarea>
        </div>

        <button class="btn btn-success">Simpan</button>
        <a href="{{ route('pembayaranhutang.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection

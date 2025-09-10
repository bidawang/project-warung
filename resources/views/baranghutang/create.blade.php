@extends('layouts.app')

@section('title', 'Tambah Barang Hutang')

@section('content')
<div class="container">
    <h2 class="mb-4">Tambah Barang Hutang</h2>

    <form action="{{ route('baranghutang.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Hutang</label>
            <select name="id_hutang" class="form-select" required>
                <option value="">-- Pilih Hutang --</option>
                @foreach($hutang as $h)
                <option value="{{ $h->id }}">ID: {{ $h->id }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Transaksi Barang</label>
            <select name="id_transaksi_barang" class="form-select" required>
                <option value="">-- Pilih Transaksi Barang --</option>
                @foreach($transaksiBarang as $tb)
                <option value="{{ $tb->id }}">ID: {{ $tb->id }}</option>
                @endforeach
            </select>
        </div>

        <button class="btn btn-success">Simpan</button>
        <a href="{{ route('baranghutang.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection

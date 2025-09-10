@extends('layouts.app')

@section('title', 'Tambah Kuantitas')

@section('content')
<div class="container">
    <h2 class="mb-4">Tambah Kuantitas</h2>

    <form action="{{ route('kuantitas.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Stok Warung</label>
            <select name="id_stok_warung" class="form-select" required>
                <option value="">-- Pilih --</option>
                @foreach($stokWarung as $s)
                    <option value="{{ $s->id }}">
                        {{ $s->barang->nama_barang }} - {{ $s->warung->nama_warung }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Jumlah</label>
            <input type="number" name="jumlah" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Harga Jual</label>
            <input type="number" name="harga_jual" class="form-control" required>
        </div>

        <button class="btn btn-primary">Simpan</button>
        <a href="{{ route('kuantitas.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection

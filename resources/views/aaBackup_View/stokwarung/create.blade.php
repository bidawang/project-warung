@extends('layouts.app')

@section('title', 'Tambah Stok Warung')

@section('content')
<div class="container">
    <h1 class="mb-4">Tambah Stok Warung</h1>
    <form action="{{ route('stokwarung.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Warung</label>
            <select name="id_warung" class="form-control" required>
                <option value="">-- Pilih Warung --</option>
                @foreach($warung as $w)
                    <option value="{{ $w->id }}">{{ $w->nama_warung }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label>Barang</label>
            <select name="id_barang" class="form-control" required>
                <option value="">-- Pilih Barang --</option>
                @foreach($barang as $b)
                    <option value="{{ $b->id }}">{{ $b->nama_barang }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label>Keterangan</label>
            <textarea name="keterangan" class="form-control"></textarea>
        </div>
        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="{{ route('stokwarung.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection

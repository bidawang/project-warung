@extends('layouts.app')

@section('title', 'Edit Kuantitas')

@section('content')

<div class="container">
<h2 class="mb-4">Edit Kuantitas</h2>

<form action="{{ route('kasir.kuantitas.update', $kuantitas->id) }}" method="POST">
    @csrf
    @method('PUT')

    {{-- Info stok warung tidak bisa diganti --}}
    <div class="mb-3">
        <label for="stok_info" class="form-label">Stok Warung</label>
        <input type="text" class="form-control" id="stok_info"
            value="{{ $kuantitas->stokWarung->barang->nama_barang }} - {{ $kuantitas->stokWarung->warung->nama_warung }}" readonly>
        <input type="hidden" name="id_stok_warung" value="{{ $kuantitas->id_stok_warung }}">
    </div>

    {{-- Jumlah unit --}}
    <div class="mb-3">
        <label for="jumlah" class="form-label">Jumlah</label>
        <input type="number" name="jumlah" id="jumlah" class="form-control"
            value="{{ old('jumlah', $kuantitas->jumlah) }}" min="2" required>
    </div>

    {{-- Harga jual --}}
    <div class="mb-3">
        <label for="harga_jual" class="form-label">Harga Jual</label>
        <input type="number" name="harga_jual" id="harga_jual" class="form-control"
            value="{{ old('harga_jual', $kuantitas->harga_jual) }}"
            min="{{ $hargaJualSatuanDasar }}" required>
        <small class="form-text text-muted">
            Harga jual satuan dasar: **Rp {{ number_format($hargaJualSatuanDasar, 0, ',', '.') }}**
        </small>
    </div>

    <button type="submit" class="btn btn-primary">Update</button>
    <a href="{{ url()->previous() }}" class="btn btn-secondary">Batal</a>
</form>

</div>
@endsection

@extends('layouts.app')

@section('title', 'Tambah Kuantitas')

@section('content')

<div class="container">
<h2 class="mb-4">Tambah Kuantitas</h2>

<form action="{{ route('kasir.kuantitas.store') }}" method="POST">
    @csrf

    <div class="mb-3">
        <label for="stok_info" class="form-label">Stok Warung</label>
        @if(isset($selectedStokWarung))
        <input type="text" class="form-control" id="stok_info"
            value="{{ $selectedStokWarung->barang->nama_barang }} - {{ $selectedStokWarung->warung->nama_warung }}" readonly>
        <input type="hidden" name="id_stok_warung" value="{{ $selectedStokWarung->id }}">
        @else
        <select name="id_stok_warung" id="id_stok_warung" class="form-select" required>
            <option value="">-- Pilih --</option>
            @foreach($stokWarung as $s)
            <option value="{{ $s->id }}">
                {{ $s->barang->nama_barang }} - {{ $s->warung->nama_warung }}
            </option>
            @endforeach
        </select>
        @endif
    </div>

    <div class="mb-3">
        <label for="jumlah" class="form-label">Jumlah</label>
        <input type="number" name="jumlah" id="jumlah" class="form-control" value="2" min="2" required>
    </div>

    <div class="mb-3">
        <label for="harga_jual" class="form-label">Harga Jual</label>
        @if(isset($hargaJualSatuanDasar))
        <input type="number" name="harga_jual" id="harga_jual" class="form-control" min="0" required>
        <small class="form-text text-muted">Harga jual satuan dasar: **Rp {{ number_format($hargaJualSatuanDasar, 0, ',', '.') }}**</small>
        @else
        <input type="number" name="harga_jual" id="harga_jual" class="form-control" required>
        @endif
    </div>

    <button type="submit" class="btn btn-primary">Simpan</button>
    <a href="{{ url()->previous() }}" class="btn btn-secondary">Batal</a>
</form>

</div>
@endsection

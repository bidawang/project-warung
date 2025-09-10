@extends('layouts.app')

@section('title', 'Tambah Mutasi Barang')

@section('content')
<div class="container">
    <h2 class="mb-4">Tambah Mutasi Barang</h2>

    <form action="{{ route('mutasibarang.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Stok Warung</label>
            <select name="id_stok_warung" class="form-select" required>
                <option value="">-- Pilih --</option>
                @foreach($stokWarung as $stok)
                <option value="{{ $stok->id }}">
                    {{ $stok->barang->nama_barang }} ({{ $stok->warung->nama_warung }})
                </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Warung Asal</label>
            <select name="warung_asal" class="form-select" required>
                <option value="">-- Pilih --</option>
                @foreach($warung as $w)
                <option value="{{ $w->id }}">{{ $w->nama_warung }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Warung Tujuan</label>
            <select name="warung_tujuan" class="form-select" required>
                <option value="">-- Pilih --</option>
                @foreach($warung as $w)
                <option value="{{ $w->id }}">{{ $w->nama_warung }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Jumlah</label>
            <input type="number" name="jumlah" class="form-control" min="1" required>
        </div>

        <div class="mb-3">
            <label>Status</label>
            <select name="status" class="form-select" required>
                <option value="pending">Pending</option>
                <option value="disetujui">Disetujui</option>
                <option value="ditolak">Ditolak</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Keterangan</label>
            <textarea name="keterangan" class="form-control"></textarea>
        </div>

        <button class="btn btn-primary">Simpan</button>
        <a href="{{ route('mutasibarang.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
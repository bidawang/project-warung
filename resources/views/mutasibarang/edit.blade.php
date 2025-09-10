@extends('layouts.app')

@section('title', 'Edit Mutasi Barang')

@section('content')
<div class="container">
    <h2 class="mb-4">Edit Mutasi Barang</h2>

    <form action="{{ route('mutasibarang.update', $mutasi->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Stok Warung</label>
            <select name="id_stok_warung" class="form-select" required>
                <option value="">-- Pilih --</option>
                @foreach($stokWarung as $stok)
                <option value="{{ $stok->id }}" {{ $stok->id == $mutasi->id_stok_warung ? 'selected' : '' }}>
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
                <option value="{{ $w->id }}" {{ $w->id == $mutasi->warung_asal ? 'selected' : '' }}>
                    {{ $w->nama_warung }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Warung Tujuan</label>
            <select name="warung_tujuan" class="form-select" required>
                <option value="">-- Pilih --</option>
                @foreach($warung as $w)
                <option value="{{ $w->id }}" {{ $w->id == $mutasi->warung_tujuan ? 'selected' : '' }}>
                    {{ $w->nama_warung }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Jumlah</label>
            <input type="number" name="jumlah" class="form-control" min="1" value="{{ $mutasi->jumlah }}" required>
        </div>

        <div class="mb-3">
            <label>Status</label>
            <select name="status" class="form-select" required>
                <option value="pending" {{ $mutasi->status == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="disetujui" {{ $mutasi->status == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                <option value="ditolak" {{ $mutasi->status == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Keterangan</label>
            <textarea name="keterangan" class="form-control">{{ $mutasi->keterangan }}</textarea>
        </div>

        <button class="btn btn-primary">Update</button>
        <a href="{{ route('mutasibarang.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection

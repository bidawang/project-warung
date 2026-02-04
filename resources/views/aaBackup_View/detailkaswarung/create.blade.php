@extends('layouts.app')

@section('title', 'Tambah Detail Kas Warung')

@section('content')
<div class="container my-4">
    <h2 class="mb-4 text-center">Tambah Detail Kas Warung</h2>

    <div class="card shadow p-4">
        <form action="{{ route('detailkaswarung.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="id_kas_warung" class="form-label">Pilih Kas Warung</label>
                <select name="id_kas_warung" id="id_kas_warung" class="form-select" required>
                    <option value="">-- Pilih Kas Warung --</option>
                    @foreach($kas as $k)
                        <option value="{{ $k->id }}">{{ $k->jenis_kas }} - {{ $k->warung->nama_warung }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="pecahan" class="form-label">Pecahan</label>
                <input type="text" class="form-control" id="pecahan" name="pecahan" required>
            </div>

            <div class="mb-3">
                <label for="jumlah" class="form-label">Jumlah</label>
                <input type="number" class="form-control" id="jumlah" name="jumlah" required>
            </div>

            <div class="mb-3">
                <label for="keterangan" class="form-label">Keterangan</label>
                <textarea class="form-control" id="keterangan" name="keterangan"></textarea>
            </div>

            <button type="submit" class="btn btn-success">Simpan</button>
            <a href="{{ route('detailkaswarung.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection

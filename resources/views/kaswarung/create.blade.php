@extends('layouts.app')

@section('title', 'Tambah Kas Warung')

@section('content')
<div class="container my-4">
    <h2 class="mb-4 text-center">Tambah Kas Warung</h2>

    <div class="card shadow p-4">
        <form action="{{ route('kaswarung.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="id_warung" class="form-label">Pilih Warung</label>
                <select name="id_warung" id="id_warung" class="form-select" required>
                    <option value="">-- Pilih Warung --</option>
                    @foreach($warungs as $warung)
                        <option value="{{ $warung->id }}">{{ $warung->nama_warung }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="jenis_kas" class="form-label">Jenis Kas</label>
                <input type="text" class="form-control" id="jenis_kas" name="jenis_kas" required>
            </div>

            <div class="mb-3">
                <label for="keterangan" class="form-label">Keterangan</label>
                <textarea class="form-control" id="keterangan" name="keterangan"></textarea>
            </div>

            <button type="submit" class="btn btn-success">Simpan</button>
            <a href="{{ route('kaswarung.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection

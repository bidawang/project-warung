@extends('layouts.app')

@section('title', 'Tambah Warung')

@section('content')
<div class="container my-4">
    <h2 class="text-center mb-4">Tambah Warung Baru</h2>

    <div class="card shadow p-4">
        <form action="{{ route('warung.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="id_user" class="form-label">Pemilik (User)</label>
                <select class="form-select" id="id_user" name="id_user" required>
                    <option value="">-- Pilih User --</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label for="id_area" class="form-label">Area</label>
                <select class="form-select" id="id_area" name="id_area" required>
                    <option value="">-- Pilih Area --</option>
                    @foreach($areas as $area)
                        <option value="{{ $area->id }}">{{ $area->area }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label for="nama_warung" class="form-label">Nama Warung</label>
                <input type="text" class="form-control" id="nama_warung" name="nama_warung" value="{{ old('nama_warung') }}" required>
            </div>
            <div class="mb-3">
                <label for="modal" class="form-label">Modal</label>
                <input type="number" class="form-control" id="modal" name="modal" value="{{ old('modal') }}" required>
            </div>
            <div class="mb-3">
                <label for="keterangan" class="form-label">Keterangan</label>
                <textarea class="form-control" id="keterangan" name="keterangan">{{ old('keterangan') }}</textarea>
            </div>
            <div class="d-flex justify-content-end">
                <a href="{{ route('warung.index') }}" class="btn btn-secondary me-2">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

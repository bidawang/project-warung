@extends('layouts.app')

@section('title', 'Tambah Hutang')

@section('content')
<div class="container">
    <h2 class="mb-4">Tambah Hutang</h2>

    <form action="{{ route('hutang.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Warung</label>
            <select name="id_warung" class="form-select" required>
                <option value="">-- Pilih Warung --</option>
                @foreach($warung as $w)
                <option value="{{ $w->id }}">{{ $w->nama_warung }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>User</label>
            <select name="id_user" class="form-select" required>
                <option value="">-- Pilih User --</option>
                @foreach($user as $u)
                <option value="{{ $u->id }}">{{ $u->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Jumlah</label>
            <input type="number" name="jumlah" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Tenggat</label>
            <input type="date" name="tenggat" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Status</label>
            <select name="status" class="form-select">
                <option value="belum lunas">Belum Lunas</option>
                <option value="lunas">Lunas</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Keterangan</label>
            <textarea name="keterangan" class="form-control"></textarea>
        </div>

        <button class="btn btn-primary">Simpan</button>
        <a href="{{ route('hutang.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Tambah Target Pencapaian')

@section('content')
<div class="container">
    <h2 class="mb-4">Tambah Target Pencapaian</h2>

    <form action="{{ route('targetpencapaian.store') }}" method="POST">
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
            <label>Periode Awal</label>
            <input type="date" name="periode_awal" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Periode Akhir</label>
            <input type="date" name="periode_akhir" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Target Pencapaian</label>
            <input type="number" name="target_pencapaian" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Status</label>
            <select name="status_pencapaian" class="form-select" required>
                <option value="belum tercapai">Belum Tercapai</option>
                <option value="tercapai">Tercapai</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Keterangan</label>
            <textarea name="keterangan" class="form-control"></textarea>
        </div>

        <button class="btn btn-primary">Simpan</button>
        <a href="{{ route('targetpencapaian.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Edit Target Pencapaian')

@section('content')
<div class="container">
    <h2 class="mb-4">Edit Target Pencapaian</h2>

    <form action="{{ route('targetpencapaian.update', $targetpencapaian->id) }}" method="POST">
        @csrf @method('PUT')

        <div class="mb-3">
            <label>Warung</label>
            <select name="id_warung" class="form-select" required>
                @foreach($warung as $w)
                    <option value="{{ $w->id }}" {{ $w->id == $targetpencapaian->id_warung ? 'selected' : '' }}>
                        {{ $w->nama_warung }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Periode Awal</label>
            <input type="date" name="periode_awal" class="form-control" value="{{ $targetpencapaian->periode_awal }}" required>
        </div>

        <div class="mb-3">
            <label>Periode Akhir</label>
            <input type="date" name="periode_akhir" class="form-control" value="{{ $targetpencapaian->periode_akhir }}" required>
        </div>

        <div class="mb-3">
            <label>Target Pencapaian</label>
            <input type="number" name="target_pencapaian" class="form-control" value="{{ $targetpencapaian->target_pencapaian }}" required>
        </div>

        <div class="mb-3">
            <label>Status</label>
            <select name="status_pencapaian" class="form-select" required>
                <option value="belum tercapai" {{ $targetpencapaian->status_pencapaian == 'belum tercapai' ? 'selected' : '' }}>Belum Tercapai</option>
                <option value="tercapai" {{ $targetpencapaian->status_pencapaian == 'tercapai' ? 'selected' : '' }}>Tercapai</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Keterangan</label>
            <textarea name="keterangan" class="form-control">{{ $targetpencapaian->keterangan }}</textarea>
        </div>

        <button class="btn btn-primary">Update</button>
        <a href="{{ route('targetpencapaian.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection

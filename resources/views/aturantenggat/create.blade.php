@extends('layouts.app')

@section('title', 'Tambah Aturan Tenggat')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <h1 class="text-center my-4">Tambah Aturan Tenggat Baru</h1>
            <div class="card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title">Formulir Aturan Tenggat</h5>
                </div>
                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                <form action="{{ route('aturan_tenggat.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="id_area" class="form-label">Area</label>
                        <select class="form-control" id="id_area" name="id_area" required>
                            <option value="">Pilih Area</option>
                            @foreach ($areas as $area)
                            <option value="{{ $area->id }}" {{ old('id_area') == $area->id ? 'selected' : '' }}>
                                {{ $area->area }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="tanggal_awal" class="form-label">Tanggal Awal</label>
                        <input type="date" class="form-control" id="tanggal_awal" name="tanggal_awal" value="{{ old('tanggal_awal') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="tanggal_akhir" class="form-label">Tanggal Akhir</label>
                        <input type="date" class="form-control" id="tanggal_akhir" name="tanggal_akhir" value="{{ old('tanggal_akhir') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="jatuh_tempo_hari" class="form-label">Jatuh Tempo (Hari)</label>
                        <input type="number" class="form-control" id="jatuh_tempo_hari" name="jatuh_tempo_hari" value="{{ old('jatuh_tempo_hari') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="jatuh_tempo_bulan" class="form-label">Jatuh Tempo (Bulan)</label>
                        <input type="number" class="form-control" id="jatuh_tempo_bulan" name="jatuh_tempo_bulan" value="{{ old('jatuh_tempo_bulan') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="bunga" class="form-label">Bunga (%)</label>
                        <input type="number" step="0.01" class="form-control" id="bunga" name="bunga" value="{{ old('bunga') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan (Opsional)</label>
                        <textarea class="form-control" id="keterangan" name="keterangan" rows="3">{{ old('keterangan') }}</textarea>
                    </div>
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('aturan_tenggat.index') }}" class="btn btn-secondary me-2">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
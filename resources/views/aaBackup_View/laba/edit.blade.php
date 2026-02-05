@extends('layouts.app')

@section('title', 'Edit Data Laba')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <h1 class="text-center my-4">Edit Data Laba</h1>
            <div class="card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title">Formulir Laba</h5>
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
                <form action="{{ route('laba.update', $laba->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="id_area" class="form-label">Area</label>
                        <select class="form-control" id="id_area" name="id_area" required>
                            <option value="">Pilih Area</option>
                            @foreach ($areas as $area)
                            <option value="{{ $area->id }}" {{ old('id_area', $laba->id_area) == $area->id ? 'selected' : '' }}>
                                {{ $area->area }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="input_minimal" class="form-label">Input Minimal</label>
                        <input type="number" step="0.01" class="form-control" id="input_minimal" name="input_minimal" value="{{ old('input_minimal', $laba->input_minimal) }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="input_maksimal" class="form-label">Input Maksimal</label>
                        <input type="number" step="0.01" class="form-control" id="input_maksimal" name="input_maksimal" value="{{ old('input_maksimal', $laba->input_maksimal) }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="harga_jual" class="form-label">Harga Jual</label>
                        <input type="number" step="0.01" class="form-control" id="harga_jual" name="harga_jual" value="{{ old('harga_jual', $laba->harga_jual) }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="jenis" class="form-label">Jenis</label>
                        <input type="text" class="form-control" id="jenis" name="jenis" value="{{ old('jenis', $laba->jenis) }}">
                    </div>
                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan (Opsional)</label>
                        <textarea class="form-control" id="keterangan" name="keterangan" rows="3">{{ old('keterangan', $laba->keterangan) }}</textarea>
                    </div>
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('laba.index') }}" class="btn btn-secondary me-2">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
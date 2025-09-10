@extends('layouts.app')

@section('title', 'Tambah Area')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <h1 class="text-center my-4">Tambah Area Baru</h1>
            <div class="card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title">Formulir Area</h5>
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
                <form action="{{ route('area.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="area" class="form-label">Nama Area</label>
                        <input type="text" class="form-control" id="area" name="area" value="{{ old('area') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan (Opsional)</label>
                        <textarea class="form-control" id="keterangan" name="keterangan" rows="3">{{ old('keterangan') }}</textarea>
                    </div>
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('area.index') }}" class="btn btn-secondary me-2">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@extends('layouts.app')

@section('title', 'Tambah Subkategori')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <h1 class="text-center my-4">Tambah Subkategori Baru</h1>
            <div class="card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title">Formulir Subkategori</h5>
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
                <form action="{{ route('subkategori.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="id_kategori" class="form-label">Kategori Induk</label>
                        <select class="form-control" id="id_kategori" name="id_kategori" required>
                            <option value="">Pilih Kategori</option>
                            @foreach ($kategoris as $kategori)
                            <option value="{{ $kategori->id }}" {{ old('id_kategori') == $kategori->id ? 'selected' : '' }}>
                                {{ $kategori->kategori }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="subkategori" class="form-label">Nama Subkategori</label>
                        <input type="text" class="form-control" id="subkategori" name="sub_kategori" value="{{ old('sub_kategori') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan (Opsional)</label>
                        <textarea class="form-control" id="keterangan" name="keterangan" rows="3">{{ old('keterangan') }}</textarea>
                    </div>
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('subkategori.index') }}" class="btn btn-secondary me-2">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
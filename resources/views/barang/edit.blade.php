@extends('layouts.app')

@section('title', 'Edit Barang')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <h1 class="text-center my-4">Edit Barang</h1>
            <div class="card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title">Formulir Barang</h5>
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
                <form action="{{ route('barang.update', $barang->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="id_sub_kategori" class="form-label">Subkategori</label>
                        <select class="form-control" id="id_sub_kategori" name="id_sub_kategori" required>
                            <option value="">Pilih Subkategori</option>
                            @foreach ($subkategoris as $subkategori)
                            <option value="{{ $subkategori->id }}" {{ old('id_sub_kategori', $barang->id_sub_kategori) == $subkategori->id ? 'selected' : '' }}>
                                {{ $subkategori->kategori->kategori ?? '' }} > {{ $subkategori->sub_kategori }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="kode_barang" class="form-label">Kode Barang</label>
                        <input type="text" class="form-control" id="kode_barang" name="kode_barang" value="{{ old('kode_barang', $barang->kode_barang) }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="nama_barang" class="form-label">Nama Barang</label>
                        <input type="text" class="form-control" id="nama_barang" name="nama_barang" value="{{ old('nama_barang', $barang->nama_barang) }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan (Opsional)</label>
                        <textarea class="form-control" id="keterangan" name="keterangan" rows="3">{{ old('keterangan', $barang->keterangan) }}</textarea>
                    </div>
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('barang.index') }}" class="btn btn-secondary me-2">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
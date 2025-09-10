@extends('layouts.app')

@section('title', 'Edit Stok Warung')

@section('content')
<div class="container">
    <h1 class="mb-4">Edit Stok Warung</h1>
    <form action="{{ route('stokwarung.update', $stokwarung->id) }}" method="POST">
        @csrf @method('PUT')
        <div class="mb-3">
            <label>Warung</label>
            <select name="id_warung" class="form-control" required>
                @foreach($warung as $w)
                    <option value="{{ $w->id }}" {{ $stokwarung->id_warung == $w->id ? 'selected' : '' }}>
                        {{ $w->nama_warung }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label>Barang</label>
            <select name="id_barang" class="form-control" required>
                @foreach($barang as $b)
                    <option value="{{ $b->id }}" {{ $stokwarung->id_barang == $b->id ? 'selected' : '' }}>
                        {{ $b->nama_barang }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label>Keterangan</label>
            <textarea name="keterangan" class="form-control">{{ $stokwarung->keterangan }}</textarea>
        </div>
        <button type="submit" class="btn btn-success">Update</button>
        <a href="{{ route('stokwarung.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection

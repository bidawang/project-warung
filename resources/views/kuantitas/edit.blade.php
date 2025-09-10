@extends('layouts.app')

@section('title', 'Edit Kuantitas')

@section('content')
<div class="container">
    <h2 class="mb-4">Edit Kuantitas</h2>

    <form action="{{ route('kuantitas.update', $kuantita->id) }}" method="POST">
        @csrf @method('PUT')

        <div class="mb-3">
            <label>Stok Warung</label>
            <select name="id_stok_warung" class="form-select" required>
                @foreach($stokWarung as $s)
                    <option value="{{ $s->id }}" {{ $s->id == $kuantita->id_stok_warung ? 'selected' : '' }}>
                        {{ $s->barang->nama_barang }} - {{ $s->warung->nama_warung }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Jumlah</label>
            <input type="number" name="jumlah" class="form-control" value="{{ $kuantita->jumlah }}" required>
        </div>

        <div class="mb-3">
            <label>Harga Jual</label>
            <input type="number" name="harga_jual" class="form-control" value="{{ $kuantita->harga_jual }}" required>
        </div>

        <button class="btn btn-primary">Update</button>
        <a href="{{ route('kuantitas.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection

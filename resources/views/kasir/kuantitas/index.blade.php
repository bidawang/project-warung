@extends('layouts.app')

@section('title', 'Daftar Kuantitas')

@section('content')
<div class="container">
    <h2 class="mb-4">Daftar Kuantitas</h2>

    <a href="{{ route('kuantitas.create') }}" class="btn btn-primary mb-3">Tambah Kuantitas</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Barang</th>
                <th>Warung</th>
                <th>Jumlah</th>
                <th>Harga Jual</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($kuantitas as $k)
            <tr>
                <td>{{ $k->id }}</td>
                <td>{{ $k->stokWarung->barang->nama_barang ?? '-' }}</td>
                <td>{{ $k->stokWarung->warung->nama_warung ?? '-' }}</td>
                <td>{{ $k->jumlah }}</td>
                <td>Rp {{ number_format($k->harga_jual,0,',','.') }}</td>
                <td>
                    <a href="{{ route('kuantitas.show', $k->id) }}" class="btn btn-info btn-sm">Detail</a>
                    <a href="{{ route('kuantitas.edit', $k->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('kuantitas.destroy', $k->id) }}" method="POST" style="display:inline;">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger btn-sm" onclick="return confirm('Hapus kuantitas ini?')">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Data Stok Warung')

@section('content')
<div class="container">
    <h1 class="mb-4">Data Stok Warung</h1>
    <a href="{{ route('stokwarung.create') }}" class="btn btn-primary mb-3">Tambah Stok</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Warung</th>
                <th>Barang</th>
                <th>Keterangan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($stokWarung as $stok)
                <tr>
                    <td>{{ $stok->id }}</td>
                    <td>{{ $stok->warung->nama_warung ?? '-' }}</td>
                    <td>{{ $stok->barang->nama_barang ?? '-' }}</td>
                    <td>{{ $stok->keterangan }}</td>
                    <td>
                        <a href="{{ route('stokwarung.show', $stok->id) }}" class="btn btn-info btn-sm">Detail</a>
                        <a href="{{ route('stokwarung.edit', $stok->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('stokwarung.destroy', $stok->id) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus data ini?')">Hapus</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

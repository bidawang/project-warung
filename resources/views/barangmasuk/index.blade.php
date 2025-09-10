@extends('layouts.app')

@section('title', 'Data Barang Masuk')

@section('content')
<div class="container">
    <h1 class="mb-4">Data Barang Masuk</h1>
    <a href="{{ route('barangmasuk.create') }}" class="btn btn-primary mb-3">Tambah Barang Masuk</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Transaksi</th>
                <th>Stok Warung</th>
                <th>Jumlah</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($barangMasuk as $bm)
                <tr>
                    <td>{{ $bm->id }}</td>
                    <td>{{ $bm->transaksiBarang->id ?? '-' }}</td>
                    <td>{{ $bm->stokWarung->id ?? '-' }}</td>
                    <td>{{ $bm->jumlah }}</td>
                    <td>{{ $bm->status }}</td>
                    <td>
                        <a href="{{ route('barangmasuk.show', $bm->id) }}" class="btn btn-info btn-sm">Detail</a>
                        <a href="{{ route('barangmasuk.edit', $bm->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('barangmasuk.destroy', $bm->id) }}" method="POST" class="d-inline">
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

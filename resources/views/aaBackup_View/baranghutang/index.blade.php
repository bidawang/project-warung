@extends('layouts.app')

@section('title', 'Daftar Barang Hutang')

@section('content')
<div class="container">
    <h2 class="mb-4">Daftar Barang Hutang</h2>
    <a href="{{ route('baranghutang.create') }}" class="btn btn-primary mb-3">Tambah Barang Hutang</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>ID Hutang</th>
                <th>ID Transaksi Barang</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($baranghutang as $bh)
            <tr>
                <td>{{ $bh->id }}</td>
                <td>{{ $bh->hutang->id }}</td>
                <td>{{ $bh->transaksiBarang->id }}</td>
                <td>
                    <a href="{{ route('baranghutang.edit', $bh->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('baranghutang.destroy', $bh->id) }}" method="POST" class="d-inline"
                        onsubmit="return confirm('Yakin hapus?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger btn-sm">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

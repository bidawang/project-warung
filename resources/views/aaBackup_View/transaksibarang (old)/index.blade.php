@extends('layouts.app')

@section('title', 'Daftar Transaksi Barang')

@section('content')
<div class="container">
    <h2 class="mb-4">Daftar Transaksi Barang</h2>

    <a href="{{ route('transaksibarang.create') }}" class="btn btn-primary mb-3">Tambah Transaksi Barang</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Transaksi Kas</th>
                <th>Barang</th>
                <th>Jumlah</th>
                <th>Status</th>
                <th>Jenis</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transaksibarangs as $trx)
            <tr>
                <td>{{ $trx->id }}</td>
                <td>#{{ $trx->transaksiKas->id ?? '-' }}</td>
                <td>{{ $trx->barang->nama_barang ?? '-' }}</td>
                <td>{{ $trx->jumlah }}</td>
                <td>{{ $trx->status }}</td>
                <td>{{ ucfirst($trx->jenis) }}</td>
                <td>
                    <a href="{{ route('transaksibarang.edit', $trx->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('transaksibarang.destroy', $trx->id) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('Yakin hapus transaksi ini?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger btn-sm">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $transaksibarangs->links() }}
</div>
@endsection

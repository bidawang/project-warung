@extends('layouts.app')

@section('title', 'Daftar Transaksi Kas')

@section('content')
<div class="container">
    <h2 class="mb-4">Daftar Transaksi Kas</h2>

    <a href="{{ route('transaksikas.create') }}" class="btn btn-primary mb-3">Tambah Transaksi</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Warung</th>
                <th>Total</th>
                <th>Metode Pembayaran</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transaksis as $trx)
            <tr>
                <td>{{ $trx->id }}</td>
                <td>{{ $trx->kasWarung->warung->nama_warung ?? '-' }}</td>
                <td>{{ number_format($trx->total, 0, ',', '.') }}</td>
                <td>{{ $trx->metode_pembayaran }}</td>
                <td>
                    <a href="{{ route('transaksikas.show', $trx->id) }}" class="btn btn-info btn-sm">Detail</a>
                    <a href="{{ route('transaksikas.edit', $trx->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('transaksikas.destroy', $trx->id) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('Yakin hapus transaksi ini?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger btn-sm">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $transaksis->links() }}
</div>
@endsection

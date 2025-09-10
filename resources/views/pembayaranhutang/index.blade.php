@extends('layouts.app')

@section('title', 'Daftar Pembayaran Hutang')

@section('content')
<div class="container">
    <h2 class="mb-4">Daftar Pembayaran Hutang</h2>
    <a href="{{ route('pembayaranhutang.create') }}" class="btn btn-primary mb-3">Tambah Pembayaran</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Hutang</th>
                <th>Transaksi Kas</th>
                <th>Keterangan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pembayaran as $p)
            <tr>
                <td>{{ $p->id }}</td>
                <td>{{ $p->hutang->id }} - {{ $p->hutang->user->name ?? '-' }}</td>
                <td>{{ $p->transaksiKas->id ?? '-' }}</td>
                <td>{{ $p->keterangan }}</td>
                <td>
                    <a href="{{ route('pembayaranhutang.edit', $p->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('pembayaranhutang.destroy', $p->id) }}" method="POST" class="d-inline"
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

@extends('layouts.app')

@section('title', 'Daftar Detail Transaksi')

@section('content')
<div class="container">
    <h2 class="mb-4">Daftar Detail Transaksi</h2>

    <a href="{{ route('detailtransaksi.create') }}" class="btn btn-primary mb-3">Tambah Detail</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>ID Transaksi</th>
                <th>Pecahan</th>
                <th>Jumlah</th>
                <th>Keterangan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($details as $detail)
            <tr>
                <td>{{ $detail->id }}</td>
                <td>{{ $detail->transaksi->id ?? '-' }}</td>
                <td>{{ number_format($detail->pecahan, 0, ',', '.') }}</td>
                <td>{{ $detail->jumlah }}</td>
                <td>{{ $detail->keterangan }}</td>
                <td>
                    <a href="{{ route('detailtransaksi.show', $detail->id) }}" class="btn btn-info btn-sm">Detail</a>
                    <a href="{{ route('detailtransaksi.edit', $detail->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('detailtransaksi.destroy', $detail->id) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('Yakin hapus detail ini?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger btn-sm">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $details->links() }}
</div>
@endsection

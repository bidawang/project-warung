@extends('layouts.app')

@section('title', 'Data Hutang')

@section('content')
<div class="container">
    <h2 class="mb-4">Data Hutang</h2>
    <a href="{{ route('hutang.create') }}" class="btn btn-primary mb-3">Tambah Hutang</a>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Warung</th>
                <th>User</th>
                <th>Jumlah</th>
                <th>Tenggat</th>
                <th>Status</th>
                <th>Keterangan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($hutang as $h)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $h->warung->nama_warung }}</td>
                <td>{{ $h->user->name }}</td>
                <td>{{ $h->jumlah }}</td>
                <td>{{ $h->tenggat }}</td>
                <td>{{ $h->status }}</td>
                <td>{{ $h->keterangan }}</td>
                <td>
                    <a href="{{ route('hutang.show', $h->id) }}" class="btn btn-info btn-sm">Detail</a>
                    <a href="{{ route('hutang.edit', $h->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('hutang.destroy', $h->id) }}" method="POST" style="display:inline-block;">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger btn-sm" onclick="return confirm('Hapus data hutang ini?')">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Daftar Kasir')

@section('content')
<div class="container my-4">
    <h2 class="text-center mb-4">Daftar Kasir</h2>

    <div class="card shadow p-4">
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Nomor HP</th>
                    <th>Google ID</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($kasirs as $kasir)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $kasir->user->name }}</td>
                    <td>{{ $kasir->user->email }}</td>
                    <td>{{ $kasir->user->nomor_hp }}</td>
                    <td>{{ $kasir->google_id }}</td>
                    <td>
                        <a href="{{ route('kasir.show', $kasir->id) }}" class="btn btn-info btn-sm">Detail</a>
                        <a href="{{ route('user.edit', $kasir->user->id) }}" class="btn btn-primary btn-sm">Edit</a>
                        <form action="{{ route('user.destroy', $kasir->user->id) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Yakin hapus kasir ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

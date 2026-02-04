@extends('layouts.app')

@section('title', 'Data Bunga')

@section('content')
<div class="container">
    <h2 class="mb-4">Data Bunga</h2>
    <a href="{{ route('bunga.create') }}" class="btn btn-primary mb-3">Tambah Bunga</a>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Hutang</th>
                <th>Jumlah Bunga</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bunga as $b)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>Hutang #{{ $b->id_hutang }}</td>
                <td>{{ $b->jumlah_bunga }}</td>
                <td>
                    <a href="{{ route('bunga.show', $b->id) }}" class="btn btn-info btn-sm">Detail</a>
                    <a href="{{ route('bunga.edit', $b->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('bunga.destroy', $b->id) }}" method="POST" style="display:inline-block;">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger btn-sm" onclick="return confirm('Hapus data bunga ini?')">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

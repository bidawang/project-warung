@extends('layouts.app')

@section('title', 'Daftar Target Pencapaian')

@section('content')
<div class="container">
    <h2 class="mb-4">Daftar Target Pencapaian</h2>

    <a href="{{ route('targetpencapaian.create') }}" class="btn btn-primary mb-3">Tambah Target</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Warung</th>
                <th>Periode</th>
                <th>Target</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($target as $t)
            <tr>
                <td>{{ $t->id }}</td>
                <td>{{ $t->warung->nama_warung ?? '-' }}</td>
                <td>{{ $t->periode_awal }} s/d {{ $t->periode_akhir }}</td>
                <td>Rp {{ number_format($t->target_pencapaian,0,',','.') }}</td>
                <td>{{ $t->status_pencapaian }}</td>
                <td>
                    <a href="{{ route('targetpencapaian.show', $t->id) }}" class="btn btn-info btn-sm">Detail</a>
                    <a href="{{ route('targetpencapaian.edit', $t->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('targetpencapaian.destroy', $t->id) }}" method="POST" style="display:inline;">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger btn-sm" onclick="return confirm('Hapus target ini?')">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

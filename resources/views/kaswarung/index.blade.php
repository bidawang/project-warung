@extends('layouts.app')

@section('title', 'Kas Warung')

@section('content')
<div class="container my-4">
    <h2 class="text-center mb-4">Kas Warung</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow p-4">
        <div class="d-flex justify-content-between mb-3">
            <h5>Daftar Kas Warung</h5>
            <a href="{{ route('kaswarung.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i> Tambah Kas
            </a>
        </div>

        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Warung</th>
                    <th>Jenis Kas</th>
                    <th>Keterangan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($kasWarung as $kas)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $kas->warung->nama_warung ?? '-' }}</td>
                    <td>{{ $kas->jenis_kas }}</td>
                    <td>{{ $kas->keterangan ?? '-' }}</td>
                    <td>
                        <a href="{{ route('kaswarung.edit', $kas->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('kaswarung.destroy', $kas->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus kas ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center">Belum ada data kas warung.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

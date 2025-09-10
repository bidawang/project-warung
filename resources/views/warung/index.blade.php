@extends('layouts.app')

@section('title', 'Manajemen Warung')

@section('content')
<div class="container my-4">
    <h2 class="text-center mb-4">Manajemen Warung</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow p-4">
        <div class="d-flex justify-content-between mb-3">
            <h5>Daftar Warung</h5>
            <a href="{{ route('warung.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i> Tambah Warung
            </a>
        </div>

        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Nama Warung</th>
                    <th>Pemilik</th>
                    <th>Area</th>
                    <th>Modal</th>
                    <th>Keterangan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($warungs as $warung)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $warung->nama_warung }}</td>
                    <td>{{ $warung->user->name ?? '-' }}</td>
                    <td>{{ $warung->area->area ?? '-' }}</td>
                    <td>Rp {{ number_format($warung->modal, 0, ',', '.') }}</td>
                    <td>{{ $warung->keterangan ?? '-' }}</td>
                    <td>
                        <a href="{{ route('warung.edit', $warung->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('warung.destroy', $warung->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus warung ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center">Belum ada data warung.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

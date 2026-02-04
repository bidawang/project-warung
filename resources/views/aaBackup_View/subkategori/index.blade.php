@extends('layouts.app')

@section('title', 'Manajemen Subkategori')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-12">
            <h1 class="text-center my-4">Manajemen Subkategori</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
            <div class="card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title">Daftar Subkategori</h5>
                    <a href="{{ route('subkategori.create') }}" class="btn btn-primary rounded-pill d-flex align-items-center">
                        <i class="fas fa-plus me-2"></i> Tambah Subkategori
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Nama Subkategori</th>
                                <th scope="col">Kategori Induk</th>
                                <th scope="col">Keterangan</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($subkategoris as $subkategori)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $subkategori->sub_kategori }}</td>
                                <td>{{ $subkategori->kategori->kategori ?? '-' }}</td>
                                <td>{{ $subkategori->keterangan ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('subkategori.edit', $subkategori->id) }}" class="btn btn-sm btn-warning me-2" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('subkategori.destroy', $subkategori->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus subkategori ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">Belum ada data subkategori.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
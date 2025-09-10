@extends('layouts.app')

@section('title', 'Manajemen Aturan Tenggat')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-12">
            <h1 class="text-center my-4">Manajemen Aturan Tenggat</h1>
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
                    <h5 class="card-title">Daftar Aturan Tenggat</h5>
                    <a href="{{ route('aturan_tenggat.create') }}" class="btn btn-primary rounded-pill d-flex align-items-center">
                        <i class="fas fa-plus me-2"></i> Tambah Aturan Tenggat
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Area</th>
                                <th scope="col">Tanggal Awal</th>
                                <th scope="col">Tanggal Akhir</th>
                                <th scope="col">Jatuh Tempo (Hari)</th>
                                <th scope="col">Jatuh Tempo (Bulan)</th>
                                <th scope="col">Bunga</th>
                                <th scope="col">Keterangan</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($aturanTenggats as $aturanTenggat)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $aturanTenggat->area->area ?? '-' }}</td>
                                <td>{{ \Carbon\Carbon::parse($aturanTenggat->tanggal_awal)->format('d F Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($aturanTenggat->tanggal_akhir)->format('d F Y') }}</td>
                                <td>{{ $aturanTenggat->jatuh_tempo_hari }}</td>
                                <td>{{ $aturanTenggat->jatuh_tempo_bulan }}</td>
                                <td>{{ $aturanTenggat->bunga }}%</td>
                                <td>{{ $aturanTenggat->keterangan ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('aturan_tenggat.edit', $aturanTenggat->id) }}" class="btn btn-sm btn-warning me-2" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('aturan_tenggat.destroy', $aturanTenggat->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus aturan tenggat ini?');">
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
                                <td colspan="9" class="text-center">Belum ada data aturan tenggat.</td>
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
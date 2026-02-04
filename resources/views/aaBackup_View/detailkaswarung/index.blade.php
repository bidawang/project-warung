@extends('layouts.app')

@section('title', 'Detail Kas Warung')

@section('content')
<div class="container my-4">
    <h2 class="text-center mb-4">Detail Kas Warung</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow p-4">
        <div class="d-flex justify-content-between mb-3">
            <h5>Daftar Detail Kas</h5>
            <a href="{{ route('detailkaswarung.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i> Tambah Detail
            </a>
        </div>

        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Warung</th>
                    <th>Jenis Kas</th>
                    <th>Pecahan</th>
                    <th>Jumlah</th>
                    <th>Keterangan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($detailKas as $detail)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $detail->kasWarung->warung->nama_warung ?? '-' }}</td>
                    <td>{{ $detail->kasWarung->jenis_kas ?? '-' }}</td>
                    <td>Rp {{ number_format($detail->pecahan, 0, ',', '.') }}</td>
                    <td>{{ $detail->jumlah }}</td>
                    <td>{{ $detail->keterangan ?? '-' }}</td>
                    <td>
                        <a href="{{ route('detailkaswarung.edit', $detail->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('detailkaswarung.destroy', $detail->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus detail kas ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center">Belum ada detail kas warung.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

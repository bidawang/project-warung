@extends('layouts.app')

@section('title', 'Data Mutasi Barang')

@section('content')
<div class="container">
    <h2 class="mb-4">Data Mutasi Barang</h2>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('mutasibarang.create') }}" class="btn btn-primary mb-3">Tambah Mutasi</a>

    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Barang</th>
                <th>Warung Asal</th>
                <th>Warung Tujuan</th>
                <th>Jumlah</th>
                <th>Status</th>
                <th>Keterangan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($mutasi as $row)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $row->stokWarung->barang->nama ?? '-' }}</td>
                <td>{{ $row->warungAsal->nama ?? '-' }}</td>
                <td>{{ $row->warungTujuan->nama ?? '-' }}</td>
                <td>{{ $row->jumlah }}</td>
                <td>{{ ucfirst($row->status) }}</td>
                <td>{{ $row->keterangan }}</td>
                <td>
                    <a href="{{ route('mutasibarang.show', $row->id) }}" class="btn btn-info btn-sm">Detail</a>
                    <a href="{{ route('mutasibarang.edit', $row->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('mutasibarang.destroy', $row->id) }}" method="POST" class="d-inline"
                        onsubmit="return confirm('Yakin hapus data ini?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger btn-sm">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">Belum ada data</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
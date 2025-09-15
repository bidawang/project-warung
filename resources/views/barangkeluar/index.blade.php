@extends('layouts.app')

@section('title', 'Daftar Barang Keluar')

@section('content')

<div class="container-fluid">
    

    {{-- Main Content --}}
    <div class="p-4 bg-light min-vh-100">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
            <h1 class="h2 mb-3 mb-md-0 text-gray-800">Daftar Barang Keluar</h1>
            <a href="{{ route('barangkeluar.create') }}" class="btn btn-primary">
                + Tambah Barang Keluar
            </a>
        </div>

        @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif

        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Stok Warung</th>
                                <th>Jumlah</th>
                                <th>Jenis</th>
                                <th>Total</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($barang_keluar as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->stokWarung->barang->nama_barang ?? 'Stok Tidak Ditemukan' }}</td>
                                <td>{{ $item->jumlah }}</td>
                                <td>{{ $item->jenis }}</td>
                                <td>{{ $item->transaksiBarangKeluar->jumlah ?? '-' }}</td>
                                <td>{{ $item->created_at?->format('d/m/Y') ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">Belum ada data barang keluar.</td>
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
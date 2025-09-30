@extends('layouts.app')

@section('title', 'Riwayat Rencana Belanja')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">Riwayat Rencana Belanja</h5>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <a href="{{ route('kasir.rencanabelanja.index') }}" class="btn btn-warning btn-sm text-dark">
                    <i class="fas fa-arrow-left"></i> Kembali ke Rencana Aktif
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Barang</th>
                            <th>Jumlah Awal</th>
                            <th>Jumlah Dibeli</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($historyRencanaBelanja as $rencana)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $rencana->barang->nama_barang ?? 'Barang Tidak Dikenal' }}</td>
                                <td>
                                    <span class="badge bg-secondary">
                                        {{ number_format($rencana->jumlah_awal, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-success">
                                        {{ number_format($rencana->jumlah_dibeli, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-primary">Selesai</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Belum ada riwayat belanja yang selesai.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Rencana Belanja Aktif')

@section('content')

<div class="container mt-4">
<div class="card shadow-sm">
<div class="card-header bg-warning text-dark">
<h5 class="mb-0">Rencana Belanja (Menunggu Pembelian)</h5>
</div>
<div class="card-body">
<div class="mb-3 d-flex justify-content-between align-items-center">
{{-- Tombol untuk buat rencana belanja baru (perlu fungsi create/store baru) --}}
<a href="{{ route('kasir.rencanabelanja.create') }}" class="btn btn-success btn-sm">
<i class="fas fa-plus"></i> Buat Rencana Baru
</a>
<a href="{{ route('kasir.rencanabelanja.history') }}" class="btn btn-info btn-sm text-white">
<i class="fas fa-history"></i> Lihat Riwayat Belanja
</a>
</div>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Barang</th>
                        <th>Jumlah Rencana Awal</th>
                        <th>Jumlah Sudah Dibeli</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rencanaBelanjaAktif as $rencana)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $rencana->barang->nama_barang ?? 'Barang Tidak Dikenal' }}</td>
                            <td>
                                <span class="badge bg-secondary">
                                    {{ number_format($rencana->jumlah_awal, 0, ',', '.') }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-info">
                                    {{ number_format($rencana->jumlah_dibeli ?? 0, 0, ',', '.') }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-danger">Pending</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada rencana belanja yang aktif saat ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

</div>
@endsection

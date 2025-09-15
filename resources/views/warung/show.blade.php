@extends('layouts.app')

@section('title', 'Detail Warung')

@section('content')
<div class="container my-4">
    @if (Auth::check() && Auth::user()->role === 'admin')

    <h2 class="text-center mb-4">Detail Warung</h2>
    <div class="card shadow p-4">
        <div class="row">
            <div class="col-md-6">
                <h3 class="card-title">{{ $warung->nama_warung }}</h3>
                <p class="text-muted">Pemilik: {{ $warung->user->name ?? '-' }}</p>
                <p class="text-muted">Area: {{ $warung->area->area ?? '-' }}</p>

                <hr>

                <div class="d-flex align-items-center mb-3">
                    <h5 class="me-2">Modal:</h5>
                    <h4 class="text-success">Rp {{ number_format($warung->modal, 0, ',', '.') }}</h4>
                </div>

                <div class="mb-4">
                    <h5>Keterangan:</h5>
                    <p class="card-text">{{ $warung->keterangan ?? '-' }}</p>
                </div>
            </div>
            <div class="col-md-6 d-flex align-items-center justify-content-center">
                <i class="fas fa-store fa-9x text-muted"></i>
            </div>
        </div>
        @endif
        <hr class="my-4">

        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0">Daftar Barang</h4>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Nama Barang</th>
                                <th>Stok Tersedia</th>
                                <th>Harga Satuan (setelah markup)</th>
                                <th>Harga Jual (range laba)</th>
                                <th>Kuantitas</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($warung->stokWarung as $stok)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $stok->barang->nama_barang ?? 'Barang tidak ditemukan' }}</td>
                                <td>{{ $stok->stok_saat_ini ?? '-' }}</td>
                                <td>Rp {{ number_format($stok->harga_satuan, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($stok->harga_jual, 0, ',', '.') }}</td>
                                <td>
                                    <ul class="list-unstyled mb-2">
                                        @forelse($stok->kuantitas as $kuantitas)
                                        <li>{{ $kuantitas->jumlah }} unit: Rp {{ number_format($kuantitas->harga_jual, 0, ',', '.') }}</li>
                                        @empty
                                        <li>-</li>
                                        @endforelse
                                    </ul>
                                    <a href="{{ route('kuantitas.create', ['id_stok_warung' => $stok->id]) }}" class="btn btn-sm btn-outline-primary">
                                        + Tambah Kuantitas
                                    </a>
                                </td>
                                <td>{{ $stok->keterangan ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">Belum ada barang di warung ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @if (Auth::check() && Auth::user()->role === 'admin')
    <div class="mt-3">
        <a href="{{ route('warung.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Warung
        </a>
    </div>
    @endif

</div>
@endsection
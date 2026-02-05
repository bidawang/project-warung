@extends('layouts.app')

@section('title', 'Manajemen Hutang Pelanggan')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="fw-bold text-dark">Data Hutang</h2>
            <p class="text-muted">Kelola dan pantau tagihan pelanggan dengan mudah.</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 15px;">
        <div class="card-body p-4">

            <form method="GET" class="row g-3 mb-4 align-items-center">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" name="q" value="{{ request('q') }}"
                               class="form-control border-start-0 ps-0"
                               placeholder="Cari nama pelanggan...">
                    </div>
                </div>

                <div class="col-md-8 d-flex justify-content-md-end">
                    <div class="btn-group shadow-sm" role="group" style="border-radius: 10px; overflow: hidden;">
                        <a href="{{ route('kasir.hutang.index') }}"
                           class="btn btn-white border {{ request('status') == null ? 'active bg-light fw-bold' : '' }}">
                           Semua
                        </a>
                        <a href="{{ route('kasir.hutang.index', ['status' => 'belum_lunas']) }}"
                           class="btn btn-white border {{ request('status') == 'belum_lunas' ? 'active bg-danger text-white' : '' }}">
                           Belum Lunas
                        </a>
                        <a href="{{ route('kasir.hutang.index', ['status' => 'lunas']) }}"
                           class="btn btn-white border {{ request('status') == 'lunas' ? 'active bg-success text-white' : '' }}">
                           Lunas
                        </a>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3 text-uppercase fs-7 text-muted">#</th>
                            <th class="text-uppercase fs-7 text-muted">Pelanggan</th>
                            <th class="text-uppercase fs-7 text-muted">Jumlah Hutang</th>
                            <th class="text-uppercase fs-7 text-muted">Jatuh Tempo</th>
                            <th class="text-uppercase fs-7 text-muted">Status</th>
                            <th class="text-center text-uppercase fs-7 text-muted">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($hutangList as $hutang)
                            <tr class="border-bottom">
                                <td class="ps-3 fw-medium text-muted">
                                    {{ $loop->iteration + ($hutangList->firstItem() - 1) }}
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-3 bg-soft-primary text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px; background-color: #e7f1ff;">
                                            {{ strtoupper(substr($hutang->user->name ?? 'P', 0, 1)) }}
                                        </div>
                                        <span class="fw-bold text-dark">{{ $hutang->user->name ?? '-' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-bold text-dark">
                                        Rp {{ number_format($hutang->jumlah_sisa_hutang, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="text-dark">{{ \Carbon\Carbon::parse($hutang->tenggat)->format('d M Y') }}</span>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($hutang->tenggat)->diffForHumans() }}</small>
                                    </div>
                                </td>
                                <td>
                                    @if ($hutang->status == 'belum_lunas')
                                        <span class="badge rounded-pill px-3 py-2 bg-soft-danger text-danger" style="background-color: #fce8e8;">Belum Lunas</span>
                                    @elseif($hutang->status == 'lewat_tenggat')
                                        <span class="badge rounded-pill px-3 py-2 bg-soft-warning text-warning" style="background-color: #fff4e5;">Lewat Tenggat</span>
                                    @else
                                        <span class="badge rounded-pill px-3 py-2 bg-soft-success text-success" style="background-color: #e7f7ed;">Lunas</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ url('/kasir/hutang/detail/' . $hutang->id) }}"
                                       class="btn btn-sm btn-outline-primary px-3 rounded-pill">
                                       Lihat Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <img src="https://illustrations.popsy.co/gray/box.svg" alt="Empty" style="width: 120px;" class="mb-3">
                                    <p class="text-muted">Tidak ada data hutang ditemukan.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4 d-flex justify-content-between align-items-center">
                <p class="text-muted small mb-0">
                    Menampilkan {{ $hutangList->firstItem() }} sampai {{ $hutangList->lastItem() }} dari {{ $hutangList->total() }} data
                </p>
                <div>
                    {{ $hutangList->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Custom subtle colors for badges */
    .bg-soft-danger { background-color: #f8d7da; color: #842029; }
    .bg-soft-success { background-color: #d1e7dd; color: #0f5132; }
    .bg-soft-warning { background-color: #fff3cd; color: #664d03; }

    .fs-7 { font-size: 0.8rem; letter-spacing: 0.05em; }
    .table thead th { border-top: none; }
    .card { transition: transform 0.2s ease; }
    .btn-white { background: #fff; }
    .btn-white.active { border-color: #0d6efd !important; }
</style>
@endsection

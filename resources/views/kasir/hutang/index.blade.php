@extends('layouts.app')

@section('title', 'Manajemen Hutang Pelanggan')

@section('content')
<div class="container-fluid py-3 py-md-4" style="background-color: #f8f9fc; min-height: 100vh;">

    {{-- HEADER --}}
    <div class="row align-items-center mb-4 g-3">
        <div class="col-12 col-md-6">
            <h2 class="fw-bold text-primary mb-1 fs-3">Manajemen Hutang</h2>
            <p class="text-muted small mb-0">Pantau dan kelola piutang pelanggan secara real-time.</p>
        </div>

        {{-- TOTAL PIUTANG CARD --}}
        <div class="col-12 col-md-6 text-md-end">
            <div class="d-inline-flex align-items-center bg-white p-3 rounded-4 shadow-sm border-start border-primary border-4 w-100 w-md-auto">
                <div class="me-3 bg-primary-subtle p-3 rounded-circle d-none d-sm-block">
                    <i class="fas fa-hand-holding-usd text-primary fs-4"></i>
                </div>
                <div class="text-start">
                    <small class="text-muted fw-bold text-uppercase d-block" style="font-size: 0.65rem; letter-spacing: 1px;">
                        Total Piutang Toko
                    </small>
                    <h3 class="fw-bold mb-0 text-dark">
                        Rp {{ number_format($hutangList->sum('total_sisa_hutang'), 0, ',', '.') }}
                    </h3>
                </div>
            </div>
        </div>
    </div>

    {{-- FILTER & SEARCH --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="" class="row g-2">
                <div class="col-12 col-md-5">
                    <div class="input-group bg-light rounded-pill px-3 border shadow-none">
                        <span class="input-group-text bg-transparent border-0 ps-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" name="q" value="{{ request('q') }}"
                               class="form-control bg-transparent border-0 small"
                               placeholder="Cari nama pelanggan...">
                    </div>
                </div>

                <div class="col-12 col-md-7 d-flex justify-content-md-end overflow-auto py-1" style="white-space: nowrap;">
                    <div class="btn-group p-1 bg-light rounded-pill border">
                        <a href="{{ route('kasir.hutang.index') }}"
                           class="btn btn-sm rounded-pill px-3 {{ request('status') == null ? 'btn-primary' : 'btn-light border-0' }}">
                            Semua
                        </a>
                        <a href="{{ route('kasir.hutang.index', ['status' => 'belum_lunas']) }}"
                           class="btn btn-sm rounded-pill px-3 {{ request('status') == 'belum_lunas' ? 'btn-danger' : 'btn-light border-0' }}">
                            Belum Lunas
                        </a>
                        <a href="{{ route('kasir.hutang.index', ['status' => 'lunas']) }}"
                           class="btn btn-sm rounded-pill px-3 {{ request('status') == 'lunas' ? 'btn-success' : 'btn-light border-0' }}">
                            Lunas
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- DATA LIST --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        
        {{-- DESKTOP VIEW (Table) --}}
        <div class="table-responsive d-none d-md-block">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 small text-muted uppercase">Pelanggan</th>
                        <th class="small text-muted uppercase">Total Hutang</th>
                        <th class="small text-muted uppercase">Sisa Hutang</th>
                        <th class="small text-muted uppercase text-center">Status</th>
                        <th class="text-end pe-4 small text-muted uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($hutangList as $hutang)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                <div class="avatar-circle me-3 bg-primary-subtle fw-bold text-primary">
                                    {{ strtoupper(substr($hutang->user->name ?? 'P', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">{{ $hutang->user->name ?? 'Pelanggan Umum' }}</div>
                                    <small class="text-muted">ID: #{{ $hutang->id_user }}</small>
                                </div>
                            </div>
                        </td>
                        <td class="text-muted">Rp {{ number_format($hutang->total_hutang, 0, ',', '.') }}</td>
                        <td class="fw-bold text-dark">Rp {{ number_format($hutang->total_sisa_hutang, 0, ',', '.') }}</td>
                        <td class="text-center">
                            @if ($hutang->total_sisa_hutang > 0)
                                <span class="badge badge-soft-danger rounded-pill px-3 py-2">Belum Lunas</span>
                            @else
                                <span class="badge badge-soft-success rounded-pill px-3 py-2">Lunas</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('kasir.hutang.detail', $hutang->id_user) }}"
                               class="btn btn-outline-primary btn-sm rounded-pill px-4 fw-bold">
                                Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-5">Tidak ada data hutang.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- MOBILE VIEW (Cards) --}}
        <div class="d-md-none">
            @forelse ($hutangList as $hutang)
            <div class="p-3 border-bottom">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="d-flex align-items-center">
                        <div class="avatar-circle-sm me-2 bg-primary-subtle fw-bold text-primary">
                            {{ strtoupper(substr($hutang->user->name ?? 'P', 0, 1)) }}
                        </div>
                        <div>
                            <div class="fw-bold text-dark mb-0">{{ $hutang->user->name ?? 'Pelanggan' }}</div>
                            <small class="text-muted" style="font-size: 0.7rem;">ID: #{{ $hutang->id_user }}</small>
                        </div>
                    </div>
                    @if ($hutang->total_sisa_hutang > 0)
                        <span class="badge badge-soft-danger rounded-pill px-2 py-1 small">Belum Lunas</span>
                    @else
                        <span class="badge badge-soft-success rounded-pill px-2 py-1 small">Lunas</span>
                    @endif
                </div>
                
                <div class="bg-light rounded-3 p-2 mb-2">
                    <div class="d-flex justify-content-between mb-1">
                        <small class="text-muted">Total Hutang:</small>
                        <small class="text-muted">Rp {{ number_format($hutang->total_hutang, 0, ',', '.') }}</small>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="small fw-bold text-dark">Sisa Piutang:</span>
                        <span class="fw-bold text-danger">Rp {{ number_format($hutang->total_sisa_hutang, 0, ',', '.') }}</span>
                    </div>
                </div>

                <a href="{{ route('kasir.hutang.detail', $hutang->id_user) }}"
                   class="btn btn-primary w-100 rounded-pill btn-sm fw-bold">
                    Lihat Rincian & Bayar
                </a>
            </div>
            @empty
            <div class="text-center py-5">
                <p class="text-muted">Tidak ada data hutang.</p>
            </div>
            @endforelse
        </div>

        {{-- PAGINATION --}}
        <div class="card-footer bg-white border-0 py-3">
            <div class="d-md-flex justify-content-between align-items-center text-center text-md-start">
                <small class="text-muted d-block mb-3 mb-md-0">
                    Menampilkan <strong>{{ $hutangList->firstItem() ?? 0 }}</strong> - <strong>{{ $hutangList->lastItem() ?? 0 }}</strong> dari {{ $hutangList->total() }} pelanggan
                </small>
                <div class="d-inline-block">
                    {{ $hutangList->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Avatar desktop */
.avatar-circle {
    width: 45px; height: 45px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center; font-size: 1.1rem;
}
/* Avatar mobile */
.avatar-circle-sm {
    width: 35px; height: 35px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center; font-size: 0.9rem;
}

.uppercase { text-transform: uppercase; letter-spacing: .8px; font-weight: 700; font-size: 0.65rem !important; }

/* Custom Badge Colors */
.badge-soft-danger { background: #fee2e2; color: #b91c1c; }
.badge-soft-success { background: #dcfce7; color: #15803d; }
.bg-primary-subtle { background: #e0e7ff; color: #4338ca; }

/* Menyesuaikan pagination di mobile */
.pagination { margin-bottom: 0; flex-wrap: wrap; justify-content: center; }

/* Smooth transition untuk hover */
.table-hover tbody tr:hover { background-color: #f8fafc; transition: 0.2s; }
</style>
@endsection
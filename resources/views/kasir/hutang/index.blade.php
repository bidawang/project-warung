@extends('layouts.app')

@section('title', 'Manajemen Hutang Pelanggan')

@section('content')
<div class="container py-4" style="background-color: #f8f9fc; min-height: 100vh;">

    {{-- HEADER --}}
    <div class="row align-items-end mb-4">
        <div class="col-md-6">
            <h2 class="fw-bold text-primary mb-1">Manajemen Hutang</h2>
            <p class="text-muted">Rekap hutang pelanggan.</p>
        </div>

        {{-- TOTAL PIUTANG --}}
        <div class="col-md-6 text-md-end">
            <div class="d-inline-block bg-white p-3 rounded-4 shadow-sm border-start border-primary border-4">
                <small class="text-muted fw-bold text-uppercase d-block" style="font-size: 0.7rem;">
                    Total Piutang
                </small>
                <h3 class="fw-black mb-0">
                    Rp {{ number_format($hutangList->sum('total_sisa_hutang'), 0, ',', '.') }}
                </h3>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">

        {{-- FILTER --}}
        <div class="card-header bg-white border-0 py-3">
            <form method="GET" class="row g-3 align-items-center">
                <div class="col-md-5">
                    <div class="input-group bg-light rounded-pill">
                        <span class="input-group-text bg-transparent border-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" name="q" value="{{ request('q') }}"
                               class="form-control bg-transparent border-0"
                               placeholder="Cari nama pelanggan...">
                    </div>
                </div>

                <div class="col-md-7 d-flex justify-content-md-end">
                    <div class="btn-group bg-light p-1 rounded-pill border">
                        <a href="{{ route('kasir.hutang.index') }}"
                           class="btn btn-sm rounded-pill px-3 {{ request('status') == null ? 'btn-primary' : 'btn-light' }}">
                            Semua
                        </a>
                        <a href="{{ route('kasir.hutang.index', ['status' => 'belum_lunas']) }}"
                           class="btn btn-sm rounded-pill px-3 {{ request('status') == 'belum_lunas' ? 'btn-danger' : 'btn-light' }}">
                            Belum Lunas
                        </a>
                        <a href="{{ route('kasir.hutang.index', ['status' => 'lunas']) }}"
                           class="btn btn-sm rounded-pill px-3 {{ request('status') == 'lunas' ? 'btn-success' : 'btn-light' }}">
                            Lunas
                        </a>
                    </div>
                </div>
            </form>
        </div>

        {{-- TABLE --}}
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 small text-muted uppercase">Pelanggan</th>
                            <th class="small text-muted uppercase">Total Hutang</th>
                            <th class="small text-muted uppercase">Sisa Hutang</th>
                            <th class="small text-muted uppercase">Status</th>
                            <th class="text-end pe-4 small text-muted uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($hutangList as $hutang)
                        <tr>
                            {{-- Pelanggan --}}
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle me-3 bg-primary-subtle fw-bold">
                                        {{ strtoupper(substr($hutang->user->name ?? 'P', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $hutang->user->name ?? 'Pelanggan Umum' }}</div>
                                        <small class="text-muted">ID: {{ $hutang->id_user }}</small>
                                    </div>
                                </div>
                            </td>

                            {{-- Total Hutang --}}
                            <td>
                                Rp {{ number_format($hutang->total_hutang, 0, ',', '.') }}
                            </td>

                            {{-- Sisa Hutang --}}
                            <td class="fw-bold">
                                Rp {{ number_format($hutang->total_sisa_hutang, 0, ',', '.') }}
                            </td>

                            {{-- Status --}}
                            <td>
                                @if ($hutang->total_sisa_hutang > 0)
                                    <span class="badge badge-soft-danger rounded-pill px-3">
                                        Belum Lunas
                                    </span>
                                @else
                                    <span class="badge badge-soft-success rounded-pill px-3">
                                        Lunas
                                    </span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="text-end pe-4">
                                <a href="{{ route('kasir.hutang.detail', $hutang->id_user) }}"
                                   class="btn btn-primary btn-sm rounded-pill px-4">
                                    Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                Tidak ada data hutang pelanggan
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- PAGINATION --}}
        <div class="card-footer bg-white border-0">
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Menampilkan {{ $hutangList->firstItem() ?? 0 }} - {{ $hutangList->lastItem() ?? 0 }}
                    dari {{ $hutangList->total() }} pelanggan
                </small>
                {{ $hutangList->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}
.uppercase { text-transform: uppercase; letter-spacing: .5px; }
.fw-black { font-weight: 800; }

.badge-soft-danger { background:#fff5f5; color:#e53e3e; }
.badge-soft-success { background:#f0fff4; color:#38a169; }
.bg-primary-subtle { background:#eef2ff; color:#4e73df; }
</style>
@endsection

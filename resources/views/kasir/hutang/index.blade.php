@extends('layouts.app')

@section('title', 'Manajemen Hutang Pelanggan')

@section('content')
<div class="container py-4" style="background-color: #f8f9fc; min-height: 100vh;">

    {{-- BAGIAN HEADER & RINGKASAN KUMULATIF --}}
    <div class="row align-items-end mb-4">
        <div class="col-md-6">
            <h2 class="fw-bold text-primary mb-1">Manajemen Hutang</h2>
            <p class="text-muted">Pantau piutang berjalan dari seluruh pelanggan Anda.</p>
        </div>

        {{-- Card Total Hutang Global --}}
        <div class="col-md-6 text-md-end">
            <div class="d-inline-block bg-white p-3 rounded-4 shadow-sm border-start border-primary border-4">
                <small class="text-muted fw-bold text-uppercase d-block" style="font-size: 0.7rem;">Total Piutang Seluruh Pelanggan</small>
                {{-- Gunakan variabel total dari controller, di sini saya asumsikan menjumlahkan dari koleksi yang ada --}}
                <h3 class="fw-black text-dark mb-0">
                    Rp {{ number_format($hutangList->sum('jumlah_sisa_hutang'), 0, ',', '.') }}
                </h3>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        {{-- BAGIAN FILTER --}}
        <div class="card-header bg-white border-0 py-3">
            <form method="GET" class="row g-3 align-items-center">
                <div class="col-md-5">
                    <div class="input-group input-group-merge shadow-none">
                        <span class="input-group-text bg-light border-0 px-3">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" name="q" value="{{ request('q') }}"
                               class="form-control bg-light border-0 ps-0 rounded-end"
                               placeholder="Cari nama pelanggan atau ID...">
                    </div>
                </div>

                <div class="col-md-7 d-flex justify-content-md-end gap-2">
                    <div class="btn-group bg-light p-1 rounded-pill border" role="group">
                        <a href="{{ route('kasir.hutang.index') }}"
                           class="btn btn-sm rounded-pill px-3 {{ request('status') == null ? 'btn-primary shadow-sm' : 'btn-light border-0' }}">
                            Semua
                        </a>
                        <a href="{{ route('kasir.hutang.index', ['status' => 'belum_lunas']) }}"
                           class="btn btn-sm rounded-pill px-3 {{ request('status') == 'belum_lunas' ? 'btn-danger shadow-sm' : 'btn-light border-0' }}">
                            Belum Lunas
                        </a>
                        <a href="{{ route('kasir.hutang.index', ['status' => 'lunas']) }}"
                           class="btn btn-sm rounded-pill px-3 {{ request('status') == 'lunas' ? 'btn-success shadow-sm' : 'btn-light border-0' }}">
                            Lunas
                        </a>
                    </div>
                </div>
            </form>
        </div>

        {{-- TABEL DATA PER PELANGGAN --}}
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 text-muted fw-600 small uppercase">Pelanggan</th>
                            <th class="text-muted fw-600 small uppercase">Saldo Hutang</th>
                            <th class="text-muted fw-600 small uppercase">Status Terakhir</th>
                            <th class="text-muted fw-600 small uppercase text-center">Jatuh Tempo</th>
                            <th class="text-end pe-4 text-muted fw-600 small uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($hutangList as $hutang)
                        <tr>
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle me-3 bg-primary-subtle text-primary fw-bold">
                                        {{ strtoupper(substr($hutang->user->name ?? 'P', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $hutang->user->name ?? 'Pelanggan Umum' }}</div>
                                        <small class="text-muted">ID: #TRX-{{ $hutang->id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">Rp {{ number_format($hutang->jumlah_sisa_hutang, 0, ',', '.') }}</div>
                                <small class="text-muted">Total awal: Rp {{ number_format($hutang->jumlah_hutang ?? 0, 0, ',', '.') }}</small>
                            </td>
                            <td>
                                @if ($hutang->status == 'belum_lunas')
                                    <span class="badge badge-soft-danger px-3 py-2 rounded-pill border border-danger-subtle">
                                        <i class="fas fa-exclamation-circle me-1"></i> Belum Lunas
                                    </span>
                                @elseif($hutang->status == 'lewat_tenggat')
                                    <span class="badge badge-soft-warning px-3 py-2 rounded-pill border border-warning-subtle">
                                        <i class="fas fa-clock me-1"></i> Terlambat
                                    </span>
                                @else
                                    <span class="badge badge-soft-success px-3 py-2 rounded-pill border border-success-subtle">
                                        <i class="fas fa-check-circle me-1"></i> Lunas
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="fw-medium {{ \Carbon\Carbon::parse($hutang->tenggat)->isPast() && $hutang->status != 'lunas' ? 'text-danger fw-bold' : 'text-dark' }}">
                                    {{ \Carbon\Carbon::parse($hutang->tenggat)->translatedFormat('d M Y') }}
                                </div>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($hutang->tenggat)->diffForHumans() }}</small>
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ url('/kasir/hutang/detail/' . $hutang->id) }}"
                                   class="btn btn-primary btn-sm rounded-pill px-4 shadow-sm hover-scale">
                                    <i class="fas fa-file-invoice-dollar me-1"></i> Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="py-4">
                                    <i class="fas fa-folder-open fa-3x text-light mb-3"></i>
                                    <h5 class="text-muted">Tidak ada rekaman hutang pelanggan.</h5>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer bg-white border-0 py-3">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="text-muted small mb-0">Menampilkan {{ $hutangList->firstItem() ?? 0 }} - {{ $hutangList->lastItem() ?? 0 }} dari {{ $hutangList->total() }} pelanggan</p>
                </div>
                <div class="col-md-6 d-flex justify-content-md-end mt-2 mt-md-0">
                    {{ $hutangList->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .fw-black { font-weight: 800; }
    .fw-600 { font-weight: 600; }
    .uppercase { text-transform: uppercase; letter-spacing: 0.5px; }

    .avatar-circle {
        width: 42px;
        height: 42px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-size: 1.1rem;
    }

    .bg-primary-subtle { background-color: #eef2ff !important; color: #4e73df !important; }

    /* Soft Badges */
    .badge-soft-danger { background-color: #fff5f5; color: #e53e3e; }
    .badge-soft-success { background-color: #f0fff4; color: #38a169; }
    .badge-soft-warning { background-color: #fffaf0; color: #dd6b20; }

    .hover-scale { transition: all 0.2s ease; }
    .hover-scale:hover { transform: scale(1.05); }

    .table thead th { font-size: 0.75rem; border: none; }
    .table tbody tr { border-bottom: 1px solid #f0f2f5; transition: background 0.2s; }
    .table tbody tr:hover { background-color: #fbfcfe; }
</style>
@endsection

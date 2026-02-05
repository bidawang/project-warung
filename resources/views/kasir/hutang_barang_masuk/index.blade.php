@extends('layouts.app')

@section('title', 'Data Hutang Barang Masuk')

@section('content')
<div class="container py-4">
    {{-- Header Section --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">Manajemen Hutang</h3>
            <p class="text-muted small mb-0">Kelola kewajiban pembayaran kepada supplier barang masuk.</p>
        </div>
        <div class="bg-warning-subtle p-3 rounded-4 border border-warning-alpha">
            <small class="text-warning-emphasis fw-bold d-block">Total Hutang Aktif</small>
            {{-- Asumsi Anda mengirimkan variabel $totalHutangAktif dari Controller --}}
            <h4 class="fw-black text-warning mb-0">Rp {{ number_format($hutangList->where('status', '!=', 'lunas')->sum('total'), 0, ',', '.') }}</h4>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-header bg-white border-bottom py-3">
            <div class="row g-3 align-items-center">
                {{-- Filter Pencarian --}}
                <div class="col-md-5">
                    <form method="GET" class="input-group">
                        <input type="text" name="q" value="{{ request('q') }}"
                               class="form-control border-end-0 rounded-start-pill ps-3"
                               placeholder="Cari Supplier atau No. Faktur...">
                        <button class="btn btn-outline-secondary border-start-0 rounded-end-pill px-3" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>

                {{-- Filter Status --}}
                <div class="col-md-7 text-md-end">
                    <div class="btn-group rounded-pill p-1 bg-light border" role="group">
                        <a href="{{ url()->current() . '?' . http_build_query(request()->except(['status'])) }}"
                           class="btn btn-sm rounded-pill px-3 {{ request('status') == null ? 'btn-warning text-white shadow-sm' : 'btn-light border-0' }}">
                            Semua
                        </a>
                        <a href="{{ url()->current() . '?' . http_build_query(array_merge(request()->except(['status']), ['status' => 'belum lunas'])) }}"
                           class="btn btn-sm rounded-pill px-3 {{ request('status') == 'belum lunas' ? 'btn-danger text-white shadow-sm' : 'btn-light border-0' }}">
                            Belum Lunas
                        </a>
                        <a href="{{ url()->current() . '?' . http_build_query(array_merge(request()->except(['status']), ['status' => 'lunas'])) }}"
                           class="btn btn-sm rounded-pill px-3 {{ request('status') == 'lunas' ? 'btn-success text-white shadow-sm' : 'btn-light border-0' }}">
                            Lunas
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            {{-- Alert Messages --}}
            @if (session('success'))
                <div class="alert alert-success border-0 rounded-0 mb-0"><i class="fas fa-check-circle me-2"></i>{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger border-0 rounded-0 mb-0"><i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}</div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4" style="width: 50px;">No</th>
                            <th>Tanggal Masuk</th>
                            <th>Nominal Hutang</th>
                            <th>Keterangan</th>
                            <th class="text-center">Status</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($hutangList as $hutang)
                        <tr>
                            <td class="ps-4 text-muted">{{ $loop->iteration + ($hutangList->firstItem() - 1) }}</td>
                            <td>
                                <div class="fw-bold">{{ \Carbon\Carbon::parse($hutang->created_at)->translatedFormat('d F Y') }}</div>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($hutang->created_at)->diffForHumans() }}</small>
                            </td>
                            <td>
                                <span class="fw-bold text-dark">Rp {{ number_format($hutang->total, 0, ',', '.') }}</span>
                            </td>
                            <td>
                                <span class="text-muted small">{{ $hutang->keterangan ?? 'Pembelian Stok Barang' }}</span>
                            </td>
                            <td class="text-center">
                                @if(($hutang->status ?? 'belum lunas') == 'lunas')
                                    <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill border border-success-subtle">
                                        <i class="fas fa-check-circle me-1"></i> Lunas
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill border border-danger-subtle pulse-animation">
                                        <i class="fas fa-clock me-1"></i> Belum Lunas
                                    </span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                @if(($hutang->status ?? 'belum lunas') != 'lunas')
                                    <a href="{{ route('kasir.bayar.detail', ['id' => $hutang->id]) }}"
                                       class="btn btn-warning btn-sm text-white rounded-pill px-4 shadow-sm hover-up">
                                        Bayar Sekarang
                                    </a>
                                @else
                                    <button class="btn btn-light btn-sm rounded-pill px-4 border" disabled>
                                        Selesai
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="py-3">
                                    <i class="fas fa-file-invoice-dollar fa-3x text-light mb-3"></i>
                                    <p class="text-muted">Wah, tidak ada data hutang ditemukan.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer bg-white border-top py-3">
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">Menampilkan {{ $hutangList->firstItem() }} - {{ $hutangList->lastItem() }} dari {{ $hutangList->total() }} data</small>
                <div>
                    {{ $hutangList->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .fw-black { font-weight: 900; }
    .bg-warning-subtle { background-color: #fff9e6; }
    .border-warning-alpha { border-color: rgba(255, 193, 7, 0.3) !important; }

    .hover-up:hover {
        transform: translateY(-2px);
        transition: all 0.2s;
    }

    /* Efek Berkedip untuk Hutang Belum Lunas */
    .pulse-animation {
        animation: pulse-red 2s infinite;
    }

    @keyframes pulse-red {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }

    .table th {
        font-weight: 600;
        letter-spacing: 0.5px;
    }
</style>
@endsection

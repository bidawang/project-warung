@extends('layouts.app')

@section('title', 'Data Hutang Barang Masuk')

@section('content')
<div class="container py-4">
    {{-- Header Section --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">Manajemen Hutang</h3>
            <p class="text-muted small mb-0">Nota hutang per pengiriman stok barang.</p>
        </div>
        <div class="bg-warning-subtle p-3 rounded-4 border border-warning-alpha">
            <small class="text-warning-emphasis fw-bold d-block">Total Hutang Aktif</small>
            <h4 class="fw-black text-warning mb-0">
                Rp {{ number_format($hutangList->where('status', '!=', 'lunas')->sum('total'), 0, ',', '.') }}
            </h4>
        </div>
    </div>

    {{-- Filter Section --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-center">
                <div class="col-md-4">
                    <form method="GET" class="input-group">
                        <input type="text" name="q" value="{{ request('q') }}" class="form-control rounded-pill-start" placeholder="Cari ID Nota...">
                        <button class="btn btn-warning text-white rounded-pill-end" type="submit"><i class="fas fa-search"></i></button>
                    </form>
                </div>
                <div class="col-md-8 text-md-end">
                    <div class="btn-group p-1 bg-light border rounded-pill">
                        <a href="{{ request()->fullUrlWithQuery(['status' => null]) }}" class="btn btn-sm rounded-pill px-3 {{ request('status') == null ? 'btn-warning text-white shadow-sm' : 'btn-light border-0' }}">Semua</a>
                        <a href="{{ request()->fullUrlWithQuery(['status' => 'belum lunas']) }}" class="btn btn-sm rounded-pill px-3 {{ request('status') == 'belum lunas' ? 'btn-danger text-white shadow-sm' : 'btn-light border-0' }}">Belum Lunas</a>
                        <a href="{{ request()->fullUrlWithQuery(['status' => 'lunas']) }}" class="btn btn-sm rounded-pill px-3 {{ request('status') == 'lunas' ? 'btn-success text-white shadow-sm' : 'btn-light border-0' }}">Lunas</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Accordion List --}}
    <div class="accordion" id="accordionHutang">
        @forelse($hutangList as $hutang)
        <div class="accordion-item border-0 shadow-sm rounded-4 mb-3 overflow-hidden">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed bg-white py-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $hutang->id }}">
                    <div class="row w-100 align-items-center">
                        <div class="col-md-3">
                            <span class="d-block text-muted small">Nota ID</span>
                            <span class="fw-bold">#HTG-{{ str_pad($hutang->id, 5, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div class="col-md-3">
                            <span class="d-block text-muted small">Tanggal Nota</span>
                            <span class="fw-bold text-dark">{{ $hutang->created_at->translatedFormat('d F Y') }}</span>
                        </div>
                        <div class="col-md-3">
                            <span class="d-block text-muted small">Total Tagihan</span>
                            <span class="fw-bold text-warning">Rp {{ number_format($hutang->total, 0, ',', '.') }}</span>
                        </div>
                        <div class="col-md-3 text-end pe-4">
                            @if($hutang->status == 'lunas')
                                <span class="badge bg-success-subtle text-success rounded-pill px-3">Lunas</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger rounded-pill px-3 pulse-animation">Belum Lunas</span>
                            @endif
                        </div>
                    </div>
                </button>
            </h2>
            <div id="collapse{{ $hutang->id }}" class="accordion-collapse collapse" data-bs-parent="#accordionHutang">
                <div class="accordion-body bg-light-subtle">
                    <div class="table-responsive">
                        <table class="table table-sm table-borderless align-middle">
                            <thead class="text-muted small text-uppercase">
                                <tr class="border-bottom">
                                    <th>Barang</th>
                                    <th class="text-center">Jumlah</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($hutang->hutangBarangMasuk as $detail)
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $detail->barangMasuk->transaksiBarang->barang->nama_barang ?? 'Barang Tanpa Nama' }}</div>
                                        <small class="text-muted">Expired: {{ $detail->barangMasuk->tanggal_kadaluarsa ?? '-' }}</small>
                                    </td>
                                    <td class="text-center">{{ $detail->barangMasuk->jumlah }} unit</td>
                                    <td class="text-end fw-bold">Rp {{ number_format($detail->total, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="border-top">
                                <tr>
                                    <td colspan="2" class="text-end py-3 text-muted">Total Pembayaran Nota:</td>
                                    <td class="text-end py-3"><h5 class="fw-bold text-dark mb-0">Rp {{ number_format($hutang->total, 0, ',', '.') }}</h5></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-2">
                        @if($hutang->status != 'lunas')
                            <a href="{{ route('kasir.bayar.detail', ['id' => $hutang->id]) }}" class="btn btn-warning text-white rounded-pill px-4 shadow-sm">
                                <i class="fas fa-money-bill-wave me-2"></i>Bayar Nota Ini
                            </a>
                        @endif
                        <a href="#" class="btn btn-outline-secondary rounded-pill px-4">
                            <i class="fas fa-print me-2"></i>Cetak Nota
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-5 bg-white rounded-4 shadow-sm">
            <i class="fas fa-file-invoice-dollar fa-3x text-light mb-3"></i>
            <p class="text-muted">Tidak ada data hutang ditemukan.</p>
        </div>
        @endforelse

        <div class="mt-4">
            {{ $hutangList->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<style>
    .accordion-button:not(.collapsed) {
        background-color: #fffdf5;
        color: inherit;
        box-shadow: none;
    }
    .accordion-button::after {
        background-size: 1rem;
    }
    .rounded-pill-start { border-top-left-radius: 50px !important; border-bottom-left-radius: 50px !important; }
    .rounded-pill-end { border-top-right-radius: 50px !important; border-bottom-right-radius: 50px !important; }
    .fw-black { font-weight: 900; }
    .pulse-animation { animation: pulse-red 2s infinite; }
    @keyframes pulse-red {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
</style>
@endsection
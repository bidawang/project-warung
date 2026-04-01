@extends('layouts.app')

@section('title', 'Data Hutang Barang Masuk')

@section('content')

<div class="bs-scope">
<div class="container py-4">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">Manajemen Hutang</h3>
            <p class="text-muted small mb-0">Nota hutang per pengiriman stok barang.</p>
        </div>

        <div class="bg-warning-subtle p-3 rounded-4 border">
            <small class="text-warning-emphasis fw-bold d-block">Total Hutang Aktif</small>
            <h4 class="fw-black text-warning mb-0">
                Rp {{ number_format($totalHutang, 0, ',', '.') }}
            </h4>
        </div>
    </div>

    {{-- FILTER --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-center">
                <div class="col-md-4">
                    <form method="GET" class="input-group">
                        <input type="text" name="q" value="{{ request('q') }}"
                               class="form-control rounded-pill-start"
                               placeholder="Cari ID Nota...">

                        <button class="btn btn-warning text-white rounded-pill-end">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <h5 class="fw-bold mb-3">Histori Hutang</h5>

    {{-- ACCORDION --}}
    <div class="accordion" id="accordionHutang">

        @forelse($hutangList as $hutang)

        <div class="accordion-item border-0 shadow-sm rounded-4 mb-3 overflow-hidden">

            {{-- HEADER --}}
            <h2 class="accordion-header">
                <button class="accordion-button collapsed bg-white py-3"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#collapse{{ $hutang->id }}">

                    <div class="row w-100 align-items-center">

                        <div class="col-md-4">
                            <div class="text-muted small">Nota</div>
                            <div class="fw-bold">
                                #HTG-{{ str_pad($hutang->id, 5, '0', STR_PAD_LEFT) }}
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="text-muted small">Tanggal</div>
                            <div class="fw-bold">
                                {{ $hutang->created_at->translatedFormat('d M Y') }}
                            </div>
                        </div>

                        <div class="col-md-4 text-md-end">
                            <div class="text-muted small">Total</div>
                            <div class="fw-bold text-warning">
                                Rp {{ number_format($hutang->total, 0, ',', '.') }}
                            </div>
                        </div>

                    </div>
                </button>
            </h2>

            {{-- BODY --}}
            <div id="collapse{{ $hutang->id }}"
                 class="accordion-collapse collapse">

                <div class="accordion-body bg-light-subtle">

                    {{-- TABLE --}}
                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead class="small text-muted text-uppercase">
                                <tr>
                                    <th>Barang</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($hutang->hutangBarangMasuk as $detail)
                                <tr>
                                    <td>
                                        <div class="fw-bold">
                                            {{ $detail->barangMasuk->transaksiBarang->barang->nama_barang ?? '-' }}
                                        </div>
                                        <small class="text-muted">
                                            Exp: {{ $detail->barangMasuk->tanggal_kadaluarsa ?? '-' }}
                                        </small>
                                    </td>

                                    <td class="text-center">
                                        {{ $detail->barangMasuk->jumlah }}
                                    </td>

                                    <td class="text-end fw-bold">
                                        Rp {{ number_format($detail->total, 0, ',', '.') }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>

                            <tfoot>
                                <tr class="border-top">
                                    <td colspan="2" class="text-end small text-muted">
                                        Total
                                    </td>
                                    <td class="text-end fw-bold">
                                        Rp {{ number_format($hutang->total, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>

                        </table>
                    </div>

                    {{-- ACTION --}}
                    <div class="text-end mt-2">
                        <a href="#"
                           class="btn btn-outline-secondary rounded-pill px-4">
                            <i class="fas fa-print me-2"></i>Cetak
                        </a>
                    </div>

                </div>
            </div>
        </div>

        @empty
        <div class="text-center py-5 bg-white rounded-4 shadow-sm">
            <i class="fas fa-file-invoice-dollar fa-3x text-light mb-3"></i>
            <p class="text-muted">Tidak ada data hutang</p>
        </div>
        @endforelse

    </div>

    {{-- PAGINATION --}}
    <div class="mt-4">
        {{ $hutangList->withQueryString()->links('pagination::bootstrap-5') }}
    </div>

</div>
</div>

{{-- STYLE ISOLATION --}}
<style>
.bs-scope {
    font-family: inherit;
}

.bs-scope .accordion-button:not(.collapsed) {
    background-color: #fffdf5;
    box-shadow: none;
}

.bs-scope .rounded-pill-start {
    border-radius: 50px 0 0 50px !important;
}

.bs-scope .rounded-pill-end {
    border-radius: 0 50px 50px 0 !important;
}

.bs-scope .fw-black {
    font-weight: 900;
}
</style>

@endsection
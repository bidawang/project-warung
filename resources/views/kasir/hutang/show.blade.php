@extends('layouts.app')

@section('title', 'Riwayat Pembayaran Hutang')

@section('content')
<div class="container py-4" style="background-color:#f8f9fc; min-height:100vh;">

    {{-- HEADER --}}
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <h3 class="fw-bold text-primary mb-1">Riwayat Pembayaran</h3>
            <p class="text-muted mb-0">Nota Transaksi: <span class="fw-bold">#H-{{ $hutang->id }}</span></p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ url()->previous() }}" class="btn btn-light rounded-pill px-4 shadow-sm">
                ← Kembali
            </a>
        </div>
    </div>

    <div class="row">
        {{-- KARTU INFORMASI HUTANG --}}
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm rounded-4 mb-3">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0">Informasi Hutang</h5>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Status</span>
                        @if($hutang->status == 'lunas')
                            <span class="badge bg-success rounded-pill">Lunas</span>
                        @else
                            <span class="badge bg-danger rounded-pill">Belum Lunas</span>
                        @endif
                    </div>
                    <hr class="text-muted opacity-25">
                    <div class="mb-3">
                        <small class="text-muted d-block">Total Hutang Awal</small>
                        <span class="fw-bold fs-5">Rp {{ number_format($hutang->jumlah_hutang_awal, 0, ',', '.') }}</span>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">Sisa Hutang</small>
                        <span class="fw-bold fs-5 text-danger">Rp {{ number_format($hutang->jumlah_sisa_hutang, 0, ',', '.') }}</span>
                    </div>
                    <div class="mb-0">
                        <small class="text-muted d-block">Tanggal Jatuh Tempo</small>
                        <span class="fw-bold">{{ \Carbon\Carbon::parse($hutang->tenggat)->format('d F Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- TABEL RIWAYAT PEMBAYARAN --}}
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0">Log Pembayaran</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 small text-muted">TANGGAL BAYAR</th>
                                <th class="small text-muted text-center">METODE</th>
                                <th class="small text-muted text-end pe-4">NOMINAL</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Asumsi relasi di model Hutang adalah 'logs' atau 'pembayaran' --}}
                            @forelse ($hutang->logs as $log)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark">
                                        {{ $log->created_at->format('d M Y') }}
                                    </div>
                                    <small class="text-muted">{{ $log->created_at->format('H:i') }} WIB</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border rounded-pill px-3">Tunai</span>
                                </td>
                                <td class="text-end pe-4 fw-bold text-success">
                                    Rp {{ number_format($log->jumlah_pembayaran, 0, ',', '.') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-5 text-muted">
                                    Belum ada riwayat pembayaran untuk hutang ini.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        @if($hutang->logs->count() > 0)
                        <tfoot class="bg-light fw-bold">
                            <tr>
                                <td colspan="2" class="ps-4 text-end">Total Terbayar:</td>
                                <td class="text-end pe-4 text-primary">
                                    Rp {{ number_format($hutang->logs->sum('jumlah_pembayaran'), 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

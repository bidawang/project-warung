@extends('layouts.app')

@section('title', 'Manajemen Kas Warung')

@section('content')
<div class="container-fluid py-4" style="background-color: #f4f7f6; min-height: 100vh;">

    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h2 class="fw-bold text-dark mb-1">Manajemen Kas</h2>
            <p class="text-muted mb-0">Pantau arus kas <span class="badge bg-white text-primary shadow-sm">Cash</span> & <span class="badge bg-white text-info shadow-sm">Bank</span> secara real-time.</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <a href="{{ route('kasir.kas.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold">
                <i class="fas fa-plus-circle me-2"></i>Transaksi Manual
            </a>
        </div>
    </div>

    <div class="d-flex align-items-center mb-3">
        <div class="icon-box bg-warning text-white rounded-3 me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
            <i class="fas fa-money-bill-wave"></i>
        </div>
        <h6 class="text-uppercase text-dark fw-bold mb-0">Ringkasan Kas Tunai (Cash)</h6>
    </div>

    <div class="row g-3 mb-4">
        @php
            $cashStats = [
                ['label' => 'Pendapatan Cash', 'val' => $pendapatanCash, 'icon' => 'fa-arrow-trend-up', 'color' => 'success', 'bg' => '#e7f7ed'],
                ['label' => 'Pengeluaran Cash', 'val' => $pengeluaranCash, 'icon' => 'fa-arrow-trend-down', 'color' => 'danger', 'bg' => '#fce8e8'],
                ['label' => 'Saldo Sistem', 'val' => $saldoCash, 'icon' => 'fa-wallet', 'color' => 'primary', 'bg' => '#e7f1ff'],
                ['label' => 'Total Uang Fisik', 'val' => $totalUangFisik, 'icon' => 'fa-coins', 'color' => 'dark', 'bg' => '#f0f2f5'],
            ];
        @endphp
        @foreach ($cashStats as $stat)
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm h-100 card-hover" style="border-radius: 12px;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <span class="text-muted d-block small fw-bold text-uppercase mb-1">{{ $stat['label'] }}</span>
                                <h4 class="mb-0 fw-bold">Rp {{ number_format($stat['val'], 0, ',', '.') }}</h4>
                            </div>
                            <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                                 style="width: 45px; height: 45px; background-color: {{ $stat['bg'] }}; color: var(--bs-{{ $stat['color'] }});">
                                <i class="fas {{ $stat['icon'] }} fs-5"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="d-flex align-items-center mb-3">
        <div class="icon-box bg-info text-white rounded-3 me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
            <i class="fas fa-university"></i>
        </div>
        <h6 class="text-uppercase text-dark fw-bold mb-0">Ringkasan Kas Bank / Transfer</h6>
    </div>

    <div class="row g-3 mb-5">
        @php
            $bankStats = [
                ['label' => 'Pendapatan Bank', 'val' => $pendapatanBank, 'icon' => 'fa-arrow-up', 'color' => 'success'],
                ['label' => 'Pengeluaran Bank', 'val' => $pengeluaranBank, 'icon' => 'fa-arrow-down', 'color' => 'danger'],
                ['label' => 'Saldo Perhitungan', 'val' => $saldoHitungBank, 'icon' => 'fa-calculator', 'color' => 'secondary'],
                ['label' => 'Saldo Sistem (Bank)', 'val' => $saldoSistemBank, 'icon' => 'fa-building-columns', 'color' => 'info'],
            ];
        @endphp
        @foreach ($bankStats as $stat)
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm h-100 card-bank border-bottom border-3 border-{{ $stat['color'] }}" style="border-radius: 12px;">
                    <div class="card-body">
                        <span class="text-muted d-block small fw-bold mb-1">{{ $stat['label'] }}</span>
                        <div class="d-flex align-items-center">
                            <i class="fas {{ $stat['icon'] }} me-2 text-{{ $stat['color'] }}"></i>
                            <h4 class="mb-0 fw-bold">Rp {{ number_format($stat['val'], 0, ',', '.') }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
                <div class="card-header bg-white py-3 border-0">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-vault me-2 text-primary"></i>
                        <h5 class="mb-0 fw-bold">Rincian Uang Fisik</h5>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 fs-7 text-muted text-uppercase">Pecahan</th>
                                    <th class="text-center fs-7 text-muted text-uppercase">Jml</th>
                                    <th class="text-end pe-4 fs-7 text-muted text-uppercase">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pecahanKas as $item)
                                    <tr>
                                        <td class="ps-4 fw-medium text-dark">Rp {{ number_format($item->pecahan, 0, ',', '.') }}</td>
                                        <td class="text-center">
                                            <span class="badge rounded-pill bg-light text-dark border px-3">{{ $item->jumlah }}</span>
                                        </td>
                                        <td class="text-end pe-4 fw-bold text-primary">
                                            Rp {{ number_format($item->pecahan * $item->jumlah, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-0">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-history me-2 text-primary"></i>
                        <h5 class="mb-0 fw-bold text-dark">Aktivitas Hari Ini</h5>
                    </div>
                    <span class="badge bg-soft-primary text-primary rounded-pill px-3 py-2">
                        {{ count($riwayatTransaksi) }} Transaksi
                    </span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 fs-7 text-muted text-uppercase">Waktu</th>
                                    <th class="fs-7 text-muted text-uppercase">Tipe</th>
                                    <th class="fs-7 text-muted text-uppercase">Keterangan</th>
                                    <th class="text-end pe-4 fs-7 text-muted text-uppercase">Nominal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($riwayatTransaksi as $trx)
                                    @php
                                        $isIn = in_array($trx->jenis, ['penjualan barang', 'penjualan pulsa', 'masuk', 'inject']);
                                        $isCash = $trx->id_kas_warung == $kasCash->id;
                                    @endphp
                                    <tr class="hover-row">
                                        <td class="ps-4">
                                            <span class="text-muted small fw-bold">{{ $trx->created_at->format('H:i') }}</span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $isCash ? 'bg-soft-warning text-warning' : 'bg-soft-info text-info' }} rounded-pill px-3">
                                                <i class="fas {{ $isCash ? 'fa-money-bill' : 'fa-credit-card' }} me-1 small"></i>
                                                {{ $isCash ? 'CASH' : 'BANK' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="text-dark fw-bold small">{{ $trx->keterangan }}</span>
                                                <small class="text-muted text-uppercase" style="font-size: 10px;">{{ $trx->metode_pembayaran }}</small>
                                            </div>
                                        </td>
                                        <td class="text-end pe-4 fw-bold {{ $isIn ? 'text-success' : 'text-danger' }}">
                                            <span class="small">{{ $isIn ? '+' : '-' }}</span> Rp {{ number_format($trx->total, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="fas fa-receipt fa-3x mb-3 opacity-25"></i>
                                                <p>Belum ada aktivitas transaksi untuk hari ini.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .fs-7 { font-size: 0.75rem; letter-spacing: 0.05em; }
    .bg-soft-primary { background-color: #e7f1ff; }
    .bg-soft-warning { background-color: #fff4e5; }
    .bg-soft-info { background-color: #e0f7fa; }
    .card-hover { transition: all 0.3s ease; }
    .card-hover:hover { transform: translateY(-5px); }
    .hover-row:hover { background-color: #f8f9fa; }
    .icon-shape {
        width: 48px;
        height: 48px;
        background-position: center;
        border-radius: 0.75rem;
    }
</style>
@endsection

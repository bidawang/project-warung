@extends('layouts.app')

@section('title', 'Manajemen Kas Warung')

@section('content')
    <div class="container-fluid py-4" style="background-color: #f8f9fa; min-height: 100vh;">
        {{-- CSS tetap sama seperti kode Anda sebelumnya --}}

        <div class="row align-items-center mb-4">
            <div class="col-md-6">
                <h2 class="fw-bold text-dark mb-0">Manajemen Kas</h2>
                <p class="text-muted small mb-0">Pantau arus kas Cash & Bank secara real-time.</p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <a href="{{ route('kasir.kas.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
                    <i class="fas fa-plus me-2"></i>Transaksi Manual
                </a>
            </div>
        </div>

        {{-- BARIS 1: KAS CASH --}}
        <h6 class="text-uppercase text-muted fw-bold mb-3"><i class="fas fa-money-bill-wave me-2"></i>Ringkasan Kas Tunai
            (Cash)</h6>
        <div class="row g-3 mb-4">
            @php
                $cashStats = [
                    [
                        'label' => 'Pendapatan Cash',
                        'val' => $pendapatanCash,
                        'icon' => 'fa-arrow-up',
                        'color' => 'success',
                    ],
                    [
                        'label' => 'Pengeluaran Cash',
                        'val' => $pengeluaranCash,
                        'icon' => 'fa-arrow-down',
                        'color' => 'danger',
                    ],
                    [
                        'label' => 'Saldo Sistem (Cash)',
                        'val' => $saldoCash,
                        'icon' => 'fa-wallet',
                        'color' => 'primary',
                    ],
                    ['label' => 'Total Uang Fisik', 'val' => $totalUangFisik, 'icon' => 'fa-coins', 'color' => 'dark'],
                ];
            @endphp
            @foreach ($cashStats as $stat)
                <div class="col-xl-3 col-md-6">
                    <div class="card card-stats shadow-sm border-start border-4 border-{{ $stat['color'] }}">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <span class="text-muted mb-1 d-block small fw-bold">{{ $stat['label'] }}</span>
                                    <h4 class="mb-0 fw-bold">Rp {{ number_format($stat['val'], 0, ',', '.') }}</h4>
                                </div>
                                <div class="col-auto">
                                    <div class="icon-shape bg-{{ $stat['color'] }} text-white shadow-sm">
                                        <i class="fas {{ $stat['icon'] }}"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- BARIS 2: KAS BANK --}}
        <h6 class="text-uppercase text-muted fw-bold mb-3"><i class="fas fa-university me-2"></i>Ringkasan Kas Bank /
            Transfer</h6>
        <div class="row g-3 mb-5">
            @php
                $bankStats = [
                    [
                        'label' => 'Pendapatan Bank',
                        'val' => $pendapatanBank,
                        'icon' => 'fa-arrow-up',
                        'color' => 'success',
                    ],
                    [
                        'label' => 'Pengeluaran Bank',
                        'val' => $pengeluaranBank,
                        'icon' => 'fa-arrow-down',
                        'color' => 'danger',
                    ],
                    [
                        'label' => 'Saldo Perhitungan',
                        'val' => $saldoHitungBank,
                        'icon' => 'fa-calculator',
                        'color' => 'secondary',
                    ],
                    [
                        'label' => 'Saldo Sistem (Bank)',
                        'val' => $saldoSistemBank,
                        'icon' => 'fa-university',
                        'color' => 'info',
                    ],
                ];
            @endphp
            @foreach ($bankStats as $stat)
                <div class="col-xl-3 col-md-6">
                    <div class="card card-stats shadow-sm border-start border-4 border-{{ $stat['color'] }}">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <span class="text-muted mb-1 d-block small fw-bold">{{ $stat['label'] }}</span>
                                    <h4 class="mb-0 fw-bold">Rp {{ number_format($stat['val'], 0, ',', '.') }}</h4>
                                </div>
                                <div class="col-auto">
                                    <div class="icon-shape bg-{{ $stat['color'] }} text-white shadow-sm">
                                        <i class="fas {{ $stat['icon'] }}"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row g-4">
            {{-- TABEL PECAHAN (Tetap) --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                    <div class="card-header bg-white py-3 border-0">
                        <h5 class="mb-0 fw-bold text-dark">Rincian Uang Fisik</h5>
                    </div>
                    <div class="card-body p-0">
                        {{-- Isian tabel pecahan tetap menggunakan $pecahanKas --}}
                        <div class="table-responsive">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-4">Pecahan</th>
                                        <th class="text-center">Jml</th>
                                        <th class="text-end pe-4">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pecahanKas as $item)
                                        <tr>
                                            <td class="ps-4 fw-medium">Rp {{ number_format($item->pecahan, 0, ',', '.') }}
                                            </td>
                                            <td class="text-center"><span
                                                    class="badge rounded-pill bg-light text-dark border">{{ $item->jumlah }}</span>
                                            </td>
                                            <td class="text-end pe-4 fw-bold">Rp
                                                {{ number_format($item->pecahan * $item->jumlah, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- RIWAYAT TRANSAKSI GABUNGAN --}}
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-0">
                        <h5 class="mb-0 fw-bold text-dark">Aktivitas Hari Ini (Cash & Bank)</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-4">Waktu</th>
                                        <th>Kas</th>
                                        <th>Keterangan</th>
                                        <th class="text-end pe-4">Nominal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($riwayatTransaksi as $trx)
                                        @php
                                            $isIn = in_array($trx->jenis, [
                                                'penjualan barang',
                                                'penjualan pulsa',
                                                'masuk',
                                                'inject',
                                            ]);
                                            // Cek jenis kas dari relasi id_kas_warung
                                            $isCash = $trx->id_kas_warung == $kasCash->id;
                                        @endphp
                                        <tr>
                                            <td class="ps-4 small text-muted">{{ $trx->created_at->format('H:i') }}</td>
                                            <td>
                                                <span
                                                    class="badge {{ $isCash ? 'bg-light text-dark' : 'bg-info text-white' }} small">
                                                    {{ $isCash ? 'CASH' : 'BANK' }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="text-dark small fw-medium">{{ $trx->keterangan }}</span>
                                                <br><small
                                                    class="text-muted">{{ strtoupper($trx->metode_pembayaran) }}</small>
                                            </td>
                                            <td class="text-end pe-4 fw-bold {{ $isIn ? 'text-success' : 'text-danger' }}">
                                                {{ $isIn ? '+' : '-' }} Rp {{ number_format($trx->total, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-5">Tidak ada transaksi hari ini.</td>
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
@endsection

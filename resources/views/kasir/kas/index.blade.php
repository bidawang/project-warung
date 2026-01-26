@extends('layouts.app')

@section('title', 'Manajemen Kas Warung')

@section('content')
    <div class="container-fluid py-4" style="background-color: #f8f9fa; min-height: 100vh;">

        {{-- CUSTOM CSS UNTUK ESTETIKA --}}
        <style>
            .card-stats {
                transition: transform 0.2s;
                border: none;
                border-radius: 12px;
            }

            .card-stats:hover {
                transform: translateY(-5px);
            }

            .icon-shape {
                width: 48px;
                height: 48px;
                background-position: center;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .table thead th {
                background-color: #f1f4f8;
                text-transform: uppercase;
                font-size: 0.75rem;
                letter-spacing: 1px;
                color: #8898aa;
                border-bottom: none;
            }

            .badge-soft-success {
                background-color: #e6fffa;
                color: #38b2ac;
                border: 1px solid #b2f5ea;
            }

            .badge-soft-danger {
                background-color: #fff5f5;
                color: #e53e3e;
                border: 1px solid #feb2b2;
            }

            .glass-header {
                background: rgba(255, 255, 255, 0.9);
                backdrop-filter: blur(10px);
            }
        </style>

        {{-- NOTIFIKASI --}}
        @if (session('success') || session('error'))
            <div class="position-fixed top-0 end-0 p-3" style="z-index: 1050">
                <div class="alert {{ session('success') ? 'alert-success' : 'alert-danger' }} border-0 shadow-lg alert-dismissible fade show"
                    role="alert">
                    <i class="fas {{ session('success') ? 'fa-check-circle' : 'fa-exclamation-circle' }} me-2"></i>
                    {{ session('success') ?? session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        @endif

        {{-- HEADER --}}
        <div class="row align-items-center mb-4">
            <div class="col-md-6">
                <h2 class="fw-bold text-dark mb-0">Manajemen Kas</h2>
                <p class="text-muted small mb-0">Pantau arus kas dan pecahan uang fisik secara real-time.</p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <a href="{{-- route('kasir.kas.laporan-harian') --}}" class="btn btn-outline-primary rounded-pill px-4 me-2">
                    <i class="fas fa-print me-2"></i>Cetak Laporan
                </a>
                <a href="{{ route('kasir.kas.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
                    <i class="fas fa-plus me-2"></i>Transaksi Manual
                </a>
            </div>
        </div>

        {{-- RINGKASAN KARTU --}}
        <div class="row g-3 mb-4">
            @php
                $stats = [
                    [
                        'label' => 'Total Pendapatan',
                        'val' => $totalPendapatan,
                        'icon' => 'fa-arrow-up',
                        'color' => 'success',
                    ],
                    [
                        'label' => 'Total Pengeluaran',
                        'val' => $totalPengeluaran,
                        'icon' => 'fa-arrow-down',
                        'color' => 'danger',
                    ],
                    ['label' => 'Saldo Sistem', 'val' => $saldoBersih, 'icon' => 'fa-wallet', 'color' => 'primary'],
                    ['label' => 'Total Uang Fisik', 'val' => $totalUangFisik, 'icon' => 'fa-coins', 'color' => 'dark'],
                ];
            @endphp

            @foreach ($stats as $stat)
                <div class="col-xl-3 col-md-6">
                    <div class="card card-stats shadow-sm h-100">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <span class="text-muted mb-1 d-block small fw-bold">{{ $stat['label'] }}</span>
                                    <h3 class="mb-0 fw-bold">Rp {{ number_format($stat['val'], 0, ',', '.') }}</h3>
                                </div>
                                <div class="col-auto">
                                    <div class="icon-shape bg-{{ $stat['color'] }} text-white shadow">
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
            {{-- TABEL PECAHAN --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                    <div class="card-header bg-white py-3 border-0">
                        <h5 class="mb-0 fw-bold text-dark">Rincian Uang Fisik</h5>
                    </div>
                    <div class="card-body p-0">
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
                                    @forelse($pecahanKas as $item)
                                        <tr>
                                            <td class="ps-4 fw-medium text-dark">Rp
                                                {{ number_format($item->pecahan, 0, ',', '.') }}</td>
                                            <td class="text-center"><span
                                                    class="badge rounded-pill bg-light text-dark border">{{ $item->jumlah }}</span>
                                            </td>
                                            <td class="text-end pe-4 fw-bold text-dark">Rp
                                                {{ number_format($item->pecahan * $item->jumlah, 0, ',', '.') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-4 text-muted">Data kosong</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot class="bg-light">
                                    <tr>
                                        <th colspan="2" class="ps-4 py-3">Grand Total</th>
                                        <th class="text-end pe-4 py-3 text-primary h5 mb-0 fw-bold">Rp
                                            {{ number_format($totalUangFisik, 0, ',', '.') }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- RIWAYAT TRANSAKSI --}}
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-0">
                        <h5 class="mb-0 fw-bold text-dark">Riwayat Transaksi Terakhir</h5>
                        <span class="badge bg-soft-primary text-primary px-3">Bulan Ini</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table align-items-center mb-0 hoverable">
                                <thead>
                                    <tr>
                                        <th class="ps-4">Tanggal & Waktu</th>
                                        <th>Keterangan</th>
                                        <th class="text-center">Metode</th>
                                        <th class="text-end pe-4">Nominal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($riwayatTransaksi as $trx)
                                        @php
                                            $incomeTypes = ['penjualan barang', 'penjualan pulsa', 'masuk'];
                                            $isIn = in_array($trx->jenis, $incomeTypes);
                                        @endphp
                                        <tr>
                                            <td class="ps-4">
                                                <div class="d-flex flex-column">
                                                    <span
                                                        class="text-dark fw-bold small text-nowrap">{{ \Carbon\Carbon::parse($trx->created_at)->translatedFormat('d M Y') }}</span>
                                                    <span class="text-muted extra-small"
                                                        style="font-size: 11px;">{{ \Carbon\Carbon::parse($trx->created_at)->format('H:i') }}
                                                        WIB</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <span
                                                        class="badge {{ $isIn ? 'bg-soft-success text-success' : 'bg-soft-danger text-danger' }} me-2 p-1">
                                                        <i class="fas {{ $isIn ? 'fa-plus' : 'fa-minus' }}"
                                                            style="font-size: 10px;"></i>
                                                    </span>
                                                    <span
                                                        class="text-dark small fw-medium">{{ $trx->keterangan ?? 'Tanpa Keterangan' }}</span>
                                                </div>
                                            </td>
                                            <td class="text-center text-muted small">
                                                {{ strtoupper($trx->metode_pembayaran) }}</td>
                                            <td class="text-end pe-4 fw-bold {{ $isIn ? 'text-success' : 'text-danger' }}">
                                                {{ $isIn ? '+' : '-' }} Rp {{ number_format($trx->total, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-5 text-muted">Belum ada aktivitas
                                                transaksi hari ini.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-0 py-3 text-center">
                        <a href="{{ url('/kasir/riwayat-transaksi') }}" class="text-primary text-decoration-none small fw-bold">Lihat Semua Transaksi <i
                                class="fas fa-chevron-right ms-1"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

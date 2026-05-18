@extends('layouts.app')

@section('title', 'Manajemen Pulsa Warung')

@section('content')
    <style>
        .transition-all { transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1); }
        .hover-lift:hover { transform: translateY(-3px); box-shadow: 0 1rem 3rem rgba(0,0,0,0.1) !important; }
        .fs-7 { font-size: 0.75rem !important; }
        .fs-8 { font-size: 0.65rem !important; }
        .tracking-wide { letter-spacing: 0.05em; }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        
        /* Nav Tabs Glassmorphism Style */
        .nav-pills-custom .nav-link {
            color: #6c757d;
            background: #fff;
            border: 1px solid #e9ecef;
            padding: 0.5rem 1.25rem;
            font-weight: 600;
            border-radius: 50px;
            white-space: nowrap;
        }
        .nav-pills-custom .nav-link.active {
            color: #fff;
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            border-color: transparent;
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.25);
        }
        
        /* Visual Left Border on Table Hover */
        #hargaTable tbody tr { border-left: 3px solid transparent; }
        #hargaTable tbody tr:hover {
            background-color: #f8fafc;
            border-left-color: #0d6efd;
        }
    </style>

    <div class="container-fluid py-3 py-lg-4 px-3 px-xl-4" style="background-color: #f8fafc; min-height: 100vh;">

        {{-- ALERT SUCCESS --}}
        @if (session('success'))
            <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show mb-4 rounded-4" role="alert">
                <div class="d-flex align-items-center py-1">
                    <i class="bi bi-check-circle-fill me-3 fs-5 text-success"></i>
                    <div>
                        <h6 class="mb-0 fw-bold">Berhasil!</h6>
                        <small class="text-secondary">{{ session('success') }}</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row g-4">
            
            {{-- ==================== KOLOM STRATEGIS (KIRI DESKTOP / ATAS MOBILE) ==================== --}}
            <div class="col-lg-5 col-xl-4 order-1 order-lg-2">
                
                {{-- CARD UTAMA SALDO (DESIGN FINTECH) --}}
                <div class="card border-0 bg-dark text-white rounded-4 shadow-sm overflow-hidden mb-4 position-relative" 
                     style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
                    <div class="position-absolute top-0 end-0 p-3 opacity-10 fs-1">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="fs-7 text-white-50 text-uppercase tracking-wide fw-bold">Total Monitor Saldo</span>
                            <span class="badge bg-success bg-opacity-20 text-success rounded-pill px-2 py-1 fs-8">{{ count($saldoPulsas) }} Provider</span>
                        </div>
                        
                        <div class="d-flex flex-nowrap overflow-auto hide-scrollbar g-2 flex-lg-column gap-2" style="-webkit-overflow-scrolling: touch;">
                            @forelse ($saldoPulsas as $pulsa)
                                <div class="bg-white bg-opacity-10 border border-white border-opacity-10 rounded-3 p-3 flex-shrink-0 flex-lg-shrink-1 w-lg-100" style="min-width: 160px;">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <span class="fs-8 fw-bold text-white-50 text-uppercase">
                                            {{ $pulsa->jenisPulsa->nama_jenis ?? 'PROVIDER' }}
                                        </span>
                                        <i class="bi bi-phone fs-7 text-info"></i>
                                    </div>
                                    <h5 class="fw-bold mb-0 text-white">Rp {{ number_format($pulsa->jumlah, 0, ',', '.') }}</h5>
                                </div>
                            @empty
                                <div class="text-center py-2 text-white-50 fs-7 w-100">Belum ada data saldo</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- QUICK ACTION BUTTON --}}
                <div class="mb-4">
                    <a href="{{ route('kasir.pulsa.jual.create') }}"
                       class="btn btn-primary w-100 py-3 rounded-4 fw-bold shadow-sm transition-all hover-lift d-flex align-items-center justify-content-center"
                       style="background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%); border: none;">
                        <i class="fas fa-mobile-screen me-2 fs-5"></i> JUAL PULSA SEKARANG
                    </a>
                </div>

                {{-- RIWAYAT TRANSAKSI (ELEGANT LIST) --}}
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden d-none d-lg-block">
                    <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold text-dark d-flex align-items-center">
                            <i class="bi bi-clock-history me-2 text-secondary"></i> Riwayat Terakhir
                        </h6>
                        <span class="badge bg-light text-muted fs-8 rounded-pill px-2 py-1">Live Update</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive hide-scrollbar" style="max-height: 380px;">
                            <table class="table align-middle mb-0">
                                <tbody>
                                    @forelse ($transaksi_pulsa as $trx)
                                        <tr class="transition-all">
                                            <td class="ps-4 py-3">
                                                <div class="fw-bold text-dark fs-6">
                                                    Pulsa {{ number_format($trx->hargaPulsa->jumlah_pulsa ?? 0, 0, ',', '.') }}
                                                </div>
                                                <small class="text-muted d-block mt-0.5 fs-8">
                                                    {{ $trx->created_at->diffForHumans() }}
                                                </small>
                                            </td>
                                            <td class="text-end py-3">
                                                <span class="fw-bold text-dark fs-6 d-block">
                                                    Rp {{ number_format($trx->total ?? 0, 0, ',', '.') }}
                                                </span>
                                                <span class="badge bg-light border text-dark-emphasis rounded-pill fs-8 fw-semibold px-2 py-0.5">
                                                    {{ strtoupper($trx->hargaPulsa->jenisPulsa->nama_jenis ?? 'UMUM') }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="text-center py-5 text-muted small">
                                                <i class="bi bi-inbox fs-3 d-block text-light mb-2"></i> Belum ada aktivitas penjualan
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ==================== KOLOM UTAMA: DATA HARGA & FILTER TABS (KANAN DESKTOP / BAWAH MOBILE) ==================== --}}
            <div class="col-lg-7 col-xl-8 order-2 order-lg-1">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5 mb-lg-0">
                    
                    {{-- HEADER & SEARCH ENGINE --}}
                    <div class="card-header bg-white pt-4 pb-3 border-0">
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 mb-3">
                            <div>
                                <h5 class="mb-1 fw-black text-dark d-flex align-items-center">
                                    <i class="fas fa-tags me-2 text-primary fs-5"></i> Cek Harga Jual Aktual
                                </h5>
                                <p class="text-muted mb-0 small">Katalog harga grosir langsung terintegrasi sistem kasir</p>
                            </div>
                            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2 fw-bold fs-7">
                                {{ count($harga_pulsas) }} SKU Terdaftar
                            </span>
                        </div>
                        
                        <div class="input-group shadow-sm rounded-4 overflow-hidden bg-light border-0 p-1 mb-2">
                            <span class="input-group-text bg-transparent border-0 pe-2 ps-3">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" id="searchInputHarga"
                                class="form-control border-0 bg-transparent py-2.5 shadow-none fs-6"
                                placeholder="Ketik nominal atau operator... (Contoh: 10000 Telkomsel)">
                        </div>
                    </div>

                    {{-- NAV TABS OPERATOR (DYNAMIC & HORIZONTAL SCROLLABLE) --}}
                    <div class="px-4 pb-3 border-0 bg-white">
                        <ul class="nav nav-pills nav-pills-custom flex-nowrap overflow-auto hide-scrollbar gap-2 pb-2" id="operatorTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active filter-btn" data-operator="all" type="button">
                                    <i class="bi bi-grid-fill me-1"></i> Semua Produk
                                </button>
                            </li>
                            {{-- Mengambil Unique Operator Name Secara Dinamis dari Data yang Ada --}}
                            @php
                                $uniqueOperators = $harga_pulsas->pluck('jenisPulsa.nama_jenis')->filter()->unique();
                            @endphp
                            @foreach ($uniqueOperators as $operator)
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link filter-btn" data-operator="{{ strtolower($operator) }}" type="button">
                                        {{ strtoupper($operator) }}
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- LAYOUT UTAMA DAFTAR HARGA --}}
                    <div class="card-body p-0">
                        
                        {{-- VIEW DESKTOP & TABLET (TABLE SYSTEM) --}}
                        <div class="table-responsive d-none d-md-block">
                            <table class="table align-middle mb-0" id="hargaTable">
                                <thead class="bg-light border-bottom">
                                    <tr class="fs-7 text-uppercase tracking-wide text-secondary fw-bold">
                                        <th class="ps-4 py-3" style="width: 60%;">Nama Produk / Provider</th>
                                        <th class="pe-4 py-3 text-end" style="width: 40%;">Harga Jual Kasir</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($harga_pulsas as $harga)
                                        @php $opName = $harga->jenisPulsa->nama_jenis ?? 'Umum'; @endphp
                                        <tr class="harga-item transition-all"
                                            data-nominal="{{ $harga->jumlah_pulsa }}"
                                            data-operator="{{ strtolower($opName) }}">
                                            
                                            <td class="ps-4 py-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-primary-subtle text-primary rounded-3 d-flex align-items-center justify-content-center me-3 shadow-sm" style="width: 40px; height: 40px;">
                                                        <i class="bi bi-lightning-charge-fill fs-5"></i>
                                                    </div>
                                                    <div>
                                                        <span class="d-block fw-bold text-dark fs-6 mb-0">
                                                            {{ $opName }}
                                                        </span>
                                                        <small class="text-muted fw-medium fs-7">
                                                            Denom / Nominal: <strong class="text-dark">{{ number_format($harga->jumlah_pulsa, 0, ',', '.') }}</strong>
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            
                                            <td class="pe-4 py-3 text-end">
                                                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 fs-6 fw-bold rounded-3 border border-primary border-opacity-10">
                                                    Rp {{ number_format($harga->harga_jual, 0, ',', '.') }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr class="empty-state-row">
                                            <td colspan="2" class="text-center py-5 text-muted">Data produk tidak ditemukan.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- VIEW MOBILE (CARD-LIST SYSTEM) --}}
                        <div class="d-md-none" id="mobileHargaList">
                            @forelse ($harga_pulsas as $harga)
                                @php $opName = $harga->jenisPulsa->nama_jenis ?? 'Umum'; @endphp
                                <div class="harga-item p-3 border-bottom d-flex justify-content-between align-items-center bg-white transition-all"
                                     data-nominal="{{ $harga->jumlah_pulsa }}"
                                     data-operator="{{ strtolower($opName) }}">
                                    <div>
                                        <div class="d-flex align-items-center mb-1 gap-2">
                                            <span class="fw-extrabold text-dark fs-5">
                                                {{ number_format($harga->jumlah_pulsa, 0, ',', '.') }}
                                            </span>
                                            <span class="badge bg-light text-dark border px-2 py-1 fs-8 fw-bold">
                                                {{ strtoupper($opName) }}
                                            </span>
                                        </div>
                                        <small class="text-muted d-block fs-7"><i class="bi bi-lightning-fill text-warning me-1"></i>Metode Instan</small>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-black text-primary fs-5 mb-0">Rp {{ number_format($harga->harga_jual, 0, ',', '.') }}</div>
                                        <small class="text-success fw-bold fs-8"><i class="fas fa-check-circle me-1"></i>Ready</small>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-5 bg-white empty-state-row">
                                    <i class="bi bi-folder-x fs-1 text-light d-block mb-2"></i>
                                    <p class="text-muted small mb-0">Daftar harga tidak tersedia.</p>
                                </div>
                            @endforelse
                        </div>

                        <div id="noDataAlert" class="text-center py-5 bg-white d-none rounded-bottom-4">
                            <i class="bi bi-search fs-2 text-muted d-block mb-2"></i>
                            <h6 class="fw-bold text-dark">Data Tidak Ditemukan</h6>
                            <p class="text-muted small mb-0">Coba kata kunci lain atau pilih tab provider berbeda.</p>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ALGORITMA CORE: FILTER TABS DAN SEARCH ENGINE INTEGRATED (SUPER CEPAT TANPA LAG) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInputHarga');
            const filterButtons = document.querySelectorAll('.filter-btn');
            const items = document.querySelectorAll('.harga-item');
            const noDataAlert = document.getElementById('noDataAlert');
            const desktopTableHead = document.querySelector('#hargaTable thead');

            let currentOperator = 'all';
            let currentSearchTerm = '';

            function applyFilter() {
                let visibleCount = 0;

                items.forEach(item => {
                    const itemOperator = item.dataset.operator;
                    const itemNominal = item.dataset.nominal;
                    
                    const matchesTab = (currentOperator === 'all' || itemOperator === currentOperator);
                    const matchesSearch = (itemNominal + ' ' + itemOperator).toLowerCase().includes(currentSearchTerm);

                    if (matchesTab && matchesSearch) {
                        item.classList.remove('d-none');
                        if (item.tagName === 'TR') item.style.display = '';
                        visibleCount++;
                    } else {
                        item.classList.add('d-none');
                        if (item.tagName === 'TR') item.style.display = 'none';
                    }
                });

                // Mengatur visibility header tabel & pesan kosong
                if (visibleCount === 0) {
                    noDataAlert.classList.remove('d-none');
                    if(desktopTableHead) desktopTableHead.classList.add('d-none');
                } else {
                    noDataAlert.classList.add('d-none');
                    if(desktopTableHead) desktopTableHead.classList.remove('d-none');
                }
            }

            // Event handler Input Pencarian
            searchInput.addEventListener('input', function() {
                currentSearchTerm = this.value.toLowerCase().trim();
                applyFilter();
            });

            // Event handler Klik Nav Tabs Operator
            filterButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Berpindah kelas active pada komponen tab
                    filterButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');

                    currentOperator = this.dataset.operator;
                    applyFilter();
                });
            });
        });
    </script>
@endsection
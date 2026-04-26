@extends('layouts.app')

@section('title', 'Manajemen Pulsa Warung')

@section('content')
<div class="container-fluid py-3 py-md-4" style="background-color: #f4f7f6; min-height: 100vh;">

    {{-- ALERT SUCCESS --}}
    @if (session('success'))
        <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show mb-3 mx-2" role="alert">
            <div class="d-flex align-items-center small">
                <i class="bi bi-check-circle-fill me-2"></i>
                <div>{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-3">
        {{-- KOLOM KANAN (PINDAH KE ATAS DI HP): SALDO & AKSI --}}
        <div class="col-lg-5 col-xl-4 order-1 order-lg-2">
            
            {{-- TOMBOL JUAL (UTAMA DI HP) --}}
            <div class="px-2 mb-3 d-md-none">
                <a href="{{ route('kasir.pulsa.jual.create') }}"
                   class="btn btn-primary w-100 py-3 rounded-4 fw-bold shadow-sm animate__animated animate__pulse animate__infinite">
                    <i class="fas fa-mobile-screen me-2"></i>JUAL PULSA SEKARANG
                </a>
            </div>

            {{-- CARD SALDO PULSA (Horizontal Scroll di HP) --}}
            <div class="mb-3">
                <div class="px-2 d-flex justify-content-between align-items-end mb-2">
                    <h6 class="fw-bold mb-0 text-dark small"><i class="fas fa-wallet me-2 text-success"></i>Saldo Pulsa</h6>
                    <small class="text-muted" style="font-size: 10px;">{{ count($saldoPulsas) }} Kategori</small>
                </div>
                <div class="d-flex flex-nowrap overflow-auto px-2 pb-2 g-2 flex-lg-column" style="-webkit-overflow-scrolling: touch;">
                    @forelse ($saldoPulsas as $pulsa)
                        <div class="card border-0 shadow-sm rounded-4 me-2 mb-lg-2 flex-shrink-0 flex-lg-shrink-1" 
                             style="min-width: 160px; background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                            <div class="card-body p-3 text-white">
                                <small class="text-white-50 d-block fw-bold uppercase" style="font-size: 9px;">{{ $pulsa->jenisPulsa->nama_jenis }}</small>
                                <div class="fw-bold fs-6">Rp {{ number_format($pulsa->saldo, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="card border-0 shadow-sm rounded-4 w-100 bg-white">
                            <div class="card-body p-3 text-center small text-muted">Saldo kosong</div>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- TOMBOL AKSI DESKTOP --}}
            <div class="card border-0 shadow-sm mb-3 d-none d-lg-block" style="border-radius: 15px;">
                <div class="card-body p-3">
                    <a href="{{ route('kasir.pulsa.jual.create') }}"
                       class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-sm">
                        <i class="fas fa-mobile-screen me-2"></i>JUAL PULSA SEKARANG
                    </a>
                </div>
            </div>

            {{-- RIWAYAT TRANSAKSI (Sembunyikan di HP jika terlalu panjang, atau gunakan ringkasan) --}}
            <div class="card border-0 shadow-sm d-none d-lg-block" style="border-radius: 15px; overflow: hidden;">
                <div class="card-header bg-white py-3 border-0 border-bottom">
                    <h6 class="mb-0 fw-bold text-dark small"><i class="fas fa-history me-2 text-muted"></i>Riwayat Terakhir</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 300px;">
                        <table class="table align-middle mb-0 table-sm">
                            <tbody class="small">
                                @forelse ($transaksi_pulsa as $pulsa)
                                    <tr class="border-bottom">
                                        <td class="ps-3 py-2">
                                            <div class="fw-bold text-dark">Pulsa {{ number_format($pulsa->jumlah, 0, ',', '.') }}</div>
                                            <div class="text-muted" style="font-size: 10px;">{{ $pulsa->created_at->diffForHumans() }}</div>
                                        </td>
                                        <td class="text-end pe-3">
                                            <span class="fw-bold text-danger">Rp {{ number_format($pulsa->total, 0, ',', '.') }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="2" class="text-center py-4 text-muted small">Kosong</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- KOLOM KIRI: DAFTAR HARGA JUAL PULSA --}}
        <div class="col-lg-7 col-xl-8 order-2 order-lg-1">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5 mb-lg-0">
                <div class="card-header bg-white py-3 border-0 sticky-top shadow-sm-mobile">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0 fw-bold text-dark">
                            <i class="fas fa-tags me-2 text-primary"></i>Cek Harga Jual
                        </h6>
                        <span class="badge bg-soft-primary text-primary rounded-pill" style="font-size: 10px;">{{ count($harga_pulsas) }} Produk</span>
                    </div>
                    {{-- Search Bar Sticky --}}
                    <div class="input-group input-group-sm shadow-sm rounded-pill overflow-hidden bg-light border-0 px-2 py-1">
                        <span class="input-group-text bg-transparent border-0 pe-1">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" id="searchInputHarga" class="form-control border-0 bg-transparent py-2 shadow-none"
                               placeholder="Cari: 10000 telkomsel...">
                    </div>
                </div>

                <div class="card-body p-0">
                    {{-- DESKTOP TABLE --}}
                    <div class="table-responsive d-none d-md-block">
                        <table class="table align-middle mb-0 table-hover" id="hargaTable">
                            <thead class="bg-light">
                                <tr class="fs-7 text-muted uppercase">
                                    <th class="ps-4">Produk</th>
                                    <th>Harga Jual</th>
                                    <th class="text-center">Operator</th>
                                    <th class="text-center pe-4">Kategori</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($harga_pulsas as $harga)
                                    <tr class="harga-item" data-nominal="{{ $harga->jumlah_pulsa }}" data-operator="{{ strtolower($harga->operator ?? '-') }}">
                                        <td class="ps-4 fw-bold text-dark">Pulsa {{ number_format($harga->jumlah_pulsa, 0, ',', '.') }}</td>
                                        <td class="fw-bold text-primary">Rp {{ number_format($harga->harga, 0, ',', '.') }}</td>
                                        <td class="text-center"><span class="badge bg-light text-dark border">{{ strtoupper($harga->operator ?? 'UMUM') }}</span></td>
                                        <td class="text-center pe-4"><span class="badge bg-soft-info text-info rounded-pill">{{ ucfirst($harga->nama_jenis) }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- MOBILE LIST VIEW --}}
                    <div class="d-md-none" id="mobileHargaList">
                        @forelse ($harga_pulsas as $harga)
                            <div class="harga-item p-3 border-bottom d-flex justify-content-between align-items-center" 
                                 data-nominal="{{ $harga->jumlah_pulsa }}" 
                                 data-operator="{{ strtolower($harga->operator ?? '-') }}">
                                <div>
                                    <div class="d-flex align-items-center mb-1">
                                        <span class="fw-bold text-dark me-2">Pulsa {{ number_format($harga->jumlah_pulsa, 0, ',', '.') }}</span>
                                        <span class="badge bg-light text-dark border" style="font-size: 9px;">{{ strtoupper($harga->operator ?? 'UMUM') }}</span>
                                    </div>
                                    <small class="text-muted">{{ ucfirst($harga->nama_jenis) }}</small>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold text-primary fs-5">Rp {{ number_format($harga->harga, 0, ',', '.') }}</div>
                                    <small class="text-success fw-bold" style="font-size: 9px;">Aktif <i class="fas fa-check-circle"></i></small>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <img src="https://illustrations.popsy.co/gray/data-report.svg" style="width: 100px;" class="mb-3 opacity-50">
                                <p class="text-muted small">Daftar harga belum ada.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .fs-7 { font-size: 0.65rem; letter-spacing: 0.05em; }
    .bg-soft-primary { background-color: #e7f1ff; }
    .bg-soft-info { background-color: #e0f7fa; }
    .uppercase { text-transform: uppercase; letter-spacing: 1px; }
    
    /* Mobile Optimization */
    @media (max-width: 991px) {
        .shadow-sm-mobile { box-shadow: 0 .125rem .25rem rgba(0,0,0,.075)!important; }
        .rounded-4 { border-radius: 1.2rem !important; }
        .sticky-top { top: 0; z-index: 1020; }
    }

    /* Hide scrollbar for Chrome, Safari and Opera */
    .overflow-auto::-webkit-scrollbar { display: none; }
    .overflow-auto { -ms-overflow-style: none; scrollbar-width: none; }
    
    .harga-item:active { background-color: #f0f0f0; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInputHarga');
        const items = document.querySelectorAll('.harga-item');

        searchInput.addEventListener('input', function() {
            const term = this.value.toLowerCase().trim();
            items.forEach(item => {
                const text = (item.dataset.nominal + ' ' + item.dataset.operator).toLowerCase();
                if(text.includes(term)) {
                    item.classList.remove('d-none');
                    // Handle table row vs div display
                    if(item.tagName === 'TR') item.style.display = '';
                } else {
                    item.classList.add('d-none');
                    if(item.tagName === 'TR') item.style.display = 'none';
                }
            });
        });
    });
</script>
@endsection
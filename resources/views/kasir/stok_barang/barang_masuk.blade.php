@extends('layouts.app')

@section('title', 'Verifikasi Barang Masuk')

@section('content')
    <div class="container-fluid py-3 py-md-4" style="background-color: #f8f9fa;">

        {{-- HEADER & NAVIGATION --}}
        {{-- HEADER & NAVIGATION --}}
        <div class="row mb-3 g-3 align-items-center">
            <div class="col-12 col-md-6">
                <div class="d-flex align-items-center gap-3">
                    <a href="{{ url('/kasir/stok-barang') }}"
                        class="btn btn-white shadow-sm rounded-circle d-flex align-items-center justify-content-center"
                        style="width: 40px; height: 40px;">
                        <i class="fas fa-arrow-left text-dark"></i>
                    </a>
                    <div>
                        <h3 class="fw-bold text-dark mb-0 fs-4">Verifikasi Barang</h3>
                        <p class="text-muted small mb-0 d-none d-md-block">Konfirmasi stok masuk untuk sinkronisasi data.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="d-flex gap-2 justify-content-md-end align-items-center">
                    {{-- Tombol Rencana Belanja Selalu Muncul --}}
                    <a href="{{ route('kasir.rencanabelanja.create') }}"
                        class="btn btn-outline-success rounded-pill px-3 flex-grow-1 flex-md-grow-0 shadow-sm">
                        <i class="fas fa-plus-circle me-1"></i> <span class="small fw-bold">Rencana</span>
                    </a>

                    {{-- TOMBOL KONFIRMASI (Hidden by Default) --}}
                    <div id="wrapper-btn-konfirmasi" class="d-none animate__animated animate__fadeInRight">
                        <button type="submit" form="form-konfirmasi" name="status_baru" value="terima"
                            class="btn btn-primary rounded-pill px-3 shadow-sm">
                            <i class="fas fa-check-circle me-1"></i>
                            <span class="small fw-bold" id="btn-text">Konfirmasi (0)</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        @if (session('success') || session('error'))
            <div
                class="alert {{ session('success') ? 'alert-success' : 'alert-danger' }} border-0 shadow-sm rounded-4 mb-4">
                {{ session('success') ?? session('error') }}
            </div>
        @endif

        {{-- TABS & FILTER --}}
        <div class="card border-0 shadow-sm rounded-4 mb-3">
            <div class="card-body p-3">
                {{-- Nav Tabs yang Scrollable di HP --}}
                <div class="d-flex overflow-auto border-bottom mb-3 pb-1" style="white-space: nowrap;">
                    <a class="nav-link-custom {{ request('status') == 'kirim' || !request('status') ? 'active' : '' }}"
                        href="{{ request()->fullUrlWithQuery(['status' => 'kirim']) }}">Perlu Verifikasi</a>
                    <a class="nav-link-custom {{ request('status') == 'terima' ? 'active' : '' }}"
                        href="{{ request()->fullUrlWithQuery(['status' => 'terima']) }}">Riwayat Selesai</a>
                </div>

                <form method="GET" action="">
                    <input type="hidden" name="status" value="{{ request('status', 'kirim') }}">
                    <div class="row g-2">
                        <div class="col-8 col-md-5">
                            <div class="input-group bg-light rounded-pill px-2 border">
                                <span class="input-group-text bg-transparent border-0"><i
                                        class="fas fa-search text-muted"></i></span>
                                <input type="text" name="search" value="{{ request('search') }}"
                                    class="form-control bg-transparent border-0 small" placeholder="Cari barang...">
                            </div>
                        </div>
                        <div class="col-4 col-md-2">
                            <button type="submit" class="btn btn-dark w-100 rounded-pill fw-bold small">Filter</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- SELEKSI SEMUA (Hanya Mobile) --}}
        @if (request('status') != 'terima')
            <div class="d-md-none mb-2 px-2">
                <div class="form-check custom-checkbox py-2 px-3 bg-white rounded-3 shadow-sm border">
                    <input type="checkbox" class="form-check-input" id="select-all-mobile">
                    <label class="form-check-label small fw-bold ms-2" for="select-all-mobile">Pilih Semua Barang</label>
                </div>
            </div>
        @endif

        {{-- DATA CONTENT --}}
        <form id="form-konfirmasi" action="{{ route('kasir.barang-masuk.konfirmasi') }}" method="POST">
            @csrf

            {{-- VIEW DESKTOP: TABLE --}}
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden d-none d-md-block">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr class="text-muted small text-uppercase">
                                <th width="50" class="ps-4">
                                    <input type="checkbox" class="form-check-input" id="select-all"
                                        {{ request('status') == 'terima' ? 'disabled' : '' }}>
                                </th>
                                <th>Info Barang</th>
                                <th class="text-center">Jumlah</th>
                                <th>Harga Satuan</th>
                                <th>Waktu</th>
                                <th class="pe-4 text-end">Tipe</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($barangMasuk as $bm)
                                <tr>
                                    <td class="ps-4">
                                        <input type="checkbox" name="barangMasuk[]" value="{{ $bm->id }}"
                                            {{ $bm->status !== 'kirim' ? 'disabled' : '' }}
                                            class="form-check-input item-checkbox">
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">
                                            {{ $bm->stokWarung->barang->nama_barang ?? 'Barang Terhapus' }}</div>
                                        <span class="badge bg-light text-muted fw-normal" style="font-size: 10px;">ID:
                                            #{{ $bm->id }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge rounded-pill bg-soft-info text-info px-3 py-2">
                                            {{ number_format($bm->jumlah_dibeli ?? ($bm->jumlah ?? 0), 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-success">
                                            Rp{{ number_format($bm->harga_final_satuan, 0, ',', '.') }}</div>
                                        <div class="text-muted" style="font-size: 10px;">Total:
                                            Rp{{ number_format($bm->harga_final_total, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="small">{{ $bm->created_at?->translatedFormat('d M Y') }}</td>
                                    <td class="pe-4 text-end">
                                        <span
                                            class="badge {{ $bm->jenis == 'tambahan' ? 'bg-primary-subtle text-primary' : 'bg-success-subtle text-success' }} border-0 small"
                                            style="font-size: 10px;">{{ strtoupper($bm->jenis) }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">Tidak ada data.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- VIEW MOBILE: CARDS --}}
            <div class="d-md-none">
                @forelse($barangMasuk as $bm)
                    <div class="card border-0 shadow-sm rounded-4 mb-3 overflow-hidden">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-start gap-3">
                                {{-- Checkbox --}}
                                <div class="form-check custom-checkbox">
                                    <input type="checkbox" name="barangMasuk[]" value="{{ $bm->id }}"
                                        {{ $bm->status !== 'kirim' ? 'disabled' : '' }}
                                        class="form-check-input item-checkbox" style="width: 22px; height: 22px;">
                                </div>

                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <h6 class="fw-bold text-dark mb-1">
                                            {{ $bm->stokWarung->barang->nama_barang ?? 'Barang' }}</h6>
                                        <span
                                            class="badge {{ $bm->jenis == 'tambahan' ? 'bg-primary-subtle text-primary' : 'bg-success-subtle text-success' }}"
                                            style="font-size: 9px;">
                                            {{ strtoupper($bm->jenis) }}
                                        </span>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <div class="text-muted small">
                                            <i class="fas fa-box me-1"></i> Qty:
                                            <strong>{{ number_format($bm->jumlah_dibeli ?? ($bm->jumlah ?? 0), 0, ',', '.') }}</strong>
                                        </div>
                                        <div class="text-success fw-bold">
                                            Rp{{ number_format($bm->harga_final_satuan, 0, ',', '.') }}
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                                        <span class="text-muted"
                                            style="font-size: 11px;">{{ $bm->created_at?->translatedFormat('d M, H:i') }}</span>
                                        <span class="text-dark fw-bold" style="font-size: 11px;">Total:
                                            Rp{{ number_format($bm->harga_final_total, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 bg-white rounded-4 shadow-sm">
                        <p class="text-muted">Tidak ada barang masuk.</p>
                    </div>
                @endforelse
            </div>

            @if ($barangMasuk->hasPages())
                <div class="py-4">{{ $barangMasuk->links() }}</div>
            @endif
        </form>
    </div>

    <style>
        /* Custom Styling */
        .nav-link-custom {
            padding: 8px 16px;
            color: #6c757d;
            font-weight: 600;
            text-decoration: none;
            border-bottom: 3px solid transparent;
            transition: 0.3s;
        }

        .nav-link-custom.active {
            color: #0d6efd;
            border-bottom-color: #0d6efd;
        }

        .bg-soft-info {
            background-color: #e0f2f1;
        }

        .bg-white {
            background-color: #ffffff !important;
        }

        /* Memperbesar area klik checkbox di mobile */
        .form-check-input {
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .btn span {
                font-size: 0.8rem;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAllDesktop = document.getElementById('select-all');
            const selectAllMobile = document.getElementById('select-all-mobile');
            const checkboxes = document.querySelectorAll('.item-checkbox:not(:disabled)');

            // Element yang akan dimanipulasi
            const wrapperBtn = document.getElementById('wrapper-btn-konfirmasi');
            const btnText = document.getElementById('btn-text');

            function updateButtonState() {
                const checkedCount = document.querySelectorAll('.item-checkbox:checked:not(:disabled)').length;

                if (checkedCount > 0) {
                    // Munculkan tombol jika ada yang diceklis
                    wrapperBtn.classList.remove('d-none');
                    btnText.textContent = `Konfirmasi (${checkedCount})`;
                } else {
                    // Sembunyikan jika tidak ada
                    wrapperBtn.classList.add('d-none');

                    // Uncheck "Select All" jika semua item di-uncheck manual
                    if (selectAllDesktop) selectAllDesktop.checked = false;
                    if (selectAllMobile) selectAllMobile.checked = false;
                }
            }

            function syncSelectAll(status) {
                checkboxes.forEach(cb => cb.checked = status);
                if (selectAllDesktop) selectAllDesktop.checked = status;
                if (selectAllMobile) selectAllMobile.checked = status;
                updateButtonState();
            }

            // Event Listeners
            selectAllDesktop?.addEventListener('change', (e) => syncSelectAll(e.target.checked));
            selectAllMobile?.addEventListener('change', (e) => syncSelectAll(e.target.checked));

            checkboxes.forEach(cb => {
                cb.addEventListener('change', updateButtonState);
            });
        });
    </script>
@endsection

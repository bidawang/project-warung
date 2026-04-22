@extends('layouts.app')

@section('title', 'Notifikasi Barang Masuk')

@section('content')
    <div class="container py-5">

        {{-- HEADER --}}
        <div class="row mb-4 align-items-center">
            <div class="col-md-4">
                <h2 class="fw-bold text-dark mb-1">Verifikasi Barang Masuk</h2>
                <p class="text-muted small mb-0">Kelola konfirmasi stok masuk untuk sinkronisasi harga dan jumlah.</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="{{ route('kasir.rencanabelanja.create') }}" class="btn btn-success rounded-pill px-4 shadow-sm">
                    <i class="fas fa-plus-circle me-2"></i>Buat Rencana Belanja
                </a>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <button type="submit" form="form-konfirmasi" name="status_baru" value="terima"
                    class="btn btn-primary rounded-pill px-4 shadow-sm" id="btn-submit-all" disabled>
                    <i class="fas fa-check-circle me-2"></i>Konfirmasi Terpilih
                </button>
            </div>
        </div>
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        {{-- FILTER & TABS --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <ul class="nav nav-tabs-custom nav-tabs nav-line-tabs nav-line-tabs-2x border-bottom-0 fw-bold">
                    <li class="nav-item">
                        <a class="nav-link text-active-primary py-3 {{ request('status') == 'kirim' || !request('status') ? 'active border-bottom border-primary border-3' : 'text-muted' }}"
                            href="{{ request()->fullUrlWithQuery(['status' => 'kirim']) }}">
                            Perlu Verifikasi
                        </a>
                    </li>
                    <li class="nav-item ms-3">
                        <a class="nav-link text-active-primary py-3 {{ request('status') == 'terima' ? 'active border-bottom border-primary border-3' : 'text-muted' }}"
                            href="{{ request()->fullUrlWithQuery(['status' => 'terima']) }}">
                            Riwayat
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body px-4 pb-4">
                <form method="GET" action="">
                    {{-- Keep status in query --}}
                    <input type="hidden" name="status" value="{{ request('status', 'kirim') }}">

                    <div class="row g-3">
                        <div class="col-md-5">
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i
                                        class="fas fa-search text-muted"></i></span>
                                <input type="text" name="search" value="{{ request('search') }}"
                                    class="form-control bg-light border-0"
                                    placeholder="Cari nama barang atau ID transaksi...">
                            </div>
                        </div>

                        <div class="col-md-5">
                            <div class="input-group">
                                <input type="date" name="from" value="{{ request('from') }}"
                                    class="form-control bg-light border-0">
                                <span class="input-group-text bg-light border-0 text-muted small">s/d</span>
                                <input type="date" name="to" value="{{ request('to') }}"
                                    class="form-control bg-light border-0">
                            </div>
                        </div>

                        <div class="col-md-2">
                            <button type="submit" class="btn btn-dark w-100 rounded-3">Filter</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        {{-- FILTER JENIS (Nav Tabs) --}}
        <div class="mb-3 px-4">
            <div class="d-flex align-items-center gap-3">
                <span class="text-muted small fw-bold text-uppercase">Tipe:</span>
                <ul class="nav nav-pills nav-pills-custom gap-2">
                    <li class="nav-item">
                        <a class="nav-link btn btn-sm rounded-pill px-3 {{ !request('jenis') ? 'btn-dark active' : 'btn-outline-secondary border-0' }}"
                            href="{{ request()->fullUrlWithQuery(['jenis' => null]) }}">
                            Semua
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-sm rounded-pill px-3 {{ request('jenis') == 'rencana' ? 'btn-success active text-white' : 'btn-outline-secondary border-0' }}"
                            href="{{ request()->fullUrlWithQuery(['jenis' => 'rencana']) }}">
                            <i class="fas fa-calendar-check me-1"></i> Rencana
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-sm rounded-pill px-3 {{ request('jenis') == 'tambahan' ? 'btn-primary active text-white' : 'btn-outline-secondary border-0' }}"
                            href="{{ request()->fullUrlWithQuery(['jenis' => 'tambahan']) }}">
                            <i class="fas fa-plus-circle me-1"></i> Tambahan
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        {{-- DATA TABLE --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <form id="form-konfirmasi" action="{{ route('kasir.barang-masuk.konfirmasi') }}" method="POST">
                @csrf
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light-subtle">
                            <tr class="text-muted small text-uppercase">
                                {{-- Di dalam <thead> --}}
                                <th width="50" class="ps-4">
                                    <div class="form-check custom-checkbox">
                                        <input type="checkbox" class="form-check-input" id="select-all"
                                            {{ request('status') == 'terima' ? 'disabled' : '' }}>
                                    </div>
                                </th>
                                <th>Info Barang</th>
                                <th class="text-center">Jumlah</th>
                                <th>Estimasi Harga (Final)</th>
                                <th>Waktu Kirim</th>
                                <th class="pe-4 text-end">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($barangMasuk as $bm)
                                <tr>
                                    <td class="ps-4">
                                        <div class="form-check custom-checkbox">
                                            <input type="checkbox" name="barangMasuk[]" value="{{ $bm->id }}"
                                                {{ $bm->status !== 'kirim' ? 'disabled' : '' }}
                                                class="form-check-input item-checkbox">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-3 bg-light rounded text-center py-2 px-3">
                                                <i class="fas fa-box text-primary"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark">
                                                    {{ $bm->stokWarung->barang->nama_barang ?? 'Barang Terhapus' }}</div>
                                                {{-- <div class="text-muted small">ID: #{{ $bm->id }}</div> --}}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @if ($bm->is_rencana)
                                            <div class="d-flex flex-column align-items-center gap-1">

                                                {{-- Permintaan --}}
                                                <div class="d-flex flex-column align-items-center">
                                                    <span class="badge bg-soft-secondary text-secondary rounded-pill px-3">
                                                        {{ number_format($bm->jumlah_permintaan ?? ($bm->jumlah ?? 0), 0, ',', '.') }}
                                                    </span>
                                                    <small class="text-muted" style="font-size: 10px;">Permintaan</small>
                                                </div>

                                                {{-- Dibeli --}}
                                                <div class="d-flex align-items-center gap-1">
                                                    <span class="badge bg-soft-info text-info rounded-pill px-3 py-2">
                                                        {{ number_format($bm->jumlah_dibeli ?? 0, 0, ',', '.') }}
                                                    </span>

                                                    @if (!empty($bm->keterangan))
                                                        <small class="text-primary fw-bold text-uppercase lh-1"
                                                            style="font-size: 0.6rem;">
                                                            Tambahan
                                                        </small>
                                                    @endif
                                                </div>
                                                <small class="text-muted" style="font-size: 10px;">Dibeli</small>

                                            </div>
                                        @else
                                            {{-- Non rencana --}}
                                            <span class="badge rounded-pill bg-soft-info px-3 py-2 text-info fs-6">
                                                {{ number_format($bm->jumlah ?? 0, 0, ',', '.') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        {{-- Harga Dasar (Kecil/Muted) --}}
                                        <div class="text-muted small"
                                            style="text-decoration: line-through; font-size: 11px;">
                                            Dasar: Rp {{ number_format($bm->harga_dasar_satuan, 0, ',', '.') }}
                                        </div>

                                        {{-- Harga Final (Besar/Tebal) --}}
                                        <div class="fw-bold text-success">
                                            Rp {{ number_format($bm->harga_final_satuan, 0, ',', '.') }}
                                        </div>

                                        {{-- Info Total --}}
                                        <div class="text-muted" style="font-size: 10px;">
                                            Total: Rp {{ number_format($bm->harga_final_total, 0, ',', '.') }}
                                            (+{{ $bm->markup_percent }}%)
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-dark small">{{ $bm->created_at?->translatedFormat('d M Y') }}
                                        </div>
                                        <div class="text-muted small">{{ $bm->created_at?->format('H:i') }}</div>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <div class="mt-1">
                                            @if ($bm->jenis == 'tambahan')
                                                <span class="badge bg-primary-subtle text-primary border-0 small"
                                                    style="font-size: 10px;">TAMBAHAN</span>
                                            @else
                                                <span class="badge bg-success-subtle text-success border-0 small"
                                                    style="font-size: 10px;">RENCANA</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="80"
                                            class="mb-3 opacity-25">
                                        <h6 class="text-muted">Tidak ada data barang masuk ditemukan</h6>
                                        <p class="text-muted small">Coba ubah filter atau status pencarian Anda</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($barangMasuk->hasPages())
                    <div class="card-footer bg-white border-0 py-4">
                        {{ $barangMasuk->links() }}
                    </div>
                @endif
            </form>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAll = document.getElementById('select-all');
            // Filter: hanya ambil checkbox yang tidak disabled
            const checkboxes = document.querySelectorAll('.item-checkbox:not(:disabled)');
            const btnSubmit = document.getElementById('btn-submit-all');

            function updateButtonState() {
                // Hanya hitung yang dicentang DAN tidak disabled
                const checkedCount = document.querySelectorAll('.item-checkbox:checked:not(:disabled)').length;

                btnSubmit.disabled = checkedCount === 0;
                if (checkedCount > 0) {
                    btnSubmit.innerHTML =
                        `<i class="fas fa-check-circle me-2"></i>Konfirmasi (${checkedCount}) Barang`;
                } else {
                    btnSubmit.innerHTML = `<i class="fas fa-check-circle me-2"></i>Konfirmasi Terpilih`;
                }
            }

            selectAll?.addEventListener('change', function() {
                checkboxes.forEach(cb => {
                    cb.checked = this.checked;
                });
                updateButtonState();
            });

            checkboxes.forEach(cb => {
                cb.addEventListener('change', updateButtonState);
            });
        });
    </script>
@endsection

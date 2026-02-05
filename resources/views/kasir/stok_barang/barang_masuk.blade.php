@extends('layouts.app')

@section('title', 'Notifikasi Barang Masuk')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="{{ url('/kasir/stok-barang') }}" class="text-decoration-none text-muted">Stok Barang</a></li>
                    <li class="breadcrumb-item active fw-bold text-primary">Barang Masuk</li>
                </ol>
            </nav>
            <h2 class="fw-bold text-dark mb-0">Verifikasi Barang Masuk</h2>
            <p class="text-muted small">Konfirmasi barang yang baru datang untuk memperbarui stok warung secara otomatis.</p>
        </div>
    </div>

    {{-- Pesan Sukses atau Error --}}
    @if(session('success'))
    <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show mb-4 rounded-4" role="alert">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger border-0 shadow-sm alert-dismissible fade show mb-4 rounded-4" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        {{-- Form pembungkus tabel --}}
        <form action="{{ route('kasir.barang-masuk.konfirmasi') }}" method="POST" id="formVerifikasi">
            @csrf

            <div class="card-header bg-white py-4 px-4 border-0 border-bottom">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div class="text-muted small fw-bold text-uppercase">
                        <i class="fas fa-list me-2"></i>Daftar Tunggu Konfirmasi
                    </div>

                    {{-- Tombol aksi --}}
                    @if($barangMasuk->count() > 0)
                    <div class="d-flex gap-2">
                        <button type="submit" name="status_baru" value="tolak"
                                class="btn btn-outline-danger btn-sm rounded-pill px-4"
                                onclick="return confirm('Apakah Anda yakin ingin menolak item terpilih?')">
                            <i class="fas fa-times-circle me-1"></i> Tolak
                        </button>
                        <button type="submit" name="status_baru" value="terima"
                                class="btn btn-success btn-sm rounded-pill px-4 shadow-sm fw-bold">
                            <i class="fas fa-check-circle me-1"></i> Terima & Masukkan Stok
                        </button>
                    </div>
                    @endif
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr class="small fw-bold text-muted text-uppercase">
                                <th width="60" class="text-center ps-4">
                                    <div class="form-check d-flex justify-content-center">
                                        <input class="form-check-input" type="checkbox" id="select-all">
                                    </div>
                                </th>
                                <th width="50">#</th>
                                <th>Informasi Barang</th>
                                <th class="text-center">Jumlah</th>
                                <th>Tanggal Masuk</th>
                                <th class="pe-4">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($barangMasuk as $bm)
                            <tr class="hover-row">
                                <td class="text-center ps-4">
                                    <div class="form-check d-flex justify-content-center">
                                        <input class="form-check-input item-checkbox" type="checkbox" name="barangMasuk[]" value="{{ $bm->id }}">
                                    </div>
                                </td>
                                <td class="text-muted small">
                                    {{ $loop->iteration + ($barangMasuk->currentPage() - 1) * $barangMasuk->perPage() }}
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $bm->stokWarung->barang->nama_barang ?? '-' }}</div>
                                    <div class="text-muted small">Ref ID: #BM-{{ $bm->id }}</div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-soft-primary text-primary rounded-pill px-3 py-2 fw-bold fs-6">
                                        +{{ $bm->jumlah }}
                                    </span>
                                </td>
                                <td>
                                    <div class="small text-dark">{{ $bm->created_at?->format('d M Y') ?? '-' }}</div>
                                    <div class="text-muted small" style="font-size: 11px;">{{ $bm->created_at?->format('H:i') }} WIB</div>
                                </td>
                                <td class="pe-4">
                                    <div class="p-2 rounded bg-light small text-muted border-start border-3 border-warning">
                                        {{ $bm->keterangan ?? 'Tanpa keterangan' }}
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="mb-3">
                                        <i class="fas fa-clipboard-check fa-3x text-light"></i>
                                    </div>
                                    <h5 class="text-muted fw-normal">Semua barang sudah terverifikasi!</h5>
                                    <p class="small text-muted">Belum ada notifikasi barang masuk yang perlu dikonfirmasi saat ini.</p>
                                    <a href="{{ url('/kasir/stok-barang') }}" class="btn btn-outline-primary btn-sm rounded-pill px-4 mt-2">
                                        <i class="fas fa-arrow-left me-1"></i> Kembali ke Stok
                                    </a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($barangMasuk->hasPages())
            <div class="card-footer bg-white border-0 py-4 d-flex justify-content-center">
                {{ $barangMasuk->links() }}
            </div>
            @endif
        </form>
    </div>
</div>

<style>
    /* Styling khusus verifikasi */
    .bg-soft-primary { background-color: #e7f1ff; color: #0d6efd; }
    .hover-row:hover { background-color: #f8faff; transition: 0.2s; }
    .form-check-input { cursor: pointer; border-color: #dee2e6; }
    .form-check-input:checked { background-color: #198754; border-color: #198754; }

    /* Breadcrumb styling */
    .breadcrumb-item + .breadcrumb-item::before { content: "›"; font-size: 1.2rem; line-height: 1; vertical-align: middle; }

    /* Responsive adjustment */
    @media (max-width: 768px) {
        .card-header .d-flex { flex-direction: column; align-items: stretch !important; }
        .card-header .d-flex .d-flex { justify-content: space-between; }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('select-all');
        const checkboxes = document.querySelectorAll('.item-checkbox');

        if (selectAll) {
            selectAll.addEventListener('change', function() {
                checkboxes.forEach(cb => {
                    cb.checked = this.checked;
                    cb.closest('tr').classList.toggle('bg-light', this.checked);
                });
            });
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                this.closest('tr').classList.toggle('bg-light', this.checked);
            });
        });
    });
</script>
@endsection

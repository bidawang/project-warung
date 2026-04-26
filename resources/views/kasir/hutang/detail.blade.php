@extends('layouts.app')

@section('title', 'Detail Hutang Pelanggan')

@section('content')
<div class="container-fluid py-3 py-md-4" style="background-color:#f8f9fc; min-height:100vh;">

    {{-- HEADER --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h3 class="fw-bold text-primary mb-1 fs-4">Detail Hutang</h3>
            <p class="text-muted small mb-0">
                <i class="fas fa-user-circle me-1"></i> {{ $pelanggan->name ?? 'Pelanggan Umum' }}
            </p>
        </div>
        <a href="{{ route('kasir.hutang.index') }}" class="btn btn-white btn-sm rounded-pill px-3 shadow-sm border text-muted">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>

    {{-- NOTIFIKASI --}}
    @if(session('success') || session('info'))
        <div class="alert alert-{{ session('success') ? 'success' : 'info' }} border-0 shadow-sm rounded-4 mb-4 small animate__animated animate__fadeInDown">
            <i class="fas {{ session('success') ? 'fa-check-circle' : 'fa-info-circle' }} me-2"></i>
            {{ session('success') ?? session('info') }}
        </div>
    @endif

    {{-- RINGKASAN KARTU (Horizontal Scroll on Mobile) --}}
    <div class="row g-3 mb-4 flex-nowrap overflow-auto pb-2 pb-md-0 flex-md-wrap" style="-webkit-overflow-scrolling: touch;">
        <div class="col-10 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3">
                    <small class="text-muted d-block mb-1">Total Hutang Awal</small>
                    <h5 class="fw-bold mb-0 text-dark">
                        Rp {{ number_format($hutangList->sum('jumlah_hutang_awal'), 0, ',', '.') }}
                    </h5>
                </div>
            </div>
        </div>

        <div class="col-10 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 bg-danger text-white h-100">
                <div class="card-body p-3">
                    <small class="text-white-50 d-block mb-1">Total Sisa Hutang</small>
                    <h5 class="fw-bold mb-0">
                        Rp {{ number_format($hutangList->sum('jumlah_sisa_hutang'), 0, ',', '.') }}
                    </h5>
                </div>
            </div>
        </div>

        <div class="col-10 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 border-start border-warning border-4">
                <div class="card-body p-3">
                    <small class="text-muted d-block mb-1">Status Keaktifan</small>
                    <h5 class="fw-bold mb-0 text-dark">
                        {{ $hutangList->where('status', '!=', 'lunas')->count() }} Tagihan Aktif
                    </h5>
                </div>
            </div>
        </div>
    </div>

    {{-- LIST DATA --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white py-3 border-bottom-0">
            <h6 class="fw-bold mb-0"><i class="fas fa-history me-2 text-primary"></i>Riwayat Transaksi</h6>
        </div>

        {{-- DESKTOP VIEW --}}
        <div class="table-responsive d-none d-md-block">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 small text-muted uppercase">ID Transaksi</th>
                        <th class="small text-muted uppercase">Tanggal & Tenggat</th>
                        <th class="small text-muted uppercase">Hutang Awal</th>
                        <th class="small text-muted uppercase">Sisa Hutang</th>
                        <th class="small text-muted uppercase text-center">Status</th>
                        <th class="text-end pe-4 small text-muted uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($hutangList as $hutang)
                    <tr>
                        <td class="ps-4">
                            <span class="fw-bold text-dark">#H-{{ $hutang->id }}</span>
                        </td>
                        <td>
                            <div class="small text-dark">{{ \Carbon\Carbon::parse($hutang->created_at)->format('d/m/Y') }}</div>
                            <small class="{{ $hutang->status != 'lunas' && \Carbon\Carbon::parse($hutang->tenggat)->isPast() ? 'text-danger fw-bold' : 'text-muted' }}">
                                <i class="far fa-calendar-alt me-1"></i>Tenggat: {{ \Carbon\Carbon::parse($hutang->tenggat)->format('d/m/Y') }}
                            </small>
                        </td>
                        <td class="text-muted small">Rp {{ number_format($hutang->jumlah_hutang_awal, 0, ',', '.') }}</td>
                        <td class="fw-bold text-dark">Rp {{ number_format($hutang->jumlah_sisa_hutang, 0, ',', '.') }}</td>
                        <td class="text-center">
                            <span class="badge {{ $hutang->status == 'lunas' ? 'badge-soft-success' : 'badge-soft-danger' }} rounded-pill px-3">
                                {{ ucfirst($hutang->status) }}
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end gap-2">
                                @if ($hutang->status != 'lunas')
                                    <button class="btn btn-sm btn-success rounded-pill px-3 fw-bold shadow-sm"
                                            data-bs-toggle="modal" data-bs-target="#modalBayar"
                                            data-id="{{ $hutang->id }}" data-sisa="{{ $hutang->jumlah_sisa_hutang }}">
                                        Bayar
                                    </button>
                                @endif
                                <a href="{{ route('kasir.hutang.show', $hutang->id) }}" class="btn btn-sm btn-light border rounded-pill px-3">Detail</a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-5">Tidak ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- MOBILE VIEW --}}
        <div class="d-md-none">
            @forelse ($hutangList as $hutang)
            <div class="p-3 border-bottom {{ $hutang->status != 'lunas' && \Carbon\Carbon::parse($hutang->tenggat)->isPast() ? 'bg-light-danger' : '' }}">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="badge bg-light text-dark border small">#H-{{ $hutang->id }}</span>
                    <span class="badge {{ $hutang->status == 'lunas' ? 'badge-soft-success' : 'badge-soft-danger' }} rounded-pill px-2">
                        {{ ucfirst($hutang->status) }}
                    </span>
                </div>

                <div class="row g-0 mb-3">
                    <div class="col-6">
                        <small class="text-muted d-block">Tgl Transaksi</small>
                        <span class="small fw-bold">{{ \Carbon\Carbon::parse($hutang->created_at)->format('d M Y') }}</span>
                    </div>
                    <div class="col-6 text-end">
                        <small class="text-muted d-block">Jatuh Tempo</small>
                        <span class="small fw-bold {{ $hutang->status != 'lunas' && \Carbon\Carbon::parse($hutang->tenggat)->isPast() ? 'text-danger' : '' }}">
                            {{ \Carbon\Carbon::parse($hutang->tenggat)->format('d M Y') }}
                        </span>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center bg-white border rounded-3 p-2 mb-3 shadow-sm">
                    <div class="text-start">
                        <small class="text-muted d-block" style="font-size: 0.7rem;">Sisa Hutang</small>
                        <span class="fw-bold text-danger">Rp {{ number_format($hutang->jumlah_sisa_hutang, 0, ',', '.') }}</span>
                    </div>
                    <div class="text-end">
                        <small class="text-muted d-block" style="font-size: 0.7rem;">Total Awal</small>
                        <span class="text-muted small text-decoration-line-through">Rp {{ number_format($hutang->jumlah_hutang_awal, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="row g-2">
                    <div class="col-{{ $hutang->status != 'lunas' ? '8' : '12' }}">
                        <a href="{{ route('kasir.hutang.show', $hutang->id) }}" class="btn btn-outline-primary w-100 btn-sm rounded-pill fw-bold">
                            Rincian Transaksi
                        </a>
                    </div>
                    @if ($hutang->status != 'lunas')
                    <div class="col-4">
                        <button class="btn btn-success w-100 btn-sm rounded-pill fw-bold shadow-sm"
                                data-bs-toggle="modal" data-bs-target="#modalBayar"
                                data-id="{{ $hutang->id }}" data-sisa="{{ $hutang->jumlah_sisa_hutang }}">
                            Bayar
                        </button>
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <div class="text-center py-5">
                <p class="text-muted">Tidak ada riwayat hutang.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

{{-- MODAL BAYAR (Mobile Friendly) --}}
<div class="modal fade" id="modalBayar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm-fullscreen">
        <div class="modal-content border-0 shadow rounded-4-mobile">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Bayar Hutang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formBayar" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="bg-light rounded-4 p-4 text-center mb-4 border">
                        <small class="text-muted d-block mb-1">Tagihan Tersisa</small>
                        <h3 class="fw-bold text-danger mb-0" id="display_sisa">Rp 0</h3>
                    </div>

                    <div class="form-group">
                        <label class="form-label small fw-bold text-dark">Nominal Bayar (Rp)</label>
                        <input type="number" name="jumlah_bayar" id="input_bayar" 
                               class="form-control form-control-lg bg-white border-primary shadow-none fw-bold text-primary"
                               required min="1" placeholder="Masukkan jumlah...">
                        <div class="d-flex justify-content-between mt-2">
                            <button type="button" class="btn btn-sm btn-link text-decoration-none p-0" onclick="setFullPayment()">Bayar Lunas</button>
                            <small class="text-muted" style="font-size: 0.7rem;">*Input tanpa titik/koma</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 flex-grow-1 flex-md-grow-0 fw-bold shadow">
                        Simpan Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* CSS Khusus Mobile */
@media (max-width: 576px) {
    .container-fluid { padding-left: 10px; padding-right: 10px; }
    .fs-4 { font-size: 1.15rem !important; }
    .rounded-4-mobile { border-radius: 20px 20px 0 0 !important; }
    .modal-dialog.modal-sm-fullscreen {
        margin: 0; position: fixed; bottom: 0; width: 100%;
    }
    .modal-content { border-radius: 25px 25px 0 0 !important; }
    .bg-light-danger { background-color: #fff8f8 !important; }
}

.uppercase { text-transform: uppercase; letter-spacing: .8px; font-size: 0.65rem !important; font-weight: 700; }
.badge-soft-danger { background:#fee2e2; color:#b91c1c; }
.badge-soft-success { background:#dcfce7; color:#15803d; }
.btn-white { background: #fff; color: #64748b; }
</style>

<script>
    let currentSisa = 0;

    document.addEventListener('DOMContentLoaded', function () {
        const modalBayar = document.getElementById('modalBayar');
        const formBayar = document.getElementById('formBayar');
        const displaySisa = document.getElementById('display_sisa');
        const inputBayar = document.getElementById('input_bayar');

        modalBayar.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const sisa = button.getAttribute('data-sisa');
            currentSisa = sisa;

            formBayar.action = `/kasir/hutang/bayar/${id}`;
            displaySisa.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(sisa);
            inputBayar.max = sisa;
            inputBayar.value = sisa; 
            
            // Auto focus input di mobile
            setTimeout(() => inputBayar.focus(), 500);
        });
    });

    function setFullPayment() {
        document.getElementById('input_bayar').value = currentSisa;
    }
</script>
@endsection 
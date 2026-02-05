@extends('layouts.app')

@section('title', 'Detail Hutang Pelanggan')

@section('content')
<div class="container py-4" style="background-color:#f8f9fc; min-height:100vh;">

    {{-- HEADER --}}
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <h3 class="fw-bold text-primary mb-1">
                Detail Hutang Pelanggan
            </h3>
            <p class="text-muted mb-0">
                {{ $pelanggan->name ?? 'Pelanggan Umum' }}
            </p>
        </div>

        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <a href="{{ route('kasir.hutang.index') }}"
               class="btn btn-light rounded-pill px-4 shadow-sm border">
                ← Kembali
            </a>
        </div>
    </div>

    {{-- NOTIFIKASI --}}
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">
            {{ session('success') }}
        </div>
    @endif
    @if(session('info'))
        <div class="alert alert-info border-0 shadow-sm rounded-4 mb-4">
            {{ session('info') }}
        </div>
    @endif

    {{-- RINGKASAN --}}
    <div class="row mb-4">
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <small class="text-muted d-block">Total Hutang Awal</small>
                    <h4 class="fw-bold mb-0">
                        Rp {{ number_format($hutangList->sum('jumlah_hutang_awal'), 0, ',', '.') }}
                    </h4>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3 mb-md-0">
            <div class="card border-0 shadow-sm rounded-4 bg-danger text-white">
                <div class="card-body">
                    <small class="text-white-50 d-block">Sisa Hutang</small>
                    <h4 class="fw-bold mb-0">
                        Rp {{ number_format($hutangList->sum('jumlah_sisa_hutang'), 0, ',', '.') }}
                    </h4>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <small class="text-muted d-block">Jumlah Transaksi</small>
                    <h4 class="fw-bold mb-0">
                        {{ $hutangList->count() }} Transaksi
                    </h4>
                </div>
            </div>
        </div>
    </div>

    {{-- TABLE HUTANG --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 small text-muted uppercase">ID</th>
                        <th class="small text-muted uppercase">Tanggal</th>
                        <th class="small text-muted uppercase">Tenggat</th>
                        <th class="small text-muted uppercase">Hutang Awal</th>
                        <th class="small text-muted uppercase">Sisa Hutang</th>
                        <th class="small text-muted uppercase">Status</th>
                        <th class="text-end pe-4 small text-muted uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($hutangList as $hutang)
                    <tr>
                        <td class="ps-4 fw-bold">
                            #H-{{ $hutang->id }}
                        </td>

                        <td>
                            {{ \Carbon\Carbon::parse($hutang->created_at)->format('d M Y') }}
                        </td>

                        <td>
                            <span class="{{ $hutang->status != 'lunas' && \Carbon\Carbon::parse($hutang->tenggat)->isPast() ? 'text-danger fw-bold' : '' }}">
                                {{ \Carbon\Carbon::parse($hutang->tenggat)->format('d M Y') }}
                            </span>
                        </td>

                        <td>
                            Rp {{ number_format($hutang->jumlah_hutang_awal, 0, ',', '.') }}
                        </td>

                        <td class="fw-bold">
                            Rp {{ number_format($hutang->jumlah_sisa_hutang, 0, ',', '.') }}
                        </td>

                        <td>
                            @if ($hutang->status == 'lunas')
                                <span class="badge badge-soft-success rounded-pill px-3">
                                    Lunas
                                </span>
                            @else
                                <span class="badge badge-soft-danger rounded-pill px-3">
                                    Belum Lunas
                                </span>
                            @endif
                        </td>

                        <td class="text-end pe-4">
                            <div class="btn-group">
                                {{-- Tombol Bayar (Hanya jika belum lunas) --}}
                                @if ($hutang->status != 'lunas')
                                    <button type="button"
                                            class="btn btn-sm btn-success rounded-pill px-3 me-2"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalBayar"
                                            data-id="{{ $hutang->id }}"
                                            data-sisa="{{ $hutang->jumlah_sisa_hutang }}">
                                        Bayar
                                    </button>
                                @endif
                                <a href="{{ route('kasir.hutang.show', $hutang->id) }}"
                                   class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    Detail
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            Tidak ada data hutang
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL BAYAR --}}
<div class="modal fade" id="modalBayar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Pembayaran Hutang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            {{-- Form Action akan diisi lewat JavaScript --}}
            <form id="formBayar" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <small class="text-muted d-block mb-1">Sisa yang harus dibayar</small>
                        <h2 class="fw-bold text-danger" id="display_sisa">Rp 0</h2>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label small fw-bold">Nominal Pembayaran</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0">Rp</span>
                            <input type="number"
                                   name="jumlah_bayar"
                                   id="input_bayar"
                                   class="form-control bg-light border-0 rounded-end shadow-none"
                                   required
                                   min="1">
                        </div>
                        <small class="text-muted mt-2 d-block">
                            *Maksimal pembayaran sesuai sisa hutang.
                        </small>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow">Simpan Pembayaran</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.uppercase { text-transform: uppercase; letter-spacing: .5px; font-size: 0.75rem; }
.badge-soft-danger { background:#fff5f5; color:#e53e3e; }
.badge-soft-success { background:#f0fff4; color:#38a169; }
.btn-group .btn { transition: all 0.2s; }
.btn-group .btn:hover { transform: translateY(-1px); }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modalBayar = document.getElementById('modalBayar');
        const formBayar = document.getElementById('formBayar');
        const displaySisa = document.getElementById('display_sisa');
        const inputBayar = document.getElementById('input_bayar');

        modalBayar.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const sisa = button.getAttribute('data-sisa');

            // 1. Update Action URL Form (Sesuaikan dengan route name kamu)
            // Route ini akan menghasilkan: /kasir/hutang/bayar/{id}
            formBayar.action = `/kasir/hutang/bayar/${id}`;

            // 2. Tampilan Sisa Hutang
            displaySisa.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(sisa);

            // 3. Set Input Constraints
            inputBayar.max = sisa;
            inputBayar.value = sisa; // Default lunas
        });
    });
</script>
@endsection

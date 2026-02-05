@extends('layouts.app')

@section('title', 'Riwayat Transaksi')

@section('content')
<div class="container-fluid py-4" style="background-color: #f4f7fe; min-height: 100vh;">

    {{-- Ringkasan Statistik Singkat --}}
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 text-white" style="background: linear-gradient(45deg, #4e73df, #224abe);">
                <div class="card-body p-3">
                    <small class="opacity-75">Total Transaksi Hari Ini</small>
                    <h4 class="fw-bold mb-0">{{ $riwayatTransaksi->total() }} Transaksi</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        {{-- HEADER: Menggunakan warna Biru Indigo yang soft --}}
        <div class="card-header bg-white border-0 py-3 mt-2">
            <div class="row align-items-center g-3">
                <div class="col-md-4">
                    <h5 class="mb-0 fw-bold text-primary">
                        <i class="fas fa-list-ul me-2"></i>Riwayat Transaksi
                    </h5>
                </div>
                <div class="col-md-8">
                    <form method="GET" class="d-flex gap-2 justify-content-md-end">
                        <select name="filter_jenis" class="form-select form-select-sm border-light-subtle shadow-sm w-auto rounded-pill">
                            <option value="">Semua Kategori</option>
                            <option value="Penjualan" {{ request('filter_jenis') == 'Penjualan' ? 'selected' : '' }}>Penjualan</option>
                            <option value="Piutang" {{ request('filter_jenis') == 'Piutang' ? 'selected' : '' }}>Piutang</option>
                            <option value="Keluar" {{ request('filter_jenis') == 'Keluar' ? 'selected' : '' }}>Pengeluaran</option>
                        </select>

                        <div class="input-group input-group-sm shadow-sm rounded-pill overflow-hidden" style="max-width: 250px;">
                            <input type="text" name="search" class="form-control border-0 ps-3"
                                   placeholder="Cari transaksi..." value="{{ request('search') }}">
                            <button class="btn btn-primary px-3" type="submit"><i class="fas fa-search"></i></button>
                        </div>

                        @if(request()->anyFilled(['search', 'filter_jenis']))
                            <a href="{{ route('kasir.riwayat-transaksi.index') }}" class="btn btn-sm btn-light rounded-pill border shadow-sm">
                                <i class="fas fa-sync-alt"></i>
                            </a>
                        @endif
                    </form>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-primary small fw-bold text-uppercase">
                        <tr>
                            <th class="ps-4 py-3 border-0">ID Ref</th>
                            <th class="border-0">Waktu</th>
                            <th class="border-0 text-center">Jenis</th>
                            <th class="border-0">Deskripsi</th>
                            <th class="border-0 text-end">Total</th>
                            <th class="border-0 text-center">Metode</th>
                            <th class="border-0 text-center pe-4">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="border-top-0">
                        @forelse($riwayatTransaksi as $trx)
                        <tr>
                            <td class="ps-4">
                                <span class="badge bg-primary-subtle text-primary fw-medium px-2 py-1">
                                    #{{ $trx->id_ref }}
                                </span>
                            </td>

                            <td>
                                <div class="fw-bold text-dark mb-0">{{ $trx->tanggal->format('d M Y') }}</div>
                                <div class="small text-muted">{{ $trx->tanggal->format('H:i') }} WIB</div>
                            </td>

                            <td class="text-center">
                                @php
                                    $jt = $trx->jenis_transaksi;
                                    $style = 'bg-secondary-subtle text-secondary';
                                    if(str_contains($jt, 'Penjualan')) $style = 'bg-success-subtle text-success';
                                    elseif(str_contains($jt, 'Piutang')) $style = 'bg-warning-subtle text-warning-emphasis';
                                    elseif(str_contains($jt, 'Keluar')) $style = 'bg-danger-subtle text-danger';
                                @endphp
                                <span class="badge {{ $style }} border-0 px-3 py-2 rounded-pill shadow-none" style="min-width: 90px;">
                                    {{ $jt }}
                                </span>
                            </td>

                            <td class="small text-muted">
                                {{ Str::limit($trx->deskripsi, 40) }}
                            </td>

                            <td class="text-end fw-bold">
                                <span class="{{ $trx->total >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $trx->total >= 0 ? '+' : '-' }} Rp {{ number_format(abs($trx->total),0,',','.') }}
                                </span>
                            </td>

                            <td class="text-center">
                                <span class="small text-dark fw-medium bg-light px-2 py-1 rounded border">
                                    {{ strtoupper($trx->metode_pembayaran ?? 'N/A') }}
                                </span>
                            </td>

                            <td class="text-center pe-4">
                                <button class="btn btn-sm btn-outline-primary rounded-circle"
                                        data-bs-toggle="modal"
                                        data-bs-target="#struk{{ $trx->id_ref }}"
                                        style="width: 32px; height: 32px; padding: 0;">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <p class="text-muted">Data transaksi tidak ditemukan.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer bg-white border-0 py-3 rounded-bottom-4">
            {{ $riwayatTransaksi->links() }}
        </div>
    </div>
</div>

{{-- MODAL STRUK: Desain Bersih ala Apple/Modern UI --}}
@foreach($riwayatTransaksi as $trx)
<div class="modal fade" id="struk{{ $trx->id_ref }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <div class="bg-primary-subtle text-primary d-inline-block rounded-circle p-3 mb-2">
                        <i class="fas fa-store fa-lg"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-0">WARUNG DIGITAL</h6>
                    <small class="text-muted">Nota Transaksi Digital</small>
                </div>

                <div class="d-flex justify-content-between small text-muted mb-1">
                    <span>ID Ref</span>
                    <span class="text-dark fw-medium">#{{ $trx->id_ref }}</span>
                </div>
                <div class="d-flex justify-content-between small text-muted mb-3">
                    <span>Waktu</span>
                    <span class="text-dark fw-medium">{{ $trx->tanggal->format('d/m/Y H:i') }}</span>
                </div>

                <div style="border-top: 1px dashed #dee2e6;" class="my-3"></div>

                @foreach($trx->items ?? [] as $item)
                <div class="mb-2">
                    <div class="d-flex justify-content-between small fw-bold text-dark">
                        <span>{{ $item->nama_barang }}</span>
                        <span>{{ number_format($item->subtotal,0,',','.') }}</span>
                    </div>
                    <div class="small text-muted">{{ $item->jumlah }} x {{ number_format($item->harga,0,',','.') }}</div>
                </div>
                @endforeach

                <div style="border-top: 1px dashed #dee2e6;" class="my-3"></div>

                <div class="d-flex justify-content-between fw-bold text-primary mb-1">
                    <span>TOTAL</span>
                    <span>Rp {{ number_format($trx->total,0,',','.') }}</span>
                </div>

                <div class="text-center mt-4">
                    <button class="btn btn-primary w-100 rounded-pill mb-2" onclick="window.print()">
                        <i class="fas fa-print me-2"></i>Cetak Struk
                    </button>
                    <button class="btn btn-link btn-sm text-decoration-none text-muted" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach

<style>
    /* Menghilangkan garis hitam tabel */
    .table > :not(caption) > * > * {
        border-bottom-width: 1px;
        border-bottom-color: #f0f2f5;
        box-shadow: none;
    }
    .bg-success-subtle { background-color: #e8fadf !important; color: #28a745 !important; }
    .bg-primary-subtle { background-color: #eef2ff !important; color: #4e73df !important; }
    .bg-danger-subtle { background-color: #ffeef0 !important; color: #dc3545 !important; }
    .bg-warning-subtle { background-color: #fff8ec !important; color: #b07d05 !important; }

    .pagination { justify-content: flex-end; margin-bottom: 0; }
    .page-link { border: none; color: #4e73df; border-radius: 8px; margin: 0 2px; }
    .page-item.active .page-link { background-color: #4e73df; border-radius: 8px; }
</style>
@endsection

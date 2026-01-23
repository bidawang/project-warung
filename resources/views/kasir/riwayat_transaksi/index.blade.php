@extends('layouts.app')

@section('title', 'Riwayat Transaksi')

@section('content')
<div class="container-fluid">
    <div class="card border border-dark shadow-sm">

        {{-- HEADER --}}
        <div class="card-header bg-white border-bottom border-dark d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-dark">
                RIWAYAT TRANSAKSI KEUANGAN
            </h5>

            {{-- SEARCH --}}
            <form method="GET" class="d-flex gap-2">
                <input type="text"
                       name="search"
                       class="form-control form-control-sm border-dark"
                       placeholder="Cari transaksi / barang"
                       value="{{ request('search') }}">
                <button class="btn btn-sm btn-dark">Cari</button>
                @if(request('search'))
                    <a href="{{ route('kasir.riwayat-transaksi.index') }}"
                       class="btn btn-sm btn-outline-dark">Reset</a>
                @endif
            </form>
        </div>

        {{-- BODY --}}
        <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0 align-middle">
                    <thead class="table-dark text-center">
                        <tr>
                            <th style="width:110px">REF</th>
                            <th style="width:160px">WAKTU</th>
                            <th style="width:160px">JENIS</th>
                            <th>DESKRIPSI</th>
                            <th style="width:150px">TOTAL</th>
                            <th style="width:120px">METODE</th>
                            <th style="width:90px">AKSI</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($riwayatTransaksi as $trx)
                        <tr>
                            <td class="text-center fw-bold text-muted">
                                {{ $trx->id_ref }}
                            </td>

                            <td>
                                <div class="small text-muted">
                                    {{ $trx->tanggal->format('d M Y') }}
                                </div>
                                <div class="fw-bold">
                                    {{ $trx->tanggal->format('H:i') }}
                                </div>
                            </td>

                            <td class="text-center">
                                <span class="badge px-3 py-2
                                    @if(str_contains($trx->jenis_transaksi,'Penjualan') || str_contains($trx->jenis_transaksi,'Masuk')) bg-success
                                    @elseif(str_contains($trx->jenis_transaksi,'Piutang')) bg-warning text-dark
                                    @elseif(str_contains($trx->jenis_transaksi,'Keluar') || str_contains($trx->jenis_transaksi,'Kerugian')) bg-danger
                                    @else bg-secondary @endif">
                                    {{ $trx->jenis_transaksi }}
                                </span>
                            </td>

                            <td class="small">
                                {{ $trx->deskripsi }}
                            </td>

                            <td class="text-end fw-bold {{ $trx->total >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $trx->total >= 0 ? '+' : '-' }}
                                Rp {{ number_format(abs($trx->total),0,',','.') }}
                            </td>

                            <td class="text-center">
                                <span class="badge bg-dark">
                                    {{ strtoupper($trx->metode_pembayaran ?? 'N/A') }}
                                </span>
                            </td>

                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-dark"
                                        data-bs-toggle="modal"
                                        data-bs-target="#struk{{ $trx->id_ref }}">
                                    DETAIL
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                Tidak ada data transaksi
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>

        {{-- FOOTER --}}
        <div class="card-footer bg-white border-top border-dark">
            {{ $riwayatTransaksi->links() }}
        </div>

    </div>
</div>

{{-- =======================
     MODAL STRUK
======================= --}}
@foreach($riwayatTransaksi as $trx)
<div class="modal fade" id="struk{{ $trx->id_ref }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border border-dark">

            <div class="modal-body p-4">

                {{-- HEADER --}}
                <div class="text-center border-bottom border-dark pb-2 mb-3">
                    <h6 class="fw-bold mb-0 text-uppercase">WARUNG DIGITAL</h6>
                    <small class="text-muted">STRUK TRANSAKSI</small>
                </div>

                {{-- INFO --}}
                <div class="d-flex justify-content-between small mb-2">
                    <span>{{ $trx->id_ref }}</span>
                    <span>{{ $trx->tanggal->format('d M Y H:i') }}</span>
                </div>

                <hr class="border-dark border-dashed">

                {{-- ITEM --}}
                @foreach($trx->items ?? [] as $item)
                <div class="d-flex justify-content-between mb-1">
                    <div>
                        <strong>{{ $item->nama_barang }}</strong><br>
                        <small>{{ $item->jumlah }} x Rp {{ number_format($item->harga,0,',','.') }}</small>
                    </div>
                    <div class="fw-bold">
                        Rp {{ number_format($item->subtotal,0,',','.') }}
                    </div>
                </div>
                @endforeach

                <hr class="border-dark border-dashed">

                {{-- TOTAL --}}
                <div class="d-flex justify-content-between fw-bold mb-1">
                    <span>Total</span>
                    <span>Rp {{ number_format($trx->total,0,',','.') }}</span>
                </div>

                {{-- CASH --}}
                @if($trx->uang_dibayar !== null)
                <div class="d-flex justify-content-between small">
                    <span>Bayar</span>
                    <span>Rp {{ number_format($trx->uang_dibayar,0,',','.') }}</span>
                </div>
                <div class="d-flex justify-content-between fw-bold">
                    <span>Kembalian</span>
                    <span>Rp {{ number_format($trx->uang_kembalian,0,',','.') }}</span>
                </div>
                @endif

                {{-- PIUTANG --}}
                @if($trx->metode_pembayaran === 'Piutang')
                <div class="mt-3 p-2 border border-danger text-center fw-bold text-danger">
                    TRANSAKSI PIUTANG
                </div>
                @endif

                <hr class="border-dark">

                <p class="text-center small text-muted mb-0">
                    Terima kasih telah berbelanja
                </p>

            </div>

            <div class="modal-footer border-top border-dark">
                <button class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button class="btn btn-sm btn-dark">
                    Cetak
                </button>
            </div>

        </div>
    </div>
</div>
@endforeach
@endsection

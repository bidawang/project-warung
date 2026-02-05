@extends('layouts.app')

@section('title', 'Riwayat Barang Masuk')

@section('content')
<div class="container-fluid">
    <div class="card border border-dark shadow-sm">

        {{-- HEADER --}}
        <div class="card-header bg-white border-bottom border-dark d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-dark">RIWAYAT BARANG MASUK</h5>

            <form method="GET" class="d-flex gap-2">
                <input type="text"
                       name="search"
                       class="form-control form-control-sm border-dark"
                       placeholder="Cari barang"
                       value="{{ request('search') }}">
                <button class="btn btn-sm btn-dark">Cari</button>

                @if(request('search'))
                    <a href="{{ route('kasir.riwayat-barang-masuk.index') }}"
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
                            <th>REF</th>
                            <th>WAKTU</th>
                            <th>JENIS</th>
                            <th>BARANG</th>
                            <th>JUMLAH</th>
                            <th>SUBTOTAL</th>
                            <th>AKSI</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($riwayatBarangMasuk as $bm)
                        @php
                            $barang   = optional($bm->stokWarung->barang);
                            $harga    = $bm->transaksiBarang->harga ?? 0;
                            // $subtotal = $bm->$harga;
                        @endphp
                        <tr>
                            <td class="text-center fw-bold text-muted">
                                BM-{{ $bm->id }}
                            </td>

                            <td>
                                <div class="small text-muted">
                                    {{ $bm->created_at->format('d M Y') }}
                                </div>
                                <div class="fw-bold">
                                    {{ $bm->created_at->format('H:i') }}
                                </div>
                            </td>

                            <td class="text-center">
                                <span class="badge bg-success">
                                    {{ strtoupper($bm->jenis ?? 'MASUK') }}
                                </span>
                            </td>

                            <td>
                                <strong>{{ $barang->nama_barang ?? '-' }}</strong>
                            </td>

                            <td class="text-center fw-bold">
                                {{ $bm->jumlah }}
                            </td>

                            <td class="text-end fw-bold text-success">
                                Rp {{ number_format($harga,0,',','.') }}
                            </td>

                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-dark"
                                        data-bs-toggle="modal"
                                        data-bs-target="#bm{{ $bm->id }}">
                                    DETAIL
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                Tidak ada data barang masuk
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- FOOTER --}}
        <div class="card-footer bg-white border-top border-dark">
            {{ $riwayatBarangMasuk->links() }}
        </div>

    </div>
</div>

{{-- MODAL DETAIL --}}
@foreach($riwayatBarangMasuk as $bm)
@php
    $barang   = optional($bm->stokWarung->barang);
    $harga    = $bm->transaksiBarang->harga ?? 0;
@endphp
<div class="modal fade" id="bm{{ $bm->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border border-dark">
            <div class="modal-body p-4">

                <div class="text-center border-bottom border-dark pb-2 mb-3">
                    <h6 class="fw-bold mb-0">DETAIL BARANG MASUK</h6>
                </div>

                <p><strong>Barang:</strong> {{ $barang->nama_barang }}</p>
                <p><strong>Jumlah:</strong> {{ $bm->jumlah }}</p>
                <p><strong>Harga:</strong> Rp {{ number_format($harga,0,',','.') }}</p>
                <p><strong>Subtotal:</strong> Rp {{ number_format($bm->$harga,0,',','.') }}</p>
                <p><strong>Status:</strong> {{ strtoupper($bm->status ?? '-') }}</p>

                @if($bm->tanggal_kadaluarsa)
                    <p><strong>Kadaluarsa:</strong>
                        {{ \Carbon\Carbon::parse($bm->tanggal_kadaluarsa)->format('d M Y') }}
                    </p>
                @endif

            </div>
        </div>
    </div>
</div>
@endforeach
@endsection

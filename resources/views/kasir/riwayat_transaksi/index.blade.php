@extends('layouts.app')

@section('title', 'Riwayat Transaksi Keuangan')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card shadow-lg border-0 rounded-3">
            <div class="card-header bg-success text-white py-3 rounded-top-3">
                <h5 class="mb-0">Histori Semua Transaksi Warung</h5>
            </div>

            <div class="card-body p-4">
                {{-- Search Form --}}
                <form method="GET"
                    action="{{ url('/kasir/riwayat-transaksi') }}"
                    class="input-group mb-4 rounded-pill overflow-hidden shadow-sm">
                    <input type="text"
                            name="search"
                            class="form-control border-0 ps-3"
                            placeholder="Cari deskripsi transaksi, nama barang, atau metode pembayaran..."
                            value="{{ $search }}">
                    <button class="btn btn-success" type="submit">
                        <i class="fas fa-search me-1"></i> Cari
                    </button>
                    @if($search)
                        <a href="{{ url('/kasir/riwayat-transaksi') }}" class="btn btn-outline-secondary">
                            Reset
                        </a>
                    @endif
                </form>

                <hr>

                {{-- Table Riwayat Transaksi --}}
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead class="bg-light">
                            <tr>
                                <th scope="col" class="text-center">#</th>
                                <th scope="col" style="min-width: 150px;">Waktu Transaksi</th>
                                <th scope="col" style="min-width: 150px;">Jenis Transaksi</th>
                                <th scope="col">Deskripsi</th>
                                <th scope="col" class="text-end">Total (Rp)</th>
                                <th scope="col">Metode Pembayaran</th>
                                <th scope="col">Sumber Data</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($riwayatTransaksi as $transaksi)
                            <tr>
                                <th scope="row" class="text-center">
                                    {{ $loop->iteration + ($riwayatTransaksi->currentPage() - 1) * $riwayatTransaksi->perPage() }}
                                </th>
                                <td>
                                    <span class="text-muted">{{ \Carbon\Carbon::parse($transaksi->tanggal)->format('d/m/Y') }}</span>
                                    <br>
                                    <span class="fw-bold">{{ \Carbon\Carbon::parse($transaksi->tanggal)->format('H:i:s') }}</span>
                                </td>
                                <td>
                                    <span class="badge
                                        @if(str_contains($transaksi->jenis_transaksi, 'Penjualan') || str_contains($transaksi->jenis_transaksi, 'Masuk'))
                                            bg-success
                                        @elseif(str_contains($transaksi->jenis_transaksi, 'Keluar'))
                                            bg-danger
                                        @elseif(str_contains($transaksi->jenis_transaksi, 'Hutang'))
                                            bg-warning text-dark
                                        @else
                                            bg-secondary
                                        @endif
                                        ">
                                        {{ $transaksi->jenis_transaksi }}
                                    </span>
                                </td>
                                <td>{{ $transaksi->deskripsi }}</td>
                                <td class="text-end fw-bold">
                                    {{ number_format($transaksi->total, 0, ',', '.') }}
                                </td>
                                <td>{{ $transaksi->metode_pembayaran ?? 'Hutang' }}</td>
                                <td>
                                    <span class="badge bg-info text-dark">{{ $transaksi->tipe_sumber }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    Tidak ada riwayat transaksi yang ditemukan.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="d-flex justify-content-center mt-3">
                    {{ $riwayatTransaksi->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

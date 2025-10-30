@extends('layouts.app')

@section('title', 'Riwayat Transaksi Keuangan')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card shadow-lg border-0 rounded-3">
            <div class="card-header bg-primary text-white py-3 rounded-top-3">
                <h5 class="mb-0"><i class="fas fa-history me-2"></i>Histori Semua Transaksi Warung</h5>
            </div>

            <div class="card-body p-4">
                {{-- Search Form --}}
                <form method="GET"
                    action="{{ url('/kasir/riwayat-transaksi') }}"
                    class="input-group mb-4 rounded-pill overflow-hidden shadow-sm">
                    <input type="text"
                            name="search"
                            class="form-control border-0 ps-3"
                            placeholder="Cari deskripsi, barang, pulsa, atau metode pembayaran..."
                            value="{{ $search }}">
                    <button class="btn btn-primary" type="submit">
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
                    <table class="table table-hover table-striped table-sm align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th scope="col" class="text-center">Ref.</th>
                                <th scope="col" style="min-width: 150px;">Waktu Transaksi</th>
                                <th scope="col" style="min-width: 150px;">Jenis Transaksi</th>
                                <th scope="col">Deskripsi / Detail</th>
                                <th scope="col" class="text-end" style="min-width: 100px;">Total (Rp)</th>
                                <th scope="col" style="min-width: 130px;">Metode Bayar</th>
                                <th scope="col">Sumber</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($riwayatTransaksi as $transaksi)
                            <tr>
                                <th scope="row" class="text-center fw-normal text-muted small">
                                    {{-- Menggunakan id_ref untuk identifikasi unik --}}
                                    {{ $transaksi->id_ref }}
                                </th>
                                <td>
                                    <span class="text-muted">{{ \Carbon\Carbon::parse($transaksi->tanggal)->format('d M Y') }}</span>
                                    <br>
                                    <span class="fw-bold small">{{ \Carbon\Carbon::parse($transaksi->tanggal)->format('H:i') }}</span>
                                </td>
                                <td>
                                    {{-- Menggunakan badge untuk Jenis Transaksi --}}
                                    {{-- Disesuaikan agar mencakup 'Piutang', 'Penjualan', 'Pelunasan', 'Masuk', 'Keluar', 'Kerugian' --}}
                                    <span class="badge
                                        @if(str_contains($transaksi->jenis_transaksi, 'Penjualan') || str_contains($transaksi->jenis_transaksi, 'Pelunasan') || str_contains($transaksi->jenis_transaksi, 'Masuk'))
                                            bg-success
                                        @elseif(str_contains($transaksi->jenis_transaksi, 'Piutang') || str_contains($transaksi->jenis_transaksi, 'Hutang'))
                                            bg-warning text-dark
                                        @elseif(str_contains($transaksi->jenis_transaksi, 'Keluar') || str_contains($transaksi->jenis_transaksi, 'Kerugian'))
                                            bg-danger
                                        @else
                                            bg-secondary
                                        @endif
                                    ">
                                        {{ $transaksi->jenis_transaksi }}
                                    </span>
                                </td>
                                <td class="small">{{ $transaksi->deskripsi }}</td>

                                {{-- Kolom Total dengan format warna --}}
                                <td class="text-end fw-bold
                                    @if ($transaksi->total >= 0)
                                        text-success
                                    @else
                                        text-danger
                                    @endif">
                                    {{ ($transaksi->total < 0 ? '-' : '+') . number_format(abs($transaksi->total), 0, ',', '.') }}
                                </td>

                                <td>
                                    {{-- Menggunakan badge untuk Metode Pembayaran --}}
                                    <span class="badge
                                        @if(str_contains(strtolower($transaksi->metode_pembayaran), 'tunai') || str_contains(strtolower($transaksi->metode_pembayaran), 'cash'))
                                            bg-primary
                                        @elseif(str_contains(strtolower($transaksi->metode_pembayaran), 'piutang') || str_contains(strtolower($transaksi->metode_pembayaran), 'hutang'))
                                            bg-info text-dark
                                        @elseif(str_contains(strtolower($transaksi->metode_pembayaran), 'transfer'))
                                            bg-dark
                                        @else
                                            bg-secondary
                                        @endif
                                    ">
                                        {{ ucfirst(str_replace('_', ' ', $transaksi->metode_pembayaran ?? 'N/A')) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $transaksi->tipe_sumber }}</span>
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

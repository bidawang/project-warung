@extends('layouts.app')

@section('title', 'Data Hutang Barang Masuk')

@section('content')

<div class="container py-4">
    <div class="card shadow-lg border-0 rounded-4">
        <div class="card-header bg-warning text-white rounded-top-4 py-3">
            <h5 class="mb-0 fw-bold"><i class="fas fa-hand-holding-usd me-2"></i>Daftar Hutang Barang Masuk</h5>
        </div>
        <div class="card-body p-4">
            {{-- Form Filter --}}
            <form method="GET" class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                <div class="input-group" style="max-width: 350px;">
                    {{-- Asumsi 'q' digunakan untuk mencari Supplier atau Keterangan --}}
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control rounded-start-pill" placeholder="Cari berdasarkan Supplier/Keterangan...">
                    <button class="btn btn-warning text-white rounded-end-pill" type="submit"><i class="fas fa-search"></i></button>
                </div>
                <div class="btn-group" role="group">
                    <a href="{{ url()->current() . '?' . http_build_query(request()->except(['status'])) }}"
                        class="btn btn-outline-warning {{ request('status') == null ? 'active' : '' }}">
                        Semua
                    </a>
                    <a href="{{ url()->current() . '?' . http_build_query(array_merge(request()->except(['status']), ['status' => 'belum lunas'])) }}"
                        class="btn btn-outline-warning {{ request('status') == 'belum lunas' ? 'active' : '' }}">
                        Belum Lunas
                    </a>
                    <a href="{{ url()->current() . '?' . http_build_query(array_merge(request()->except(['status']), ['status' => 'lunas'])) }}"
                        class="btn btn-outline-warning {{ request('status') == 'lunas' ? 'active' : '' }}">
                        Lunas
                    </a>
                </div>
            </form>

            {{-- Pesan Sukses --}}
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead class="bg-light">
                        <tr>
                            <th scope="col">No</th>
                            <th scope="col">Jumlah Hutang</th>
                            <th scope="col">Tanggal Hutang</th>
                            <th scope="col">Status</th> {{-- Kolom Status --}}
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($hutangList as $index => $hutang)
                            <tr class="align-middle">
                                <td>{{ $loop->iteration + ($hutangList->firstItem() - 1) }}</td>
                                <td>Rp {{ number_format($hutang->total, 0, ',', '.') }}</td>
                                <td>{{ \Carbon\Carbon::parse($hutang->created_at)->format('d-m-Y') }}</td>
                                <td>
                                    @php
                                        // Asumsi kolom status ada di BarangMasuk
                                        $statusPembayaran = $hutang->status ?? 'belum lunas';
                                    @endphp

                                    @if($statusPembayaran == 'lunas')
                                        <span class="badge bg-success py-2 px-3">Lunas</span>
                                    @else
                                        <span class="badge bg-danger py-2 px-3">Belum Lunas</span>
                                    @endif
                                </td>
                                <td>
                                    @if($statusPembayaran != 'lunas')
                                        {{-- Mengarahkan ke form pembayaran hutang, asumsikan route: kasir.hutang.bayar.detail --}}
                                        <a href="{{ route('kasir.bayar.detail', ['id' => $hutang->id]) }}" class="btn btn-sm btn-success text-white rounded-pill px-3" title="Bayar Hutang"><i class="fas fa-money-bill-wave me-1"></i>Bayar</a>
                                    @else
                                        {{-- Jika sudah lunas, tampilkan tombol detail atau nonaktif --}}
                                        <button class="btn btn-sm btn-info text-white rounded-pill px-3" disabled><i class="fas fa-check-circle me-1"></i>Lunas</button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Tidak ada data hutang barang masuk.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $hutangList->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

</div>
@endsection

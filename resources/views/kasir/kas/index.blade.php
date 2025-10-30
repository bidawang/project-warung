@extends('layouts.app')

@section('title', 'Kas Warung')

@section('content')

<div class="container mt-4">
{{-- Notifikasi Sukses/Error --}}
@if (session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
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

<h3 class="mb-4">Ringkasan Kas Warung</h3>

<div class="row mb-4">
<div class="col-md-4">
<div class="card bg-success text-white shadow-sm">
<div class="card-body">
<div class="d-flex justify-content-between align-items-center">
<div>
<h5 class="card-title">Total Pendapatan</h5>
<h2 class="card-text">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h2>
</div>
<i class="fas fa-dollar-sign fa-2x"></i>
</div>
</div>
</div>
</div>
<div class="col-md-4">
<div class="card bg-danger text-white shadow-sm">
<div class="card-body">
<div class="d-flex justify-content-between align-items-center">
<div>
<h5 class="card-title">Total Pengeluaran</h5>
<h2 class="card-text">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</h2>
</div>
<i class="fas fa-shopping-cart fa-2x"></i>
</div>
</div>
</div>
</div>
<div class="col-md-4">
<div class="card bg-primary text-white shadow-sm">
<div class="card-body">
<div class="d-flex justify-content-between align-items-center">
<div>
<h5 class="card-title">Saldo Bersih</h5>
<h2 class="card-text">Rp {{ number_format($saldoBersih, 0, ',', '.') }}</h2>
</div>
<i class="fas fa-wallet fa-2x"></i>
</div>
</div>
</div>
</div>
</div>

<div class="card shadow-sm">
<div class="card-header d-flex justify-content-between align-items-center bg-info text-white">
<h5 class="mb-0">Riwayat Transaksi</h5>
<a href="{{ route('kasir.kas.create') }}" class="btn btn-light">
<i class="fas fa-plus-circle me-2"></i> Tambah Transaksi Manual
</a>
</div>
<div class="card-body">
<div class="input-group mb-4">
{{-- Form pencarian harus memiliki method GET agar sesuai standar --}}
<input type="text" class="form-control" placeholder="Cari keterangan transaksi..." name="search" value="{{ request('search') }}">
<button class="btn btn-outline-secondary" type="submit">Cari</button>
</div>

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Tanggal</th>
                    <th scope="col">Tipe</th>
                    <th scope="col">Keterangan</th>
                    <th scope="col">Metode</th>
                    <th scope="col" class="text-end">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($riwayatTransaksi as $transaksi)
                    @php
                        // Daftar jenis transaksi yang dianggap sebagai Pemasukan, sesuai dengan Controller.
                        $incomeTypes = ['penjualan barang', 'penjualan pulsa', 'masuk'];

                        $isPemasukan = in_array($transaksi->jenis, $incomeTypes);
                        $badgeClass = $isPemasukan ? 'bg-success' : 'bg-danger';
                        $tipeText = $isPemasukan ? 'Pemasukan' : 'Pengeluaran';
                        $sign = $isPemasukan ? '+' : '-';

                        // Mendapatkan teks yang lebih detail untuk kolom Keterangan (opsional, untuk memperjelas)
                        $detailTipe = [
                            'penjualan barang' => 'Penjualan Barang',
                            'penjualan pulsa' => 'Penjualan Pulsa',
                            'masuk' => 'Pemasukan Manual',
                            'expayet' => 'Barang Exp/Rusak',
                            'hilang' => 'Barang Hilang',
                            'keluar' => 'Pengeluaran Manual',
                            'hutang barang' => 'Bayar Hutang Barang',
                            'hutang pulsa' => 'Bayar Hutang Pulsa',
                        ][$transaksi->jenis] ?? ($isPemasukan ? 'Pemasukan Lain' : 'Pengeluaran Lain');

                        $keteranganDisplay = $transaksi->keterangan ? $transaksi->keterangan : $detailTipe;

                    @endphp
                    <tr>
                        <th scope="row">{{ $loop->iteration }}</th>
                        <td>{{ \Carbon\Carbon::parse($transaksi->created_at)->format('Y-m-d H:i') }}</td>
                        <td>
                            <span class="badge {{ $badgeClass }}">{{ $tipeText }}</span>
                        </td>
                        <td>
                            {{-- Menampilkan keterangan, dengan detail tipe di dalam kurung jika ada --}}
                            {{ $keteranganDisplay }}
                            @if($transaksi->keterangan && $detailTipe != $tipeText)
                                <small class="text-muted">({{ $detailTipe }})</small>
                            @endif
                        </td>
                        <td>{{ Str::ucfirst($transaksi->metode_pembayaran) }}</td>
                        <td class="text-end">
                            <strong class="{{ $isPemasukan ? 'text-success' : 'text-danger' }}">
                                {{ $sign }} Rp {{ number_format($transaksi->total, 0, ',', '.') }}
                            </strong>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-3">Tidak ada riwayat transaksi kas ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Fitur Pagination (jika diperlukan dan $riwayatTransaksi adalah instance dari LengthAwarePaginator) --}}
    {{-- {{ $riwayatTransaksi->links() }} --}}
</div>


</div>

</div>
@endsection

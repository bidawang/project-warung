@extends('layouts.app')

@section('title', 'Detail Hutang Pelanggan')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Detail Hutang Pelanggan</h5>
            <a href="{{ route('kasir.hutang.index') }}" class="btn btn-light btn-sm">Kembali</a>
        </div>
        <div class="card-body">

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="row mb-4">
                {{-- Detail Informasi Hutang --}}
                <div class="col-md-6">
                    <table class="table table-bordered table-sm">
                        <tr>
                            <th>Nama Pelanggan</th>
                            <td>{{ $hutang->user->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Jumlah Hutang Awal</th>
                            <td>Rp {{ number_format($hutang->jumlah_hutang_awal, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Jumlah Sisa Hutang</th>
                            <td class="fw-bold text-danger">Rp {{ number_format($hutang->jumlah_sisa_hutang, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Jatuh Tempo</th>
                            <td>{{ \Carbon\Carbon::parse($hutang->tenggat)->format('d F Y') }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if ($hutang->status == 'belum lunas')
                                    <span class="badge bg-danger">Belum Lunas</span>
                                @else
                                    <span class="badge bg-success">Lunas</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Deskripsi / Catatan</th>
                            <td>{{ $hutang->keterangan ?? '-' }}</td>
                        </tr>
                    </table>
                </div>

                {{-- Log Pembayaran --}}
                <div class="col-md-6">
                    <h6>Riwayat Pembayaran</h6>
                    @if ($logPembayaran->isEmpty())
                        <p class="text-muted">Belum ada pembayaran yang tercatat.</p>
                    @else
                        <ul class="list-group list-group-flush border rounded-3">
                            @foreach ($logPembayaran as $log)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>Rp {{ number_format($log->jumlah_pembayaran, 0, ',', '.') }}</strong>
                                    </div>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($log->created_at)->format('d M Y H:i') }}</small>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <h5 class="mb-3 mt-4">Daftar Barang yang Dihutang</h5>
            <div class="table-responsive mb-4">
                @if ($hutang->barangHutang->isEmpty())
                    <p class="alert alert-info">Tidak ada detail barang yang tercatat untuk hutang ini.</p>
                @else
                    <table class="table table-striped table-hover table-bordered">
                        <thead class="bg-light">
                            <tr>
                                <th>Barang & Keterangan</th>
                                <th class="text-center">Jumlah</th>
                                <th class="text-end">Harga Satuan</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $totalBarang = 0; @endphp
                            @foreach ($hutang->barangHutang as $barangHutangItem)
                                @php
                                    // Mengakses data melalui rantai relasi
                                    $barangKeluar = $barangHutangItem->barangKeluar;

                                    // Ambil data yang diperlukan
                                    $namaBarang = $barangKeluar->stokWarung->barang->nama_barang ?? 'Barang Tidak Ditemukan';
                                    $jumlah = $barangKeluar->jumlah;
                                    $hargaSatuan = $barangKeluar->harga_satuan ?? 0;
                                    $keteranganKeluar = $barangKeluar->keterangan ?? ''; // Keterangan dari BarangKeluar
                                    $subtotal = $jumlah * $hargaSatuan;

                                    $totalBarang += $subtotal;
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $namaBarang }}</strong>
                                        {{-- Keterangan diletakkan di bawah nama barang --}}
                                        @if ($keteranganKeluar)
                                            <small class="text-muted d-block mt-1">Catatan: {{ $keteranganKeluar }}</small>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ number_format($jumlah, 0, ',', '.') }}</td>
                                    <td class="text-end">Rp {{ number_format($hargaSatuan, 0, ',', '.') }}</td>
                                    <td class="text-end">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                {{-- colspan="3" agar total menempati 4 kolom (3 kolom kosong + 1 kolom nilai) --}}
                                <th colspan="3" class="text-end bg-light">Total Nilai Barang</th>
                                <th class="text-end bg-light">Rp {{ number_format($totalBarang, 0, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                @endif
            </div>

            {{-- Form Pembayaran Hutang --}}
            @if ($hutang->status == 'belum lunas')
                <div class="card mt-4 border-success">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0">Bayar Hutang</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('kasir.hutang.bayar', $hutang->id) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="jumlah_bayar" class="form-label">Jumlah Bayar (Maksimal: Rp {{ number_format($hutang->jumlah_sisa_hutang, 0, ',', '.') }})</label>
                                <input type="number" name="jumlah_bayar" id="jumlah_bayar" class="form-control"
                                    min="1"
                                    max="{{ $hutang->jumlah_sisa_hutang }}"
                                    value="{{ $hutang->jumlah_sisa_hutang }}"
                                    placeholder="Masukkan jumlah pembayaran"
                                    required>
                                @error('jumlah_bayar')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-cash me-1"></i> Proses Pembayaran
                            </button>
                        </form>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>
@endsection

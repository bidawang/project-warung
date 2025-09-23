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
                <div class="col-md-6">
                    <table class="table table-bordered">
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
                            <td>Rp {{ number_format($hutang->jumlah_sisa_hutang, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Jatuh Tempo</th>
                            <td>{{ \Carbon\Carbon::parse($hutang->tenggat)->format('Y-m-d') }}</td>
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
                            <td>{{ $hutang->deskripsi ?? '-' }}</td>
                        </tr>
                    </table>
                </div>

                <div class="col-md-6">
                    <h6>Log Pembayaran</h6>
                    @if ($logPembayaran->isEmpty())
                        <p class="text-muted">Belum ada pembayaran.</p>
                    @else
                        <ul class="list-group">
                            @foreach ($logPembayaran as $log)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>{{ \Carbon\Carbon::parse($log->created_at)->format('Y-m-d H:i') }}</span>
                                    <span>Rp {{ number_format($log->jumlah_pembayaran, 0, ',', '.') }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            @if ($hutang->status == 'belum lunas')
                <div class="card mt-4">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0">Bayar Hutang</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('kasir.hutang.bayar', $hutang->id) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="jumlah_bayar" class="form-label">Jumlah Bayar</label>
                                <input type="number" name="jumlah_bayar" id="jumlah_bayar" class="form-control"
                                    max="{{ $hutang->jumlah_sisa_hutang }}"
                                    value="{{ $hutang->jumlah_sisa_hutang }}" required>
                            </div>
                            <button type="submit" class="btn btn-success">Bayar Hutang</button>
                        </form>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>
@endsection

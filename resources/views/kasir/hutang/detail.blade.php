@extends('layouts.app')

@section('title', 'Detail Hutang Pelanggan')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Detail Hutang Pelanggan</h5>
        </div>
        <div class="card-body">

            <table class="table table-bordered">
                <tr>
                    <th>Nama Pelanggan</th>
                    <td>{{ $hutang->user->name ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Jumlah Hutang</th>
                    <td>Rp {{ number_format($hutang->jumlah_pokok, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <th>Tanggal Jatuh Tempo</th>
                    <td>{{ \Carbon\Carbon::parse($hutang->tenggat)->format('Y-m-d') }}</td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>
                        @if($hutang->status == 'belum lunas')
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
            <a href="{{ route('kasir.hutang.index') }}" class="btn btn-secondary">Kembali</a>
            @if($hutang->status == 'belum lunas')
                <form action="{{ route('kasir.hutang.bayar', $hutang->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="jumlah_bayar" class="form-label">Jumlah Bayar</label>
                        <input type="number" name="jumlah_bayar" id="jumlah_bayar" class="form-control"
                               max="{{ $hutang->jumlah_pokok }}" value="{{ $hutang->jumlah_pokok }}" required>
                    </div>

                    <button type="submit" class="btn btn-success">Bayar Hutang</button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection

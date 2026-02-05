@extends('layouts.app')

@section('title', 'Detail Kas Warung')

@section('content')
<div class="container my-4">
    <h2 class="text-center mb-4">Detail Kas Warung</h2>
    <div class="card shadow p-4">
        <div class="row">
            <div class="col-md-6">
                <h4 class="card-title">{{ $kasWarung->jenis_kas }}</h4>
                <p class="text-muted">Warung: {{ $kasWarung->warung->nama_warung ?? '-' }}</p>
                <div class="mb-4">
                    <h5>Keterangan:</h5>
                    <p class="card-text">{{ $kasWarung->keterangan ?? '-' }}</p>
                </div>
            </div>
        </div>

        <hr class="my-4">

        <div class="row">
            <div class="col-12">
                <h4 class="mb-3">Detail Pecahan Uang</h4>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Pecahan</th>
                                <th>Jumlah</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($kasWarung->detailKasWarung as $detail)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>Rp {{ number_format($detail->pecahan, 0, ',', '.') }}</td>
                                <td>{{ $detail->jumlah ?? '-' }}</td>
                                <td>{{ $detail->keterangan ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">Tidak ada detail kas yang tersedia.</td>
                            </tr>
                            @endforelse
                            <tr>
                                <th colspan="3" class="text-end">Total Kas:</th>
                                <th>Rp {{ number_format($kasWarung->detailKasWarung->sum(function($detail) { return $detail->pecahan * $detail->jumlah; }), 0, ',', '.') }}</th>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="mt-3">
        <a href="{{ route('kaswarung.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Kas Warung
        </a>
    </div>
</div>
@endsection
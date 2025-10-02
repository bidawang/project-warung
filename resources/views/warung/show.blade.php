@extends('layouts.app')

@section('title', 'Detail Warung')

@section('content')
<div class="container my-4">
    @if (Auth::check() && Auth::user()->role === 'admin')
    <h2 class="text-center mb-4">Detail Warung</h2>
    <div class="card shadow p-4">
        <div class="row">
            <div class="col-md-6">
                <h3 class="card-title">{{ $warung->nama_warung }}</h3>
                <p class="text-muted">Pemilik: {{ $warung->user->name ?? '-' }}</p>
                <p class="text-muted">Area: {{ $warung->area->area ?? '-' }}</p>

                <hr>

                <div class="d-flex align-items-center mb-3">
                    <h5 class="me-2">Modal:</h5>
                    <h4 class="text-success">Rp {{ number_format($warung->modal, 0, ',', '.') }}</h4>
                </div>

                <div class="mb-4">
                    <h5>Keterangan:</h5>
                    <p class="card-text">{{ $warung->keterangan ?? '-' }}</p>
                </div>
            </div>
            <div class="col-md-6 d-flex align-items-center justify-content-center">
                <i class="fas fa-store fa-9x text-muted"></i>
            </div>
        </div>
        @endif
        <hr class="my-4">

        {{-- Nav Tabs --}}
        <ul class="nav nav-tabs mb-3" id="barangTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="tersedia-tab" data-bs-toggle="tab" data-bs-target="#tersedia"
                    type="button" role="tab" aria-controls="tersedia" aria-selected="true">
                    Barang Tersedia
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="kosong-tab" data-bs-toggle="tab" data-bs-target="#kosong"
                    type="button" role="tab" aria-controls="kosong" aria-selected="false">
                    Barang Kosong
                </button>
            </li>
        </ul>

        <div class="tab-content" id="barangTabContent">
            @php
            $tersedia = $barangWithStok->filter(fn($barang) => ($barang->stok_saat_ini ?? 0) > 0);
            $kosong = $barangWithStok->filter(fn($barang) => ($barang->stok_saat_ini ?? 0) <= 0);
                @endphp

                {{-- Barang Tersedia --}}
                <div class="tab-pane fade show active" id="tersedia" role="tabpanel" aria-labelledby="tersedia-tab">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Nama Barang</th>
                                <th>Stok Tersedia</th>
                                <th>Harga Satuan</th>
                                <th>Harga Jual</th>
                                <th>Kuantitas</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($tersedia as $barang)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $barang->nama_barang ?? '-' }}</td>
                                <td>{{ $barang->stok_saat_ini ?? 0 }}</td>
                                <td>Rp {{ number_format($barang->harga_satuan, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($barang->harga_jual, 0, ',', '.') }}</td>
                                <td>
                                    <ul class="list-unstyled mb-2">
                                        @forelse($barang->kuantitas as $kuantitas)
                                        <li>{{ $kuantitas->jumlah }} unit:
                                            Rp {{ number_format($kuantitas->harga_jual, 0, ',', '.') }}
                                        </li>
                                        @empty
                                        <li>-</li>
                                        @endforelse
                                    </ul>
                                    @if($barang->stok_saat_ini > 0)
                                    {{-- <form action="{{ route('kuantitas.create') }}" method="POST" class="d-inline"> --}}
                                    <form action="#" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="id_stok_warung" value="{{ $barang->id_stok_warung }}">
                                        <button type="submit" class="btn btn-sm btn-outline-primary"
                                            @if(!$barang->id_stok_warung) disabled @endif>
                                            + Tambah Kuantitas
                                        </button>
                                    </form>

                                    @endif
                                </td>
                                <td>{{ $barang->keterangan ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">Tidak ada barang tersedia.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
        </div>

        {{-- Barang Kosong --}}
        <div class="tab-pane fade" id="kosong" role="tabpanel" aria-labelledby="kosong-tab">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Nama Barang</th>
                            <th>Stok Tersedia</th>
                            <th>Harga Satuan</th>
                            <th>Harga Jual</th>
                            <th>Kuantitas</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($kosong as $barang)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $barang->nama_barang ?? '-' }}</td>
                            <td>{{ $barang->stok_saat_ini ?? 0 }}</td>
                            <td>Rp {{ number_format($barang->harga_satuan, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($barang->harga_jual, 0, ',', '.') }}</td>
                            <td>
                                <ul class="list-unstyled mb-2">
                                    @forelse($barang->kuantitas as $kuantitas)
                                    <li>{{ $kuantitas->jumlah }} unit:
                                        Rp {{ number_format($kuantitas->harga_jual, 0, ',', '.') }}
                                    </li>
                                    @empty
                                    <li>-</li>
                                    @endforelse
                                </ul>

                            </td>
                            <td>{{ $barang->keterangan ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Tidak ada barang kosong.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@if (Auth::check() && Auth::user()->role === 'admin')
<div class="mt-3">
    <a href="{{ route('warung.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Warung
    </a>
</div>
@endif
</div>
@endsection

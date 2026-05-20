@extends('layouts.app')

@section('title', 'Manajemen Pulsa Warung')

@section('content')

    {{-- TAILWIND CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- ALPINE JS --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body {
            background: #f4f6f9;
        }

        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .pulsa-card {
            transition: .2s ease;
        }

        .pulsa-card:hover {
            transform: translateY(-2px);
        }

        .operator-btn {
            transition: .2s;
            white-space: nowrap;
        }

        .operator-btn.active {
            background: #0d6efd !important;
            color: white !important;
            border-color: #0d6efd !important;
        }
    </style>

    <div class="container-fluid py-3" x-data="{
        search: '',
        operator: 'all'
    }">

        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">

            <div>
                <h4 class="fw-bold mb-0">
                    Pulsa & PPOB
                </h4>
                <small class="text-muted">
                    Pilih nominal lalu transaksi
                </small>
            </div>

            <a href="{{ route('kasir.pulsa.jual.create') }}" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                <i class="fas fa-plus-circle me-2"></i>Jual Pulsa
            </a>

        </div>

        {{-- SALDO --}}
        <div class="card border-0 shadow-sm rounded-4 mb-3 overflow-hidden">

            <div class="card-body p-3">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="fw-bold">
                        Saldo Provider
                    </div>

                    <span class="badge bg-primary rounded-pill">
                        {{ count($saldoPulsas) }} Provider
                    </span>
                </div>

                <div class="d-flex overflow-auto hide-scrollbar gap-2">

                    @forelse ($saldoPulsas as $pulsa)
                        <div class="bg-dark text-white rounded-4 p-3 flex-shrink-0" style="min-width: 150px;">

                            <small class="text-white-50 d-block mb-1">
                                {{ strtoupper($pulsa->jenisPulsa->nama_jenis ?? '-') }}
                            </small>

                            <div class="fw-bold fs-5">
                                Rp {{ number_format($pulsa->jumlah, 0, ',', '.') }}
                            </div>

                        </div>
                    @empty

                        <div class="text-muted small">
                            Tidak ada saldo
                        </div>
                    @endforelse

                </div>

            </div>

        </div>

        {{-- SEARCH --}}
        <div class="card border-0 shadow-sm rounded-4 mb-3">

            <div class="card-body p-3">

                <div class="input-group">

                    <span class="input-group-text bg-white border-end-0">
                        <i class="fas fa-search text-muted"></i>
                    </span>

                    <input type="text" class="form-control border-start-0 shadow-none"
                        placeholder="Cari nominal atau operator..." x-model="search">

                </div>

            </div>

        </div>

        {{-- FILTER OPERATOR --}}
        <div class="d-flex overflow-auto hide-scrollbar gap-2 mb-3 pb-1">

            <button class="btn btn-light border rounded-pill operator-btn" :class="{ 'active': operator === 'all' }"
                @click="operator = 'all'">

                Semua

            </button>

            @php
                $uniqueOperators = $harga_pulsas->pluck('jenisPulsa.nama_jenis')->filter()->unique();
            @endphp

            @foreach ($uniqueOperators as $operator)
                <button class="btn btn-light border rounded-pill operator-btn"
                    :class="{ 'active': operator === '{{ strtolower($operator) }}' }"
                    @click="operator = '{{ strtolower($operator) }}'">

                    {{ strtoupper($operator) }}

                </button>
            @endforeach

        </div>

        {{-- LIST PRODUK --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">

            <div class="divide-y">

                @forelse ($harga_pulsas as $harga)
                    @php
                        $opName = $harga->jenisPulsa->nama_jenis ?? 'Umum';
                    @endphp

                    <div class="pulsa-card p-3 border-bottom bg-white"
                    style="{{ !$harga->tersedia ? 'opacity:.55;' : '' }}"
                        x-show="
                            (operator === 'all'
                                || operator === '{{ strtolower($opName) }}')
&&
                            (
                                '{{ strtolower($opName) }} {{ $harga->jumlah_pulsa }}'
                                .includes(search.toLowerCase())
                            )
                        "
                        x-transition>

                        <div class="d-flex justify-content-between align-items-center">

                            {{-- LEFT --}}
                            <div>

                                <div class="d-flex align-items-center gap-2 mb-1">

                                    <div class="fw-bold fs-5 text-dark">
                                        {{ number_format($harga->jumlah_pulsa, 0, ',', '.') }}
                                    </div>

                                    <span class="badge bg-light text-dark border rounded-pill">
                                        {{ strtoupper($opName) }}
                                    </span>

                                </div>

                                <small class="text-muted">
                                    Ready Stock
                                </small>

                            </div>

                            {{-- RIGHT --}}
                            <div class="text-end">

                                <div class="fw-bold text-primary fs-5">
                                    Rp {{ number_format($harga->harga_jual, 0, ',', '.') }}
                                </div>

                                @if ($harga->tersedia)
                                    <small class="text-success fw-bold">
                                        Tersedia
                                    </small>
                                @else
                                    <small class="text-danger fw-bold">
                                        Saldo Habis
                                    </small>
                                @endif

                            </div>

                        </div>

                    </div>

                @empty

                    <div class="text-center p-5">

                        <i class="fas fa-box-open fs-1 text-secondary mb-3"></i>

                        <div class="fw-bold">
                            Tidak ada produk
                        </div>

                        <small class="text-muted">
                            Produk pulsa belum tersedia
                        </small>

                    </div>
                @endforelse

            </div>

        </div>

        {{-- RIWAYAT --}}
        <div class="card border-0 shadow-sm rounded-4 mt-3">

            <div class="card-body p-3">

                <div class="d-flex justify-content-between align-items-center mb-3">

                    <div class="fw-bold">
                        Riwayat Terakhir
                    </div>

                    <span class="badge bg-light text-dark border">
                        Live
                    </span>

                </div>

                @forelse ($transaksi_pulsa as $trx)
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">

                        <div>

                            <div class="fw-semibold">
                                {{ strtoupper($trx->saldoPulsa->JenisPulsa->nama_jenis ?? '-') }}
                                {{ number_format($trx->jumlah_pulsa ?? 0, 0, ',', '.') }}
                            </div>

                            <small class="text-muted">
                                {{ $trx->created_at->diffForHumans() }}
                            </small>

                        </div>

                        <div class="text-end">

                            <div class="fw-bold">
                                Rp {{ number_format($trx->total ?? 0, 0, ',', '.') }}
                            </div>

                        </div>

                    </div>

                @empty

                    <div class="text-center text-muted py-4 small">
                        Belum ada transaksi
                    </div>
                @endforelse

            </div>

        </div>

    </div>

@endsection

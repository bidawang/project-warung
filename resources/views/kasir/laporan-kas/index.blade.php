@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="mb-4">
        <h2 class="fw-bold mb-0">Audit Kas & Bank Warung</h2>
        <p class="text-muted small">Audit saldo sistem vs aktual, dipisah per jenis.</p>
    </div>

    {{-- TAB --}}
    <ul class="nav nav-pills mb-4">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="pill" href="#kas">Kas Fisik</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="pill" href="#bank">Bank</a>
        </li>
    </ul>

    <div class="tab-content">

        {{-- ===================== TAB KAS ===================== --}}
        <div class="tab-pane fade show active" id="kas">

            <div class="d-flex justify-content-end mb-3">
                @if(!$isFilledKasToday)
                    <button class="btn btn-primary rounded-pill px-4" onclick="openModalInput()">
                        <i class="fas fa-coins me-2"></i>Audit Kas
                    </button>
                @else
                    <button class="btn btn-success rounded-pill px-4" disabled>
                        <i class="fas fa-check-circle me-2"></i>Audit Kas Hari Ini Selesai
                    </button>
                @endif
            </div>

            @forelse($laporanKas as $timeGroup => $items)
                <div class="card border-0 shadow-sm mb-4" style="border-radius:15px;">
                    <div class="card-header bg-white fw-bold text-primary">
                        <i class="fas fa-history me-2"></i>
                        Sesi Audit Kas:
                        {{ \Carbon\Carbon::parse($timeGroup)->translatedFormat('d F Y - H:i') }} WIB
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="bg-light small text-uppercase">
                                <tr>
                                    <th class="ps-4">Pecahan</th>
                                    <th class="text-center">Sistem</th>
                                    <th class="text-center">Fisik</th>
                                    <th class="text-center">Selisih</th>
                                    <th class="text-end pe-4">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items->where('tipe','adjustment')->sortByDesc('pecahan') as $adj)
                                    @php
                                        $lap = $items->where('tipe','laporan')->where('pecahan',$adj->pecahan)->first();
                                        $diff = $adj->jumlah - ($lap->jumlah ?? 0);
                                    @endphp
                                    <tr>
                                        <td class="ps-4 fw-bold">
                                            Rp {{ number_format($adj->pecahan,0,',','.') }}
                                        </td>
                                        <td class="text-center">{{ $lap->jumlah ?? 0 }}</td>
                                        <td class="text-center fw-bold text-primary">{{ $adj->jumlah }}</td>
                                        <td class="text-center">
                                            <span class="badge {{ $diff == 0 ? 'bg-success':'bg-danger' }}">
                                                {{ $diff > 0 ? '+' : '' }}{{ $diff }}
                                            </span>
                                        </td>
                                        <td class="text-end pe-4 fw-bold">
                                            Rp {{ number_format($adj->pecahan * $adj->jumlah,0,',','.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <p class="text-muted text-center py-5">Belum ada audit kas.</p>
            @endforelse
        </div>

        {{-- ===================== TAB BANK ===================== --}}
        <div class="tab-pane fade" id="bank">

            <div class="d-flex justify-content-end mb-3">
                @if(!$isFilledBankToday)
                    <button class="btn btn-warning rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalInputBank">
                        <i class="fas fa-university me-2"></i>Audit Bank
                    </button>
                @else
                    <button class="btn btn-success rounded-pill px-4" disabled>
                        <i class="fas fa-check-circle me-2"></i>Audit Bank Hari Ini Selesai
                    </button>
                @endif
            </div>

            @forelse($laporanBank as $time => $items)
                @php
                    $lap = $items->where('tipe','laporan')->first();
                    $adj = $items->where('tipe','adjustment')->first();
                    $diff = ($adj->jumlah ?? 0) - ($lap->jumlah ?? 0);
                @endphp

                <div class="card border-0 shadow-sm mb-4" style="border-radius:15px;">
                    <div class="card-header bg-white fw-bold text-warning">
                        <i class="fas fa-university me-2"></i>
                        Audit Bank:
                        {{ \Carbon\Carbon::parse($time)->translatedFormat('d F Y - H:i') }} WIB
                    </div>

                    <div class="card-body">
                        <table class="table mb-0">
                            <tr>
                                <th width="40%">Saldo Sistem</th>
                                <td>Rp {{ number_format($lap->jumlah ?? 0,0,',','.') }}</td>
                            </tr>
                            <tr>
                                <th>Saldo Aktual</th>
                                <td class="fw-bold text-primary">
                                    Rp {{ number_format($adj->jumlah ?? 0,0,',','.') }}
                                </td>
                            </tr>
                            <tr>
                                <th>Selisih</th>
                                <td>
                                    <span class="badge {{ $diff == 0 ? 'bg-success':'bg-danger' }}">
                                        {{ $diff >= 0 ? '+' : '' }}{{ number_format($diff,0,',','.') }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            @empty
                <p class="text-muted text-center py-5">Belum ada audit bank.</p>
            @endforelse
        </div>
    </div>
</div>

{{-- ===================== MODAL BANK ===================== --}}
<div class="modal fade" id="modalInputBank" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:20px;">
            <form action="{{ route('kasir.laporan-bank.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="fw-bold">Audit Saldo Bank</h5>
                </div>
                <div class="modal-body">
                    <label class="small fw-bold mb-2">Saldo Bank Aktual</label>
                    <input type="number" name="jumlah" class="form-control" required>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-warning fw-bold">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
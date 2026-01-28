@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    
    {{-- HEADER & BUTTON INPUT --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">Audit Kas Warung</h2>
            <p class="text-muted small">Rekapitulasi perbandingan kas fisik dan sistem per sesi.</p>
        </div>
        @if(!$isFilledToday)
            <button class="btn btn-primary rounded-pill px-4" onclick="openModalInput()">
                <i class="fas fa-plus me-2"></i>Audit Baru
            </button>
        @else
            <button class="btn btn-success rounded-pill px-4" disabled>
                <i class="fas fa-check-circle me-2"></i>Audit Selesai
            </button>
        @endif
    </div>

    {{-- FILTER SEARCH --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
        <div class="card-body">
            <form action="{{ route('kasir.laporan-kas.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Tanggal</label>
                    <input type="date" name="tanggal" class="form-control" value="{{ request('tanggal') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Bulan</label>
                    <select name="bulan" class="form-select">
                        <option value="">Semua Bulan</option>
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ request('bulan') == $m ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Tahun</label>
                    <input type="number" name="tahun" class="form-control" placeholder="2024" value="{{ request('tahun') }}">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-dark px-4"><i class="fas fa-search me-2"></i>Cari</button>
                    <a href="{{ route('kasir.laporan-kas.index') }}" class="btn btn-light border px-4">Reset</a>
                </div>
            </form>
        </div>
    </div>

    {{-- LIST SESI LAPORAN --}}
    @forelse($laporanKas as $timeGroup => $items)
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px; overflow: hidden;">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <span class="fw-bold text-primary">
                    <i class="fas fa-history me-2"></i>Sesi Audit: {{ \Carbon\Carbon::parse($timeGroup)->translatedFormat('d F Y - H:i') }} WIB
                </span>
                <span class="badge bg-light text-dark border">
                    Total Fisik: Rp {{ number_format($items->where('tipe', 'adjustment')->sum(fn($i) => $i->pecahan * $i->jumlah), 0, ',', '.') }}
                </span>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="bg-light">
                        <tr class="small text-uppercase">
                            <th class="ps-4">Pecahan</th>
                            <th class="text-center">Sistem (Laporan)</th>
                            <th class="text-center">Fisik (Adjustment)</th>
                            <th class="text-center">Selisih</th>
                            <th class="text-end pe-4">Subtotal Fisik</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items->where('tipe', 'adjustment')->sortByDesc('pecahan') as $adj)
                            @php
                                $lap = $items->where('tipe', 'laporan')->where('pecahan', $adj->pecahan)->first();
                                $diff = $adj->jumlah - ($lap->jumlah ?? 0);
                            @endphp
                            <tr>
                                <td class="ps-4 fw-bold">Rp {{ number_format($adj->pecahan, 0, ',', '.') }}</td>
                                <td class="text-center">{{ $lap->jumlah ?? 0 }}</td>
                                <td class="text-center fw-bold text-primary">{{ $adj->jumlah }}</td>
                                <td class="text-center">
                                    <span class="badge {{ $diff == 0 ? 'bg-success' : 'bg-danger' }}">
                                        {{ $diff > 0 ? '+' : '' }}{{ $diff }}
                                    </span>
                                </td>
                                <td class="text-end pe-4 fw-bold">Rp {{ number_format($adj->pecahan * $adj->jumlah, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @empty
        <div class="text-center py-5">
            <p class="text-muted">Tidak ada data audit ditemukan untuk periode ini.</p>
        </div>
    @endforelse
</div>

{{-- MODAL INPUT (Sama seperti sebelumnya namun pastikan pemanggilan benar) --}}
{{-- MODAL 1: INPUT FISIK (UTAMA) --}}
<div class="modal fade" id="modalInputHarian" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <form action="{{ route('kasir.laporan-kas.store') }}" method="POST" id="formLaporanKas">
                @csrf
                <div class="modal-header border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0">Audit Kas Fisik</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4">
                    <div class="alert alert-info border-0 small mb-4" style="border-radius: 12px;">
                        <i class="fas fa-info-circle me-2"></i> 
                        Sistem akan menyalin stok saat ini sebagai <strong>Laporan</strong> dan memperbarui stok baru berdasarkan <strong>Adjustment</strong> yang Anda input.
                    </div>
                    
                    <div class="row g-3">
                        @php 
                            $listPecahan = [100000, 75000, 50000, 20000, 10000, 5000, 2000, 1000, 500, 200, 100]; 
                        @endphp
                        
                        @foreach($listPecahan as $index => $p)
                            <div class="col-md-6">
                                <div class="input-group shadow-sm" style="border-radius: 10px; overflow: hidden;">
                                    <span class="input-group-text bg-white border-end-0 fw-bold text-dark" style="width: 120px;">
                                        Rp {{ number_format($p, 0, ',', '.') }}
                                    </span>
                                    <input type="hidden" name="data[{{ $index }}][pecahan]" value="{{ $p }}" class="input-pecahan">
                                    <input type="number" 
                                           name="data[{{ $index }}][jumlah]" 
                                           class="form-control border-start-0 input-jumlah" 
                                           placeholder="0" 
                                           min="0"
                                           onwheel="this.blur()">
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary rounded-pill px-5 fw-bold" onclick="reviewInput()">
                        Review & Sinkronkan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL 2: REVIEW KONFIRMASI (MUNCUL SETELAH MODAL 1) --}}
<div class="modal fade" id="modalReview" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header bg-danger text-white border-0 pt-4 px-4" style="border-radius: 20px 20px 0 0;">
                <h5 class="modal-title fw-bold">Konfirmasi Update Stok</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 pt-4">
                <p class="text-muted small mb-3">Berikut adalah ringkasan uang fisik yang akan menggantikan stok di sistem:</p>
                
                {{-- Container List Pecahan --}}
                <div class="bg-light rounded-3 p-2 mb-3" style="max-height: 250px; overflow-y: auto;">
                    <ul id="listReview" class="list-group list-group-flush bg-transparent">
                        {{-- Diisi melalui JavaScript --}}
                    </ul>
                </div>

                <div class="text-end p-3 rounded-3" style="background-color: #fff5f5; border: 1px dashed #feb2b2;">
                    <span class="text-muted small d-block fw-bold text-uppercase">Total Uang Fisik Baru</span>
                    <h3 id="totalReview" class="text-danger fw-bold mb-0">Rp 0</h3>
                </div>

                <div class="mt-4 p-3 rounded-3 bg-light border">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="confirmCheck" required>
                        <label class="form-check-label small text-dark" for="confirmCheck">
                            Saya mengonfirmasi bahwa perhitungan fisik ini sudah benar dan siap memperbarui saldo kas warung.
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pb-4 px-4">
                <button type="button" class="btn btn-link text-muted fw-bold text-decoration-none" data-bs-dismiss="modal">Perbaiki Data</button>
                <button type="button" class="btn btn-danger rounded-pill px-5 fw-bold" onclick="submitForm()">
                    Ya, Sinkronkan Sekarang
                </button>
            </div>
        </div>
    </div>
</div>
<script>
    // Pastikan modal diinisialisasi dengan benar
    var modalInputInstance;
    var modalReviewInstance;

    document.addEventListener('DOMContentLoaded', function() {
        modalInputInstance = new bootstrap.Modal(document.getElementById('modalInputHarian'));
        modalReviewInstance = new bootstrap.Modal(document.getElementById('modalReview'));
    });

    window.openModalInput = () => modalInputInstance.show();

    window.reviewInput = function() {
        let listHtml = '';
        let total = 0;
        const inputs = document.querySelectorAll('.input-jumlah');
        const pecahan = document.querySelectorAll('.input-pecahan');

        inputs.forEach((input, index) => {
            let jml = parseInt(input.value) || 0;
            let nom = parseInt(pecahan[index].value);
            if (jml > 0) {
                listHtml += `<li class="list-group-item d-flex justify-content-between">
                    <span>Rp ${nom.toLocaleString('id-ID')} x ${jml}</span>
                    <strong>Rp ${(nom * jml).toLocaleString('id-ID')}</strong>
                </li>`;
                total += (nom * jml);
            }
        });

        if (total === 0) return alert("Isi data terlebih dahulu!");
        
        document.getElementById('listReview').innerHTML = listHtml;
        document.getElementById('totalReview').innerText = 'Rp ' + total.toLocaleString('id-ID');
        modalReviewInstance.show();
    }

    window.submitForm = function() {
        document.getElementById('formLaporanKas').submit();
    }
</script>
@endsection
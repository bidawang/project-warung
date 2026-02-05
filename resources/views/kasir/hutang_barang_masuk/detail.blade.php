@extends('layouts.app')

@section('title', 'Detail Pelunasan Hutang')

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <a href="{{ route('kasir.hutang.barangmasuk.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <div class="row g-4">
        {{-- Kiri: Rincian Barang Masuk --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="fw-bold mb-0">Rincian Barang (Nota #{{ $hutang->id }})</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="bg-light text-muted small text-uppercase">
                                <tr>
                                    <th class="ps-4">Item Barang</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end pe-4">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($hutang->hutangBarangMasuk as $detail)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark">
                                            {{ $detail->barangMasuk->transaksiBarang->barang->nama_barang ?? 'N/A' }}
                                        </div>
                                        <small class="text-muted">ID Transaksi: {{ $detail->id_barang_masuk }}</small>
                                    </td>
                                    <td class="text-center">{{ $detail->barangMasuk->jumlah }}</td>
                                    <td class="text-end pe-4 fw-bold">
                                        Rp {{ number_format($detail->total, 0, ',', '.') }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white py-3">
                    <div class="d-flex justify-content-between px-2">
                        <span class="fw-bold text-muted">Total Keseluruhan</span>
                        <span class="fw-bold text-dark h5 mb-0">Rp {{ number_format($hutang->total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kanan: Form Pembayaran --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-success text-white py-3">
                    <h5 class="fw-bold mb-0 text-center">Konfirmasi Bayar</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ url('/kasir/' . $hutang->id . '/proses-bayar') }}" method="POST">
                        @csrf
                        
                        {{-- Ringkasan Tagihan --}}
                        <div class="mb-4 text-center">
                            <small class="text-muted d-block mb-1">Total yang harus dibayar</small>
                            <h3 class="fw-black text-danger">Rp {{ number_format($hutang->total, 0, ',', '.') }}</h3>
                        </div>

                        {{-- Pilihan Metode Pembayaran --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase">Metode Pembayaran</label>
                            <div class="row g-2">
                                @foreach($kasOptions as $kas)
                                <div class="col-6">
                                    <input type="radio" class="btn-check" name="id_kas_warung" id="kas_{{ $kas->id }}" value="{{ $kas->id }}" required>
                                    <label class="btn btn-outline-success w-100 py-3 rounded-3" for="kas_{{ $kas->id }}">
                                        <i class="fas {{ $kas->jenis_kas == 'cash' ? 'fa-money-bill-wave' : 'fa-university' }} mb-1 d-block"></i>
                                        <span class="small fw-bold">{{ strtoupper($kas->jenis_kas) }}</span>
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Input Nominal (ReadOnly karena harus lunas) --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase">Nominal Bayar</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">Rp</span>
                                <input type="number" name="total_bayar" class="form-control bg-light fw-bold" 
                                       value="{{ $hutang->total }}" readonly>
                            </div>
                            <div class="form-text text-muted small italic text-end">*Nominal harus sesuai tagihan</div>
                        </div>

                        <button type="submit" class="btn btn-success w-100 py-3 rounded-pill fw-bold shadow-sm">
                            <i class="fas fa-check-circle me-2"></i> Lunasi Sekarang
                        </button>
                    </form>
                </div>
            </div>

            <div class="mt-3 p-3 bg-warning-subtle rounded-4 border border-warning-alpha small text-warning-emphasis">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Pastikan saldo kas pilihan Anda mencukupi untuk melakukan pembayaran ini.
            </div>
        </div>
    </div>
</div>

<style>
    .fw-black { font-weight: 900; }
    .btn-check:checked + .btn-outline-success {
        background-color: #198754;
        color: white;
        border-color: #198754;
    }
    .bg-warning-subtle { background-color: #fff9e6; }
    .border-warning-alpha { border-color: rgba(255, 193, 7, 0.2); }
</style>
@endsection
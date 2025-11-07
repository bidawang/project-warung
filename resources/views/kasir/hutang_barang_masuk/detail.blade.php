@extends('layouts.app')

@section('title', 'Bayar Hutang Barang Masuk')

@section('content')

<div class="container mt-4">
    <h3 class="mb-4">Pembayaran Hutang Barang Masuk</h3>

    <div class="card shadow-lg">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0 fw-bold"><i class="fas fa-money-bill-wave me-2"></i>Bayar Hutang</h5>
        </div>
        <div class="card-body">

            {{-- Pesan Error / Success --}}
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-warning">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Detail Hutang --}}
            <div class="mb-4 p-3 border rounded">
                <p class="mb-1"><strong>ID Barang Masuk:</strong> {{ $hutang->barangMasuk->kode_barang_masuk ?? '-' }}</p>
                <p class="mb-1"><strong>Supplier:</strong> {{ $hutang->barangMasuk->supplier->nama ?? 'Tidak Diketahui' }}</p>
                <p class="mb-1"><strong>Tanggal Hutang:</strong> {{ \Carbon\Carbon::parse($hutang->created_at)->format('d M Y') }}</p>
                <h4 class="text-danger mt-3">Total Hutang: **Rp {{ number_format($hutang->total, 0, ',', '.') }}**</h4>
            </div>

            {{-- Form Pembayaran --}}
            <form action="{{ url('/kasir/' . $hutang->id . '/proses-bayar') }}" method="POST">
                @csrf

                {{-- Hidden input untuk ID Kas Warung Cash --}}
                <input type="hidden" name="id_kas_warung" value="{{ $idKasWarung }}">

                <div class="mb-3">
                    <label for="total_bayar" class="form-label fw-bold">Jumlah Pembayaran <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" name="total_bayar" id="total_bayar" class="form-control form-control-lg text-success fw-bold"
                            value="{{ old('total_bayar', $hutang->total) }}"
                            placeholder="Masukkan jumlah pembayaran" required min="1" step="1"
                            {{-- Memastikan jumlah bayar adalah total hutang --}}
                            data-total-hutang="{{ $hutang->total }}">
                    </div>
                    <div class="form-text text-danger">Pembayaran harus sama persis dengan total hutang.</div>
                    @error('total_bayar')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('kasir.hutang.index') }}" class="btn btn-secondary rounded-pill px-3">
                        <i class="fas fa-arrow-left me-2"></i> Kembali ke Daftar
                    </a>
                    <button type="submit" class="btn btn-success rounded-pill px-4">
                        <i class="fas fa-check-circle me-2"></i> Konfirmasi Pembayaran
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

{{-- Script sederhana untuk memastikan nilai input sesuai total hutang saat diketik --}}
{{-- Anda bisa menggunakan JavaScript untuk lebih baik, tapi ini sebagai contoh --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const totalBayarInput = document.getElementById('total_bayar');
        if (totalBayarInput) {
            const totalHutang = parseFloat(totalBayarInput.dataset.totalHutang);

            totalBayarInput.addEventListener('change', function() {
                const totalBayar = parseFloat(this.value);
                if (totalBayar !== totalHutang) {
                    alert('PERINGATAN: Jumlah pembayaran harus sama dengan total hutang (Rp ' + totalHutang.toLocaleString('id-ID') + ')');
                    // Opsi: kembalikan nilai ke total hutang
                    this.value = totalHutang;
                }
            });
        }
    });
</script>
@endsection

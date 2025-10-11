@extends('layouts.app')

@section('title', 'Jual Pulsa')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2>Formulir Transaksi Jual Pulsa</h2>
                </div>

                <div class="card-body">
                    {{-- Tampilkan pesan error jika ada --}}
                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    {{-- Form akan POST data ke route kasir.pulsa.jual.store --}}
                    <form action="{{ route('kasir.pulsa.jual.store') }}" method="POST">
                        @csrf

                        {{-- Field Nomor HP Pelanggan --}}
                        <div class="mb-3">
                            <label for="nomor_hp" class="form-label">Nomor HP Pelanggan</label>
                            <input type="text"
                                class="form-control @error('nomor_hp') is-invalid @enderror"
                                id="nomor_hp"
                                name="nomor_hp"
                                value="{{ old('nomor_hp') }}"
                                placeholder="Contoh: 081234567890"
                                required>
                            @error('nomor_hp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Pastikan nomor HP yang dimasukkan sudah benar.</small>
                        </div>

                        {{-- Field Pilihan Nominal Pulsa (Dropdown) --}}
                        <div class="mb-3">
                            <label for="harga_pulsa_id" class="form-label">Pilih Nominal Pulsa</label>
                            <select
                                class="form-control @error('harga_pulsa_id') is-invalid @enderror"
                                id="harga_pulsa_id"
                                name="harga_pulsa_id"
                                required
                            >
                                <option value="">-- Pilih Nominal --</option>
                                @foreach($harga_pulsas as $hargaPulsa)
                                    <option
                                        value="{{ $hargaPulsa->id }}"
                                        data-harga="{{ $hargaPulsa->harga }}"
                                        {{ old('harga_pulsa_id') == $hargaPulsa->id ? 'selected' : '' }}
                                    >
                                        {{ number_format($hargaPulsa->jumlah_pulsa, 0, ',', '.') }} (Harga Jual: Rp {{ number_format($hargaPulsa->harga, 0, ',', '.') }})
                                    </option>
                                @endforeach
                            </select>
                            @error('harga_pulsa_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Pilih nominal pulsa yang ingin dibeli pelanggan.</small>
                        </div>

                        {{-- Field Harga Jual (Otomatis terisi) --}}
                        <div class="mb-3">
                            <label for="harga_jual_display" class="form-label">Harga Jual (Dibayar Pelanggan)</label>
                            <input type="text"
                                class="form-control"
                                id="harga_jual_display"
                                value="Rp 0"
                                readonly>
                        </div>

                        {{-- Field Uang Bayar (Tunai) --}}
                        <div class="mb-3">
                            <label for="bayar" class="form-label">Uang Bayar (Tunai)</label>
                            <input type="number"
                                class="form-control @error('bayar') is-invalid @enderror"
                                id="bayar"
                                name="bayar"
                                value="{{ old('bayar') }}"
                                placeholder="Contoh: 15000"
                                required>
                            @error('bayar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Jumlah uang yang dibayarkan oleh pelanggan.</small>
                        </div>

                        {{-- Field Kembalian (Otomatis terisi) --}}
                        <div class="mb-3">
                            <label for="kembalian_display" class="form-label">Kembalian</label>
                            <input type="text"
                                class="form-control"
                                id="kembalian_display"
                                value="Rp 0"
                                readonly>
                        </div>

                        <button type="submit" class="btn btn-primary">Proses Penjualan</button>
                        <a href="{{ route('kasir.pulsa.index') }}" class="btn btn-secondary">Batal</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectNominal = document.getElementById('harga_pulsa_id');
        const inputBayar = document.getElementById('bayar');
        const displayHargaJual = document.getElementById('harga_jual_display');
        const displayKembalian = document.getElementById('kembalian_display');

        function formatRupiah(number) {
            // Fungsi untuk memformat angka menjadi format Rupiah (tanpa desimal)
            return 'Rp ' + (number ? number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".") : '0');
        }

        function calculateKembalian() {
            // Ambil data harga jual dari attribute 'data-harga' di option yang dipilih
            const selectedOption = selectNominal.options[selectNominal.selectedIndex];
            const hargaJual = parseInt(selectedOption.getAttribute('data-harga')) || 0;
            const uangBayar = parseInt(inputBayar.value) || 0;

            // Tampilkan Harga Jual
            displayHargaJual.value = formatRupiah(hargaJual);

            // Hitung dan Tampilkan Kembalian
            const kembalian = uangBayar - hargaJual;
            displayKembalian.value = formatRupiah(kembalian);

            // Opsional: Beri warna merah pada kembalian jika pembayaran kurang
            if (kembalian < 0) {
                displayKembalian.classList.remove('text-success');
                displayKembalian.classList.add('text-danger');
            } else {
                displayKembalian.classList.remove('text-danger');
                displayKembalian.classList.add('text-success'); // Tambahkan warna hijau untuk kembalian positif/nol
            }
        }

        // Event Listeners
        selectNominal.addEventListener('change', calculateKembalian);
        inputBayar.addEventListener('input', calculateKembalian);

        // Panggil saat halaman dimuat untuk mengisi nilai awal jika ada old input
        calculateKembalian();
    });
</script>
@endsection

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
                    {{-- Tampilkan pesan error dari server jika ada --}}
                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form id="formJualPulsa" action="{{ route('kasir.pulsa.jual.store') }}" method="POST">
                        @csrf

                        {{-- Nomor HP --}}
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

                        {{-- Pilihan Nominal Pulsa --}}
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
                                        {{-- Tambahkan data nominal pulsa untuk pengecekan saldo di frontend --}}
                                        data-nominal="{{ $hargaPulsa->jumlah_pulsa }}"
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

                        {{-- Harga Jual --}}
                        <div class="mb-3">
                            <label for="harga_jual_display" class="form-label">Harga Jual (Total Tagihan)</label>
                            <input type="text" class="form-control" id="harga_jual_display" value="Rp 0" readonly>
                        </div>

                        <hr>

                        {{-- Jenis Pembayaran BARU --}}
                        <div class="mb-3">
                            <label for="jenis_pembayaran" class="form-label">Jenis Pembayaran</label>
                            <select
                                class="form-control @error('jenis_pembayaran') is-invalid @enderror"
                                id="jenis_pembayaran"
                                name="jenis_pembayaran"
                                required
                            >
                                <option value="penjualan pulsa" {{ old('jenis_pembayaran') == 'penjualan pulsa' ? 'selected' : '' }}>Tunai (Bayar Penuh)</option>
                                {{-- Jika ada Non-Tunai, bisa ditambahkan di sini --}}
                                <option value="hutang pulsa" {{ old('jenis_pembayaran') == 'hutang pulsa' ? 'selected' : '' }}>Hutang / Kredit</option>
                            </select>
                            @error('jenis_pembayaran')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Pelanggan (Hanya Muncul Jika Pilih Hutang) --}}
                        <div class="mb-3" id="pelanggan_field" style="display: {{ old('jenis_pembayaran') == 'hutang pulsa' ? 'block' : 'none' }};">
                            <label for="pelanggan_id" class="form-label">Pilih Pelanggan (untuk Hutang)</label>
                            <select
                                class="form-control @error('pelanggan_id') is-invalid @enderror"
                                id="pelanggan_id"
                                name="pelanggan_id"
                            >
                                <option value="">-- Pilih Pelanggan --</option>
                                @foreach($pelanggans as $pelanggan)
                                    <option
                                        value="{{ $pelanggan->id }}"
                                        {{ old('pelanggan_id') == $pelanggan->id ? 'selected' : '' }}
                                    >
                                        {{ $pelanggan->name }} ({{ $pelanggan->nomor_hp }})
                                    </option>
                                @endforeach
                            </select>
                            @error('pelanggan_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-danger">Wajib diisi jika jenis pembayaran adalah Hutang.</small>
                        </div>

                        {{-- Uang Bayar (Hanya Muncul Jika Pilih Tunai) --}}
                        <div class="mb-3" id="bayar_field" style="display: {{ old('jenis_pembayaran') != 'hutang pulsa' ? 'block' : 'none' }};">
                            <label for="bayar" class="form-label">Uang Bayar (Tunai)</label>
                            <input type="number"
                                class="form-control @error('bayar') is-invalid @enderror"
                                id="bayar"
                                name="bayar"
                                value="{{ old('bayar', 0) }}" {{-- Set default 0 agar kalkulasi tidak error --}}
                                placeholder="Contoh: 15000"
                                required>
                            @error('bayar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Jumlah uang yang dibayarkan oleh pelanggan.</small>
                        </div>

                        {{-- Kembalian (Hanya Muncul Jika Pilih Tunai) --}}
                        <div class="mb-3" id="kembalian_field" style="display: {{ old('jenis_pembayaran') != 'hutang pulsa' ? 'block' : 'none' }};">
                            <label for="kembalian_display" class="form-label">Kembalian</label>
                            <input type="text" class="form-control" id="kembalian_display" value="Rp 0" readonly>
                        </div>

                        <input type="hidden" name="jenis_pembayaran" id="hidden_jenis_pembayaran" value="{{ old('jenis_pembayaran', 'penjualan pulsa') }}">

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
    const form = document.getElementById('formJualPulsa');
    const selectNominal = document.getElementById('harga_pulsa_id');
    const selectPembayaran = document.getElementById('jenis_pembayaran');
    const selectPelanggan = document.getElementById('pelanggan_id');
    const inputBayar = document.getElementById('bayar');
    const inputHP = document.getElementById('nomor_hp');
    const displayHargaJual = document.getElementById('harga_jual_display');
    const displayKembalian = document.getElementById('kembalian_display');
    const pelangganField = document.getElementById('pelanggan_field');
    const bayarField = document.getElementById('bayar_field');
    const kembalianField = document.getElementById('kembalian_field');
    const hiddenJenisPembayaran = document.getElementById('hidden_jenis_pembayaran');

    function formatRupiah(number) {
        return 'Rp ' + (number ? number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".") : '0');
    }

    function toggleFields() {
        const jenis = selectPembayaran.value;
        hiddenJenisPembayaran.value = jenis; // Pastikan hidden field terisi

        if (jenis === 'hutang pulsa') {
            pelangganField.style.display = 'block';
            bayarField.style.display = 'none';
            kembalianField.style.display = 'none';
            inputBayar.removeAttribute('required');
            inputBayar.value = 0; // Set bayar jadi 0 jika hutang
            selectPelanggan.setAttribute('required', 'required');
            calculateKembalian(); // Hitung ulang kembalian agar Rp 0
        } else { // Tunai
            pelangganField.style.display = 'none';
            bayarField.style.display = 'block';
            kembalianField.style.display = 'block';
            inputBayar.setAttribute('required', 'required');
            selectPelanggan.removeAttribute('required');
            selectPelanggan.value = ''; // Kosongkan pelanggan jika bukan hutang
            calculateKembalian(); // Hitung ulang kembalian
        }
    }

    function calculateKembalian() {
        const selectedOption = selectNominal.options[selectNominal.selectedIndex];
        const hargaJual = parseInt(selectedOption.getAttribute('data-harga')) || 0;

        displayHargaJual.value = formatRupiah(hargaJual);

        if (selectPembayaran.value === 'hutang pulsa') {
            displayKembalian.value = formatRupiah(0);
            displayKembalian.classList.remove('text-danger');
            displayKembalian.classList.remove('text-success');
            return;
        }

        const uangBayar = parseInt(inputBayar.value) || 0;
        const kembalian = uangBayar - hargaJual;
        displayKembalian.value = formatRupiah(kembalian);

        if (kembalian < 0) {
            displayKembalian.classList.remove('text-success');
            displayKembalian.classList.add('text-danger');
        } else {
            displayKembalian.classList.remove('text-danger');
            displayKembalian.classList.add('text-success');
        }
    }

    selectNominal.addEventListener('change', calculateKembalian);
    inputBayar.addEventListener('input', calculateKembalian);
    selectPembayaran.addEventListener('change', toggleFields);

    // Panggil saat load pertama kali untuk menyesuaikan tampilan (misal ada old data)
    toggleFields();
    calculateKembalian();

    // VALIDASI SEBELUM SUBMIT
    form.addEventListener('submit', function(event) {
        const nomorHP = inputHP.value.trim();
        const selectedOption = selectNominal.options[selectNominal.selectedIndex];
        const hargaJual = parseInt(selectedOption.getAttribute('data-harga')) || 0;
        const jenisPembayaran = selectPembayaran.value;

        // Cek nomor HP valid (angka dan minimal 10 digit)
        if (!/^\d{10,15}$/.test(nomorHP)) {
            alert('Nomor HP tidak valid! Masukkan hanya angka dengan panjang 10-15 digit.');
            event.preventDefault();
            return;
        }

        // Cek apakah nominal pulsa sudah dipilih
        if (selectNominal.value === '') {
            alert('Silakan pilih nominal pulsa terlebih dahulu.');
            event.preventDefault();
            return;
        }

        if (jenisPembayaran === 'penjualan pulsa') {
            const uangBayar = parseInt(inputBayar.value) || 0;
            // Cek apakah uang bayar cukup
            if (uangBayar < hargaJual) {
                alert('Uang bayar kurang dari harga pulsa! Mohon periksa kembali.');
                event.preventDefault();
                return;
            }
        } else if (jenisPembayaran === 'hutang pulsa') {
            // Cek apakah pelanggan sudah dipilih
            if (selectPelanggan.value === '') {
                alert('Silakan pilih Pelanggan untuk transaksi Hutang.');
                event.preventDefault();
                return;
            }
        }

        // Cek Saldo Pulsa Warung (hanya di sisi client jika diperlukan,
        // tapi validasi utama harus di server)

    });
});
</script>
@endsection

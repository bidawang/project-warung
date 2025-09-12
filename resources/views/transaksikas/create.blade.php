@extends('layouts.app')

@section('title', 'Tambah Transaksi Kas')

@section('content')
<div class="container my-4">
    <h2 class="mb-4">Tambah Transaksi Kas</h2>

    <form action="{{ route('transaksikas.store') }}" method="POST">
        @csrf
        <div class="card p-4 shadow">
            <div class="mb-3">
                <label for="id_kas_warung" class="form-label">Kas Warung</label>
                <select name="id_kas_warung" id="id_kas_warung" class="form-control" required>
                    @foreach($kasWarungs as $kas)
                    <option value="{{ $kas->id }}">{{ $kas->warung->nama_warung ?? '-' }} - {{ $kas->jenis_kas }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="jenis_transaksi" class="form-label">Jenis Transaksi</label>
                <select name="jenis_transaksi" id="jenis_transaksi" class="form-control" required>
                    <option value="masuk">Masuk</option>
                    <option value="keluar">Keluar</option>
                </select>
            </div>

            <hr>

            <h5 class="mt-3 mb-3">Pecahan Uang</h5>
            <div class="row g-3" id="pecahan-container">
                {{-- Pecahan akan di-render oleh JS --}}
            </div>

            <hr class="my-4">

            <div class="mb-3">
                <label for="total" class="form-label fw-bold">Total Kas</label>
                <input type="text" id="total_display" class="form-control" readonly>
                <input type="hidden" name="total_hidden" id="total_hidden">
            </div>

            <div class="mb-3">
                <label for="keterangan" class="form-label">Keterangan</label>
                <textarea name="keterangan" id="keterangan" class="form-control"></textarea>
            </div>
        </div>

        <div class="mt-3">
            <button type="submit" class="btn btn-success">Simpan</button>
            <a href="{{ url()->previous() }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const pecahanContainer = document.getElementById('pecahan-container');
        const jenisTransaksiSelect = document.getElementById('jenis_transaksi');
        const totalDisplay = document.getElementById('total_display');
        const totalHidden = document.getElementById('total_hidden');

        // Data pecahan dari server
        const pecahanMasuk = @json($pecahanMasuk);
        const pecahanKeluar = @json($kasWarungs-> first()-> detailKasWarung ?? []);

        // Fungsi render pecahan
        function renderPecahan(jenis) {
            pecahanContainer.innerHTML = '';

            let pecahanList = [];

            if (jenis === 'masuk') {
                pecahanList = pecahanMasuk.map(p => ({
                    pecahan: p,
                    jumlah: 0
                }));
            } else if (jenis === 'keluar') {
                pecahanList = pecahanKeluar.map(d => ({
                    pecahan: d.pecahan,
                    jumlah: d.jumlah ?? 0
                }));
            }

            pecahanList.forEach(item => {
                const col = document.createElement('div');
                col.className = 'col-6 col-sm-4 col-md-3';

                const label = document.createElement('label');
                label.setAttribute('for', 'pecahan-' + item.pecahan);
                label.className = 'form-label';
                label.textContent = `Rp ${item.pecahan.toLocaleString('id-ID')}`;
                if (jenis === 'keluar') {
                    label.textContent += ` (${item.jumlah})`;
                }

                const input = document.createElement('input');
                input.type = 'number';
                input.name = `pecahan[${item.pecahan}]`;
                input.id = 'pecahan-' + item.pecahan;
                input.className = 'form-control jumlah-pecahan';
                input.value = 0;
                input.min = 0;

                col.appendChild(label);
                col.appendChild(input);
                pecahanContainer.appendChild(col);
            });

            // Pasang event listener input baru
            attachInputListeners();
            calculateTotal();
        }

        // Hitung total
        function calculateTotal() {
            let total = 0;
            const inputs = document.querySelectorAll('.jumlah-pecahan');
            inputs.forEach(input => {
                const value = parseInt(input.value) || 0;
                const pecahan = parseInt(input.id.split('-')[1]);
                total += value * pecahan;
            });
            totalDisplay.value = 'Rp ' + total.toLocaleString('id-ID');
            totalHidden.value = total;
        }

        // Pasang event listener input pecahan
        function attachInputListeners() {
            const inputs = document.querySelectorAll('.jumlah-pecahan');
            inputs.forEach(input => {
                input.addEventListener('input', calculateTotal);
            });
        }

        // Event change jenis transaksi
        jenisTransaksiSelect.addEventListener('change', function() {
            renderPecahan(this.value);
        });

        // Render awal dengan jenis transaksi default (masuk)
        renderPecahan(jenisTransaksiSelect.value);
    });
</script>
@endsection
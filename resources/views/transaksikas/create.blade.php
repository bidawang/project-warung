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

            <hr>

            <h5 class="mt-3 mb-3">Pecahan Uang</h5>
            <div class="row g-3">
                @foreach ($kasWarungs->first()->detailKasWarung ?? [] as $detail)
                <div class="col-6 col-sm-4 col-md-3">
                    <label for="pecahan-{{ $detail->pecahan }}" class="form-label">Rp {{ number_format($detail->pecahan, 0, ',', '.') }} ({{ $detail->jumlah }})</label>
                    <input type="number" name="pecahan[{{ $detail->pecahan }}]" id="pecahan-{{ $detail->pecahan }}" class="form-control jumlah-pecahan" value="0" min="0">
                </div>
                @endforeach
            </div>

            <hr class="my-4">

            <div class="mb-3">
                <label for="total" class="form-label fw-bold">Total Kas</label>
                <input type="text" id="total_display" class="form-control" readonly>
                <input type="hidden" name="total_hidden" id="total_hidden">
            </div>

            <div class="mb-3">
                <label for="metode_pembayaran" class="form-label">Metode Pembayaran</label>
                <input type="text" name="metode_pembayaran" id="metode_pembayaran" class="form-control" required>
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
        const inputs = document.querySelectorAll('.jumlah-pecahan');
        const totalDisplay = document.getElementById('total_display');
        const totalHidden = document.getElementById('total_hidden');

        function calculateTotal() {
            let total = 0;
            inputs.forEach(input => {
                const value = parseInt(input.value) || 0;
                const pecahan = parseInt(input.id.split('-')[1]);
                total += value * pecahan;
            });
            totalDisplay.value = 'Rp ' + total.toLocaleString('id-ID');
            totalHidden.value = total;
        }

        inputs.forEach(input => {
            input.addEventListener('input', calculateTotal);
        });

        calculateTotal();
    });
</script>
@endsection

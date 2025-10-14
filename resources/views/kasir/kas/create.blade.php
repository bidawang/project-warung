@extends('layouts.app')

@section('title', 'Tambah Transaksi Kas Manual')

@section('content')

<div class="container mt-4">
<h3 class="mb-4">Tambah Transaksi Kas Manual</h3>

<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        Formulir Pemasukan / Pengeluaran
    </div>
    <div class="card-body">
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

        <form action="{{ route('kasir.kas.store') }}" method="POST">
            @csrf
            {{-- Hidden input untuk ID Kas Warung --}}
            <input type="hidden" name="id_kas_warung" value="{{ $idKasWarung }}">

            <div class="mb-3">
                <label class="form-label">Jenis Transaksi <span class="text-danger">*</span></label>
                <div class="d-flex gap-3">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="jenis" id="jenis_masuk" value="masuk"
                            {{ old('jenis') == 'masuk' ? 'checked' : '' }} required>
                        <label class="form-check-label" for="jenis_masuk">
                            Pemasukan (Kas Masuk)
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="jenis" id="jenis_pengeluaran" value="keluar"
                            {{ old('jenis') == 'keluar' ? 'checked' : '' }} required>
                        <label class="form-check-label" for="jenis_pengeluaran">
                            Pengeluaran (Kas Keluar)
                        </label>
                    </div>
                </div>
                @error('jenis')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="total" class="form-label">Jumlah Uang (Rp) <span class="text-danger">*</span></label>
                <input type="number" name="total" id="total" class="form-control" value="{{ old('total') }}" placeholder="Contoh: 50000" required min="1">
                @error('total')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="keterangan" class="form-label">Keterangan / Tujuan <span class="text-danger">*</span></label>
                <textarea name="keterangan" id="keterangan" class="form-control" rows="3" placeholder="Contoh: Pembelian alat kebersihan kantor atau Modal Awal Tambahan" required>{{ old('keterangan') }}</textarea>
                @error('keterangan')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('kasir.kas.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i> Batal
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save me-2"></i> Simpan Transaksi
                </button>
            </div>
        </form>
    </div>
</div>

</div>
@endsection

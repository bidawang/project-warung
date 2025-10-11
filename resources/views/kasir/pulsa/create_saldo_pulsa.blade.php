@extends('layouts.app')

@section('title', 'Tambah Saldo Pulsa')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2>Tambah Saldo Pulsa Warung</h2>
                </div>

                <div class="card-body">
                    {{-- Form akan POST data ke route kasir.pulsa.saldo.store --}}
                    <form action="{{ route('kasir.pulsa.saldo.store') }}" method="POST">
                        @csrf

                        {{-- Field Nominal Saldo --}}
                        <div class="mb-3">
                            <label for="nominal" class="form-label">Nominal Saldo yang Ditambahkan</label>
                            <input type="number"
                                class="form-control @error('nominal') is-invalid @enderror"
                                id="nominal"
                                name="nominal"
                                value="{{ old('nominal') }}"
                                placeholder="Contoh: 100000"
                                required>
                            @error('nominal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Masukkan jumlah uang yang digunakan untuk menambah saldo pulsa (Modal Kas Warung).</small>
                        </div>

                        <button type="submit" class="btn btn-primary">Tambahkan Saldo</button>
                        <a href="{{ route('kasir.pulsa.index') }}" class="btn btn-secondary">Batal</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

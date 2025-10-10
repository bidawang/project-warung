@extends('layouts.app')

@section('title', 'Tambah Harga Pulsa')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2>Tambah Harga Pulsa Baru</h2>
                </div>

                <div class="card-body">
                    {{-- Form akan POST data ke route harga_pulsa.store --}}
                    <form action="{{ route('kasir.pulsa.harga-pulsa.store') }}" method="POST">
                        @csrf

                        {{-- Field Jumlah Pulsa (Misal: 5000, 10000) --}}
                        <div class="mb-3">
                            <label for="jumlah_pulsa" class="form-label">Jumlah Pulsa (Nominal)</label>
                            <input type="number"
                                   class="form-control @error('jumlah_pulsa') is-invalid @enderror"
                                   id="jumlah_pulsa"
                                   name="jumlah_pulsa"
                                   value="{{ old('jumlah_pulsa') }}"
                                   placeholder="Contoh: 10000"
                                   required>
                            @error('jumlah_pulsa')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Masukkan nominal pulsa (misal: 5000 untuk pulsa 5 ribu).</small>
                        </div>

                        {{-- Field Harga Beli/Jual --}}
                        <div class="mb-3">
                            <label for="harga" class="form-label">Harga Jual (Modal + Untung)</label>
                            <input type="number"
                                   class="form-control @error('harga') is-invalid @enderror"
                                   id="harga"
                                   name="harga"
                                   value="{{ old('harga') }}"
                                   placeholder="Contoh: 12000"
                                   required>
                            @error('harga')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Masukkan harga jual yang akan dibayar oleh pelanggan.</small>
                        </div>

                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="{{ route('kasir.pulsa.index') }}" class="btn btn-secondary">Batal</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

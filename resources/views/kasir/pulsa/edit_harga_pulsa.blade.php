@extends('layouts.app')

@section('title', 'Edit Harga Pulsa')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2>Edit Harga Pulsa</h2>
                </div>

                <div class="card-body">
                    {{-- Form akan POST data ke route harga_pulsa.update dengan method PUT --}}
                    {{-- Asumsi $hargaPulsa adalah objek model data harga pulsa yang akan diedit --}}
                    <form action="{{ route('kasir.pulsa.harga-pulsa.update', $hargaPulsa->id) }}" method="POST">
                        @csrf
                        @method('PUT') {{-- Digunakan untuk mengirimkan permintaan UPDATE --}}

                        {{-- Field Jumlah Pulsa (Misal: 5000, 10000) --}}
                        <div class="mb-3">
                            <label for="jumlah_pulsa" class="form-label">Jumlah Pulsa (Nominal)</label>
                            <input type="number"
                                class="form-control @error('jumlah_pulsa') is-invalid @enderror"
                                id="jumlah_pulsa"
                                name="jumlah_pulsa"
                                {{-- Mengisi nilai field dengan data yang sudah ada atau old input --}}
                                value="{{ old('jumlah_pulsa', $hargaPulsa->jumlah_pulsa) }}"
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
                                {{-- Mengisi nilai field dengan data yang sudah ada atau old input --}}
                                value="{{ old('harga', $hargaPulsa->harga) }}"
                                placeholder="Contoh: 12000"
                                required>
                            @error('harga')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Masukkan harga jual yang akan dibayar oleh pelanggan.</small>
                        </div>

                        <button type="submit" class="btn btn-primary">Update</button>
                        {{-- Link kembali ke halaman index/daftar harga pulsa --}}
                        <a href="{{ route('kasir.pulsa.index') }}" class="btn btn-secondary">Batal</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

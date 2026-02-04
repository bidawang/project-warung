@extends('layouts.app')

@section('title', 'Tambah Bunga')

@section('content')
<div class="container">
    <h2 class="mb-4">Tambah Bunga</h2>

    <form action="{{ route('bunga.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Hutang</label>
            <select name="id_hutang" class="form-select" required>
                <option value="">-- Pilih Hutang --</option>
                @foreach($hutang as $h)
                <option value="{{ $h->id }}">
                    {{ $h->user->name ?? '-' }} - {{ $h->warung->nama_warung ?? '-' }} | Rp {{ number_format($h->jumlah,0,',','.') }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Jumlah Bunga</label>
            <input type="number" name="jumlah_bunga" class="form-control" required>
        </div>

        <button class="btn btn-primary">Simpan</button>
        <a href="{{ route('bunga.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection

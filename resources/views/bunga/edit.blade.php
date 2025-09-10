@extends('layouts.app')

@section('title', 'Edit Bunga')

@section('content')
<div class="container">
    <h2 class="mb-4">Edit Bunga</h2>

    <form action="{{ route('bunga.update', $bunga->id) }}" method="POST">
        @csrf @method('PUT')

        <div class="mb-3">
            <label>Hutang</label>
            <select name="id_hutang" class="form-select" required>
                @foreach($hutang as $h)
                <option value="{{ $h->id }}" {{ $bunga->id_hutang == $h->id ? 'selected' : '' }}>
                    {{ $h->user->name ?? '-' }} - {{ $h->warung->nama_warung ?? '-' }} | Rp {{ number_format($h->jumlah,0,',','.') }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Jumlah Bunga</label>
            <input type="number" name="jumlah_bunga" class="form-control" value="{{ $bunga->jumlah_bunga }}" required>
        </div>

        <button class="btn btn-primary">Update</button>
        <a href="{{ route('bunga.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection

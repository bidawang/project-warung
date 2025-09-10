@extends('layouts.app')

@section('title', 'Edit Pembayaran Hutang')

@section('content')
<div class="container">
    <h2 class="mb-4">Edit Pembayaran Hutang</h2>

    <form action="{{ route('pembayaranhutang.update', $pembayaran->id) }}" method="POST">
        @csrf @method('PUT')

        <div class="mb-3">
            <label>Hutang</label>
            <select name="id_hutang" class="form-select" required>
                @foreach($hutang as $h)
                <option value="{{ $h->id }}" {{ $pembayaran->id_hutang == $h->id ? 'selected' : '' }}>
                    ID: {{ $h->id }} - {{ $h->user->name ?? '-' }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Transaksi Kas</label>
            <select name="id_transaksi_kas" class="form-select" required>
                @foreach($transaksiKas as $t)
                <option value="{{ $t->id }}" {{ $pembayaran->id_transaksi_kas == $t->id ? 'selected' : '' }}>
                    ID: {{ $t->id }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Keterangan</label>
            <textarea name="keterangan" class="form-control">{{ $pembayaran->keterangan }}</textarea>
        </div>

        <button class="btn btn-primary">Update</button>
        <a href="{{ route('pembayaranhutang.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection

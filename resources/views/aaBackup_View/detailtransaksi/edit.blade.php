@extends('layouts.app')

@section('title', 'Edit Detail Transaksi')

@section('content')
<div class="container">
    <h2 class="mb-4">Edit Detail Transaksi</h2>

    <form action="{{ route('detailtransaksi.update', $detailtransaksi->id) }}" method="POST">
        @csrf @method('PUT')
        <div class="mb-3">
            <label>Transaksi</label>
            <select name="id_transaksi" class="form-control">
                @foreach($transaksis as $trx)
                    <option value="{{ $trx->id }}" {{ $trx->id == $detailtransaksi->id_transaksi ? 'selected' : '' }}>
                        #{{ $trx->id }} - {{ ucfirst($trx->jenis) }} - Rp{{ number_format($trx->total, 0, ',', '.') }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label>Pecahan</label>
            <input type="number" name="pecahan" class="form-control" value="{{ $detailtransaksi->pecahan }}" required>
        </div>
        <div class="mb-3">
            <label>Jumlah</label>
            <input type="number" name="jumlah" class="form-control" value="{{ $detailtransaksi->jumlah }}" required>
        </div>
        <div class="mb-3">
            <label>Keterangan</label>
            <textarea name="keterangan" class="form-control">{{ $detailtransaksi->keterangan }}</textarea>
        </div>
        <button class="btn btn-success">Update</button>
    </form>
</div>
@endsection

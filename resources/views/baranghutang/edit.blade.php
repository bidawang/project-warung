@extends('layouts.app')

@section('title', 'Edit Barang Hutang')

@section('content')
<div class="container">
    <h2 class="mb-4">Edit Barang Hutang</h2>

    <form action="{{ route('baranghutang.update', $baranghutang->id) }}" method="POST">
        @csrf @method('PUT')

        <div class="mb-3">
            <label>Hutang</label>
            <select name="id_hutang" class="form-select" required>
                @foreach($hutang as $h)
                <option value="{{ $h->id }}" {{ $baranghutang->id_hutang == $h->id ? 'selected' : '' }}>
                    ID: {{ $h->id }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Transaksi Barang</label>
            <select name="id_transaksi_barang" class="form-select" required>
                @foreach($transaksiBarang as $tb)
                <option value="{{ $tb->id }}" {{ $baranghutang->id_transaksi_barang == $tb->id ? 'selected' : '' }}>
                    ID: {{ $tb->id }}
                </option>
                @endforeach
            </select>
        </div>

        <button class="btn btn-primary">Update</button>
        <a href="{{ route('baranghutang.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection

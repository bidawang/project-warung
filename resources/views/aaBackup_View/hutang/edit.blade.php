@extends('layouts.app')

@section('title', 'Edit Hutang')

@section('content')
<div class="container">
    <h2 class="mb-4">Edit Hutang</h2>

    <form action="{{ route('hutang.update', $hutang->id) }}" method="POST">
        @csrf @method('PUT')

        <div class="mb-3">
            <label>Warung</label>
            <select name="id_warung" class="form-select" required>
                @foreach($warung as $w)
                <option value="{{ $w->id }}" {{ $hutang->id_warung == $w->id ? 'selected' : '' }}>
                    {{ $w->nama_warung }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>User</label>
            <select name="id_user" class="form-select" required>
                @foreach($user as $u)
                <option value="{{ $u->id }}" {{ $hutang->id_user == $u->id ? 'selected' : '' }}>
                    {{ $u->name }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Jumlah</label>
            <input type="number" name="jumlah" class="form-control" value="{{ $hutang->jumlah }}" required>
        </div>

        <div class="mb-3">
            <label>Tenggat</label>
            <input type="date" name="tenggat" class="form-control" value="{{ $hutang->tenggat }}" required>
        </div>

        <div class="mb-3">
            <label>Status</label>
            <select name="status" class="form-select">
                <option value="belum lunas" {{ $hutang->status->value == 'belum lunas' ? 'selected' : '' }}>Belum Lunas</option>
                <option value="lunas" {{ $hutang->status->value == 'lunas' ? 'selected' : '' }}>Lunas</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Keterangan</label>
            <textarea name="keterangan" class="form-control">{{ $hutang->keterangan }}</textarea>
        </div>

        <button class="btn btn-primary">Update</button>
        <a href="{{ route('hutang.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection

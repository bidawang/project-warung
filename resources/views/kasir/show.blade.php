@extends('layouts.app')

@section('title', 'Detail Kasir')

@section('content')
<div class="container my-4">
    <h2 class="text-center mb-4">Detail Kasir</h2>

    <div class="card shadow p-4">
        <div class="mb-3">
            <strong>Nama:</strong>
            <p>{{ $kasir->user->name }}</p>
        </div>
        <div class="mb-3">
            <strong>Email:</strong>
            <p>{{ $kasir->user->email }}</p>
        </div>
        <div class="mb-3">
            <strong>Nomor HP:</strong>
            <p>{{ $kasir->user->nomor_hp }}</p>
        </div>
        <div class="mb-3">
            <strong>Google ID:</strong>
            <p>{{ $kasir->google_id }}</p>
        </div>
        <div class="mb-3">
            <strong>Keterangan:</strong>
            <p>{{ $kasir->user->keterangan ?? '-' }}</p>
        </div>

        <div class="d-flex justify-content-end">
            <a href="{{ route('user.index') }}" class="btn btn-secondary me-2">Kembali</a>
            <a href="{{ route('user.edit', $kasir->user->id) }}" class="btn btn-primary">Edit</a>
        </div>
    </div>
</div>
@endsection

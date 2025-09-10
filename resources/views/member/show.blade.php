@extends('layouts.app')

@section('title', 'Detail Member')

@section('content')
<div class="container my-4">
    <h2 class="text-center mb-4">Detail Member</h2>

    <div class="card shadow p-4">
        <div class="mb-3">
            <strong>Nama:</strong>
            <p>{{ $member->user->name }}</p>
        </div>
        <div class="mb-3">
            <strong>Email:</strong>
            <p>{{ $member->user->email }}</p>
        </div>
        <div class="mb-3">
            <strong>Nomor HP:</strong>
            <p>{{ $member->user->nomor_hp }}</p>
        </div>
        <div class="mb-3">
            <strong>Kode User:</strong>
            <p>{{ $member->kode_user }}</p>
        </div>
        <div class="mb-3">
            <strong>Keterangan:</strong>
            <p>{{ $member->user->keterangan ?? '-' }}</p>
        </div>

        <div class="d-flex justify-content-end">
            <a href="{{ route('user.index') }}" class="btn btn-secondary me-2">Kembali</a>
            <a href="{{ route('user.edit', $member->user->id) }}" class="btn btn-primary">Edit</a>
        </div>
    </div>
</div>
@endsection

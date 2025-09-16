@extends('layouts.app')

@section('title', 'Profil Pengguna')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white text-center">
                    <h5 class="mb-0">Profil Pengguna</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        {{-- Placeholder untuk gambar profil --}}
                        <img src="https://via.placeholder.com/150" alt="Profil Pengguna" class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                        <h4>John Doe</h4>
                        <p class="text-muted">Kasir</p>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong><i class="fas fa-envelope me-2"></i> Email:</strong>
                            <p class="mb-0">johndoe@example.com</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong><i class="fas fa-calendar-alt me-2"></i> Bergabung Sejak:</strong>
                            <p class="mb-0">17 September 2025</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong><i class="fas fa-id-badge me-2"></i> Posisi:</strong>
                            <p class="mb-0">Kasir Utama</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong><i class="fas fa-phone me-2"></i> Nomor Telepon:</strong>
                            <p class="mb-0">0812-3456-7890</p>
                        </div>
                    </div>

                    <hr>

                    {{-- Tombol Aksi --}}
                    <div class="d-flex justify-content-center mt-4">
                        <a href="#" class="btn btn-warning me-2">
                            <i class="fas fa-edit me-2"></i> Edit Profil
                        </a>
                        <a href="#" class="btn btn-secondary">
                            <i class="fas fa-lock me-2"></i> Ganti Password
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

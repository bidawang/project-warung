@extends('layouts.app')

@section('title', 'Edit Member')

@section('content')
<div class="container-fluid mt-4">

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0">Edit Data Member</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('kasir.member.update', $member->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label fw-bold">Nama Lengkap</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ $member->name }}">
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label fw-bold">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ $member->email }}">
                        </div>

                        <div class="mb-3">
                            <label for="nomor_hp" class="form-label fw-bold">No. HP</label>
                            <input type="text" class="form-control" id="nomor_hp" name="nomor_hp" value="{{ $member->nomor_hp }}">
                        </div>

                        <div class="mb-3">
                            <label for="keterangan" class="form-label fw-bold">Keterangan</label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="3">Member VIP</textarea>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ url()->previous() }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-warning text-white">
                                <i class="fas fa-save"></i> Update Member
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

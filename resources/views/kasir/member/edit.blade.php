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
                    <form action="#" method="POST">
                        {{-- @csrf --}}
                        {{-- @method('PUT') --}}

                        <div class="mb-3">
                            <label for="name" class="form-label fw-bold">Nama Lengkap</label>
                            <input type="text" class="form-control" id="name" name="name" value="Budi Santoso">
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label fw-bold">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="budi@example.com">
                        </div>

                        <div class="mb-3">
                            <label for="telephone" class="form-label fw-bold">No. HP</label>
                            <input type="text" class="form-control" id="telephone" name="telephone" value="08123456789">
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label fw-bold">Alamat</label>
                            <textarea class="form-control" id="address" name="address" rows="3">Jl. Merpati No. 10</textarea>
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

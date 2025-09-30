@extends('layouts.app')

@section('title', 'Detail Member')

@section('content')
<div class="container-fluid mt-4">

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Detail Member</h5>
                    <a href="{{ url()->previous() }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Nama Lengkap</dt>
                        <dd class="col-sm-8">{{ $member->name }}</dd>

                        <dt class="col-sm-4">Email</dt>
                        <dd class="col-sm-8">{{ $member->email }}</dd>

                        <dt class="col-sm-4">No. HP</dt>
                        <dd class="col-sm-8">{{ $member->nomor_hp }}</dd>

                        <dt class="col-sm-4">Keterangan</dt>
                        <dd class="col-sm-8">{{ $member->keterangan }}</dd>

                        <dt class="col-sm-4">Tanggal Daftar</dt>
                        <dd class="col-sm-8">{{ $member->created_at }}</dd>
                    </dl>
                </div>
                <div class="card-footer d-flex justify-content-end">
                    <a href="{{ route('kasir.member.edit', $member->id) }}" class="btn btn-warning text-white me-2">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                </div>
            </div>

            {{-- Hutang Member --}}
            <div class="card shadow-sm">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">Daftar Hutang Member</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Jumlah Hutang Awal</th>
                                    <th>jumlah Sisa Hutang</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($hutang as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->jumlah_hutang_awal }}</td>
                                    <td>{{ $item->jumlah_sisa_hutang }}</td>
                                    <td><span class="badge bg-warning text-dark">{{ $item->status }}</span></td>
                                </tr>
                                @endforeach


                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>
@endsection

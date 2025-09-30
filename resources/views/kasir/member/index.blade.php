@extends('layouts.app')

@section('title', 'Halaman Member')

@section('content')
    <div class="container-fluid mt-4">

        <div class="row">
            <div class="col-md-12 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
                        <h5 class="mb-0">Data Member</h5>
                        <a href="{{ route('kasir.member.create') }}" class="btn btn-light">
                            <i class="fas fa-plus-circle me-2"></i> Tambah Member
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="input-group mb-3" style="max-width: 300px;">
                            <input type="text" class="form-control" placeholder="Cari member...">
                            <button class="btn btn-outline-secondary" type="button">Cari</button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Nama</th>
                                        <th scope="col">Email</th>
                                        <th scope="col">No. HP</th>
                                        <th scope="col">Keterangan</th>
                                        <th scope="col">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($member as $item)
                                        <tr>
                                            <th scope="row">{{ $loop->iteration }}</th>
                                            <td>{{ $item->name }}</td>
                                            <td>{{ $item->email }}</td>
                                            <td>{{ $item->nomor_hp }}</td>
                                            <td>{{ $item->keterangan }}</td>
                                            <td>
                                                <div class="d-flex">
                                                    <a href="{{ url('kasir/member/detail', $item->id) }}"
                                                        class="btn btn-sm btn-info text-white me-2">
                                                        <i class="fas fa-info-circle"></i> Detail
                                                    </a>
                                                    <a href="{{ url('kasir/member/edit', $item->id) }}"
                                                        class="btn btn-sm btn-warning text-white me-2">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>
                                                    <form action="{{ url('kasir/member/delete', $item->id) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger">
                                                            <i class="fas fa-trash"></i> Hapus
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>

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

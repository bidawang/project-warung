@extends('layouts.app')

@section('title', 'Daftar Member')

@section('content')
<div class="container my-4">
    <h2 class="text-center mb-4">Daftar Member</h2>

    <div class="card shadow p-4">
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Nomor HP</th>
                    <th>Kode User</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($members as $member)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $member->user->name }}</td>
                    <td>{{ $member->user->email }}</td>
                    <td>{{ $member->user->nomor_hp }}</td>
                    <td>{{ $member->kode_user }}</td>
                    <td>
                        <a href="{{ route('member.show', $member->id) }}" class="btn btn-info btn-sm">Detail</a>
                        <a href="{{ route('user.edit', $member->user->id) }}" class="btn btn-primary btn-sm">Edit</a>
                        <form action="{{ route('user.destroy', $member->user->id) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Yakin hapus member ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

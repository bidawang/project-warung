@extends('layouts.app')

@section('title', 'Tambah User')

@section('content')
<div class="container">
    <h1 class="text-center my-4">Tambah User Baru</h1>
    <div class="card p-4">
        @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $err) <li>{{ $err }}</li> @endforeach
            </ul>
        </div>
        @endif
        <form action="{{ route('user.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Role</label>
                <select name="role" id="role" class="form-select" required>
                    <option value="">-- Pilih Role --</option>
                    <option value="kasir">Kasir</option>
                    <option value="member">Member</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Nama</label>
                <input type="text" class="form-control" name="name" required value="{{ old('name') }}">
            </div>
            <div class="mb-3">
                <label class="form-label">Nomor HP</label>
                <input type="text" class="form-control" name="nomor_hp" required value="{{ old('nomor_hp') }}">
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" name="email" required value="{{ old('email') }}">
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" class="form-control" name="password" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Konfirmasi Password</label>
                <input type="password" class="form-control" name="password_confirmation" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Keterangan</label>
                <textarea class="form-control" name="keterangan">{{ old('keterangan') }}</textarea>
            </div>

            {{-- khusus kasir --}}
            <div class="mb-3 d-none" id="googleIdField">
                <label class="form-label">Google ID</label>
                <input type="text" class="form-control" name="google_id" value="{{ old('google_id') }}">
            </div>

            {{-- khusus member --}}
            <div class="mb-3 d-none" id="kodeUserField">
                <label class="form-label">Kode User</label>
                <input type="text" class="form-control" name="kode_user" value="{{ old('kode_user') }}">
            </div>

            <div class="d-flex justify-content-end">
                <a href="{{ route('user.index') }}" class="btn btn-secondary me-2">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('role').addEventListener('change', function(){
    document.getElementById('googleIdField').classList.add('d-none');
    document.getElementById('kodeUserField').classList.add('d-none');
    if(this.value === 'kasir') document.getElementById('googleIdField').classList.remove('d-none');
    if(this.value === 'member') document.getElementById('kodeUserField').classList.remove('d-none');
});
</script>
@endsection
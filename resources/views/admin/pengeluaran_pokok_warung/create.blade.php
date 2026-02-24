@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white fw-bold">
            Tambah Pengeluaran Pokok
        </div>

        <div class="card-body">

            <form action="{{ route('admin.pengeluaran-pokok-warung.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Warung</label>
                    <select name="id_warung" class="form-select" required>
                        <option value="">-- Pilih Warung --</option>
                        @foreach($warungs as $w)
                            <option value="{{ $w->id }}">{{ $w->nama_warung }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Redaksi</label>
                    <input type="text" name="redaksi" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Jumlah</label>
                    <input type="number" step="0.01" name="jumlah" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tanggal</label>
                    <input type="date" name="date" class="form-control" required>
                </div>

                <button class="btn btn-primary">Simpan</button>
                <a href="{{ route('admin.pengeluaran-pokok-warung.index') }}"
                   class="btn btn-secondary">Batal</a>
            </form>

        </div>
    </div>

</div>
@endsection
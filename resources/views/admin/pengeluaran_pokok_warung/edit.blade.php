@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white fw-bold">
            Edit Pengeluaran Pokok
        </div>

        <div class="card-body">

            <form action="{{ route('admin.pengeluaran-pokok-warung.update',$pengeluaran_pokok_warung) }}"
                  method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Warung</label>
                    <select name="id_warung" class="form-select" required>
                        @foreach($warungs as $w)
                            <option value="{{ $w->id }}"
                                {{ $pengeluaran_pokok_warung->id_warung == $w->id ? 'selected' : '' }}>
                                {{ $w->nama_warung }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Redaksi</label>
                    <input type="text"
                           name="redaksi"
                           value="{{ $pengeluaran_pokok_warung->redaksi }}"
                           class="form-control"
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Jumlah</label>
                    <input type="number"
                           step="0.01"
                           name="jumlah"
                           value="{{ $pengeluaran_pokok_warung->jumlah }}"
                           class="form-control"
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tanggal</label>
                    <input type="date"
                           name="date"
                           value="{{ \Carbon\Carbon::parse($pengeluaran_pokok_warung->date)->format('Y-m-d') }}"
                           class="form-control"
                           required>
                </div>

                <button class="btn btn-primary">Update</button>
                <a href="{{ route('admin.pengeluaran-pokok-warung.index') }}"
                   class="btn btn-secondary">Batal</a>
            </form>

        </div>
    </div>

</div>
@endsection
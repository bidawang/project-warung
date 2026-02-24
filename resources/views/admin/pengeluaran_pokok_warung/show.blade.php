@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white fw-bold">
            Detail Pengeluaran Pokok
        </div>

        <div class="card-body">

            <div class="row mb-3">
                <div class="col-md-4 fw-bold">Warung</div>
                <div class="col-md-8">
                    {{ $pengeluaran_pokok_warung->warung->nama_warung ?? '-' }}
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4 fw-bold">Redaksi</div>
                <div class="col-md-8">
                    {{ $pengeluaran_pokok_warung->redaksi }}
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4 fw-bold">Jumlah</div>
                <div class="col-md-8">
                    Rp {{ number_format($pengeluaran_pokok_warung->jumlah,0,',','.') }}
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4 fw-bold">Tanggal</div>
                <div class="col-md-8">
                    {{ \Carbon\Carbon::parse($pengeluaran_pokok_warung->date)->format('d M Y') }}
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4 fw-bold">Status</div>
                <div class="col-md-8">
                    <span class="badge 
                        {{ $pengeluaran_pokok_warung->status == 'belum terpenuhi' ? 'bg-warning text-dark' : 'bg-success' }}">
                        {{ ucfirst($pengeluaran_pokok_warung->status) }}
                    </span>
                </div>
            </div>

            <a href="{{ route('admin.pengeluaran-pokok-warung.index') }}"
               class="btn btn-secondary">
                Kembali
            </a>

        </div>
    </div>

</div>
@endsection
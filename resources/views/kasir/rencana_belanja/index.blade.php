@extends('layouts.app')

@section('title', 'Rencana Belanja')

@section('content')
<div class="container py-4">

<div class="card shadow-lg border-0 rounded-4">

    <div class="card-header bg-warning text-dark">
        <h5 class="mb-0">Rencana Belanja Kasir</h5>
    </div>

    <div class="card-body">

        {{-- NAV TABS --}}
        <ul class="nav nav-tabs mb-3">
            <li class="nav-item">
                <a class="nav-link {{ $status=='pending' ? 'active' : '' }}" href="?status=pending">Menunggu</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $status=='dikirim' ? 'active' : '' }}" href="?status=dikirim">Diterima</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $status=='selesai' ? 'active' : '' }}" href="?status=selesai">Selesai</a>
            </li>
        </ul>

        {{-- Pesan Sukses/Error --}}
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="mb-3 d-flex justify-content-between align-items-center">
            <a href="{{ route('kasir.rencanabelanja.create') }}" class="btn btn-success btn-sm">
                <i class="fas fa-plus"></i> Buat Rencana Baru
            </a>

            {{-- TOMBOL AKSI: Hanya muncul jika status 'dikirim' --}}
            @if($status == 'dikirim' && $data->count() > 0)
            <div>
                <button type="submit" form="formKonfirmasi" name="status_baru" value="tolak" class="btn btn-danger btn-sm me-2">
                    <i class="fas fa-times-circle"></i> Tolak Terpilih
                </button>
                <button type="submit" form="formKonfirmasi" name="status_baru" value="selesai" class="btn btn-primary btn-sm">
                    <i class="fas fa-check-circle"></i> Konfirmasi Selesai
                </button>
            </div>
            @endif
        </div>

        {{-- FORM PEMBUNGKUS (Hanya aktif jika status dikirim) --}}
        <form action="{{ route('kasir.rencanabelanja.konfirmasi') }}" method="POST" id="formKonfirmasi">
            @csrf
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead>
                        <tr>
                            {{-- Kolom Checkbox Header --}}
                            @if($status == 'dikirim')
                            <th width="40" class="text-center">
                                <input type="checkbox" id="select-all">
                            </th>
                            @endif
                            <th>#</th>
                            <th>Barang</th>
                            <th>Rencana</th>
                            <th>Dibeli</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                    @forelse($data as $r)
                        <tr>
                            {{-- Checkbox Body --}}
                            @if($status == 'dikirim')
                            <td class="text-center">
                                <input type="checkbox" name="barangMasuk[]" value="{{ $r->id }}" class="item-checkbox">
                            </td>
                            @endif
                            
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $r->barang->nama_barang ?? 'Barang Hilang' }}</td>

                            <td>
                                <span class="badge bg-secondary">
                                    {{ number_format($r->jumlah_awal,0,',','.') }}
                                </span>
                            </td>

                            <td>
                                <span class="badge bg-info">
                                    {{ number_format($r->jumlah_dibeli ?? 0,0,',','.') }}
                                </span>
                            </td>

                            <td>
                                <span class="badge 
                                    @if($status=='pending') bg-danger
                                    @elseif($status=='dibeli') bg-primary
                                    @elseif($status=='dikirim') bg-warning text-dark
                                    @elseif($status=='selesai') bg-success
                                    @endif
                                ">
                                    {{ ucfirst($status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $status == 'dikirim' ? '6' : '5' }}" class="text-center">
                                Tidak ada data untuk status ini.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </form>

    </div>
</div>
</div>

{{-- SCRIPT SELECT ALL --}}
@if($status == 'dikirim')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('select-all');
        const checkboxes = document.querySelectorAll('.item-checkbox');

        if (selectAll) {
            selectAll.addEventListener('change', function() {
                checkboxes.forEach(cb => cb.checked = this.checked);
            });
        }
    });
</script>
@endif
@endsection
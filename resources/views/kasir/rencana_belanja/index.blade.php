@extends('layouts.app')

@section('title', 'Rencana Belanja')

@section('content')
<div class="container py-5">

    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h2 class="fw-bold text-dark mb-1">Rencana Belanja</h2>
            <p class="text-muted mb-0">Kelola stok barang masuk dan verifikasi belanjaan.</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <a href="{{ route('kasir.rencanabelanja.create') }}" class="btn btn-success rounded-pill px-4 shadow-sm">
                <i class="fas fa-plus-circle me-2"></i>Buat Rencana Baru
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">

            <div class="bg-light px-4 pt-3 border-bottom">
                <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link px-4 rounded-pill {{ $status=='pending' ? 'active bg-danger shadow-sm' : 'text-muted' }}"
                           href="?status=pending">
                           <i class="fas fa-clock me-2"></i>Menunggu
                        </a>
                    </li>
                    <li class="nav-item ms-2">
                        <a class="nav-link px-4 rounded-pill {{ $status=='dikirim' ? 'active bg-warning text-dark shadow-sm' : 'text-muted' }}"
                           href="?status=dikirim">
                           <i class="fas fa-truck me-2"></i>Diterima
                        </a>
                    </li>
                    <li class="nav-item ms-2">
                        <a class="nav-link px-4 rounded-pill {{ $status=='selesai' ? 'active bg-success shadow-sm' : 'text-muted' }}"
                           href="?status=selesai">
                           <i class="fas fa-check-double me-2"></i>Selesai
                        </a>
                    </li>
                </ul>
            </div>

            @if($status == 'dikirim' && $data->count() > 0)
            <div class="px-4 py-3 bg-white d-flex align-items-center justify-content-between border-bottom">
                <div class="small text-muted">
                    <i class="fas fa-info-circle me-1"></i> Pilih item untuk dikonfirmasi atau ditolak
                </div>
                <div>
                    <button type="submit" form="formKonfirmasi" name="status_baru" value="tolak"
                            class="btn btn-outline-danger btn-sm rounded-pill px-3 me-2 shadow-sm">
                        <i class="fas fa-times-circle me-1"></i> Tolak Terpilih
                    </button>
                    <button type="submit" form="formKonfirmasi" name="status_baru" value="selesai"
                            class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm fw-bold">
                        <i class="fas fa-check-circle me-1"></i> Konfirmasi Selesai
                    </button>
                </div>
            </div>
            @endif

            <form action="{{ route('kasir.rencanabelanja.konfirmasi') }}" method="POST" id="formKonfirmasi">
                @csrf
                <div class="table-responsive">
                    <table class="table align-middle mb-0 table-hover">
                        <thead class="bg-light text-uppercase small fw-bold">
                            <tr>
                                @if($status == 'dikirim')
                                <th width="60" class="text-center ps-4">
                                    <div class="form-check d-flex justify-content-center">
                                        <input class="form-check-input" type="checkbox" id="select-all">
                                    </div>
                                </th>
                                @endif
                                <th class="ps-4">#</th>
                                <th>Barang</th>
                                <th class="text-center">Rencana (Qty)</th>
                                <th class="text-center">Dibeli (Qty)</th>
                                <th class="text-center pe-4">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data as $r)
                                <tr class="hover-row">
                                    @if($status == 'dikirim')
                                    <td class="text-center ps-4">
                                        <div class="form-check d-flex justify-content-center">
                                            <input class="form-check-input item-checkbox" type="checkbox" name="barangMasuk[]" value="{{ $r->id }}">
                                        </div>
                                    </td>
                                    @endif

                                    <td class="ps-4 text-muted">{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $r->barang->nama_barang ?? 'Barang Hilang' }}</div>
                                        <small class="text-muted">ID: #{{ $r->id }}</small>
                                    </td>

                                    <td class="text-center">
                                        <span class="badge bg-soft-secondary text-secondary rounded-pill px-3">
                                            {{ number_format($r->jumlah_awal,0,',','.') }}
                                        </span>
                                    </td>

                                    <td class="text-center">
                                        <span class="badge bg-soft-info text-info rounded-pill px-3">
                                            {{ number_format($r->jumlah_dibeli ?? 0,0,',','.') }}
                                        </span>
                                    </td>

                                    <td class="text-center pe-4">
                                        @php
                                            $colorClass = [
                                                'pending' => 'bg-danger',
                                                'dikirim' => 'bg-warning text-dark',
                                                'selesai' => 'bg-success'
                                            ][$status] ?? 'bg-secondary';
                                        @endphp
                                        <span class="badge {{ $colorClass }} rounded-pill px-3 py-2 fw-normal" style="min-width: 85px;">
                                            {{ ucfirst($status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fas fa-folder-open fa-3x mb-3 opacity-25"></i>
                                            <p class="mb-0">Tidak ada rencana belanja dengan status <strong>{{ $status }}</strong>.</p>
                                        </div>
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

<style>
    /* Styling Nav Pills */
    .nav-pills .nav-link {
        font-weight: 600;
        transition: all 0.3s;
        border: 1px solid transparent;
    }
    .nav-pills .nav-link:not(.active):hover {
        background-color: #fff;
        border-color: #dee2e6;
    }

    /* Soft Badges */
    .bg-soft-secondary { background-color: #f0f2f5; color: #6c757d; }
    .bg-soft-info { background-color: #e0f7fa; color: #00bcd4; }

    /* Table Enhancements */
    .hover-row:hover {
        background-color: #fbfbfb;
    }
    .form-check-input:checked {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }

    .table thead th {
        border-top: none;
        letter-spacing: 0.5px;
        color: #8898aa;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('select-all');
        const checkboxes = document.querySelectorAll('.item-checkbox');

        if (selectAll) {
            selectAll.addEventListener('change', function() {
                checkboxes.forEach(cb => {
                    cb.checked = this.checked;
                    // Trigger style change on row if needed
                    cb.closest('tr').classList.toggle('bg-light', this.checked);
                });
            });
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                this.closest('tr').classList.toggle('bg-light', this.checked);
            });
        });
    });
</script>
@endsection

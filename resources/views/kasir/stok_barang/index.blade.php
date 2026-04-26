@extends('layouts.app')

@section('title', 'Manajemen Stok')

@section('content')
<div class="container-fluid py-3 py-md-4">
    <div class="row">
        <div class="col-12">
            {{-- HEADER & TABS --}}
            <div class="card border-0 shadow-sm rounded-4 mb-3">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0 text-dark">Manajemen Barang</h5>
                        
                        {{-- TOMBOL BARANG MASUK (GANTI DARI LONCENG) --}}
                        <a href="{{ url('/kasir/stok-barang/barang-masuk') }}" 
                           class="btn btn-primary rounded-pill px-3 shadow-sm d-flex align-items-center gap-2">
                      
                            <span class="fw-bold" style="font-size: 0.55rem;">Barang Masuk</span>
                            {{-- Badge Notif diletakkan di dalam tombol --}}
                            <span id="notifCount" class="badge rounded-pill bg-danger border border-white" 
                                  style="display:none; font-size: 10px; margin-left: 5px;">0</span>
                        </a>
                    </div>

                    {{-- NAVIGASI SUB-BAB (TABS) --}}
                    <ul class="nav nav-pills nav-fill bg-light rounded-pill p-1" id="stokTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active rounded-pill py-2" id="stok-tab" data-bs-toggle="tab" data-bs-target="#stok-content" type="button" role="tab">
                                <i class="fas fa-boxes-stacked me-2"></i>Stok
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-pill py-2" id="keluar-tab" data-bs-toggle="tab" data-bs-target="#keluar-content" type="button" role="tab">
                                <i class="fas fa-box-open me-2"></i>Keluar Hari Ini
                            </button>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- ISI KONTEN (TAB PANES) --}}
            <div class="tab-content" id="stokTabContent">
                
                {{-- SUB-BAB 1: STOK INVENTARIS --}}
                <div class="tab-pane fade show active" id="stok-content" role="tabpanel">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-3 p-md-4">
                            {{-- Search Bar --}}
                            <form method="GET" action="{{ url('/kasir/stok-barang') }}" class="mb-4">
                                <div class="input-group bg-light rounded-pill p-1 border shadow-sm">
                                    <input type="text" name="search" class="form-control border-0 bg-transparent ps-3" 
                                           placeholder="Cari produk..." value="{{ $search }}">
                                    <button class="btn btn-primary rounded-pill px-4" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </form>

                            {{-- TAMPILAN DESKTOP (TABLE) --}}
                            <div class="table-responsive d-none d-md-block">
                                <table class="table align-middle table-hover">
                                    <thead class="bg-light">
                                        <tr class="text-muted small uppercase">
                                            <th>Produk</th>
                                            <th class="text-center">Sisa Stok</th>
                                            <th>Harga Jual</th>
                                            <th class="text-center">Kadaluarsa</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($stokBarang as $item)
                                        <tr>
                                            <td>
                                                <div class="fw-bold">{{ $item->barang->nama_barang ?? '-' }}</div>
                                                <small class="text-muted">ID: STK-{{ $item->id }}</small>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge rounded-pill {{ $item->stok_saat_ini <= 5 ? 'bg-soft-danger text-danger' : 'bg-soft-success text-success' }} px-3 py-2">
                                                    {{ $item->stok_saat_ini }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="fw-bold text-primary">Rp{{ number_format($item->harga_jual, 0, ',', '.') }}</div>
                                                @if($item->kuantitas_list->count() > 0)
                                                    <small class="text-muted">+{{ $item->kuantitas_list->count() }} Harga Grosir</small>
                                                @endif
                                            </td>
                                            <td class="text-center small">{{ $item->tanggal_kadaluarsa ?? '-' }}</td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="4" class="text-center py-5">Data tidak ditemukan.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            {{-- TAMPILAN MOBILE (CARDS) --}}
                            <div class="d-md-none">
                                @foreach($stokBarang as $item)
                                <div class="card border mb-3 rounded-4 shadow-sm">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="fw-bold mb-0">{{ $item->barang->nama_barang ?? '-' }}</h6>
                                                <small class="text-muted">ID: {{ $item->id }}</small>
                                            </div>
                                            <span class="badge rounded-pill {{ $item->stok_saat_ini <= 5 ? 'bg-danger' : 'bg-success' }}">
                                                Stok: {{ $item->stok_saat_ini }}
                                            </span>
                                        </div>
                                        <hr class="my-2 opacity-50">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="text-primary fw-bold">Rp{{ number_format($item->harga_jual, 0, ',', '.') }}</div>
                                            <a href="{{ route('kasir.kuantitas.create', ['id_stok_warung' => $item->id]) }}" class="btn btn-sm btn-outline-primary rounded-pill py-0">
                                                + Grosir
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            
                            <div class="mt-3">
                                {{ $stokBarang->links() }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SUB-BAB 2: BARANG KELUAR HARI INI --}}
                <div class="tab-pane fade" id="keluar-content" role="tabpanel">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-0">
                            @include('kasir.stok_barang.barang_keluar_list', ['barangKeluar' => $barangKeluar])
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<style>
    /* Styling Tabs */
    .nav-pills .nav-link { color: #6c757d; font-weight: 600; font-size: 0.85rem; transition: 0.3s; }
    .nav-pills .nav-link.active { background-color: #0d6efd; color: white; box-shadow: 0 4px 10px rgba(13, 110, 253, 0.25); }
    
    /* Styling Table & Badges */
    .bg-soft-success { background-color: #e8f5e9; }
    .bg-soft-danger { background-color: #ffebee; }
    .table > :not(caption) > * > * { border-bottom-width: 1px; padding: 1rem 0.5rem; }

    /* Custom button Barang Masuk agar pas di HP */
    @media (max-width: 576px) {
        .btn-primary span { font-size: 0.75rem; }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const notifCountEl = document.getElementById('notifCount');
    function updateNotif(count) {
        if (count > 0) {
            notifCountEl.style.display = 'inline-block';
            notifCountEl.textContent = count;
        } else {
            notifCountEl.style.display = 'none';
        }
    }
    // API Call untuk notif
    fetch('{{ url("/kasir/api/notif-barang-masuk") }}', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(res => res.json()).then(data => updateNotif(data.count)).catch(() => updateNotif(0));
});
</script>
@endsection
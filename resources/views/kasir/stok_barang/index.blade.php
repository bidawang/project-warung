@extends('layouts.app')

@section('title', 'Stok & Barang Keluar')

@section('content')
<div class="container-fluid py-4" style="background-color: #f8f9fa;">

    <div class="row g-4">
        {{-- KOLOM KIRI: DAFTAR BARANG KELUAR --}}
        <div class="col-xl-4 col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="fas fa-box-open me-2 text-warning"></i>Barang Keluar
                    </h5>
                </div>
                <div class="card-body p-0">
                    @include('kasir.stok_barang.barang_keluar_list', ['barangKeluar' => $barangKeluar])
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: DAFTAR STOK BARANG --}}
        <div class="col-xl-8 col-lg-7">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="fas fa-inventory me-2 text-primary"></i>Stok Inventaris
                    </h5>

                    {{-- Notifikasi Button --}}
                    <a href="{{ url('/kasir/stok-barang/barang-masuk') }}"
                       class="btn btn-light rounded-circle shadow-sm position-relative p-2"
                       style="width: 45px; height: 45px;"
                       id="notifBarangMasukBtn">
                        <i class="fas fa-bell text-primary fs-5"></i>
                        <span id="notifCount"
                              class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-white"
                              style="display:none; font-size: 10px;">
                            0
                        </span>
                    </a>
                </div>

                <div class="card-body p-4">
                    {{-- SEARCH BAR MODERN --}}
                    <form method="GET" action="{{ url('/kasir/stok-barang') }}" class="mb-4">
                        <div class="input-group bg-light rounded-pill p-1 shadow-sm border">
                            <input type="text" name="search" class="form-control border-0 bg-transparent ps-3"
                                   placeholder="Cari nama produk..." value="{{ $search }}">
                            <button class="btn btn-primary rounded-pill px-4" type="submit">
                                <i class="fas fa-search me-2"></i>Cari
                            </button>
                        </div>
                    </form>

                    {{-- TABLE STOK --}}
                    <div class="table-responsive">
                        <table class="table align-middle table-hover border-0">
                            <thead class="bg-light">
                                <tr class="text-muted small uppercase" style="letter-spacing: 0.5px;">
                                    <th class="ps-3 border-0">Produk</th>
                                    <th class="text-center border-0">Sisa Stok</th>
                                    <th class="border-0">Skema Harga Jual</th>
                                    <th class="text-center border-0">Kadaluarsa</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stokBarang as $item)
                                <tr>
                                    <td class="ps-3 py-3">
                                        <div class="fw-bold text-dark">{{ $item->barang->nama_barang ?? '-' }}</div>
                                        <div class="text-muted small">ID: STK-{{ str_pad($item->id, 4, '0', STR_PAD_LEFT) }}</div>
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $stokClass = $item->stok_saat_ini <= 5 ? 'bg-soft-danger text-danger' : 'bg-soft-success text-success';
                                        @endphp
                                        <span class="badge rounded-pill {{ $stokClass }} px-3 py-2 fs-6">
                                            {{ $item->stok_saat_ini }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column gap-1">
                                            {{-- Default Price --}}
                                            <div class="price-pill d-flex justify-content-between align-items-center shadow-sm border rounded-pill px-3 py-1 bg-white">
                                                <span class="small text-muted">1 Unit:</span>
                                                <span class="fw-bold text-primary">Rp{{ number_format($item->harga_jual, 0, ',', '.') }}</span>
                                            </div>

                                            {{-- Grosir / Multi-Kuantitas --}}
                                            @foreach($item->kuantitas_list as $kuantitas)
                                            <div class="price-pill-alt d-flex justify-content-between align-items-center border rounded-pill px-3 py-1 bg-light">
                                                <span class="small text-muted">{{ $kuantitas->jumlah }} Unit:</span>
                                                <div class="d-flex align-items-center">
                                                    <span class="fw-bold me-2" style="font-size: 0.85rem;">Rp{{ number_format($kuantitas->harga_jual, 0, ',', '.') }}</span>
                                                    <div class="btn-group">
                                                        <a href="{{ route('kasir.kuantitas.edit', $kuantitas->id) }}" class="text-warning me-2 small"><i class="fas fa-edit"></i></a>
                                                        <form action="{{ route('kasir.kuantitas.destroy', $kuantitas->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus?')">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="border-0 bg-transparent text-danger p-0 small"><i class="fas fa-trash"></i></button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach

                                            <a href="{{ route('kasir.kuantitas.create', ['id_stok_warung' => $item->id]) }}"
                                               class="btn btn-sm btn-outline-primary rounded-pill border-dashed mt-1 py-0 px-2" style="border-style: dashed !important;">
                                                <i class="fas fa-plus-circle small me-1"></i>Tambah Harga
                                            </a>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @if($item->tanggal_kadaluarsa)
                                            <div class="small fw-bold {{ \Carbon\Carbon::parse($item->tanggal_kadaluarsa)->isPast() ? 'text-danger' : 'text-dark' }}">
                                                {{ \Carbon\Carbon::parse($item->tanggal_kadaluarsa)->format('d/m/Y') }}
                                            </div>
                                            <div class="text-muted" style="font-size: 10px;">{{ \Carbon\Carbon::parse($item->tanggal_kadaluarsa)->diffForHumans() }}</div>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <img src="https://illustrations.popsy.co/gray/empty-box.svg" style="width: 120px;" class="mb-3 opacity-50">
                                        <p class="text-muted">Data stok tidak ditemukan.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- PAGINATION --}}
                    <div class="d-flex justify-content-between align-items-center mt-4 px-2">
                        <div class="small text-muted">Menampilkan {{ $stokBarang->firstItem() }} - {{ $stokBarang->lastItem() }} dari {{ $stokBarang->total() }} barang</div>
                        <div>{{ $stokBarang->links() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-soft-success { background-color: #e8f5e9; color: #2e7d32; }
    .bg-soft-danger { background-color: #ffebee; color: #c62828; }
    .price-pill { font-size: 0.9rem; min-width: 160px; }
    .price-pill-alt { font-size: 0.8rem; min-width: 160px; }
    .border-dashed { border-width: 1px !important; }
    .table > :not(caption) > * > * { padding: 1rem 0.5rem; }
    .nav-link.active { font-weight: bold; border-bottom: 2px solid #0d6efd !important; }
</style>

<script>
// Script Notifikasi Anda (Sudah benar secara logika)
document.addEventListener('DOMContentLoaded', function() {
    const notifCountEl = document.getElementById('notifCount');

    function updateNotif(count) {
        if (count > 0) {
            notifCountEl.style.display = 'inline-block';
            notifCountEl.textContent = count > 99 ? '99+' : count;
            // Animasi pulse jika ada notif baru
            notifCountEl.classList.add('animate__animated', 'animate__pulse', 'animate__infinite');
        } else {
            notifCountEl.style.display = 'none';
        }
    }

    if (window.Echo) {
        window.Echo.channel('user.{{ auth()->id() }}')
            .listen('BarangMasukUpdated', (e) => {
                updateNotif(e.count);
            });
    }

    fetch('{{ url("/kasir/api/notif-barang-masuk") }}', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => updateNotif(data.count))
    .catch(() => updateNotif(0));
});
</script>
@endsection

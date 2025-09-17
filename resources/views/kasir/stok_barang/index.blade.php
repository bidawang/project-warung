@extends('layouts.app')

@section('title', 'Data Stok Warung')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
            <h5 class="mb-0">Daftar Stok Barang</h5>
            {{-- Tombol Notifikasi Barang Masuk --}}
            <a href="{{ url('/kasir/stok-barang/barang-masuk') }}" class="btn btn-primary position-relative" title="Lihat barang masuk dari owner" id="notifBarangMasukBtn">
                <i class="fas fa-bell fa-lg"></i>
                <span id="notifCount" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display:none;">
                    0
                    <span class="visually-hidden">notifikasi barang baru</span>
                </span>
            </a>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ url('/kasir/stok-barang') }}" class="input-group mb-4">
                <input type="text" name="search" class="form-control" placeholder="Cari nama barang..." value="{{ $search }}">
                <button class="btn btn-outline-secondary" type="submit">Cari</button>
            </form>

            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Nama Barang</th>
                            <th scope="col">Stok</th>
                            <th scope="col">Harga Jual</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stokBarang as $item)
                        <tr>
                            <th scope="row">{{ $loop->iteration + ($stokBarang->currentPage() - 1) * $stokBarang->perPage() }}</th>
                            <td>{{ $item->barang->nama_barang ?? '-' }}</td>
                            <td>{{ $item->stok_saat_ini }}</td>
                            <td>Rp {{ number_format($item->harga_jual, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center">Belum ada data stok barang.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-3">
                {{ $stokBarang->links() }}
            </div>
        </div>
    </div>
</div>

{{-- Laravel Echo dan Pusher JS --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const notifCountEl = document.getElementById('notifCount');

    function updateNotif(count) {
        if (count > 0) {
            notifCountEl.style.display = 'inline-block';
            notifCountEl.textContent = count > 99 ? '99+' : count;
        } else {
            notifCountEl.style.display = 'none';
        }
    }

    // Subscribe ke channel user dengan Laravel Echo
    window.Echo.channel('user.{{ auth()->id() }}')
        .listen('BarangMasukUpdated', (e) => {
            updateNotif(e.count);
        });

    // Fetch count awal via AJAX agar badge langsung tampil saat load halaman
    fetch('{{ url("/kasir/api/notif-barang-masuk") }}', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => updateNotif(data.count))
    .catch(() => updateNotif(0));
});
</script>
@endsection
@extends('layouts.app')

@section('title', 'Data Stok Warung')

@section('content')
<div class="">
    <div class="card shadow-lg border-0 rounded-3">
        <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white py-3 rounded-top-3">
            <h5 class="mb-0">Daftar Stok Barang</h5>
            {{-- Tombol Notifikasi Barang Masuk --}}
            <a href="{{ url('/kasir/stok-barang/barang-masuk') }}"
               class="btn btn-outline-light position-relative"
               title="Lihat barang masuk dari owner"
               id="notifBarangMasukBtn">
                <i class="fas fa-bell fa-lg"></i>
                <span id="notifCount"
                      class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                      style="display:none;">
                    0
                    <span class="visually-hidden">notifikasi barang baru</span>
                </span>
            </a>
        </div>

        <div class="card-body p-4">
            {{-- Search --}}
            <form method="GET"
                  action="{{ url('/kasir/stok-barang') }}"
                  class="input-group mb-4 rounded-pill overflow-hidden shadow-sm">
                <input type="text"
                       name="search"
                       class="form-control border-0 ps-3"
                       placeholder="Cari nama barang..."
                       value="{{ $search }}">
                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-search me-1"></i> Cari
                </button>
            </form>

            {{-- Table --}}
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead class="bg-light">
                        <tr>
                            <th scope="col" class="text-center">#</th>
                            <th scope="col">Nama Barang</th>
                            <th scope="col" class="text-center">Stok</th>
                            <th scope="col">Harga & Kuantitas</th>
                            <th scope="col" class="text-center">Tanggal Kadaluarsa</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stokBarang as $item)
                        <tr>
                            <th scope="row" class="text-center">
                                {{ $loop->iteration + ($stokBarang->currentPage() - 1) * $stokBarang->perPage() }}
                            </th>
                            <td>{{ $item->barang->nama_barang ?? '-' }}</td>
                            <td class="text-center">{{ $item->stok_saat_ini }}</td>
                            <td>
                                <ul class="list-unstyled mb-2">
                                    {{-- Harga 1 unit --}}
                                    <li class="d-flex justify-content-between align-items-center mb-1 p-2 border rounded-3 bg-light">
                                        <span>
                                            1 unit:
                                            <span class="fw-bold">
                                                Rp {{ number_format($item->harga_jual, 0, ',', '.') }}
                                            </span>
                                        </span>
                                    </li>

                                    {{-- Harga kuantitas tambahan --}}
                                    @forelse($item->kuantitas as $kuantitas)
                                    <li class="d-flex justify-content-between align-items-center mb-1 p-2 border rounded-3 bg-light">
                                        <span>
                                            {{ $kuantitas->jumlah }} unit:
                                            <span class="fw-bold">
                                                Rp {{ number_format($kuantitas->harga_jual, 0, ',', '.') }}
                                            </span>
                                        </span>
                                        <span>
                                            <a href="{{ route('kasir.kuantitas.edit', $kuantitas->id) }}"
                                               class="btn btn-sm btn-outline-warning rounded-pill">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('kasir.kuantitas.destroy', $kuantitas->id) }}"
                                                  method="POST"
                                                  class="d-inline"
                                                  onsubmit="return confirm('Yakin hapus kuantitas ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </span>
                                    </li>
                                    @empty
                                    {{-- Tidak ada kuantitas tambahan --}}
                                    @endforelse
                                </ul>

                                {{-- Tombol tambah kuantitas --}}
                                @if($item->id)
                                <a href="{{ route('kasir.kuantitas.create', ['id_stok_warung' => $item->id]) }}"
                                   class="btn btn-sm btn-primary rounded-pill mt-2">
                                    <i class="fas fa-plus me-1"></i> Tambah
                                </a>
                                @endif
                            </td>
                            <td class="text-center">
                                {{ $item->tanggal_kadaluarsa
                                    ? \Carbon\Carbon::parse($item->tanggal_kadaluarsa)->format('d-m-Y')
                                    : 'Tidak Ada' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                Belum ada data stok barang.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="d-flex justify-content-center mt-3">
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

    // Subscribe ke channel user
    window.Echo.channel('user.{{ auth()->id() }}')
        .listen('BarangMasukUpdated', (e) => {
            updateNotif(e.count);
        });

    // Fetch count awal
    fetch('{{ url("/kasir/api/notif-barang-masuk") }}', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => updateNotif(data.count))
    .catch(() => updateNotif(0));
});
</script>
@endsection

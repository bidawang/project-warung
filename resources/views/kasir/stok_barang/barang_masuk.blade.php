@extends('layouts.app')

@section('title', 'Notifikasi Barang Masuk')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">Daftar Barang Masuk</h5>
        </div>
        <div class="card-body">
            {{-- Form untuk mengonfirmasi banyak barang sekaligus --}}
            <form action="{{ url('kasir/stok-barang/barang-masuk/konfirmasi') }}" method="POST">
                @csrf

                {{-- Tombol aksi di atas kanan tabel --}}
                <div class="d-flex justify-content-end mb-3">
                    <button type="submit" name="action" value="reject" class="btn btn-danger me-2">
                        <i class="fas fa-times-circle me-2"></i> Tolak
                    </button>
                    <button type="submit" name="action" value="confirm" class="btn btn-success">
                        <i class="fas fa-check-circle me-2"></i> Konfirmasi
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead>
                            <tr>
                                <th scope="col">
                                    {{-- Kotak centang untuk memilih semua item --}}
                                    <input type="checkbox" id="select-all">
                                </th>
                                <th scope="col">#</th>
                                <th scope="col">Nama Barang</th>
                                <th scope="col">Jumlah</th>
                                <th scope="col">Tanggal Masuk</th>
                                <th scope="col">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($barangMasuk as $index => $bm)
                            <tr>
                                <td>
                                    <input type="checkbox" name="item_ids[]" value="{{ $bm->id }}">
                                </td>
                                <th scope="row">{{ $loop->iteration + ($barangMasuk->currentPage() - 1) * $barangMasuk->perPage() }}</th>
                                <td>{{ $bm->stokWarung->barang->nama_barang ?? '-' }}</td>
                                <td>{{ $bm->jumlah }} {{ $bm->stokWarung->barang->satuan ?? 'pcs' }}</td>
                                <td>{{ $bm->created_at?->format('Y-m-d') ?? '-' }}</td>
                                <td>{{ $bm->keterangan ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">Belum ada notifikasi barang masuk.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-3">
                    {{ $barangMasuk->links() }}
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Script untuk select all checkbox --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAllCheckbox = document.getElementById('select-all');
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('input[name="item_ids[]"]');
                checkboxes.forEach(cb => cb.checked = this.checked);
            });
        }
    });
</script>
@endsection
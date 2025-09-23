@extends('layouts.app')

@section('title', 'Notifikasi Barang Masuk')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">Daftar Barang Masuk</h5>
        </div>
        <div class="card-body">
            {{-- Pesan Sukses atau Error --}}
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            {{-- Form untuk mengonfirmasi banyak barang sekaligus --}}
            <form action="{{ route('kasir.barang-masuk.konfirmasi') }}" method="POST">
                @csrf

                {{-- Tombol aksi di atas kanan tabel --}}
                <div class="d-flex justify-content-end mb-3">
                    <button type="submit" name="status_baru" value="tolak" class="btn btn-danger me-2">
                        <i class="fas fa-times-circle me-2"></i> Tolak
                    </button>
                    <button type="submit" name="status_baru" value="terima" class="btn btn-success">
                        <i class="fas fa-check-circle me-2"></i> Konfirmasi
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th scope="col" class="text-center">
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
                            @forelse($barangMasuk as $bm)
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" name="barangMasuk[]" value="{{ $bm->id }}">
                                </td>
                                <th scope="row">{{ $loop->iteration + ($barangMasuk->currentPage() - 1) * $barangMasuk->perPage() }}</th>
                                <td>{{ $bm->stokWarung->barang->nama_barang ?? '-' }}</td>
                                <td>{{ $bm->jumlah }} {{ $bm->stokWarung->barang->satuan ?? 'pcs' }}</td>
                                <td>{{ $bm->created_at?->format('d M Y') ?? '-' }}</td>
                                <td>{{ $bm->keterangan ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Belum ada notifikasi barang masuk.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="d-flex justify-content-center mt-3">
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
        const checkboxes = document.querySelectorAll('input[name="barangMasuk[]"]');

        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                checkboxes.forEach(cb => cb.checked = this.checked);
            });
        }
    });
</script>
@endsection

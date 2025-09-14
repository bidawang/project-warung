@extends('layouts.app')

@section('title', 'Data Barang Masuk')

@section('content')
<div class="container">
    <h1 class="mb-4">Data Barang Masuk</h1>

    {{-- Nav Tabs --}}
    @php
    $tabs = ['pending' => 'Pending', 'terima' => 'Diterima', 'tolak' => 'Ditolak'];
    @endphp
    <ul class="nav nav-tabs mb-4">
        @foreach($tabs as $key => $label)
        <li class="nav-item">
            <a class="nav-link {{ $status === $key ? 'active' : '' }}"
                href="{{ route('barangmasuk.index', ['status' => $key, 'search' => $search]) }}">
                {{ $label }}
            </a>
        </li>
        @endforeach
    </ul>

    {{-- Form Pencarian --}}
    <form method="GET" action="{{ route('barangmasuk.index') }}" class="mb-3">
        <input type="hidden" name="status" value="{{ $status }}">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Cari Nama Barang / Transaksi"
                value="{{ $search }}">
            <button type="submit" class="btn btn-outline-secondary">Cari</button>
        </div>
    </form>

    {{-- Tabel Data --}}
    @if(Auth::check() && Auth::user()->role === 'kasir')
    @if($status === 'pending')
    <form id="updateStatusForm" action="{{ route('barangmasuk.update_status') }}" method="POST">
        @csrf
        @method('POST')

        <div class="d-flex justify-content-between mb-3 align-items-center">
            <h5 class="mb-0">Barang Masuk Pending</h5>
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    Aksi
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <li><button type="button" class="dropdown-item"
                            onclick="submitStatus('terima', 'Yakin ingin menerima barang-barang terpilih?')">Terima</button></li>
                    <li><button type="button" class="dropdown-item"
                            onclick="submitStatus('tolak', 'Yakin ingin menolak barang-barang terpilih?')">Tolak</button></li>
                </ul>
            </div>
        </div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        @if($status === 'pending')
                        <th><input type="checkbox" id="checkAll"></th>
                        @endif
                        <th>Transaksi</th>
                        <th>Barang</th>
                        <th>Jumlah</th>
                        <th>Harga (setelah markup)</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($barangMasuk as $bm)
                    <tr>
                        @if($status === 'pending')
                        <td><input type="checkbox" name="barangMasuk[]" value="{{ $bm->id }}"></td>
                        @endif

                        <td>{{ $bm->transaksiBarang->created_at?->format('d/m/Y H:i') ?? '-' }}</td>
                        <td>{{ $bm->stokWarung->barang->nama_barang ?? '-' }}</td>
                        <td>{{ $bm->jumlah }}</td>

                        <td>
                            @if($bm->harga_final_total > 0)
                            Rp {{ number_format($bm->harga_final_total, 0, ',', '.') }}
                            <br>
                            <small class="text-muted">
                                (Rp {{ number_format($bm->harga_final_satuan, 0, ',', '.') }} /pcs,
                                +{{ $bm->markup_percent }}%)
                            </small>
                            @else
                            -
                            @endif
                        </td>

                        <td>{{ ucfirst($bm->status) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ $status === 'pending' ? 6 : 5 }}" class="text-center">
                            Belum ada data barang masuk.
                        </td>
                    </tr>
                    @endforelse
                </tbody>

            </table>
        </div>



        {{-- Pagination --}}
        <div class="mt-3">
            {{ $barangMasuk->links() }}
        </div>

        @if($status === 'pending')
        <input type="hidden" name="status_baru" id="statusBaru">
    </form>
    @endif
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkAll = document.getElementById('checkAll');
        if (checkAll) {
            checkAll.addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('input[name="barangMasuk[]"]');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
            });
        }
    });

    function submitStatus(status, message) {
        const selectedItems = document.querySelectorAll('input[name="barangMasuk[]"]:checked');
        if (selectedItems.length === 0) {
            alert('Pilih setidaknya satu barang untuk diperbarui.');
            return;
        }

        if (confirm(message)) {
            document.getElementById('statusBaru').value = status;
            document.getElementById('updateStatusForm').submit();
        }
    }
</script>
@endsection
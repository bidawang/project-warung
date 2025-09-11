@extends('layouts.app')

@section('title', 'Data Barang Masuk')

@section('content')
<div class="container">
    <h1 class="mb-4">Data Barang Masuk</h1>

    {{-- Tampilan untuk Admin 
    @if(Auth::check() && Auth::user()->role === 'admin')--}}
        <a href="{{ route('barangmasuk.create') }}" class="btn btn-primary mb-3">Tambah Barang Masuk</a>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Transaksi</th>
                        <th>Stok Warung</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($barangMasuk as $bm)
                        <tr>
                            <td>{{ $bm->id }}</td>
                            <td>{{ $bm->transaksiBarang->id ?? '-' }}</td>
                            <td>{{ $bm->stokWarung->id ?? '-' }}</td>
                            <td>{{ $bm->jumlah }}</td>
                            <td>{{ $bm->status }}</td>
                            <td>
                                <a href="{{ route('barangmasuk.edit', $bm->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form action="{{ route('barangmasuk.destroy', $bm->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus data ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    {{-- @endif

    Tampilan untuk Kasir 
    @if(Auth::check() && Auth::user()->role === 'kasir')--}}
        <form id="updateStatusForm" action="{{ route('barangmasuk.update_status') }}" method="POST">
            @csrf
            @method('POST')
            <div class="d-flex justify-content-between mb-3 align-items-center">
                <h5 class="mb-0">Barang Masuk Pending</h5>
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                        Aksi
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <li><button type="button" class="dropdown-item" onclick="submitStatus('terima', 'Yakin ingin menerima barang-barang terpilih?')">Terima</button></li>
                        <li><button type="button" class="dropdown-item" onclick="submitStatus('tolak', 'Yakin ingin menolak barang-barang terpilih?')">Tolak</button></li>
                    </ul>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th><input type="checkbox" id="checkAll"></th>
                            <th>ID</th>
                            <th>Transaksi</th>
                            <th>Stok Warung</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($barangMasuk as $bm)
                            <tr>
                                <td><input type="checkbox" name="barangMasuk[]" value="{{ $bm->id }}"></td>
                                <td>{{ $bm->id }}</td>
                                <td>{{ $bm->transaksiBarang->id ?? '-' }}</td>
                                <td>{{ $bm->stokWarung->id ?? '-' }}</td>
                                <td>{{ $bm->jumlah }}</td>
                                <td>{{ $bm->status }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Belum ada data barang masuk.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <input type="hidden" name="status_baru" id="statusBaru">
        </form>
    {{--@endif--}}
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

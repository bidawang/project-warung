@extends('layouts.app')

@section('title', 'Data Mutasi Barang')

@section('content')
<div class="container">
    <h1 class="mb-4">Data Mutasi Barang</h1>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Tampilan untuk Admin 
    @if(Auth::check() && Auth::user()->role === 'admin')--}}
    <a href="{{ route('mutasibarang.create') }}" class="btn btn-primary mb-3">Tambah Mutasi</a>
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Barang</th>
                    <th>Warung Asal</th>
                    <th>Warung Tujuan</th>
                    <th>Jumlah</th>
                    <th>Status</th>
                    <th>Keterangan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mutasi as $row)
                <tr>
                    <td>{{ $row->id }}</td>
                    <td>{{ $row->stokWarung->barang->nama_barang ?? '-' }}</td>
                    <td>{{ $row->warungAsal->nama_warung ?? '-' }}</td>
                    <td>{{ $row->warungTujuan->nama_warung ?? '-' }}</td>
                    <td>{{ $row->jumlah }}</td>
                    <td>{{ ucfirst($row->status) }}</td>
                    <td>{{ $row->keterangan }}</td>
                    <td>
                        <a href="{{ route('mutasibarang.edit', $row->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('mutasibarang.destroy', $row->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus data ini?')">
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
    @if(Auth::check() && Auth::user()->role === 'kasir') --}}
    <form id="updateStatusForm" action="{{ route('mutasibarang.update_status') }}" method="POST">
        @csrf
        @method('POST')
        <div class="d-flex justify-content-between mb-3 align-items-center">
            <h5 class="mb-0">Mutasi Barang Pending</h5>
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                    Aksi
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <li><button type="button" class="dropdown-item" onclick="submitStatus('terima', 'Yakin ingin menerima mutasi barang terpilih?')">Terima</button></li>
                    <li><button type="button" class="dropdown-item" onclick="submitStatus('tolak', 'Yakin ingin menolak mutasi barang terpilih?')">Tolak</button></li>
                </ul>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th><input type="checkbox" id="checkAll"></th>
                        <th>ID</th>
                        <th>Barang</th>
                        <th>Warung Asal</th>
                        <th>Warung Tujuan</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mutasi as $row)
                    <tr>
                        <td><input type="checkbox" name="mutasiBarang[]" value="{{ $row->id }}"></td>
                        <td>{{ $row->id }}</td>
                        <td>{{ $row->stokWarung->barang->nama_barang ?? '-' }}</td>
                        <td>{{ $row->warungAsal->nama_warung ?? '-' }}</td>
                        <td>{{ $row->warungTujuan->nama_warung ?? '-' }}</td>
                        <td>{{ $row->jumlah }}</td>
                        <td>{{ ucfirst($row->status) }}</td>
                        <td>{{ $row->keterangan }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">Belum ada data mutasi barang.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <input type="hidden" name="status_baru" id="statusBaru">
    </form>
   {{-- @endif--}}
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkAll = document.getElementById('checkAll');
        if (checkAll) {
            checkAll.addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('input[name="mutasiBarang[]"]');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
            });
        }
    });

    function submitStatus(status, message) {
        const selectedItems = document.querySelectorAll('input[name="mutasiBarang[]"]:checked');
        if (selectedItems.length === 0) {
            alert('Pilih setidaknya satu mutasi barang untuk diperbarui.');
            return;
        }

        if (confirm(message)) {
            document.getElementById('statusBaru').value = status;
            document.getElementById('updateStatusForm').submit();
        }
    }
</script>
@endsection
@extends('layouts.app')

@section('title', 'Data Mutasi Barang')

@section('content')
<div class="container">
    <h1 class="mb-4">Data Mutasi Barang</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('mutasibarang.create') }}" class="btn btn-primary mb-3">Tambah Mutasi</a>

    {{-- TAB UTAMA --}}
    <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" id="masuk-tab" data-bs-toggle="tab" data-bs-target="#masuk"
                type="button" role="tab" aria-controls="masuk" aria-selected="true">
                Mutasi Masuk 📥
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="keluar-tab" data-bs-toggle="tab" data-bs-target="#keluar"
                type="button" role="tab" aria-controls="keluar" aria-selected="false">
                Mutasi Keluar 📤
            </button>
        </li>
    </ul>

    <div class="tab-content">
        {{-- ================== MUTASI MASUK ================== --}}
        <div class="tab-pane fade show active" id="masuk" role="tabpanel" aria-labelledby="masuk-tab">
            <form id="updateStatusForm" action="{{ route('mutasibarang.update_status') }}" method="POST">
                @csrf
                <input type="hidden" name="status_baru" id="statusBaru">

                <div class="d-flex justify-content-between mb-3 align-items-center">
                    <h5 class="mb-0">Mutasi Masuk Pending</h5>
                    <div>
                        <button type="button" class="btn btn-success btn-sm"
                            onclick="submitStatus('terima', 'Yakin ingin menerima mutasi terpilih?')">Terima</button>
                        <button type="button" class="btn btn-danger btn-sm"
                            onclick="submitStatus('tolak', 'Yakin ingin menolak mutasi terpilih?')">Tolak</button>
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
                                <th>Jumlah</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($mutasiMasuk as $row)
                                @if($row->status === 'pending')
                                <tr>
                                    <td><input type="checkbox" name="mutasiBarang[]" value="{{ $row->id }}"></td>
                                    <td>{{ $row->id }}</td>
                                    <td>{{ $row->stokWarung->barang->nama_barang ?? '-' }}</td>
                                    <td>{{ $row->warungAsal->nama_warung ?? '-' }}</td>
                                    <td>{{ $row->jumlah }}</td>
                                    <td>{{ ucfirst($row->status) }}</td>
                                    <td>{{ $row->keterangan }}</td>
                                </tr>
                                @endif
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">Belum ada mutasi masuk.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </form>
        </div>

        {{-- ================== MUTASI KELUAR ================== --}}
        <div class="tab-pane fade" id="keluar" role="tabpanel" aria-labelledby="keluar-tab">

            {{-- SUB-TAB MUTASI KELUAR --}}
            <ul class="nav nav-pills mb-3" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" id="keluar-pending-tab" data-bs-toggle="tab" data-bs-target="#keluar-pending" type="button" role="tab">Pending</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="keluar-terima-tab" data-bs-toggle="tab" data-bs-target="#keluar-terima" type="button" role="tab">Diterima</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="keluar-tolak-tab" data-bs-toggle="tab" data-bs-target="#keluar-tolak" type="button" role="tab">Ditolak</button>
                </li>
            </ul>

            <div class="tab-content">
                {{-- Pending --}}
                <div class="tab-pane fade show active" id="keluar-pending" role="tabpanel">
                    @include('mutasibarang.partial-index', ['data' => $mutasiKeluarPending, 'tipe' => 'Warung Tujuan'])
                </div>
                {{-- Diterima --}}
                <div class="tab-pane fade" id="keluar-terima" role="tabpanel">
                    @include('mutasibarang.partial-index', ['data' => $mutasiKeluarDiterima, 'tipe' => 'Warung Tujuan'])
                </div>
                {{-- Ditolak --}}
                <div class="tab-pane fade" id="keluar-tolak" role="tabpanel">
                    @include('mutasibarang.partial-index', ['data' => $mutasiKeluarDitolak, 'tipe' => 'Warung Tujuan'])
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkAll = document.getElementById('checkAll');
        if (checkAll) {
            checkAll.addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('input[name="mutasiBarang[]"]');
                checkboxes.forEach(cb => cb.checked = this.checked);
            });
        }
    });

    function submitStatus(status, message) {
        const selectedItems = document.querySelectorAll('input[name="mutasiBarang[]"]:checked');
        if (selectedItems.length === 0) {
            alert('Pilih setidaknya satu mutasi barang.');
            return;
        }
        if (confirm(message)) {
            document.getElementById('statusBaru').value = status;
            document.getElementById('updateStatusForm').submit();
        }
    }
</script>
@endsection

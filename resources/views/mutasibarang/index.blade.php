@extends('layouts.app')

@section('title', 'Halaman Mutasi')

@section('content')
<div class="container-fluid mt-4">

    <div class="row">
        {{-- Card Notifikasi Mutasi Masuk --}}
        <div class="col-md-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center bg-success text-white">
                    <h5 class="mb-0">Notifikasi Mutasi Masuk</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Barang</th>
                                    <th scope="col">Asal</th>
                                    <th scope="col">Jumlah</th>
                                    <th scope="col">Keterangan</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($mutasiMasuk as $row)
                                <tr>
                                    <th scope="row">{{ $loop->iteration }}</th>
                                    <td>{{ $row->stokWarung->barang->nama_barang ?? '-' }}</td>
                                    <td>{{ $row->warungAsal->nama_warung ?? '-' }}</td>
                                    <td>{{ $row->jumlah }}</td>
                                    <td>{{ $row->keterangan }}</td>
                                    <td>
                                        @if($row->status === 'pending')
                                        <span class="badge bg-info text-white">Menunggu Konfirmasi</span>
                                        @elseif($row->status === 'terima')
                                        <span class="badge bg-success">Dikonfirmasi</span>
                                        @elseif($row->status === 'tolak')
                                        <span class="badge bg-danger">Ditolak</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($row->status === 'pending')
                                        {{-- <a href="{{ route('mutasibarang.konfirmasi', $row->id) }}" class="btn btn-sm btn-primary">Konfirmasi</a> --}}
                                        @else
                                        <button class="btn btn-sm btn-secondary disabled" disabled>Konfirmasi</button>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">Belum ada mutasi masuk.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card Mutasi Barang Keluar dengan Accordion --}}
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
                    <h5 class="mb-0">Mutasi Barang Keluar</h5>
                    <a href="{{ route('mutasibarang.create') }}" class="btn btn-light">
                        <i class="fas fa-plus-circle me-2"></i> Buat Mutasi Baru
                    </a>
                </div>
                <div class="card-body">
                    <div class="accordion" id="mutasiKeluarAccordion">
                        @forelse($mutasiKeluarGrouped as $warungTujuanId => $mutations)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading{{ $warungTujuanId }}">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $warungTujuanId }}" aria-expanded="false" aria-controls="collapse{{ $warungTujuanId }}">
                                    Mutasi ke Warung: {{ $mutations->first()->warungTujuan->nama_warung ?? 'N/A' }}
                                </button>
                            </h2>
                            <div id="collapse{{ $warungTujuanId }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $warungTujuanId }}" data-bs-parent="#mutasiKeluarAccordion">
                                <div class="accordion-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th scope="col">#</th>
                                                    <th scope="col">Barang</th>
                                                    <th scope="col">Jumlah</th>
                                                    <th scope="col">Keterangan</th>
                                                    <th scope="col">Status</th>
                                                    <th scope="col">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($mutations as $row)
                                                <tr>
                                                    <th scope="row">{{ $loop->iteration }}</th>
                                                    <td>{{ $row->stokWarung->barang->nama_barang ?? '-' }}</td>
                                                    <td>{{ $row->jumlah }}</td>
                                                    <td>{{ $row->keterangan }}</td>
                                                    <td>
                                                        @if($row->status === 'pending')
                                                        <span class="badge bg-warning text-dark">Menunggu Konfirmasi</span>
                                                        @elseif($row->status === 'terima')
                                                        <span class="badge bg-success">Terkirim</span>
                                                        @elseif($row->status === 'tolak')
                                                        <span class="badge bg-danger">Ditolak</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-info text-white" data-bs-toggle="modal" data-bs-target="#mutasiDetailModal" data-id="{{ $row->id }}" data-barang="{{ $row->stokWarung->barang->nama_barang ?? '-' }}" data-asal="{{ $row->warungAsal->nama_warung ?? '-' }}" data-tujuan="{{ $row->warungTujuan->nama_warung ?? '-' }}" data-jumlah="{{ $row->jumlah }}" data-keterangan="{{ $row->keterangan }}" data-status="{{ ucfirst($row->status) }}">
                                                            Lihat Detail
                                                        </button>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="alert alert-info text-center">Belum ada mutasi keluar.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="mutasiDetailModal" tabindex="-1" aria-labelledby="mutasiDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="mutasiDetailModalLabel">Detail Mutasi Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th scope="row">Barang</th>
                            <td id="modalBarang"></td>
                        </tr>
                        <tr>
                            <th scope="row">Warung Asal</th>
                            <td id="modalAsal"></td>
                        </tr>
                        <tr>
                            <th scope="row">Warung Tujuan</th>
                            <td id="modalTujuan"></td>
                        </tr>
                        <tr>
                            <th scope="row">Jumlah</th>
                            <td id="modalJumlah"></td>
                        </tr>
                        <tr>
                            <th scope="row">Status</th>
                            <td id="modalStatus"></td>
                        </tr>
                        <tr>
                            <th scope="row">Keterangan</th>
                            <td id="modalKeterangan"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    const mutasiDetailModal = document.getElementById('mutasiDetailModal');
    mutasiDetailModal.addEventListener('show.bs.modal', function(event) {
        // Button that triggered the modal
        const button = event.relatedTarget;
        // Extract info from data-* attributes
        const barang = button.getAttribute('data-barang');
        const asal = button.getAttribute('data-asal');
        const tujuan = button.getAttribute('data-tujuan');
        const jumlah = button.getAttribute('data-jumlah');
        const keterangan = button.getAttribute('data-keterangan');
        const status = button.getAttribute('data-status');

        // Update the modal's content.
        const modalBarang = mutasiDetailModal.querySelector('#modalBarang');
        const modalAsal = mutasiDetailModal.querySelector('#modalAsal');
        const modalTujuan = mutasiDetailModal.querySelector('#modalTujuan');
        const modalJumlah = mutasiDetailModal.querySelector('#modalJumlah');
        const modalKeterangan = mutasiDetailModal.querySelector('#modalKeterangan');
        const modalStatus = mutasiDetailModal.querySelector('#modalStatus');

        modalBarang.textContent = barang;
        modalAsal.textContent = asal;
        modalTujuan.textContent = tujuan;
        modalJumlah.textContent = jumlah;
        modalKeterangan.textContent = keterangan;
        modalStatus.textContent = status;
    });
</script>
@endsection

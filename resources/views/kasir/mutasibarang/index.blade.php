@extends('layouts.app')

@section('title', 'Manajemen Mutasi')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-primary fw-bold"><i class="fas fa-exchange-alt me-2"></i>Log Mutasi Barang</h5>
                        <a href="{{ route('mutasibarang.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i> Buat Mutasi Baru
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <ul class="nav nav-tabs nav-fill mb-4" id="mutasiTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-bold" id="masuk-tab" data-bs-toggle="tab" data-bs-target="#masuk" type="button" role="tab">
                                <i class="fas fa-download me-2 text-success"></i>Penerimaan (Mutasi Masuk)
                                @if($mutasiMasuk->where('status', 'pending')->count() > 0)
                                    <span class="badge bg-danger ms-2">{{ $mutasiMasuk->where('status', 'pending')->count() }}</span>
                                @endif
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold" id="keluar-tab" data-bs-toggle="tab" data-bs-target="#keluar" type="button" role="tab">
                                <i class="fas fa-upload me-2 text-primary"></i>Pengiriman (Mutasi Keluar)
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="mutasiTabContent">
                        
                        <div class="tab-pane fade show active" id="masuk" role="tabpanel">
                            <form action="{{ route('kasir.mutasibarang.konfirmasi-masal') }}" method="POST">
                                @csrf
                                @method('PATCH')
                                
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="40px" class="text-center">
                                                    <input type="checkbox" class="form-check-input" id="checkAll">
                                                </th>
                                                <th>Barang</th>
                                                <th>Asal Warung</th>
                                                <th class="text-center">Jumlah</th>
                                                <th>Status</th>
                                                <th>Keterangan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($mutasiMasuk as $row)
                                            <tr>
                                                <td class="text-center">
                                                    @if($row->status === 'pending')
                                                        <input type="checkbox" name="ids[]" value="{{ $row->id }}" class="form-check-input cb-child">
                                                    @else
                                                        <i class="fas fa-check-double text-muted small"></i>
                                                    @endif
                                                </td>
                                                <td><strong>{{ $row->stokWarung->barang->nama_barang ?? '-' }}</strong></td>
                                                <td><span class="badge bg-light text-dark border">{{ $row->warungAsal->nama_warung ?? '-' }}</span></td>
                                                <td class="text-center fw-bold text-success">+{{ $row->jumlah }}</td>
                                                <td>
                                                    @if($row->status === 'pending')
                                                        <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Pending</span>
                                                    @elseif($row->status === 'terima')
                                                        <span class="badge bg-success"><i class="fas fa-check me-1"></i>Diterima</span>
                                                    @else
                                                        <span class="badge bg-danger"><i class="fas fa-times me-1"></i>Ditolak</span>
                                                    @endif
                                                </td>
                                                <td><small class="text-muted">{{ Str::limit($row->keterangan, 30) ?: '-' }}</small></td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-5 text-muted">Tidak ada mutasi masuk.</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                @if($mutasiMasuk->where('status', 'pending')->count() > 0)
                                <div class="alert alert-light border d-flex align-items-center mt-3">
                                    <div class="fw-bold me-auto text-secondary"><i class="fas fa-arrow-right me-2"></i>Aksi Masal untuk Item Terpilih:</div>
                                    <button type="submit" name="action" value="terima" class="btn btn-success me-2 px-4 shadow-sm" onclick="return confirm('Terima semua yang dipilih?')">
                                        <i class="fas fa-check-circle me-1"></i> Terima Semua
                                    </button>
                                    <button type="submit" name="action" value="tolak" class="btn btn-outline-danger px-4 shadow-sm" onclick="return confirm('Tolak semua yang dipilih?')">
                                        <i class="fas fa-times-circle me-1"></i> Tolak
                                    </button>
                                </div>
                                @endif
                            </form>
                        </div>

                        <div class="tab-pane fade" id="keluar" role="tabpanel">
                            <div class="accordion accordion-flush border rounded" id="accMutasiKeluar">
                                @forelse($mutasiKeluarGrouped as $warungTujuanId => $mutations)
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed py-3" type="button" data-bs-toggle="collapse" data-bs-target="#coll-{{ $warungTujuanId }}">
                                            <div class="d-flex justify-content-between w-100 align-items-center pe-3">
                                                <div>
                                                    <i class="fas fa-store me-2 text-primary"></i>
                                                    <span class="fw-bold">Tujuan: {{ $mutations->first()->warungTujuan->nama_warung ?? 'N/A' }}</span>
                                                </div>
                                                <span class="badge rounded-pill bg-primary px-3">{{ $mutations->count() }} Item</span>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="coll-{{ $warungTujuanId }}" class="accordion-collapse collapse" data-bs-parent="#accMutasiKeluar">
                                        <div class="accordion-body bg-light-subtle">
                                            <table class="table table-sm table-borderless align-middle">
                                                <thead class="text-muted small text-uppercase">
                                                    <tr>
                                                        <th>Nama Barang</th>
                                                        <th class="text-center">Jumlah</th>
                                                        <th>Status</th>
                                                        <th class="text-end">Detail</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($mutations as $m)
                                                    <tr class="border-bottom">
                                                        <td class="py-2">{{ $m->stokWarung->barang->nama_barang ?? '-' }}</td>
                                                        <td class="text-center fw-bold text-danger">-{{ $m->jumlah }}</td>
                                                        <td>
                                                            @if($m->status === 'pending')
                                                                <span class="text-warning"><i class="fas fa-spinner fa-spin me-1 small"></i>Proses</span>
                                                            @elseif($m->status === 'terima')
                                                                <span class="text-success"><i class="fas fa-check-circle me-1 small"></i>Terkirim</span>
                                                            @else
                                                                <span class="text-danger"><i class="fas fa-times-circle me-1 small"></i>Ditolak</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-end">
                                                            <button type="button" class="btn btn-link btn-sm text-info p-0" data-bs-toggle="modal" data-bs-target="#mutasiDetailModal" data-barang="{{ $m->stokWarung->barang->nama_barang ?? '-' }}" data-asal="{{ $m->warungAsal->nama_warung ?? '-' }}" data-tujuan="{{ $m->warungTujuan->nama_warung ?? '-' }}" data-jumlah="{{ $m->jumlah }}" data-keterangan="{{ $m->keterangan }}" data-status="{{ ucfirst($m->status) }}">
                                                                <i class="fas fa-info-circle"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div class="text-center py-5">
                                    <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="80" class="opacity-25 mb-3">
                                    <p class="text-muted">Belum ada riwayat pengiriman.</p>
                                </div>
                                @endforelse
                            </div>
                        </div>

                    </div> </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="mutasiDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <div class="icon-circle bg-info text-white mb-2 mx-auto d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; border-radius: 50%;">
                        <i class="fas fa-box-open fa-lg"></i>
                    </div>
                    <h5 class="fw-bold">Rincian Mutasi Barang</h5>
                </div>
                <div class="list-group list-group-flush border-top border-bottom">
                    <div class="list-group-item d-flex justify-content-between"><span>Barang</span> <strong id="modalBarang">-</strong></div>
                    <div class="list-group-item d-flex justify-content-between"><span>Dari</span> <span id="modalAsal">-</span></div>
                    <div class="list-group-item d-flex justify-content-between"><span>Ke</span> <span id="modalTujuan">-</span></div>
                    <div class="list-group-item d-flex justify-content-between"><span>Jumlah</span> <strong id="modalJumlah" class="text-primary">-</strong></div>
                    <div class="list-group-item d-flex justify-content-between"><span>Status</span> <span id="modalStatus" class="badge bg-secondary">-</span></div>
                </div>
                <div class="mt-3">
                    <label class="small text-muted fw-bold">Keterangan:</label>
                    <p id="modalKeterangan" class="small text-dark p-2 bg-light rounded">-</p>
                </div>
                <button type="button" class="btn btn-secondary w-100 mt-3" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Logic Checkbox "Select All"
    const checkAll = document.getElementById('checkAll');
    if(checkAll) {
        checkAll.addEventListener('click', function() {
            const childs = document.querySelectorAll('.cb-child');
            childs.forEach(cb => cb.checked = this.checked);
        });
    }

    // Modal Populate
    const mutasiDetailModal = document.getElementById('mutasiDetailModal');
    mutasiDetailModal.addEventListener('show.bs.modal', function(event) {
        const btn = event.relatedTarget;
        mutasiDetailModal.querySelector('#modalBarang').textContent = btn.getAttribute('data-barang');
        mutasiDetailModal.querySelector('#modalAsal').textContent = btn.getAttribute('data-asal');
        mutasiDetailModal.querySelector('#modalTujuan').textContent = btn.getAttribute('data-tujuan');
        mutasiDetailModal.querySelector('#modalJumlah').textContent = btn.getAttribute('data-jumlah');
        mutasiDetailModal.querySelector('#modalKeterangan').textContent = btn.getAttribute('data-keterangan');
        
        const status = btn.getAttribute('data-status');
        const modalStatus = mutasiDetailModal.querySelector('#modalStatus');
        modalStatus.textContent = status;
        
        // Warna badge status di modal
        modalStatus.className = 'badge';
        if(status === 'Terima') modalStatus.classList.add('bg-success');
        else if(status === 'Tolak') modalStatus.classList.add('bg-danger');
        else modalStatus.classList.add('bg-warning', 'text-dark');
    });
</script>

<style>
    .nav-tabs .nav-link { color: #6c757d; border: none; border-bottom: 2px solid transparent; }
    .nav-tabs .nav-link.active { color: #0d6efd; border: none; border-bottom: 3px solid #0d6efd; background: transparent; }
    .accordion-button:not(.collapsed) { background-color: #f8f9fa; color: #0d6efd; }
    .table-hover tbody tr:hover { background-color: #fdfdfd; }
</style>
@endsection
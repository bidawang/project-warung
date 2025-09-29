@extends('layouts.app')

@section('title', 'Buat Rencana Belanja Baru')

@section('content')

<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">Form Rencana Pembelian Barang</h5>
        </div>
        <div class="card-body">

```
        {{-- Pesan Feedback --}}
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <p class="mb-0">Mohon perbaiki kesalahan input berikut:</p>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="rencanaBelanjaForm" action="{{ route('kasir.rencanabelanja.store') }}" method="POST">
            @csrf

            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th style="width: 5%;">#</th>
                            <th>Nama Barang</th>
                            <th style="width: 15%;">Stok Saat Ini</th>
                            <th style="width: 25%;">Jumlah Rencana Beli</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($stokBarang as $stok)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <strong>{{ $stok->barang->nama_barang ?? 'Barang Tidak Dikenal' }}</strong>
                                    <input type="hidden"
                                           name="rencana[{{ $stok->id }}][id_barang]"
                                           value="{{ $stok->id_barang }}">
                                </td>
                                <td>
                                    <span class="badge bg-{{ $stok->jumlah < 5 ? 'danger' : ($stok->jumlah < 20 ? 'warning text-dark' : 'success') }}">
                                        {{ number_format($stok->jumlah, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td>
                                    <input type="number"
                                           name="rencana[{{ $stok->id }}][jumlah_awal]"
                                           class="form-control form-control-sm rencana-input"
                                           value="{{ old('rencana.' . $stok->id . '.jumlah_awal', 0) }}"
                                           min="0"
                                           placeholder="0"
                                           data-nama="{{ $stok->barang->nama_barang ?? 'Barang Tidak Dikenal' }}">
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">Tidak ada stok barang di warung ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-4">
                <a href="{{ route('kasir.rencanabelanja.index') }}" class="btn btn-secondary me-2">Batal</a>
                <button type="button" class="btn btn-success" id="btnKonfirmasi">
                    <i class="fas fa-check-circle"></i> Konfirmasi Rencana Belanja (Lock)
                </button>
            </div>
        </form>
    </div>
</div>
```

</div>

{{-- Modal Konfirmasi --}}

<div class="modal fade" id="konfirmasiModal" tabindex="-1" aria-labelledby="konfirmasiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="konfirmasiModalLabel">Konfirmasi Daftar Belanja</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <p>Pastikan daftar berikut sudah benar. Hanya barang dengan Jumlah Rencana Beli > 0 yang akan disimpan.</p>
                <div class="table-responsive">
                    <table class="table table-bordered" id="konfirmasiList">
                        <thead>
                            <tr class="table-primary">
                                <th>Barang</th>
                                <th style="width: 30%;">Jumlah Rencana Beli</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div id="modalError" class="alert alert-danger d-none"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal / Koreksi</button>
                <button type="button" class="btn btn-success" id="btnSubmitFinal">
                    <i class="fas fa-save"></i> Simpan Rencana Belanja
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('rencanaBelanjaForm');
    const btnKonfirmasi = document.getElementById('btnKonfirmasi');
    const btnSubmitFinal = document.getElementById('btnSubmitFinal');
    const konfirmasiListBody = document.querySelector('#konfirmasiList tbody');
    const modalError = document.getElementById('modalError');
    const konfirmasiModal = new bootstrap.Modal(document.getElementById('konfirmasiModal'));

    btnKonfirmasi.addEventListener('click', () => {
        konfirmasiListBody.innerHTML = '';
        modalError.classList.add('d-none');

        const selectedItems = Array.from(document.querySelectorAll('.rencana-input'))
            .map(input => ({
                nama: input.dataset.nama,
                jumlah: parseInt(input.value) || 0
            }))
            .filter(item => item.jumlah > 0);

        if (selectedItems.length === 0) {
            modalError.textContent = 'Anda belum memilih barang. Masukkan jumlah minimal 1 untuk melanjutkan.';
            modalError.classList.remove('d-none');
            konfirmasiModal.show();
            return;
        }

        selectedItems.forEach(item => {
            const row = konfirmasiListBody.insertRow();
            row.innerHTML = `
                <td>${item.nama}</td>
                <td class="text-end"><strong>${item.jumlah.toLocaleString('id-ID')}</strong></td>
            `;
        });

        konfirmasiModal.show();
    });

    btnSubmitFinal.addEventListener('click', () => {
        konfirmasiModal.hide();
        form.submit();
    });
});
</script>

@endsection

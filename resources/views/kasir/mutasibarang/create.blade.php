@extends('layouts.app')

@section('title', 'Tambah Mutasi Barang')

@section('content')
<div class="container">
    <h2 class="mb-4">Tambah Mutasi Barang</h2>
@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

    <form id="mutasiForm" action="{{ route('mutasibarang.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Warung Tujuan</label>
            <select name="warung_tujuan" class="form-select" required>
                <option value="">-- Pilih --</option>
                @foreach($warungTujuan as $w)
                <option value="{{ $w->id }}">{{ $w->nama_warung }}</option>
                @endforeach
            </select>
        </div>

        <h5 class="mt-4">Pilih Barang untuk Mutasi</h5>
        <table class="table table-bordered mt-2">
            <thead>
                <tr>
                    <th>Pilih</th>
                    <th>Nama Barang</th>
                    <th>Stok</th>
                    <th>Jumlah Mutasi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($barangTersedia as $barang)
                <tr>
                    <td>
                        <input type="checkbox" name="barang[{{ $barang->id_stok_warung }}][pilih]" value="1" class="barang-checkbox">
                    </td>
                    <td>{{ $barang->nama_barang }}</td>
                    <td>{{ $barang->stok_saat_ini }}</td>
                    <td>
                        <input type="number" name="barang[{{ $barang->id_stok_warung }}][jumlah]" class="form-control jumlah-input" min="1" max="{{ $barang->stok_saat_ini }}" disabled>
                        <input type="hidden" name="barang[{{ $barang->id_stok_warung }}][id_stok_warung]" value="{{ $barang->id_stok_warung }}">
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mb-3">
            <label>Keterangan</label>
            <textarea name="keterangan" class="form-control"></textarea>
        </div>

        <button type="button" id="btnMutasi" class="btn btn-primary">Mutasi</button>
        <a href="{{ route('mutasibarang.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>

{{-- Modal Konfirmasi --}}
<div class="modal fade" id="konfirmasiModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Mutasi Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin melakukan mutasi barang berikut?</p>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nama Barang</th>
                            <th>Jumlah</th>
                        </tr>
                    </thead>
                    <tbody id="konfirmasiList">
                        <!-- List barang akan diisi dengan JS -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" id="konfirmasiSubmit" class="btn btn-primary">Ya, Mutasikan</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Enable jumlah input kalau checkbox dicentang
        document.querySelectorAll(".barang-checkbox").forEach(cb => {
            cb.addEventListener("change", function() {
                let jumlahInput = this.closest("tr").querySelector(".jumlah-input");
                jumlahInput.disabled = !this.checked;
                if (!this.checked) jumlahInput.value = "";
            });
        });

        // Klik tombol Mutasi → tampilkan modal konfirmasi
        document.getElementById("btnMutasi").addEventListener("click", function() {
            let list = document.getElementById("konfirmasiList");
            list.innerHTML = "";

            document.querySelectorAll(".barang-checkbox:checked").forEach(cb => {
                let row = cb.closest("tr");
                let namaBarang = row.children[1].innerText;
                let jumlah = row.querySelector(".jumlah-input").value;

                if (jumlah > 0) {
                    list.innerHTML += `<tr><td>${namaBarang}</td><td>${jumlah}</td></tr>`;
                }
            });

            if (list.innerHTML === "") {
                alert("Pilih minimal satu barang dan isi jumlahnya!");
                return;
            }

            new bootstrap.Modal(document.getElementById("konfirmasiModal")).show();
        });

        // Submit form setelah konfirmasi
        document.getElementById("konfirmasiSubmit").addEventListener("click", function() {
            document.getElementById("mutasiForm").submit();
        });

        document.querySelectorAll(".jumlah-input").forEach(input => {
            input.addEventListener("input", function() {
                let max = parseInt(this.getAttribute("max"));
                if (this.value > max) {
                    alert(`Jumlah mutasi tidak boleh lebih dari stok (${max})`);
                    this.value = max; // otomatis dikembalikan ke stok maksimal
                }
            });
        });
    });
</script>
@endsection

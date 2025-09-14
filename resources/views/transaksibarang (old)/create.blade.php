@extends('layouts.app')

@section('title', 'Tambah Transaksi Barang')

@section('content')
<div class="container">
    <h2 class="mb-4">Tambah Transaksi Barang</h2>

    <form action="{{ route('transaksibarang.store') }}" method="POST" id="formTransaksiBarang">
        @csrf

        <div class="mb-3">
            <label>Transaksi Kas</label>
            <select name="id_transaksi_kas" class="form-control" required>
                @foreach($transaksis as $trx)
                    <option value="{{ $trx->id }}">#{{ $trx->id }} - {{ ucfirst($trx->jenis) }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Status</label>
            <input type="text" name="status" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Jenis</label>
            <select name="jenis" id="jenisTransaksi" class="form-control" required>
                <option value="masuk">Masuk</option>
                <option value="keluar">Keluar</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Keterangan</label>
            <textarea name="keterangan" class="form-control"></textarea>
        </div>

        <hr>

        <h5>Daftar Barang</h5>
        <table class="table table-bordered" id="barangTable">
            <thead>
                <tr>
                    <th>Barang</th>
                    <th>Jumlah <small class="text-muted">(max sesuai stok untuk keluar)</small></th>
                    <th>Total Harga (Rp)</th>
                    <th style="width: 40px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <select name="id_barang[]" class="form-control select-barang" required>
                            <option value="" disabled selected>Pilih Barang</option>
                            @foreach($stokWarung as $stok)
                                <option value="{{ $stok->id_barang }}" data-stok="{{ $stok->stok }}" data-harga="{{ $stok->barang->harga ?? 0 }}">
                                    {{ $stok->barang->nama_barang }} (Stok: {{ $stok->stok }})
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="number" name="jumlah[]" class="form-control input-jumlah" min="1" required>
                    </td>
                    <td>
                        <input type="text" name="total_harga[]" class="form-control total-harga" readonly>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm btn-remove-row" title="Hapus Baris">&times;</button>
                    </td>
                </tr>
            </tbody>
        </table>
        <button type="button" class="btn btn-secondary mb-3" id="btnAddRow">Tambah Barang</button>

        <hr>

        <h5>Pecahan Uang</h5>
        <div class="row g-3" id="pecahan-container">
            @php
                $pecahanList = [100, 200, 500, 1000, 2000, 5000, 10000, 20000, 50000, 100000];
            @endphp
            @foreach($pecahanList as $pecahan)
                <div class="col-6 col-sm-4 col-md-3">
                    <label for="pecahan-{{ $pecahan }}" class="form-label">Rp {{ number_format($pecahan, 0, ',', '.') }}</label>
                    <input type="number" name="pecahan[{{ $pecahan }}]" id="pecahan-{{ $pecahan }}" class="form-control jumlah-pecahan" value="0" min="0">
                </div>
            @endforeach
        </div>

        <div class="mb-3 mt-3">
            <label for="total_harga_pecahan" class="form-label fw-bold">Total Pecahan</label>
            <input type="text" id="total_harga_pecahan" class="form-control" readonly value="Rp 0">
        </div>

        <button type="submit" class="btn btn-success">Simpan</button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnAddRow = document.getElementById('btnAddRow');
    const barangTableBody = document.querySelector('#barangTable tbody');
    const stokBarang = @json($stokBarang); // {id_barang: stok}
    const pecahanInputs = document.querySelectorAll('.jumlah-pecahan');
    const totalHargaPecahanInput = document.getElementById('total_harga_pecahan');
    const form = document.getElementById('formTransaksiBarang');
    const jenisTransaksiSelect = document.getElementById('jenisTransaksi');

    function updateSelectOptions() {
        const selectedValues = Array.from(document.querySelectorAll('.select-barang'))
            .map(sel => sel.value)
            .filter(val => val !== '');

        document.querySelectorAll('.select-barang').forEach(select => {
            const currentValue = select.value;
            Array.from(select.options).forEach(option => {
                if (option.value === '') return;
                option.hidden = (option.value !== currentValue && selectedValues.includes(option.value));
            });
        });
    }

    function updateTotalHargaRow(tr) {
        const selectBarang = tr.querySelector('.select-barang');
        const inputJumlah = tr.querySelector('.input-jumlah');
        const inputTotalHarga = tr.querySelector('.total-harga');

        const barangId = selectBarang.value;
        if (!barangId) {
            inputTotalHarga.value = '';
            return;
        }

        const hargaSatuan = parseFloat(selectBarang.options[selectBarang.selectedIndex].dataset.harga) || 0;
        const jumlah = parseInt(inputJumlah.value) || 0;

        if (jenisTransaksiSelect.value === 'keluar') {
            const total = hargaSatuan * jumlah;
            inputTotalHarga.value = total > 0 ? total.toLocaleString('id-ID') : '';
            inputTotalHarga.readOnly = true;
        } else {
            // Masuk: total harga input manual, kosongkan jika kosong
            if (!inputTotalHarga.value) inputTotalHarga.value = '';
            inputTotalHarga.readOnly = false;
        }
    }

    function hitungTotalHargaBarang() {
        let total = 0;
        document.querySelectorAll('.total-harga').forEach(input => {
            let val = input.value.replace(/\./g, '').replace(/,/g, '');
            val = val ? parseInt(val) : 0;
            total += val;
        });
        return total;
    }

    function hitungTotalPecahan() {
        let total = 0;
        pecahanInputs.forEach(input => {
            const pecahan = parseInt(input.id.split('-')[1]);
            const jumlah = parseInt(input.value) || 0;
            total += pecahan * jumlah;
        });
        return total;
    }

    function updateTotalPecahanDisplay() {
        const total = hitungTotalPecahan();
        totalHargaPecahanInput.value = 'Rp ' + total.toLocaleString('id-ID');
    }

    function validateJumlah(input) {
        if (jenisTransaksiSelect.value !== 'keluar') return; // hanya validasi untuk keluar

        const tr = input.closest('tr');
        const selectBarang = tr.querySelector('.select-barang');
        const barangId = selectBarang.value;
        if (!barangId) return;

        const maxStok = stokBarang[barangId] ?? 0;
        let val = parseInt(input.value) || 0;

        if (val > maxStok) {
            alert(`Jumlah tidak boleh lebih dari stok (${maxStok})`);
            input.value = maxStok;
            val = maxStok;
        } else if (val < 1) {
            input.value = 1;
            val = 1;
        }

        updateTotalHargaRow(tr);
        updateTotalPecahanDisplay();
    }

    // Tambah baris baru
    btnAddRow.addEventListener('click', function() {
        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td>
                <select name="id_barang[]" class="form-control select-barang" required>
                    <option value="" disabled selected>Pilih Barang</option>
                    @foreach($stokWarung as $stok)
                        <option value="{{ $stok->id_barang }}" data-stok="{{ $stok->stok }}" data-harga="{{ $stok->barang->harga ?? 0 }}">
                            {{ $stok->barang->nama_barang }} (Stok: {{ $stok->stok }})
                        </option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="number" name="jumlah[]" class="form-control input-jumlah" min="1" required>
            </td>
            <td>
                <input type="text" name="total_harga[]" class="form-control total-harga" readonly>
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm btn-remove-row" title="Hapus Baris">&times;</button>
            </td>
        `;
        barangTableBody.appendChild(newRow);
        updateSelectOptions();
        toggleJumlahColumn();
    });

    // Hapus baris
    barangTableBody.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-remove-row')) {
            const rows = barangTableBody.querySelectorAll('tr');
            if (rows.length > 1) {
                e.target.closest('tr').remove();
                updateSelectOptions();
                updateTotalPecahanDisplay();
            } else {
                alert('Minimal harus ada satu barang.');
            }
        }
    });

    // Saat barang dipilih
    barangTableBody.addEventListener('change', function(e) {
        if (e.target.classList.contains('select-barang')) {
            updateSelectOptions();

            const tr = e.target.closest('tr');
            const inputJumlah = tr.querySelector('.input-jumlah');
            const inputTotalHarga = tr.querySelector('.total-harga');

            if (jenisTransaksiSelect.value === 'keluar') {
                inputJumlah.value = '';
                inputJumlah.required = true;
                inputJumlah.style.display = '';
                inputTotalHarga.readOnly = true;
                inputTotalHarga.value = '';
            } else {
                inputJumlah.value = '';
                inputJumlah.required = true; // tetap required karena input manual
                inputJumlah.style.display = '';
                inputTotalHarga.readOnly = false;
                inputTotalHarga.value = '';
            }
        }
    });

    // Validasi jumlah input
    barangTableBody.addEventListener('input', function(e) {
        if (e.target.classList.contains('input-jumlah')) {
            validateJumlah(e.target);
        }
    });

    // Update total harga saat total harga diinput manual (jenis masuk)
    barangTableBody.addEventListener('input', function(e) {
        if (e.target.classList.contains('total-harga') && !e.target.readOnly) {
            updateTotalPecahanDisplay();
        }
    });

    // Update total pecahan saat input pecahan berubah
    pecahanInputs.forEach(input => {
        input.addEventListener('input', updateTotalPecahanDisplay);
    });

    // Validasi form sebelum submit
    form.addEventListener('submit', function(e) {
        let valid = true;

        // Validasi jumlah barang hanya untuk keluar
        if (jenisTransaksiSelect.value === 'keluar') {
            document.querySelectorAll('.input-jumlah').forEach(input => {
                const tr = input.closest('tr');
                const selectBarang = tr.querySelector('.select-barang');
                const barangId = selectBarang.value;
                if (!barangId) {
                    alert('Pilih barang terlebih dahulu.');
                    valid = false;
                    return;
                }
                const maxStok = stokBarang[barangId] ?? 0;
                const val = parseInt(input.value) || 0;
                if (val < 1 || val > maxStok) {
                    alert(`Jumlah untuk barang "${selectBarang.options[selectBarang.selectedIndex].text}" harus antara 1 sampai ${maxStok}.`);
                    valid = false;
                }
            });
        } else {
            // Untuk masuk, pastikan jumlah > 0
            document.querySelectorAll('.input-jumlah').forEach(input => {
                const val = parseInt(input.value) || 0;
                if (val < 1) {
                    alert('Jumlah harus diisi dan minimal 1.');
                    valid = false;
                }
            });
        }

        if (!valid) {
            e.preventDefault();
            return;
        }

        // Validasi total pecahan sama dengan total harga barang
        const totalHargaBarang = hitungTotalHargaBarang();
        const totalPecahan = hitungTotalPecahan();

        if (totalHargaBarang !== totalPecahan) {
            alert(`Total pecahan uang (Rp ${totalPecahan.toLocaleString('id-ID')}) harus sama dengan total harga barang (Rp ${totalHargaBarang.toLocaleString('id-ID')}).`);
            e.preventDefault();
            return;
        }
    });

    // Saat jenis transaksi berubah
    jenisTransaksiSelect.addEventListener('change', function() {
        toggleJumlahColumn();

        // Reset semua input jumlah dan total harga
        document.querySelectorAll('tr').forEach(tr => {
            const inputJumlah = tr.querySelector('.input-jumlah');
            const inputTotalHarga = tr.querySelector('.total-harga');
            if (inputJumlah) inputJumlah.value = '';
            if (inputTotalHarga) inputTotalHarga.value = '';
        });

        updateSelectOptions();
        updateTotalPecahanDisplay();
    });

    // Toggle kolom jumlah dan readonly total harga sesuai jenis transaksi
    function toggleJumlahColumn() {
        const isKeluar = jenisTransaksiSelect.value === 'keluar';

        // Kolom jumlah selalu tampil, tapi validasi dan batasan stok hanya untuk keluar
        document.querySelectorAll('.input-jumlah').forEach(input => {
            input.required = true;
            input.style.display = '';
        });

        // Total harga readonly hanya untuk keluar
        document.querySelectorAll('.total-harga').forEach(input => {
            input.readOnly = isKeluar;
        });
    }

    // Inisialisasi
    updateSelectOptions();
    toggleJumlahColumn();
    updateTotalPecahanDisplay();
});
</script>
@endsection
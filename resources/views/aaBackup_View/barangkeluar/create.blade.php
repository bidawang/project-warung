@extends('layouts.app')

@section('title', 'Tambah Barang Keluar')

@section('content')
    <div class="container-fluid">
        <div class="p-4 bg-light min-vh-100">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <form action="{{ route('barangkeluar.store') }}" method="POST" id="formBarangKeluar">
                        @csrf
                        <div class="mb-4">
                            <h2 class="font-weight-semibold mb-3 text-gray-800">Daftar Barang Keluar</h2>
                            <div id="barangContainer" class="space-y-4">
                                <div class="barang-block border rounded p-4 bg-light">
                                    <div class="d-flex justify-content-end align-items-center mb-3">
                                        <button type="button" class="btn btn-danger btn-sm btn-remove-barang"
                                            style="font-size: 1.5rem; line-height: 1;">&times;</button>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <label class="form-label font-weight-semibold">Stok Warung</label>
                                            <select name="id_stok_warung[]" class="form-control select-stok" required>
                                                <option value="">-- Pilih Stok Warung --</option>
                                                @foreach ($stok_warungs as $stok)
                                                    <option value="{{ $stok->id }}" data-harga="{{ $stok->harga_jual }}"
                                                        data-stok="{{ $stok->stok_saat_ini }}"
                                                        data-kuantitas='@json($stok->kuantitas_list)'
                                                        @if ($stok->stok_saat_ini < 1) disabled @endif>
                                                        {{ $stok->barang->nama_barang }}
                                                        (Rp {{ number_format($stok->harga_jual, 0, ',', '.') }})
                                                        - Stok: {{ $stok->stok_saat_ini }}
                                                        @if ($stok->stok_saat_ini < 1)
                                                            (Habis)
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-2">
                                            <label class="form-label font-weight-semibold">Jumlah</label>
                                            <input type="number" name="jumlah[]" class="form-control input-jumlah"
                                                min="1" required />
                                        </div>

                                        <div class="col-md-2">
                                            <label class="form-label font-weight-semibold">Jenis</label>
                                            <select name="jenis[]" class="form-control select-jenis" required>
                                                <option value="penjualan" selected>Penjualan</option>
                                                <option value="hutang">Hutang</option>
                                                <option value="expayet">Expayet</option>
                                                <option value="hilang">Hilang</option>
                                            </select>
                                        </div>

                                        <!-- 🔹 Input User Hutang (default hidden) -->
                                        <div class="col-md-3 d-none user-hutang-block">
                                            <label class="form-label font-weight-semibold">Pilih User</label>
                                            <select name="user_id[]" class="form-control select-user">
                                                <option value="">-- Pilih User --</option>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-2">
                                            <label class="form-label font-weight-semibold">Harga Satuan</label>
                                            <input type="text" class="form-control input-harga" value="0" readonly />
                                        </div>

                                        <div class="col-md-2">
                                            <label class="form-label font-weight-semibold">Total</label>
                                            <input type="text" class="form-control input-total" value="0" readonly />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button type="button" id="btnAddBarang" class="btn btn-success mt-3">+ Tambah Barang</button>
                        </div>

                        <div class="mb-4">
                            <h4 class="font-weight-semibold">Grand Total:
                                <span id="grandTotal" class="text-primary">Rp 0</span>
                            </h4>
                        </div>

                        <div class="mb-4">
                            <label for="metode_pembayaran" class="form-label font-weight-semibold">Metode Pembayaran</label>
                            <select name="metode_pembayaran" id="metode_pembayaran" class="form-control" required>
                                <option value="">-- Pilih Metode Pembayaran --</option>
                                <option value="cash">Cash</option>
                                <option value="transfer">Transfer</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="keterangan" class="form-label font-weight-semibold">Keterangan (Opsional)</label>
                            <textarea name="keterangan" id="keterangan" rows="3" class="form-control">{{ old('keterangan') }}</textarea>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('barangkeluar.index') }}" class="btn btn-secondary me-2">Batal</a>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const barangContainer = document.getElementById('barangContainer');
            const btnAddBarang = document.getElementById('btnAddBarang');
            const grandTotalEl = document.getElementById('grandTotal');

            function formatRupiah(angka) {
                return angka.toLocaleString('id-ID', { style: 'currency', currency: 'IDR' });
            }

            function hitungTotal(jumlah, hargaSatuan, kuantitasList) {
                if (!kuantitasList || kuantitasList.length === 0) return jumlah * hargaSatuan;
                let total = 0, sisa = jumlah;
                kuantitasList.sort((a, b) => b.jumlah - a.jumlah);
                for (let k of kuantitasList) {
                    if (sisa >= k.jumlah) {
                        const pack = Math.floor(sisa / k.jumlah);
                        total += pack * k.harga_jual;
                        sisa -= pack * k.jumlah;
                    }
                }
                total += sisa * hargaSatuan;
                return total;
            }

            function updateHarga(block) {
                const select = block.querySelector('.select-stok');
                const jumlahInput = block.querySelector('.input-jumlah');
                const hargaInput = block.querySelector('.input-harga');
                const totalInput = block.querySelector('.input-total');

                const harga = parseFloat(select.selectedOptions[0]?.dataset.harga || 0);
                const stok = parseInt(select.selectedOptions[0]?.dataset.stok || 0);
                const kuantitasList = JSON.parse(select.selectedOptions[0]?.dataset.kuantitas || '[]');
                const jumlah = parseInt(jumlahInput.value) || 0;

                jumlahInput.max = stok > 0 ? stok : 1;
                hargaInput.value = formatRupiah(harga);
                totalInput.value = formatRupiah(hitungTotal(jumlah, harga, kuantitasList));
                updateGrandTotal();
            }

            function updateGrandTotal() {
                let total = 0;
                barangContainer.querySelectorAll('.barang-block').forEach(block => {
                    const select = block.querySelector('.select-stok');
                    const jumlahInput = block.querySelector('.input-jumlah');
                    const harga = parseFloat(select.selectedOptions[0]?.dataset.harga || 0);
                    const kuantitasList = JSON.parse(select.selectedOptions[0]?.dataset.kuantitas || '[]');
                    const jumlah = parseInt(jumlahInput.value) || 0;
                    total += hitungTotal(jumlah, harga, kuantitasList);
                });
                grandTotalEl.textContent = formatRupiah(total);
            }

            // 🔹 Tampilkan User jika jenis = hutang
            barangContainer.addEventListener('change', e => {
                if (e.target.classList.contains('select-stok') || e.target.classList.contains('input-jumlah')) {
                    updateHarga(e.target.closest('.barang-block'));
                }

                if (e.target.classList.contains('select-jenis')) {
                    const block = e.target.closest('.barang-block');
                    const userBlock = block.querySelector('.user-hutang-block');
                    const userSelect = userBlock.querySelector('.select-user');

                    if (e.target.value === 'hutang') {
                        userBlock.classList.remove('d-none');
                        userSelect.setAttribute('required', 'required');
                    } else {
                        userBlock.classList.add('d-none');
                        userSelect.removeAttribute('required');
                        userSelect.value = "";
                    }
                }
            });

            btnAddBarang.addEventListener('click', () => {
                const newBarang = barangContainer.querySelector('.barang-block').cloneNode(true);
                newBarang.querySelector('select[name="id_stok_warung[]"]').value = "";
                newBarang.querySelector('input[name="jumlah[]"]').value = "";
                newBarang.querySelector('.input-harga').value = "0";
                newBarang.querySelector('.input-total').value = "0";
                newBarang.querySelector('.select-jenis').value = "penjualan";
                newBarang.querySelector('.user-hutang-block').classList.add('d-none');
                newBarang.querySelector('.select-user').value = "";
                barangContainer.appendChild(newBarang);
            });

            document.addEventListener('click', e => {
                if (e.target.classList.contains('btn-remove-barang')) {
                    const parentBlock = e.target.closest('.barang-block');
                    if (barangContainer.querySelectorAll('.barang-block').length > 1) {
                        parentBlock.remove();
                        updateGrandTotal();
                    } else {
                        alert('Minimal harus ada 1 barang keluar.');
                    }
                }
            });
        });
    </script>
@endsection

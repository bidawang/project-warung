@extends('layouts.app')

@section('title', 'Jual Pulsa')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h2>Formulir Transaksi Jual Pulsa</h2>
                    </div>

                    <div class="card-body">

                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        <form id="formJualPulsa" action="{{ route('kasir.pulsa.jual.store') }}" method="POST">
                            @csrf

                            {{-- JENIS PULSA --}}
                            <div class="mb-3">
                                <label class="form-label">Jenis Pulsa</label>
                                <select name="jenis_pulsa_id" id="jenis_pulsa" class="form-control" required>
                                    <option value="">-- Pilih Jenis Pulsa --</option>
                                    @foreach ($jenisPulsa as $jp)
                                        <option value="{{ $jp->id }}"
                                            data-tipe="{{ Str::contains(strtolower($jp->nama_jenis), 'listrik') ? 'listrik' : 'hp' }}">
                                            {{ $jp->nama_jenis }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>


                            {{-- NOMOR TUJUAN --}}
                            <div class="mb-3">
                                <label class="form-label" id="label_nomor">Nomor HP Pelanggan</label>
                                <input type="text" id="nomor_hp" name="nomor_hp" class="form-control"
                                    value="{{ old('nomor_hp') }}" placeholder="Contoh: 081234567890" required>
                            </div>

                            <select id="harga_pulsa_id" name="harga_pulsa_id" class="form-control" required>
                                <option value="">-- Pilih Nominal --</option>
                                @foreach ($harga_pulsas as $p)
                                    <option value="{{ $p->id }}" data-harga="{{ $p->harga }}"
                                        data-jenis="{{ $p->jenis_pulsa_id }}">
                                        {{ number_format($p->jumlah_pulsa) }} - Rp {{ number_format($p->harga) }}
                                    </option>
                                @endforeach
                            </select>

                            {{-- TOTAL --}}
                            <div class="mb-3">
                                <label class="form-label">Harga Jual</label>
                                <input type="text" id="harga_jual_display" class="form-control" value="Rp 0" readonly>
                            </div>

                            <hr>

                            {{-- JENIS PEMBAYARAN --}}
                            <div class="mb-3">
                                <label class="form-label">Jenis Pembayaran</label>
                                <select id="jenis_pembayaran" name="jenis_pembayaran" class="form-control" required>
                                    <option value="penjualan pulsa">Tunai</option>
                                    <option value="hutang pulsa">Hutang</option>
                                </select>
                            </div>

                            {{-- PELANGGAN --}}
                            <div class="mb-3" id="pelanggan_field" style="display:none">
                                <label class="form-label">Pelanggan</label>
                                <select id="pelanggan_id" name="pelanggan_id" class="form-control">
                                    <option value="">-- Pilih Pelanggan --</option>
                                    @foreach ($pelanggans as $p)
                                        <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->nomor_hp }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- BAYAR --}}
                            <div class="mb-3" id="bayar_field">
                                <label class="form-label">Uang Bayar</label>
                                <input type="number" id="bayar" name="bayar" class="form-control" value="0">
                            </div>

                            {{-- KEMBALIAN --}}
                            <div class="mb-3" id="kembalian_field">
                                <label class="form-label">Kembalian</label>
                                <input type="text" id="kembalian_display" class="form-control" value="Rp 0" readonly>
                            </div>

                            <button class="btn btn-primary">Proses</button>
                            <a href="{{ route('kasir.pulsa.index') }}" class="btn btn-secondary">Batal</a>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const jenisPulsa = document.getElementById('jenis_pulsa');
            const labelNomor = document.getElementById('label_nomor');
            const nomorHP = document.getElementById('nomor_hp');
            const nominal = document.getElementById('harga_pulsa_id');
            const bayar = document.getElementById('bayar');
            const jenisBayar = document.getElementById('jenis_pembayaran');
            const pelangganField = document.getElementById('pelanggan_field');
            const bayarField = document.getElementById('bayar_field');
            const kembalianField = document.getElementById('kembalian_field');
            const hargaDisplay = document.getElementById('harga_jual_display');
            const kembalianDisplay = document.getElementById('kembalian_display');

            function rupiah(x) {
                return 'Rp ' + x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }

            jenisPulsa.addEventListener('change', () => {
                const selected = jenisPulsa.selectedOptions[0];
                const tipe = selected?.dataset.tipe || 'hp';

                if (tipe === 'listrik') {
                    labelNomor.textContent = 'Nomor Meter / ID PLN';
                    nomorHP.placeholder = 'Contoh: 12345678901';
                } else {
                    labelNomor.textContent = 'Nomor HP Pelanggan';
                    nomorHP.placeholder = 'Contoh: 081234567890';
                }

                // filter nominal sesuai jenis pulsa
                Array.from(nominal.options).forEach(opt => {
                    if (!opt.value) return;
                    opt.style.display =
                        opt.dataset.jenis === jenisPulsa.value ? 'block' : 'none';
                });

                nominal.value = '';
                hargaDisplay.value = 'Rp 0';
            });


            function hitung() {
                const harga = nominal.selectedOptions[0]?.dataset.harga || 0;
                hargaDisplay.value = rupiah(harga);

                if (jenisBayar.value === 'hutang pulsa') {
                    kembalianDisplay.value = rupiah(0);
                    return;
                }

                const kembali = bayar.value - harga;
                kembalianDisplay.value = rupiah(kembali);
            }

            nominal.addEventListener('change', hitung);
            bayar.addEventListener('input', hitung);

            jenisBayar.addEventListener('change', () => {
                if (jenisBayar.value === 'hutang pulsa') {
                    pelangganField.style.display = 'block';
                    bayarField.style.display = 'none';
                    kembalianField.style.display = 'none';
                    bayar.value = 0;
                } else {
                    pelangganField.style.display = 'none';
                    bayarField.style.display = 'block';
                    kembalianField.style.display = 'block';
                }
                hitung();
            });

        });
    </script>
@endsection

@extends('layouts.app')

@section('title', 'Jual Pulsa')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-header bg-white py-3 border-bottom">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-signal text-primary me-2 fs-4"></i>
                            <h5 class="card-title mb-0 fw-bold">Transaksi Jual Pulsa</h5>
                        </div>
                    </div>
                    @if ($errors->any())
                        <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4">
                            <div class="fw-bold mb-2">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                Terjadi Kesalahan
                            </div>

                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4">
                            <i class="fas fa-times-circle me-2"></i>
                            {{ session('error') }}
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                        </div>
                    @endif
                    <div class="card-body p-4">
                        <form id="formJualPulsa" action="{{ route('kasir.pulsa.jual.store') }}" method="POST">
                            @csrf

                            {{-- JENIS PULSA --}}
                            <div class="mb-4">
                                <label class="form-label fw-semibold text-secondary small uppercase">1. Jenis
                                    Layanan</label>
                                <select name="jenis_pulsa_id" id="jenis_pulsa" class="form-select select2" required>
                                    <option value="">-- Pilih Jenis Pulsa/Token --</option>
                                    @foreach ($jenisPulsa as $jp)
                                        <option value="{{ $jp->id }}"
                                            data-tipe="{{ Str::contains(strtolower($jp->nama_jenis), 'listrik') ? 'listrik' : 'hp' }}">
                                            {{ $jp->nama_jenis }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- NOMOR TUJUAN --}}
                            {{-- <div class="mb-4">
                            <label class="form-label fw-semibold text-secondary small uppercase" id="label_nomor">Nomor HP Pelanggan</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-phone text-muted"></i></span>
                                <input type="text" id="nomor_hp" name="nomor_hp" class="form-control border-start-0 ps-0"
                                    value="{{ old('nomor_hp') }}" placeholder="Contoh: 0812..." required>
                            </div>
                        </div> --}}
                            <input type="hidden" name="nomor_hp" value="0">
                            {{-- PILIH NOMINAL (Hanya muncul jika jenis sudah dipilih) --}}
                            <div class="mb-4" id="nominal_wrapper" style="display: none;">
                                <label class="form-label fw-semibold text-secondary small uppercase">2. Pilih
                                    Nominal</label>
                                <select id="harga_pulsa_id" name="harga_pulsa_id" class="form-select select2" required
                                    disabled>
                                    <option value="">-- Pilih Nominal --</option>
                                    @foreach ($harga_pulsas as $p)
                                        <option value="{{ $p->id }}" data-jual="{{ $p->harga_jual }}"
                                            data-hutang="{{ $p->harga_hutang }}" data-jenis="{{ $p->jenis_pulsa_id }}">
                                            {{ number_format($p->jumlah_pulsa) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="row bg-light rounded-3 p-3 mb-4 mx-0 border">
                                {{-- JENIS PEMBAYARAN --}}
                                <div class="col-sm-6 mb-3 mb-sm-0">
                                    <label class="form-label fw-bold small">Metode Pembayaran</label>
                                    <select id="jenis_pembayaran" name="jenis_pembayaran" class="form-select form-select-sm"
                                        required>
                                        <option value="cash">Tunai</option>
                                        <option value="hutang">Hutang</option>
                                    </select>
                                </div>
                                {{-- HARGA FINAL DISPLAY --}}
                                <div class="col-sm-6">
                                    <label class="form-label fw-bold small text-primary">Total Bayar</label>
                                    <div class="fs-4 fw-bold text-dark" id="harga_jual_display">Rp 0</div>
                                </div>
                            </div>

                            {{-- PELANGGAN (Hanya jika Hutang) --}}
                            <div class="mb-4" id="pelanggan_field" style="display:none">
                                <label class="form-label fw-semibold text-secondary small uppercase">Pilih Pelanggan</label>
                                <select id="pelanggan_id" name="pelanggan_id" class="form-select select2">
                                    <option value="">-- Cari Nama Pelanggan --</option>
                                    @foreach ($pelanggans as $p)
                                        <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->nomor_hp }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- TUNAI AREA --}}
                            <div id="tunai_area">
                                <div class="row">
                                    <div class="col-6 mb-4">
                                        <label class="form-label fw-semibold text-secondary small uppercase">Uang
                                            Bayar</label>
                                        <input type="number" id="bayar" name="bayar" class="form-control"
                                            value="0">
                                    </div>
                                    <div class="col-6 mb-4">
                                        <label
                                            class="form-label fw-semibold text-secondary small uppercase">Kembalian</label>
                                        <input type="text" id="kembalian_display"
                                            class="form-control bg-light border-0 fw-bold" value="Rp 0" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg shadow-sm border-0 py-3">
                                    <i class="fas fa-check-circle me-2"></i>Proses Transaksi
                                </button>
                                <a href="{{ route('kasir.pulsa.index') }}"
                                    class="btn btn-link text-muted text-decoration-none small">
                                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Riwayat
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tambahkan script khusus di bawah --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // Inisialisasi Select2 dengan tema Bootstrap 5
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });

            const $jenisPulsa = $('#jenis_pulsa');
            const $nominal = $('#harga_pulsa_id');
            const $nominalWrapper = $('#nominal_wrapper');
            const $labelNomor = $('#label_nomor');
            const $nomorHP = $('#nomor_hp');
            const $jenisBayar = $('#jenis_pembayaran');
            const $bayar = $('#bayar');
            const $hargaDisplay = $('#harga_jual_display');
            const $kembalianDisplay = $('#kembalian_display');

            function formatRupiah(x) {
                return 'Rp ' + x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }

            // 1. Logika Filter Jenis Pulsa
            $jenisPulsa.on('change', function() {
                const selected = $(this).find(':selected');
                const tipe = selected.data('tipe') || 'hp';
                const val = $(this).val();

                // Ubah Label
                $labelNomor.text(tipe === 'listrik' ? 'Nomor Meter / ID PLN' : 'Nomor HP Pelanggan');
                $nomorHP.attr('placeholder', tipe === 'listrik' ? 'Contoh: 12345678901' :
                    'Contoh: 081234567890');

                if (val) {
                    $nominalWrapper.slideDown();
                    $nominal.prop('disabled', false);

                    // Filter Nominal yang sesuai jenisnya saja
                    $nominal.find('option').each(function() {
                        const optJenis = $(this).data('jenis');
                        if (!$(this).val()) return; // Skip placeholder

                        if (optJenis == val) {
                            $(this).show();
                        } else {
                            $(this).hide();
                        }
                    });

                    // Reset nominal select
                    $nominal.val('').trigger('change');
                } else {
                    $nominalWrapper.slideUp();
                    $nominal.prop('disabled', true);
                }
            });

            // 2. Logika Hitung Total & Harga
            function hitung() {
                const selectedNominal = $nominal.find(':selected');
                const metode = $jenisBayar.val();

                let harga = 0;

                if (selectedNominal.val()) {
                    harga = (metode === 'hutang') ?
                        selectedNominal.data('hutang') :
                        selectedNominal.data('jual');
                }

                $hargaDisplay.text(formatRupiah(harga));

                if (metode === 'cash') {
                    const totalBayar = parseFloat($bayar.val()) || 0;
                    const kembali = totalBayar - harga;

                    $kembalianDisplay.val(formatRupiah(kembali));

                    if (kembali < 0) {
                        $kembalianDisplay.addClass('text-danger').removeClass('text-dark');
                    } else {
                        $kembalianDisplay.removeClass('text-danger').addClass('text-dark');
                    }
                }
            }

            $nominal.on('change', hitung);
            $bayar.on('input', hitung);

            // 3. Logika Ganti Metode Pembayaran
            $jenisBayar.on('change', function() {
                if ($(this).val() === 'hutang') {
                    $('#pelanggan_field').slideDown();
                    $('#tunai_area').slideUp();
                    $bayar.val(0);
                } else {
                    $('#pelanggan_field').slideUp();
                    $('#tunai_area').slideDown();
                }

                hitung();
            });
        });
    </script>

    <style>
        /* Custom Styling tambahan untuk menyesuaikan layout headbar/bottom-sidebar */
        .select2-container--bootstrap-5 .select2-selection {
            border-radius: 0.5rem;
            padding-top: 0.25rem;
            padding-bottom: 0.25rem;
        }

        .card {
            border-radius: 1rem;
        }

        .uppercase {
            letter-spacing: 0.05em;
            font-size: 0.65rem;
        }
    </style>
@endsection

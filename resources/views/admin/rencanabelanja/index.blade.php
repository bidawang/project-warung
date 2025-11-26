@extends('layouts.admin')

@section('title', 'Rencana Belanja Per Warung')

@section('content')
<div class="flex-1 flex flex-col overflow-hidden">
    {{-- Header (Sama seperti sebelumnya) --}}
    <header class="flex justify-between items-center p-6 bg-white border-b-2 border-gray-200 shadow-sm">
        <button id="openSidebarBtn" class="text-gray-500 hover:text-gray-900 lg:hidden">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>
        <h1 class="text-2xl font-bold text-gray-800">Manajemen Rencana Belanja</h1>
        <div class="flex items-center">
            <span class="mr-4 font-semibold hidden sm:inline">Admin User</span>
            <div class="w-10 h-10 bg-blue-500 rounded-full"></div>
        </div>
    </header>

    @if ($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <strong>Terjadi kesalahan:</strong>
        <ul class="mt-2 list-disc list-inside text-sm">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Main Content --}}
    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-6 md:p-10">
        <div class="container mx-auto grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="md:col-span-1">
                {{-- Tombol Navigasi --}}
                <a href="{{ route('transaksibarang.index') }}"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow transition-colors duration-200 flex items-center justify-center text-sm mb-4">
                    <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h11l-3-3m0 6l3-3m-3 3v7m9-14v2a2 2 0 01-2 2h-6a2 2 0 01-2-2v-2a2 2 0 012-2h6a2 2 0 012 2z" />
                    </svg>
                    Ke Stok Pengiriman
                </a>
                <a href="{{ route('transaksibarang.create') }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow transition-colors duration-200 flex items-center justify-center text-sm">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Buat Transaksi Baru
                </a>
            </div>

            {{-- Kolom Kanan: Rencana Belanja (3/4) --}}
            <div class="md:col-span-3 bg-white p-6 rounded-xl shadow-xl border border-gray-100">
                <form id="formKirimRencana" method="POST" action="{{ route('admin.transaksibarang.kirim.rencana.proses') }}">
                    @csrf
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 border-b pb-4">
                        <h2 class="text-2xl font-bold text-gray-800 flex items-center mb-2 md:mb-0">
                            <svg class="w-6 h-6 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2m-9 0a2 2 0 002 2h2m-2 0h-2m9 0h2m-2 0a2 2 0 00-2-2h-2" />
                            </svg>
                            Rencana Belanja (Per Warung)
                        </h2>
                        {{-- Tombol Submit Rencana Belanja --}}
                        <button type="submit" id="btnSubmitRencana" disabled
                            class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg shadow transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center text-sm">
                            <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            Kirim Rencana Terpilih
                        </button>
                    </div>

                    {{-- Search --}}
                    <input type="text" id="searchRencana" class="w-full mb-6 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Cari Warung...">

                    {{-- Container Rencana Belanja per Warung --}}
                    <div id="rencanaContainer" class="space-y-6">
                        @forelse($rencanaBelanjaByWarung as $warungId => $items)
                        <div class="p-4 rounded-lg bg-indigo-50 border border-indigo-200 item-block rencana-warung-block"
                            data-warung-id="{{ $warungId }}" data-nama-warung="{{ $items[0]->warung->nama_warung }}">
                            <h3 class="font-bold text-indigo-700 text-lg mb-3 border-b border-indigo-300 pb-1 flex justify-between items-center">
                                <span>{{ $items[0]->warung->nama_warung }}</span>
                                <input type="checkbox" class="chk-rencana-warung cursor-pointer" data-warung-id="{{ $warungId }}" data-valid="false"/>
                            </h3>
                            <ul class="list-none text-sm text-gray-700 space-y-3">
                                @foreach($items as $i)
                                @php
                                    $rencanaId = $i->id;
                                    $barangId = $i->barang->id;
                                    $namaBarang = $i->barang->nama_barang;
                                    $jumlahKebutuhan = $i->jumlah_awal - $i->jumlah_dibeli;
                                @endphp
                                <li class="rencana-item flex flex-col space-y-1 p-3 border-l-4 border-indigo-400 bg-white shadow-sm"
                                    data-rencana-id="{{ $rencanaId }}" data-barang-id="{{ $barangId }}" data-kebutuhan="{{ $jumlahKebutuhan }}">
                                    <span class="font-semibold text-base">{{ $namaBarang }}</span>
                                    <div class="grid grid-cols-1 md:grid-cols-4 gap-2 items-center text-xs">
                                        <div class="flex items-center space-x-2">
                                            <span class="text-gray-500">Kebutuhan:</span>
                                            <span class="font-bold text-red-600">{{ $jumlahKebutuhan }}</span>
                                        </div>

                                        <div class="flex items-center space-x-2">
                                            <span class="text-gray-500">Jml. Kirim:</span>
                                            <input type="number" name="rencana[{{ $items[0]->warung->id }}][{{ $rencanaId }}][jumlah_kirim]" value="{{ $jumlahKebutuhan }}"
                                                min="0" max="{{ $jumlahKebutuhan }}" disabled required
                                                class="rencana-qty-input border border-gray-300 rounded px-2 py-1 w-16 text-center focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100 transition duration-150"/>
                                        </div>

                                        <div class="col-span-2 flex items-center space-x-2">
                                            <span class="text-gray-500">Sumber Stok:</span>
                                            <input type="hidden" name="rencana[{{ $items[0]->warung->id }}][{{ $rencanaId }}][rencana_id]" value="{{ $rencanaId }}">
                                            <input type="hidden" name="rencana[{{ $items[0]->warung->id }}][{{ $rencanaId }}][barang_id]" value="{{ $barangId }}">
                                            <select
                                                name="rencana[{{ $items[0]->warung->id }}][{{ $rencanaId }}][transaksi_id]"
                                                disabled required
                                                class="rencana-trx-select border border-gray-300 rounded px-2 py-1 flex-1 focus:ring-blue-500 focus:border-blue-500 bg-white disabled:bg-gray-100 transition duration-150"
                                                data-barang-id="{{ $i->id_barang }}">
                                                <option value="" disabled selected>Pilih Sumber Stok...</option>
                                            </select>
                                        </div>
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @empty
                        <p class="text-center text-gray-500 py-4">Tidak ada rencana belanja yang tertunda.</p>
                        @endforelse
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Data Global (Membutuhkan data transaksi sumber dari DB)
        // Disesuaikan untuk menggunakan $allTransactionsForJs dari controller
        const allTransactions = @json($allTransactionsForJs ?? []);
        // Jika perlu, ambil Warung data dari DB:
        // const warungs = @json(\App\Models\Warung::pluck('nama_warung', 'id'));

        // Forms dan Buttons
        const formRencana = document.getElementById('formKirimRencana');
        const btnSubmitRencana = document.getElementById('btnSubmitRencana');
        const rencanaBlocks = document.querySelectorAll('.rencana-warung-block');

        let currentStockSisa = {};
        const stockByBarang = {};

        // Inisialisasi State Stok (Sama seperti Bagian 1)
        allTransactions.forEach(trx => {
            const barangId = trx.id_barang;
            if (!stockByBarang[barangId]) {
                stockByBarang[barangId] = [];
            }
            stockByBarang[barangId].push({
                id: trx.id,
                jumlah_awal: trx.jumlah,
                nama_barang: trx.barang?.nama_barang ?? "N/A",
                harga: trx.harga
            });
            currentStockSisa[trx.id] = trx.jumlah;
        });

        function numberWithCommas(x) {
            if (x === undefined || x === null) return '0';
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        /**
         * Fungsi utama untuk menghitung ulang sisa stok global
         * HANYA dikurangi oleh alokasi dari Kolom Rencana Belanja yang tercentang.
         */
        function recalculateGlobalStock() {
            // 1. Reset sisa stok ke nilai awal
            Object.values(stockByBarang).flat().forEach(stock => {
                currentStockSisa[stock.id] = stock.jumlah_awal;
            });

            // 2. Kurangi dari Kolom Rencana Belanja yang tercentang
            document.querySelectorAll('.chk-rencana-warung:checked').forEach(chk => {
                const warungBlock = chk.closest('.rencana-warung-block');
                if (!warungBlock) return;

                warungBlock.querySelectorAll('.rencana-item').forEach(itemEl => {
                    const trxSelect = itemEl.querySelector('.rencana-trx-select:not(:disabled)');
                    const qtyInput = itemEl.querySelector('.rencana-qty-input:not(:disabled)');

                    if (trxSelect && qtyInput) {
                        const trxId = trxSelect.value;
                        const qty = +qtyInput.value;

                        if (trxId && qty > 0) {
                            currentStockSisa[trxId] = Math.max(0, currentStockSisa[trxId] - qty);
                        }
                    }
                });
            });

            // 3. Setelah menghitung ulang, validasi ulang semua alokasi Rencana yang aktif
            document.querySelectorAll('.chk-rencana-warung:checked').forEach(chk => updateRencanaWarung(chk.dataset.warungId, false));

            // 4. Perbarui teks di dropdown Rencana Belanja
            updateRencanaOptionsGlobal();
        }

        // --- Fungsi yang dipindahkan dari Bagian 2 (Relevan dengan Kolom Kanan) ---

        function populateStockOptions(selectEl, barangId) {
            const stocks = stockByBarang[barangId] || [];
            let options = '<option value="" disabled selected>Pilih Sumber Stok...</option>';
            stocks.forEach(stock => {
                options += `<option value="${stock.id}" data-max-stock="${stock.jumlah_awal}" data-harga="${stock.harga}">
                    Stok #${stock.id} (${stock.jumlah_awal} pcs) @Rp${numberWithCommas(stock.harga)}
                </option>`;
            });
            selectEl.innerHTML = options;
        }

        function updateRencanaOptionsGlobal() {
            const allRencanaSelects = document.querySelectorAll('.rencana-trx-select');
            allRencanaSelects.forEach(selectEl => {
                const currentValue = selectEl.value;

                [...selectEl.options].forEach(option => {
                    const trxId = option.value;
                    if (!trxId) return;

                    const stock = Object.values(stockByBarang).flat().find(s => s.id == trxId);
                    const sisa = currentStockSisa[trxId] || 0;

                    // Perbarui teks opsi dengan sisa stok real-time
                    option.textContent = `Stok #${trxId} (${sisa} pcs) @Rp${numberWithCommas(stock.harga)}`;

                    // Nonaktifkan opsi jika stok habis DAN opsi tersebut BUKAN yang sedang dipilih
                    const isDisabled = sisa <= 0 && trxId !== currentValue;
                    option.disabled = isDisabled;
                    option.classList.toggle('disabled-option', isDisabled);
                });

                // Jika stok yang sedang dipilih tiba-tiba menjadi 0, beri indikasi error
                if(currentValue && currentStockSisa[currentValue] <= 0 && selectEl.disabled === false){
                    selectEl.classList.add('border-red-500');
                }
            });
        }

        function updateRencanaWarung(warungId, shouldRecalculate = true) {
            if (shouldRecalculate) recalculateGlobalStock();

            const warungBlock = document.querySelector(`.rencana-warung-block[data-warung-id="${warungId}"]`);
            if (!warungBlock) return;

            const chk = warungBlock.querySelector('.chk-rencana-warung');
            const items = [...warungBlock.querySelectorAll('.rencana-item')];
            let isValid = true;
            let isChecked = chk.checked;

            items.forEach(itemEl => {
                const qtyInput = itemEl.querySelector('.rencana-qty-input');
                const trxSelect = itemEl.querySelector('.rencana-trx-select');
                const kebutuhan = +itemEl.dataset.kebutuhan;
                const qty = +qtyInput.value;
                const trxId = trxSelect.value;

                qtyInput.classList.remove('border-red-500');
                trxSelect.classList.remove('border-red-500');

                // Toggle disable/enable
                qtyInput.disabled = !isChecked;
                trxSelect.disabled = !isChecked;

                // Logika untuk menonaktifkan pengiriman nama di form submit ketika disabled
                [qtyInput, trxSelect].forEach(input => {
                    if (!isChecked) {
                        input.setAttribute('data-original-name', input.getAttribute('name'));
                        input.removeAttribute('name');
                    } else {
                        const originalName = input.getAttribute('data-original-name');
                        if (originalName) {
                            input.setAttribute('name', originalName);
                            input.removeAttribute('data-original-name');
                        }
                    }
                });

                if (isChecked) {
                    // VALIDASI SUMBER STOK (TRX ID)
                    if (!trxId) {
                        trxSelect.classList.add('border-red-500');
                        isValid = false;
                    }

                    // VALIDASI QTY vs Kebutuhan
                    if (qty < 1 || qty > kebutuhan || isNaN(qty)) {
                        qtyInput.classList.add('border-red-500');
                        isValid = false;
                    }

                    // VALIDASI QTY vs STOK GLOBAL (jika trxId sudah dipilih)
                    if (trxId) {
                        let totalQtyRencanaIni = 0;
                        items.forEach(i => {
                            const s = i.querySelector('.rencana-trx-select').value;
                            const q = +i.querySelector('.rencana-qty-input').value;
                            if(s === trxId) totalQtyRencanaIni += q;
                        });

                        const stokAwal = Object.values(stockByBarang).flat().find(s => s.id == trxId).jumlah_awal;

                        // Hitung total alokasi DARI SUMBER TRX ID INI, di SEMUA Warung Rencana yang tercentang (kecuali warung saat ini)
                        let alokasiRencanaLain = 0;
                        document.querySelectorAll('.chk-rencana-warung:checked').forEach(otherChk => {
                            const otherWarungId = otherChk.dataset.warungId;
                            if (otherWarungId !== warungId.toString()) {
                                otherChk.closest('.rencana-warung-block').querySelectorAll('.rencana-item').forEach(otherItem => {
                                    if (otherItem.querySelector('.rencana-trx-select').value == trxId) {
                                        alokasiRencanaLain += +otherItem.querySelector('.rencana-qty-input').value;
                                    }
                                });
                            }
                        });

                        const stokTersediaGlobal = stokAwal - alokasiRencanaLain;

                        // Cek ketersediaan: Total Qty yang diminta Warung ini tidak boleh melebihi stok yang tersisa
                        if (totalQtyRencanaIni > stokTersediaGlobal) {
                            qtyInput.classList.add('border-red-500');
                            isValid = false;
                        }
                    }
                }
            });

            // Update data-valid dan tombol submit
            chk.dataset.valid = isValid.toString();
            updateSubmitRencana();
        }

        function updateSubmitRencana() {
            const checkedWarungs = [...document.querySelectorAll('.chk-rencana-warung:checked')];
            const canSubmit = checkedWarungs.length > 0 && checkedWarungs.every(c => c.dataset.valid === "true");
            btnSubmitRencana.disabled = !canSubmit;
        }

        // --- INISIALISASI ---

        // Pastikan semua checkbox awalnya tidak tercentang
        document.querySelectorAll('.chk-rencana-warung').forEach(chk => {
            chk.checked = false;
        });

        // Isi dropdown Rencana Belanja dengan opsi sumber stok
        document.querySelectorAll('.rencana-item').forEach(itemEl => {
            const selectEl = itemEl.querySelector('.rencana-trx-select');
            const barangId = selectEl.dataset.barangId;
            populateStockOptions(selectEl, barangId);
        });

        // Matikan input Rencana Belanja dan jalankan validasi awal
        rencanaBlocks.forEach(block => {
            updateRencanaWarung(block.dataset.warungId, false);
        });

        recalculateGlobalStock();

        // --- EVENT LISTENERS ---

        // 1. Event Change (Checkbox Rencana, Stock Select Rencana)
        document.addEventListener('change', e => {
            if (e.target.classList.contains('chk-rencana-warung')) {
                updateRencanaWarung(e.target.dataset.warungId);
            } else if (e.target.classList.contains('rencana-trx-select')) {
                const warungBlock = e.target.closest('.rencana-warung-block');
                recalculateGlobalStock();
                if (warungBlock) updateRencanaWarung(warungBlock.dataset.warungId, false);
            }
        });

        // 2. Event Input (Qty Input Rencana)
        document.addEventListener('input', e => {
            if (e.target.classList.contains('rencana-qty-input')) {
                const warungBlock = e.target.closest('.rencana-warung-block');
                if (!warungBlock) return;
                const max = +e.target.max;
                const inputVal = +e.target.value;

                if (inputVal > max) e.target.value = max;
                if (inputVal < 0) e.target.value = 0;

                recalculateGlobalStock();
                updateRencanaWarung(warungBlock.dataset.warungId, false);
            }
        });

        // 3. Logika Rencana Belanja (Filter)
        const searchRencana = document.getElementById('searchRencana');
        searchRencana.addEventListener('input', () => {
            const q = searchRencana.value.toLowerCase();
            rencanaBlocks.forEach(block => {
                const namaWarung = block.dataset.namaWarung.toLowerCase();
                const match = namaWarung.includes(q);
                block.classList.toggle('hidden', !match);
            });
        });

        // 4. Submit form
        formRencana.addEventListener('submit', e => {
            if(btnSubmitRencana.disabled){
                e.preventDefault();
                alert('Terdapat rencana belanja yang tidak valid. Pastikan semua item memiliki sumber stok dan jumlah kirim yang sesuai dengan sisa stok global.');
            }
        });

    });
</script>
<style>
    .border-red-500 {
        border-color: #ef4444 !important;
        box-shadow: 0 0 0 1px #ef4444 !important;
    }
    .text-red-600-strong {
        color: #dc2626 !important;
    }
    .disabled-option {
        color: #9ca3af;
        font-style: italic;
    }
</style>

@endsection

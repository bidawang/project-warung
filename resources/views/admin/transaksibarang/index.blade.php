@extends('layouts.admin')

@section('title', 'Daftar Stok Pengiriman Barang')

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
        <h1 class="text-2xl font-bold text-gray-800">Manajemen Pengiriman Stok Barang</h1>
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
        <div class="container mx-auto">

            {{-- Tombol Aksi & Header --}}
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-800 mb-4 md:mb-0">Daftar Stok Sumber Pengiriman</h1>
                <div class="flex space-x-4">
                    @if($status === 'pending')
                    {{-- Tombol untuk Kolom Kiri (Pengiriman Massal Standar) --}}
                    <button type="submit" form="formKirim" id="btnSubmitMassal" disabled
                        class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center text-sm">
                        <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h11l-3-3m0 6l3-3m-3 3v7m9-14v2a2 2 0 01-2 2h-6a2 2 0 01-2-2v-2a2 2 0 012-2h6a2 2 0 012 2z" />
                        </svg>
                        Kirim Stok Terpilih
                    </button>
                    {{-- Tombol navigasi ke Rencana Belanja --}}
                    <a href="{{ route('admin.rencana.index') }}"
                        class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-lg shadow transition-colors duration-200 flex items-center justify-center text-sm">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2m-9 0a2 2 0 002 2h2m-2 0h-2m9 0h2m-2 0a2 2 0 00-2-2h-2" />
                        </svg>
                        Ke Rencana Belanja
                    </a>
                    @endif
                    <a href="{{ route('admin.transaksibarang.create') }}"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow transition-colors duration-200 flex items-center justify-center text-sm">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Baru
                    </a>
                </div>
            </div>

            {{-- Nav Tabs (Status Transaksi) --}}
            <nav class="mb-6 border-b border-gray-300">
                @php
                $tabs = [
                    'pending' => 'Belum Dikirim',
                    'kirim' => 'Dikirim',
                    'terima' => 'Sudah Diterima',
                    'tolak' => 'Ditolak'
                ];
                @endphp
                <ul class="flex space-x-4">
                    @foreach($tabs as $key => $label)
                    <li>
                        <a href="{{ route('admin.transaksibarang.index',['status'=>$key]) }}"
                            class="inline-block px-4 py-2 rounded-t-lg font-semibold transition-colors
                            {{ $status === $key ? 'bg-white border border-b-0 border-gray-300 text-blue-600' : 'text-gray-600 hover:text-blue-600' }}">
                            {{ $label }}
                        </a>
                    </li>
                    @endforeach
                </ul>
            </nav>

            @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-700 rounded-lg shadow-sm">
                {{ session('success') }}
            </div>
            @endif

            {{-- Tabel Transaksi (Stok Sumber) --}}
            <div class="bg-white shadow-xl rounded-lg overflow-hidden overflow-x-auto">
                <form id="formKirim" method="POST" action="{{ route('admin.transaksibarang.kirim.mass.proses') }}">
                    @csrf
                    <table class="min-w-full leading-normal">
                        <thead class="bg-gray-100">
                            <tr>
                                @if($status === 'pending')
                                <th class="px-4 py-3 border-b-2 border-gray-200 w-[4%] text-center">
                                    <input type="checkbox" id="checkAll" class="cursor-pointer"/>
                                </th>
                                @endif
                                <th class="px-4 py-3 border-b-2 text-left text-xs font-semibold text-gray-600 uppercase w-1/5">Barang</th>
                                <th class="px-4 py-3 border-b-2 text-center text-xs font-semibold text-gray-600 uppercase w-[10%]">Sisa Jml.</th>
                                <th class="px-4 py-3 border-b-2 text-left text-xs font-semibold text-gray-600 uppercase w-2/5">Tujuan & Jumlah Kirim</th>
                                <th class="px-4 py-3 border-b-2 text-left text-xs font-semibold text-gray-600 uppercase w-[10%]">Harga Satuan</th>
                                <th class="px-4 py-3 border-b-2 text-center text-xs font-semibold text-gray-600 uppercase w-[10%]">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transaksibarangs as $trx)
                            <tr class="hover:bg-gray-50 border-t border-gray-200" id="trx-{{ $trx->id }}">
                                @if($status === 'pending')
                                <td class="px-4 py-4 border-b text-sm text-center">
                                    <input type="checkbox" class="chk-trx cursor-pointer" data-id="{{ $trx->id }}"
                                        name="transaksi_ids[]" value="{{ $trx->id }}" data-valid="false">
                                </td>
                                @endif

                                <td class="px-4 py-4 border-b text-sm font-semibold text-gray-700">
                                    {{ $trx->barang->nama_barang ?? '-' }}
                                    {{-- ⭐ PERBAIKAN: Hidden input BARANG_ID diletakkan di sini --}}
                                    @if($status === 'pending')
                                    <input type="hidden"
                                           name="transaksi[{{ $trx->id }}][barang_id]"
                                           value="{{ $trx->id_barang }}">
                                    @endif
                                    {{-- ⭐ END PERBAIKAN --}}
                                </td>

                                <td class="px-4 py-4 border-b text-sm text-center">
                                    <span class="font-bold text-lg text-blue-600" id="sisa-{{ $trx->id }}" data-max="{{ $trx->jumlah }}">{{ $trx->jumlah }}</span>
                                </td>

                                <td class="px-4 py-4 border-b text-sm">
                                    <div id="deliveries-{{ $trx->id }}" data-id="{{ $trx->id }}" class="space-y-2"></div>
                                </td>

                                <td class="px-4 py-4 border-b text-sm text-gray-600">{{ number_format($trx->harga, 0, ',', '.') }}</td>

                                <td class="px-4 py-4 border-b text-sm text-center">
                                    @if($status === 'pending')
                                    <button type="button" class="btn-add text-xs bg-indigo-500 hover:bg-indigo-600 text-white font-semibold py-1 px-3 rounded-full transition duration-150 disabled:opacity-50 disabled:cursor-not-allowed"
                                        data-id="{{ $trx->id }}" disabled>+ Warung Lain</button>
                                    @else
                                    <span class="text-gray-500">{{ ucfirst($trx->status) }}</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ $status === 'pending' ? 6 : 5 }}" class="px-5 py-5 border-b text-center text-gray-500">
                                    Tidak ada transaksi untuk status **{{ ucfirst($status) }}**.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </form>
            </div>

            <div class="mt-6">
                {{ $transaksibarangs->links() }}
            </div>
        </div>
    </main>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const isPending = "{{ $status }}" === 'pending';
        if (!isPending) return;

        // Data yang Dibutuhkan
        const warungs = @json($warungs->pluck('nama_warung','id'));
        const allTransactions = @json($transaksibarangs->getCollection() ?? []);

        // Forms dan Buttons
        const formKirim = document.getElementById('formKirim');
        const btnSubmitMassal = document.getElementById('btnSubmitMassal');
        const checkAll = document.getElementById('checkAll');

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

        const warungOptions = Object.entries(warungs).map(([id, nm]) => `<option value="${id}">${nm}</option>`).join('');
        const baseOptions = `<option value="" disabled selected>Pilih Warung</option>${warungOptions}`;

        function numberWithCommas(x) {
            if (x === undefined || x === null) return '0';
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        /**
         * Fungsi utama untuk menghitung ulang sisa stok global
         * Karena ini halaman Pengiriman, kita hanya perlu mengurangi dari alokasi lokal (Kolom Kiri)
         */
        function recalculateGlobalStock() {
            // 1. Reset sisa stok ke nilai awal
            Object.values(stockByBarang).flat().forEach(stock => {
                currentStockSisa[stock.id] = stock.jumlah_awal;
            });

            // 2. Kurangi dari Kolom Kiri (Pengiriman Massal Standar) yang tercentang
            document.querySelectorAll('.chk-trx:checked').forEach(chk => {
                const trxId = chk.dataset.id;
                const container = document.getElementById(`deliveries-${trxId}`);

                container.querySelectorAll('.qty-input:not(:disabled)').forEach(qtyInput => {
                    const qty = +qtyInput.value;
                    if (qty > 0) {
                        currentStockSisa[trxId] = Math.max(0, currentStockSisa[trxId] - qty);
                    }
                });
            });

            // 3. Update tampilan Sisa Jml di Kolom Kiri
            Object.keys(currentStockSisa).forEach(trxId => {
                const sisaEl = document.getElementById(`sisa-${trxId}`);
                if (sisaEl) {
                    const sisa = currentStockSisa[trxId];
                    sisaEl.textContent = sisa;
                    sisaEl.classList.toggle('text-red-600-strong', sisa <= 0);
                    sisaEl.classList.toggle('text-blue-600', sisa > 0);
                }
            });

            // 4. Validasi ulang semua alokasi yang aktif
            document.querySelectorAll('.chk-trx:checked').forEach(chk => updateSisa(chk.dataset.id, false));
        }

        // --- Fungsi yang dipindahkan dari Bagian 2 (Relevan dengan Kolom Kiri) ---

        function createDeliveryRow(trxId, maxQty, isFirst = false) {
            let defaultQty = maxQty > 0 ? maxQty : 1;
            const rowId = `row-${trxId}-${Date.now()}-${Math.floor(Math.random() * 1000)}`;

            return `
                <div class="flex space-x-2 items-center delivery-row" data-trx="${trxId}" data-row-id="${rowId}">
                    <select name="transaksi[${trxId}][details][${rowId}][warung_id]" required
                        class="warung-select border border-gray-300 rounded px-2 py-1 text-sm flex-1 focus:ring-blue-500 focus:border-blue-500 bg-white transition duration-150">
                        ${baseOptions}
                    </select>
                    {{-- ⭐ BARIS HIDDEN INPUT BARANG_ID DIHAPUS DARI SINI --}}
                    <input type="number" name="transaksi[${trxId}][details][${rowId}][jumlah]" value="${defaultQty}" min="1" max="${maxQty}" required
                        class="qty-input border border-gray-300 rounded px-2 py-1 text-sm w-16 text-center focus:ring-blue-500 focus:border-blue-500 transition duration-150"/>
                    <button type="button" class="btn-del text-red-500 hover:text-red-700 text-xs font-semibold w-10 py-1 transition duration-150 rounded" title="Hapus baris" ${isFirst ? 'style="visibility:hidden;"' : ''}>Hapus</button>
                </div>
            `;
        }

        function toggleInputs(id, enable) {
            const container = document.getElementById(`deliveries-${id}`);
            const max = +document.getElementById(`sisa-${id}`).dataset.max;
            const btnAdd = document.querySelector(`.btn-add[data-id="${id}"]`);

            if (enable && container.children.length === 0) {
                container.innerHTML = createDeliveryRow(id, max, true);
            }

            container.querySelectorAll('select, input, button').forEach(el => {
                if (el.classList.contains('btn-del') && el.style.visibility === 'hidden') return;
                el.disabled = !enable;
            });

            if(btnAdd) btnAdd.disabled = !enable; // Tambahkan toggle untuk button Add

            // Logika untuk menonaktifkan pengiriman nama di form submit ketika disabled
            container.querySelectorAll('input:not([type="hidden"]), select').forEach(input => {
                if (!enable) {
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
            updateSisa(id, true);
        }

        function updateWarungOptions(trxId) {
            const container = document.getElementById(`deliveries-${trxId}`);
            const allSelects = [...container.querySelectorAll('.warung-select:not(:disabled)')];
            const selectedWarungs = new Set();

            allSelects.forEach(select => { if (select.value) selectedWarungs.add(select.value); });

            allSelects.forEach(currentSelect => {
                const currentValue = currentSelect.value;
                [...currentSelect.options].forEach(option => {
                    if (option.value === "") { option.disabled = true; }
                    else if (option.value === currentValue) {
                        option.disabled = false;
                        option.classList.remove('disabled-option');
                    } else {
                        const isDisabled = selectedWarungs.has(option.value);
                        option.disabled = isDisabled;
                        option.classList.toggle('disabled-option', isDisabled);
                    }
                });
            });
            return selectedWarungs.size;
        }

        function updateSisa(id, shouldRecalculate = true) {
            if (shouldRecalculate) recalculateGlobalStock();

            const max = +document.getElementById(`sisa-${id}`).dataset.max;
            const container = document.getElementById(`deliveries-${id}`);
            const rows = [...container.querySelectorAll('.delivery-row')];
            const chk = document.querySelector(`.chk-trx[data-id="${id}"]`);
            const btnAdd = document.querySelector(`.btn-add[data-id="${id}"]`);

            let totalPengirimanLokal = 0;
            let isValidLokal = true;
            const allSelects = [...container.querySelectorAll('.warung-select:not(:disabled)')];

            const uniqueWarungCount = updateWarungOptions(id);

            rows.forEach(rowEl => {
                const selectEl = rowEl.querySelector('.warung-select');
                const qtyEl = rowEl.querySelector('.qty-input');
                const qtyVal = +qtyEl.value || 0;

                selectEl.classList.remove('border-red-500');
                qtyEl.classList.remove('border-red-500');

                if (!selectEl.disabled) {
                    // Validasi input
                    if (!selectEl.value) {
                        selectEl.classList.add('border-red-500');
                        isValidLokal = false;
                    }
                    if (qtyVal < 1 || isNaN(qtyVal) || qtyVal > max) {
                        qtyEl.classList.add('border-red-500');
                        isValidLokal = false;
                    }
                    totalPengirimanLokal += qtyVal;
                }
            });

            // Validasi total Qty
            if (totalPengirimanLokal > max) isValidLokal = false;

            // Validasi semua row terisi
            if (allSelects.some(s => !s.value)) { isValidLokal = false; }

            // Final check untuk checkbox yang tercentang
            chk.dataset.valid = (chk.checked && isValidLokal && totalPengirimanLokal > 0).toString();

            if (btnAdd) {
                const allWarungsUsed = uniqueWarungCount >= Object.keys(warungs).length;
                const sisaUntukLokal = max - totalPengirimanLokal;
                // Tombol Add: disabled jika semua warung terpakai, stok habis, atau checkbox tidak dicentang
                btnAdd.disabled = sisaUntukLokal <= 0 || allWarungsUsed || !chk.checked;
            }

            updateSubmitMassal();
        }

        function updateSubmitMassal() {
            const checkedTrx = [...document.querySelectorAll('.chk-trx:checked')];
            const canSubmit = checkedTrx.length > 0 && checkedTrx.every(c => c.dataset.valid === "true");
            btnSubmitMassal.disabled = !canSubmit;

            if (checkAll) {
                const allTrx = document.querySelectorAll('.chk-trx');
                checkAll.checked = allTrx.length > 0 && checkedTrx.length === allTrx.length;
            }
        }

        // --- INISIALISASI ---
        document.querySelectorAll('.chk-trx').forEach(chk => {
            chk.checked = false;
        });

        recalculateGlobalStock();

        // --- EVENT LISTENERS (Hanya yang relevan dengan Kolom Kiri) ---

        // 1. Event Change (Checkboxes, Warung Select)
        document.addEventListener('change', e => {
            if (e.target.classList.contains('chk-trx')) {
                toggleInputs(e.target.dataset.id, e.target.checked);
            } else if (e.target.classList.contains('warung-select')) {
                const rowEl = e.target.closest('.delivery-row');
                if (rowEl) updateSisa(rowEl.dataset.trx);
            } else if (e.target.id === 'checkAll') {
                document.querySelectorAll('.chk-trx').forEach(cb => {
                    cb.checked = e.target.checked;
                    toggleInputs(cb.dataset.id, cb.checked);
                });
            }
        });

        // 2. Event Input (Qty Inputs)
        document.addEventListener('input', e => {
            if (e.target.classList.contains('qty-input')) {
                const rowEl = e.target.closest('.delivery-row');
                if (!rowEl) return;
                const max = +document.getElementById(`sisa-${rowEl.dataset.trx}`).dataset.max; // Ambil max dari data atribut
                const inputVal = +e.target.value;

                // Batasi input agar tidak melebihi max dan minimal 1
                if (inputVal > max) e.target.value = max;
                if (inputVal < 1) e.target.value = 1;

                updateSisa(rowEl.dataset.trx);
            }
        });

        // 3. Event Delegasi untuk Tombol Add/Delete
        document.addEventListener('click', e => {
            if (e.target.classList.contains('btn-add')) {
                const id = e.target.dataset.id;
                const max = +document.getElementById(`sisa-${id}`).dataset.max;
                document.getElementById(`deliveries-${id}`).insertAdjacentHTML('beforeend', createDeliveryRow(id, max));
                updateSisa(id);
            }
            if (e.target.classList.contains('btn-del')) {
                const rowEl = e.target.closest('.delivery-row');
                if (!rowEl) return;
                const id = rowEl.dataset.trx;
                rowEl.remove();
                updateSisa(id);
            }
        });

        // 4. Submit form
        formKirim.addEventListener('submit', e => {
            if(btnSubmitMassal.disabled){
                e.preventDefault();
                alert('Terdapat data pengiriman yang tidak valid atau kuantitas melebihi stok yang tersedia.');
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

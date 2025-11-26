@extends('layouts.admin')

@section('title', 'Daftar Transaksi Barang')

@section('content')
<div class="flex-1 flex flex-col overflow-hidden">
    {{-- Header --}}
    <header class="flex justify-between items-center p-6 bg-white border-b-2 border-gray-200 shadow-sm">
        <button id="openSidebarBtn" class="text-gray-500 hover:text-gray-900 lg:hidden">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>
        <h1 class="text-2xl font-bold text-gray-800">Manajemen Transaksi Barang</h1>
        <div class="flex items-center">
            <span class="mr-4 font-semibold hidden sm:inline">Admin User</span>
            <div class="w-10 h-10 bg-blue-500 rounded-full"></div>
        </div>
    </header>

    {{-- Main Content --}}
    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-6 md:p-10">
        <div class="container mx-auto grid grid-cols-1 md:grid-cols-3 gap-8">

            {{-- Kolom Kiri: Daftar Transaksi (2/3) --}}
            <div class="md:col-span-2">

                {{-- Tombol Aksi & Header --}}
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                    <h1 class="text-3xl font-bold text-gray-800 mb-4 md:mb-0">Daftar Pengiriman Barang</h1>
                    <div class="flex space-x-4">
                        @if($status === 'pending')
                        <button type="submit" form="formKirim" id="btnSubmitMassal" disabled
                            class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg shadow transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            Kirim Transaksi Terpilih
                        </button>
                        @endif
                        <a href="{{ route('transaksibarang.create') }}"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow transition-colors duration-200 flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Buat Transaksi Baru
                        </a>
                    </div>
                </div>

                {{-- Nav Tabs --}}
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
                            <a href="{{ route('transaksibarang.index',['status'=>$key]) }}"
                                class="inline-block px-4 py-2 rounded-t-lg font-semibold transition-colors
                                {{ $status === $key ? 'bg-white border border-b-0 border-gray-300 text-blue-600' : 'text-gray-600 hover:text-blue-600' }}">
                                {{ $label }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </nav>

                {{-- Alert --}}
                @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-700 rounded-lg shadow-sm">
                    {{ session('success') }}
                </div>
                @endif

                {{-- Tabel Transaksi --}}
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
                                    </td>

                                    <td class="px-4 py-4 border-b text-sm text-center">
                                        <span class="font-bold text-lg text-blue-600" id="sisa-{{ $trx->id }}" data-max="{{ $trx->jumlah }}">{{ $trx->jumlah }}</span>
                                    </td>

                                    <td class="px-4 py-4 border-b text-sm">
                                        <div id="deliveries-{{ $trx->id }}" data-id="{{ $trx->id }}" class="space-y-2"></div>
                                        <input type="hidden" name="transaksi[{{ $trx->id }}][barang_id]" value="{{ $trx->barang_id }}">
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

            {{-- Kolom Kanan: Rencana Belanja (1/3) --}}
            <div class="md:col-span-1 bg-white p-6 rounded-xl shadow-xl h-fit sticky top-6 border border-gray-100">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2m-9 0a2 2 0 002 2h2m-2 0h-2m9 0h2m-2 0a2 2 0 00-2-2h-2" />
                    </svg>
                    Rencana Belanja (Kebutuhan)
                </h2>

                {{-- Toggle View --}}
                <div class="flex justify-around mb-4 space-x-2 p-1 bg-gray-100 rounded-lg">
                    <button type="button" data-view="warung"
                            class="toggle-view flex-1 bg-blue-600 text-white px-3 py-2 rounded-lg text-sm font-semibold transition-colors duration-200 shadow-md">
                        Berdasarkan Warung
                    </button>
                    <button type="button" data-view="barang"
                            class="toggle-view flex-1 bg-transparent text-gray-700 px-3 py-2 rounded-lg text-sm font-semibold transition-colors duration-200">
                        Berdasarkan Barang
                    </button>
                </div>

                {{-- Search --}}
                <input type="text" id="searchRencana" class="w-full mb-4 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Cari Warung atau Barang...">

                {{-- Container Rencana Belanja --}}
                <div id="rencanaContainer" class="space-y-4 max-h-[70vh] overflow-y-auto pr-2">
                    {{-- Warung View (Default) --}}
                    <div id="viewWarung" class="view-content space-y-3">
                        @forelse($rencanaBelanjaByWarung as $warung => $items)
                        <div class="p-4 rounded-lg bg-indigo-50 border border-indigo-200 item-block shadow-sm" data-nama="{{ $warung }}">
                            <h3 class="font-bold text-indigo-700 text-base mb-1">{{ $warung }}</h3>
                            <ul class="list-disc list-inside text-sm text-gray-700 space-y-0.5 ml-2">
                                @foreach($items as $i)
                                <li data-sub="{{ $i->barang->nama_barang }}">
                                    {{ $i->barang->nama_barang }}: <span class="font-semibold">{{ $i->jumlah_awal - $i->jumlah_dibeli }} pcs</span>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @empty
                        <p class="text-center text-gray-500 py-4">Tidak ada rencana belanja yang tertunda.</p>
                        @endforelse
                    </div>

                    {{-- Barang View --}}
                    <div id="viewBarang" class="view-content space-y-3 hidden">
                        <div id="totalKebutuhan" class="p-4 rounded-lg bg-yellow-100 border border-yellow-200 mb-4 shadow-sm">
                            <h3 class="font-bold text-yellow-800 text-base mb-2 border-b border-yellow-300 pb-1">Total Kebutuhan Barang</h3>
                            <ul class="text-sm space-y-1">
                                @forelse($rencanaBelanjaTotalByBarang ?? [] as $barang=>$total)
                                <li data-sub="{{ $barang }}" class="flex justify-between items-center text-yellow-900">
                                    <span>{{ $barang }}</span>
                                    <span class="font-bold">{{ $total }} pcs</span>
                                </li>
                                @empty
                                <li class="text-gray-500">Tidak ada total kebutuhan.</li>
                                @endforelse
                            </ul>
                        </div>
                        @forelse($rencanaBelanjaByBarang as $barang=>$items)
                        <div class="p-4 rounded-lg bg-green-50 border border-green-200 item-block shadow-sm" data-nama="{{ $barang }}">
                            <h3 class="font-bold text-green-700 text-base mb-1">{{ $barang }}</h3>
                            <ul class="list-disc list-inside text-sm text-gray-700 space-y-0.5 ml-2">
                                @foreach($items as $i)
                                <li data-sub="{{ $i->warung->nama_warung }}">
                                    {{ $i->warung->nama_warung }}: <span class="font-semibold">{{ $i->jumlah_awal - $i->jumlah_dibeli }} pcs</span>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @empty
                        {{-- Dikesampingkan, karena sudah ditangani oleh totalKebutuhan --}}
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </main>
</div>

<style>
    .border-red-500 {
        border-color: #ef4444 !important;
        box-shadow: 0 0 0 1px #ef4444 !important;
    }
    .text-red-600-strong {
        color: #dc2626 !important;
    }
    .disabled-option {
        color: #9ca3af; /* gray-400 */
        font-style: italic;
    }
</style>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const isPending = "{{ $status }}" === 'pending';
    if (!isPending) return;

    const warungs = @json($warungs->pluck('nama_warung','id'));
    const totalWarungs = Object.keys(warungs).length;
    const form = document.getElementById('formKirim');
    const btnSubmit = document.getElementById('btnSubmitMassal');
    const checkAll = document.getElementById('checkAll');

    // Opsi Warung untuk Select
    const warungOptions = Object.entries(warungs).map(([id, nm]) => `<option value="${id}">${nm}</option>`).join('');
    const baseOptions = `<option value="" disabled selected>Pilih Warung</option>${warungOptions}`;

    // 1. Fungsi untuk membuat baris pengiriman
    function createDeliveryRow(trxId, maxQty, isFirst = false) {
        const sisaSaatIni = getRemainingQty(trxId);
        let defaultQty = isFirst ? maxQty : (sisaSaatIni > 0 ? sisaSaatIni : 1);
        if (defaultQty > maxQty) defaultQty = maxQty;
        if (defaultQty < 1) defaultQty = 1;

        const rowId = `row-${trxId}-${Date.now()}-${Math.floor(Math.random() * 1000)}`;

        return `
            <div class="flex space-x-2 items-center delivery-row" data-trx="${trxId}" data-row-id="${rowId}">
                <select name="transaksi[${trxId}][details][${rowId}][warung_id]" required class="warung-select border border-gray-300 rounded px-2 py-1 text-sm flex-1 focus:ring-blue-500 focus:border-blue-500 bg-white transition duration-150">
                    ${baseOptions}
                </select>
                <input type="number" name="transaksi[${trxId}][details][${rowId}][jumlah]" value="${defaultQty}" min="1" max="${maxQty}" required class="qty-input border border-gray-300 rounded px-2 py-1 text-sm w-16 text-center focus:ring-blue-500 focus:border-blue-500 transition duration-150"/>
                <button type="button" class="btn-del text-red-500 hover:text-red-700 text-xs font-semibold w-10 py-1 transition duration-150 rounded" title="Hapus baris" ${isFirst ? 'style="visibility:hidden;"' : ''}>Hapus</button>
            </div>
        `;
    }

    // 2. Mendapatkan sisa kuantitas
    function getRemainingQty(trxId) {
        const remainingEl = document.getElementById(`sisa-${trxId}`);
        return remainingEl ? parseInt(remainingEl.textContent) || 0 : 0;
    }

    // 3. Mengaktifkan/menonaktifkan input
    function toggleInputs(id, enable) {
        const container = document.getElementById(`deliveries-${id}`);
        const chk = document.querySelector(`.chk-trx[data-id="${id}"]`);

        container.querySelectorAll('select, input, button').forEach(el => {
            if (el.classList.contains('btn-del') && el.style.visibility === 'hidden') return;
            el.disabled = !enable;
        });

        // Hapus input name jika disable untuk memastikan data tidak terkirim
        container.querySelectorAll('input, select').forEach(input => {
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

        // Panggil updateSisa untuk mengupdate status tombol tambah dan opsi warung
        updateSisa(id);
    }

    // 4. Update Opsi Warung: Disable warung yang sudah terpilih
    function updateWarungOptions(trxId) {
        const container = document.getElementById(`deliveries-${trxId}`);
        const allSelects = [...container.querySelectorAll('.warung-select:not(:disabled)')];
        const selectedWarungs = new Set();

        // 1. Kumpulkan Warung yang sudah terpilih
        allSelects.forEach(select => {
            if (select.value) {
                selectedWarungs.add(select.value);
            }
        });

        // 2. Iterasi setiap select untuk memperbarui opsi
        allSelects.forEach(currentSelect => {
            const currentValue = currentSelect.value;

            [...currentSelect.options].forEach(option => {
                if (option.value === "") { // Opsi "Pilih Warung"
                    option.disabled = true;
                } else if (option.value === currentValue) {
                    option.disabled = false; // Biarkan opsi yang sedang terpilih aktif
                    option.classList.remove('disabled-option');
                } else {
                    // Disable jika warung sudah terpilih di baris lain
                    const isDisabled = selectedWarungs.has(option.value);
                    option.disabled = isDisabled;
                    option.classList.toggle('disabled-option', isDisabled);
                }
            });
        });

        return selectedWarungs.size; // Mengembalikan jumlah warung unik yang terpilih
    }

    // 5. Update validasi, sisa, dan status tombol
    function updateSisa(id) {
        const max = +document.getElementById(`sisa-${id}`).dataset.max;
        const container = document.getElementById(`deliveries-${id}`);
        const rows = [...container.querySelectorAll('.delivery-row')];
        const chk = document.querySelector(`.chk-trx[data-id="${id}"]`);
        const btnAdd = document.querySelector(`.btn-add[data-id="${id}"]`);

        let total = 0;
        let isValid = true;
        const allSelects = [...container.querySelectorAll('.warung-select:not(:disabled)')];

        // 1. Update Warung Options
        const uniqueWarungCount = updateWarungOptions(id);

        // 2. Validasi Qty dan Hitung Total
        rows.forEach(rowEl => {
            const selectEl = rowEl.querySelector('.warung-select');
            const qtyEl = rowEl.querySelector('.qty-input');
            const qtyVal = +qtyEl.value || 0;

            selectEl.classList.remove('border-red-500');
            qtyEl.classList.remove('border-red-500');

            if (!selectEl.disabled) {
                // VALIDASI 1: Warung Belum Dipilih ATAU Duplikat
                // Check if Warung is empty, OR if this Warung is selected more than once (only happens before updateWarungOptions is called after input)
                if (!selectEl.value) {
                    selectEl.classList.add('border-red-500');
                    isValid = false;
                }
                // Kita tidak perlu cek duplikasi di sini lagi karena updateWarungOptions sudah men-disable,
                // tapi kita tambahkan validasi untuk kuantitas.

                // VALIDASI 2: Kuantitas
                if (qtyVal < 1 || qtyVal > max || isNaN(qtyVal)) {
                    qtyEl.classList.add('border-red-500');
                    isValid = false;
                }

                total += qtyVal;
            }
        });

        // Periksa kembali jika ada warung kosong
        if (allSelects.some(s => !s.value)) {
             isValid = false;
        }

        // 3. Update Sisa
        const sisa = max - total;
        document.getElementById(`sisa-${id}`).textContent = sisa;
        document.getElementById(`sisa-${id}`).classList.toggle('text-red-600-strong', sisa < 0);
        document.getElementById(`sisa-${id}`).classList.toggle('text-blue-600', sisa >= 0);

        if (sisa < 0) isValid = false;

        // 4. Update status validasi pada checkbox
        // HANYA valid jika dicentang dan semua input valid
        chk.dataset.valid = (chk.checked && isValid && total > 0).toString();

        // 5. Update status tombol tambah warung (+ Warung Lain)
        if (btnAdd) {
            // Tombol tambah tetap aktif jika:
            // a) Warung unik yang tersedia belum habis (uniqueWarungCount < totalWarungs)
            // b) Ada sisa kuantitas (sisa > 0)
            const allWarungsUsed = uniqueWarungCount >= totalWarungs;
            // Kita tidak menyertakan `isValid` di sini agar tombol add tetap bisa diklik
            // untuk menambahkan baris baru meski ada baris lain yang belum selesai diisi.
            btnAdd.disabled = sisa <= 0 || allWarungsUsed || !chk.checked;
        }

        updateSubmit();
    }

    // 6. Update tombol Kirim Transaksi Terpilih
    function updateSubmit() {
        const checkedTrx = [...document.querySelectorAll('.chk-trx:checked')];
        // Submit hanya bisa jika ada yang dicentang DAN semua yang dicentang valid
        const canSubmit = checkedTrx.length > 0 && checkedTrx.every(c => c.dataset.valid === "true");
        btnSubmit.disabled = !canSubmit;

        if (checkAll) {
            const allTrx = document.querySelectorAll('.chk-trx');
            checkAll.checked = allTrx.length > 0 && checkedTrx.length === allTrx.length;
        }
    }

    // --- Inisialisasi ---
    document.querySelectorAll('[id^="deliveries-"]').forEach(c => {
        const id = c.dataset.id;
        const max = +document.getElementById(`sisa-${id}`).dataset.max;
        c.innerHTML = createDeliveryRow(id, max, true);
        toggleInputs(id, false);
    });

    // --- Event Listeners ---

    // Event Delegasi untuk Tombol Add/Delete
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

    // Event Input Qty/Select Warung
    document.addEventListener('input', e => {
        if (e.target.classList.contains('qty-input')) {
            const rowEl = e.target.closest('.delivery-row');
            if (!rowEl) return;
            const id = rowEl.dataset.trx;
            const max = +e.target.max;
            if (+e.target.value > max) e.target.value = max;
            updateSisa(id);
        }
    });

    document.addEventListener('change', e => {
        // Perubahan Warung Select
        if (e.target.classList.contains('warung-select')) {
            const rowEl = e.target.closest('.delivery-row');
            if (!rowEl) return;
            updateSisa(rowEl.dataset.trx);
        }

        // Perubahan Checkbox Transaksi
        if (e.target.classList.contains('chk-trx')) {
            const id = e.target.dataset.id;
            toggleInputs(id, e.target.checked);
            updateSisa(id);
        }

        // Perubahan Checkbox Semua
        if (e.target.id === 'checkAll') {
            document.querySelectorAll('.chk-trx').forEach(cb => {
                cb.checked = e.target.checked;
                toggleInputs(cb.dataset.id, cb.checked);
                updateSisa(cb.dataset.id);
            });
        }
    });

    // Event Submit Form: hapus input dari transaksi yang tidak dicentang atau tidak valid
    form.addEventListener('submit', e => {
        // Logika ini sudah memadai karena `toggleInputs` menangani penghapusan `name` attribute
    });

    // --- Logika Rencana Belanja (tidak berubah) ---
    const btnViews = document.querySelectorAll('.toggle-view');
    const viewW = document.getElementById('viewWarung'), viewB = document.getElementById('viewBarang');
    const search = document.getElementById('searchRencana');

    function toggleView(btn) {
        const view = btn.dataset.view;
        viewW.classList.toggle('hidden', view !== 'warung');
        viewB.classList.toggle('hidden', view !== 'barang');

        btnViews.forEach(x => {
            x.classList.toggle('bg-blue-600', x === btn);
            x.classList.toggle('text-white', x === btn);
            x.classList.toggle('bg-transparent', x !== btn);
            x.classList.toggle('text-gray-700', x !== btn);
        });

        search.placeholder = view === 'warung' ? "Cari Warung atau Barang..." : "Cari Barang atau Warung...";
        filterRencana();
    }

    function filterRencana() {
        const q = search.value.toLowerCase();
        const activeView = viewW.classList.contains('hidden') ? viewB : viewW;
        const blocks = activeView.querySelectorAll('.item-block');
        const totalKebutuhan = document.getElementById('totalKebutuhan');

        if (totalKebutuhan) totalKebutuhan.classList.toggle('hidden', q.length > 0 && activeView === viewB);

        blocks.forEach(block => {
            const mainName = block.dataset.nama.toLowerCase();
            const subs = [...block.querySelectorAll('li')].map(li => li.dataset.sub.toLowerCase());
            const match = mainName.includes(q) || subs.some(s => s.includes(q));
            block.classList.toggle('hidden', !match);
        });
    }

    btnViews.forEach(b => b.addEventListener('click', () => toggleView(b)));
    search.addEventListener('input', filterRencana);

    toggleView(btnViews[0]);
});
</script>
@endsection

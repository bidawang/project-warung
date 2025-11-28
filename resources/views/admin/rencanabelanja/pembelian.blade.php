@extends('layouts.admin')

@section('title', 'Buat Transaksi Pembelian dari Kebutuhan Rencana')

@section('content')
<div class="flex-1 flex flex-col overflow-hidden">
    {{-- Header --}}
    <header class="flex justify-between items-center p-6 bg-white border-b-2 border-gray-200 shadow-sm">
        <h1 class="text-2xl font-bold text-gray-800">Pembelian Berdasarkan Rencana</h1>
        <a href="{{ route('admin.rencana.index') }}"
            class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg shadow transition-colors duration-200 text-sm flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            Kembali ke Rencana
        </a>
    </header>

    {{-- Error/Success Message --}}
    @if ($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded m-6">
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
            <div class="bg-white p-6 rounded-xl shadow-xl border border-gray-100">

                @if($totalKebutuhan->isEmpty())
                    <div class="text-center py-10">
                        <p class="text-xl text-gray-500">🎉 Tidak ada kebutuhan Rencana Belanja yang tertunda saat ini.</p>
                        <p class="text-gray-400 mt-2">Anda dapat membuat Transaksi Pembelian manual jika diperlukan.</p>
                    </div>
                @else
                    <form action="{{ route('admin.rencana.store') }}" method="POST" id="pembelianForm">
                        @csrf
                        
                        {{-- KETERANGAN GLOBAL DIHAPUS DARI BLADE --}}

                        <div class="flex justify-between items-center mb-6 border-b pb-4">
                            <h2 class="text-xl font-bold text-gray-800">Detail Pembelian Barang ({{ $totalKebutuhan->count() }} Jenis)</h2>
                            <button type="submit" id="btnSubmitPembelian"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-lg shadow-md transition-colors duration-200 text-base disabled:bg-indigo-400 disabled:cursor-not-allowed">
                                Proses Pembelian & Update Rencana
                            </button>
                        </div>

                        <div class="overflow-x-auto rounded-lg shadow-sm border border-gray-200">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider w-1/4 border-r">Barang</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-600 uppercase tracking-wider w-24">Total Kebutuhan</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider w-1/4">Area Pembelian</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-600 uppercase tracking-wider w-20">Jml. Beli</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-600 uppercase tracking-wider w-24">Harga Beli (@)</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-600 uppercase tracking-wider w-24">Tanggal Kadaluarsa</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-600 uppercase tracking-wider w-16">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100" id="purchaseTableBody">
                                    @foreach($totalKebutuhan as $index => $item)
                                    @php
                                        $groupIndex = $index;
                                        $initialPurchaseIndex = 0;
                                        $inputName = "items[{$groupIndex}][purchases][{$initialPurchaseIndex}]";
                                        $isAutoSelected = count($item['valid_areas']) === 1;
                                    @endphp

                                    {{-- Baris Header Barang (Group) --}}
                                    <tr data-group-index="{{ $groupIndex }}" class="bg-indigo-50 hover:bg-indigo-100 transition duration-150">
                                        <td colspan="7" class="p-0 border-t border-indigo-200">
                                            <input type="hidden" name="items[{{ $groupIndex }}][id_barang]" value="{{ $item['id_barang'] }}">
                                            <input type="hidden" name="items[{{ $groupIndex }}][total_kebutuhan]" value="{{ $item['total_kebutuhan'] }}" id="total_kebutuhan_{{ $groupIndex }}">
                                            <input type="hidden" name="items[{{ $groupIndex }}][rencana_ids]" value="{{ implode(',', $item['rencana_ids']) }}">

                                            <div class="px-4 py-2 text-sm font-bold text-indigo-700">
                                                {{ $item['nama_barang'] }}
                                                <div class="mt-1 text-xs text-indigo-500 font-normal italic leading-tight">
                                                    Kebutuhan Warung:
                                                    @foreach($item['detail_warung'] as $detail)
                                                        <span class="inline-block mr-3">
                                                            <span class="font-semibold">{{ $detail['warung'] }}</span>: {{ $detail['kebutuhan'] }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                    {{-- Baris Pertama Pembelian (Purchase Row) --}}
                                    <tr class="purchase-row" data-group-index="{{ $groupIndex }}" data-purchase-index="{{ $initialPurchaseIndex }}">
                                        {{-- Kolom Kiri Kosong --}}
                                        <td class="px-4 py-2 text-sm text-gray-500 border-r border-gray-200"></td>

                                        {{-- Total Kebutuhan (ReadOnly - Rowspan) --}}
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-center font-extrabold total-kebutuhan-cell align-top border-r border-gray-200"
                                            rowspan="1" id="total_qty_cell_{{ $groupIndex }}" data-total-kebutuhan="{{ $item['total_kebutuhan'] }}" data-current-qty="{{ $item['total_kebutuhan'] }}">
                                            {{ $item['total_kebutuhan'] }} pcs
                                        </td>

                                        {{-- Area Pembelian (Dropdown) --}}
                                        <td class="px-4 py-2 whitespace-nowrap text-sm">
                                            <select name="{{ $inputName }}[area_pembelian_id]" required
                                                onchange="handleAreaChange({{ $groupIndex }})"
                                                class="area-select w-full border border-gray-300 rounded-md px-2 py-1 text-xs focus:ring-blue-500 focus:border-blue-500">
                                                <option value="" disabled {{ !$isAutoSelected ? 'selected' : '' }}>Pilih Area...</option>
                                                @foreach($item['valid_areas'] as $area)
                                                    <option value="{{ $area->id }}"
                                                        {{ old($inputName . '.area_pembelian_id') == $area->id ? 'selected' : ($isAutoSelected ? 'selected' : '') }}>
                                                        {{ $area->area }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>

                                        {{-- Jumlah Beli (Input) --}}
                                        <td class="px-4 py-2 whitespace-nowrap text-sm">
                                            <input type="number" name="{{ $inputName }}[jumlah_beli]" required min="1"
                                                value="{{ old($inputName . '.jumlah_beli', $item['total_kebutuhan']) }}"
                                                class="jumlah-beli-input w-full border border-gray-300 rounded-md px-2 py-1 text-sm text-center focus:ring-blue-500 focus:border-blue-500 transition duration-150"
                                                oninput="updateGroupStatus({{ $groupIndex }})">
                                        </td>

                                        {{-- Harga Beli (Input) --}}
                                        <td class="px-4 py-2 whitespace-nowrap text-sm">
                                            <input type="number" name="{{ $inputName }}[harga]" min="0" step="1" required
                                                value="{{ old($inputName . '.harga', 0) }}"
                                                class="w-full border border-gray-300 rounded-md px-2 py-1 text-sm text-right focus:ring-blue-500 focus:border-blue-500 transition duration-150">
                                        </td>
                                        
                                        {{-- Tanggal Kadaluarsa (Input) --}}
                                        <td class="px-4 py-2 whitespace-nowrap text-sm">
                                            <input type="date" name="{{ $inputName }}[tanggal_kadaluarsa]"
                                                value="{{ old($inputName . '.tanggal_kadaluarsa') }}"
                                                class="w-full border border-gray-300 rounded-md px-2 py-1 text-sm text-center focus:ring-blue-500 focus:border-blue-500 transition duration-150">
                                        </td>

                                        {{-- Aksi --}}
                                        <td class="px-4 py-2 text-center whitespace-nowrap text-sm flex items-center justify-center space-x-1">
                                            <button type="button" onclick="removePurchaseRow(this, {{ $groupIndex }})"
                                                class="text-red-500 hover:text-red-700 p-1 rounded-full transition duration-150 hover:bg-red-50" title="Hapus Baris Pembelian">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            </button>
                                            <button type="button" onclick="addPurchaseRow({{ $groupIndex }})"
                                                class="add-row-button text-green-600 hover:text-green-800 p-1 rounded-full transition duration-150 hover:bg-green-50" title="Tambah Baris Pembelian">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </main>
</div>

<script>
    // Menyimpan indeks pembelian berikutnya untuk setiap grup
    const nextPurchaseIndex = {};

    // Simpan data area pembelian di scope global JS (dari controller)
    const itemValidAreas = {!! $totalKebutuhan->keyBy('id_barang')->map(fn($item) => $item['valid_areas'])->toJson() !!};

    // Inisialisasi indeks untuk setiap grup
    @foreach($totalKebutuhan as $index => $item)
        nextPurchaseIndex[{{ $index }}] = 1; // Mulai dari 1 karena 0 sudah dipakai
    @endforeach

    // =========================================================================
    // LOGIKA AREA PEMBELIAN
    // =========================================================================

    /**
     * Mendapatkan daftar ID Area yang sudah dipilih untuk grup tertentu.
     */
    function getUsedAreas(groupIndex) {
        const usedAreaIds = new Set();
        document.querySelectorAll(`tr.purchase-row[data-group-index="${groupIndex}"] .area-select`).forEach(select => {
            if (select.value !== "") {
                usedAreaIds.add(select.value);
            }
        });
        return usedAreaIds;
    }

    /**
     * Memperbarui opsi di semua dropdown Area Pembelian untuk grup.
     */
    function updateAreaOptions(groupIndex) {
        const usedAreaIds = getUsedAreas(groupIndex);

        // Ambil ID Barang
        const idBarang = document.querySelector(`tr[data-group-index="${groupIndex}"].bg-indigo-50 input[name="items[${groupIndex}][id_barang]"]`).value;
        const validAreas = itemValidAreas[idBarang] || [];

        // Perbarui semua select dropdown di grup ini
        document.querySelectorAll(`tr.purchase-row[data-group-index="${groupIndex}"] .area-select`).forEach(currentSelect => {
            const currentValue = currentSelect.value;
            currentSelect.innerHTML = '<option value="" disabled>Pilih Area...</option>';
            
            let hasSelectedValue = false;
            let currentOptionsCount = 0;

            validAreas.forEach(area => {
                const areaIdString = String(area.id);
                const isSelected = (currentValue == areaIdString) ? 'selected' : '';

                if (currentValue == areaIdString || !usedAreaIds.has(areaIdString)) {
                    currentSelect.insertAdjacentHTML('beforeend',
                        `<option value="${areaIdString}" ${isSelected}>${area.area}</option>`
                    );
                    currentOptionsCount++;
                    if (isSelected) hasSelectedValue = true;
                }
            });

            if (hasSelectedValue) {
                currentSelect.value = currentValue;
            } else if (currentOptionsCount === 1) {
                currentSelect.querySelector('option:not([disabled])').selected = true;
            } else {
                currentSelect.value = "";
            }
        });

        updateAddButtonState(groupIndex);
        updateGroupStatus(groupIndex); // Panggil status setelah area diperbarui
        checkFormValidity(); // Panggil cek validitas form global
    }

    /**
     * Menangani perubahan di dropdown Area Pembelian.
     */
    function handleAreaChange(groupIndex) {
        updateAreaOptions(groupIndex);
    }

    /**
     * Memperbarui status tombol "Tambah Baris Pembelian".
     */
    function updateAddButtonState(groupIndex) {
        const usedAreaIds = getUsedAreas(groupIndex);
        const idBarang = document.querySelector(`tr[data-group-index="${groupIndex}"].bg-indigo-50 input[name="items[${groupIndex}][id_barang]"]`).value;
        const validAreas = itemValidAreas[idBarang] || [];

        const isAllUsed = usedAreaIds.size >= validAreas.length;

        document.querySelectorAll(`tr.purchase-row[data-group-index="${groupIndex}"] .add-row-button`).forEach(button => {
            button.disabled = isAllUsed;
            button.classList.toggle('opacity-50', isAllUsed);
            button.classList.toggle('cursor-not-allowed', isAllUsed);
            button.classList.toggle('hover:bg-green-50', !isAllUsed);
        });
    }

    // =========================================================================
    // LOGIKA BARIS PEMBELIAN (UTAMA)
    // =========================================================================

    /**
     * Menambahkan baris pembelian baru untuk barang tertentu.
     */
    function addPurchaseRow(groupIndex) {
        const tableBody = document.getElementById('purchaseTableBody');
        const nextIndex = nextPurchaseIndex[groupIndex]++;
        const totalKebutuhanCell = document.getElementById(`total_kebutuhan_${groupIndex}`);
        const totalQty = parseInt(totalKebutuhanCell.value);

        const idBarang = document.querySelector(`tr[data-group-index="${groupIndex}"].bg-indigo-50 input[name="items[${groupIndex}][id_barang]"]`).value;
        const usedAreaIds = getUsedAreas(groupIndex);

        let areaOptions = '';
        const validAreas = itemValidAreas[idBarang] || [];
        const remainingAreas = validAreas.filter(area => !usedAreaIds.has(String(area.id)));

        if (remainingAreas.length === 0) {
            alert("Semua Area Pembelian untuk barang ini sudah digunakan.");
            nextPurchaseIndex[groupIndex]--;
            return;
        }

        let autoSelectedAreaId = '';
        if (remainingAreas.length === 1) {
            autoSelectedAreaId = String(remainingAreas[0].id);
        }

        areaOptions += `<option value="" disabled ${!autoSelectedAreaId ? 'selected' : ''}>Pilih Area...</option>`;

        remainingAreas.forEach(area => {
            areaOptions += `<option value="${area.id}" ${autoSelectedAreaId === String(area.id) ? 'selected' : ''}>${area.area}</option>`;
        });


        const newRowHtml = `
            <tr class="purchase-row hover:bg-gray-50 transition duration-150" data-group-index="${groupIndex}" data-purchase-index="${nextIndex}">
                <td class="px-4 py-2 text-sm text-gray-500 border-r border-gray-200"></td>
                <td class="hidden total-kebutuhan-cell-spacer"></td>

                {{-- Area Pembelian --}}
                <td class="px-4 py-2 whitespace-nowrap text-sm">
                    <select name="items[${groupIndex}][purchases][${nextIndex}][area_pembelian_id]" required
                        onchange="handleAreaChange(${groupIndex})"
                        class="area-select w-full border border-gray-300 rounded-md px-2 py-1 text-xs focus:ring-blue-500 focus:border-blue-500">
                        ${areaOptions}
                    </select>
                </td>

                {{-- Jumlah Beli --}}
                <td class="px-4 py-2 whitespace-nowrap text-sm">
                    <input type="number" name="items[${groupIndex}][purchases][${nextIndex}][jumlah_beli]" required min="1"
                        value="1" 
                        class="jumlah-beli-input w-full border border-gray-300 rounded-md px-2 py-1 text-sm text-center focus:ring-blue-500 focus:border-blue-500"
                        oninput="updateGroupStatus(${groupIndex})">
                </td>

                {{-- Harga Beli --}}
                <td class="px-4 py-2 whitespace-nowrap text-sm">
                    <input type="number" name="items[${groupIndex}][purchases][${nextIndex}][harga]" min="0" step="1" required
                        value="0" class="w-full border border-gray-300 rounded-md px-2 py-1 text-sm text-right focus:ring-blue-500 focus:border-blue-500">
                </td>
                
                {{-- Tanggal Kadaluarsa --}}
                <td class="px-4 py-2 whitespace-nowrap text-sm">
                    <input type="date" name="items[${groupIndex}][purchases][${nextIndex}][tanggal_kadaluarsa]"
                        class="w-full border border-gray-300 rounded-md px-2 py-1 text-sm text-center focus:ring-blue-500 focus:border-blue-500">
                </td>

                {{-- Aksi --}}
                <td class="px-4 py-2 text-center whitespace-nowrap text-sm flex items-center justify-center space-x-1">
                    <button type="button" onclick="removePurchaseRow(this, ${groupIndex})" class="text-red-500 hover:text-red-700 p-1 rounded-full transition duration-150 hover:bg-red-50" title="Hapus Baris Pembelian">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </button>
                    <button type="button" onclick="addPurchaseRow(${groupIndex})" class="add-row-button text-green-600 hover:text-green-800 p-1 rounded-full transition duration-150 hover:bg-green-50" title="Tambah Baris Pembelian">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </button>
                </td>
            </tr>
        `;

        const lastRow = tableBody.querySelector(`tr.purchase-row[data-group-index="${groupIndex}"]:last-of-type`);
        if (lastRow) {
            lastRow.insertAdjacentHTML('afterend', newRowHtml);
        } else {
            const headerRow = tableBody.querySelector(`tr[data-group-index="${groupIndex}"].bg-indigo-50`);
            if (headerRow) headerRow.insertAdjacentHTML('afterend', newRowHtml);
        }

        updateRowspan(groupIndex);
        updateAreaOptions(groupIndex);
    }

    /**
     * Menghapus baris pembelian (purchase row).
     */
    function removePurchaseRow(button, groupIndex) {
        const row = button.closest('.purchase-row');
        const rowsInGroup = document.querySelectorAll(`tr.purchase-row[data-group-index="${groupIndex}"]`);

        if (rowsInGroup.length > 1) {
            if (row) {
                row.remove();
                updateRowspan(groupIndex);
                updateAreaOptions(groupIndex);
                updateGroupStatus(groupIndex);
            }
        } else {
            alert("Minimal harus ada satu baris Pembelian untuk setiap Barang.");
        }
    }

    /**
     * Mengupdate rowspan untuk kolom Total Kebutuhan.
     */
    function updateRowspan(groupIndex) {
        const rows = document.querySelectorAll(`tr.purchase-row[data-group-index="${groupIndex}"]`);
        const totalQtyCell = document.getElementById(`total_qty_cell_${groupIndex}`);

        if (totalQtyCell) {
            rows.forEach((row, index) => {
                const spacerCell = row.querySelector('.total-kebutuhan-cell-spacer');
                if (spacerCell) spacerCell.remove();

                if (index === 0) {
                    totalQtyCell.classList.remove('hidden');
                    totalQtyCell.rowSpan = rows.length;
                    if (totalQtyCell.parentNode !== row) {
                        row.children[0].insertAdjacentElement('afterend', totalQtyCell);
                    }
                } else {
                    if (row.children.length < 7) { // 7 adalah jumlah kolom
                        const newSpacer = document.createElement('td');
                        newSpacer.classList.add('hidden', 'total-kebutuhan-cell-spacer');
                        if (row.children[0]) {
                            row.children[0].insertAdjacentElement('afterend', newSpacer);
                        }
                    }
                }
            });
        }
    }

    /**
     * Memperbarui status grup berdasarkan total jumlah beli (warna indikator).
     */
    function updateGroupStatus(groupIndex) {
        const totalKebutuhan = parseInt(document.getElementById(`total_kebutuhan_${groupIndex}`).value);
        const purchaseInputs = document.querySelectorAll(`tr.purchase-row[data-group-index="${groupIndex}"] .jumlah-beli-input`);
        const totalQtyCell = document.getElementById(`total_qty_cell_${groupIndex}`);
        const headerDiv = document.querySelector(`tr[data-group-index="${groupIndex}"].bg-indigo-50 div`);

        let currentTotal = 0;
        purchaseInputs.forEach(input => {
            let val = parseInt(input.value) || 0;
            if (val < 1) val = 1; // Pastikan minimal 1
            input.value = val;
            currentTotal += val;
        });
        
        // Simpan total terbaru di data attribute
        totalQtyCell.setAttribute('data-current-qty', currentTotal); 

        // Hapus semua kelas warna
        totalQtyCell.classList.remove('text-red-600', 'text-red-800', 'text-yellow-600', 'text-green-600', 'bg-red-100/50', 'border-red-500', 'bg-yellow-100/50', 'border-yellow-500');
        headerDiv.classList.remove('text-red-700', 'text-yellow-700', 'text-green-700');
        headerDiv.classList.add('text-indigo-700'); // Default

        if (currentTotal > totalKebutuhan) {
            // Indikator Merah: Beli BERLEBIH
            totalQtyCell.classList.add('text-red-800', 'bg-red-100/50', 'border-red-500');
            totalQtyCell.innerHTML = `${currentTotal} pcs <span class="text-xs text-red-500 block font-normal">(Berlebih: ${currentTotal - totalKebutuhan})</span>`;
        } else if (currentTotal < totalKebutuhan) {
            // Indikator Kuning: Beli KURANG
            totalQtyCell.classList.add('text-yellow-600', 'bg-yellow-100/50', 'border-yellow-500');
            totalQtyCell.innerHTML = `${currentTotal} pcs <span class="text-xs text-yellow-500 block font-normal">(Kurang: ${totalKebutuhan - currentTotal})</span>`;
        } else {
            // Indikator Hijau: PAS
            totalQtyCell.classList.add('text-green-600');
            totalQtyCell.innerHTML = `${currentTotal} pcs <span class="text-xs text-green-500 block font-normal">(PAS)</span>`;
        }

        checkFormValidity();
    }
    
    /**
     * Memeriksa validitas form global (hanya cek Area Pembelian sudah dipilih atau belum).
     */
    function checkFormValidity() {
        let allValid = true;
        const submitButton = document.getElementById('btnSubmitPembelian');

        document.querySelectorAll('tr.purchase-row .area-select').forEach(select => {
            if (!select.value) { 
                allValid = false;
                select.classList.add('border-red-500', 'bg-red-50');
            } else {
                select.classList.remove('border-red-500', 'bg-red-50');
            }
        });
        
        submitButton.disabled = !allValid;
    }


    document.addEventListener('DOMContentLoaded', () => {
        // Event listener for quantity change (delegation for dynamic inputs)
        document.getElementById('purchaseTableBody').addEventListener('input', function(event) {
            if (event.target.classList.contains('jumlah-beli-input')) {
                const groupIndex = event.target.closest('tr.purchase-row').getAttribute('data-group-index');
                updateGroupStatus(groupIndex);
            }
        });

        // Event listener for Area change (re-run global validation)
        document.getElementById('purchaseTableBody').addEventListener('change', function(event) {
            if (event.target.classList.contains('area-select')) {
                checkFormValidity();
            }
        });
        
        // Initializer
        document.querySelectorAll('#purchaseTableBody > tr.bg-indigo-50').forEach(header => {
            const groupIndex = header.getAttribute('data-group-index');
            updateRowspan(groupIndex);
            updateAreaOptions(groupIndex); 
            updateGroupStatus(groupIndex);
        });
        
        checkFormValidity();
    });
</script>
@endsection
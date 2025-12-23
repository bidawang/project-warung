@extends('layouts.admin')
@section('title','Buat Transaksi Pembelian dari Kebutuhan Rencana')
@section('content')

<div class="flex-1 flex flex-col overflow-hidden">

<header class="flex justify-between items-center p-4 bg-white border-b border-gray-300 shadow-sm">
    <h1 class="text-xl font-bold text-gray-800">🛒 Pembelian Berdasarkan Rencana</h1>
    <a href="{{ route('admin.rencana.index') }}" class="bg-gray-600 text-white px-3 py-1 text-sm rounded hover:bg-gray-700 transition duration-150 shadow-md">
        <i class="fas fa-arrow-left mr-1"></i> Kembali
    </a>
</header>

<main class="p-4 overflow-y-auto bg-gray-50">

@if($errors->any())
    <div class="bg-red-100 text-red-700 p-3 rounded mb-3 border border-red-300 text-sm">
        <strong>Kesalahan Validasi:</strong>
        <ul class="list-disc ml-5 mt-1">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
@endif

@if($totalKebutuhan->isEmpty())
    <div class="text-center py-8 text-gray-500 border border-gray-300 rounded-lg bg-white shadow-inner">Tidak ada kebutuhan Rencana Pembelian yang perlu diproses.</div>
@else

<form action="{{ route('admin.rencana.store') }}" method="POST" id="pembelianForm">
@csrf

<div class="shadow overflow-hidden border border-gray-200 sm:rounded-lg mb-4">
<table class="min-w-full divide-y divide-gray-200">
<thead class="bg-gray-100 sticky top-0 z-10">
<tr class="text-xs text-gray-600 uppercase tracking-wider">
    {{-- 6 Kolom Utama dengan penyesuaian lebar --}}
    <th class="p-2 text-left w-[25%]">Item | Skip | Sisa Kebutuhan</th> 
    <th class="p-2 text-left w-[18%]">Area Pembelian</th>
    <th class="p-2 text-center w-[12%]">Qty (pcs)</th>
    <th class="p-2 text-center w-[15%]">Total Harga (Rp)</th>
    <th class="p-2 text-center w-[18%]">Tanggal Exp.</th>
    <th class="p-2 text-center w-[12%]">Aksi</th>
</tr>
</thead>

<tbody id="purchaseTableBody" class="bg-white divide-y divide-gray-200">

@foreach($totalKebutuhan as $g => $item)
@php
  $validAreas = $item['valid_areas'];
  $noArea = $validAreas->isEmpty();
  $singleArea = $validAreas->count() === 1;
  $autoSkip = $noArea; 
  
  $areaOptions = $validAreas->map(function($a) {
    return [
      'id'  => $a->id,
      'area' => $a->area
    ];
  })->toJson();
@endphp

{{-- === HEADER GRUP BARANG (Ringkas: Nama & Kebutuhan Total - colspan=6) === --}}
<tr class="group-header-row bg-white text-gray-800 border-t-4 border-gray-600 {{ $autoSkip ? 'is-skipped' : '' }}" data-group="{{ $g }}">
    <td colspan="6" class="p-1 px-2"> 
        <div class="flex items-center space-x-3">
            <span class="text-gray-900 text-base font-bold">{{ $item['nama_barang'] }}</span>
            <span class="bg-gray-200 text-gray-700 px-2 py-0.5 rounded text-xs font-semibold">
                Kebutuhan Total: {{ $item['total_kebutuhan'] }} pcs
            </span>
        </div>
        {{-- Hidden Input Data --}}
        <input type="hidden" name="items[{{ $g }}][id_barang]" value="{{ $item['id_barang'] }}">
        <input type="hidden" name="items[{{ $g }}][rencana_ids]" value="{{ implode(',',$item['rencana_ids']) }}">
        <input type="hidden" id="total_kebutuhan_{{ $g }}" value="{{ $item['total_kebutuhan'] }}">
        <input type="hidden" id="area_options_data_{{ $g }}" value='{{ $areaOptions }}'>
    </td>
</tr>

{{-- BARIS DETAIL KEBUTUHAN PER WARUNG (Minimalis - colspan=6) --}}
<tr class="detail-row bg-gray-50 border-b border-gray-200" data-group="{{ $g }}">
    <td colspan="6" class="px-2 py-1">
        <div class="text-xs text-gray-700 flex flex-wrap gap-x-3">
            <strong class="text-gray-800">Split Kebutuhan:</strong>
            @foreach($item['detail_warung'] as $detail)
                <span class="whitespace-nowrap">{{ $detail['warung'] }}: <span class="font-bold text-gray-700">{{ $detail['kebutuhan'] }} pcs</span></span>
            @endforeach
        </div>
    </td>
</tr>

{{-- ROW PEMBELIAN PERTAMA (INPUT) --}}
<tr class="purchase-row group-row border-t border-gray-200 {{ $autoSkip ? 'bg-red-50' : 'bg-white' }}" data-group="{{ $g }}" data-index="0">
    
    {{-- Kolom 1: SKIP & Sisa Kebutuhan (Paling Kiri) --}}
    <td class="p-1">
        <div class="flex items-center justify-between mb-1">
            <label class="text-red-600 font-medium cursor-pointer flex items-center text-xs">
                <input type="hidden" name="items[{{ $g }}][skip]" value="{{ $autoSkip ? 1 : 0 }}" class="skip-hidden-input"> 
                <input type="checkbox" class="skip-checkbox mr-1 h-4 w-4 text-red-600 border-gray-300 rounded focus:ring-red-500" data-group="{{ $g }}" {{ $autoSkip ? 'checked' : '' }}> 
                SKIP
            </label>
            <span class="text-gray-600 text-xs whitespace-nowrap">
                Sisa: <span class="text-red-600 font-bold" id="sisa_kebutuhan_{{ $g }}">0</span> pcs
            </span>
        </div>
    </td>

    {{-- Kolom 2: SELECT AREA --}}
    <td class="p-1">
        <select {{ $autoSkip ? 'disabled' : '' }} name="items[{{ $g }}][purchases][0][area_pembelian_id]" 
                class="area-select w-full border border-gray-300 p-2 text-xs rounded-md focus:ring-gray-500 focus:border-gray-500" data-group="{{ $g }}">
            @if($noArea)
                <option value="" selected>❌ Tidak ada area</option>
            @elseif($singleArea)
                <option value="{{ $validAreas->first()->id }}" selected>{{ $validAreas->first()->area }}</option>
            @else
                <option value="" selected>Pilih Area</option>
                @foreach($validAreas as $a)
                    <option value="{{ $a->id }}">{{ $a->area }}</option>
                @endforeach
            @endif
        </select>
    </td>

    {{-- Kolom 3: Jumlah Beli (Qty) --}}
    <td class="p-1">
    <label class="block text-xs font-medium text-gray-700">Qty (pcs)</label>
        <input type="number" name="items[{{ $g }}][purchases][0][jumlah_beli]" 
                value="{{ $autoSkip ? 0 : ($singleArea ? $item['total_kebutuhan'] : 0) }}" 
                {{ $autoSkip ? 'disabled' : '' }}
                min="0"
                class="qty w-full border border-gray-300 p-2 text-center text-sm rounded-md focus:ring-gray-500 focus:border-gray-500"
                data-group="{{ $g }}">
    </td>

    {{-- Kolom 4: Total Harga (INPUT) --}}
    <td class="p-1">
    <label class="block text-xs font-medium text-gray-700">Total Harga (Rp)</label>
        <input type="number" name="items[{{ $g }}][purchases][0][harga]" 
                value="0" {{ $autoSkip ? 'disabled' : '' }}
                min="0"
                class="row-total-price w-full border border-gray-300 p-2 text-right text-sm rounded-md focus:ring-gray-500 focus:border-gray-500"
                data-group="{{ $g }}">
    </td>

    {{-- Kolom 5: Tanggal Exp --}}
    <td class="p-1">
    <label class="block text-xs font-medium text-gray-700">Tanggal Exp.</label>
        <input type="date" name="items[{{ $g }}][purchases][0][tanggal_kadaluarsa]" 
                {{ $autoSkip ? 'disabled' : '' }}
                class="w-full border border-gray-300 p-2 text-center text-xs rounded-md focus:ring-gray-500 focus:border-gray-500">
    </td>

    {{-- Kolom 6: Aksi --}}
    <td class="text-center p-1">
        @if($noArea)
            <span class="text-orange-500 text-xs font-bold">SKIP</span>
        @else
            <button type="button" class="text-green-600 hover:text-green-800 add-row font-bold text-lg transition p-1 rounded-full {{ $singleArea ? 'opacity-50 cursor-not-allowed' : '' }}" 
                    data-group="{{ $g }}" 
                    title="{{ $singleArea ? 'Tidak bisa menambah area karena hanya ada 1 area valid' : 'Tambah Area Pembelian (Split Purchase)' }}"
                    {{ $singleArea ? 'disabled' : '' }}>
                <i class="fas fa-plus-circle"></i>
            </button>
        @endif
    </td>
</tr>

<tr class="total-row bg-gray-100 border-t border-gray-300" data-group="{{ $g }}">
    {{-- Total Beli Label (Col 1 & 2 digabung) --}}
    <td colspan="2" class="text-right font-semibold p-2 text-gray-800">Total Beli:</td> 
    {{-- Total Qty Value (Col 3) --}}
    <td class="text-center font-extrabold p-2 text-lg text-blue-700 whitespace-nowrap" id="total_qty_bought_{{ $g }}">0</td>
    {{-- Total Harga Value (Spanning Col 4 & 5) --}}
    <td colspan="2" class="text-right font-extrabold p-2 text-lg text-green-700 whitespace-nowrap" id="grand_total_price_{{ $g}}">0</td>
    {{-- Aksi Column (Col 6) --}}
    <td colspan="1"></td> 
</tr>

@endforeach
</tbody>
</table>
</div>

<button type="submit" id="btnSubmitPembelian" class="mt-3 bg-gray-600 text-white px-5 py-2 rounded hover:bg-gray-700 transition duration-150 shadow-lg disabled:opacity-50 disabled:cursor-not-allowed text-sm" disabled>
    <i class="fas fa-paper-plane mr-2"></i> Proses Pembelian
</button>
</form>

@endif
</main>
</div>

{{-- Membutuhkan Font Awesome untuk ikon --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"> 

<script>
let nextRow = {}
let rowCounts = {}
let totalKebutuhan = {}

// ===================== INISIALISASI =======================
document.addEventListener("DOMContentLoaded", function() {
    const purchaseTableBody = document.getElementById('purchaseTableBody');
    if (!purchaseTableBody) return;

    // Loop semua item yang ada di table body
    document.querySelectorAll('tr.group-header-row').forEach(headerRow => {
        const g = headerRow.dataset.group;
        const totalKebutuhanEl = document.getElementById(`total_kebutuhan_${g}`);
        
        if (!totalKebutuhanEl) return;
        
        // Inisialisasi variabel global
        nextRow[g] = 1;
        rowCounts[g] = 1;
        totalKebutuhan[g] = parseInt(totalKebutuhanEl.value) || 0;

        // Hitung total awal
        updateGroupTotals(g);
        updateAreaSelects(g); // Panggil fungsi baru untuk inisialisasi area
    });
    
    // Attach Listeners
    attachEventListeners();
    checkFormValidity(); // Cek validitas form setelah inisialisasi
});


// ===================== EVENT LISTENERS (DELEGASI) =======================
function attachEventListeners() {
    // Tombol Add/Remove Row (Event Delegation)
    document.removeEventListener("click", rowButtonHandler);
    document.addEventListener("click", rowButtonHandler);

    // Input Qty dan Total Row Price (Event Delegation)
    document.removeEventListener("input", inputHandler);
    document.addEventListener("input", inputHandler);
    
    // Area Select Change (Delegation tidak bekerja baik di 'change' event pada select, 
    // jadi kita attach pada elemen yang sudah ada saat DOMContentLoaded)
    // Untuk elemen baru, listener di-attach di addRow.
    document.querySelectorAll(".area-select").forEach(select => {
        // Hapus listener lama jika ada
        select.removeEventListener("change", areaChangeHandler);
        select.addEventListener("change", areaChangeHandler);
    });
    
    // Skip Checkbox
    document.querySelectorAll(".skip-checkbox").forEach(cb => {
        cb.removeEventListener("change", skipHandler);
        cb.addEventListener("change", skipHandler);
    });
}

function rowButtonHandler(e) {
    // Gunakan e.target.closest() untuk delegasi event
    if (e.target.closest(".add-row")) {
        e.preventDefault();
        addRow(e.target.closest(".add-row").dataset.group);
    } else if (e.target.closest(".remove-row")) {
        e.preventDefault();
        removeRow(e.target.closest(".remove-row").dataset.group, e.target.closest('tr'));
    }
}

function inputHandler(e) {
    const group = e.target.closest('tr.purchase-row')?.dataset.group;
    
    // Cek jika input adalah Qty atau Total Harga per Baris
    if (group && (e.target.classList.contains('qty') || e.target.classList.contains('row-total-price'))) {
        
        // Pastikan nilai selalu non-negatif
        if (e.target.value < 0) e.target.value = 0;
        
        updateGroupTotals(group);
    }
}

function areaChangeHandler(e) {
    const g = e.target.dataset.group;
    updateAreaSelects(g);
    checkFormValidity(); // Cek validitas saat area berubah
}

function skipHandler(e) {
    let g = e.target.dataset.group;
    let disabled = e.target.checked;
    
    document.querySelector(`input[name="items[${g}][skip]"]`).value = disabled ? 1 : 0;
    
    const purchaseRows = document.querySelectorAll(`tr.purchase-row[data-group="${g}"]`);
    const headerRow = document.querySelector(`tr.group-header-row[data-group="${g}"]`);
    const detailRow = document.querySelector(`tr.detail-row[data-group="${g}"]`);
    
    // Tambah/Hapus kelas untuk styling
    headerRow.classList.toggle('is-skipped', disabled);
    purchaseRows.forEach(row => {
        row.classList.toggle('bg-red-50', disabled);
        row.classList.toggle('bg-white', !disabled);
    });
    
    // Disable/Enable semua input pembelian
    purchaseRows.forEach(row => {
        row.querySelectorAll('input, select').forEach(el => {
            if (el.type !== 'hidden' && el.type !== 'checkbox') {
                el.disabled = disabled;
                // Reset value to 0 if disabled
                if (disabled && (el.classList.contains('qty') || el.classList.contains('row-total-price'))) {
                    el.value = 0;
                }
            }
        });
        
        // Handle Add Row button state
        const addButton = row.querySelector('.add-row');
        if (addButton) {
            const isSingleArea = addButton.title.includes('hanya ada 1 area valid');
            
            if (!isSingleArea) {
                addButton.disabled = disabled;
            }
            addButton.classList.toggle('opacity-50', disabled || isSingleArea);
            addButton.classList.toggle('cursor-not-allowed', disabled || isSingleArea);
        }
    });
    
    updateAreaSelects(g); // Update area selects state
    updateGroupTotals(g); // Update total dan cek validasi
}


// ================= UPDATE AREA SELECTS (Fungsi Baru) =====================
function updateAreaSelects(g) {
    const selectedAreaIds = new Set();
    const areaSelects = document.querySelectorAll(`tr.purchase-row[data-group="${g}"] .area-select`);
        
    // 1. Kumpulkan ID area yang sudah terpilih
    areaSelects.forEach(select => {
        if (select.value) {
            selectedAreaIds.add(select.value);
        }
    });
    
    // 2. Iterasi lagi untuk menonaktifkan opsi yang sudah terpilih di SEMUA baris
    areaSelects.forEach(select => {
        const currentValue = select.value;
        
        select.querySelectorAll('option').forEach(option => {
            const areaId = option.value;
            
            // Abaikan opsi kosong/Pilih Area
            if (!areaId) return; 
            
            // Jika ID area ada di Set dan BUKAN nilai saat ini, maka disable
            if (selectedAreaIds.has(areaId) && areaId !== currentValue) {
                option.disabled = true;
                option.classList.add('bg-gray-200');
            } else {
                // Aktifkan kembali opsi yang tadinya didisable
                option.disabled = false;
                option.classList.remove('bg-gray-200');
            }
        });
    });
    
    // 3. Cek apakah tombol tambah baris harus dinonaktifkan
    const addButton = document.querySelector(`tr.purchase-row[data-group="${g}"] .add-row`);
    if (addButton) {
        const areaOptionsData = document.getElementById(`area_options_data_${g}`).value;
        const totalValidAreas = JSON.parse(areaOptionsData).length;
        
        // Hitung jumlah baris pembelian yang ada
        const currentRowsCount = document.querySelectorAll(`tr.purchase-row[data-group="${g}"]`).length;
        
        const maxRowsReached = currentRowsCount >= totalValidAreas;
        
        // Cek apakah tombol awalnya dinonaktifkan karena singleArea, kita jangan ubah titlenya
        const isSingleArea = addButton.title.includes('hanya ada 1 area valid');
        const isSkipped = document.querySelector(`.skip-checkbox[data-group="${g}"]`)?.checked || false;

        if (maxRowsReached && !isSingleArea) {
            addButton.disabled = true;
            addButton.classList.add('opacity-50', 'cursor-not-allowed');
            addButton.title = "Semua Area Pembelian yang valid sudah digunakan.";
        } else if (!isSingleArea && !isSkipped) {
            // Hanya aktifkan kembali jika tidak dalam kondisi singleArea/isSkipped
            addButton.disabled = false;
            addButton.classList.remove('opacity-50', 'cursor-not-allowed');
            addButton.title = 'Tambah Area Pembelian (Split Purchase)';
        }
        // Jika singleArea atau isSkipped, biarkan statusnya seperti di Blade/skipHandler
    }
}


// ================= UPDATE TOTALS =====================
function updateGroupTotals(g){
    let totalQtyBought = 0
    let grandTotalPrice = 0
    const isSkipped = document.querySelector(`.skip-checkbox[data-group="${g}"]`)?.checked || false;

    // Loop semua baris pembelian untuk group ini
    document.querySelectorAll(`tr.purchase-row[data-group="${g}"]`).forEach(row=>{
        const qtyInput = row.querySelector('.qty')
        const rowTotalPriceInput = row.querySelector('.row-total-price')
        
        // Ambil nilai
        const qty = parseInt(qtyInput.value) || 0
        const rowTotal = parseInt(rowTotalPriceInput.value) || 0
        
        if (!isSkipped) {
            totalQtyBought += qty
            grandTotalPrice += rowTotal 
        }
    })

    const sisaKebutuhan = totalKebutuhan[g] - totalQtyBought
    
    // Update total di footer dan sisa kebutuhan di header
    document.getElementById(`total_qty_bought_${g}`).innerText = formatNumber(totalQtyBought)
    document.getElementById(`sisa_kebutuhan_${g}`).innerText = formatNumber(sisaKebutuhan)
    document.getElementById(`grand_total_price_${g}`).innerText = formatNumber(grandTotalPrice)

    // Validasi Sisa Kebutuhan (Styling)
    const sisaKebutuhanEl = document.getElementById(`sisa_kebutuhan_${g}`);
    const headerRow = document.querySelector(`tr.group-header-row[data-group="${g}"]`);
    const detailRow = document.querySelector(`tr.detail-row[data-group="${g}"]`);
    
    if (sisaKebutuhan < 0) {
        sisaKebutuhanEl.classList.add('text-red-700', 'animate-pulse');
        headerRow.classList.add('bg-red-100', 'border-red-500');
        detailRow.classList.add('bg-red-50');
        detailRow.classList.remove('bg-gray-50');
    } else {
        sisaKebutuhanEl.classList.remove('text-red-700', 'animate-pulse');
        headerRow.classList.remove('bg-red-100', 'border-red-500');
        detailRow.classList.remove('bg-red-50');
        detailRow.classList.add('bg-gray-50');
    }
    
    updateAreaSelects(g); // Panggil juga untuk memastikan tombol add row diperbarui
    checkFormValidity();
}


// ================= VALIDASI FORM KESELURUHAN =====================
function checkFormValidity() {
    let hasUnskippedZero = false;
    let hasNegativeSisa = false;
    let anyValidGroup = false;
    let hasInputError = false; 

    for(const g in totalKebutuhan) {
        const sisaEl = document.getElementById(`sisa_kebutuhan_${g}`);
        const totalBoughtEl = document.getElementById(`total_qty_bought_${g}`);
        const isSkipped = document.querySelector(`.skip-checkbox[data-group="${g}"]`)?.checked || false;
        
        // Tambahkan pengecekan null untuk totalBoughtEl
        if (sisaEl && totalBoughtEl) { 
            const sisa = parseInt(sisaEl.innerText.replace(/\./g, '')) || 0;
            const totalBought = parseInt(totalBoughtEl.innerText.replace(/\./g, '')) || 0;
                        
            if (!isSkipped) {
                // Check for unskipped zero purchase
                if (totalKebutuhan[g] > 0 && totalBought === 0) {
                    hasUnskippedZero = true;
                }
                
                // Check for empty area/price/qty in any purchase row
                document.querySelectorAll(`tr.purchase-row[data-group="${g}"]`).forEach(row => {
                    const areaSelect = row.querySelector('.area-select');
                    const qtyInput = row.querySelector('.qty');
                    const rowTotalPriceInput = row.querySelector('.row-total-price');
                    
                    const qty = parseInt(qtyInput?.value) || 0;
                    const rowTotal = parseInt(rowTotalPriceInput?.value) || 0;
                    const area = areaSelect?.value;

                    // Jika qty > 0, harus ada area dan total harga > 0
                    if (qty > 0) {
                        // Jika area belum dipilih, atau total harga 0
                        if (!area || rowTotal === 0) { 
                            hasInputError = true;
                        }
                    }
                });
                
                if (totalBought > 0 || totalKebutuhan[g] === 0) {
                    anyValidGroup = true;
                }
            } else {
                anyValidGroup = true; // Jika di-skip, dianggap valid untuk diproses
            }
        }
    }
    
    const submitBtn = document.getElementById('btnSubmitPembelian');
    // Clear semua kelas status
    submitBtn.classList.remove('bg-red-600', 'hover:bg-red-700', 'bg-orange-500', 'hover:bg-orange-600', 'bg-gray-600', 'hover:bg-gray-700', 'bg-gray-400');
    
    if (!anyValidGroup && Object.keys(totalKebutuhan).length > 0) {
        submitBtn.disabled = true;
        submitBtn.innerText = '❌ Tidak ada item untuk diproses.';
        submitBtn.classList.add('bg-gray-400');
    } else if (hasNegativeSisa) {
        submitBtn.disabled = true;
        submitBtn.innerText = '🚨 Kuantitas Berlebihan. Periksa Item Merah!';
        submitBtn.classList.add('bg-red-600', 'hover:bg-red-700');
    } else if (hasInputError) {
        submitBtn.disabled = true;
        submitBtn.innerText = '⚠️ Ada Baris Pembelian yang Belum Lengkap (Area/Total Harga 0)!';
        submitBtn.classList.add('bg-orange-500', 'hover:bg-orange-600');
    } else if (hasUnskippedZero) {
        submitBtn.disabled = true;
        submitBtn.innerText = '⚠️ Ada item yang belum dibeli (Qty 0) dan belum di-skip!';
        submitBtn.classList.add('bg-orange-500', 'hover:bg-orange-600');
    } else {
        submitBtn.disabled = false;
        submitBtn.innerText = '✅ Proses Pembelian';
        submitBtn.classList.add('bg-gray-600', 'hover:bg-gray-700');
    }
}


// ================= ADD ROW ==========================
function addRow(g){
    const areaOptionsData = document.getElementById(`area_options_data_${g}`).value;
    const areaOptions = JSON.parse(areaOptionsData);
    const noArea = areaOptions.length === 0;
    
    // Ambil semua area yang sudah terpilih
    const currentSelectedAreaIds = new Set();
    document.querySelectorAll(`tr.purchase-row[data-group="${g}"] .area-select`).forEach(select => {
        if (select.value) {
            currentSelectedAreaIds.add(select.value);
        }
    });
    
    // Cek ketersediaan slot (sudah dilakukan di updateAreaSelects, tapi di sini double check)
    if (areaOptions.length <= currentSelectedAreaIds.size) {
        alert(`Pembelian tidak bisa dipecah lagi karena semua ${areaOptions.length} Area Pembelian yang valid sudah digunakan.`);
        return;
    }

    // Dapatkan ID Area yang tersedia pertama kali
    let nextAreaId = '';
    for (const a of areaOptions) {
        if (!currentSelectedAreaIds.has(String(a.id))) {
            nextAreaId = String(a.id);
            break;
        }
    }
    
    const row = nextRow[g]++;
    rowCounts[g]++;
    
    const isSkipped = document.querySelector(`.skip-checkbox[data-group="${g}"]`)?.checked || false;
    
    // Bangun opsi HTML dengan nextAreaId sebagai selected
    let optionsHtml = noArea ? '<option value="" selected>❌ Tidak ada</option>' : '<option value="">Pilih Area</option>';
    areaOptions.forEach(a => {
        const isSelected = String(a.id) === nextAreaId;
        optionsHtml += `<option value="${a.id}" ${isSelected ? 'selected' : ''}>${a.area}</option>`;
    });
    
    const rowBgClass = isSkipped ? 'bg-red-50' : 'bg-white';
    
    // Row tambahan hanya menampilkan input (Kolom 1 - 5)
    let html = `
    <tr class="purchase-row border-t border-gray-200 ${rowBgClass}" data-group="${g}" data-index="${row}">
        {{-- Kolom 1: Dibuat kosong atau hanya pemisah visual --}}
        <td class="p-1"></td>
        {{-- Kolom 2: Area Select --}}
        <td class="p-1">
            <select name="items[${g}][purchases][${row}][area_pembelian_id]" 
                class="area-select w-full border border-gray-300 p-2 text-xs rounded-md focus:ring-gray-500 focus:border-gray-500" data-group="${g}" ${noArea||isSkipped?'disabled':''}>
                ${optionsHtml}
            </select>
        </td>
        {{-- Kolom 3: Qty --}}
        <td class="p-1">
<label class="block text-xs font-medium text-gray-700 mb-1">Qty (pcs)</label>
<input type="number" name="items[${g}][purchases][${row}][jumlah_beli]" 
                class="qty w-full border border-gray-300 p-2 text-center text-sm rounded-md focus:ring-gray-500 focus:border-gray-500" 
                ${noArea||isSkipped?'disabled':''} min="0" value="0" data-group="${g}"></td>
        {{-- Kolom 4: Total Harga (Input) --}}
        <td class="p-1">
<label class="block text-xs font-medium text-gray-700">Total Harga (Rp)</label>
<input type="number" name="items[${g}][purchases][${row}][harga]" 
                class="row-total-price w-full border border-gray-300 p-2 text-right text-sm rounded-md focus:ring-gray-500 focus:border-gray-500" 
                ${noArea||isSkipped?'disabled':''} min="0" value="0" data-group="${g}"></td>
        {{-- Kolom 5: Tanggal Exp --}}
        <td class="p-1">
<label class="block text-xs font-medium text-gray-700">Tanggal Exp.</label>
<input type="date" name="items[${g}][purchases][${row}][tanggal_kadaluarsa]" 
                class="w-full border border-gray-300 p-2 text-center text-xs rounded-md focus:ring-gray-500 focus:border-gray-500" ${noArea||isSkipped?'disabled':''}></td>
        {{-- Kolom 6: Aksi --}}
        <td class="text-center p-1">
            <button type="button" class="text-red-600 hover:text-red-800 remove-row transition p-1 rounded-full" data-group="${g}" title="Hapus Baris">
                <i class="fas fa-minus-circle"></i>
            </button>
        </td>
    </tr>
    `;

    const totalRow = document.querySelector(`tr.total-row[data-group="${g}"]`);
    totalRow.insertAdjacentHTML('beforebegin',html);
    
    // Ambil elemen select yang baru dibuat dan pasang listener-nya
    const newRow = totalRow.previousElementSibling;
    const newAreaSelect = newRow.querySelector('.area-select');
    if (newAreaSelect) {
        newAreaSelect.addEventListener("change", areaChangeHandler);
    }
    
    updateAreaSelects(g); // Perbarui status select area di semua baris
    updateGroupTotals(g);
}

// ================= REMOVE ROW =======================
function removeRow(g, rowElement){
    
    if (rowElement.dataset.index === "0") {
        alert("Baris pembelian pertama tidak boleh dihapus!");
        return;
    }

    rowElement.remove();
    rowCounts[g]--;
    
    updateAreaSelects(g); // Panggil ini setelah baris dihapus untuk mengaktifkan kembali opsi
    updateGroupTotals(g);
}

// ================= HELPER ===========================
function formatNumber(num) {
    // Menggunakan toLocaleString untuk format angka yang lebih baik (misalnya 1.000.000)
    return (Number(num) || 0).toLocaleString('id-ID'); 
}
</script>
@endsection
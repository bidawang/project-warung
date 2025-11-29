
@php
    $validAreas = $item['valid_areas'];
    $noArea = $validAreas->isEmpty();
    $singleArea = $validAreas->count() === 1;
    $autoSkip = $noArea; 
    
    $areaOptions = $validAreas->map(function($a) {
        return [
            'id'   => $a->id,
            'area' => $a->area
        ];
    })->toJson();
@endphp

{{-- === HEADER GRUP BARANG (COMPACT VIEW) === --}}
<tr class="bg-indigo-50 font-semibold text-gray-800 border-t-4 border-indigo-300 {{ $autoSkip ? 'is-skipped' : '' }}" data-group="{{ $g }}">
    <td colspan="6" class="p-3">
        <div class="flex justify-between items-center flex-wrap">
            <div class="flex items-center space-x-4">
                <span class="text-indigo-800 text-lg font-extrabold">{{ $item['nama_barang'] }}</span>
                <button type="button" class="text-xs bg-indigo-200 text-indigo-700 px-2 py-1 rounded-full hover:bg-indigo-300 transition detail-button flex items-center gap-1 shadow-sm" data-group="{{ $g }}">
                    <i class="fas fa-info-circle"></i> Detail ({{ $item['total_kebutuhan'] }} pcs)
                </button>
            </div>
            
            <div class="flex items-center space-x-6 text-sm">
                <span class="text-gray-600 whitespace-nowrap">Sisa Kebutuhan: <span class="text-red-500 font-extrabold" id="sisa_kebutuhan_{{ $g }}"></span> pcs</span>

                <label class="text-red-600 font-medium cursor-pointer flex items-center">
                    <input type="hidden" name="items[{{ $g }}][skip]" value="{{ $autoSkip ? 1 : 0 }}" class="skip-hidden-input"> 
                    <input type="checkbox" class="skip-checkbox mr-1 h-4 w-4 text-red-600 border-gray-300 rounded focus:ring-red-500" data-group="{{ $g }}" {{ $autoSkip ? 'checked' : '' }}> 
                    Skip
                </label>
            </div>
        </div>

        {{-- Hidden Input Data --}}
        <input type="hidden" name="items[{{ $g }}][id_barang]" value="{{ $item['id_barang'] }}">
        <input type="hidden" name="items[{{ $g }}][rencana_ids]" value="{{ implode(',',$item['rencana_ids']) }}">
        <input type="hidden" id="total_kebutuhan_{{ $g }}" value="{{ $item['total_kebutuhan'] }}">
        <input type="hidden" id="area_options_data_{{ $g }}" value='{{ $areaOptions }}'>
    </td>
</tr>

{{-- Box Detail Kebutuhan (default hidden) --}}
<tr class="hidden detail-row bg-indigo-100 border-b border-indigo-200" data-group="{{ $g }}">
    <td colspan="6" class="p-3">
        <strong class="text-indigo-800 text-sm">Kebutuhan per Warung:</strong>
        <div class="flex flex-wrap gap-2 mt-2">
        @foreach($item['detail_warung'] as $detail)
            <span class="bg-white p-2 text-xs rounded shadow-sm border border-indigo-300">{{ $detail['warung'] }}: <span class="text-indigo-600 font-bold">{{ $detail['kebutuhan'] }} pcs</span></span>
        @endforeach
        </div>
    </td>
</tr>


{{-- ROW PEMBELIAN PERTAMA --}}
<tr class="purchase-row group-row border-t border-gray-200 {{ $autoSkip ? 'bg-red-50' : 'bg-white' }}" data-group="{{ $g }}" data-index="0">
    
    {{-- Kolom Pembelian Area --}}
    <td class="p-1">
        <select {{ $autoSkip ? 'disabled' : '' }} name="items[{{ $g }}][purchases][0][area_pembelian_id]" 
                class="area-select w-full border border-gray-300 p-2 text-xs rounded-md focus:ring-indigo-500 focus:border-indigo-500" data-group="{{ $g }}">
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

    {{-- Kolom Pembelian QTY --}}
    <td class="p-1">
        <input type="number" name="items[{{ $g }}][purchases][0][jumlah_beli]" 
                value="{{ $autoSkip ? 0 : ($singleArea ? $item['total_kebutuhan'] : 0) }}" 
                {{ $autoSkip ? 'disabled' : '' }}
                min="0"
                class="qty w-full border border-gray-300 p-2 text-center text-sm rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                data-group="{{ $g }}"
                data-type="qty-input">
    </td>

    {{-- Kolom Harga Satuan --}}
    <td class="p-1">
        <input type="number" name="items[{{ $g }}][purchases][0][harga]" 
                value="0" {{ $autoSkip ? 'disabled' : '' }}
                min="0"
                class="price w-full border border-gray-300 p-2 text-right text-sm rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                data-group="{{ $g }}"
                data-type="price-input" 
                oninput="this.value = Math.abs(this.value)">
    </td>
    
    {{-- Kolom Total Harga --}}
    <td class="p-1 text-right font-bold text-gray-700 total-price-cell whitespace-nowrap text-sm bg-gray-50" id="total_price_{{ $g }}_0">
        0
    </td>

    {{-- Kolom Tanggal Exp --}}
    <td class="p-1">
        <input type="date" name="items[{{ $g }}][purchases][0][tanggal_kadaluarsa]" 
                {{ $autoSkip ? 'disabled' : '' }}
                class="w-full border border-gray-300 p-2 text-center text-xs rounded-md focus:ring-indigo-500 focus:border-indigo-500">
    </td>

    {{-- Kolom Aksi --}}
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
    <td colspan="1" class="text-right font-semibold p-2 text-gray-800">Total Beli:</td>
    <td class="text-center font-extrabold p-2 text-lg text-indigo-700 whitespace-nowrap" id="total_qty_bought_{{ $g }}">0</td>
    <td colspan="2" class="text-right font-extrabold p-2 text-lg text-green-700 whitespace-nowrap" id="grand_total_price_{{ $g}}">0</td>
    <td colspan="2"></td>
</tr>

@endforeach
</tbody>
</table>
</div>

<button type="submit" id="btnSubmitPembelian" class="mt-3 bg-indigo-600 text-white px-5 py-2 rounded hover:bg-indigo-700 transition duration-150 shadow-lg disabled:opacity-50 disabled:cursor-not-allowed text-sm" disabled>
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
    document.querySelectorAll('tr.bg-indigo-50').forEach(headerRow => {
        const g = headerRow.dataset.group;
        const autoSkip = headerRow.classList.contains('is-skipped');
        const totalKebutuhanEl = document.getElementById(`total_kebutuhan_${g}`);
        
        if (!totalKebutuhanEl) return;
        
        // Inisialisasi variabel global
        nextRow[g] = 1;
        rowCounts[g] = 1;
        totalKebutuhan[g] = parseInt(totalKebutuhanEl.value) || 0;

        // Hitung total awal
        updateGroupTotals(g);
    });
    
    // Attach Listeners
    attachEventListeners();
    checkFormValidity(); // Cek validitas form setelah inisialisasi
});


// ===================== EVENT LISTENERS =======================
function attachEventListeners() {
    // Tombol Add/Remove Row
    document.removeEventListener("click", rowButtonHandler);
    document.addEventListener("click", rowButtonHandler);

    // Input Qty dan Price
    document.removeEventListener("input", inputHandler);
    document.addEventListener("input", inputHandler);
    
    // Skip Checkbox
    document.querySelectorAll(".skip-checkbox").forEach(cb => {
        cb.removeEventListener("change", skipHandler);
        cb.addEventListener("change", skipHandler);
    });

    // Detail Button
    document.querySelectorAll(".detail-button").forEach(btn => {
        btn.removeEventListener("click", detailHandler);
        btn.addEventListener("click", detailHandler);
    });
}

function rowButtonHandler(e) {
    if (e.target.closest(".add-row")?.classList.contains("add-row")) {
        e.preventDefault();
        addRow(e.target.closest(".add-row").dataset.group);
    } else if (e.target.closest(".remove-row")?.classList.contains("remove-row")) {
        e.preventDefault();
        removeRow(e.target.closest(".remove-row").dataset.group, e.target.closest('tr'));
    }
}

function inputHandler(e) {
    const group = e.target.dataset.group;
    
    if (group && (e.target.classList.contains('qty') || e.target.classList.contains('price'))) {
        
        // Pastikan nilai selalu positif
        if (e.target.value < 0) e.target.value = 0;
        
        updateGroupTotals(group);
    }
}

function skipHandler(e) {
    let g = e.target.dataset.group;
    let disabled = e.target.checked;
    
    document.querySelector(`input[name="items[${g}][skip]"]`).value = disabled ? 1 : 0;
    
    const purchaseRows = document.querySelectorAll(`tr.purchase-row[data-group="${g}"]`);
    const headerRow = document.querySelector(`tr.bg-indigo-50[data-group="${g}"]`);
    
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
                if (disabled && (el.classList.contains('qty') || el.classList.contains('price'))) {
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
    
    updateGroupTotals(g); // Update total dan cek validasi
}


// ================= UPDATE TOTALS =====================
function updateGroupTotals(g){
    let totalQtyBought = 0
    let grandTotalPrice = 0
    const isSkipped = document.querySelector(`.skip-checkbox[data-group="${g}"]`)?.checked || false;

    // Loop semua baris pembelian untuk group ini
    document.querySelectorAll(`tr.purchase-row[data-group="${g}"]`).forEach(row=>{
        const qtyInput = row.querySelector('.qty')
        const priceInput = row.querySelector('.price')
        const totalPriceCell = row.querySelector('.total-price-cell')
        
        const qty = parseInt(qtyInput.value) || 0
        const price = parseInt(priceInput.value) || 0
        
        const totalPrice = qty * price

        if (!isSkipped) {
            totalQtyBought += qty
            grandTotalPrice += totalPrice
            if(totalPriceCell) totalPriceCell.innerText = formatNumber(totalPrice)
        } else {
            if(totalPriceCell) totalPriceCell.innerText = '0'
        }
    })

    const sisaKebutuhan = totalKebutuhan[g] - totalQtyBought
    
    // Update total di footer
    document.getElementById(`total_qty_bought_${g}`).innerText = formatNumber(totalQtyBought)
    document.getElementById(`sisa_kebutuhan_${g}`).innerText = formatNumber(sisaKebutuhan)
    document.getElementById(`grand_total_price_${g}`).innerText = formatNumber(grandTotalPrice)

    // Validasi Sisa Kebutuhan (Styling)
    const sisaKebutuhanEl = document.getElementById(`sisa_kebutuhan_${g}`);
    const headerRow = document.querySelector(`tr.bg-indigo-50[data-group="${g}"]`);
    
    if (sisaKebutuhan < 0) {
        sisaKebutuhanEl.classList.add('text-red-700', 'animate-pulse');
        headerRow.classList.add('bg-red-200', 'border-red-500');
    } else {
        sisaKebutuhanEl.classList.remove('text-red-700', 'animate-pulse');
        headerRow.classList.remove('bg-red-200', 'border-red-500');
    }
    
    checkFormValidity();
}


// ================= VALIDASI FORM KESELURUHAN =====================
function checkFormValidity() {
    let hasUnskippedZero = false;
    let hasNegativeSisa = false;
    let anyValidGroup = false;
    let hasInputError = false; // Cek untuk input Area/Price/Qty 0 yang belum diisi

    for(const g in totalKebutuhan) {
        const sisaEl = document.getElementById(`sisa_kebutuhan_${g}`);
        const totalBoughtEl = document.getElementById(`total_qty_bought_${g}`);
        const isSkipped = document.querySelector(`.skip-checkbox[data-group="${g}"]`)?.checked || false;
        
        if (sisaEl) {
            const sisa = parseInt(sisaEl.innerText.replace(/\./g, '')) || 0;
            const totalBought = parseInt(totalBoughtEl.innerText.replace(/\./g, '')) || 0;
            
            if (sisa < 0) {
                hasNegativeSisa = true;
            }
            
            if (!isSkipped) {
                // Check for unskipped zero purchase
                if (totalKebutuhan[g] > 0 && totalBought === 0) {
                    hasUnskippedZero = true;
                }
                
                // Check for empty area/price/qty in any purchase row
                document.querySelectorAll(`tr.purchase-row[data-group="${g}"]`).forEach(row => {
                    const areaSelect = row.querySelector('.area-select');
                    const qtyInput = row.querySelector('.qty');
                    const priceInput = row.querySelector('.price');
                    
                    const qty = parseInt(qtyInput?.value) || 0;
                    const price = parseInt(priceInput?.value) || 0;
                    const area = areaSelect?.value;

                    // Jika qty > 0 tapi area/price 0
                    if (qty > 0) {
                        if (!area || price === 0) {
                            hasInputError = true;
                        }
                    }
                });
                
                if (totalBought > 0 || totalKebutuhan[g] === 0) {
                    anyValidGroup = true;
                }
            } else {
                anyValidGroup = true;
            }
        }
    }
    
    const submitBtn = document.getElementById('btnSubmitPembelian');
    submitBtn.classList.remove('bg-red-600', 'hover:bg-red-700', 'bg-orange-500', 'hover:bg-orange-600', 'bg-indigo-600', 'hover:bg-indigo-700');
    
    if (!anyValidGroup) {
        submitBtn.disabled = true;
        submitBtn.innerText = '❌ Tidak ada item untuk diproses.';
        submitBtn.classList.add('bg-gray-400');
    } else if (hasNegativeSisa) {
        submitBtn.disabled = true;
        submitBtn.innerText = '🚨 Kuantitas Berlebihan. Periksa Item Merah!';
        submitBtn.classList.add('bg-red-600', 'hover:bg-red-700');
    } else if (hasInputError) {
        submitBtn.disabled = true;
        submitBtn.innerText = '⚠️ Ada Baris Pembelian yang Belum Lengkap (Area/Harga 0)!';
        submitBtn.classList.add('bg-orange-500', 'hover:bg-orange-600');
    } else if (hasUnskippedZero) {
        submitBtn.disabled = true;
        submitBtn.innerText = '⚠️ Ada item yang belum dibeli (Qty 0) dan belum di-skip!';
        submitBtn.classList.add('bg-orange-500', 'hover:bg-orange-600');
    } else {
        submitBtn.disabled = false;
        submitBtn.innerText = '✅ Proses Pembelian';
        submitBtn.classList.add('bg-indigo-600', 'hover:bg-indigo-700');
    }
}


// ================= DETAIL HANDLER =======================
function detailHandler(e) {
    e.preventDefault();
    const g = e.currentTarget.dataset.group;
    const headerRow = e.currentTarget.closest('tr');
    
    // Detail row berada tepat setelah header row
    let detailRow = headerRow.nextElementSibling;

    if (detailRow && detailRow.classList.contains('detail-row') && detailRow.dataset.group === g) {
        detailRow.classList.toggle('hidden');
        
        const icon = e.currentTarget.querySelector('i');
        const textNode = e.currentTarget.childNodes[2]; 

        if (detailRow.classList.contains('hidden')) {
            icon.classList.remove('fa-times-circle');
            icon.classList.add('fa-info-circle');
            textNode.nodeValue = ' Detail (' + formatNumber(totalKebutuhan[g]) + ' pcs)';
        } else {
            icon.classList.remove('fa-info-circle');
            icon.classList.add('fa-times-circle');
            textNode.nodeValue = ' Tutup Detail';
        }
    }
}


// ================= ADD ROW ==========================
function addRow(g){
    const areaOptionsData = document.getElementById(`area_options_data_${g}`).value;
    const areaOptions = JSON.parse(areaOptionsData);
    const noArea = areaOptions.length === 0;
    
    if (areaOptions.length <= rowCounts[g]) {
        alert(`Pembelian tidak bisa dipecah lagi karena semua ${areaOptions.length} Area Pembelian yang valid sudah digunakan atau hanya ada satu area valid.`);
        return;
    }
    
    const row = nextRow[g]++;
    rowCounts[g]++;
    
    const isSkipped = document.querySelector(`.skip-checkbox[data-group="${g}"]`)?.checked || false;
    
    let optionsHtml = noArea ? '<option value="" selected>❌ Tidak ada</option>' : '<option value="">Pilih Area</option>';
    areaOptions.forEach(a => {
        optionsHtml += `<option value="${a.id}">${a.area}</option>`;
    });
    
    const rowBgClass = isSkipped ? 'bg-red-50' : 'bg-white';

    let html = `
    <tr class="purchase-row border-t border-gray-200 ${rowBgClass}" data-group="${g}" data-index="${row}">
        <td class="p-1">
            <select name="items[${g}][purchases][${row}][area_pembelian_id]" 
                class="area-select w-full border border-gray-300 p-2 text-xs rounded-md focus:ring-indigo-500 focus:border-indigo-500" ${noArea||isSkipped?'disabled':''}>
                ${optionsHtml}
            </select>
        </td>
        <td class="p-1"><input type="number" name="items[${g}][purchases][${row}][jumlah_beli]" 
                class="qty w-full border border-gray-300 p-2 text-center text-sm rounded-md focus:ring-indigo-500 focus:border-indigo-500" 
                ${noArea||isSkipped?'disabled':''} min="0" value="0" data-group="${g}" data-type="qty-input" oninput="this.value = Math.abs(this.value)"></td>
        <td class="p-1"><input type="number" name="items[${g}][purchases][${row}][harga]" 
                class="price w-full border border-gray-300 p-2 text-right text-sm rounded-md focus:ring-indigo-500 focus:border-indigo-500" 
                ${noArea||isSkipped?'disabled':''} min="0" value="0" data-group="${g}" data-type="price-input" oninput="this.value = Math.abs(this.value)"></td>
        <td class="p-1 text-right font-bold text-gray-700 total-price-cell whitespace-nowrap text-sm bg-gray-50" id="total_price_${g}_${row}">0</td>
        <td class="p-1"><input type="date" name="items[${g}][purchases][${row}][tanggal_kadaluarsa]" 
                class="w-full border border-gray-300 p-2 text-center text-xs rounded-md focus:ring-indigo-500 focus:border-indigo-500" ${noArea||isSkipped?'disabled':''}></td>
        <td class="text-center p-1">
            <button type="button" class="text-red-600 hover:text-red-800 remove-row transition p-1 rounded-full" data-group="${g}" title="Hapus Baris">
                <i class="fas fa-minus-circle"></i>
            </button>
        </td>
    </tr>
    `;

    const totalRow = document.querySelector(`tr.total-row[data-group="${g}"]`);
    totalRow.insertAdjacentHTML('beforebegin',html);
    
    updateGroupTotals(g);
    attachEventListeners(); // Re-attach event listeners for new elements
}

// ================= REMOVE ROW =======================
function removeRow(g, rowElement){
    
    if (rowElement.dataset.index === "0") {
        alert("Baris pembelian pertama tidak boleh dihapus!");
        return;
    }

    rowElement.remove();
    rowCounts[g]--;
    
    updateGroupTotals(g);
}

// ================= HELPER ===========================
function formatNumber(num) {
    // Menggunakan toLocaleString untuk format angka yang lebih baik
    return (Number(num) || 0).toLocaleString('id-ID'); 
}
</script>

@endsection
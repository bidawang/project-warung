<script>
document.addEventListener('DOMContentLoaded', () => {
    const isPending = "{{ $status }}" === 'pending';
    if (!isPending) return;

    // Pastikan variabel ini tersedia dari Controller Anda
    const warungs = @json($warungs->pluck('nama_warung','id'));
    const allTransactions = @json($transaksibarangs->getCollection() ?? []);

    // Forms dan Buttons
    const formKirim = document.getElementById('formKirim');
    const btnSubmitMassal = document.getElementById('btnSubmitMassal');
    const checkAll = document.getElementById('checkAll');
    const formRencana = document.getElementById('formKirimRencana');
    const btnSubmitRencana = document.getElementById('btnSubmitRencana');
    const rencanaBlocks = document.querySelectorAll('.rencana-warung-block');

    let currentStockSisa = {}; // State stok global (ID unik Transaksi => Sisa Qty)
    const stockByBarang = {}; // Mapping Barang ID => Array Stok Sumber (Transaksi)

    allTransactions.forEach(trx => {
    console.log("=== DEBUG TRX ===", trx);

    const barangId = trx.id_barang; // pakai ini, bukan trx.barang_id

    if (!stockByBarang[barangId]) {
        stockByBarang[barangId] = [];
    }

    stockByBarang[barangId].push({
        id: trx.id,
        jumlah_awal: trx.jumlah,
        nama_barang: trx.barang?.nama_barang ?? "N/A",
        harga: trx.harga
    });

    currentStockSisa[trx.id] = trx.jumlah; // Inisialisasi stok awal
});



    const warungOptions = Object.entries(warungs).map(([id, nm]) => `<option value="${id}">${nm}</option>`).join('');
    const baseOptions = `<option value="" disabled selected>Pilih Warung</option>${warungOptions}`;

    function numberWithCommas(x) {
        if (x === undefined || x === null) return '0';
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    /**
     * Fungsi utama untuk menghitung ulang sisa stok global (currentStockSisa)
     * dengan mengurangi semua alokasi dari Kolom Kiri dan Rencana Belanja yang tercentang.
     */
    function recalculateGlobalStock() {
        // 1. Reset sisa stok ke nilai awal (jumlah_awal dari database)
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

        // 3. Kurangi dari Kolom Kanan (Rencana Belanja) yang tercentang
        document.querySelectorAll('.chk-rencana-warung:checked').forEach(chk => {
            const warungBlock = chk.closest('.rencana-warung-block');
            if (!warungBlock) return;

            warungBlock.querySelectorAll('.rencana-item').forEach(itemEl => {
                const trxSelect = itemEl.querySelector('.rencana-trx-select:not(:disabled)');
                const qtyInput = itemEl.querySelector('.rencana-qty-input:not(:disabled)');

                if (trxSelect && qtyInput) {
                    const trxId = trxSelect.value; // ID unik stok sumber yang dipilih
                    const qty = +qtyInput.value; // Jumlah yang diminta oleh Rencana

                    if (trxId && qty > 0) {
                        currentStockSisa[trxId] = Math.max(0, currentStockSisa[trxId] - qty);
                    }
                }
            });
        });

        // 4. Update tampilan Sisa Jml di Kolom Kiri
        Object.keys(currentStockSisa).forEach(trxId => {
            const sisaEl = document.getElementById(`sisa-${trxId}`);
            if (sisaEl) {
                const sisa = currentStockSisa[trxId];
                sisaEl.textContent = sisa;
                // Hanya berikan warning warna jika stok habis (bukan error jika < 0)
                sisaEl.classList.toggle('text-red-600-strong', sisa <= 0);
                sisaEl.classList.toggle('text-blue-600', sisa > 0);
            }
        });

        // 5. Setelah menghitung ulang, validasi ulang semua alokasi yang aktif
        document.querySelectorAll('.chk-trx:checked').forEach(chk => updateSisa(chk.dataset.id, false));
        document.querySelectorAll('.chk-rencana-warung:checked').forEach(chk => updateRencanaWarung(chk.dataset.warungId, false));

        // 6. Perbarui teks di dropdown Rencana Belanja (Kolom Kanan)
        updateRencanaOptionsGlobal();
    }

    function createDeliveryRow(trxId, maxQty, isFirst = false) {
        let defaultQty = maxQty > 0 ? maxQty : 1;

        const rowId = `row-${trxId}-${Date.now()}-${Math.floor(Math.random() * 1000)}`;

        return `
    <div class="flex space-x-2 items-center delivery-row" data-trx="${trxId}" data-row-id="${rowId}">
        <select name="transaksi[${trxId}][details][${rowId}][warung_id]" required
            class="warung-select border border-gray-300 rounded px-2 py-1 text-sm flex-1 focus:ring-blue-500 focus:border-blue-500 bg-white transition duration-150">
            ${baseOptions}
        </select>
        <input type="hidden" name="transaksi[${trxId}][barang_id]" value="${stockByBarang[Object.keys(stockByBarang).find(bid => stockByBarang[bid].some(s => s.id == trxId))]?.[0].id_barang ?? ''}">
        <input type="number" name="transaksi[${trxId}][details][${rowId}][jumlah]" value="${defaultQty}" min="1" max="${maxQty}" required
            class="qty-input border border-gray-300 rounded px-2 py-1 text-sm w-16 text-center focus:ring-blue-500 focus:border-blue-500 transition duration-150"/>
        <button type="button" class="btn-del text-red-500 hover:text-red-700 text-xs font-semibold w-10 py-1 transition duration-150 rounded" title="Hapus baris" ${isFirst ? 'style="visibility:hidden;"' : ''}>Hapus</button>
    </div>
`;

    }

    function toggleInputs(id, enable) {
        const container = document.getElementById(`deliveries-${id}`);
        const max = +document.getElementById(`sisa-${id}`).dataset.max;

        if (enable && container.children.length === 0) {
            container.innerHTML = createDeliveryRow(id, max, true);
        }

        container.querySelectorAll('select, input, button').forEach(el => {
            if (el.classList.contains('btn-del') && el.style.visibility === 'hidden') return;
            el.disabled = !enable;
        });

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

        // 1. Kumpulkan semua warung yang sudah dipilih (kecuali yang sedang diubah)
        allSelects.forEach(select => { if (select.value) selectedWarungs.add(select.value); });

        // 2. Update opsi untuk setiap select
        allSelects.forEach(currentSelect => {
            const currentValue = currentSelect.value;
            [...currentSelect.options].forEach(option => {
                if (option.value === "") { option.disabled = true; }
                else if (option.value === currentValue) {
                    option.disabled = false;
                    option.classList.remove('disabled-option');
                } else {
                    // Nonaktifkan jika sudah dipilih di select lain dalam transaksi yang sama
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
                if (qtyVal < 1 || isNaN(qtyVal) || qtyVal > max) { // Tambah validasi max di sini
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

    function populateStockOptions(selectEl, barangId) {
        console.log(`Populating stock options for barang ID: ${barangId}`);
        console.log('Available stocks for this barang:', stockByBarang[barangId]);
        console.log('Select Element:', selectEl);
        const stocks = stockByBarang[barangId] || [];
        let options = '<option value="" disabled selected>Pilih Sumber Stok...</option>';
        stocks.forEach(stock => {
            // Gunakan `stock.id` sebagai value
            options += `<option value="${stock.id}" data-max-stock="${stock.jumlah_awal}" data-harga="${stock.harga}">
                Stok #${stock.id} (${stock.jumlah_awal} pcs) @Rp${numberWithCommas(stock.harga)}
            </option>`;
        });
        selectEl.innerHTML = options;
    }

    /**
     * Memperbarui label opsi di dropdown Rencana Belanja
     * agar mencerminkan 'Sisa' stok global yang terbaru.
     */
    function updateRencanaOptionsGlobal() {
        const allRencanaSelects = document.querySelectorAll('.rencana-trx-select');
// console.log('Updating Rencana Options Globally...');
// console.log('Current Stock Sisa:', currentStockSisa);
// console.log('Stock By Barang:', stockByBarang);
// console.log('All Rencana Selects:', allRencanaSelects);
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

            // Jika stok yang sedang dipilih tiba-tiba menjadi 0, paksa validasi
            if(currentValue && currentStockSisa[currentValue] <= 0 && selectEl.disabled === false){
                 selectEl.classList.add('border-red-500');
            }
        });
    }

    /**
     * Validasi dan toggle input untuk Warung Rencana Belanja (Kolom Kanan)
     */
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
                    // Cari total kebutuhan dari rencana yang sama yang sedang dicek
                    let totalQtyRencanaIni = 0;
                    items.forEach(i => {
                        const s = i.querySelector('.rencana-trx-select').value;
                        const q = +i.querySelector('.rencana-qty-input').value;
                        if(s === trxId) totalQtyRencanaIni += q;
                    });

                    // Stok yang tersedia adalah Stok Awal dikurangi Alokasi Warung LAIN di Rencana dan Kolom Kiri
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

                    // Hitung total alokasi DARI SUMBER TRX ID INI, di Kolom Kiri
                    let alokasiKolomKiri = 0;
                    document.querySelectorAll(`.chk-trx:checked[data-id="${trxId}"]`).forEach(chkKiri => {
                        const containerKiri = document.getElementById(`deliveries-${trxId}`);
                        containerKiri.querySelectorAll('.qty-input:not(:disabled)').forEach(qtyInputKiri => {
                            alokasiKolomKiri += +qtyInputKiri.value;
                        });
                    });

                    const stokTersediaGlobal = stokAwal - alokasiRencanaLain - alokasiKolomKiri;

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
    document.querySelectorAll('.chk-trx').forEach(chk => {
        chk.checked = false;
    });
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

    // 1. Event Change (Selects, Checkboxes)
    document.addEventListener('change', e => {
        // Kolom Kiri: Checkbox, Warung Select
        if (e.target.classList.contains('chk-trx')) {
            toggleInputs(e.target.dataset.id, e.target.checked);
        } else if (e.target.classList.contains('warung-select')) {
            const rowEl = e.target.closest('.delivery-row');
            if (rowEl) updateSisa(rowEl.dataset.trx);
        }

        // Kolom Kanan: Checkbox Rencana, Stock Select Rencana
        else if (e.target.classList.contains('chk-rencana-warung')) {
            updateRencanaWarung(e.target.dataset.warungId);
        } else if (e.target.classList.contains('rencana-trx-select')) {
            const warungBlock = e.target.closest('.rencana-warung-block');
            // Cukup panggil recalculateGlobalStock agar opsi Rencana lain terupdate
            recalculateGlobalStock();
            if (warungBlock) updateRencanaWarung(warungBlock.dataset.warungId, false);
        }

        // Kolom Kiri: Checkbox Semua
        else if (e.target.id === 'checkAll') {
            document.querySelectorAll('.chk-trx').forEach(cb => {
                cb.checked = e.target.checked;
                toggleInputs(cb.dataset.id, cb.checked);
            });
        }
    });

    // 2. Event Input (Qty Inputs)
    document.addEventListener('input', e => {
        // Kolom Kiri: Qty Input
        if (e.target.classList.contains('qty-input')) {
            const rowEl = e.target.closest('.delivery-row');
            if (!rowEl) return;
            const max = +e.target.max;

            // Batasi input agar tidak melebihi max
            if (+e.target.value > max) e.target.value = max;
            if (+e.target.value < 1) e.target.value = 1;

            updateSisa(rowEl.dataset.trx);
        }

        // Kolom Kanan: Qty Input Rencana
        else if (e.target.classList.contains('rencana-qty-input')) {
            const warungBlock = e.target.closest('.rencana-warung-block');
            if (!warungBlock) return;
            const max = +e.target.max;

            if (+e.target.value > max) e.target.value = max;
            if (+e.target.value < 0) e.target.value = 0;

            // Panggil recalculateGlobalStock dulu
            recalculateGlobalStock();
            // Kemudian update Warung ini (dengan shouldRecalculate = false karena sudah dipanggil di atas)
            updateRencanaWarung(warungBlock.dataset.warungId, false);
        }
    });

    // 3. Event Delegasi untuk Tombol Add/Delete (Kolom Kiri)
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

    // 4. Logika Rencana Belanja (Filter)
    const searchRencana = document.getElementById('searchRencana');
    searchRencana.addEventListener('input', () => {
        const q = searchRencana.value.toLowerCase();
        rencanaBlocks.forEach(block => {
            const namaWarung = block.dataset.namaWarung.toLowerCase();
            const match = namaWarung.includes(q);
            block.classList.toggle('hidden', !match);
        });
    });

    // 5. Submit form
    formKirim.addEventListener('submit', e => {
        // ToggleInputs sudah memastikan hanya data yang dicentang/enable yang memiliki attribute name
        if(btnSubmitMassal.disabled){
            e.preventDefault();
            alert('Terdapat data pengiriman yang tidak valid atau kuantitas melebihi stok yang tersedia.');
        }
    });

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
        color: #9ca3af; /* gray-400 */
        font-style: italic;
    }
</style>

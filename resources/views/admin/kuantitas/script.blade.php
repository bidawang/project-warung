
<script>
    // ===================================
    // KONSTANTA & FUNGSI UTILITY
    // (Tidak ada perubahan, kode JS Anda sudah baik)
    // ===================================

    // Rasio diskon maksimum yang diizinkan (misalnya, 0.8 berarti harga jual per unit tidak boleh kurang dari 80% harga dasar per unit).
    const MIN_UNIT_PRICE_RATIO = 0.80;

    function formatRupiah(number) {
        // Math.ceil untuk memastikan angka tidak terpotong ke bawah saat dibulatkan ke Rupiah terdekat (tanpa desimal)
        return new Intl.NumberFormat('id-ID', { minimumFractionDigits: 0 }).format(Math.ceil(number));
    }

    function displayError(message, targetId = 'js-error-message') {
        const errorBox = document.getElementById(targetId);
        errorBox.innerHTML = '<strong>❌ Validasi Gagal:</strong> ' + message;
        errorBox.classList.remove('hidden');
        if (targetId === 'js-error-message') {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }

    function hideError(targetId = 'js-error-message') {
        const errorBox = document.getElementById(targetId);
        if (errorBox) {
            errorBox.classList.add('hidden');
        }
    }

    // ===================================
    // MODAL TOGGLE FUNCTION
    // ===================================
    function toggleModal(modalId) {
        const modal = document.getElementById(modalId);
        const content = modal.querySelector('.modal-content');

        if (modal.classList.contains('hidden')) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                content.classList.remove('scale-95');
            }, 10);
        } else {
            modal.classList.add('opacity-0');
            content.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
                // Hapus error saat modal ditutup
                const errorBox = document.getElementById(`modal-js-error-${modalId.replace('editModal-', '')}`) || document.getElementById('modal-js-error-delete');
                if (errorBox) {
                     errorBox.classList.add('hidden');
                }
            }, 300);
        }
    }

    // ===================================
    // CREATE FORM VALIDATION
    // ===================================

    /**
     * Memvalidasi form pembuatan kuantitas baru.
     */
    function validateForm() {
        hideError('js-error-message');
        const jumlahInput = document.getElementById('jumlah');
        const hargaJualInput = document.getElementById('harga_jual');
        const stokInfoBox = document.getElementById('stok-info-box');

        const newJumlah = parseInt(jumlahInput.value);
        const newHargaJual = parseInt(hargaJualInput.value);

        if (!stokInfoBox || isNaN(newJumlah) || isNaN(newHargaJual)) {
             displayError('Pastikan semua input terisi dengan angka yang valid.');
             return false;
        }

        try {
            const existingJumlahMapRaw = stokInfoBox.dataset.existingJumlahMap;
            const existingJumlahMap = JSON.parse(existingJumlahMapRaw);
            const basePrice = parseFloat(stokInfoBox.dataset.basePrice);

            // 1. Check Duplikasi Jumlah
            if (existingJumlahMap.hasOwnProperty(newJumlah)) {
                displayError(`Nilai **Jumlah** (${newJumlah} pcs) sudah terdaftar sebagai varian untuk barang ini. Mohon gunakan nilai jumlah yang berbeda.`);
                jumlahInput.focus();
                return false;
            }

            // 2. Validasi Harga Jual minimal (Diskon Floor Check)
            if (basePrice > 0) {
                const unitPriceVariant = newHargaJual / newJumlah;
                const minAllowedUnitPrice = basePrice * MIN_UNIT_PRICE_RATIO;
                const minAllowedTotalPrice = minAllowedUnitPrice * newJumlah;

                if (unitPriceVariant < minAllowedUnitPrice) {
                    displayError(`Harga Jual Total terlalu rendah! Harga satuan per unit setelah diskon tidak boleh kurang dari **${(MIN_UNIT_PRICE_RATIO * 100).toFixed(0)}%** dari harga dasar (Rp ${formatRupiah(basePrice)}). Minimal total yang diizinkan adalah **Rp ${formatRupiah(minAllowedTotalPrice)}**.`);
                    hargaJualInput.focus();
                    return false;
                }
            }
        } catch (e) {
            console.error('JSON Parsing Error or Data Missing:', e);
            displayError('Terjadi kesalahan saat memproses data stok. Coba muat ulang halaman.');
            return false;
        }

        return true;
    }

    /**
     * Menghitung dan menyarankan harga jual total.
     */
    function calculatePriceSuggestion() {
        const jumlahInput = document.getElementById('jumlah');
        const hargaJualInput = document.getElementById('harga_jual');
        const stokInfoBox = document.getElementById('stok-info-box');
        const suggestionNote = document.getElementById('price-suggestion-note');

        const newJumlah = parseInt(jumlahInput.value);

        if (!stokInfoBox || !newJumlah || newJumlah < 1) return;

        const basePrice = parseFloat(stokInfoBox.dataset.basePrice);
        const oldJumlahValue = parseInt(jumlahInput.getAttribute('data-old-value') || 0);
        const oldHargaValue = parseInt(hargaJualInput.value);
        const suggestedTotal = basePrice * newJumlah;

        if (basePrice > 0) {
            // Cek apakah harga jual kosong atau masih menggunakan default harga standar sebelumnya
            const isDefaultPrice = oldHargaValue === (basePrice * oldJumlahValue);

            if (hargaJualInput.value === '' || isDefaultPrice) {
                 // Gunakan harga saran sebagai default, beri diskon 5% untuk mendorong input harga bulk
                 hargaJualInput.value = Math.ceil(suggestedTotal * 0.95);
            }

            jumlahInput.setAttribute('data-old-value', newJumlah);
            suggestionNote.innerHTML = `Harga Satuan Dasar: **Rp ${formatRupiah(basePrice)}**. Total Saran (Diskon 5%): **Rp ${formatRupiah(suggestedTotal * 0.95)}** (Harga Standar: Rp ${formatRupiah(suggestedTotal)}).`;
        } else {
            suggestionNote.innerHTML = '⚠️ Harga dasar belum terhitung. Harap input harga jual secara manual.';
        }
    }
    document.addEventListener('DOMContentLoaded', calculatePriceSuggestion);


    // ===================================
    // EDIT FORM SETUP & VALIDATION
    // (Tidak ada perubahan, sudah benar)
    // ===================================

    /**
     * Menyiapkan modal edit sebelum ditampilkan
     */
    function setupEditModal(modalId, kuantitasId, basePrice, existingKuantitasJson) {
        // Hilangkan error yang mungkin ada dari sesi sebelumnya
        hideError(`modal-js-error-${kuantitasId}`);

        // Tampilkan modal
        toggleModal(modalId);
    }

    /**
     * Memvalidasi form edit kuantitas.
     */
    function validateEditForm(event, kuantitasId) {
        event.preventDefault();

        const form = document.getElementById(`editKuantitasForm-${kuantitasId}`);
        const modalErrorId = `modal-js-error-${kuantitasId}`;

        hideError(modalErrorId);

        const newJumlah = parseInt(document.getElementById(`modal_jumlah_${kuantitasId}`).value);
        const newHargaJual = parseInt(document.getElementById(`modal_harga_jual_${kuantitasId}`).value);

        const basePrice = parseFloat(document.getElementById(`edit_base_price_${kuantitasId}`).value);
        const existingItemsRaw = document.getElementById(`edit_existing_data_${kuantitasId}`).value;
        const existingItems = JSON.parse(existingItemsRaw);

        if (isNaN(newJumlah) || isNaN(newHargaJual)) {
             displayError('Pastikan semua input terisi dengan angka yang valid.', modalErrorId);
             return false;
        }

        // 1. Validasi Duplikasi Jumlah
        for (const id in existingItems) {
            const currentItem = existingItems[id];
            // Cek duplikasi jumlah dengan item lain (bukan item yang sedang diedit)
            if (parseInt(id) !== kuantitasId && currentItem.jumlah === newJumlah) {
                 displayError(`Nilai Jumlah (${newJumlah} pcs) sudah terdaftar pada varian lain untuk barang di warung ini.`, modalErrorId);
                 return false;
            }
        }

        // 2. Validasi Harga Jual minimal (Diskon Floor Check)
        if (basePrice > 0) {
            const unitPriceVariant = newHargaJual / newJumlah;
            const minAllowedUnitPrice = basePrice * MIN_UNIT_PRICE_RATIO;
            const minAllowedTotalPrice = minAllowedUnitPrice * newJumlah;

            if (unitPriceVariant < minAllowedUnitPrice) {
                displayError(`Harga Jual Total terlalu rendah! Harga satuan per unit setelah diskon tidak boleh kurang dari **${(MIN_UNIT_PRICE_RATIO * 100).toFixed(0)}%** dari harga dasar (Rp ${formatRupiah(basePrice)}). Minimal total yang diizinkan adalah **Rp ${formatRupiah(minAllowedTotalPrice)}**.`
                    , modalErrorId);
                return false;
            }
        }

        // Jika lolos semua validasi JS, submit form
        form.submit();
        return true;
    }

    // ===================================
    // DELETE MODAL (Menggantikan confirm)
    // ===================================

    /**
     * Menetapkan target form delete dan menampilkan modal.
     */
    function setDeleteTarget(actionUrl, itemName) {
        document.getElementById('delete-item-name').textContent = itemName;

        const globalForm = document.getElementById('global-delete-form');
        globalForm.action = actionUrl;

        toggleModal('deleteConfirmModal');
    }
</script>

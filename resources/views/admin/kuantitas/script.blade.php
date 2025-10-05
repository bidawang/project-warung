
<script>
    // ===================================
    // NEW MODAL TOGGLE FUNCTION
    // ===================================
    function toggleModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal.classList.contains('hidden')) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                modal.querySelector('div').classList.remove('scale-95', 'opacity-0');
            }, 10);
        } else {
            modal.querySelector('div').classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
            }, 300);
        }
    }

    // ===================================
    // GLOBAL VALIDATION FUNCTIONS (Create Form - Tetap)
    // ===================================
    function displayError(message) {
        const errorBox = document.getElementById('js-error-message');
        errorBox.innerHTML = '<strong>❌ Validasi Gagal:</strong> ' + message;
        errorBox.classList.remove('hidden');
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function hideError() {
        document.getElementById('js-error-message').classList.add('hidden');
    }

    function validateForm() {
        hideError();
        // ... (Logika validasi form Create tetap sama) ...
        const jumlahInput = document.getElementById('jumlah');
        const hargaJualInput = document.getElementById('harga_jual');
        const stokInfoBox = document.getElementById('stok-info-box');

        const newJumlah = parseInt(jumlahInput.value);
        const newHargaJual = parseInt(hargaJualInput.value);

        if (!stokInfoBox) return true;

        const existingJumlahs = stokInfoBox.dataset.existingJumlahs.split(',').map(Number).filter(n => n > 0);
        const existingHargas = stokInfoBox.dataset.existingHargas.split(',').map(Number).filter(n => n > 0);
        const basePrice = parseFloat(stokInfoBox.dataset.basePrice);

        if (existingJumlahs.includes(newJumlah)) {
            displayError(`Nilai **Jumlah** (${newJumlah} pcs) sudah terdaftar sebagai varian untuk barang ini. Mohon gunakan nilai jumlah yang berbeda.`);
            jumlahInput.focus();
            return false;
        }

        if (existingHargas.includes(newHargaJual)) {
            displayError(`Nilai **Harga Jual Total** (Rp ${newHargaJual.toLocaleString('id-ID')}) sudah terdaftar sebagai varian. Mohon gunakan harga yang berbeda.`);
            hargaJualInput.focus();
            return false;
        }

        if (basePrice > 0) {
            const thresholdPrice = basePrice * newJumlah;
            if (newHargaJual < thresholdPrice) {
                displayError(`Harga Jual Total terlalu rendah! Harga minimal yang disarankan adalah **Rp ${Math.ceil(thresholdPrice).toLocaleString('id-ID')}**.`);
                hargaJualInput.focus();
                return false;
            }
        }
        return true;
    }

    function calculatePriceSuggestion() {
        const jumlahInput = document.getElementById('jumlah');
        const hargaJualInput = document.getElementById('harga_jual');
        const stokInfoBox = document.getElementById('stok-info-box');
        const suggestionNote = document.getElementById('price-suggestion-note');

        const newJumlah = parseInt(jumlahInput.value);

        if (!stokInfoBox || !newJumlah || newJumlah < 1) return;

        const basePrice = parseFloat(stokInfoBox.dataset.basePrice);

        if (basePrice > 0) {
            const suggestedTotal = basePrice * newJumlah;
            const oldJumlahValue = parseInt(jumlahInput.getAttribute('data-old-value') || 0);
            const oldHargaValue = parseInt(hargaJualInput.value);

            if (hargaJualInput.value === '' || oldHargaValue === (basePrice * oldJumlahValue)) {
                 hargaJualInput.value = suggestedTotal;
            }

            jumlahInput.setAttribute('data-old-value', newJumlah);
            suggestionNote.innerHTML = `Harga Satuan Asumsi: **Rp ${basePrice.toLocaleString('id-ID')}**. Total Saran untuk ${newJumlah} pcs: **Rp ${suggestedTotal.toLocaleString('id-ID')}**`;
        } else {
            suggestionNote.innerHTML = '⚠️ Harga dasar belum terhitung. Harap input harga jual secara manual.';
        }
    }
    document.addEventListener('DOMContentLoaded', calculatePriceSuggestion);

    // ===================================
    // VALIDATION FORM EDIT (BARU - DENGAN ID SPESIFIK)
    // ===================================

    function validateEditForm(event, kuantitasId, basePrice, currentStokWarungId) {
        event.preventDefault();

        const modalError = document.getElementById(`modal-js-error-${kuantitasId}`);
        modalError.classList.add('hidden');

        const newJumlah = parseInt(document.getElementById(`modal_jumlah_${kuantitasId}`).value);
        const newHargaJual = parseInt(document.getElementById(`modal_harga_jual_${kuantitasId}`).value);

        // Ambil nilai original dari hidden input di form yang sama
        const form = document.getElementById(`editKuantitasForm-${kuantitasId}`);
        const originalJumlah = parseInt(form.querySelector('input[name="original_jumlah"]').value);
        const originalHargaJual = parseInt(form.querySelector('input[name="original_harga_jual"]').value);

        // Ambil SEMUA item yang memiliki Stok Warung ID yang SAMA
        const existingItems = document.querySelectorAll(`[data-stok-warung-id="${currentStokWarungId}"]`);
        let existingJumlahsSameSW = [];
        let existingHargasSameSW = [];

        existingItems.forEach(item => {
            const currentJumlah = parseInt(item.dataset.jumlah);
            const currentHarga = parseInt(item.dataset.hargaJual);

            // Kita hanya menambahkan nilai ke array jika nilainya BUKAN nilai ORIGINAL dari item yang sedang diedit
            if (currentJumlah !== originalJumlah) {
                existingJumlahsSameSW.push(currentJumlah);
            }
            if (currentHarga !== originalHargaJual) {
                existingHargasSameSW.push(currentHarga);
            }
        });

        // 1. Validasi Duplikasi Jumlah (dengan item lain di Stok Warung yang sama)
        if (existingJumlahsSameSW.includes(newJumlah)) {
            modalError.textContent = `Nilai Jumlah (${newJumlah} pcs) sudah terdaftar pada varian lain untuk barang di warung ini.`;
            modalError.classList.remove('hidden');
            return false;
        }

        // 2. Validasi Duplikasi Harga Jual (dengan item lain di Stok Warung yang sama)
        if (existingHargasSameSW.includes(newHargaJual)) {
            modalError.textContent = `Nilai Harga Jual Total (Rp ${newHargaJual.toLocaleString('id-ID')}) sudah terdaftar pada varian lain di warung ini.`;
            modalError.classList.remove('hidden');
            return false;
        }

        // 3. Validasi Harga Jual minimal
        if (basePrice > 0) {
            const thresholdPrice = basePrice * newJumlah;
            if (newHargaJual < thresholdPrice) {
                modalError.textContent = `Harga Jual Total terlalu rendah! Harga minimal yang disarankan adalah Rp ${Math.ceil(thresholdPrice).toLocaleString('id-ID')}.`;
                modalError.classList.remove('hidden');
                return false;
            }
        }

        // Jika lolos semua validasi JS, submit form
        form.submit();
        return true;
    }

    // ===================================
    // VALIDASI HAPUS (Delete) - Tetap
    // ===================================

    function confirmDelete(event, kuantitasId) {
        event.preventDefault();
        const confirmation = confirm(`Apakah Anda yakin ingin menghapus varian kuantitas ini ? Aksi ini tidak dapat dibatalkan.`);

        if (confirmation) {
            document.getElementById(`delete-form-${kuantitasId}`).submit();
        }

        return false;
    }
</script>

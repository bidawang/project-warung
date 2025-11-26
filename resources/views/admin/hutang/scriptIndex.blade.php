
<script>
    /**
     * Fungsi untuk Toggle View Compact/Detail
     */
    document.addEventListener('DOMContentLoaded', function() {
        const viewToggles = document.querySelectorAll('.view-toggle');
        const filterForm = document.getElementById('filter-form');
        const viewModeInput = document.getElementById('view-mode-input');

        viewToggles.forEach(button => {
            button.addEventListener('click', function() {
                const mode = this.getAttribute('data-mode');
                viewModeInput.value = mode;

                // Perbarui URL query parameter tanpa memicu reload, lalu kirimkan form
                const url = new URL(window.location);
                url.searchParams.set('view', mode);
                window.history.pushState({}, '', url);

                // Ganti class button
                viewToggles.forEach(btn => btn.classList.remove('bg-white', 'shadow', 'text-indigo-600', 'text-red-600'));

                if (mode === 'compact') {
                    this.classList.add('bg-white', 'shadow', 'text-indigo-600');
                    document.getElementById('toggle-detail').classList.remove('bg-white', 'shadow', 'text-red-600');
                } else {
                    this.classList.add('bg-white', 'shadow', 'text-red-600');
                    document.getElementById('toggle-compact').classList.remove('bg-white', 'shadow', 'text-indigo-600');
                }

                // Tampilkan/Sembunyikan Container
                document.getElementById('container-compact').style.display = mode === 'compact' ? 'flex' : 'none';
                document.getElementById('container-detail').style.display = mode === 'detail' ? 'block' : 'none';
            });
        });

        // Set initial state based on $viewMode (handle Laravel's render on reload)
        const initialMode = '{{ $viewMode }}';
        if (initialMode === 'compact') {
            document.getElementById('toggle-compact').click();
        } else {
            document.getElementById('toggle-detail').click();
        }


        // Global Search for Compact Mode
        document.querySelectorAll('.compact-search').forEach(input => {
            input.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();
                const targetId = this.getAttribute('data-target');
                const listContainer = document.querySelector(targetId);
                const items = listContainer.querySelectorAll('.hutang-item');
                let found = 0;

                items.forEach(item => {
                    const itemSearchTerm = item.getAttribute('data-search-term');
                    if (itemSearchTerm.includes(searchTerm)) {
                        item.style.display = 'block';
                        found++;
                    } else {
                        item.style.display = 'none';
                    }
                });

                const notFoundMessage = listContainer.querySelector('.not-found-message');
                if (notFoundMessage) {
                    notFoundMessage.style.display = found === 0 && items.length > 0 ? 'block' : 'none';
                }
                listContainer.querySelectorAll('.empty-row').forEach(row => row.style.display = 'none');
            });
        });

    });


    /**
     * Fungsi-fungsi untuk Modal Aturan Tenggat
     */
    function clearValidationErrors() {
        document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
        document.getElementById('modal-global-error').classList.add('hidden');
        document.getElementById('modal-global-error-list').innerHTML = '';
        document.querySelectorAll('#aturan-form input, #aturan-form select, #aturan-form textarea').forEach(el => {
            el.classList.remove('border-red-500');
            el.classList.add('border-gray-300');
        });
    }

    function openAturanModal(mode, data = null) {
        const modal = document.getElementById('aturan-tenggat-modal');
        const form = document.getElementById('aturan-form');
        const title = document.getElementById('modal-title');
        const formMethod = document.getElementById('form-method');
        const submitBtn = document.getElementById('modal-submit-btn');

        form.reset();
        clearValidationErrors();

        const updateRoute = (id) => `{{ url('admin/aturan-tenggat') }}/${id}`; // Menggunakan url() untuk route update
        const storeRoute = '{{ route('admin.aturanTenggat.store') }}'; // Menggunakan route() untuk route store

        if (mode === 'create') {
            title.textContent = 'Tambah Aturan Tenggat Baru';
            submitBtn.textContent = 'Simpan Aturan';
            formMethod.value = 'POST';
            form.action = storeRoute;
        } else if (mode === 'edit' && data) {
            title.textContent = `Edit Aturan Tenggat untuk ${data.warung ? data.warung.nama_warung : 'Warung #' + data.id_warung}`;
            submitBtn.textContent = 'Update Aturan';
            formMethod.value = 'PUT'; // Metode override untuk PUT
            form.action = updateRoute(data.id);

            // Isi data ke form
            document.getElementById('id_warung').value = data.id_warung;
            document.getElementById('tanggal_awal').value = data.tanggal_awal;
            document.getElementById('tanggal_akhir').value = data.tanggal_akhir;
            document.getElementById('jatuh_tempo_hari').value = data.jatuh_tempo_hari;
            document.getElementById('bunga').value = data.bunga;
            document.getElementById('keterangan').value = data.keterangan || '';
        }

        modal.classList.remove('hidden');
    }

    function closeAturanModal() {
        document.getElementById('aturan-tenggat-modal').classList.add('hidden');
        clearValidationErrors();
    }

    /**
     * Submit Form dengan Fetch API untuk menampilkan Error Validasi
     */
    document.getElementById('aturan-form').addEventListener('submit', function(e) {
        e.preventDefault();

        const form = e.target;
        const formData = new FormData(form);
        const actionUrl = form.action;
        const method = document.getElementById('form-method').value;

        // Siapkan data untuk fetch
        let data = {};
        formData.forEach((value, key) => (data[key] = value));

        clearValidationErrors();

        fetch(actionUrl, {
            method: 'POST', // Selalu POST di sini, method PUT/DELETE di-override via _method
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Content-Type': 'application/json',
                'X-HTTP-Method-Override': method // Laravel akan membaca header ini
            },
            body: JSON.stringify(data)
        })
        .then(response => {
            if (response.ok) {
                // Sukses
                alert('Aturan tenggat berhasil disimpan!');
                window.location.reload();
            } else if (response.status === 422) {
                // Error Validasi
                return response.json().then(data => {
                    handleValidationErrors(data.errors);
                });
            } else {
                // Error Server lainnya
                alert('Terjadi kesalahan server saat menyimpan data.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan koneksi saat menyimpan aturan tenggat.');
        });
    });

    /**
     * Menangani Error Validasi dari Laravel (422)
     */
    function handleValidationErrors(errors) {
        let globalErrors = [];

        for (const field in errors) {
            if (errors.hasOwnProperty(field)) {
                const message = errors[field][0];
                const errorElement = document.querySelector(`.error-message[data-field="${field}"]`);
                const inputElement = document.getElementById(field);

                if (errorElement) {
                    errorElement.textContent = message;
                } else if (inputElement) {
                    // Jika error spesifik tapi elemen error-message tidak ditemukan, tandai input
                    inputElement.classList.add('border-red-500');
                    inputElement.classList.remove('border-gray-300');
                    // Tambahkan ke daftar global jika tidak ada tempat untuk menampilkannya
                    globalErrors.push(message);
                } else {
                    // Ini menangani error seperti 'tanggal_awal' atau 'tanggal_akhir'
                    // yang memiliki pesan validasi tumpang tindih.
                    globalErrors.push(message);
                }
            }
        }

        // Tampilkan pesan error global yang mungkin terlewat atau error non-field (seperti dari validateDateOverlap)
        if (globalErrors.length > 0) {
            const globalErrorContainer = document.getElementById('modal-global-error');
            const globalErrorList = document.getElementById('modal-global-error-list');
            globalErrors.forEach(msg => {
                const li = document.createElement('li');
                li.textContent = msg;
                globalErrorList.appendChild(li);
            });
            globalErrorContainer.classList.remove('hidden');
        }
    }


    /**
     * Fungsi untuk Delete Aturan Tenggat
     */
    function deleteAturan(id) {
        if (confirm('Apakah Anda yakin ingin menghapus aturan tenggat ini? Tindakan ini tidak dapat dibatalkan.')) {
            const deleteUrl = `{{ url('admin/aturan-tenggat') }}/${id}`;

            fetch(deleteUrl, {
                method: 'POST', // Selalu POST di sini
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'X-HTTP-Method-Override': 'DELETE' // Kirim method override
                },
                body: JSON.stringify({})
            })
            .then(response => {
                if (response.ok) {
                    alert('Aturan tenggat berhasil dihapus!');
                    window.location.reload();
                } else {
                    response.json().then(data => {
                         alert(`Gagal menghapus aturan tenggat: ${data.message || 'Terjadi kesalahan.'}`);
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan koneksi saat menghapus aturan tenggat.');
            });
        }
    }
</script>

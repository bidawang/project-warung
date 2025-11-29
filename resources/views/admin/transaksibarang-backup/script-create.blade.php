
<script>
    // Pastikan jQuery sudah dimuat sebelum Select2
    document.addEventListener('DOMContentLoaded', () => {
        let areaIndex = 0;
        const areaContainer = document.getElementById('areaContainer');
        const btnAddArea = document.getElementById('btnAddArea');
        const allAreas = @json($areas->pluck('id')); // semua ID area
        const searchInput = document.getElementById('searchRencana');

        // --- FUNGSI SELECT2 BARU ---
        // Fungsi untuk inisialisasi Select2 pada semua select-barang
        function initializeSelect2() {
            // Hancurkan Select2 yang mungkin sudah ada sebelum inisialisasi ulang
            $('.select-barang').each(function() {
                if ($(this).data('select2')) {
                    $(this).select2('destroy');
                }
            });

            // Inisialisasi Select2 pada semua select-barang
            $('.select-barang').select2({
                placeholder: 'Pilih Barang',
                allowClear: true,
                width: '100%'
            });
        }

        // Panggil inisialisasi saat DOMContentLoaded
        initializeSelect2();
        // --- END FUNGSI SELECT2 BARU ---


        // Inisialisasi index area pada blok pertama
        areaContainer.querySelector('.area-index-label').textContent = '1';

        // Fungsi JS untuk Pencarian/Filter Rencana Belanja (TETAP SAMA)
        // ... (window.filterRencanaBelanja function) ...
        window.filterRencanaBelanja = function() {
            // (Isi fungsi filterRencanaBelanja() tetap sama)
            const filter = searchInput.value.toLowerCase();

            // Dapatkan view yang sedang aktif
            const activeViewId = document.querySelector('.view-content:not(.hidden)').id;

            // Jika view-nya adalah viewBarang, tampilkan/sembunyikan juga Total Kebutuhan
            const viewBarangTotal = document.getElementById('viewBarangTotal');
            if (activeViewId === 'viewBarang') {
                // Untuk view Barang, kita filter total list-nya dan juga detailnya

                // Filter Total Kebutuhan Barang
                const listTotalBarang = document.getElementById('listTotalBarang');
                let totalFoundInTotalList = 0;
                listTotalBarang.querySelectorAll('li').forEach(item => {
                    const namaBarang = item.dataset.itemNama.toLowerCase();
                    if (namaBarang.includes(filter)) {
                        item.classList.remove('hidden');
                        totalFoundInTotalList++;
                    } else {
                        item.classList.add('hidden');
                    }
                });
                // Sembunyikan/tampilkan blok Total Kebutuhan jika tidak ada hasil
                if (filter.length > 0 && totalFoundInTotalList === 0) {
                    viewBarangTotal.classList.add('hidden');
                } else {
                    viewBarangTotal.classList.remove('hidden');
                }
            } else if (activeViewId === 'viewWarung') {
                 // Pastikan Total Kebutuhan tersembunyi jika view Warung
                 if(viewBarangTotal) viewBarangTotal.classList.add('hidden');
            }


            // Filter Item Block (viewWarung atau viewBarang detail)
            document.querySelectorAll('.view-content:not(.hidden) .item-block').forEach(block => {
                const namaUtama = block.dataset.namaUtama.toLowerCase(); // Warung atau Barang
                let found = false;

                // Cek nama utama (Warung atau Barang)
                if (namaUtama.includes(filter)) {
                    found = true;
                }

                // Cek item detail di dalam blok (Barang atau Warung)
                block.querySelectorAll('li').forEach(listItem => {
                    const itemNama = listItem.dataset.itemNama.toLowerCase(); // Barang atau Warung
                    if (itemNama.includes(filter)) {
                        found = true;
                        listItem.classList.remove('hidden');
                    } else {
                        listItem.classList.add('hidden');
                    }
                });

                // Tampilkan/Sembunyikan blok
                if (found) {
                    block.classList.remove('hidden');
                } else {
                    block.classList.add('hidden');
                }
            });

        };
        // END: Fungsi JS untuk Pencarian
    $(document).ready(function() {
        $('.select-barang').select2();
    });

        // Fungsi update nama input untuk index area yang benar (TETAP SAMA)
        function reindexAreaBlocks() {
            areaContainer.querySelectorAll('.area-block').forEach((areaBlock, index) => {
                areaBlock.querySelector('.area-index-label').textContent = (index + 1);

                areaBlock.querySelectorAll('select[name^="id_barang"], input[name^="jumlah"], input[name^="total_harga"], input[name^="tanggal_kadaluarsa"]').forEach(el => {
                    if (el.name.includes('id_barang')) el.name = `id_barang[${index}][]`;
                    else if (el.name.includes('jumlah')) el.name = `jumlah[${index}][]`;
                    else if (el.name.includes('total_harga')) el.name = `total_harga[${index}][]`;
                    else if (el.name.includes('tanggal_kadaluarsa')) el.name = `tanggal_kadaluarsa[${index}][]`;
                });
            });
            // Update areaIndex global
            areaIndex = areaContainer.querySelectorAll('.area-block').length - 1;
        }

        // Fungsi cek area duplikat & update tombol (TETAP SAMA)
        function updateAreaOptions() {
            const selected = Array.from(document.querySelectorAll('.select-area'))
                .map(sel => sel.value).filter(v => v);

            // Disable opsi yang sudah dipilih di select lain
            document.querySelectorAll('.select-area').forEach(sel => {
                sel.querySelectorAll('option').forEach(opt => {
                    if (opt.value && selected.includes(opt.value) && sel.value !== opt.value) {
                        opt.disabled = true;
                    } else {
                        opt.disabled = false;
                    }
                });
            });

            // Disable tombol tambah area jika semua sudah dipilih
            if (selected.length >= allAreas.length) {
                btnAddArea.disabled = true;
                btnAddArea.classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                btnAddArea.disabled = false;
                btnAddArea.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        }

        // Fungsi untuk mengkalkulasi total harga (TETAP SAMA)
        function calculateTotalHarga(row) {
            const selectBarang = row.querySelector('.select-barang');
            const inputJumlah = row.querySelector('.input-jumlah');
            const totalHargaInput = row.querySelector('.total-harga');

            // Ambil harga dari data-harga attribute pada option yang terpilih
            const selectedOption = selectBarang.options[selectBarang.selectedIndex];
            const hargaPerBarang = selectedOption ? selectedOption.getAttribute('data-harga') : null;
            const jumlah = inputJumlah.value;

            if (hargaPerBarang && jumlah && jumlah > 0) {
                const total = parseFloat(hargaPerBarang) * parseInt(jumlah);
                totalHargaInput.value = total;
            } else {
                totalHargaInput.value = 0;
            }
        }

        // Tambah Area baru
        btnAddArea.addEventListener('click', () => {
            // Hancurkan Select2 sebelum kloning
            areaContainer.querySelectorAll('.select-barang').forEach(sel => {
                if ($(sel).data('select2')) {
                    $(sel).select2('destroy');
                }
            });

            const newArea = areaContainer.querySelector('.area-block').cloneNode(true);

            // Inisialisasi ulang Select2 pada semua select (termasuk yang dikloning)
            setTimeout(initializeSelect2, 0);
            // Timeout 0ms untuk memastikan kloning selesai dan DOM diperbarui sebelum inisialisasi

            // Reset select & input
            newArea.querySelector('select[name="id_area[]"]').value = "";
            newArea.querySelectorAll('tbody tr').forEach((tr, i) => {
                if (i > 0) tr.remove(); // Hapus baris barang selain baris pertama
            });

            // Reset dan atur ulang baris barang pertama
            const firstRow = newArea.querySelector('tbody tr');
            firstRow.querySelector('.select-barang').value = "";
            firstRow.querySelector('.input-jumlah').value = "";
            firstRow.querySelector('.total-harga').value = "";
            firstRow.querySelector('.input-tgl-kadaluarsa').value = "";

            areaContainer.appendChild(newArea);
            reindexAreaBlocks();
            updateAreaOptions();

            // Panggil inisialisasi Select2 lagi setelah penambahan area
            // Perlu dipanggil lagi karena kloning merusak Select2, dan reindex
            initializeSelect2();
        });

        // Delegasi event untuk perubahan area, hapus area, tambah/hapus row, dan input barang (TETAP SAMA)
        document.addEventListener('change', e => {
            if (e.target.classList.contains('select-area')) {
                updateAreaOptions();
            } else if (e.target.classList.contains('select-barang')) {
                // Select2 memicu event change pada select asli.
                calculateTotalHarga(e.target.closest('tr'));
            }
        });

        document.addEventListener('input', e => {
            if (e.target.classList.contains('input-jumlah')) {
                calculateTotalHarga(e.target.closest('tr'));
            }
        });

        document.addEventListener('click', e => {
            if (e.target.classList.contains('btn-remove-area')) {
                if (areaContainer.querySelectorAll('.area-block').length > 1) {
                    // Hancurkan Select2 sebelum remove
                    e.target.closest('.area-block').querySelectorAll('.select-barang').forEach(sel => {
                        if ($(sel).data('select2')) {
                            $(sel).select2('destroy');
                        }
                    });

                    e.target.closest('.area-block').remove();
                    reindexAreaBlocks(); // Perlu re-index setelah dihapus
                    updateAreaOptions();

                    // Inisialisasi ulang Select2 pada area yang tersisa
                    initializeSelect2();

                } else {
                    alert('Minimal harus ada 1 area pembelian.');
                }
            }
            if (e.target.classList.contains('btn-remove-row')) {
                const tbody = e.target.closest('tbody');
                if (tbody.rows.length > 1) {
                    // Hancurkan Select2 sebelum remove
                    const selectBarang = e.target.closest('tr').querySelector('.select-barang');
                    if ($(selectBarang).data('select2')) {
                        $(selectBarang).select2('destroy');
                    }

                    e.target.closest('tr').remove();
                    // Tidak perlu initializeSelect2 karena hanya 1 baris yang dihapus
                } else {
                    alert('Minimal harus ada 1 barang di area ini.');
                }
            }
            if (e.target.classList.contains('btn-add-row')) {
                const tbody = e.target.closest('.area-block').querySelector('tbody');
                const areaBlock = e.target.closest('.area-block');
                const areaIndex = Array.from(areaContainer.children).indexOf(areaBlock);

                // Clone the row using jQuery to handle Select2 properly
                // Hancurkan Select2 pada baris template sebelum kloning
                const templateRow = tbody.querySelector('tr');
                const selectBarangTemplate = templateRow.querySelector('.select-barang');
                if ($(selectBarangTemplate).data('select2')) {
                    $(selectBarangTemplate).select2('destroy');
                }

                // Clone baris template
                const newRow = templateRow.cloneNode(true);

                // Reset values and update names for the new row
                newRow.querySelectorAll('select, input').forEach(el => {
                    if (el.name.includes('id_barang')) el.name = `id_barang[${areaIndex}][]`;
                    else if (el.name.includes('jumlah')) el.name = `jumlah[${areaIndex}][]`;
                    else if (el.name.includes('total_harga')) el.name = `total_harga[${areaIndex}][]`;
                    else if (el.name.includes('tanggal_kadaluarsa')) el.name = `tanggal_kadaluarsa[${areaIndex}][]`;

                    el.value = (el.tagName === 'SELECT') ? "" : (el.type === 'number' ? "" : "");
                    if(el.classList.contains('total-harga')) el.value = "0"; // Pastikan 0 atau kosong
                });

                tbody.appendChild(newRow);

                // Panggil inisialisasi Select2 untuk semua select-barang di area ini
                initializeSelect2();
            }

            // ... (Aksi Lain-Lain dan Toggle View tetap sama) ...

            // Aksi Tambah Transaksi Lain-Lain
            if (e.target.classList.contains('btn-remove-lain')) {
                e.target.closest('.lain-block').remove();
                const lainContainer = document.getElementById('lainContainer');
                if (!lainContainer.querySelector('.lain-block')) {
                    lainContainer.classList.add('hidden');
                }
            }

            // Toggle Tampilan Rencana Belanja
            if (e.target.classList.contains('btn-view-toggle')) {
                document.querySelectorAll('.btn-view-toggle').forEach(btn => {
                    btn.classList.remove('bg-blue-600', 'text-white');
                    btn.classList.add('bg-gray-300', 'text-gray-800');
                });

                e.target.classList.remove('bg-gray-300', 'text-gray-800');
                e.target.classList.add('bg-blue-600', 'text-white');

                const targetView = e.target.dataset.view;

                document.querySelectorAll('.view-content').forEach(content => {
                    content.classList.add('hidden');
                });

                document.getElementById('view' + targetView.charAt(0).toUpperCase() + targetView.slice(1)).classList.remove('hidden');

                // Panggil filter ulang setelah ganti view
                filterRencanaBelanja();
            }

            // Menambahkan item rencana belanja ke form
            // CATATAN: Karena tombol [+] DIHILANGKAN, bagian ini (btn-add-item-to-form) seharusnya tidak akan terpanggil
            // Namun, jika Anda ingin mengaktifkannya kembali, pastikan Anda menambahkan tombol [+] ke Blade.
            if (e.target.classList.contains('btn-add-item-to-form')) {
                const itemId = e.target.dataset.itemId;
                const itemNama = e.target.dataset.itemNama;
                const itemHarga = e.target.dataset.itemHarga;

                // Temukan area block pertama atau area yang sesuai
                const firstAreaBlock = areaContainer.querySelector('.area-block');
                const tbody = firstAreaBlock.querySelector('tbody');
                const areaIndex = Array.from(areaContainer.children).indexOf(firstAreaBlock);

                // ... (Logika penambahan item ke form tetap sama) ...
            }
        });


        // Transaksi Lain-Lain (tetap seperti sebelumnya)
        const lainContainer = document.getElementById('lainContainer');
        const btnAddLain = document.getElementById('btnAddLain');

        btnAddLain.addEventListener('click', () => {
            if (lainContainer.classList.contains('hidden')) {
                lainContainer.classList.remove('hidden');
            }

            const newLain = document.createElement('div');
            newLain.className = "lain-block border rounded-lg p-4 bg-gray-50";
            newLain.innerHTML = `
                <div class="flex justify-between items-center mb-3">
                    <h3 class="font-semibold text-gray-700">Item Lain-Lain</h3>
                    <button type="button"
                        class="btn-remove-lain text-red-600 hover:text-red-800 font-bold text-xl leading-none">&times;</button>
                </div>
                <div class="mb-3">
                    <label class="block text-sm text-gray-700 mb-1">Keterangan</label>
                    <input type="text" name="lain_keterangan[]" class="w-full border rounded-lg px-3 py-2"
                        placeholder="Misal: Ongkos kirim">
                </div>
                <div class="mb-3">
                    <label class="block text-sm text-gray-700 mb-1">Harga (Rp)</label>
                    <input type="number" name="lain_harga[]" class="w-full border rounded-lg px-3 py-2" min="0">
                </div>
            `;
            lainContainer.appendChild(newLain);
        });

        // Inisialisasi awal
        updateAreaOptions();
    });
</script>

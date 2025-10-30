@extends('layouts.app')

@section('title', 'Halaman Kasir - Transaksi Cepat')

@section('content')
    <div class="container-fluid mt-4">
        <div class="row g-4">
            {{-- BAGIAN DAFTAR PRODUK (KIRI) --}}
            <div class="col-lg-8 col-xl-7">
                <div class="card shadow-lg border-0 h-100">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-boxes me-2"></i>Daftar Produk</h5>
                    </div>
                    <div class="card-body p-3">
                        {{-- Alert Session --}}
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        {{-- Search & Filter --}}
                        <div class="input-group mb-3 sticky-top p-0 bg-white" style="top: -16px; z-index: 10;">
                            <span class="input-group-text bg-light"><i class="fas fa-search"></i></span>
                            <input type="text" id="searchInput" class="form-control form-control-lg"
                                placeholder="Cari produk berdasarkan nama...">
                        </div>

                        {{-- Product Grid --}}
                        <div class="row g-3 product-grid" id="productList">
                            @forelse($products as $product)
                                <div class="col-6 col-sm-4 col-md-3 col-xl-2 d-flex product-item"
                                    data-name="{{ strtolower($product->barang->nama_barang ?? '') }}"
                                    data-stock="{{ $product->stok_saat_ini }}" data-stok-id="{{ $product->id }}"
                                    data-harga-jual-dasar="{{ $product->harga_jual }}"
                                    data-kuantitas-list="{{ json_encode($product->kuantitas_list) }}">

                                    <button type="button"
                                        class="card product-card text-decoration-none shadow-sm h-100 w-100 border-0 rounded-3 text-start add-to-cart-btn"
                                        data-id="{{ $product->id }}"
                                        title="{{ $product->barang->nama_barang ?? '-' }} (Stok: {{ $product->stok_saat_ini }})">
                                        <div class="card-body p-2 d-flex flex-column justify-content-between">
                                            <div class="d-flex align-items-center mb-1">
                                                <div class="product-initial-placeholder bg-primary text-white fw-bold flex-shrink-0 me-2"
                                                    style="width: 40px; height: 40px; font-size: 1.1em; border-radius: 6px;">
                                                    <?php
                                                    $nama_barang = $product->barang->nama_barang ?? 'P';
                                                    $words = explode(' ', trim($nama_barang));
                                                    $initials = '';
                                                    $initials .= strtoupper(substr($words[0], 0, 1));
                                                    if (isset($words[1])) {
                                                        $initials .= strtoupper(substr($words[1], 0, 1));
                                                    } elseif (strlen($words[0]) > 1) {
                                                        $initials .= strtoupper(substr($words[0], 1, 1));
                                                    }
                                                    ?>
                                                    {{ $initials }}
                                                </div>
                                                <h6 class="card-title my-0 flex-grow-1 text-truncate"
                                                    style="font-size: 0.9em;">
                                                    {{ $product->barang->nama_barang ?? '-' }}
                                                </h6>
                                            </div>

                                            <div class="text-end">
                                                <p class="card-text mb-0 text-success fw-bold" style="font-size: 1em;">
                                                    Rp {{ number_format($product->harga_jual, 0, ',', '.') }}
                                                </p>
                                                <small class="text-muted" style="font-size: 0.8em;">Stok: <span
                                                        class="product-stock-count">{{ $product->stok_saat_ini }}</span></small>
                                            </div>
                                        </div>
                                    </button>
                                </div>
                            @empty
                                <div class="col-12">
                                    <p class="text-center text-muted py-5"><i class="fas fa-times-circle me-2"></i>Tidak ada
                                        produk tersedia di warung ini.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- BAGIAN KERANJANG & TRANSAKSI (KANAN) --}}
            <div class="col-lg-4 col-xl-5">
                <div class="card shadow-lg border-0 h-100">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Keranjang Belanja</h5>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <form id="checkoutForm" action="{{ route('kasir.barangkeluar.store') }}" method="POST"
                            class="flex-grow-1 d-flex flex-column">
                            @csrf
                            <input type="hidden" name="id_warung" value="{{ session('id_warung') }}">

                            {{-- Daftar Item Keranjang --}}
                            <div class="cart-scroll-area flex-grow-1 mb-3" style="max-height: 400px; overflow-y: auto;">
                                <ul class="list-group" id="cartList">
                                    <li class="list-group-item text-center text-muted" id="emptyCartMessage">Keranjang
                                        kosong. Tambahkan produk!</li>
                                </ul>
                            </div>

                            <hr class="my-2">

                            {{-- Total Harga --}}
                            <div class="d-flex justify-content-between font-weight-bold fs-5 mb-3">
                                <span>Total:</span>
                                <span id="totalPrice" class="text-success fw-bolder">Rp 0</span>
                                <input type="hidden" name="total_harga" id="totalPriceInput" value="0">
                            </div>

                            <hr class="my-3">

                            {{-- Jenis Transaksi --}}
                            <div class="form-group mb-3">
                                <label for="transactionType" class="form-label fw-bold">Jenis Transaksi:</label>
                                <select class="form-select form-select-lg" id="transactionType" name="jenis">
                                    <option value="penjualan barang">Penjualan (Bayar Tunai)</option>
                                    <option value="hutang barang">Hutang (Member)</option>
                                </select>
                            </div>

                            {{-- Member Select Section (Hutang) --}}
                            <div class="form-group mb-3" id="memberSelectSection" style="display: none;">
                                <label for="memberSelect" class="form-label fw-bold">Pilih Member:</label>
                                <div class="input-group">
                                    <select class="form-select form-select-lg" id="memberSelect" name="id_user_member">
                                        <option value="">-- Pilih Member --</option>
                                        @foreach ($members as $member)
                                            <option value="{{ $member->id }}">{{ $member->name }}
                                                ({{ $member->email }})</option>
                                        @endforeach
                                    </select>
                                    <button type="button" class="btn btn-primary"
                                        onclick="window.location='{{ url('kasir/member/create') }}'">
                                        <i class="fas fa-plus"></i> Tambah
                                    </button>
                                </div>
                            </div>


                            {{-- Payment Section (Penjualan) --}}
                            <div id="paymentSection">
                                <div class="form-group mb-3">
                                    <label for="payment" class="form-label fw-bold">Bayar (Uang Tunai):</label>
                                    <input type="number" class="form-control form-control-lg text-end" id="payment"
                                        name="bayar" placeholder="0" min="0" value="0">
                                </div>
                                <div class="form-group mb-3">
                                    <label for="change" class="form-label fw-bold">Kembalian:</label>
                                    <input type="text"
                                        class="form-control form-control-lg text-end fw-bolder text-primary"
                                        id="change" value="Rp 0" readonly>
                                </div>
                            </div>

                            {{-- Checkout Button --}}
                            <div class="d-grid gap-2 mt-auto pt-2">
                                <button type="submit" class="btn btn-success btn-lg fw-bold" id="checkoutBtn" disabled>
                                    <i class="fas fa-cash-register me-2"></i>Selesaikan Transaksi
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cartList = document.getElementById('cartList');
            const totalPriceEl = document.getElementById('totalPrice');
            const totalPriceInput = document.getElementById('totalPriceInput');
            const checkoutBtn = document.getElementById('checkoutBtn');
            const searchInput = document.getElementById('searchInput');
            const productList = document.getElementById('productList');
            const transactionTypeSelect = document.getElementById('transactionType');
            const memberSelectSection = document.getElementById('memberSelectSection');
            const paymentSection = document.getElementById('paymentSection');
            const paymentInput = document.getElementById('payment');
            const changeInput = document.getElementById('change');
            const memberSelect = document.getElementById('memberSelect');
            const checkoutForm = document.getElementById('checkoutForm');
            const emptyCartMessage = document.getElementById('emptyCartMessage');

            let cart = {}; // Object untuk menyimpan item di keranjang

            // Fungsi format angka ke Rupiah
            function formatRupiah(number) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(number).replace('IDR', 'Rp');
            }

            // Fungsi hitung harga total berdasarkan kuantitas (bundle)
            function calculateHargaTotal(kuantitasList, hargaJualDasar, qty) {
                let sisaQty = qty;
                let totalHarga = 0;
                // Urutkan bundle dari jumlah terbanyak
                kuantitasList.sort((a, b) => b.jumlah - a.jumlah);

                for (const bundle of kuantitasList) {
                    const bundleQty = bundle.jumlah;
                    const bundleHarga = bundle.harga_jual;
                    if (bundleQty <= 0) continue;

                    const kelipatan = Math.floor(sisaQty / bundleQty);
                    if (kelipatan > 0) {
                        totalHarga += kelipatan * bundleHarga;
                        sisaQty -= kelipatan * bundleQty;
                    }
                }
                // Sisa yang tidak masuk dalam bundle, dihitung dengan harga jual dasar
                if (sisaQty > 0) {
                    totalHarga += sisaQty * hargaJualDasar;
                }
                return totalHarga;
            }

            // Fungsi untuk menghitung harga per unit item yang ditampilkan di keranjang (untuk referensi)
            function getCartUnitPrice(kuantitasList, hargaJualDasar) {
                const bundleOneUnit = kuantitasList.find(b => b.jumlah === 1);
                return bundleOneUnit ? bundleOneUnit.harga_jual : hargaJualDasar;
            }

            // Update tampilan keranjang dan total harga
            function updateCart() {
                cartList.innerHTML = '';
                let total = 0;
                const cartKeys = Object.keys(cart);

                if (cartKeys.length === 0) {
                    cartList.appendChild(emptyCartMessage);
                } else {
                    for (const id of cartKeys) {
                        const item = cart[id];
                        total += item.totalPrice;

                        const li = document.createElement('li');
                        li.className = 'list-group-item d-flex justify-content-between align-items-center p-2';
                        li.innerHTML = `
                        <div class="flex-grow-1 me-2">
                            <h6 class="my-0 text-capitalize">${item.name}</h6>
                            <small class="text-muted">@ ${formatRupiah(item.unitPrice)}</small>
                            <input type="hidden" name="items[${item.stokId}][id_stok_warung]" value="${item.stokId}">
                            <input type="hidden" name="items[${item.stokId}][jumlah]" value="${item.quantity}">
                            <input type="hidden" name="items[${item.stokId}][harga]" value="${item.totalPrice}">
                        </div>
                        <div class="d-flex align-items-center flex-shrink-0">

                            {{-- Kontrol Kuantitas: Tombol Minus, Input, Tombol Plus --}}
                            <div class="input-group input-group-sm" style="width: 120px;">
                                <button type="button" class="btn btn-outline-secondary cart-qty-btn-minus" data-stok-id="${id}" ${item.quantity <= 1 ? 'disabled' : ''}>
                                    <i class="fas fa-minus fa-xs"></i>
                                </button>
                                <input type="number"
                                    class="form-control text-center cart-qty-input"
                                    value="${item.quantity}"
                                    min="1"
                                    max="${item.maxStock}"
                                    data-stok-id="${id}">
                                <button type="button" class="btn btn-outline-secondary cart-qty-btn-plus" data-stok-id="${id}" ${item.quantity >= item.maxStock ? 'disabled' : ''}>
                                    <i class="fas fa-plus fa-xs"></i>
                                </button>
                            </div>

                            {{-- Total Harga Item --}}
                            <span class="text-muted ms-3 fw-bold item-total-price-display" style="width: 100px; text-align: right; font-size: 0.9em;">
                                ${formatRupiah(item.totalPrice)}
                            </span>

                            {{-- Tombol Hapus Penuh --}}
                            <button type="button" class="btn btn-sm btn-outline-danger ms-2 remove-full-item-btn" data-id="${id}" title="Hapus Item">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    `;
                        cartList.appendChild(li);
                    }
                }

                totalPriceEl.textContent = formatRupiah(total);
                totalPriceInput.value = total;
                calculateChange();
                checkoutBtn.disabled = cartKeys.length === 0;
            }
            // Fungsi untuk mengupdate kuantitas item di keranjang
            function updateCartItemQuantity(stokId, newQty) {
                const item = cart[stokId];
                if (!item) return;

                // Pastikan kuantitas berada dalam batas 1 hingga maxStock
                if (newQty < 1) newQty = 1;
                if (newQty > item.maxStock) {
                    alert(`Kuantitas maksimal untuk produk "${item.name}" adalah ${item.maxStock}.`);
                    newQty = item.maxStock;
                }

                item.quantity = newQty;
                item.totalPrice = calculateHargaTotal(item.kuantitasList, item.hargaJualDasar, item.quantity);
                updateCart();
            }

            // Event listener untuk tombol Plus (+)
            document.body.addEventListener('click', function(e) {
                if (e.target.closest('.cart-qty-btn-plus')) {
                    const stokId = e.target.closest('.cart-qty-btn-plus').dataset.stokId;
                    const item = cart[stokId];
                    if (item && item.quantity < item.maxStock) {
                        updateCartItemQuantity(stokId, item.quantity + 1);
                    } else if (item) {
                        alert(`Kuantitas maksimal untuk produk "${item.name}" adalah ${item.maxStock}.`);
                    }
                }
            });

            // Event listener untuk tombol Minus (-)
            document.body.addEventListener('click', function(e) {
                if (e.target.closest('.cart-qty-btn-minus')) {
                    const stokId = e.target.closest('.cart-qty-btn-minus').dataset.stokId;
                    const item = cart[stokId];
                    if (item && item.quantity > 1) {
                        updateCartItemQuantity(stokId, item.quantity - 1);
                    }
                }
            });

            // *MEMPERBARUI* Event listener input kuantitas di keranjang (agar menggunakan fungsi baru)
            document.body.addEventListener('change', function(e) {
                if (e.target.classList.contains('cart-qty-input')) {
                    const input = e.target;
                    const stokId = input.dataset.stokId;
                    let newQty = parseInt(input.value);

                    if (isNaN(newQty) || newQty < 1) {
                        newQty = 1;
                    }

                    updateCartItemQuantity(stokId, newQty);
                }
            });

            // Hitung dan tampilkan kembalian
            function calculateChange() {
                const total = parseFloat(totalPriceInput.value) || 0;
                const payment = parseFloat(paymentInput.value) || 0;
                const change = payment - total;

                // Perbarui warna tombol checkout berdasarkan status pembayaran
                if (transactionTypeSelect.value === 'penjualan barang') {
                    if (payment >= total && total > 0) {
                        checkoutBtn.classList.remove('btn-danger', 'btn-warning');
                        checkoutBtn.classList.add('btn-success');
                        checkoutBtn.textContent = 'Selesaikan Transaksi';
                        checkoutBtn.disabled = false;
                    } else if (payment > 0 && payment < total) {
                        checkoutBtn.classList.remove('btn-success', 'btn-danger');
                        checkoutBtn.classList.add('btn-warning');
                        checkoutBtn.textContent = 'Pembayaran Kurang';
                        checkoutBtn.disabled = true;
                    } else {
                        checkoutBtn.classList.remove('btn-danger', 'btn-warning');
                        checkoutBtn.classList.add('btn-success');
                        checkoutBtn.textContent = 'Selesaikan Transaksi';
                        checkoutBtn.disabled = total === 0;
                    }
                    changeInput.value = formatRupiah(change >= 0 ? change : change * -1); // Tampilkan selisih
                    changeInput.classList.toggle('text-danger', change < 0);
                    changeInput.classList.toggle('text-primary', change >= 0);
                } else if (transactionTypeSelect.value === 'hutang barang') {
                    // Untuk hutang, kembalian selalu Rp 0 dan tombol hijau jika ada member terpilih
                    changeInput.value = formatRupiah(0);
                    checkoutBtn.classList.remove('btn-danger', 'btn-warning');
                    checkoutBtn.classList.add('btn-success');
                    checkoutBtn.textContent = 'Ajukan Hutang';
                    checkoutBtn.disabled = total === 0 || !memberSelect.value;
                }
            }

            // Event listener untuk tombol tambah produk ke keranjang
            document.body.addEventListener('click', function(e) {
                if (e.target.closest('.add-to-cart-btn')) {
                    const button = e.target.closest('.add-to-cart-btn');
                    const productCard = button.closest('.product-item');
                    const stokId = productCard.dataset.stokId;
                    const name = productCard.dataset.name.toUpperCase();
                    const maxStock = parseInt(productCard.dataset.stock);
                    const hargaJualDasar = parseFloat(productCard.dataset.hargaJualDasar);
                    const kuantitasList = JSON.parse(productCard.dataset.kuantitasList);

                    if (maxStock < 1) {
                        alert('Stok produk ini sudah habis.');
                        return;
                    }

                    if (!cart[stokId]) {
                        cart[stokId] = {
                            stokId: stokId,
                            name: name,
                            unitPrice: getCartUnitPrice(kuantitasList, hargaJualDasar),
                            kuantitasList: kuantitasList,
                            hargaJualDasar: hargaJualDasar,
                            maxStock: maxStock,
                            quantity: 1
                        };
                    } else {
                        if (cart[stokId].quantity + 1 > maxStock) {
                            alert(`Stok maksimal untuk produk "${name}" adalah ${maxStock}.`);
                            return;
                        }
                        cart[stokId].quantity++;
                    }

                    cart[stokId].totalPrice = calculateHargaTotal(cart[stokId].kuantitasList, cart[stokId]
                        .hargaJualDasar, cart[stokId].quantity);
                    updateCart();
                }
            });

            // Event listener untuk tombol Hapus Item Sepenuhnya dari keranjang
            document.body.addEventListener('click', function(e) {
                if (e.target.closest('.remove-full-item-btn')) {
                    const stokId = e.target.closest('.remove-full-item-btn').dataset.id;
                    delete cart[stokId];
                    updateCart();
                }
            });

            // Event listener input kuantitas di keranjang
            document.body.addEventListener('change', function(e) {
                if (e.target.classList.contains('cart-qty-input')) {
                    const input = e.target;
                    const stokId = input.dataset.stokId;
                    let newQty = parseInt(input.value);
                    const maxStock = parseInt(input.max);
                    const item = cart[stokId];

                    if (isNaN(newQty) || newQty < 1) {
                        newQty = 1;
                    } else if (newQty > maxStock) {
                        alert(`Kuantitas maksimal untuk produk "${item.name}" adalah ${maxStock}.`);
                        newQty = maxStock;
                    }

                    input.value = newQty; // Update nilai input
                    item.quantity = newQty;
                    item.totalPrice = calculateHargaTotal(item.kuantitasList, item.hargaJualDasar, item
                        .quantity);
                    updateCart();
                }
            });


            // Event listener untuk perubahan jenis transaksi
            transactionTypeSelect.addEventListener('change', function() {
                if (this.value === 'hutang barang') {
                    memberSelectSection.style.display = 'block';
                    paymentSection.style.display = 'none';
                    paymentInput.value = '0'; // Reset pembayaran
                    changeInput.value = formatRupiah(0);
                    memberSelect.setAttribute('required', 'required');
                    paymentInput.removeAttribute('required');
                    checkoutBtn.textContent = 'Ajukan Hutang';
                } else {
                    memberSelectSection.style.display = 'none';
                    paymentSection.style.display = 'block';
                    memberSelect.removeAttribute('required');
                    paymentInput.setAttribute('required', 'required');
                    checkoutBtn.textContent = 'Selesaikan Transaksi';
                }
                calculateChange();
            });

            // Event listener input pembayaran untuk hitung kembalian
            paymentInput.addEventListener('input', calculateChange);
            paymentInput.addEventListener('blur', function() {
                if (this.value === '' || parseFloat(this.value) < 0) {
                    this.value = 0;
                }
                calculateChange();
            });

            // Event listener untuk perubahan member (khusus hutang)
            memberSelect.addEventListener('change', calculateChange);

            // Pencarian Produk (Filter)
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();
                const products = productList.querySelectorAll('.product-item');

                products.forEach(product => {
                    const productName = product.dataset.name;
                    if (productName.includes(searchTerm)) {
                        product.style.display = 'flex';
                    } else {
                        product.style.display = 'none';
                    }
                });
            });

            // Validasi form sebelum submit
            checkoutForm.addEventListener('submit', function(e) {
                const transactionType = transactionTypeSelect.value;
                const total = parseFloat(totalPriceInput.value) || 0;
                const payment = parseFloat(paymentInput.value) || 0;
                const memberId = memberSelect.value;

                if (Object.keys(cart).length === 0) {
                    e.preventDefault();
                    alert('Keranjang belanja kosong. Tidak dapat menyelesaikan transaksi.');
                    return;
                }

                if (transactionType === 'penjualan barang' && payment < total) {
                    e.preventDefault();
                    alert('Jumlah pembayaran kurang dari total harga. Mohon periksa kembali.');
                    return;
                } else if (transactionType === 'hutang barang' && !memberId) {
                    e.preventDefault();
                    alert('Harap pilih member untuk transaksi hutang.');
                    return;
                }
            });

            // Panggil updateCart dan calculateChange pertama kali
            transactionTypeSelect.dispatchEvent(new Event('change')); // Memicu update tampilan awal
            updateCart();
        });
    </script>

    <style>
        /* Penyesuaian Style untuk Tampilan Kasir yang Lebih Baik */
        .product-card {
            transition: all 0.2s ease-in-out;
            cursor: pointer;
            border: 1px solid #dee2e6 !important;
        }

        .product-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1) !important;
            border-color: #007bff !important;
        }

        .product-initial-placeholder {
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            background-color: #007bff !important;
            /* Biru Primer */
        }

        /* Kustomisasi List Item di Keranjang */
        #cartList .list-group-item {
            border-left: 4px solid #17a2b8;
            /* Border kiri warna info */
            border-radius: .25rem;
            margin-bottom: 5px;
            transition: background-color 0.1s;
        }

        #cartList .list-group-item:hover {
            background-color: #f8f9fa;
        }

        /* Input Quantity di Keranjang */
        .cart-qty-input {
            padding: 0.25rem 0.5rem;
            height: 30px;
        }
    </style>
@endsection

@extends('layouts.app')

@section('title', 'Halaman Kasir')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Daftar Produk</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <div class="input-group mb-3">
                        <input type="text" id="searchInput" class="form-control" placeholder="Cari produk...">
                    </div>
                    <div class="row g-4" id="productList">
                        @forelse($products as $product)
                            <div class="col-12 col-sm-6 col-md-6 col-lg-4 d-flex justify-content-center product-item"
                                data-name="{{ strtolower($product->barang->nama_barang ?? '') }}"
                                data-stock="{{ $product->stok_saat_ini }}"
                                data-stok-id="{{ $product->id }}"
                                data-harga-jual-dasar="{{ $product->harga_jual }}"
                                data-kuantitas-list="{{ json_encode($product->kuantitas_list) }}">

                                <div class="card product-card shadow-sm border-0 rounded-3" style="width: 350px;">
                                    <div class="card-body p-2 d-flex align-items-center gap-3">
                                        <div class="d-flex flex-column align-items-center flex-shrink-0">
                                            <div class="product-initial-placeholder bg-primary text-white fw-bold
                                                        d-flex align-items-center justify-content-center mb-1"
                                                style="width: 80px; height: 80px; font-size: 1.5em; border-radius: 8px;">
                                                <?php
                                                    $nama_barang = $product->barang->nama_barang ?? 'P';
                                                    $words = explode(' ', trim($nama_barang));
                                                    $initials = '';
                                                    if (isset($words[0])) {
                                                        $initials .= substr($words[0], 0, 2);
                                                    }
                                                    if (isset($words[1])) {
                                                        $initials .= substr($words[1], 0, 2);
                                                    }
                                                ?>
                                                {{ $initials }}
                                            </div>
                                            <h6 class="card-title mb-0 text-truncate text-center" style="max-width: 120px;" title="{{ $product->barang->nama_barang ?? '-' }}">
                                                {{ $product->barang->nama_barang ?? '-' }}
                                            </h6>
                                        </div>

                                        <div class="text-end d-flex flex-column align-items-end">
                                            <p class="card-text mb-0 text-success fw-bold">
                                                Rp {{ number_format($product->harga_jual, 0, ',', '.') }}
                                            </p>
                                            <p class="card-text text-muted mb-2">Stok: {{ $product->stok_saat_ini }}</p>

                                            <button class="btn btn-sm btn-success add-to-cart-btn"
                                                data-id="{{ $product->id }}">
                                                Tambah
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <p class="text-center text-muted">Tidak ada produk tersedia di warung ini.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Keranjang Belanja</h5>
                </div>
                <div class="card-body">
                    <form id="checkoutForm" action="{{ route('kasir.barangkeluar.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id_warung" value="{{ session('id_warung') }}">
                        <ul class="list-group mb-3" id="cartList">
                        </ul>

                        <div class="d-flex justify-content-between font-weight-bold">
                            <span>Total:</span>
                            <span id="totalPrice">Rp 0</span>
                            <input type="hidden" name="total_harga" id="totalPriceInput" value="0">
                        </div>

                        <hr class="my-3">

                        <div class="form-group mb-3">
                            <label for="transactionType">Jenis Transaksi:</label>
                            <select class="form-select" id="transactionType" name="jenis">
                                <option value="penjualan">Penjualan</option>
                                <option value="hutang">Hutang</option>
                            </select>
                        </div>

                        <div id="paymentSection">
                            <div class="form-group mb-3">
                                <label for="payment">Bayar:</label>
                                <input type="number" class="form-control" id="payment" name="bayar" placeholder="Jumlah uang pembayaran" min="0">
                            </div>
                            <div class="form-group mb-3">
                                <label for="change">Kembalian:</label>
                                <input type="text" class="form-control" id="change" value="Rp 0" readonly>
                            </div>
                        </div>

                        <div class="form-group mb-3" id="memberSelectSection" style="display: none;">
                            <label for="memberSelect">Pilih Member:</label>
                            <select class="form-select" id="memberSelect" name="id_user_member">
                                <option value="">-- Pilih Member --</option>
                                @foreach($members as $member)
                                    <option value="{{ $member->id }}">{{ $member->name }} {{$member->email}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg" id="checkoutBtn" disabled>Selesaikan Transaksi</button>
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

        let cart = {}; // Object untuk menyimpan item di keranjang

        // Fungsi format angka ke Rupiah
        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID').format(number);
        }

        // Fungsi hitung harga total berdasarkan kuantitas (bundle)
        function calculateHargaTotal(kuantitasList, hargaJualDasar, qty) {
            let sisaQty = qty;
            let totalHarga = 0;
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
            if (sisaQty > 0) {
                totalHarga += sisaQty * hargaJualDasar;
            }
            return totalHarga;
        }

        // Update tampilan keranjang dan total harga
        function updateCart() {
            cartList.innerHTML = '';
            let total = 0;
            for (const id in cart) {
                const item = cart[id];
                total += item.totalPrice;
                const li = document.createElement('li');
                li.className = 'list-group-item d-flex justify-content-between align-items-center';
                li.innerHTML = `
                    <div>
                        <h6 class="my-0">${item.name}</h6>
                        <small class="text-muted">${item.quantity} x Rp ${formatRupiah(item.unitPrice)}</small>
                        <input type="hidden" name="items[${item.stokId}][id_stok_warung]" value="${item.stokId}">
                        <input type="hidden" name="items[${item.stokId}][jumlah]" value="${item.quantity}">
                        <input type="hidden" name="items[${item.stokId}][harga]" value="${item.totalPrice}">
                    </div>
                    <div>
                        <span class="text-muted">Rp ${formatRupiah(item.totalPrice)}</span>
                        <button type="button" class="btn btn-sm btn-outline-danger ms-2 remove-from-cart-btn" data-id="${id}">-</button>
                    </div>
                `;
                cartList.appendChild(li);
            }
            totalPriceEl.textContent = `Rp ${formatRupiah(total)}`;
            totalPriceInput.value = total;
            calculateChange();
            checkoutBtn.disabled = Object.keys(cart).length === 0;
        }

        // Hitung dan tampilkan kembalian
        function calculateChange() {
            const total = parseFloat(totalPriceInput.value) || 0;
            const payment = parseFloat(paymentInput.value) || 0;
            const change = payment - total;
            changeInput.value = `Rp ${formatRupiah(change >= 0 ? change : 0)}`;
        }

        // Event listener untuk tombol tambah produk ke keranjang
        document.body.addEventListener('click', function(e) {
            if (e.target.classList.contains('add-to-cart-btn')) {
                const productCard = e.target.closest('.product-item');
                const stokId = productCard.dataset.stokId;
                const name = productCard.dataset.name;
                const maxStock = parseInt(productCard.dataset.stock);
                const hargaJualDasar = parseFloat(productCard.dataset.hargaJualDasar);
                const kuantitasList = JSON.parse(productCard.dataset.kuantitasList);

                if (!cart[stokId]) {
                    if (maxStock < 1) {
                        alert('Stok produk ini sudah habis.');
                        return;
                    }
                    let unitPriceDisplay = hargaJualDasar;
                    const bundleOneUnit = kuantitasList.find(b => b.jumlah === 1);
                    if (bundleOneUnit) {
                        unitPriceDisplay = bundleOneUnit.harga_jual;
                    }
                    cart[stokId] = {
                        stokId: stokId,
                        name: name,
                        unitPrice: unitPriceDisplay,
                        kuantitasList: kuantitasList,
                        hargaJualDasar: hargaJualDasar,
                        quantity: 1,
                        totalPrice: calculateHargaTotal(kuantitasList, hargaJualDasar, 1)
                    };
                } else {
                    if (cart[stokId].quantity + 1 > maxStock) {
                        alert(`Stok maksimal untuk produk "${name}" adalah ${maxStock}.`);
                        return;
                    }
                    cart[stokId].quantity++;
                    cart[stokId].totalPrice = calculateHargaTotal(cart[stokId].kuantitasList, cart[stokId].hargaJualDasar, cart[stokId].quantity);
                }
                updateCart();
            }
        });

        // Event listener untuk tombol hapus satuan produk dari keranjang
        document.body.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-from-cart-btn')) {
                const listItem = e.target.closest('li');
                const stokId = listItem.querySelector('input[name^="items"]').name.match(/items\[(\d+)\]/)[1];
                if (cart[stokId]) {
                    if (cart[stokId].quantity > 1) {
                        cart[stokId].quantity--;
                        cart[stokId].totalPrice = calculateHargaTotal(cart[stokId].kuantitasList, cart[stokId].hargaJualDasar, cart[stokId].quantity);
                    } else {
                        delete cart[stokId];
                    }
                }
                updateCart();
            }
        });

        // Event listener untuk perubahan jenis transaksi
        transactionTypeSelect.addEventListener('change', function() {
            if (this.value === 'hutang') {
                memberSelectSection.style.display = 'block';
                paymentSection.style.display = 'none';
                paymentInput.value = '';
                changeInput.value = 'Rp 0';
            } else {
                memberSelectSection.style.display = 'none';
                paymentSection.style.display = 'block';
            }
        });

        // Event listener input pembayaran untuk hitung kembalian
        paymentInput.addEventListener('input', calculateChange);

        // Validasi form sebelum submit
        checkoutForm.addEventListener('submit', function(e) {
            const transactionType = transactionTypeSelect.value;
            const total = parseFloat(totalPriceInput.value) || 0;
            const payment = parseFloat(paymentInput.value) || 0;
            const memberId = memberSelect.value;

            if (Object.keys(cart).length === 0) {
                e.preventDefault();
                alert('Keranjang belanja kosong.');
                return;
            }

            if (transactionType === 'penjualan' && payment < total) {
                e.preventDefault();
                alert('Jumlah pembayaran kurang dari total harga.');
            } else if (transactionType === 'hutang' && !memberId) {
                e.preventDefault();
                alert('Harap pilih member untuk transaksi hutang.');
            }
        });

        // Panggil updateCart pertama kali untuk memastikan tampilan awal benar
        updateCart();
    });
</script>
@endsection

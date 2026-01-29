@extends('layouts.app')

@section('title', 'Halaman Kasir - Transaksi Cepat')

@section('content')
    <div class="container-fluid mt-4" x-data="kasirApp()">

        <div class="row g-4">
            {{-- BAGIAN DAFTAR PRODUK (KIRI) --}}
            <div class="col-lg-7 col-xl-8">
                <div class="card shadow-sm border-0 h-100">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="card-header bg-primary text-white p-3">
                        <h5 class="mb-0"><i class="fas fa-boxes me-2"></i>Daftar Produk</h5>
                    </div>
                    <div class="card-body p-3">
                        <div class="input-group mb-3 sticky-top bg-white py-2" style="top: -16px; z-index: 10;">
                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                            <input type="text" x-model="search" class="form-control form-control-lg border-start-0 ps-0"
                                placeholder="Cari produk...">
                        </div>

                        <div class="row g-3">
                            <template x-for="product in filteredProducts" :key="product.id">
                                <div class="col-6 col-sm-4 col-md-3 col-xl-2 d-flex">
                                    <button type="button" @click="addToCart(product)"
                                        class="card product-card shadow-sm h-100 w-100 border-0 text-start"
                                        :class="product.stok <= 0 ? 'opacity-50' : ''" :disabled="product.stok <= 0">
                                        <div class="card-body p-2 d-flex flex-column justify-content-between">
                                            <div class="mb-2">
                                                <div class="product-initial bg-primary text-white mb-2"
                                                    x-text="getInitials(product.nama)"></div>
                                                <h6 class="card-title mb-1 text-truncate" style="font-size: 0.85rem;"
                                                    x-text="product.nama"></h6>
                                            </div>
                                            <div class="mt-auto">
                                                <div class="fw-bold text-success" x-text="formatRupiah(product.harga_jual)">
                                                </div>
                                                <small class="text-muted">Stok: <span x-text="product.stok"></span></small>
                                            </div>
                                        </div>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            {{-- BAGIAN KERANJANG (KANAN) --}}
            <div class="col-lg-5 col-xl-4">
                <div class="card shadow-lg border-0 h-100 sticky-top" style="top: 20px;">
                    <div class="card-header bg-dark text-white p-3">
                        <h5 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Keranjang</h5>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <form id="formTransaksi" action="{{ route('kasir.barangkeluar.store') }}" method="POST"
                            class="flex-grow-1 d-flex flex-column">
                            @csrf
                            <input type="hidden" name="id_warung" value="{{ session('id_warung') }}">
                            <input type="hidden" name="uang_dibayar"
                                :value="transactionType === 'penjualan barang' ? payment : 0">

                            <input type="hidden" name="uang_kembalian"
                                :value="transactionType === 'penjualan barang' ? change : 0">


                            {{-- Area Item --}}
                            <div class="cart-items flex-grow-1 overflow-auto mb-3" style="max-height: 350px;">
                                <ul class="list-group list-group-flush">
                                    <template x-if="cart.length === 0">
                                        <li class="list-group-item text-center py-5 text-muted">
                                            <i class="fas fa-cart-plus fa-3x mb-3 opacity-20"></i><br>Keranjang Kosong
                                        </li>
                                    </template>

                                    <template x-for="(item, index) in cart" :key="item.id">
                                        <li class="list-group-item px-0 py-2 border-bottom">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div class="flex-grow-1 pe-2">
                                                    <div class="fw-bold text-capitalize" x-text="item.nama"></div>
                                                    <small class="text-muted"
                                                        x-text="formatRupiah(item.harga_jual) + ' x ' + item.qty"></small>

                                                    <input type="hidden" :name="'items[' + index + '][id_stok_warung]'"
                                                        :value="item.id">
                                                    <input type="hidden" :name="'items[' + index + '][jumlah]'"
                                                        :value="item.qty">
                                                    <input type="hidden" :name="'items[' + index + '][harga]'"
                                                        :value="calculateItemTotal(item)">
                                                </div>
                                                <div class="text-end">
                                                    <div class="fw-bold mb-1"
                                                        x-text="formatRupiah(calculateItemTotal(item))"></div>
                                                    <div class="btn-group btn-group-sm">
                                                        <button type="button" class="btn btn-outline-secondary"
                                                            @click="updateQty(item, -1)">-</button>
                                                        <button type="button" class="btn btn-outline-secondary"
                                                            @click="updateQty(item, 1)">+</button>
                                                        <button type="button" class="btn btn-outline-danger"
                                                            @click="removeFromCart(index)"><i
                                                                class="fas fa-trash"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    </template>
                                </ul>
                            </div>

                            <div class="border-top pt-3 mt-auto">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="h5 mb-0">Total:</span>
                                    <span class="h4 mb-0 fw-bold text-primary" x-text="formatRupiah(grandTotal)"></span>
                                    <input type="hidden" name="total_harga" :value="grandTotal">
                                </div>

                                <div class="mb-3">
                                    <label class="small fw-bold text-uppercase text-muted">Metode Bayar</label>
                                    <select class="form-select shadow-sm" x-model="transactionType" name="jenis">
                                        <option value="penjualan barang">Tunai (Cash)</option>
                                        <option value="transfer">Transfer (Bank/E-Wallet)</option>
                                        <option value="hutang barang">Hutang (Member)</option>
                                    </select>
                                </div>

                                {{-- Field Member (Hanya muncul jika Hutang) --}}
                                <div class="mb-3 bg-light p-2 rounded" x-show="transactionType === 'hutang barang'"
                                    x-transition>
                                    <label class="small fw-bold text-danger">Pilih Nama Member:</label>
                                    <select class="form-select border-danger" name="id_user_member"
                                        x-model="selectedMember">
                                        <option value="">-- Pilih Member --</option>
                                        @foreach ($members as $member)
                                            <option value="{{ $member->id }}">{{ $member->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Field Cash (Hanya muncul jika Tunai) --}}
                                <div x-show="transactionType === 'penjualan barang'" x-transition>
                                    <div class="mb-3">
                                        <label class="small fw-bold text-muted">Uang Diterima:</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" class="form-control form-control-lg fw-bold text-end"
                                                x-model.number="payment">
                                        </div>
                                    </div>
                                </div>

                                {{-- Field Transfer (Muncul jika Transfer) --}}
                                <div class="mb-3 bg-light p-3 rounded border-start border-primary border-4"
                                    x-show="transactionType === 'transfer'" x-transition>

                                    <div class="mb-2">
                                        <label class="small fw-bold text-primary text-uppercase">Tujuan / Nama
                                            Aplikasi:</label>
                                        <input type="text" name="keterangan_transfer" class="form-control"
                                            placeholder="Contoh: DANA, QRIS, BCA, dll" x-model="transferDetail" uppercase>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <span class="small text-muted">Nominal Transfer:</span>
                                        <span class="fw-bold text-dark" x-text="formatRupiah(grandTotal)"></span>
                                    </div>

                                    <small class="text-muted" style="font-size: 0.75rem;">
                                        *Pastikan status transfer sudah "Berhasil" di perangkat pembeli.
                                    </small>
                                </div>

                                <button type="button" class="btn btn-primary btn-lg w-100 shadow"
                                    :disabled="!canCheckout" @click="showReceipt = true">
                                    <i class="fas fa-check-circle me-2"></i>Validasi Transaksi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- MODAL STRUK (VALIDASI) --}}
        <div class="modal fade" :class="{ 'show d-block': showReceipt }" x-show="showReceipt" tabindex="-1"
            style="background: rgba(10, 10, 10, 0.8); backdrop-filter: blur(4px);" x-cloak>
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">

                    <div class="modal-header border-0 pb-0">
                        <button type="button" class="btn-close" @click="showReceipt = false"></button>
                    </div>

                    <div class="modal-body px-4 pb-4">
                        <div class="bg-white p-4 shadow-sm position-relative"
                            style="border-radius: 12px; border: 1px solid #f0f0f0;">

                            <div class="text-center mb-4">
                                <div class="bg-dark text-white d-inline-block rounded-circle mb-2"
                                    style="width: 50px; height: 50px; line-height: 50px;">
                                    <i class="fas fa-store"></i>
                                </div>
                                <h5 class="fw-bold mb-0 text-uppercase" style="letter-spacing: 2px;">Warung Digital</h5>
                                <small class="text-muted">Jl. Raya Utama No. 123, Kota Anda</small>
                            </div>

                            <div class="d-flex justify-content-between mb-3 small border-bottom pb-2 text-secondary">
                                <span x-text="'#TRX-' + Math.floor(Date.now()/1000)"></span>
                                <span
                                    x-text="new Date().toLocaleDateString('id-ID', {day:'numeric', month:'short', year:'numeric', hour:'2-digit', minute:'2-digit'})"></span>
                            </div>

                            <div class="mb-4">
                                <template x-for="item in cart" :key="item.id">
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div style="max-width: 70%;">
                                                <div class="fw-bold text-dark text-capitalize" x-text="item.nama"></div>
                                                <div class="small text-muted"
                                                    x-text="item.qty + ' x ' + formatRupiah(item.harga_jual)"></div>
                                            </div>
                                            <div class="fw-bold text-dark"
                                                x-text="formatRupiah(calculateItemTotal(item))"></div>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <div class="border-top mb-3"
                                style="border-style: dashed !important; border-color: #dee2e6 !important;"></div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted">Subtotal</span>
                                    <span class="text-dark" x-text="formatRupiah(grandTotal)"></span>
                                </div>

                                <template x-if="transactionType === 'penjualan barang'">
                                    <div>
                                        <div class="d-flex justify-content-between mb-1">
                                            <span class="text-muted">Bayar (Tunai)</span>
                                            <span class="text-dark" x-text="formatRupiah(payment)"></span>
                                        </div>
                                        <div class="d-flex justify-content-between fw-bold fs-5 mt-2 pt-2 border-top">
                                            <span class="text-primary">KEMBALIAN</span>
                                            <span class="text-primary" x-text="formatRupiah(change)"></span>
                                        </div>
                                    </div>
                                </template>

                                <template x-if="transactionType === 'hutang barang'">
                                    <div class="mt-2 p-3 bg-light rounded-3 border-start border-danger border-4">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <small class="text-danger fw-bold d-block">METODE HUTANG</small>
                                                <span class="fw-bold text-dark" x-text="getMemberName()"></span>
                                            </div>
                                            <i class="fas fa-user-tag text-danger opacity-50 fa-lg"></i>
                                        </div>
                                    </div>
                                </template>

                                <template x-if="transactionType === 'transfer'">
                                    <div class="mt-2 p-3 bg-light rounded-3 border-start border-primary border-4">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <small class="text-primary fw-bold d-block">METODE TRANSFER</small>
                                                <span class="fw-bold text-dark" x-text="transferDetail"></span>
                                            </div>
                                            <div class="text-end">
                                                <span class="fw-bold text-primary d-block"
                                                    x-text="formatRupiah(grandTotal)"></span>
                                                <small class="text-success text-uppercase fw-bold"
                                                    style="font-size: 10px;">Lunas</small>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <div class="text-center mt-5">
                                <svg class="mb-2" id="barcode"></svg>
                                <p class="small text-muted mb-0">Terima kasih telah berbelanja!</p>
                                <small style="font-size: 10px; color: #ccc;">Sistem Kasir v2.0</small>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-0 px-4 pb-4">
                        <button type="button" class="btn btn-link text-decoration-none text-muted"
                            @click="showReceipt = false">Batal</button>
                        <button type="button" class="btn btn-dark btn-lg flex-grow-1 shadow-sm"
                            style="border-radius: 12px;" @click="submitFinal()">
                            <i class="fas fa-print me-2"></i>Konfirmasi & Simpan
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <script>
        function kasirApp() {
            return {
                search: '',
                products: @json($products).map(p => ({
                    id: p.id,
                    nama: p.barang?.nama_barang || '-',
                    stok: p.stok_saat_ini,
                    harga_jual: p.harga_jual,
                    kuantitas_list: p.kuantitas_list || []
                })),
                cart: [],
                transactionType: 'penjualan barang',
                transferDetail: '', // Tambahkan ini
                payment: 0,
                selectedMember: '',
                showReceipt: false,

                get filteredProducts() {
                    if (!this.search) return this.products;
                    return this.products.filter(p => p.nama.toLowerCase().includes(this.search.toLowerCase()));
                },

                get grandTotal() {
                    return this.cart.reduce((sum, item) => sum + this.calculateItemTotal(item), 0);
                },

                get change() {
                    return this.payment - this.grandTotal;
                },

                get canCheckout() {
                    if (this.cart.length === 0) return false;
                    if (this.transactionType === 'penjualan barang') {
                        return this.payment >= this.grandTotal && this.grandTotal > 0;
                    }
                    return this.selectedMember !== '';
                },

                addToCart(product) {
                    let existing = this.cart.find(item => item.id === product.id);
                    if (existing) {
                        if (existing.qty < product.stok) existing.qty++;
                    } else {
                        this.cart.push({
                            ...product,
                            qty: 1
                        });
                    }
                },

                updateQty(item, amount) {
                    let newQty = item.qty + amount;
                    if (newQty >= 1 && newQty <= item.stok) item.qty = newQty;
                },

                removeFromCart(index) {
                    this.cart.splice(index, 1);
                },

                calculateItemTotal(item) {
                    let sisa = item.qty;
                    let total = 0;
                    let bundles = [...item.kuantitas_list].sort((a, b) => b.jumlah - a.jumlah);

                    for (let bundle of bundles) {
                        let kelipatan = Math.floor(sisa / bundle.jumlah);
                        if (kelipatan > 0) {
                            total += kelipatan * bundle.harga_jual;
                            sisa -= kelipatan * bundle.jumlah;
                        }
                    }
                    return total + (sisa * item.harga_jual);
                },

                formatRupiah(val) {
                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(val || 0);
                },

                getInitials(name) {
                    return name.split(' ').map(n => n[0]).slice(0, 2).join('').toUpperCase();
                },

                getMemberName() {
                    const select = document.querySelector('select[name="id_user_member"]');
                    return select && select.selectedIndex > 0 ? select.options[select.selectedIndex].text : '-';
                },

                get canCheckout() {
                    if (this.cart.length === 0) return false;

                    if (this.transactionType === 'penjualan barang') {
                        return this.payment >= this.grandTotal && this.grandTotal > 0;
                    }

                    if (this.transactionType === 'transfer') {
                        // Otomatis set payment sama dengan total agar uang_dibayar di form benar
                        this.payment = this.grandTotal;
                        // Validasi: Detail transfer (Bank/E-wallet) wajib diisi
                        return this.transferDetail.trim() !== '';
                    }

                    return this.selectedMember !== '';
                },

                submitFinal() {
                    document.getElementById('formTransaksi').submit();
                }
            }
        }
    </script>

    <style>
        .product-card {
            cursor: pointer;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
            border: 1px solid #eee !important;
        }

        .product-card:hover:not(:disabled) {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1) !important;
            border-color: #0d6efd !important;
        }

        .product-initial {
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            font-weight: bold;
            font-size: 1.2rem;
        }

        .cart-items::-webkit-scrollbar {
            width: 5px;
        }

        .cart-items::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 10px;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
@endsection

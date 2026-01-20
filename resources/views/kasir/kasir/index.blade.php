@extends('layouts.app')

@section('title', 'Halaman Kasir - Transaksi Cepat')

@section('content')
<div class="container-fluid mt-4" 
     x-data="kasirApp()" 
     x-init="init()">
    
    <div class="row g-4">
        {{-- BAGIAN DAFTAR PRODUK (KIRI) --}}
        <div class="col-lg-8 col-xl-7">
            <div class="card shadow-lg border-0 h-100">
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
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-boxes me-2"></i>Daftar Produk</h5>
                </div>
                <div class="card-body p-3">
                    {{-- Search --}}
                    <div class="input-group mb-3 sticky-top p-0 bg-white" style="top: -16px; z-index: 10;">
                        <span class="input-group-text bg-light"><i class="fas fa-search"></i></span>
                        <input type="text" x-model="search" class="form-control form-control-lg"
                               placeholder="Cari produk berdasarkan nama...">
                    </div>

                    {{-- Product Grid --}}
                    <div class="row g-3">

                        <template x-for="product in filteredProducts" :key="product.id">
                            <div class="col-6 col-sm-4 col-md-3 col-xl-2 d-flex">
                                <button type="button"
                                    @click="addToCart(product)"
                                    class="card product-card text-decoration-none shadow-sm h-100 w-100 border-0 rounded-3 text-start"
                                    :title="product.nama + ' (Stok: ' + product.stok + ')'">
                                    <div class="card-body p-2 d-flex flex-column justify-content-between">
                                        <div class="d-flex align-items-center mb-1">
                                            <div class="product-initial-placeholder bg-primary text-white fw-bold flex-shrink-0 me-2"
                                                 style="width: 40px; height: 40px; font-size: 1.1em; border-radius: 6px; display: flex; align-items:center; justify-content:center;"
                                                 x-text="getInitials(product.nama)">
                                            </div>
                                            <h6 class="card-title my-0 flex-grow-1 text-truncate" 
                                                style="font-size: 0.9em;" x-text="product.nama"></h6>
                                        </div>
                                        <div class="text-end">
                                            <p class="card-text mb-0 text-success fw-bold" style="font-size: 1em;" 
                                               x-text="formatRupiah(product.harga_jual)"></p>
                                            <small class="text-muted" style="font-size: 0.8em;">
                                                Stok: <span x-text="product.stok"></span>
                                            </small>
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
        <div class="col-lg-4 col-xl-5">
            <div class="card shadow-lg border-0 h-100">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Keranjang Belanja</h5>
                </div>
                <div class="card-body d-flex flex-column">
                    <form action="{{ route('kasir.barangkeluar.store') }}" method="POST" class="flex-grow-1 d-flex flex-column">
                        @csrf
                        <input type="hidden" name="id_warung" value="{{ session('id_warung') }}">

                        {{-- Daftar Item Keranjang --}}
                        <div class="cart-scroll-area flex-grow-1 mb-3" style="max-height: 400px; overflow-y: auto;">
                            <ul class="list-group">
                                <template x-if="cart.length === 0">
                                    <li class="list-group-item text-center text-muted">Keranjang kosong. Tambahkan produk!</li>
                                </template>
                                
                                <template x-for="(item, index) in cart" :key="item.id">
                                    <li class="list-group-item d-flex justify-content-between align-items-center p-2 mb-1 border-start border-info border-4">
                                        <div class="flex-grow-1 me-2">
                                            <h6 class="my-0 text-capitalize" x-text="item.nama"></h6>
                                            <small class="text-muted" x-text="'@ ' + formatRupiah(item.harga_jual)"></small>
                                            
                                            {{-- Hidden Inputs for Laravel --}}
                                            <input type="hidden" :name="'items['+item.id+'][id_stok_warung]'" :value="item.id">
                                            <input type="hidden" :name="'items['+item.id+'][jumlah]'" :value="item.qty">
                                            <input type="hidden" :name="'items['+item.id+'][harga]'" :value="calculateItemTotal(item)">
                                        </div>
                                        
                                        <div class="d-flex align-items-center">
                                            <div class="input-group input-group-sm" style="width: 110px;">
                                                <button type="button" class="btn btn-outline-secondary" @click="updateQty(item, -1)">-</button>
                                                <input type="number" class="form-control text-center" x-model.number="item.qty" @change="validateQty(item)">
                                                <button type="button" class="btn btn-outline-secondary" @click="updateQty(item, 1)">+</button>
                                            </div>
                                            <span class="ms-2 fw-bold text-end" style="width: 90px" x-text="formatRupiah(calculateItemTotal(item))"></span>
                                            <button type="button" class="btn btn-sm btn-link text-danger ms-1" @click="removeFromCart(index)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </li>
                                </template>
                            </ul>
                        </div>

                        <div class="d-flex justify-content-between fs-5 mb-3 fw-bold">
                            <span>Total:</span>
                            <span class="text-success" x-text="formatRupiah(grandTotal)"></span>
                            <input type="hidden" name="total_harga" :value="grandTotal">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Jenis Transaksi:</label>
                            <select class="form-select" x-model="transactionType" name="jenis">
                                <option value="penjualan barang">Penjualan (Tunai)</option>
                                <option value="hutang barang">Hutang (Member)</option>
                            </select>
                        </div>

                        {{-- Member Select (Hutang) --}}
                        <div class="mb-3" x-show="transactionType === 'hutang barang'" x-transition>
                            <label class="form-label fw-bold">Pilih Member:</label>
                            <select class="form-select" name="id_user_member" x-model="selectedMember" :required="transactionType === 'hutang barang'">
                                <option value="">-- Pilih Member --</option>
                                @foreach ($members as $member)
                                    <option value="{{ $member->id }}">{{ $member->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Payment (Tunai) --}}
                        <div x-show="transactionType === 'penjualan barang'" x-transition>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Bayar:</label>
                                <input type="number" class="form-control form-control-lg text-end" x-model.number="payment" name="bayar">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Kembalian:</label>
                                <input type="text" class="form-control form-control-lg text-end fw-bold" 
                                       :class="change < 0 ? 'text-danger' : 'text-primary'"
                                       :value="formatRupiah(Math.abs(change))" readonly>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-lg w-100 fw-bold" 
                                :class="canCheckout ? 'btn-success' : 'btn-secondary'"
                                :disabled="!canCheckout">
                            <i class="fas fa-cash-register me-2"></i>
                            <span x-text="transactionType === 'hutang barang' ? 'Ajukan Hutang' : 'Selesaikan Transaksi'"></span>
                        </button>
                    </form>
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
            payment: 0,
            selectedMember: '',

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
                if (product.stok <= 0) return alert('Stok habis!');
                
                let existing = this.cart.find(item => item.id === product.id);
                if (existing) {
                    if (existing.qty < product.stok) existing.qty++;
                } else {
                    this.cart.push({ ...product, qty: 1 });
                }
            },

            updateQty(item, amount) {
                let newQty = item.qty + amount;
                if (newQty >= 1 && newQty <= item.stok) item.qty = newQty;
            },

            validateQty(item) {
                if (item.qty < 1) item.qty = 1;
                if (item.qty > item.stok) item.qty = item.stok;
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
            }
        }
    }
</script>

<style>
    .product-card { transition: all 0.2s; border: 1px solid #dee2e6 !important; }
    .product-card:hover { transform: translateY(-2px); border-color: #007bff !important; }
    [x-cloak] { display: none !important; }
</style>
@endsection
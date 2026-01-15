@extends('layouts.app')

@section('title', 'Buat Rencana Belanja')

@section('content')
<div class="container-fluid"
     x-data="rencanaBelanja()"
     x-init="$refs.search.focus()">

    <form method="POST" action="{{ route('kasir.rencanabelanja.store') }}">
        @csrf

        <div class="row g-4">

            {{-- KIRI --}}
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <strong>Cari Barang</strong>
                    </div>

                    <div class="card-body">
                        <input type="text"
                            class="form-control mb-3"
                            placeholder="Ketik nama / scan barcode"
                            x-ref="search"
                            x-model="keyword"
                            x-on:input.debounce.400ms="search">

                        <div class="table-responsive" style="max-height:65vh">
                            <table class="table table-hover align-middle">
                                <tbody>

                                    <template x-for="b in barangList" :key="b.id">
                                        <tr>
                                            <td width="70" class="text-center">
                                                <span class="badge"
                                                    :class="b.stok < 10 ? 'bg-danger' : 'bg-secondary'"
                                                    x-text="b.stok">
                                                </span>
                                            </td>

                                            <td>
                                                <strong x-text="b.nama"></strong><br>
                                                <small x-text="b.kategori"></small>
                                            </td>

                                            <td width="260">
                                                <div class="input-group input-group-sm">
                                                    <input type="number"
                                                        min="0"
                                                        class="form-control text-center"
                                                        value="0"
                                                        x-on:input="update(
                                                            b.id,
                                                            b.nama,
                                                            $event.target.value,
                                                            $refs['satuan'+b.id]?.value || 1
                                                        )">

                                                    <select class="form-select w-50"
                                                        :x-ref="'satuan'+b.id"
                                                        :disabled="b.satuan.length === 0"
                                                        x-on:change="update(
                                                            b.id,
                                                            b.nama,
                                                            $el.previousElementSibling.value,
                                                            $event.target.value
                                                        )">
                                                        <option value="1">Pcs</option>
                                                        <template x-for="s in b.satuan">
                                                            <option :value="s.qty" x-text="s.nama"></option>
                                                        </template>
                                                    </select>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>

                                    <tr x-show="loading">
                                        <td colspan="3" class="text-center py-4 text-muted">
                                            Mencari barang...
                                        </td>
                                    </tr>

                                    <tr x-show="!loading && barangList.length === 0">
                                        <td colspan="3" class="text-center py-4 text-muted">
                                            Ketik minimal 2 huruf
                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KANAN --}}
            <div class="col-md-4">
                <div class="card shadow-sm sticky-top" style="top:20px">
                    <div class="card-header bg-success text-white">
                        <strong>Ringkasan</strong>
                    </div>

                    <div class="card-body p-0">
                        <table class="table table-sm mb-0">
                            <tbody>
                                <template x-for="(item, id) in rencana" :key="id">
                                    <tr>
                                        <td x-text="item.nama"></td>
                                        <td class="text-center">
                                            <span class="badge bg-primary" x-text="item.total"></span>
                                        </td>
                                        <td class="text-center">
                                            <button type="button"
                                                class="btn btn-sm text-danger"
                                                @click="remove(id)">
                                                ✕
                                            </button>
                                        </td>
                                    </tr>
                                </template>

                                <tr x-show="Object.keys(rencana).length === 0">
                                    <td colspan="3" class="text-center py-4 text-muted">
                                        Belum ada barang
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="card-footer">
                        <button class="btn btn-success w-100 fw-bold">
                            Simpan Rencana
                        </button>
                    </div>
                </div>
            </div>

        </div>

        {{-- HIDDEN INPUT --}}
        <template x-for="(item, id) in rencana">
            <input type="hidden"
                :name="`rencana[${id}][id_barang]`"
                :value="id">
            <input type="hidden"
                :name="`rencana[${id}][jumlah_awal]`"
                :value="item.total">
        </template>

    </form>
</div>

<script>
function rencanaBelanja() {
    return {
        keyword: '',
        loading: false,
        barangList: [],
        rencana: {},

        async search() {
            if (this.keyword.length < 2) {
                this.barangList = [];
                return;
            }

            this.loading = true;

            const res = await fetch(
                `{{ route('kasir.rencanabelanja.barang.search') }}?q=${this.keyword}`
            );

            this.barangList = await res.json();
            this.loading = false;
        },

        update(id, nama, qty, multiplier) {
            const total = (parseInt(qty) || 0) * (parseInt(multiplier) || 1);

            if (total <= 0) {
                delete this.rencana[id];
                return;
            }

            this.rencana[id] = { nama, total };
        },

        remove(id) {
            delete this.rencana[id];
        }
    }
}
</script>
@endsection

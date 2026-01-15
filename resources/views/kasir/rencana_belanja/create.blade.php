@extends('layouts.app')

@section('title', 'Buat Rencana Belanja')

@section('content')
<div class="container-fluid"
     x-data="rencanaBelanja()"
     x-init="$refs.search?.focus()">

<form method="POST" action="{{ route('kasir.rencanabelanja.store') }}">
@csrf

{{-- ================= HIDDEN JSON (INTI SOLUSI) ================= --}}
<input type="hidden"
       name="rencana_json"
       :value="JSON.stringify(rencana)">

<div class="row g-4">

{{-- ================= KIRI ================= --}}
<div class="col-md-8">
<div class="card shadow-sm">

<div class="card-header bg-success text-white">
    <strong>Barang</strong>
</div>

<div class="card-body">

{{-- NAV TAB --}}
<ul class="nav nav-tabs mb-3">
    <li class="nav-item">
        <button type="button"
            class="nav-link"
            :class="activeTab === 'list' ? 'active' : ''"
            @click="activeTab = 'list'">
            📦 Daftar Barang
        </button>
    </li>
    <li class="nav-item">
        <button type="button"
            class="nav-link"
            :class="activeTab === 'search' ? 'active' : ''"
            @click="activeTab = 'search'">
            🔍 Cari Barang
        </button>
    </li>
</ul>

{{-- ================= TAB LIST (PAGINATION) ================= --}}
<div x-show="activeTab === 'list'">

<div class="table-responsive" style="max-height:65vh">
<table class="table table-hover align-middle">
<tbody>
@foreach($barangPage as $b)
@php
    $stok = $b->stokWarung->first()->jumlah ?? 0;
@endphp
<tr>
    <td width="70" class="text-center">
        <span class="badge {{ $stok < 10 ? 'bg-danger' : 'bg-secondary' }}">
            {{ $stok }}
        </span>
    </td>

    <td>
        <strong>{{ $b->nama_barang }}</strong><br>
        <small>{{ $b->subKategori->sub_kategori ?? '-' }}</small>
    </td>

    <td width="260">
        <div class="input-group input-group-sm">
            <input type="number" min="0"
                class="form-control text-center"
                @input="update(
                    {{ $b->id }},
                    '{{ $b->nama_barang }}',
                    $event.target.value,
                    $refs['satuan{{ $b->id }}']?.value || 1
                )">

            <select class="form-select w-50"
                x-ref="satuan{{ $b->id }}"
                {{ $b->satuan->count() === 0 ? 'disabled' : '' }}
                @change="update(
                    {{ $b->id }},
                    '{{ $b->nama_barang }}',
                    $el.previousElementSibling.value,
                    $event.target.value
                )">
                <option value="1">Pcs</option>
                @foreach($b->satuan as $s)
                    <option value="{{ $s->jumlah }}">{{ $s->nama_satuan }}</option>
                @endforeach
            </select>
        </div>
    </td>
</tr>
@endforeach
</tbody>
</table>
</div>

<div class="mt-2">
    {{ $barangPage->links() }}
</div>

</div>

{{-- ================= TAB SEARCH ================= --}}
<div x-show="activeTab === 'search'">

<input type="text"
    class="form-control mb-3"
    placeholder="Ketik langsung cari barang..."
    x-ref="search"
    x-model="keyword"
    x-on:input.debounce.300ms="search">

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
            <input type="number" min="0"
                class="form-control text-center"
                @input="update(
                    b.id,
                    b.nama,
                    $event.target.value,
                    $refs['satuan'+b.id]?.value || 1
                )">

            <select class="form-select w-50"
                :x-ref="'satuan'+b.id"
                :disabled="b.satuan.length === 0"
                @change="update(
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
        Barang tidak ditemukan
    </td>
</tr>

</tbody>
</table>
</div>

</div>

</div>
</div>
</div>

{{-- ================= KANAN ================= --}}
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
</form>
</div>

<script>
function rencanaBelanja() {
    return {
        activeTab: 'list',
        keyword: '',
        loading: false,
        barangList: [],
        rencana: {},

        async search() {
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

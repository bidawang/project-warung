@extends('layouts.admin')
@section('title','Buat Transaksi Pembelian dari Kebutuhan Rencana')
@section('content')

<div class="flex-1 flex flex-col overflow-hidden">

<header class="flex justify-between items-center p-6 bg-white border-b border-gray-200">
    <h1 class="text-2xl font-bold">Pembelian Berdasarkan Rencana</h1>
    <a href="{{ route('admin.rencana.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded">Kembali</a>
</header>
@if($errors->any())
    <div class="bg-red-100 text-red-700 px-4 py-3 rounded mb-4">
        <strong>Kesalahan:</strong>
        <ul class="list-disc ml-5 text-sm">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif
<main class="p-6">

@if($totalKebutuhan->isEmpty())
    <div class="text-center py-10 text-gray-500">Tidak ada kebutuhan Rencana</div>
@else

<form action="{{ route('admin.rencana.store') }}" method="POST" id="pembelianForm">
@csrf

<table class="w-full text-sm border rounded overflow-hidden">
<thead class="bg-gray-100">
<tr class="text-xs text-gray-600 uppercase">
    <th class="p-2">Barang</th>
    <th class="p-2">Total</th>
    <th class="p-2">Area</th>
    <th class="p-2">Jumlah</th>
    <th class="p-2">Harga</th>
    <th class="p-2">Exp</th>
    <th class="p-2">Skip</th>
    <th class="p-2">Aksi</th>
</tr>
</thead>

<tbody id="purchaseTableBody">

@foreach($totalKebutuhan as $g => $item)
@php
    $noArea = count($item['valid_areas']) === 0; // <=== AREA KOSONG
@endphp

<tr class="bg-indigo-50 font-bold">
    <td colspan="8" class="p-2">
        {{ $item['nama_barang'] }}
        <input type="hidden" name="items[{{ $g }}][id_barang]" value="{{ $item['id_barang'] }}">
        <input type="hidden" name="items[{{ $g }}][rencana_ids]" value="{{ implode(',',$item['rencana_ids']) }}">
        <input type="hidden" id="total_kebutuhan_{{ $g }}" value="{{ $item['total_kebutuhan'] }}">
        <label class="ml-3 text-red-600">
            <input type="hidden" name="items[{{ $g }}][skip]" value="0">
            <input type="checkbox" class="skip-checkbox" data-group="{{ $g }}" name="items[{{ $g }}][skip]"> Skip pembelian
        </label>
    </td>
</tr>

{{-- ROW PEMBELIAN PERTAMA --}}
<tr class="purchase-row" data-group="{{ $g }}" data-index="0">
    <td></td>

    <td id="qty_box_{{ $g }}" rowspan="1" class="font-bold text-center border-r">
        {{ $item['total_kebutuhan'] }} pcs
    </td>

    <td>
        <select {{ $noArea ? 'disabled' : '' }} name="items[{{ $g }}][purchases][0][area_pembelian_id]" 
                class="area-select w-full border p-1 text-xs" data-group="{{ $g }}">
            @if($noArea)
                <option selected>Tidak ada area</option>
            @else
                <option value="" selected>Pilih Area</option>
                @foreach($item['valid_areas'] as $a)
                    <option value="{{ $a->id }}">{{ $a->area }}</option>
                @endforeach
            @endif
        </select>
    </td>

    <td><input type="number" name="items[{{ $g }}][purchases][0][jumlah_beli]" 
               value="{{ $noArea ? '' : $item['total_kebutuhan'] }}" 
               {{ $noArea ? 'disabled' : '' }}
               class="qty w-full border p-1 text-center"></td>

    <td><input type="number" name="items[{{ $g }}][purchases][0][harga]" 
               value="0" {{ $noArea ? 'disabled' : '' }}
               class="w-full border p-1 text-right"></td>

    <td><input type="date" name="items[{{ $g }}][purchases][0][tanggal_kadaluarsa]" 
               {{ $noArea ? 'disabled' : '' }}
               class="w-full border p-1 text-center"></td>

    <td class="text-center font-bold {{ $noArea ? 'text-orange-500' : '' }}">
        {{ $noArea ? 'AUTO SKIP AREA' : '' }}
    </td>

    <td class="text-center">
        <button type="button" class="text-green-600 add-row" data-group="{{ $g }}" {{ $noArea?'disabled':'' }}>+</button>
    </td>
</tr>

@endforeach
</tbody>
</table>

<button type="submit" id="btnSubmitPembelian" class="mt-5 bg-indigo-600 text-white px-6 py-2 rounded">Proses</button>
</form>

@endif
</main>
</div>

<script>
let nextRow = {}

@foreach($totalKebutuhan as $g=>$i)
    nextRow[{{ $g }}] = 1
@endforeach

document.addEventListener("click",e=>{
    if(e.target.classList.contains("add-row")){
        addRow(e.target.dataset.group)
    }
})

// =============== SKIP HANDLER =======================
document.querySelectorAll(".skip-checkbox").forEach(cb=>{
    cb.addEventListener("change",()=>{
        let g = cb.dataset.group
        let disabled = cb.checked
        document.querySelectorAll(`tr.purchase-row[data-group="${g}"] input,select`).forEach(el=>{
            if (!el.closest("td")?.innerText.includes("AUTO SKIP AREA")) el.disabled = disabled
        })
    })
})

// ================= ADD ROW ==========================
function addRow(g){
    let row = nextRow[g]++
    let noArea = document.querySelector(`tr.purchase-row[data-group="${g}"] select`).hasAttribute("disabled")

    let html = `
    <tr class="purchase-row" data-group="${g}" data-index="${row}">
        <td></td><td class="hidden"></td>
        <td>
            <select name="items[${g}][purchases][${row}][area_pembelian_id]" 
                class="area-select border p-1 text-xs w-full" ${noArea?'disabled':''}>
                ${noArea?'<option selected>Tidak ada</option>':'<option value="">Pilih Area</option>'}
            </select>
        </td>
        <td><input type="number" name="items[${g}][purchases][${row}][jumlah_beli]" class="qty border p-1 text-center w-full" ${noArea?'disabled':''}></td>
        <td><input type="number" name="items[${g}][purchases][${row}][harga]" class="border p-1 text-right w-full" ${noArea?'disabled':''}></td>
        <td><input type="date" name="items[${g}][purchases][${row}][tanggal_kadaluarsa]" class="border p-1 text-center w-full" ${noArea?'disabled':''}></td>
        <td></td>
        <td><button type="button" onclick="this.closest('tr').remove()">🗑</button></td>
    </tr>
    `

    let last = document.querySelector(`tr.purchase-row[data-group="${g}"]:last-of-type`)
    last.insertAdjacentHTML('afterend',html)
}
</script>

@endsection

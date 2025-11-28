@extends('layouts.admin')

@section('title', 'Rencana Belanja Per Warung')

@section('content')
<div class="flex-1 flex flex-col overflow-hidden">

    {{-- HEADER --}}
    <header class="flex justify-between items-center p-6 bg-white border-b-2 border-gray-200 shadow-sm">
        <button id="openSidebarBtn" class="text-gray-500 hover:text-gray-900 lg:hidden">
            <svg class="w-6 h-6" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <h1 class="text-2xl font-bold text-gray-800">Manajemen Rencana Belanja</h1>
        <div class="flex items-center">
            <span class="mr-4 font-semibold hidden sm:inline">Admin User</span>
            <div class="w-10 h-10 bg-blue-500 rounded-full"></div>
        </div>
    </header>

    {{-- ERROR --}}
    @if($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <strong>Terjadi kesalahan:</strong>
        <ul class="mt-2 list-disc list-inside text-sm">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif


    {{-- MAIN CONTENT AREA --}}
    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-6 md:p-10">

        {{-- Navigasi / Aksi Cepat --}}
        <div class="mb-8 flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4">
            <a href="{{ route('admin.transaksibarang.index') }}"
               class="bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-4 rounded-lg shadow text-sm flex items-center justify-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h11l-3-3m0 6l3-3m-3 3v7m9-14v2a2 2 0 01-2 2h-6a2 2 0 01-2-2v-2a2 2 0 012-2h6a2 2 0 012 2z"/></svg>
                Ke Stok Pengiriman
            </a>

            <a href="{{ route('admin.rencana.create') }}"
               class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg shadow text-sm flex items-center justify-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Buat Rencana Baru
            </a>
        </div>


        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">

            {{-- ------------------- KOLOM STOK GLOBAL ------------------- --}}
            {{-- Menampilkan sisa stok yang tersedia dari TransaksiBarang --}}
            <div class="md:col-span-1 bg-white p-6 rounded-xl shadow-xl h-fit border">
                <h2 class="text-xl font-bold text-gray-800 flex items-center mb-4 border-b pb-2">
                    <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10a2 2 0 002 2h12a2 2 0 002-2V9a2 2 0 00-2-2h-3m-2 0V5a2 2 0 00-2-2H9a2 2 0 00-2 2v2m5 5v10"/></svg>
                    Stok Global
                </h2>

                <div id="stokGlobalContainer" class="space-y-3 max-h-96 overflow-y-auto pr-2">
                    <p class="text-sm text-gray-500 italic">Memuat data stok...</p>
                </div>
            </div>


            {{-- ------------------- KOLOM RENCANA BELANJA ------------------- --}}
            {{-- Daftar rencana belanja yang belum selesai per warung --}}
            <div class="md:col-span-3 bg-white p-6 rounded-xl shadow-xl border">
                <form id="formKirimRencana" method="POST" action="{{ route('admin.transaksibarang.kirim.rencana.proses') }}">
                @csrf

                <div class="flex flex-col md:flex-row justify-between items-start mb-4 border-b pb-4">
                    <h2 class="text-2xl font-bold text-gray-800 flex items-center mb-2 md:mb-0">
                        <svg class="w-6 h-6 mr-2 text-red-500" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/></svg>
                        Rencana Belanja per Warung
                    </h2>

                    <button type="submit" id="btnSubmitRencana" disabled
                        class="bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg shadow text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        Kirim Rencana
                    </button>
                </div>

                <input type="text" id="searchRencana"
                        class="w-full mb-6 border px-3 py-2 rounded"
                        placeholder="Cari Warung...">

                {{-- LIST WARUNG --}}
                <div id="rencanaContainer" class="space-y-6">

                @forelse($rencanaBelanjaByWarung as $warungId => $items)
                <div class="p-4 bg-indigo-50 border-r-4 border-indigo-400 rounded item-block rencana-warung-block"
                     data-warung-id="{{ $warungId }}"
                     data-nama-warung="{{ $items[0]->warung->nama_warung }}">

                    <h3 class="font-bold text-indigo-700 text-lg mb-3 flex justify-between items-center border-b pb-1">
                        <span>{{ $items[0]->warung->nama_warung }}</span>
                        <input type="checkbox" class="chk-rencana-warung cursor-pointer" data-warung-id="{{ $warungId }}"/>
                    </h3>

                    <ul class="space-y-3">
                        @foreach($items as $i)
                        @php
                            $need = $i->jumlah_awal - $i->jumlah_dibeli;
                        @endphp

                        <li class="rencana-item p-3 border-l-4 border-indigo-500 bg-white shadow-sm"
                            data-rencana-id="{{ $i->id }}"
                            data-barang-id="{{ $i->barang->id }}"
                            data-kebutuhan="{{ $need }}">

                            <span class="font-semibold">{{ $i->barang->nama_barang }}</span>

                            <div class="grid grid-cols-1 md:grid-cols-4 gap-2 text-xs mt-1">

                                <div class="flex space-x-1">
                                    <span>Kebutuhan:</span>
                                    <strong class="text-red-600">{{ $need }}</strong>
                                </div>

                                <div class="flex items-center space-x-2">
                                    <span>Kirim:</span>
                                    <input type="number"
                                            name="items[{{ $i->id }}][jumlah]"
                                            class="rencana-qty-input border rounded px-2 py-1 w-16 text-center bg-gray-100"
                                            disabled min="1" max="{{ $need }}"
                                            value="{{ $need }}">
                                </div>

                                <div class="col-span-2 flex items-center space-x-2">
                                    <span>Stok:</span>
                                    <select name="items[{{ $i->id }}][id_transaksi_barang]"
                                        class="rencana-trx-select border rounded px-2 py-1 w-full bg-gray-100" disabled>
                                        <option value="">Pilih...</option>
                                    </select>
                                </div>

                            </div>
                        </li>
                        @endforeach
                    </ul>

                </div>
                @empty
                    <p class="text-center text-gray-500">Tidak ada rencana belanja yang belum selesai.</p>
                @endforelse

                </div>
                </form>
            </div>
        </div>
    </main>
</div>


{{-- ===================== JAVASCRIPT LOGIC ====================== --}}
<script>
document.addEventListener('DOMContentLoaded',()=>{

    // 1. Inisialisasi Data dari PHP
    const trx=@json($allTransactionsForJs??[]);
    const stockTrx={},stockBarang={},stockSisa={};

    // Mapping data transaksi barang (stok sumber)
    trx.forEach(t=>{
        let b=t.id_barang,
            nama=t.nama_barang||"Barang-"+b,
            // Logika untuk menentukan Asal Pembelian (sesuai controller)
            // asal=t.supplier?.nama_supplier||t.supplier_name||t.keterangan_pembelian||"Tanpa keterangan";
            asal=t.area_pembelian||t.supplier?.t.keterangan_pembelian||"Tanpa keterangan";

        stockTrx[t.id]={...t,nama_barang:nama,asal_pembelian:asal}; // Data lengkap transaksi sumber
        stockBarang[b]||(stockBarang[b]=[]);
        stockBarang[b].push(stockTrx[t.id]); // Pengelompokan stok sumber per barang
        stockSisa[t.id]=t.jumlah; // Sisa stok awal (dari DB)
    });


    // Fungsi format angka (separasi ribuan)
    function fmt(n){return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g,".");}


    // 2. Rendering Stok Global
    function renderStock(){
        let box=document.getElementById("stokGlobalContainer");
        box.innerHTML="";

        // Urutkan berdasarkan nama barang
        Object.keys(stockTrx).sort((a,b)=>
            stockTrx[a].nama_barang.localeCompare(stockTrx[b].nama_barang)
        ).forEach(id=>{
            let s=stockSisa[id]; // Sisa stok setelah alokasi (dinamis)
            let c=s==0?"text-red-500":s<=10?"text-yellow-600":"text-green-700";
            
            // Cek jika stok habis, tidak perlu tampilkan
            if(s <= 0) return; 

            box.innerHTML+=`
                <div class="border p-2 rounded bg-white">
                    <div class="flex justify-between text-sm font-semibold">
                        <span>${stockTrx[id].nama_barang}</span><span>TRX-${id}</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <strong class="${c}">${s} pcs</strong>
                        <span>Rp${fmt(stockTrx[id].harga)}</span>
                    </div>
                    <div class="text-xs text-indigo-600 truncate">📦 ${stockTrx[id].asal_pembelian}</div>
                </div>`;
        });
    }


    // 3. Mengisi Dropdown Stok
    function fillSelect(sel,b){
        sel.innerHTML="<option value=''>Pilih...</option>";
        // Urutkan berdasarkan ID Transaksi (biasanya yang lama/dulu dibeli)
        (stockBarang[b]||[]).sort((a,b)=>a.id-b.id).forEach(s=>{
            // Teks opsi: ID Transaksi, Sisa Stok, Harga, dan Asal Pembelian
            sel.innerHTML+=`<option value="${s.id}">#${s.id} (${stockSisa[s.id]} pcs) Rp${fmt(s.harga)} | ${s.asal_pembelian}</option>`;
        });
    }


    // 4. Perhitungan Ulang Stok (Saat ada perubahan alokasi)
    function recalc(){
        // Reset stokSisa ke nilai awal sebelum alokasi
        Object.keys(stockTrx).forEach(id=>stockSisa[id]=stockTrx[id].jumlah);

        // Kurangi stok berdasarkan alokasi pada rencana belanja yang dicentang
        document.querySelectorAll(".chk-rencana-warung:checked").forEach(ch=>{
            ch.closest(".rencana-warung-block").querySelectorAll(".rencana-item").forEach(i=>{
                let qty=parseInt(i.querySelector(".rencana-qty-input").value),
                    trx=i.querySelector(".rencana-trx-select").value;
                
                if(trx && qty > 0){
                    stockSisa[trx]-=qty;
                    // pastikan stok tidak negatif
                    if(stockSisa[trx]<0) stockSisa[trx]=0; 
                }
            });
        });

        updateOptionText(); // Update teks dropdown dengan sisa stok baru
        renderStock(); // Render ulang kolom stok global
    }


    // 5. Update Teks Opsi Dropdown
    function updateOptionText(){
        document.querySelectorAll(".rencana-trx-select").forEach(s=>{
            [...s.options].forEach(o=>{
                let id=o.value;if(!id)return;
                // Update sisa stok dan asal pembelian di teks opsi
                o.textContent=`#${id} (${stockSisa[id]} pcs) Rp${fmt(stockTrx[id].harga)} | ${stockTrx[id].asal_pembelian}`;
                
                // Non-aktifkan opsi jika stok sisa 0 DAN opsi tersebut BUKAN yang sedang dipilih
                o.disabled=stockSisa[id]<=0 && o.value!=s.value;
            });
        });
    }


    // 6. Event Listeners untuk Warung Checkbox
    document.querySelectorAll(".rencana-warung-block").forEach(block=>{
        let chk=block.querySelector(".chk-rencana-warung");

        chk.addEventListener("change",()=>{
            let enable=chk.checked;
            block.querySelectorAll(".rencana-item").forEach(i=>{
                let qty=i.querySelector(".rencana-qty-input"),
                    sel=i.querySelector(".rencana-trx-select"),
                    id=i.dataset.barangId;

                // Aktifkan/non-aktifkan input/select
                qty.disabled=sel.disabled=!enable;
                
                if(enable){
                    fillSelect(sel,id); // Isi dropdown saat diaktifkan
                    
                    // Tambahkan event listener untuk perubahan
                    sel.addEventListener("change",recalc);
                    qty.addEventListener("input",recalc);
                } else {
                    // Hapus event listener saat dinonaktifkan (opsional, tapi baik)
                    sel.removeEventListener("change",recalc);
                    qty.removeEventListener("input",recalc);
                    // Reset value saat dinonaktifkan
                    sel.value = ''; 
                    qty.value = qty.getAttribute('max');
                }
            });
            recalc(); // Hitung ulang setelah aktivasi/non-aktivasi
            toggleSubmit(); // Atur status tombol kirim
        });
    });


    // 7. Toggle Tombol Kirim
    function toggleSubmit(){
        let any=[...document.querySelectorAll(".chk-rencana-warung:checked")].length;
        document.getElementById("btnSubmitRencana").disabled=!any;
    }


    // 8. Fungsi Pencarian Warung
    document.getElementById("searchRencana").addEventListener("input",e=>{
        let q=e.target.value.toLowerCase();
        document.querySelectorAll(".rencana-warung-block").forEach(b=>{
            b.style.display=b.dataset.namaWarung.toLowerCase().includes(q)?"block":"none";
        });
    });

    // Panggil saat halaman dimuat
    renderStock();
});
</script>

@endsection
@extends('layouts.admin')

@section('title','Rencana Belanja Per Warung')
@section('content')
<div class="flex-1 flex flex-col overflow-hidden">

    {{-- HEADER --}}
    <header class="flex justify-between items-center p-6 bg-white border-b shadow">
        <h1 class="text-2xl font-bold">Manajemen Rencana Belanja</h1>
    </header>

    @if($errors->any())
    <div class="bg-red-100 text-red-700 px-4 py-3 rounded mb-4">
        <strong>Kesalahan:</strong>
        <ul class="list-disc ml-5 text-sm">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif


    <main class="flex-1 overflow-y-auto bg-gray-50 p-6">

        <div class="flex gap-3 mb-6">
            <a href="{{ route('admin.transaksibarang.index') }}" class="btn bg-indigo-600 text-white px-4 py-2 rounded shadow">
                Ke Stok Pengiriman
            </a>
            <a href="{{ route('admin.rencana.create') }}" class="btn bg-blue-600 text-white px-4 py-2 rounded shadow">
                Buat Rencana Baru
            </a>
        </div>


        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">

            {{-- ==================== STOK GLOBAL (DETAIL PER TRX & AREA) ==================== --}}
            <div class="bg-white p-5 shadow rounded border h-fit">
                <h2 class="font-bold text-lg mb-3 border-b pb-2">Stok Global (Detail TRX)</h2>
                <div id="stokGlobalContainer" class="space-y-2 max-h-96 overflow-y-auto pr-1 text-sm text-gray-700">
                    Load...
                </div>
            </div>


            {{-- ==================== LIST RENCANA ==================== --}}
            <div class="md:col-span-3 bg-white p-6 shadow rounded border">

                <form id="formKirimRencana" method="POST" action="{{ route('admin.transaksibarang.kirim.rencana.proses') }}">
                    @csrf

                    <div class="flex justify-between items-center mb-4 border-b pb-3">
                        <h2 class="text-xl font-bold">Rencana Belanja per Warung</h2>

                        <button id="btnSubmitRencana" disabled
                            class="bg-green-600 text-white px-4 py-2 rounded disabled:opacity-40">
                            Kirim Rencana
                        </button>
                    </div>

                    <input type="text" id="searchRencana" placeholder="Cari Warung..."
                            class="border px-3 py-2 rounded mb-5 w-full">


                    <div id="rencanaContainer" class="space-y-6">

                        @forelse($rencanaBelanjaByWarung as $warungId=>$items)

                        <div class="p-4 bg-indigo-50 border-l-4 border-indigo-500 rounded rencana-warung-block"
                            data-warung-id="{{ $warungId }}"
                            data-nama-warung="{{ $items[0]->warung->nama_warung }}">

                            <div class="flex justify-between border-b pb-1 mb-2">
                                <h3 class="font-bold text-indigo-700">{{ $items[0]->warung->nama_warung }}</h3>
                                <input type="checkbox" class="chk-rencana-warung" data-warung-id="{{ $warungId }}">
                            </div>

                            <ul class="space-y-3">

                                @foreach($items as $i)
                                <li class="p-3 bg-white border shadow-sm rounded rencana-item"
                                    data-rencana-id="{{ $i->id }}"
                                    data-barang-id="{{ $i->barang->id }}"
                                    data-kebutuhan="{{ $i->jumlah_awal }}">

                                    <div class="font-semibold">{{ $i->barang->nama_barang }} 
                                        {{-- Penambahan span untuk menampilkan total kirim --}}
                                        <span class="ml-2 text-indigo-500 font-normal">(Kirim: <span class="total-kirim-span">0</span>)</span>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-4 gap-2 mt-1 text-xs">

                                        <span>Kebutuhan:
                                            <strong class="text-red-600">{{ $i->jumlah_awal }}</strong>
                                        </span>

                                        <div class="md:col-span-3">
                                            {{-- Kontainer untuk opsi multi-select --}}
                                            <div id="multiTrxContainer-{{ $i->id }}" class="space-y-2">
                                                <p class="text-gray-400">Pilih sumber stok di bawah.</p>
                                            </div>
                                            
                                            {{-- Tombol untuk menambah opsi multi-select --}}
                                            <button type="button" 
                                                    data-rencana-id="{{ $i->id }}" 
                                                    data-barang-id="{{ $i->barang->id }}"
                                                    class="btn-add-trx mt-2 text-blue-600 hover:text-blue-800 text-sm disabled:opacity-50" disabled>
                                                + Tambah Sumber Stok
                                            </button>
                                        </div>

                                    </div>
                                </li>
                                @endforeach

                            </ul>
                        </div>

                        @empty
                            <p class="text-center text-gray-500">Tidak ada rencana.</p>
                        @endforelse

                    </div>

                </form>
            </div>
        </div>
    </main>
</div>


{{-- ================= JS ================= --}}
<script>
document.addEventListener("DOMContentLoaded",()=>{

    // Data dari Controller: jumlah sudah merupakan STOK REAL (jumlah - jumlah_terpakai)
    const trx=@json($allTransactionsForJs??[]); 
    const stockTrx={}, stockBarang={}, stockSisa={};

    trx.forEach(t=>{
        stockTrx[t.id]=t;
        stockBarang[t.id_barang]??=[];
        stockBarang[t.id_barang].push(t);
        stockSisa[t.id]=t.jumlah; // Jumlah di sini adalah STOK REAL
    });

    const fmt=n=>n.toLocaleString("id-ID");

    /* ================= STOK GLOBAL (RENDER DETAIL PER TRX & AREA) ================= */
    function renderStock(){
        let el=document.getElementById("stokGlobalContainer");
        el.innerHTML="";

        // Tampilkan detail stok per transaksi (TRX) termasuk Area Pembelian
        Object.values(stockTrx).sort((a,b)=>a.nama_barang.localeCompare(b.nama_barang)).forEach(s=>{
            let x=stockSisa[s.id];
            if(x<=0) return; // Hanya tampilkan stok yang tersisa
            
            let color = x < 5 ? 'text-red-600' : x < 15 ? 'text-yellow-600' : 'text-green-700';

            el.innerHTML+=`
            <div class="border p-2 rounded">
                <div class="flex justify-between font-semibold">
                    <span>${s.nama_barang}</span><span>#${s.id}</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="${color}">${x} pcs</span>
                    <span>${s.area}</span>
                </div>
            </div>`;
        });
    }


    /* ================= MULTI-SELECT ITEM ================= */
    // Fungsi untuk membuat elemen select
    function createTrxSelect(rencanaId, barangId, container) {
        const itemIndex = container.children.length; // Sebagai index untuk form array
        const selectId = `trx-select-${rencanaId}-${itemIndex}`;
        const inputId = `qty-input-${rencanaId}-${itemIndex}`;
        
        const wrapper = document.createElement('div');
        wrapper.className = 'flex items-center gap-2 p-2 bg-gray-50 border rounded trx-item-wrapper';
        
        // Input QTY
        wrapper.innerHTML += `
            <input type="number" min="1" max="9999" value="1"
                name="items[${rencanaId}][transactions][${itemIndex}][jumlah]"
                id="${inputId}"
                class="rencana-qty-input border rounded w-16 text-center text-xs"
                data-rencana-id="${rencanaId}"
                data-barang-id="${barangId}"
                data-index="${itemIndex}">
        `;

        // Select TRX
        const sel = document.createElement('select');
        sel.name = `items[${rencanaId}][transactions][${itemIndex}][id_transaksi_barang]`;
        sel.id = selectId;
        sel.className = 'rencana-trx-select border rounded flex-1 text-xs bg-white';
        
        // Remove button
        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'text-red-500 hover:text-red-700 font-bold text-lg leading-none';
        removeBtn.textContent = '×';
        removeBtn.onclick = () => {
            wrapper.remove();
            recalc();
            // Cek apakah container kosong, jika iya tampilkan placeholder
            if (container.children.length === 0) {
                 container.innerHTML = '<p class="text-gray-400">Pilih sumber stok di bawah.</p>';
            }
        };

        wrapper.appendChild(sel);
        wrapper.appendChild(removeBtn);
        container.appendChild(wrapper);

        // Isi dan tambahkan event listener
        fillSelect(sel, barangId, inputId);
        document.getElementById(inputId).oninput = recalc; // QTY input event
        sel.onchange = recalc; // Select change event

        // Hapus placeholder jika ada
        if (container.querySelector('p.text-gray-400')) {
            container.querySelector('p.text-gray-400').remove();
        }

        return sel;
    }

    // Fungsi untuk mengisi opsi select TRX
    function fillSelect(sel, barangId, qtyInputId){
        sel.innerHTML=`<option value="">Pilih Sumber</option>`;
        const qtyInput = document.getElementById(qtyInputId);

        (stockBarang[barangId]??[]).forEach(s=>{
            sel.innerHTML+=`<option value="${s.id}">
                TRX-${s.id} (${s.area}) • ${stockSisa[s.id]} pcs (Rp${fmt(s.harga)})
            </option>`;
        });

        // Event listener untuk membatasi input QTY berdasarkan stok yang tersedia
        sel.addEventListener('change', () => {
            const selectedTrxId = sel.value;
            if (selectedTrxId) {
                // Maksimum QTY adalah sisa stok yang belum teralokasi
                const maxStock = stockSisa[selectedTrxId] + (qtyInput.dataset.allocated | 0); // Tambah alokasi lama jika ada
                qtyInput.setAttribute('max', maxStock);
                
                // Jika QTY melebihi max, set ke max.
                if ((qtyInput.value | 0) > maxStock) {
                     qtyInput.value = maxStock;
                }
            } else {
                 qtyInput.removeAttribute('max');
            }
            recalc(); // Panggil recalc setelah perubahan nilai select
        });
    }

    /* ================= RECALC (LOGIKA PENTING) ================= */
    function recalc(){
        // 1. Reset Sisa Stok (Global)
        // stockTrx[i].jumlah sudah berisi STOK REAL dari controller
        Object.keys(stockSisa).forEach(i=>stockSisa[i]=stockTrx[i].jumlah); 
        
        // 2. Kalkulasi Total Kirim per Warung & Kurangi Stok
        let totalWarungChecked = 0;
        let totalKirimPerRencana = {};

        document.querySelectorAll(".chk-rencana-warung:checked").forEach(ch => {
            totalWarungChecked++;
            
            ch.closest(".rencana-warung-block").querySelectorAll(".rencana-item").forEach(i => {
                const rencanaId = i.dataset.rencanaId;
                totalKirimPerRencana[rencanaId] = 0;

                i.querySelectorAll(".trx-item-wrapper").forEach(wrapper => {
                    const qtyInput = wrapper.querySelector(".rencana-qty-input");
                    const sel = wrapper.querySelector(".rencana-trx-select");
                    
                    const qty = qtyInput.value | 0;
                    const trx = sel.value;

                    // Update total kirim untuk item rencana ini
                    totalKirimPerRencana[rencanaId] += qty;

                    if (trx) {
                        // Kurangi stok global
                        stockSisa[trx] -= qty;
                        qtyInput.dataset.allocated = qty; // Simpan alokasi saat ini
                    } else {
                        qtyInput.dataset.allocated = 0;
                    }
                });
                
                // 3. Tampilkan Total Kirim & Validasi Visual
                const kebutuhan = i.dataset.kebutuhan | 0;
                const totalKirim = totalKirimPerRencana[rencanaId];
                
                const kirimTotalSpan = i.querySelector(".total-kirim-span");

                if (kirimTotalSpan) {
                    kirimTotalSpan.textContent = totalKirim;
                }
                
                // Visual feedback jika total kirim > kebutuhan
                i.style.backgroundColor = totalKirim > kebutuhan ? 'rgba(255, 235, 238, 0.7)' : 'white';
                
            });
        });

        // 4. Update Opsi Select dan Stok Global
        document.querySelectorAll(".rencana-trx-select").forEach(s=>{
            const currentTrx = s.value;
            const qtyInput = s.closest(".trx-item-wrapper").querySelector(".rencana-qty-input");
            const allocated = qtyInput.dataset.allocated | 0;

            [...s.options].forEach(o=>{
                if(!o.value) return;
                
                const sisaSaatIni = stockSisa[o.value];
                const stokAwal = stockTrx[o.value].jumlah;

                let sisaDisplay = sisaSaatIni;
                
                // Jika opsi ini adalah yang sedang dipilih, tambahkan kembali alokasi saat ini untuk ditampilkan
                if (o.value == currentTrx) {
                    sisaDisplay += allocated;
                }

                // Update text opsi select, termasuk AREA dan SISA STOK
                o.textContent=`TRX-${o.value} (${stockTrx[o.value].area}) • ${sisaDisplay} pcs (Rp${fmt(stockTrx[o.value].harga)})`;
                
                // Disable jika stok <= 0 DAN bukan opsi yang sedang dipilih
                o.disabled=sisaSaatIni <= 0 && s.value != o.value; 
            });
            
            // Set max attribute pada QTY input
            if (currentTrx) {
                const maxStock = stockSisa[currentTrx] + allocated;
                qtyInput.setAttribute('max', maxStock);
                
                // Jika QTY melebihi max, set ke max
                if ((qtyInput.value | 0) > maxStock) {
                    qtyInput.value = maxStock;
                }
            } else {
                 qtyInput.removeAttribute('max');
            }
        });

        // Render stok global
        renderStock();
        
        // Atur tombol submit
        document.getElementById("btnSubmitRencana").disabled = totalWarungChecked === 0;
    }

    /* ================= EVENT LISTENERS ================= */
    document.querySelectorAll(".chk-rencana-warung").forEach(ch=>{
        ch.addEventListener("change",()=>{
            let b=ch.closest(".rencana-warung-block");
            const isChecked = ch.checked;
            
            // Atur disabled state untuk tombol 'Tambah Sumber Stok' di warung ini
            b.querySelectorAll(".btn-add-trx").forEach(btn => {
                btn.disabled = !isChecked;
            });
            
            // Atur disabled state untuk semua input dan select di warung ini
            b.querySelectorAll(".rencana-item").forEach(i=>{
                i.querySelectorAll("input, select").forEach(el => {
                    el.disabled = !isChecked;
                });
                
                // Hapus semua select jika tidak dicentang (untuk bersih-bersih)
                if (!isChecked) {
                    const container = i.querySelector(`div[id^="multiTrxContainer-"]`);
                    // Hanya hapus elemen trx, sisakan placeholder jika ada
                    container.querySelectorAll('.trx-item-wrapper').forEach(w => w.remove());
                    if (!container.querySelector('p.text-gray-400')) {
                        container.innerHTML = '<p class="text-gray-400">Pilih sumber stok di bawah.</p>';
                    }
                }
            });
            
            // Re-kalkulasi setelah perubahan checkbox
            recalc();
        })
    });
    
    // Event listener untuk tombol tambah sumber stok
    document.querySelectorAll(".btn-add-trx").forEach(btn => {
        btn.addEventListener("click", () => {
            const rencanaId = btn.dataset.rencanaId;
            const barangId = btn.dataset.barangId;
            const container = document.getElementById(`multiTrxContainer-${rencanaId}`);
            
            createTrxSelect(rencanaId, barangId, container);
            recalc();
        });
    });


    // Listener Pencarian
    document.getElementById("searchRencana").oninput=e=>{
        let q=e.target.value.toLowerCase();
        document.querySelectorAll(".rencana-warung-block").forEach(b=>{
            b.style.display=b.dataset.namaWarung.toLowerCase().includes(q)?"block":"none";
        });
    };

    renderStock();
});
</script>
@endsection
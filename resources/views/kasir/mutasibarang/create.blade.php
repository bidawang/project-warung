@extends('layouts.app')

@section('title', 'Tambah Mutasi Barang')

@section('content')
<div class="min-h-screen bg-gray-50/50 py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- TOMBOL KEMBALI & HEADER --}}
        <div class="mb-8">
            <a href="{{ route('mutasibarang.index') }}" 
               class="inline-flex items-center gap-2 text-sm font-semibold text-gray-500 hover:text-yellow-600 transition-colors mb-4">
                <i class="fas fa-arrow-left"></i>
                Kembali ke Manajemen Mutasi
            </a>
            
            <div class="flex items-center gap-4">
                <div class="bg-yellow-500 p-3 rounded-2xl shadow-sm">
                    <i class="fas fa-truck-loading text-white text-xl"></i>
                </div>
                <div>
                    <h3 class="text-3xl font-extrabold text-gray-900 tracking-tight">Buat Mutasi Barang</h3>
                    <p class="text-gray-500">Pindahkan stok barang antar unit warung dengan mudah.</p>
                </div>
            </div>
        </div>

        @if($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-xl shadow-sm">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-500"></i>
                    </div>
                    <div class="ml-3">
                        <ul class="text-sm text-red-700 font-medium">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form id="mutasiForm" action="{{ route('mutasibarang.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                
                {{-- LEFT SIDE: CONFIGURATION --}}
                <div class="md:col-span-1 space-y-6">
                    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Unit Warung Tujuan</label>
                        <select name="warung_tujuan" class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-yellow-500 focus:border-yellow-500 transition-all font-semibold" required>
                            <option value="">-- Pilih Tujuan --</option>
                            @foreach($warungTujuan as $w)
                                <option value="{{ $w->id }}">{{ $w->nama_warung }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Catatan Tambahan</label>
                        <textarea name="keterangan" rows="4" 
                                  placeholder="Contoh: Stok cadangan untuk akhir pekan..."
                                  class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-yellow-500 focus:border-yellow-500 transition-all"></textarea>
                    </div>
                </div>

                {{-- RIGHT SIDE: BARANG SELECTION --}}
                <div class="md:col-span-2">
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
                            <h5 class="text-sm font-bold text-gray-700">Daftar Stok Tersedia</h5>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50/50 text-[10px] font-bold text-gray-400 uppercase">
                                    <tr>
                                        <th class="px-6 py-4 text-center w-16">Pilih</th>
                                        <th class="px-4 py-4 text-left">Informasi Barang</th>
                                        <th class="px-4 py-4 text-right w-32">Jumlah Mutasi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($barangTersedia as $barang)
                                    <tr class="hover:bg-yellow-50/30 transition-colors group">
                                        <td class="px-6 py-4 text-center">
                                            <input type="checkbox" name="barang[{{ $barang->id_stok_warung }}][pilih]" value="1" 
                                                   class="barang-checkbox w-5 h-5 rounded border-gray-300 text-yellow-500 focus:ring-yellow-400 cursor-pointer">
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="font-bold text-gray-800">{{ $barang->nama_barang }}</div>
                                            <div class="text-xs text-gray-400">Tersedia: <span class="font-bold text-gray-600">{{ $barang->stok_saat_ini }} Unit</span></div>
                                        </td>
                                        <td class="px-4 py-4">
                                            <input type="number" name="barang[{{ $barang->id_stok_warung }}][jumlah]" 
                                                   class="jumlah-input w-full bg-transparent border-b-2 border-gray-100 focus:border-yellow-500 focus:ring-0 text-right font-black text-lg text-gray-400 transition-all py-1 disabled:opacity-30" 
                                                   min="1" max="{{ $barang->stok_saat_ini }}" disabled placeholder="0">
                                            <input type="hidden" name="barang[{{ $barang->id_stok_warung }}][id_stok_warung]" value="{{ $barang->id_stok_warung }}">
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-3">
                        <button type="button" id="btnMutasi" 
                                class="bg-yellow-500 hover:bg-yellow-600 text-white px-10 py-3 rounded-xl font-bold transition shadow-lg shadow-yellow-200">
                            Proses Mutasi
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- MODAL KONFIRMASI (BOOTSTRAP 5) --}}
<div class="modal fade" id="konfirmasiModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-2xl rounded-3xl overflow-hidden">
            <div class="modal-body p-8">
                <div class="text-center mb-6">
                    <div class="w-20 h-20 bg-yellow-50 text-yellow-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-question-circle text-4xl"></i>
                    </div>
                    <h5 class="text-xl font-black text-gray-900">Konfirmasi Mutasi</h5>
                    <p class="text-gray-500 text-sm mt-1">Pastikan daftar barang dan jumlah sudah benar.</p>
                </div>
                
                <div class="bg-gray-50 rounded-2xl p-4 mb-6 border border-gray-100 max-h-60 overflow-y-auto">
                    <table class="w-full text-sm">
                        <tbody id="konfirmasiList" class="divide-y divide-gray-200">
                            </tbody>
                    </table>
                </div>

                <div class="flex gap-3">
                    <button type="button" class="flex-1 py-3 rounded-xl font-bold text-gray-400 hover:bg-gray-100 transition" data-bs-dismiss="modal">Batal</button>
                    <button type="button" id="konfirmasiSubmit" class="flex-1 bg-yellow-500 text-white py-3 rounded-xl font-bold shadow-lg shadow-yellow-100 hover:bg-yellow-600 transition">Ya, Kirim Sekarang</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Toggle input jumlah saat checkbox di klik
        document.querySelectorAll(".barang-checkbox").forEach(cb => {
            cb.addEventListener("change", function() {
                let row = this.closest("tr");
                let jumlahInput = row.querySelector(".jumlah-input");
                
                if(this.checked) {
                    jumlahInput.disabled = false;
                    jumlahInput.focus();
                    jumlahInput.classList.remove('text-gray-400');
                    jumlahInput.classList.add('text-yellow-600');
                    row.classList.add('bg-yellow-50/50');
                } else {
                    jumlahInput.disabled = true;
                    jumlahInput.value = "";
                    jumlahInput.classList.add('text-gray-400');
                    jumlahInput.classList.remove('text-yellow-600');
                    row.classList.remove('bg-yellow-50/50');
                }
            });
        });

        // Trigger Modal Konfirmasi
        document.getElementById("btnMutasi").addEventListener("click", function() {
            let list = document.getElementById("konfirmasiList");
            list.innerHTML = "";
            let selectedCount = 0;

            document.querySelectorAll(".barang-checkbox:checked").forEach(cb => {
                let row = cb.closest("tr");
                let namaBarang = row.querySelector('.font-bold').innerText;
                let jumlah = row.querySelector(".jumlah-input").value;

                if (jumlah > 0) {
                    selectedCount++;
                    list.innerHTML += `
                        <tr>
                            <td class="py-2 font-medium text-gray-700">${namaBarang}</td>
                            <td class="py-2 text-right font-black text-yellow-600">${jumlah} Unit</td>
                        </tr>`;
                }
            });

            if (selectedCount === 0) {
                alert("Silakan pilih minimal satu barang dan masukkan jumlah mutasi!");
                return;
            }

            let myModal = new bootstrap.Modal(document.getElementById('konfirmasiModal'));
            myModal.show();
        });

        document.getElementById("konfirmasiSubmit").addEventListener("click", function() {
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
            this.disabled = true;
            document.getElementById("mutasiForm").submit();
        });

        // Validasi stok maksimal di client-side
        document.querySelectorAll(".jumlah-input").forEach(input => {
            input.addEventListener("input", function() {
                let max = parseInt(this.getAttribute("max"));
                if (this.value > max) {
                    alert(`Stok tidak mencukupi! Maksimal stok adalah ${max}`);
                    this.value = max;
                }
            });
        });
    });
</script>
@endsection
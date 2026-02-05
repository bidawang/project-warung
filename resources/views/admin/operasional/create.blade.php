@extends('layouts.admin')

@section('title', 'Tambah Biaya Operasional')

@section('content')
<div class="max-w-4xl mx-auto" x-data="operasionalForm()">
    
    <form action="{{ route('admin.operasional.store') }}" method="POST" class="space-y-6">
        @csrf
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-warning">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center mb-4 text-blue-600">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <h3 class="text-lg font-bold">Informasi Utama</h3>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan Umum / Judul Pengeluaran</label>
                <input type="text" name="keterangan_umum" required placeholder="Contoh: Operasional Mingguan Januari"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition">
                <p class="mt-1 text-xs text-gray-400 italic">*Dana akan otomatis dipotong dari Saldo WRB Old</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h3 class="text-lg font-bold text-gray-800">Rincian Biaya</h3>
                <button type="button" @click="addBaris" class="text-sm bg-blue-50 text-blue-600 px-3 py-1.5 rounded-md hover:bg-blue-100 font-bold transition">
                    + Tambah Baris
                </button>
            </div>

            <div class="p-6">
                <table class="w-full">
                    <thead>
                        <tr class="text-left text-xs uppercase text-gray-400 font-bold border-b">
                            <th class="pb-3 pl-2">Keterangan Item</th>
                            <th class="pb-3 w-1/3">Harga/Biaya (Rp)</th>
                            <th class="pb-3 w-12 text-center"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <template x-for="(item, index) in rows" :key="index">
                            <tr>
                                <td class="py-4 pr-4">
                                    <input type="text" :name="'lain_keterangan['+index+']'" required x-model="item.ket"
                                        placeholder="Misal: Bensin Kurir"
                                        class="w-full border-none focus:ring-0 p-0 text-sm text-gray-800 placeholder-gray-300">
                                </td>
                                <td class="py-4">
                                    <input type="number" :name="'lain_harga['+index+']'" required x-model.number="item.harga"
                                        placeholder="0"
                                        class="w-full border-none focus:ring-0 p-0 text-sm font-bold text-gray-800 placeholder-gray-300">
                                </td>
                                <td class="py-4 text-center">
                                    <button type="button" @click="removeBaris(index)" x-show="rows.length > 1" class="text-red-400 hover:text-red-600">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"/></svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>

                <div class="mt-6 pt-6 border-t flex justify-between items-center">
                    <span class="text-gray-500 font-bold uppercase text-xs">Estimasi Total Pengeluaran:</span>
                    <span class="text-2xl font-black text-red-600" x-text="formatRupiah(calculateTotal())"></span>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end space-x-4">
            <a href="{{ route('admin.operasional.index') }}" class="text-gray-500 hover:text-gray-700 font-medium">Batal</a>
            <button type="submit" class="bg-gray-800 text-white px-8 py-3 rounded-xl font-bold hover:bg-black shadow-lg transition transform active:scale-95">
                Simpan Transaksi
            </button>
        </div>
    </form>
</div>

<script>
    function operasionalForm() {
        return {
            rows: [{ ket: '', harga: 0 }],
            addBaris() {
                this.rows.push({ ket: '', harga: 0 });
            },
            removeBaris(index) {
                this.rows.splice(index, 1);
            },
            calculateTotal() {
                return this.rows.reduce((sum, row) => sum + (parseFloat(row.harga) || 0), 0);
            },
            formatRupiah(number) {
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(number);
            }
        }
    }
</script>
@endsection
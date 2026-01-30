@extends('layouts.admin')

@section('title', 'Inject Kas Baru')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6 border-b border-gray-100 bg-gray-50/50">
            <h3 class="text-lg font-bold text-gray-800">Formulir Terima/Suntik Kas</h3>
        </div>
        
        <form action="{{ route('admin.inject-kas.store') }}" method="POST" class="p-6 space-y-5">
            @csrf
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Warung</label>
                <select name="id_warung" class="w-full p-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition" required>
                    <option value="">-- Pilih Warung Tujuan --</option>
                    @foreach($warungs as $w)
                        <option value="{{ $w->id }}">{{ $w->nama_warung }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Tujuan Jenis Kas</label>
                <div class="grid grid-cols-2 gap-4">
                    <label class="border p-4 rounded-xl cursor-pointer hover:bg-gray-50 flex items-center justify-between has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50">
                        <span class="font-bold text-gray-700">CASH (Tunai)</span>
                        <input type="radio" name="jenis_kas" value="cash" class="w-4 h-4 text-blue-600" checked>
                    </label>
                    <label class="border p-4 rounded-xl cursor-pointer hover:bg-gray-50 flex items-center justify-between has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50">
                        <span class="font-bold text-gray-700">BANK (Transfer)</span>
                        <input type="radio" name="jenis_kas" value="bank" class="w-4 h-4 text-blue-600">
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Jumlah Saldo (Rp)</label>
                <input type="number" name="total" placeholder="Contoh: 1000000" class="w-full p-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition" required>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Keterangan</label>
                <textarea name="keterangan" rows="2" placeholder="Alasan inject kas (misal: Penambahan Modal Awal)" class="w-full p-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition" required></textarea>
            </div>

            <div class="pt-4 flex gap-3">
                <a href="{{ route('admin.inject-kas.index') }}" class="flex-1 text-center py-3 px-4 border border-gray-200 rounded-xl text-gray-600 hover:bg-gray-50 transition font-bold">Batal</a>
                <button type="submit" class="flex-2 bg-blue-600 text-white py-3 px-8 rounded-xl font-bold hover:bg-blue-700 shadow-lg shadow-blue-200 transition">Proses Inject</button>
            </div>
        </form>
    </div>
</div>
@endsection
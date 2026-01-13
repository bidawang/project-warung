@extends('layouts.admin')

@section('title', 'Tambah Warung')

@section('content')
<div class="p-4 bg-gray-50 min-h-screen">
    {{-- HEADER AREA --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-800">Tambah Warung Baru</h1>
            <p class="text-sm text-gray-500">Daftarkan warung baru ke dalam sistem manajemen</p>
        </div>
        <a href="{{ route('admin.warung.index') }}" 
           class="bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 font-bold py-2 px-6 rounded-full shadow-sm transition text-center">
            Kembali
        </a>
    </div>

    <div class="max-w-4xl mx-auto">
        <form action="{{ route('admin.warung.store') }}" method="POST">
            @csrf

            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="p-6 md:p-8 space-y-6">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Pemilik (User) --}}
                        <div>
                            <label for="id_user" class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wider">Pemilik (User) <span class="text-red-500">*</span></label>
                            <select id="id_user" name="id_user" class="select2-init w-full" required>
                                <option value="">-- Cari Pemilik --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('id_user') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('id_user')
                                <p class="text-red-500 text-xs mt-1 italic">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Area --}}
                        <div>
                            <label for="id_area" class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wider">Area <span class="text-red-500">*</span></label>
                            <select id="id_area" name="id_area" class="select2-init w-full" required>
                                <option value="">-- Cari Area --</option>
                                @foreach($areas as $area)
                                    <option value="{{ $area->id }}" {{ old('id_area') == $area->id ? 'selected' : '' }}>
                                        {{ $area->area }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_area')
                                <p class="text-red-500 text-xs mt-1 italic">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Nama Warung --}}
                        <div>
                            <label for="nama_warung" class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wider">Nama Warung <span class="text-red-500">*</span></label>
                            <input type="text" id="nama_warung" name="nama_warung" value="{{ old('nama_warung') }}"
                                   placeholder="Contoh: Warung Berkah"
                                   class="w-full border-gray-300 rounded-lg p-2.5 focus:ring-blue-500 focus:border-blue-500 shadow-sm transition" required>
                        </div>

                        {{-- Modal --}}
                        <div>
                            <label for="modal" class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wider">Modal Awal (Rp) <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 font-bold text-sm">Rp</span>
                                <input type="number" id="modal" name="modal" value="{{ old('modal') }}"
                                       class="w-full border-gray-300 rounded-lg p-2.5 pl-10 focus:ring-blue-500 focus:border-blue-500 shadow-sm transition" required>
                            </div>
                        </div>
                    </div>

                    {{-- Keterangan --}}
                    <div>
                        <label for="keterangan" class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wider">Keterangan</label>
                        <textarea id="keterangan" name="keterangan" rows="3"
                                  placeholder="Tambahkan catatan jika ada..."
                                  class="w-full border-gray-300 rounded-lg p-2.5 focus:ring-blue-500 focus:border-blue-500 shadow-sm transition">{{ old('keterangan') }}</textarea>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t border-gray-100">
                    <a href="{{ route('admin.warung.index') }}" class="text-gray-600 hover:text-gray-800 font-bold py-2 px-6 transition text-sm flex items-center">
                        Batal
                    </a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-8 rounded-full shadow-lg transition transform hover:scale-105 active:scale-95 text-sm">
                        Simpan Warung
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- CSS Tambahan untuk Merapikan Select2 agar mirip Tailwind --}}
<style>
    .select2-container--default .select2-selection--single {
        border: 1px solid #d1d5db !important;
        border-radius: 0.5rem !important;
        height: 42px !important;
        padding: 5px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        top: 8px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #374151 !important;
    }
</style>

{{-- Script untuk inisialisasi Select2 --}}
<script>
    $(document).ready(function() {
        $('.select2-init').select2({
            width: '100%',
            placeholder: "-- Pilih --",
            allowClear: true
        });
    });
</script>
@endsection
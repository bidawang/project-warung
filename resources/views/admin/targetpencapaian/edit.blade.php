@extends('layouts.admin')

@section('title', 'Edit Target Pencapaian')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="bg-blue-600 px-6 py-4">
            <h2 class="text-lg font-bold text-white">Edit Target Pencapaian</h2>
        </div>
        <div class="p-6">
            <form action="{{ route('admin.targetpencapaian.update', $targetpencapaian->id) }}" method="POST" class="space-y-5">
                @csrf @method('PUT')

                {{-- Warung --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Warung</label>
                    <select name="id_warung" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200 focus:border-blue-400">
                        @foreach($warung as $w)
                            <option value="{{ $w->id }}" {{ $w->id == $targetpencapaian->id_warung ? 'selected' : '' }}>
                                {{ $w->nama_warung }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Periode --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Periode Awal</label>
                        <input type="date" name="periode_awal" value="{{ $targetpencapaian->periode_awal }}"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200 focus:border-blue-400" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Periode Akhir</label>
                        <input type="date" name="periode_akhir" value="{{ $targetpencapaian->periode_akhir }}"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200 focus:border-blue-400" required>
                    </div>
                </div>

                {{-- Target --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Target Pencapaian</label>
                    <input type="number" name="target_pencapaian" value="{{ $targetpencapaian->target_pencapaian }}"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200 focus:border-blue-400" required>
                </div>

                {{-- Status --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status_pencapaian" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200 focus:border-blue-400">
                        <option value="belum tercapai" {{ $targetpencapaian->status_pencapaian == 'belum tercapai' ? 'selected' : '' }}>Belum Tercapai</option>
                        <option value="tercapai" {{ $targetpencapaian->status_pencapaian == 'tercapai' ? 'selected' : '' }}>Tercapai</option>
                    </select>
                </div>

                {{-- Keterangan --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                    <textarea name="keterangan" rows="3"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200 focus:border-blue-400">{{ $targetpencapaian->keterangan }}</textarea>
                </div>

                {{-- Action --}}
                <div class="flex justify-between items-center pt-4 border-t">
                    <a href="{{ route('admin.targetpencapaian.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-lg shadow hover:bg-gray-300 transition">
                        ← Batal
                    </a>
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg shadow hover:bg-blue-700 transition">
                        💾 Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

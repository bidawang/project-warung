@extends('layouts.admin')

@section('title', 'Tambah Aturan Tenggat')

@section('content')
<div class="flex-1 flex flex-col overflow-hidden">
    <header class="flex justify-between items-center p-6 bg-white border-b-2 border-gray-200">
        <h1 class="text-2xl font-bold text-gray-800">Tambah Aturan Tenggat {{ $area->area ?? '' }}</h1>
        <a href="{{ route('admin.aturanTenggat.index', ['id_area' => $area->id]) }}"
           class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-6 rounded-full">
           Kembali
        </a>
    </header>

    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6 md:p-10">
        <div class="bg-white shadow-md rounded-lg p-8">
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-lg">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.aturanTenggat.store') }}" method="POST" class="w-full space-y-6">
                @csrf
                <input type="hidden" name="id_area" value="{{ $area->id }}">

                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Tanggal Awal (1-31)</label>
                    <input type="number" min="1" max="31" name="tanggal_awal" value="{{ old('tanggal_awal') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2" required>
                </div>

                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Tanggal Akhir (1-31)</label>
                    <input type="number" min="1" max="31" name="tanggal_akhir" value="{{ old('tanggal_akhir') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2" required>
                </div>

                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Jatuh Tempo (Hari)</label>
                    <input type="number" min="0" name="jatuh_tempo_hari" value="{{ old('jatuh_tempo_hari') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2" required>
                </div>

                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Bunga (%)</label>
                    <input type="number" step="0.01" min="0" name="bunga" value="{{ old('bunga') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2">
                </div>

                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Keterangan</label>
                    <textarea name="keterangan" rows="3"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2">{{ old('keterangan') }}</textarea>
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-full">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>
@endsection

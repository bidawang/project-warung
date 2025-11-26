@extends('layouts.admin')

@section('title', 'Edit Aturan Tenggat Hutang')

@section('content')
<div class="p-4 sm:p-6 lg:p-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">
        ✏️ Edit Aturan Tenggat Hutang
    </h1>

    <div class="mt-8 flow-root">
        <div class="max-w-4xl mx-auto bg-white shadow-xl rounded-xl overflow-hidden">
            <div class="px-4 py-5 sm:p-6">
                <h2 class="text-xl font-semibold text-indigo-700 mb-4 border-b pb-2">Data Aturan #{{ $rule->id ?? '...' }}</h2>

                {{-- Asumsi: $rule adalah objek Aturan Tenggat yang sedang diedit --}}
                <form action="{{ route('admin.aturan-tenggat-hutang.update', $rule->id) }}" method="POST">
                    @csrf
                    @method('PUT') {{-- Wajib menggunakan PUT/PATCH untuk operasi update --}}

                    <div class="mb-6">
                        <label for="id_warung" class="block text-sm font-medium text-gray-700">Warung</label>
                        <select id="id_warung" name="id_warung" required
                            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="" disabled>Pilih Warung</option>
                            {{-- Mengisi nilai dari database atau old input --}}
                            <option value="1" {{ old('id_warung', $rule->id_warung) == 1 ? 'selected' : '' }}>Warung A (Placeholder)</option>
                            <option value="2" {{ old('id_warung', $rule->id_warung) == 2 ? 'selected' : '' }}>Warung B (Placeholder)</option>
                        </select>
                        @error('id_warung')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-8 border-t border-gray-200 pt-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Waktu dan Nominal</h3>
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-6">

                            <div class="sm:col-span-3">
                                <label for="tanggal_awal" class="block text-sm font-medium text-gray-700">Tanggal Awal Tagihan (Hari ke-)</label>
                                <input type="number" id="tanggal_awal" name="tanggal_awal" value="{{ old('tanggal_awal', $rule->tanggal_awal) }}" min="1" max="31" placeholder="1-31" required
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                @error('tanggal_awal')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-2 text-xs text-gray-500">Hari dimulainya periode tagihan.</p>
                            </div>

                            <div class="sm:col-span-3">
                                <label for="tanggal_akhir" class="block text-sm font-medium text-gray-700">Tanggal Akhir Tagihan (Hari ke-)</label>
                                <input type="number" id="tanggal_akhir" name="tanggal_akhir" value="{{ old('tanggal_akhir', $rule->tanggal_akhir) }}" min="1" max="31" placeholder="1-31" required
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                @error('tanggal_akhir')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-2 text-xs text-gray-500">Hari berakhirnya periode tagihan.</p>
                            </div>

                            <div class="sm:col-span-3">
                                <label for="jatuh_tempo_hari" class="block text-sm font-medium text-gray-700">Jatuh Tempo Setelah Periode (Hari)</label>
                                <input type="number" id="jatuh_tempo_hari" name="jatuh_tempo_hari" value="{{ old('jatuh_tempo_hari', $rule->jatuh_tempo_hari) }}" min="1" placeholder="Misalnya 7" required
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                @error('jatuh_tempo_hari')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-2 text-xs text-gray-500">Jatuh tempo dihitung dari tanggal akhir periode.</p>
                            </div>

                            <div class="sm:col-span-3">
                                <label for="bunga" class="block text-sm font-medium text-gray-700">Bunga Keterlambatan (%)</label>
                                <div class="mt-1 flex rounded-md shadow-sm">
                                    <input type="text" id="bunga" name="bunga" value="{{ old('bunga', $rule->bunga) }}" placeholder="Contoh: 5" required
                                        class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300">
                                    <span class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                                        %
                                    </span>
                                </div>
                                @error('bunga')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-2 text-xs text-gray-500">Masukkan nilai bunga (contoh: **5** untuk 5%).</p>
                            </div>
                        </div>
                    </div>


                    <div class="mt-6 border-t border-gray-200 pt-6">
                        <label for="keterangan" class="block text-sm font-medium text-gray-700">Keterangan (Opsional)</label>
                        <div class="mt-1">
                            <textarea id="keterangan" name="keterangan" rows="3"
                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border border-gray-300 rounded-md">{{ old('keterangan', $rule->keterangan) }}</textarea>
                        </div>
                        @error('keterangan')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-xs text-gray-500">Catatan tambahan mengenai aturan ini. (Kolom **text**)</p>
                    </div>

                    <div class="mt-8 pt-5 border-t border-gray-200 flex justify-end space-x-3">
                        <a href="{{ route('admin.aturan-tenggat-hutang.index') }}"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Batal
                        </a>
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

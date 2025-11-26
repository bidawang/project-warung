<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">

@forelse ($barangs as $br)
    <label class="flex items-start space-x-3 bg-white p-3 border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow cursor-pointer">
        {{-- Checkbox --}}
        <input type="checkbox"
               name="barangs[]"
               value="{{ $br->id }}"
               class="mt-1 form-checkbox h-5 w-5 text-blue-600 rounded"
               {{ in_array($br->id, $selected) ? 'checked' : '' }}>

        {{-- Detail Barang --}}
        <span class="text-sm">
            <strong class="text-gray-900 block">{{ $br->nama_barang }}</strong>
            <small class="text-gray-500">
                Kategori: {{ $br->subKategori->kategori->kategori ?? 'N/A' }} /
                Sub: {{ $br->subKategori->sub_kategori ?? 'N/A' }}
            </small>
        </span>
    </label>
@empty
    <div class="col-span-full p-6 text-center bg-yellow-50 border border-yellow-200 rounded-lg">
        <p class="text-yellow-800">Tidak ada barang yang ditemukan berdasarkan filter yang diterapkan.</p>
    </div>
@endforelse

</div>

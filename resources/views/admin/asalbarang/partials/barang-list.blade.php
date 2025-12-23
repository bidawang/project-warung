<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
    @forelse ($barangs as $br)
        @php
            // Ambil nama area lain (selain area yang sedang diedit saat ini)
            $otherAreas = $br->areaPembelian->where('id', '!=', $currentAreaId ?? null)->pluck('area');
            $isUsedElsewhere = $otherAreas->isNotEmpty();
        @endphp

        <label class="relative flex items-start space-x-3 p-3 border rounded-lg shadow-sm transition-all cursor-pointer 
            {{ in_array($br->id, $selected) ? 'bg-blue-50 border-blue-300' : 'bg-white border-gray-200 hover:shadow-md' }}
            {{ $isUsedElsewhere ? 'border-l-4 border-l-amber-500' : '' }}">
            
            {{-- Checkbox --}}
            <input type="checkbox"
                   name="barangs[]"
                   value="{{ $br->id }}"
                   class="mt-1 form-checkbox h-5 w-5 text-blue-600 rounded"
                   {{ in_array($br->id, $selected) ? 'checked' : '' }}>

            {{-- Detail Barang --}}
            <span class="text-sm flex-1">
                <strong class="text-gray-900 block leading-tight">{{ $br->nama_barang }}</strong>
                <div class="text-[11px] text-gray-500 mt-1 uppercase tracking-wider">
                    {{ $br->subKategori->kategori->kategori ?? 'N/A' }} / {{ $br->subKategori->sub_kategori ?? 'N/A' }}
                </div>

                {{-- Info Area Lain --}}
                @if($isUsedElsewhere)
                    <div class="mt-2 p-1.5 bg-amber-50 rounded border border-amber-100">
                        <p class="text-[10px] font-bold text-amber-800 uppercase">Sudah Ada di Area:</p>
                        <div class="flex flex-wrap gap-1 mt-1">
                            @foreach($otherAreas as $areaName)
                                <span class="bg-amber-200 text-amber-900 text-[9px] px-1.5 py-0.5 rounded shadow-sm">
                                    {{ $areaName }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </span>
        </label>
    @empty
        <div class="col-span-full p-6 text-center bg-yellow-50 border border-yellow-200 rounded-lg">
            <p class="text-yellow-800 font-medium">Tidak ada barang yang ditemukan.</p>
        </div>
    @endforelse
</div>
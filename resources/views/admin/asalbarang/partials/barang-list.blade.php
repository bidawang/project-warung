<div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
    @forelse ($barangs as $br)
        @php
            $otherAreas = $br->areaPembelian->where('id', '!=', $currentAreaId ?? null)->pluck('area');
            $isUsedElsewhere = $otherAreas->isNotEmpty();
        @endphp

        <label class="group relative flex flex-col bg-white border rounded-xl shadow-sm transition-all cursor-pointer overflow-hidden
            {{ in_array($br->id, $selected) ? 'border-blue-500 ring-2 ring-blue-100 ring-inset' : 'border-gray-100 hover:border-blue-300 hover:shadow-md' }}
            {{ $isUsedElsewhere ? 'border-l-4 border-l-amber-400' : '' }}">
            
            <div class="p-3 flex-1 flex flex-col">
                {{-- Checkbox + Nama --}}
                <div class="flex items-start gap-2 mb-2">
                    <input type="checkbox"
                           name="barangs[]"
                           value="{{ $br->id }}"
                           class="mt-1 form-checkbox h-4 w-4 text-blue-600 rounded border-gray-300 transition focus:ring-blue-500"
                           {{ in_array($br->id, $selected) ? 'checked' : '' }}>
                    
                    <span class="text-xs font-bold text-gray-800 leading-tight group-hover:text-blue-700 transition line-clamp-2">
                        {{ $br->nama_barang }}
                    </span>
                </div>

                {{-- Kategori Label --}}
                <div class="mt-auto">
                    <div class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter truncate">
                        {{ $br->subKategori->kategori->kategori ?? 'N/A' }}
                    </div>
                    <div class="text-[10px] font-semibold text-blue-500 truncate">
                        {{ $br->subKategori->sub_kategori ?? 'N/A' }}
                    </div>
                </div>

                {{-- Alert Info Area Lain --}}
                @if($isUsedElsewhere)
                    <div class="mt-2 pt-2 border-t border-amber-50">
                        <div class="flex items-center gap-1 mb-1">
                            <svg class="w-3 h-3 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-[9px] font-bold text-amber-700 uppercase">Tersedia di:</span>
                        </div>
                        <div class="flex flex-wrap gap-1">
                            @foreach($otherAreas as $areaName)
                                <span class="bg-amber-100 text-amber-800 text-[8px] px-1.5 py-0.5 rounded-md font-medium">
                                    {{ $areaName }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Background Indicator untuk yang terpilih --}}
            @if(in_array($br->id, $selected))
                <div class="absolute top-0 right-0 p-1">
                    <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
            @endif
        </label>
    @empty
        <div class="col-span-full py-12 flex flex-col items-center justify-center bg-white rounded-xl border border-gray-100 shadow-sm">
            <svg class="w-12 h-12 text-gray-200 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <p class="text-gray-500 font-medium">Barang tidak ditemukan.</p>
            <p class="text-gray-400 text-xs">Coba ubah kata kunci atau kategori filter.</p>
        </div>
    @endforelse
</div>
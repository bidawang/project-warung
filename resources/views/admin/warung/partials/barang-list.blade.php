<div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">

    @forelse ($barangs as $br)
        <div
            class="group flex flex-col bg-white border border-gray-100 rounded-xl shadow-sm hover:border-blue-300 hover:shadow-md transition overflow-hidden">

            <div class="p-3 flex flex-col flex-1">

                {{-- Nama Barang --}}
                <div class="mb-2">

                    <span
                        class="text-xs font-bold text-gray-800 leading-tight group-hover:text-blue-700 transition line-clamp-2">

                        {{ $br->nama_barang }}

                    </span>

                </div>


                {{-- Kategori --}}
                <div class="mt-auto">

                    <div class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter truncate">

                        {{ $br->subKategori->kategori->kategori ?? 'N/A' }}

                    </div>

                    <div class="text-[10px] font-semibold text-blue-500 truncate">

                        {{ $br->subKategori->sub_kategori ?? 'N/A' }}

                    </div>

                </div>


                {{-- Input Jumlah --}}
                <div class="mt-3 pt-3 border-t border-gray-100">

                    <label class="text-[10px] font-bold text-gray-500 block mb-1">

                        Jumlah

                    </label>

                    <input type="number" name="barang[{{ $br->id }}]" value="0" min="0"
                        class="w-full border-gray-300 rounded-lg p-1.5 text-xs focus:ring-blue-500 focus:border-blue-500">

                </div>

            </div>

        </div>


    @empty

        <div
            class="col-span-full py-12 flex flex-col items-center justify-center bg-white rounded-xl border border-gray-100 shadow-sm">

            <svg class="w-12 h-12 text-gray-200 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                <path d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7" stroke-width="2" />

            </svg>

            <p class="text-gray-500 font-medium">

                Barang tidak ditemukan.

            </p>

        </div>
    @endforelse

</div>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-4 mt-4">

    {{-- ===================================================== --}}
    {{-- HUTANG PELANGGAN (COMPACT) --}}
    {{-- ===================================================== --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col">

        {{-- HEADER --}}
        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-black text-gray-800">Hutang Pelanggan</h3>
                <p class="text-[10px] text-gray-400 mt-0.5">Monitoring pembayaran pelanggan</p>
            </div>
            <a href="{{ route('admin.hutang.index') }}"
                class="text-[10px] bg-indigo-600 hover:bg-indigo-700 text-white px-2.5 py-1.5 rounded-lg font-bold transition">
                Lihat Semua
            </a>
        </div>

        {{-- SUMMARY --}}
        <div class="grid grid-cols-3 gap-2 p-3 border-b border-gray-100 bg-gray-50/50">
            <div class="bg-red-50/50 rounded-xl p-2 border border-red-100">
                <p class="text-[9px] uppercase font-bold text-red-400">Total</p>
                <h4 class="text-xs font-black text-red-600">Rp {{ number_format($totalHutang ?? 0, 0, ',', '.') }}</h4>
            </div>
            <div class="bg-yellow-50/50 rounded-xl p-2 border border-yellow-100">
                <p class="text-[9px] uppercase font-bold text-yellow-500">Sisa</p>
                <h4 class="text-xs font-black text-yellow-600">Rp {{ number_format($totalSisa ?? 0, 0, ',', '.') }}</h4>
            </div>
            <div class="bg-green-50/50 rounded-xl p-2 border border-green-100">
                <p class="text-[9px] uppercase font-bold text-green-500">Lunas</p>
                <h4 class="text-xs font-black text-green-600">Rp {{ number_format($totalLunas ?? 0, 0, ',', '.') }}</h4>
            </div>
        </div>

        {{-- LIST --}}
        <div class="max-h-[400px] overflow-y-auto divide-y divide-gray-50">
            @forelse ($hutangList as $item)
                @php $isLunas = $item->total_sisa <= 0; @endphp
                <div class="px-4 py-2.5 hover:bg-gray-50 transition flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="h-8 w-8 rounded-lg flex items-center justify-center font-black text-[11px] {{ $isLunas ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                            {{ strtoupper(substr($item->user->name ?? 'U', 0, 1)) }}
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800 text-xs truncate max-w-[120px]">{{ $item->user->name ?? '-' }}</h4>
                            <p class="text-[10px] text-gray-400">{{ $item->total_nota }} nota</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-xs font-black {{ $isLunas ? 'text-green-600' : 'text-red-600' }}">
                            Rp{{ number_format($item->total_sisa, 0, ',', '.') }}
                        </div>
                        <div class="text-[9px] text-gray-400">Awal: {{ number_format($item->total_awal, 0, ',', '.') }}</div>
                    </div>
                </div>
            @empty
                <div class="py-10 text-center text-gray-400 text-xs italic">Tidak ada data</div>
            @endforelse
        </div>
    </div>

    {{-- ===================================================== --}}
    {{-- HUTANG BARANG MASUK (COMPACT) --}}
    {{-- ===================================================== --}}
    <div x-data="{ open: null }" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col">

        {{-- HEADER --}}
        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-black text-gray-800">Hutang Barang</h3>
                <p class="text-[10px] text-gray-400 mt-0.5">Supplier & Barang Masuk</p>
            </div>
            <div class="text-right">
                <p class="text-[8px] uppercase tracking-wider text-gray-400">Total Hutang</p>
                <p class="text-sm font-black text-yellow-600">Rp{{ number_format($totalHutangBarangMasuk ?? 0, 0, ',', '.') }}</p>
            </div>
        </div>

        {{-- LIST --}}
        <div class="divide-y divide-gray-50 max-h-[475px] overflow-y-auto font-sans">
            @forelse($hutangBarangMasuk as $i => $hutang)
                <div class="transition">
                    <div class="px-4 py-2.5 hover:bg-gray-50 cursor-pointer flex items-center justify-between"
                        @click="open === {{ $i }} ? open = null : open = {{ $i }}">
                        
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded-lg bg-yellow-100 text-yellow-700 flex items-center justify-center text-[10px] font-black">
                                ID
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800 text-xs">HTG-{{ str_pad($hutang->id, 4, '0', STR_PAD_LEFT) }}</h4>
                                <p class="text-[10px] text-gray-400">{{ $hutang->created_at->translatedFormat('d/m/y') }}</p>
                            </div>
                        </div>

                        <div class="text-right">
                            <div class="text-xs font-black text-yellow-600">Rp{{ number_format($hutang->total, 0, ',', '.') }}</div>
                            <span class="inline-block px-1.5 py-0.5 rounded text-[8px] font-black uppercase {{ $hutang->status == 'lunas' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                {{ $hutang->status }}
                            </span>
                        </div>
                    </div>

                    {{-- DETAIL (COLLAPSIBLE) --}}
                    <div x-show="open === {{ $i }}" x-transition x-cloak class="bg-gray-50/80 px-4 py-3 border-y border-gray-100">
                        <div class="space-y-1.5">
                            @foreach ($hutang->hutangBarangMasuk as $detail)
                                <div class="flex items-center justify-between text-[11px] bg-white p-2 rounded-lg border border-gray-100 shadow-sm">
                                    <div class="max-w-[150px]">
                                        <div class="font-bold text-gray-700 truncate">{{ $detail->barangMasuk->transaksiBarang->barang->nama_barang ?? '-' }}</div>
                                        <div class="text-[9px] text-gray-400">Exp: {{ $detail->barangMasuk->tanggal_kadaluarsa ?? '-' }}</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-bold text-gray-700">x{{ $detail->barangMasuk->jumlah }}</div>
                                        <div class="text-gray-500 font-medium">Rp{{ number_format($detail->total, 0, ',', '.') }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @empty
                <div class="py-10 text-center text-gray-400 text-xs italic">Data kosong</div>
            @endforelse
        </div>
    </div>
</div>
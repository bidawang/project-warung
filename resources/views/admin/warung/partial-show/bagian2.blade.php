{{-- ===================================================== --}}
{{-- ROW: KAS & FINANCIAL MONITORING (REMASTERED V2) --}}
{{-- ===================================================== --}}
<div class="grid grid-cols-1 xl:grid-cols-12 gap-5 mb-6">
    
    {{-- COLUMN 1: REAL-TIME ASSETS (STATIS - TIDAK TERPENGARUH FILTER TANGGAL) --}}
    <div class="xl:col-span-8 grid grid-cols-1 md:grid-cols-2 gap-5">
        
        {{-- CARD: KAS TUNAI (CASH) --}}
        <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden flex flex-col transition-all hover:shadow-md">
            <div class="p-6 flex-1">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shadow-sm">
                            <i class="fas fa-wallet text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-gray-800 uppercase tracking-tight">Kas Tunai</h3>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Saldo Saat Ini</p>
                        </div>
                    </div>
                    <span class="bg-green-100 text-green-700 text-[9px] font-black px-2.5 py-1 rounded-lg uppercase tracking-widest">Live</span>
                </div>
                
                <div class="mb-6">
                    <h2 class="text-4xl font-black text-gray-900 tracking-tighter">
                        Rp {{ number_format($kasCash->saldo ?? 0, 0, ',', '.') }}
                    </h2>
                </div>

                {{-- Komparasi Fisik vs Sistem --}}
                <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100 space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-[10px] font-bold text-gray-500 uppercase">Total Uang Fisik</span>
                        <span class="text-sm font-black text-amber-600">Rp {{ number_format($totalUangFisik, 0, ',', '.') }}</span>
                    </div>
                    @php 
                        $selisih = $totalUangFisik - ($kasCash->saldo ?? 0);
                    @endphp
                    <div class="flex justify-between items-center pt-2 border-t border-gray-200/50">
                        <span class="text-[10px] font-bold text-gray-500 uppercase">Selisih Fisik</span>
                        <span class="text-xs font-black {{ $selisih < 0 ? 'text-red-600' : 'text-blue-600' }}">
                            {{ $selisih >= 0 ? '+' : '' }} Rp {{ number_format($selisih, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- MINI GRID: PECAHAN (SCROLLABLE AREA) --}}
            <div class="bg-gray-900 p-5">
                <p class="text-[9px] font-black text-gray-500 uppercase mb-3 tracking-[0.2em]">Pecahan Fisik Terdata</p>
                <div class="grid grid-cols-4 gap-3">
                    @foreach ($pecahanKas->take(4) as $item)
                    <div class="bg-white/5 p-2 rounded-xl border border-white/10 text-center">
                        <p class="text-[8px] text-gray-400 font-bold mb-1">{{ number_format($item->pecahan, 0, ',', '.') }}</p>
                        <p class="text-xs font-black text-white">x{{ $item->jumlah }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- CARD: KAS BANK (BANK) --}}
        <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-6 flex flex-col transition-all hover:shadow-md relative overflow-hidden group">
            <div class="absolute -right-10 -bottom-10 h-40 w-40 bg-blue-50/50 rounded-full group-hover:scale-110 transition-transform duration-700"></div>
            
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center shadow-sm">
                            <i class="fas fa-university text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-gray-800 uppercase tracking-tight">Kas Bank</h3>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Saldo Sistem</p>
                        </div>
                    </div>
                </div>

                <div class="mb-10">
                    <h2 class="text-4xl font-black text-gray-900 tracking-tighter">
                        Rp {{ number_format($kasBank->saldo ?? 0, 0, ',', '.') }}
                    </h2>
                </div>

                <div class="space-y-4">
                    <div class="flex items-center justify-between py-2 border-b border-gray-50">
                        <div class="flex items-center gap-2">
                            <div class="h-2 w-2 rounded-full bg-green-500"></div>
                            <span class="text-[10px] font-bold text-gray-400 uppercase">Total Pendapatan</span>
                        </div>
                        <span class="text-xs font-black text-gray-700">Rp {{ number_format($pendapatanBankPeriode, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="h-2 w-2 rounded-full bg-blue-500"></div>
                            <span class="text-[10px] font-bold text-gray-400 uppercase">Status Kas</span>
                        </div>
                        <span class="text-[10px] font-black text-blue-600 px-2 py-1 bg-blue-50 rounded-md">AKTIF</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- COLUMN 2: PERIODIC PERFORMANCE (DINAMIS - TERPENGARUH PERIODE) --}}
    <div class="xl:col-span-4">
        <div class="bg-[#111827] rounded-[2rem] shadow-xl p-7 h-full text-white relative overflow-hidden border border-gray-800">
            <div class="relative z-10 flex flex-col h-full">
                <div class="flex justify-between items-start mb-8">
                    <div>
                        <p class="text-[10px] font-black text-red-500 uppercase tracking-[0.2em] mb-1">Monitoring Arus</p>
                        <h3 class="text-xl font-bold text-white">{{ $periode->translatedFormat('F Y') }}</h3>
                    </div>
                    <div class="h-10 w-10 rounded-xl bg-white/5 flex items-center justify-center text-gray-400 border border-white/10">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>

                <div class="space-y-6 mb-auto">
                    {{-- Cash Flow --}}
                    <div class="group">
                        <div class="flex justify-between mb-2">
                            <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider group-hover:text-amber-400 transition-colors">Pengeluaran Tunai</span>
                            <span class="text-xs font-black text-amber-500">Rp {{ number_format($pengeluaranCashPeriode, 0, ',', '.') }}</span>
                        </div>
                        <div class="h-1.5 w-full bg-white/5 rounded-full overflow-hidden">
                            <div class="h-full bg-amber-500 rounded-full" style="width: 45%"></div>
                        </div>
                    </div>

                    {{-- Bank Flow --}}
                    <div class="group">
                        <div class="flex justify-between mb-2">
                            <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider group-hover:text-blue-400 transition-colors">Pengeluaran Bank</span>
                            <span class="text-xs font-black text-blue-500">Rp {{ number_format($pengeluaranBankPeriode, 0, ',', '.') }}</span>
                        </div>
                        <div class="h-1.5 w-full bg-white/5 rounded-full overflow-hidden">
                            <div class="h-full bg-blue-500 rounded-full" style="width: 30%"></div>
                        </div>
                    </div>
                </div>

                <div class="mt-10 pt-6 border-t border-white/5">
                    <p class="text-[9px] text-gray-500 font-bold uppercase tracking-[0.2em] mb-2">Total Beban Keluar</p>
                    <div class="flex items-center justify-between">
                        <h4 class="text-3xl font-black text-red-500 tracking-tighter">
                            Rp {{ number_format($pengeluaranCashPeriode + $pengeluaranBankPeriode, 0, ',', '.') }}
                        </h4>
                        <div class="h-12 w-12 rounded-2xl bg-red-500/10 flex items-center justify-center text-red-500">
                            <i class="fas fa-chart-line fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
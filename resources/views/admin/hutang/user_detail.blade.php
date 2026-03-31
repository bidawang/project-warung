@extends('layouts.admin')

@section('title', 'Detail Hutang Pelanggan')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<div class="flex-1 flex flex-col bg-slate-50 overflow-y-auto font-sans">
    {{-- Header Section --}}
    <header class="p-6 bg-white/80 backdrop-blur-md border-b border-slate-200 flex flex-col md:flex-row justify-between items-start md:items-center sticky top-0 z-20 shadow-sm">
        <div>
            <a href="{{ route('admin.hutang.index') }}" class="group text-indigo-600 hover:text-indigo-800 text-sm font-bold transition-all flex items-center gap-2">
                <i class="fas fa-chevron-left group-hover:-translate-x-1 transition-transform"></i> Kembali ke Daftar
            </a>
            <h1 class="text-3xl font-extrabold text-slate-800 mt-2 tracking-tight italic uppercase">Rincian Hutang</h1>
            <div class="flex items-center gap-2 mt-1">
                <span class="px-2 py-0.5 bg-slate-100 text-slate-600 rounded text-[10px] font-bold">CUSTOMER</span>
                <p class="text-sm font-semibold text-slate-500">{{ $user->name }} (ID: #{{ $user->id }})</p>
            </div>
        </div>
        <div class="mt-4 md:mt-0 text-right bg-white p-4 rounded-2xl border-2 border-red-100 shadow-sm">
            <p class="text-[10px] text-red-400 uppercase font-black tracking-widest leading-none mb-1">Total Sisa Piutang</p>
            <p class="text-3xl font-black text-red-600 leading-none">Rp {{ number_format($totalSisa, 0, ',', '.') }}</p>
        </div>
    </header>

    <main class="p-6 space-y-8">
        {{-- Statistik Ringkas --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-5">
                    <div class="w-12 h-12 flex items-center justify-center bg-indigo-50 text-indigo-600 rounded-2xl"><i class="fas fa-file-invoice-dollar fa-xl"></i></div>
                    <div>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Hutang Awal</p>
                        <p class="text-xl font-black text-slate-800 font-mono italic">Rp {{ number_format($totalHutang, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-5">
                    <div class="w-12 h-12 flex items-center justify-center bg-blue-50 text-blue-600 rounded-2xl"><i class="fas fa-layer-group fa-xl"></i></div>
                    <div>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Jumlah Nota</p>
                        <p class="text-xl font-black text-slate-800">{{ $hutangList->count() }} <span class="text-sm font-normal text-slate-400 uppercase">Transaksi</span></p>
                    </div>
                </div>
            </div>
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-5">
                    <div class="w-12 h-12 flex items-center justify-center {{ $totalSisa <= 0 ? 'bg-emerald-50 text-emerald-600' : 'bg-orange-50 text-orange-600' }} rounded-2xl">
                        <i class="fas {{ $totalSisa <= 0 ? 'fa-shield-check' : 'fa-hourglass-half' }} fa-xl"></i>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Status Akun</p>
                        <p class="text-xl font-black {{ $totalSisa <= 0 ? 'text-emerald-600' : 'text-orange-600' }} italic">
                            {{ $totalSisa <= 0 ? 'LUNAS' : 'ADA TUNGGAKAN' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabel Utama --}}
        <div class="bg-white shadow-xl shadow-slate-200/50 rounded-[2rem] overflow-hidden border border-slate-200">
            <div class="p-6 bg-white border-b border-slate-100 flex justify-between items-center">
                <h2 class="font-black text-slate-800 tracking-tight flex items-center gap-3 italic">
                    <span class="w-2 h-6 bg-indigo-600 rounded-full"></span> 
                    RIWAYAT TRANSAKSI HUTANG
                </h2>
                <span class="text-[10px] font-bold text-slate-400 uppercase bg-slate-100 px-3 py-1 rounded-full">Update Terakhir: {{ now()->format('d/m/H:i') }}</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full border-separate border-spacing-0">
                    <thead>
                        <tr class="bg-slate-50/50 text-slate-500 text-[10px] uppercase tracking-[0.2em] font-black">
                            <th class="py-5 px-8 text-left border-b border-slate-100">Jatuh Tempo</th>
                            <th class="py-5 px-8 text-left border-b border-slate-100">Warung & Deskripsi</th>
                            <th class="py-5 px-8 text-right border-b border-slate-100">Sudah Bayar</th>
                            <th class="py-5 px-8 text-right border-b border-slate-100">Sisa Tagihan</th>
                            <th class="py-5 px-8 text-center border-b border-slate-100">Status</th>
                            <th class="py-5 px-8 text-center border-b border-slate-100">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach ($hutangList as $h)
                            <tr class="group transition-all hover:bg-slate-50 {{ $h->is_overdue ? 'bg-red-50/30' : '' }}">
                                <td class="py-6 px-8">
                                    <div class="font-black text-slate-700 font-mono">{{ \Carbon\Carbon::parse($h->tenggat)->format('d M Y') }}</div>
                                    @if($h->is_overdue)
                                        <div class="flex items-center gap-1.5 mt-1">
                                            <div class="w-1.5 h-1.5 bg-red-600 rounded-full animate-ping"></div>
                                            <span class="text-[9px] text-red-600 font-black uppercase italic tracking-tighter">Terlambat</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="py-6 px-8">
                                    <div class="font-black text-indigo-900 group-hover:text-indigo-600 transition-colors">{{ $h->warung->nama_warung }}</div>
                                    <div class="text-[10px] text-slate-400 font-medium">Nota #{{ $h->id }} • {{ $h->keterangan ?? 'Tanpa Keterangan' }}</div>
                                </td>
                                <td class="py-6 px-8 text-right">
                                    <div class="font-bold text-emerald-600">Rp {{ number_format($h->pembayarans->sum('jumlah_pembayaran'), 0, ',', '.') }}</div>
                                </td>
                                <td class="py-6 px-8 text-right">
                                    <div class="font-black text-slate-800 italic">Rp {{ number_format($h->jumlah_sisa_hutang, 0, ',', '.') }}</div>
                                    @if($h->total_bunga > 0)
                                        <div class="mt-1">
                                            <span class="text-[10px] text-orange-600 font-black bg-orange-100 px-2 py-0.5 rounded-md border border-orange-200">
                                                <i class="fas fa-plus-circle mr-1"></i>BUNGA: Rp {{ number_format($h->total_bunga, 0, ',', '.') }}
                                            </span>
                                        </div>
                                    @endif
                                </td>
                                <td class="py-6 px-8 text-center">
                                    <span class="px-4 py-1.5 rounded-xl text-[7px] font-black tracking-widest italic {{ $h->status === 'lunas' ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-red-100 text-red-700 border border-red-200' }}">
                                        {{ strtoupper($h->status) }}
                                    </span>
                                </td>
                                <td class="py-6 px-8 text-center">
                                    <div class="flex justify-center items-center gap-3">
                                        <a href="{{ route('admin.hutang.detail', $h->id) }}" class="w-10 h-10 flex items-center justify-center bg-white border border-slate-200 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white hover:border-slate-800 transition-all shadow-sm" title="Lihat Log">
                                            <i class="fas fa-eye text-sm"></i>
                                        </a>

                                        @if($h->is_overdue)
                                            <button onclick="openModalBunga({{ $h->id }}, '{{ $h->warung->nama_warung }}', {{ $h->rekomendasi_bunga }})"
                                                class="h-10 px-4 bg-orange-500 hover:bg-orange-600 text-white rounded-xl text-xs font-black transition-all shadow-lg shadow-orange-200 flex items-center gap-2 hover:-translate-y-0.5 active:translate-y-0 uppercase italic tracking-tighter">
                                                <i class="fas fa-gavel"></i> Atur Bunga
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

{{-- MODAL KEPUTUSAN BUNGA --}}
<div id="modalBunga" class="hidden fixed inset-0 bg-slate-900/90 z-[100] flex items-center justify-center backdrop-blur-xl p-4 transition-all">
    <div class="bg-white rounded-[2.5rem] w-full max-w-md shadow-2xl overflow-hidden border border-slate-100 transform transition-all animate-in fade-in zoom-in duration-300">
        <div class="p-10">
            <div class="flex flex-col items-center text-center mb-8">
                <div class="w-20 h-20 bg-orange-50 text-orange-500 rounded-[2rem] flex items-center justify-center mb-5 ring-8 ring-orange-50/50 rotate-12">
                    <i class="fas fa-scale-balanced text-3xl"></i>
                </div>
                <h3 class="text-3xl font-black text-slate-800 tracking-tight italic uppercase">Penetapan Denda</h3>
                <p class="text-sm text-slate-400 mt-2 font-medium">Hutang pada <span id="txtNamaWarung" class="font-bold text-indigo-600"></span> telah melewati batas waktu.</p>
            </div>

            <form action="{{ route('admin.hutang.update-bunga') }}" method="POST" class="space-y-6">
                @csrf
                <input type="hidden" name="hutang_id" id="input_hutang_id">
                
                <div class="bg-slate-900 p-6 rounded-[2rem] text-white relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-4 opacity-10">
                        <i class="fas fa-coins text-6xl"></i>
                    </div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Rekomendasi Bunga</p>
                    <div class="flex items-baseline gap-1">
                        <span class="text-lg font-bold text-orange-400 italic">Rp</span>
                        <span id="txtBungaAturan" class="text-4xl font-black font-mono tracking-tighter">0</span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <button type="button" onclick="setNominal(0)" class="py-4 text-[10px] font-black uppercase tracking-widest border-2 border-slate-100 rounded-2xl hover:bg-slate-50 hover:border-slate-200 transition-all text-slate-400">Tanpa Bunga</button>
                    <button type="button" id="btnSesuaiAturan" class="py-4 text-[10px] font-black uppercase tracking-widest bg-orange-100 text-orange-700 rounded-2xl border-2 border-orange-200 hover:bg-orange-200 transition-all italic">Terapkan Rekomendasi</button>
                </div>

                <div class="relative">
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-3 ml-2 tracking-widest">Nominal Kustom (IDR)</label>
                    <div class="relative">
                        <span class="absolute left-5 top-1/2 -translate-y-1/2 font-black text-slate-300">Rp</span>
                        <input type="number" name="nominal_bunga" id="input_nominal" 
                            class="w-full pl-14 pr-6 py-5 bg-slate-50 border-2 border-slate-100 rounded-3xl focus:ring-8 focus:ring-indigo-500/5 focus:border-indigo-500 text-2xl font-black font-mono transition-all outline-none" 
                            placeholder="0" required>
                    </div>
                </div>

                <div class="flex flex-col gap-4 pt-6 border-t border-slate-100">
                    <button type="submit" class="w-full py-5 bg-indigo-600 text-white rounded-3xl font-black shadow-xl shadow-indigo-200 hover:bg-indigo-700 transform active:scale-95 transition-all uppercase tracking-widest italic">
                        Konfirmasi & Simpan
                    </button>
                    <button type="button" onclick="closeModalBunga()" class="w-full py-2 text-slate-400 font-bold hover:text-slate-600 transition-all uppercase text-[10px] tracking-widest">
                        Tutup Panel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openModalBunga(id, warung, bunga) {
        document.getElementById('input_hutang_id').value = id;
        document.getElementById('txtNamaWarung').innerText = warung;
        
        const formattedBunga = new Intl.NumberFormat('id-ID').format(bunga);
        document.getElementById('txtBungaAturan').innerText = formattedBunga;
        document.getElementById('input_nominal').value = Math.round(bunga);
        
        document.getElementById('btnSesuaiAturan').onclick = function() { setNominal(bunga); };
        
        const modal = document.getElementById('modalBunga');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeModalBunga() {
        const modal = document.getElementById('modalBunga');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function setNominal(val) {
        document.getElementById('input_nominal').value = Math.round(val);
    }

    window.onclick = function(event) {
        const modal = document.getElementById('modalBunga');
        if (event.target == modal) closeModalBunga();
    }
</script>
@endsection
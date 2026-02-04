@extends('layouts.admin')

@section('content')
<div class="p-6 bg-slate-50 min-h-screen">
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-800 uppercase tracking-tight">
                {{ $warung->nama_warung }}
            </h1>
            <p class="text-slate-500 text-sm">Laporan performa penjualan dan profitabilitas.</p>
        </div>
        <a href="{{ route('admin.laporan-laba.index') }}" class="px-4 py-2 bg-white border border-slate-200 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-50 transition shadow-sm">
            <i class="fas fa-chevron-left mr-2"></i> Pilih Warung Lain
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-3xl p-8 border border-slate-100 shadow-sm relative overflow-hidden group">
            <div class="relative z-10">
                <span class="text-xs font-bold text-blue-600 bg-blue-50 px-3 py-1 rounded-full uppercase tracking-wider">Total Penjualan</span>
                <h3 class="text-gray-400 font-medium mt-4">Laba Kotor</h3>
                <div class="text-3xl font-black text-slate-900 mt-1">
                    Rp {{ number_format($laba_kotor, 0, ',', '.') }}
                </div>
            </div>
            <div class="absolute -right-6 -bottom-6 text-slate-50 group-hover:text-blue-50 transition-colors duration-500">
                <i class="fas fa-chart-bar text-9xl"></i>
            </div>
        </div>

        <div class="bg-indigo-600 rounded-3xl p-8 shadow-xl shadow-indigo-100 relative overflow-hidden group">
            <div class="relative z-10">
                <span class="text-xs font-bold text-indigo-200 bg-indigo-500/30 px-3 py-1 rounded-full uppercase tracking-wider">Keuntungan Bersih</span>
                <h3 class="text-indigo-100 font-medium mt-4 text-wrap">Laba Bersih (Margin)</h3>
                <div class="text-3xl font-black text-white mt-1">
                    Rp {{ number_format($laba_bersih, 0, ',', '.') }}
                </div>
            </div>
            <div class="absolute -right-6 -bottom-6 text-indigo-500/20">
                <i class="fas fa-wallet text-9xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-8 py-6 border-b border-slate-50 flex justify-between items-center">
            <h4 class="font-bold text-slate-800 text-lg">Rincian Keuangan</h4>
            <span class="text-xs font-semibold text-slate-400 italic">Data diambil secara real-time</span>
        </div>
        <div class="p-8">
            <div class="flex flex-col space-y-5">
                <div class="flex justify-between items-center">
                    <span class="text-slate-500 font-medium">Laba Kotor (Total Penjualan)</span>
                    <span class="text-slate-900 font-bold">Rp {{ number_format($laba_kotor, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center pb-5 border-b border-slate-50">
                    <span class="text-slate-500 font-medium">Total Harga Modal</span>
                    <span class="text-red-500 font-bold">- Rp {{ number_format($total_modal, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center pt-2">
                    <div class="text-wrap">
                        <span class="text-xl font-black text-slate-900">LABA BERSIH</span>
                        <p class="text-xs text-slate-400 mt-1">Hasil pengurangan Penjualan dengan Modal tiap barang.</p>
                    </div>
                    <div class="text-right">
                        <span class="text-3xl font-black text-indigo-600">
                            Rp {{ number_format($laba_bersih, 0, ',', '.') }}
                        </span>
                        <div class="mt-1 text-xs font-bold text-green-500 bg-green-50 px-2 py-1 rounded-lg inline-block">
                            Margin: @if($laba_kotor > 0) {{ number_format(($laba_bersih / $laba_kotor) * 100, 1) }}% @else 0% @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

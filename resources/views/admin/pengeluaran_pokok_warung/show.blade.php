@extends('layouts.admin')

@section('title', 'Detail Pengeluaran')

@section('content')

<div class="max-w-2xl mx-auto">

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-6">

        <h2 class="text-xl font-bold text-gray-800">Detail Pengeluaran</h2>

        <div class="space-y-4 text-sm">

            <div class="flex justify-between">
                <span class="text-gray-500">Warung</span>
                <span class="font-semibold text-gray-800">
                    {{ $pengeluaran_pokok_warung->warung->nama_warung ?? '-' }}
                </span>
            </div>

            <div class="flex justify-between">
                <span class="text-gray-500">Redaksi</span>
                <span class="font-semibold text-gray-800">
                    {{ $pengeluaran_pokok_warung->redaksi }}
                </span>
            </div>

            <div class="flex justify-between">
                <span class="text-gray-500">Jumlah</span>
                <span class="font-semibold text-gray-800">
                    Rp {{ number_format($pengeluaran_pokok_warung->jumlah,0,',','.') }}
                </span>
            </div>

            <div class="flex justify-between">
                <span class="text-gray-500">Tanggal</span>
                <span class="font-semibold text-gray-800">
                    {{ \Carbon\Carbon::parse($pengeluaran_pokok_warung->date)->format('d M Y') }}
                </span>
            </div>

            <div class="flex justify-between">
                <span class="text-gray-500">Status</span>
                <span class="px-3 py-1 text-xs rounded-full font-semibold
                    {{ $pengeluaran_pokok_warung->status == 'belum terpenuhi'
                        ? 'bg-yellow-100 text-yellow-700'
                        : 'bg-green-100 text-green-700' }}">
                    {{ ucfirst($pengeluaran_pokok_warung->status) }}
                </span>
            </div>

        </div>

        <div class="pt-6">
            <a href="{{ route('admin.pengeluaran-pokok-warung.index') }}"
               class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition">
                Kembali
            </a>
        </div>

    </div>

</div>

@endsection
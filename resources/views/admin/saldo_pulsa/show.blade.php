@extends('layouts.admin')

@section('title', 'Riwayat Top-Up Saldo')

@section('content')
<div class="flex-1 flex flex-col overflow-hidden">
    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6 md:p-10">
        <div class="container mx-auto">
            
            {{-- Header & Info Warung --}}
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Riwayat Top-Up</h1>
                    <p class="text-gray-600">{{ $pulsa->warung->nama_warung }} | Jenis: {{ $pulsa->jenis == 'hp' ? 'Handphone' : 'Listrik' }}</p>
                </div>
                <a href="{{ route('admin.saldo-pulsa.index') }}" class="mt-4 md:mt-0 text-blue-600 hover:underline flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Kembali
                </a>
            </div>

            {{-- Summary Card --}}
            <div class="bg-white rounded-lg shadow-sm p-6 mb-8 border-l-4 border-blue-500">
                <div class="text-sm text-gray-500 uppercase font-bold tracking-wider">Total Saldo Saat Ini</div>
                <div class="text-3xl font-bold text-gray-800">Rp{{ number_format($pulsa->saldo, 0, ',', '.') }}</div>
            </div>

            {{-- Tabel Riwayat --}}
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nominal Saldo</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Harga Beli (Modal)</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($riwayatTopUp as $log)
                            <tr>
                                <td class="px-5 py-5 border-b border-gray-200 text-sm">
                                    {{ $log->created_at->format('d M Y H:i') }}
                                    <p class="text-xs text-gray-400">{{ $log->created_at->diffForHumans() }}</p>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 text-sm font-bold text-green-600">
                                    + Rp{{ number_format($log->jumlah, 0, ',', '.') }}
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 text-sm">
                                    Rp{{ number_format($log->total, 0, ',', '.') }}
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 text-sm">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ $log->tipe }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-10 border-b border-gray-200 text-center text-gray-500">
                                    Belum ada riwayat top-up untuk kategori ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="px-5 py-4">
                    {{ $riwayatTopUp->links() }}
                </div>
            </div>
        </div>
    </main>
</div>
@endsection
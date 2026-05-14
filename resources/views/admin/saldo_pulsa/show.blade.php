@extends('layouts.admin')

@section('title', 'Riwayat Saldo Pulsa')

@section('content')

<div class="flex-1 flex flex-col overflow-hidden">

    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6 md:p-10">

        <div class="container mx-auto">

            {{-- HEADER --}}
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-8">

                <div>

                    <h1 class="text-3xl font-bold text-gray-800">
                        Riwayat Saldo Pulsa
                    </h1>

                    <p class="text-gray-500 mt-2">

                        {{ $pulsa->warung->nama_warung ?? '-' }}

                        •

                        {{ $pulsa->jenisPulsa->nama_jenis ?? '-' }}

                    </p>

                </div>

                <a href="{{ route('admin.saldo-pulsa.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-xl shadow-sm hover:bg-gray-50 transition">

                    <svg class="w-4 h-4 mr-2"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18">
                        </path>

                    </svg>

                    Kembali

                </a>

            </div>

            {{-- CARD INFO --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

                {{-- TOTAL SALDO --}}
                <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-blue-500">

                    <div class="text-sm text-gray-500 uppercase font-semibold">
                        Saldo Saat Ini
                    </div>

                    <div class="mt-2 text-3xl font-bold text-gray-800">
                        Rp {{ number_format($pulsa->jumlah ?? 0, 0, ',', '.') }}
                    </div>

                </div>

                {{-- WARUNG --}}
                <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-green-500">

                    <div class="text-sm text-gray-500 uppercase font-semibold">
                        Warung
                    </div>

                    <div class="mt-2 text-xl font-bold text-gray-800">
                        {{ $pulsa->warung->nama_warung ?? '-' }}
                    </div>

                </div>

                {{-- JENIS --}}
                <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-purple-500">

                    <div class="text-sm text-gray-500 uppercase font-semibold">
                        Jenis Pulsa
                    </div>

                    <div class="mt-2 text-xl font-bold text-gray-800 uppercase">
                        {{ $pulsa->jenisPulsa->nama_jenis ?? '-' }}
                    </div>

                </div>

            </div>

            {{-- TABLE --}}
            <div class="bg-white shadow-md rounded-2xl overflow-hidden overflow-x-auto">

                <table class="min-w-full">

                    <thead class="bg-gray-100 border-b border-gray-200">

                        <tr>

                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-gray-600">
                                Tanggal
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-gray-600">
                                Nominal Saldo
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-gray-600">
                                Harga Modal
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-gray-600">
                                Total
                            </th>

                        </tr>

                    </thead>

                    <tbody class="divide-y divide-gray-100">

                        @forelse ($riwayatTransaksi as $trx)

                            <tr class="hover:bg-gray-50 transition">

                                {{-- TANGGAL --}}
                                <td class="px-6 py-5">

                                    <div class="font-medium text-gray-800">
                                        {{ $trx->created_at->format('d M Y H:i') }}
                                    </div>

                                    <div class="text-xs text-gray-400 mt-1">
                                        {{ $trx->created_at->diffForHumans() }}
                                    </div>

                                </td>

                                {{-- JUMLAH --}}
                                <td class="px-6 py-5">

                                    <div class="font-bold text-green-600">
                                        + Rp {{ number_format($trx->jumlah ?? 0, 0, ',', '.') }}
                                    </div>

                                </td>

                                {{-- HARGA MODAL --}}
                                <td class="px-6 py-5">

                                    <div class="text-gray-700 font-medium">
                                        Rp {{ number_format($trx->harga_alomogada ?? 0, 0, ',', '.') }}
                                    </div>

                                </td>

                                {{-- TOTAL --}}
                                <td class="px-6 py-5">

                                    <div class="text-gray-700 font-semibold">
                                        Rp {{ number_format($trx->total ?? 0, 0, ',', '.') }}
                                    </div>
                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="5"
                                    class="px-6 py-12 text-center text-gray-500">

                                    Belum ada riwayat transaksi pulsa.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            {{-- PAGINATION --}}
            <div class="mt-6">

                {{ $riwayatTransaksi->links() }}

            </div>

        </div>

    </main>

</div>

@endsection
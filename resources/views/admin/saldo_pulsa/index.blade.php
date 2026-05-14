@extends('layouts.admin')

@section('title', 'Daftar Saldo Pulsa Warung')

@section('content')

<div class="flex-1 flex flex-col overflow-hidden">

    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6 md:p-10">

        <div class="container mx-auto">

            {{-- HEADER --}}
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-8">

                <div>
                    <h1 class="text-3xl font-bold text-gray-800">
                        Daftar Saldo Pulsa Warung
                    </h1>

                    <p class="text-sm text-gray-500 mt-1">
                        Monitoring saldo pulsa seluruh warung
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">

                    {{-- BUTTON --}}
                    <a href="{{ route('admin.saldo-pulsa.create') }}"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-5 rounded-xl transition flex items-center justify-center shadow-sm">

                        <svg class="w-5 h-5 mr-2"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24">

                            <path stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6">
                            </path>
                        </svg>

                        Top-Up Saldo
                    </a>

                    {{-- SEARCH --}}
                    <form action="{{ route('admin.saldo-pulsa.index') }}"
                        method="GET"
                        class="relative">

                        <input type="text"
                            name="search"
                            value="{{ request('search') }}"
                            class="w-full sm:w-72 bg-white border border-gray-300 rounded-xl py-2.5 pl-10 pr-4 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Cari warung / jenis pulsa...">

                        <div class="absolute top-0 left-0 h-full flex items-center pl-3">
                            <svg class="w-5 h-5 text-gray-400"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">

                                <path stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z">
                                </path>
                            </svg>
                        </div>
                    </form>

                </div>
            </div>

            {{-- ALERT SUCCESS --}}
            @if (session('success'))
                <div class="mb-6 bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded-xl">
                    {{ session('success') }}
                </div>
            @endif

            {{-- ALERT ERROR --}}
            @if (session('error'))
                <div class="mb-6 bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded-xl">
                    {{ session('error') }}
                </div>
            @endif

            {{-- TABLE --}}
            <div class="bg-white shadow-md rounded-2xl overflow-hidden overflow-x-auto">

                <table class="min-w-full">

                    <thead class="bg-gray-100 border-b border-gray-200">

                        <tr>

                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-gray-600">
                                Warung
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-gray-600">
                                Jenis Pulsa
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-gray-600">
                                Saldo
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-gray-600">
                                Update Terakhir
                            </th>

                            <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider text-gray-600">
                                Aksi
                            </th>

                        </tr>

                    </thead>

                    <tbody class="divide-y divide-gray-100">

                        @forelse ($pulsas as $pulsa)

                            <tr class="hover:bg-gray-50 transition">

                                {{-- WARUNG --}}
                                <td class="px-6 py-5">

                                    <div class="font-semibold text-gray-800">
                                        {{ $pulsa->warung->nama_warung ?? '-' }}
                                    </div>

                                </td>

                                {{-- JENIS --}}
                                <td class="px-6 py-5">

                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700 uppercase">

                                        {{ $pulsa->jenisPulsa->nama_jenis ?? '-' }}

                                    </span>

                                </td>

                                {{-- SALDO --}}
                                <td class="px-6 py-5">

                                    <div class="font-bold text-gray-800">
                                        Rp {{ number_format($pulsa->jumlah ?? 0, 0, ',', '.') }}
                                    </div>

                                </td>

                                {{-- UPDATED --}}
                                <td class="px-6 py-5 text-sm text-gray-500">

                                    @if ($pulsa->updated_at)
                                        {{ $pulsa->updated_at->diffForHumans() }}
                                    @else
                                        -
                                    @endif

                                </td>

                                {{-- AKSI --}}
                                <td class="px-6 py-5 text-center">

                                    <a href="{{ route('admin.saldo-pulsa.show', $pulsa->id) }}"
                                        class="inline-flex items-center px-4 py-2 bg-blue-50 hover:bg-blue-100 text-blue-700 text-sm font-semibold rounded-lg transition">

                                        Lihat Riwayat

                                    </a>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="5"
                                    class="px-6 py-10 text-center text-gray-500">

                                    Tidak ada data saldo pulsa ditemukan.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            {{-- PAGINATION --}}
            <div class="mt-6">

                {{ $pulsas->links() }}

            </div>

        </div>

    </main>

</div>

@endsection
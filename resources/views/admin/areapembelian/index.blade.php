@extends('layouts.admin')

@section('title', 'Data Area Pembelian')

@section('content')
<div class="flex-1 flex flex-col overflow-hidden">
    <header class="flex justify-between items-center p-6 bg-white border-b-2 border-gray-200">
        <h1 class="text-2xl font-bold text-gray-800">Manajemen Area Pembelian</h1>
    </header>

    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6 md:p-10">
        <div class="container mx-auto">

            {{-- Alert sukses --}}
            @if(session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Tombol tambah + search --}}
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                <a href="{{ route('admin.areapembelian.create') }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition">
                    + Tambah Area
                </a>

                <form action="{{ route('admin.areapembelian.index') }}" method="GET" class="mt-4 md:mt-0 relative">
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="border rounded-lg py-2 pl-10 pr-4 focus:ring-2 focus:ring-blue-500"
                        placeholder="Cari area...">
                    <div class="absolute top-0 left-0 pl-3 pt-2">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </form>
            </div>

            {{-- Table --}}
            <div class="bg-white shadow-md rounded-lg overflow-hidden overflow-x-auto">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr>
                            <th class="px-5 py-3 bg-gray-100 border-b">Area</th>
                            <th class="px-5 py-3 bg-gray-100 border-b">Markup (%)</th>
                            <th class="px-5 py-3 bg-gray-100 border-b">Keterangan</th>
                            <th class="px-5 py-3 bg-gray-100 border-b text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($areas as $area)
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-3 border-b">{{ $area->area }}</td>
                                <td class="px-5 py-3 border-b">{{ $area->markup }}</td>
                                <td class="px-5 py-3 border-b">{{ $area->keterangan ?? '-' }}</td>
                                <td class="px-5 py-3 border-b text-right">
                                    <a href="{{ route('admin.areapembelian.edit', $area->id) }}"
                                        class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                                    <form action="{{ route('admin.areapembelian.destroy', $area->id) }}"
                                        method="POST" class="inline"
                                        onsubmit="return confirm('Hapus area ini?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-3 border-b text-center text-gray-500">Tidak ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $areas->links() }}
            </div>
        </div>
    </main>
</div>
@endsection

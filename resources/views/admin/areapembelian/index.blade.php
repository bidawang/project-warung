@extends('layouts.admin')

@section('title', 'Data Area Pembelian')

@section('content')
<div class="p-4 bg-gray-50 min-h-screen">
    {{-- HEADER AREA --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
        <h1 class="text-xl font-bold text-gray-800">Manajemen Area Pembelian</h1>
        <a href="{{ route('admin.areapembelian.create') }}" 
           class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-full shadow-sm transition text-center">
            + Tambah Area
        </a>
    </div>

    {{-- ALERT AREA --}}
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 border border-green-200 text-green-700 rounded-lg text-sm flex items-center">
            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- SEARCH AREA --}}
    <div class="mb-6">
        <form action="{{ route('admin.areapembelian.index') }}" method="GET" class="relative max-w-md">
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="Cari nama area..." 
                   class="w-full border-gray-300 rounded-lg p-2 pl-10 focus:ring-blue-500 focus:border-blue-500 shadow-sm">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
        </form>
    </div>

    {{-- TABLE AREA --}}
    <div class="bg-white border border-gray-100 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Area</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Markup</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Keterangan</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse ($areas as $area)
                        <tr class="hover:bg-blue-50/30 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-semibold text-gray-800 text-sm">{{ $area->area }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs font-bold">
                                    {{ $area->markup }}%
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-xs text-gray-500 line-clamp-1">{{ $area->keterangan ?? '-' }}</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end gap-3">
                                    <a href="{{ route('admin.areapembelian.edit', $area->id) }}" 
                                       class="text-blue-600 hover:text-blue-800 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.areapembelian.destroy', $area->id) }}" 
                                          method="POST" 
                                          onsubmit="return confirm('Hapus area ini?');" 
                                          class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-600 transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-gray-400 italic text-sm">
                                Tidak ada data area ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- PAGINATION --}}
    <div class="mt-4">
        {{ $areas->appends(request()->query())->links() }}
    </div>
</div>
@endsection
@extends('layouts.admin')

@section('title', 'Data Harga Pulsa')

@section('content')
<div class="flex-1 flex flex-col overflow-hidden">
    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6 md:p-10">
        <div class="container mx-auto">

            {{-- Header --}}
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-4 md:mb-0">
                    Daftar Harga Pulsa
                </h1>

                <a href="{{ route('admin.harga-pulsa.create') }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-5 rounded-full flex items-center transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m-6-6h12" />
                    </svg>
                    Tambah Harga
                </a>
            </div>

            {{-- Filter & Search --}}
            <form action="{{ route('admin.harga-pulsa.index') }}" method="GET"
                class="bg-white p-4 rounded-lg shadow mb-6 flex flex-col md:flex-row gap-4">

                {{-- Filter Jenis Pulsa --}}
                <select name="jenis_pulsa_id"
                    class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Jenis Pulsa</option>
                    @foreach ($jenisPulsa as $jp)
                        <option value="{{ $jp->id }}"
                            {{ request('jenis_pulsa_id') == $jp->id ? 'selected' : '' }}>
                            {{ $jp->nama_jenis }}
                        </option>
                    @endforeach
                </select>

                {{-- Search --}}
                <input type="text" name="search" value="{{ request('search') }}"
                    class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500"
                    placeholder="Cari jumlah pulsa...">

                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold">
                    Filter
                </button>

                @if(request()->hasAny(['search', 'jenis_pulsa_id']))
                    <a href="{{ route('admin.harga-pulsa.index') }}"
                        class="bg-gray-200 hover:bg-gray-300 px-4 py-2 rounded-lg text-gray-700 font-semibold">
                        Reset
                    </a>
                @endif
            </form>

            {{-- Table --}}
            <div class="bg-white shadow rounded-lg overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-gray-100 text-gray-600 uppercase text-xs">
                            <th class="px-5 py-3 text-left">Jumlah Pulsa</th>
                            <th class="px-5 py-3 text-left">Harga</th>
                            <th class="px-5 py-3 text-left">Jenis Pulsa</th>
                            <th class="px-5 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($hargaPulsas as $hargaPulsa)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-5 py-4">
                                    Rp{{ number_format($hargaPulsa->jumlah_pulsa, 0, ',', '.') }}
                                </td>
                                <td class="px-5 py-4">
                                    Rp{{ number_format($hargaPulsa->harga, 0, ',', '.') }}
                                </td>
                                <td class="px-5 py-4">
                                    {{ $hargaPulsa->nama_jenis }}
                                </td>
                                <td class="px-5 py-4 text-right space-x-2">
                                    <a href="{{ route('admin.harga-pulsa.edit', $hargaPulsa->id) }}"
                                        class="text-blue-600 hover:text-blue-800">
                                        Edit
                                    </a>

                                    <form action="{{ route('admin.harga-pulsa.destroy', $hargaPulsa->id) }}"
                                        method="POST" class="inline"
                                        onsubmit="return confirm('Yakin hapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-red-600 hover:text-red-800">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-6 text-center text-gray-500">
                                    Data harga pulsa tidak ditemukan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </main>
</div>
@endsection

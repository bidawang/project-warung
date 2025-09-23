@extends('layouts.admin')

@section('title', 'Manajemen Laba')

@section('content')

<div class="container mx-auto px-4 py-8">
    <div class="text-center my-8">
        <h1 class="text-3xl font-bold text-gray-800">Manajemen Laba</h1>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
        <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
            <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20" onclick="this.parentElement.parentElement.style.display='none'">
                <title>Close</title>
                <path
                    d="M14.348 14.849a1.2 1.2 0 01-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 11-1.697-1.697l3.029-2.651-3.029-2.651a1.2 1.2 0 011.697-1.697l2.651 3.029 2.651-3.029a1.2 1.2 0 111.697 1.697L11.819 10l3.029 2.651a1.2 1.2 0 010 1.698z" />
            </svg>
        </span>
    </div>
    @endif
    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
        <span class="block sm:inline">{{ session('error') }}</span>
        <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
            <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20" onclick="this.parentElement.parentElement.style.display='none'">
                <title>Close</title>
                <path
                    d="M14.348 14.849a1.2 1.2 0 01-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 11-1.697-1.697l3.029-2.651-3.029-2.651a1.2 1.2 0 011.697-1.697l2.651 3.029 2.651-3.029a1.2 1.2 0 111.697 1.697L11.819 10l3.029 2.651a1.2 1.2 0 010 1.698z" />
            </svg>
        </span>
    </div>
    @endif

    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="flex flex-col md:flex-row justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-gray-700 mb-4 md:mb-0">Daftar Laba</h2>
            <a href="{{ route('laba.create') }}"
                class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-full shadow-lg transition duration-300 ease-in-out transform hover:scale-105 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6">
                    </path>
                </svg>
                Tambah Data Laba
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                        <th class="py-3 px-6 text-left">#</th>
                        <th class="py-3 px-6 text-left">Area</th>
                        <th class="py-3 px-6 text-left">Input Minimal</th>
                        <th class="py-3 px-6 text-left">Input Maksimal</th>
                        <th class="py-3 px-6 text-left">Harga Jual</th>
                        <th class="py-3 px-6 text-left">Jenis</th>
                        <th class="py-3 px-6 text-left">Keterangan</th>
                        <th class="py-3 px-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    @forelse ($labas as $laba)
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-6 text-left whitespace-nowrap">{{ $loop->iteration }}</td>
                        <td class="py-3 px-6 text-left">{{ $laba->area->area ?? '-' }}</td>
                        <td class="py-3 px-6 text-left">{{ $laba->input_minimal }}</td>
                        <td class="py-3 px-6 text-left">{{ $laba->input_maksimal }}</td>
                        <td class="py-3 px-6 text-left">{{ $laba->harga_jual }}</td>
                        <td class="py-3 px-6 text-left">{{ $laba->jenis ?? '-' }}</td>
                        <td class="py-3 px-6 text-left">{{ $laba->keterangan ?? '-' }}</td>
                        <td class="py-3 px-6 text-center">
                            <div class="flex item-center justify-center">
                                <a href="{{ route('laba.edit', $laba->id) }}"
                                    class="w-6 h-6 mr-2 transform hover:scale-110" title="Edit">
                                    <svg class="w-full h-full text-yellow-500 hover:text-yellow-600" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                        </path>
                                    </svg>
                                </a>
                                <form action="{{ route('laba.destroy', $laba->id) }}" method="POST" class="inline"
                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus data laba ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-6 h-6 transform hover:scale-110" title="Hapus">
                                        <svg class="w-full h-full text-red-500 hover:text-red-600" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1H9a1 1 0 00-1 1v3m-4 0h12">
                                            </path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-3 px-6 text-center">Belum ada data laba.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

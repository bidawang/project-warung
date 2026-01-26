@extends('layouts.admin')

@section('title', 'Jenis Pulsa')

@section('content')
<div class="flex-1 flex flex-col overflow-hidden">
    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6 md:p-10">
        <div class="container mx-auto max-w-4xl">

            <div class="flex justify-between items-center mb-6">
                <h2 class="text-3xl font-bold text-gray-800">Data Jenis Pulsa</h2>
                <a href="{{ route('admin.jenis-pulsa.create') }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                    + Tambah Jenis Pulsa
                </a>
            </div>

            {{-- Notifikasi --}}
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow rounded-lg overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-200 text-gray-700">
                        <tr>
                            <th class="px-6 py-3">No</th>
                            <th class="px-6 py-3">Nama Jenis Pulsa</th>
                            <th class="px-6 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($jenisPulsa as $item)
                            <tr class="border-b">
                                <td class="px-6 py-3">{{ $loop->iteration }}</td>
                                <td class="px-6 py-3">{{ $item->nama_jenis }}</td>
                                <td class="px-6 py-3 text-center space-x-2">
                                    <a href="{{ route('admin.jenis-pulsa.edit', $item->id) }}"
                                       class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded">
                                        Edit
                                    </a>

                                    <form action="{{ route('admin.jenis-pulsa.destroy', $item->id) }}"
                                          method="POST" class="inline"
                                          onsubmit="return confirm('Yakin hapus jenis pulsa ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-center text-gray-500">
                                    Data jenis pulsa belum ada
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

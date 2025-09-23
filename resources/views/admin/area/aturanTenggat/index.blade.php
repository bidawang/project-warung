@extends('layouts.admin')

@section('title', 'Aturan Tenggat')

@section('content')
<div class="flex-1 flex flex-col overflow-hidden">
    <header class="flex justify-between items-center p-6 bg-white border-b-2 border-gray-200">
        <h1 class="text-2xl font-bold text-gray-800">
            Aturan Tenggat {{ $area ? 'Area: '.$area->area : '' }}
        </h1>
        @if($area)
            <a href="{{ route('admin.aturanTenggat.create', ['id_area' => $area->id]) }}"
               class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full">
               + Tambah
            </a>
        @endif
    </header>

    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6 md:p-10">
        <div class="bg-white shadow-md rounded-lg p-6">
            @if($aturanTenggat->count())
            <table class="min-w-full table-auto border border-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 border">Area</th>
                        <th class="px-4 py-2 border">Tanggal Awal</th>
                        <th class="px-4 py-2 border">Tanggal Akhir</th>
                        <th class="px-4 py-2 border">Hari</th>
                        <th class="px-4 py-2 border">Bunga (%)</th>
                        <th class="px-4 py-2 border">Keterangan</th>
                        <th class="px-4 py-2 border">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($aturanTenggat as $t)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 border">{{ $t->area->area }}</td>
                        <td class="px-4 py-2 border">{{ $t->tanggal_awal }}</td>
                        <td class="px-4 py-2 border">{{ $t->tanggal_akhir }}</td>
                        <td class="px-4 py-2 border">{{ $t->jatuh_tempo_hari }}</td>
                        <td class="px-4 py-2 border">{{ $t->bunga }}</td>
                        <td class="px-4 py-2 border">{{ $t->keterangan ?? '-' }}</td>
                        <td class="px-4 py-2 border space-x-2">
                            <a href="{{ route('admin.aturanTenggat.edit', $t->id) }}"
                               class="text-blue-600 hover:text-blue-800">Edit</a>
                            <form action="{{ route('admin.aturanTenggat.destroy', $t->id) }}" method="POST" class="inline-block"
                                  onsubmit="return confirm('Yakin ingin menghapus?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-4">
                {{-- {{ $aturanTenggat->links() }} --}}
            </div>
            @else
            <p class="text-gray-500">Belum ada data aturan tenggat.</p>
            @endif
        </div>
    </main>
</div>
@endsection

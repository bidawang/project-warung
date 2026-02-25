@extends('layouts.admin')

@section('title', 'Pengeluaran Pokok Warung')

@section('content')

<div class="space-y-6">

    {{-- Header Action --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Pengeluaran Pokok</h2>
            <p class="text-sm text-gray-500">Monitoring seluruh pengeluaran inti warung</p>
        </div>

        <a href="{{ route('admin.pengeluaran-pokok-warung.create') }}"
           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition">
            + Tambah Pengeluaran
        </a>
    </div>

    {{-- Filter --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <form method="GET" class="flex flex-col md:flex-row gap-3 md:items-center">

            <select name="warung"
                class="w-full md:w-64 rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                <option value="">Semua Warung</option>
                @foreach($warungs as $w)
                    <option value="{{ $w->id }}"
                        {{ request('warung') == $w->id ? 'selected' : '' }}>
                        {{ $w->nama_warung }}
                    </option>
                @endforeach
            </select>

            <button
                class="px-4 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition">
                Filter
            </button>

        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100 text-gray-600 uppercase text-xs tracking-wider">
                    <tr>
                        <th class="px-6 py-3 text-left">Tanggal</th>
                        <th class="px-6 py-3 text-left">Warung</th>
                        <th class="px-6 py-3 text-left">Redaksi</th>
                        <th class="px-6 py-3 text-right">Jumlah</th>
                        <th class="px-6 py-3 text-center">Status</th>
                        <th class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">

                    @forelse($data as $item)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                {{ \Carbon\Carbon::parse($item->date)->format('d M Y') }}
                            </td>

                            <td class="px-6 py-4 font-medium text-gray-800">
                                {{ $item->warung->nama_warung ?? '-' }}
                            </td>

                            <td class="px-6 py-4 text-gray-600">
                                {{ $item->redaksi }}
                            </td>

                            <td class="px-6 py-4 text-right font-semibold text-gray-800">
                                Rp {{ number_format($item->jumlah,0,',','.') }}
                            </td>

                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 text-xs rounded-full font-semibold
                                    {{ $item->status == 'belum terpenuhi'
                                        ? 'bg-yellow-100 text-yellow-700'
                                        : 'bg-green-100 text-green-700' }}">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-center space-x-2">

                                <a href="{{ route('admin.pengeluaran-pokok-warung.show',$item) }}"
                                   class="text-blue-600 hover:underline text-sm">
                                    Detail
                                </a>

                                @if($item->status == 'belum terpenuhi')
                                    <a href="{{ route('admin.pengeluaran-pokok-warung.edit',$item) }}"
                                       class="text-indigo-600 hover:underline text-sm">
                                        Edit
                                    </a>

                                    <form action="{{ route('admin.pengeluaran-pokok-warung.destroy',$item) }}"
                                          method="POST"
                                          class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            onclick="return confirm('Hapus data ini?')"
                                            class="text-red-600 hover:underline text-sm">
                                            Hapus
                                        </button>
                                    </form>
                                @endif

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-400">
                                Belum ada data pengeluaran.
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-100">
            {{ $data->withQueryString()->links() }}
        </div>
    </div>

</div>

@endsection
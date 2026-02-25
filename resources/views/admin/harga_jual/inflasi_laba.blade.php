@extends('layouts.admin')

@section('title', 'Inflasi Laba')

@section('content')

<div class="max-w-7xl mx-auto">

    {{-- Card Container --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">

        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-6">

            <div>
                <h2 class="text-lg font-bold text-gray-800">Monitoring Inflasi Laba</h2>
                <p class="text-sm text-gray-500">
                    Perubahan margin laba antar periode berdasarkan histori harga jual.
                </p>
            </div>

            {{-- Form Select Warung --}}
            <form method="GET" action="{{ route('admin.harga_jual.inflasi_laba') }}"
                class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">

                <select name="id_warung"
                    class="w-full sm:w-64 border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500"
                    required>
                    <option value="">-- Pilih Warung --</option>
                    @foreach($allWarung as $warung)
                        <option value="{{ $warung->id }}"
                            {{ $selectedWarungId == $warung->id ? 'selected' : '' }}>
                            {{ $warung->nama_warung }}
                        </option>
                    @endforeach
                </select>

                <button type="submit"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg shadow">
                    Lihat
                </button>

            </form>
        </div>

        {{-- Tabel --}}
        @if($selectedWarungId)

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-100 text-gray-600 uppercase text-xs tracking-wider">
                        <tr>
                            <th class="px-4 py-3 text-left">Barang</th>
                            <th class="px-4 py-3 text-left">Periode</th>
                            <th class="px-4 py-3 text-right">Margin</th>
                            <th class="px-4 py-3 text-right">Inflasi Laba</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100 bg-white">

                        @forelse($inflasiData as $row)

                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 font-medium text-gray-800">
                                    {{ $row['nama_barang'] }}
                                </td>

                                <td class="px-4 py-3 text-gray-600">
                                    {{ $row['periode_awal'] ?? '-' }}
                                    -
                                    {{ $row['periode_akhir'] ?? '-' }}
                                </td>

                                <td class="px-4 py-3 text-right font-semibold text-gray-700">
                                    Rp {{ number_format($row['margin'], 0, ',', '.') }}
                                </td>

                                <td class="px-4 py-3 text-right font-semibold">
                                    @if($row['inflasi'] !== null)

                                        @if($row['inflasi'] > 0)
                                            <span class="text-green-600">
                                                ↑ {{ $row['inflasi'] }} %
                                            </span>
                                        @elseif($row['inflasi'] < 0)
                                            <span class="text-red-600">
                                                ↓ {{ $row['inflasi'] }} %
                                            </span>
                                        @else
                                            <span class="text-gray-500">
                                                0 %
                                            </span>
                                        @endif

                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>

                        @empty

                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-gray-500">
                                    Tidak ada histori periode untuk warung ini.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>
                </table>
            </div>

        @else

            <div class="text-center py-10 text-gray-400">
                Silakan pilih warung terlebih dahulu untuk melihat inflasi laba.
            </div>

        @endif

    </div>
</div>

@endsection
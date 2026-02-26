@extends('layouts.admin')

@section('title', 'Inflasi Laba')

@section('content')

<div class="max-w-7xl mx-auto">

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">

        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-6">

            <div>
                <h2 class="text-lg font-bold text-gray-800">Monitoring Inflasi Laba</h2>
                <p class="text-sm text-gray-500">
                    Klik baris barang untuk melihat histori lengkap.
                </p>
            </div>

            <form method="GET" action="{{ route('admin.harga_jual.inflasi_laba') }}"
                class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">

                <select name="id_warung"
                    class="w-full sm:w-64 border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500"
                    required>
                    <option value="">-- Pilih Warung --</option>
                    @foreach ($allWarung as $warung)
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

        @if ($selectedWarungId)

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-100 text-gray-600 uppercase text-xs tracking-wider">
                    <tr>
                        <th class="px-4 py-3 text-left">Barang</th>
                        <th class="px-4 py-3 text-left">Periode</th>
                        <th class="px-4 py-3 text-right">Total</th>
                        <th class="px-4 py-3 text-right">Terjual</th>
                        <th class="px-4 py-3 text-right">Margin</th>
                        <th class="px-4 py-3 text-right">Inflasi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 bg-white">

                @forelse($inflasiData as $row)

                    <tr class="hover:bg-gray-50 transition cursor-pointer main-row"
                        data-barang="{{ $row['id_barang'] }}"
                        data-warung="{{ $selectedWarungId }}">

                        <td class="px-4 py-3 font-semibold text-gray-800">
                            {{ $row['nama_barang'] }}
                        </td>

                        <td class="px-4 py-3 text-gray-600">
                            {{ $row['periode_awal'] }} - {{ $row['periode_akhir'] ?? 'Sekarang' }}
                        </td>

                        <td class="px-4 py-3 text-right">
                            {{ number_format($row['total_barang'],0,',','.') }}
                        </td>

                        <td class="px-4 py-3 text-right">
                            {{ number_format($row['barang_terjual'],0,',','.') }}
                        </td>

                        {{-- MARGIN --}}
                        <td class="px-4 py-3 text-right font-semibold text-gray-700">
                            @if($row['margin_awal'] == $row['margin_akhir'])
                                Rp {{ number_format($row['margin_awal'],0,',','.') }}
                            @else
                                Rp {{ number_format($row['margin_awal'],0,',','.') }}
                                -
                                {{ number_format($row['margin_akhir'],0,',','.') }}
                            @endif
                        </td>

                        {{-- INFLASI TERAKHIR --}}
                        <td class="px-4 py-3 text-right font-semibold">
                            @if($row['inflasi_akhir'] !== null)
                                <span class="
                                    {{ $row['inflasi_akhir'] > 0 ? 'text-green-600' :
                                       ($row['inflasi_akhir'] < 0 ? 'text-red-600' : 'text-gray-500') }}">
                                    {{ $row['inflasi_akhir'] }} %
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>

                    </tr>

                    {{-- DETAIL --}}
                    <tr class="hidden detail-row bg-gray-50">
                        <td colspan="6" class="p-4">
                            <div class="detail-content text-sm text-gray-600">
                                Loading...
                            </div>
                        </td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                            Tidak ada histori periode.
                        </td>
                    </tr>
                @endforelse

                </tbody>
            </table>
        </div>

        @else
            <div class="text-center py-10 text-gray-400">
                Silakan pilih warung terlebih dahulu.
            </div>
        @endif

    </div>
</div>

@if($selectedWarungId)
<script>
document.querySelectorAll('.main-row').forEach(row => {

    row.addEventListener('click', function() {

        let detailRow = this.nextElementSibling;
        let content = detailRow.querySelector('.detail-content');

        if (!detailRow.classList.contains('hidden')) {
            detailRow.classList.add('hidden');
            return;
        }

        let barangId = this.dataset.barang;
        let warungId = this.dataset.warung;

        fetch("{{ route('admin.harga_jual.inflasi_laba_detail') }}?id_barang=" 
            + barangId + "&id_warung=" + warungId)
        .then(res => res.json())
        .then(data => {

            let html = `
            <div class="overflow-x-auto">
            <table class="min-w-full text-xs divide-y divide-gray-200">
                <thead class="bg-gray-200 text-gray-600 uppercase">
                    <tr>
                        <th class="px-3 py-2 text-left">Periode</th>
                        <th class="px-3 py-2 text-right">Total</th>
                        <th class="px-3 py-2 text-right">Terjual</th>
                        <th class="px-3 py-2 text-right">Margin</th>
                        <th class="px-3 py-2 text-right">Inflasi</th>
                    </tr>
                </thead>
                <tbody>
            `;

            data.forEach(r => {

                let margin = (r.margin_awal == r.margin_akhir)
                    ? `Rp ${Number(r.margin_awal).toLocaleString()}`
                    : `Rp ${Number(r.margin_awal).toLocaleString()} - ${Number(r.margin_akhir).toLocaleString()}`;

                let inflasi = "-";
                if (r.inflasi_awal !== null) {
                    inflasi = (r.inflasi_awal == r.inflasi_akhir)
                        ? r.inflasi_awal + " %"
                        : r.inflasi_awal + " % - " + r.inflasi_akhir + " %";
                }

                html += `
                <tr class="border-b">
                    <td class="px-3 py-2">${r.periode_awal} - ${r.periode_akhir}</td>
                    <td class="px-3 py-2 text-right">${Number(r.total_barang).toLocaleString()}</td>
                    <td class="px-3 py-2 text-right">${Number(r.barang_terjual).toLocaleString()}</td>
                    <td class="px-3 py-2 text-right font-semibold">${margin}</td>
                    <td class="px-3 py-2 text-right">${inflasi}</td>
                </tr>
                `;
            });

            html += `</tbody></table></div>`;

            content.innerHTML = html;
            detailRow.classList.remove('hidden');
        });
    });
});
</script>
@endif

@endsection
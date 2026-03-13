@foreach($historyLaba as $history)
<tr class="hover:bg-slate-50/80 transition-colors border-b border-slate-100">
    <td class="px-6 py-4 text-sm text-slate-600">
        {{ $history->created_at->format('d/m/Y') }}
        <span class="block text-xs text-slate-400">{{ $history->created_at->format('H:i') }}</span>
    </td>
    <td class="px-6 py-4">
        <span class="text-sm font-semibold text-slate-800 block">
            {{ $history->stokWarung->barang->nama_barang ?? 'N/A' }}
        </span>
        <span class="text-xs text-slate-500">Rp {{ number_format($history->harga_jual, 0, ',', '.') }}</span>
    </td>
    <td class="px-6 py-4 text-sm text-slate-600 text-center">{{ $history->jumlah }}</td>
    <td class="px-6 py-4">
        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $history->jenis == 'penjualan barang' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
            {{ $history->jenis }}
        </span>
    </td>
    <td class="px-6 py-4 text-sm font-bold text-right {{ $history->laba_bersih > 0 ? 'text-emerald-600' : 'text-rose-600' }}">
        Rp {{ number_format($history->laba_bersih, 0, ',', '.') }}
    </td>
</tr>
@endforeach
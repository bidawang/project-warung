@extends('layouts.admin')

@section('title', 'Riwayat Inject Kas')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800">Riwayat Suntik Kas</h2>
        <a href="{{ route('admin.inject-kas.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Tambah Saldo / Inject
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50 text-gray-600 text-xs uppercase font-bold">
                <tr>
                    <th class="px-6 py-4">Tanggal</th>
                    <th class="px-6 py-4">Warung</th>
                    <th class="px-6 py-4">Jenis Kas</th>
                    <th class="px-6 py-4">Keterangan</th>
                    <th class="px-6 py-4 text-right">Jumlah</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm">
                @foreach($riwayat as $item)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 text-gray-500">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-6 py-4 font-bold text-gray-800">{{ $item->kasWarung->warung->nama_warung ?? 'N/A' }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded text-[10px] font-bold uppercase {{ $item->metode_pembayaran == 'bank' ? 'bg-purple-100 text-purple-700' : 'bg-orange-100 text-orange-700' }}">
                            {{ $item->metode_pembayaran }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-600">{{ $item->keterangan }}</td>
                    <td class="px-6 py-4 text-right font-bold text-green-600">+ Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-6 py-4 bg-gray-50 border-t">
            {{ $riwayat->links() }}
        </div>
    </div>
</div>
@endsection
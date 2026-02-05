@extends('layouts.admin')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Laporan Keuangan Warung</h1>
            <p class="text-gray-500 text-sm">Pilih unit warung untuk menganalisis laba kotor dan laba bersih.</p>
        </div>

        <div class="relative w-full md:w-80">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </span>
            <input type="text" id="searchInput"
                   class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all shadow-sm bg-white"
                   placeholder="Cari nama warung...">
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse" id="warungTable">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 uppercase text-xs font-semibold tracking-wider">
                        <th class="px-6 py-4">ID</th>
                        <th class="px-6 py-4">Nama Warung</th>
                        <th class="px-6 py-4 text-center">Modal Awal</th>
                        <th class="px-6 py-4">Keterangan</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($warungs as $warung)
                    <tr class="hover:bg-blue-50/50 transition-colors group">
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                #{{ $warung->id }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mr-3 group-hover:bg-blue-600 group-hover:text-white transition-all">
                                    <i class="fas fa-store text-sm"></i>
                                </div>
                                <div class="font-semibold text-gray-800">{{ $warung->nama_warung }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-gray-700 font-medium">Rp {{ number_format($warung->modal, 0, ',', '.') }}</span>
                        </td>
                        <td class="px-6 py-4 text-gray-500 text-sm italic">
                            {{ $warung->keterangan ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.laporan-laba.show', $warung->id) }}"
                               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow-sm transition-all active:scale-95">
                                <i class="fas fa-chart-line mr-2"></i> Detail Laba
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-folder-open text-4xl mb-3 text-gray-300"></i>
                                <p>Tidak ada data warung ditemukan.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-between items-center text-sm text-gray-500 font-medium">
            <span>Total: {{ $warungs->count() }} Warung</span>
            <span class="text-xs italic text-gray-400">Update Terakhir: {{ now()->format('d/m/Y H:i') }}</span>
        </div>
    </div>
</div>

<script>
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const filter = this.value.toLowerCase();
        const rows = document.querySelectorAll('#warungTable tbody tr');

        rows.forEach(row => {
            const warungName = row.cells[1].textContent.toLowerCase();
            if (warungName.includes(filter)) {
                row.classList.remove('hidden');
            } else {
                row.classList.add('hidden');
            }
        });
    });
</script>
@endsection 

@extends('layouts.admin')

@section('title', 'Detail Area')

@section('content')
<div class="flex-1 flex flex-col overflow-hidden">
    <header class="flex justify-between items-center p-6 bg-white border-b-2 border-gray-200">
        <h1 class="text-2xl font-bold text-gray-800">Detail Area: {{ $area->area }}</h1>
        <a href="{{ url('/admin/area') }}"
            class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-6 rounded-full transition-colors duration-200">
            Kembali
        </a>
    </header>

    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6 md:p-10">
        <div class="bg-white shadow-md rounded-lg p-8 space-y-6">

            {{-- Info Area --}}
            <div>
                <h2 class="text-xl font-semibold mb-2">Informasi Area</h2>
                <p><strong>Nama Area:</strong> {{ $area->area }}</p>
                <p><strong>Keterangan:</strong> {{ $area->keterangan ?? '-' }}</p>
            </div>

            {{-- Warung --}}
            <div>
                <h2 class="text-xl font-semibold mb-2">Warung</h2>
                @if($area->warung->count())
                    <ul class="list-disc list-inside">
                        @foreach($area->warung as $warung)
                            <li>{{ $warung->nama_warung }} (Modal: {{ number_format($warung->modal,0,',','.') }})</li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-gray-500">Belum ada warung terkait.</p>
                @endif
            </div>

            {{-- Accordion: Aturan Tenggat --}}
            <div>
                <button type="button" class="accordion w-full text-left px-4 py-2 bg-gray-200 rounded-lg focus:outline-none">
                    Aturan Tenggat
                </button>
                <div class="panel max-h-0 overflow-hidden transition-all duration-300">
                    @if($area->aturanTenggat->count())
                        <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden mt-2">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-2 border-b">Tanggal Awal</th>
                                    <th class="px-4 py-2 border-b">Tanggal Akhir</th>
                                    <th class="px-4 py-2 border-b">Jatuh Tempo Hari</th>
                                    <th class="px-4 py-2 border-b">Jatuh Tempo Bulan</th>
                                    <th class="px-4 py-2 border-b">Bunga</th>
                                    <th class="px-4 py-2 border-b">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($area->aturanTenggat as $tenggat)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2 border-b">{{ $tenggat->tanggal_awal }}</td>
                                    <td class="px-4 py-2 border-b">{{ $tenggat->tanggal_akhir }}</td>
                                    <td class="px-4 py-2 border-b">{{ $tenggat->jatuh_tempo_hari }}</td>
                                    <td class="px-4 py-2 border-b">{{ $tenggat->jatuh_tempo_bulan }}</td>
                                    <td class="px-4 py-2 border-b">{{ $tenggat->bunga }}%</td>
                                    <td class="px-4 py-2 border-b">{{ $tenggat->keterangan ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-gray-500 mt-2">Belum ada aturan tenggat.</p>
                    @endif
                </div>
            </div>

            {{-- Accordion: Laba --}}
            <div>
                <button type="button" class="accordion w-full text-left px-4 py-2 bg-gray-200 rounded-lg focus:outline-none mt-2">
                    Laba
                </button>
                <div class="panel max-h-0 overflow-hidden transition-all duration-300">
                    @if($area->laba->count())
                        <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden mt-2">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-2 border-b">Input Minimal</th>
                                    <th class="px-4 py-2 border-b">Input Maksimal</th>
                                    <th class="px-4 py-2 border-b">Harga Jual</th>
                                    <th class="px-4 py-2 border-b">Jenis</th>
                                    <th class="px-4 py-2 border-b">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($area->laba as $l)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2 border-b">{{ $l->input_minimal }}</td>
                                    <td class="px-4 py-2 border-b">{{ $l->input_maksimal }}</td>
                                    <td class="px-4 py-2 border-b">{{ number_format($l->harga_jual,0,',','.') }}</td>
                                    <td class="px-4 py-2 border-b">{{ $l->jenis }}</td>
                                    <td class="px-4 py-2 border-b">{{ $l->keterangan ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-gray-500 mt-2">Belum ada data laba.</p>
                    @endif
                </div>
            </div>

        </div>
    </main>
</div>

{{-- Script Accordion --}}
<script>
    const accordions = document.querySelectorAll('.accordion');
    accordions.forEach(acc => {
        acc.addEventListener('click', () => {
            acc.classList.toggle('active');
            const panel = acc.nextElementSibling;
            if (panel.style.maxHeight) {
                panel.style.maxHeight = null;
            } else {
                panel.style.maxHeight = panel.scrollHeight + 'px';
            }
        });
    });
</script>
@endsection

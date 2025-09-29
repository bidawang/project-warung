@extends('layouts.admin')

@section('title', 'Daftar Transaksi Barang')

@section('content')
<div class="flex-1 flex flex-col overflow-hidden">
    {{-- Header --}}
    <header class="flex justify-between items-center p-6 bg-white border-b-2 border-gray-200">
        <button id="openSidebarBtn" class="text-gray-500 hover:text-gray-900 lg:hidden">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>
        <h1 class="text-2xl font-bold text-gray-800">Manajemen Transaksi Barang</h1>
        <div class="flex items-center">
            <span class="mr-4 font-semibold hidden sm:inline">Admin User</span>
            <div class="w-10 h-10 bg-blue-500 rounded-full"></div>
        </div>
    </header>

    {{-- Main Content --}}
    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6 md:p-10">
        <div class="container mx-auto">

            {{-- Tombol Aksi --}}
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-800 mb-4 md:mb-0">Daftar Transaksi Barang</h1>
                <div class="flex space-x-4">
                    {{-- Tombol Kirim Massal (hanya untuk status pending) --}}
                    @if($status === 'pending')
                    <button id="openModalBtn" disabled
                        class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg shadow transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Kirim Terpilih
                    </button>
                    @endif
                    <a href="{{ route('transaksibarang.create') }}"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow transition-colors duration-200 flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Rencana Belanja
                    </a>
                </div>
            </div>

            {{-- Nav Tabs --}}
            <nav class="mb-6 border-b border-gray-300">
                @php
                $tabs = [
                'pending' => 'Belum Dikirim',
                'kirim' => 'Dikirim',
                'terima' => 'Sudah Diterima',
                'tolak' => 'Ditolak'
                ];
                @endphp
                <ul class="flex space-x-4">
                    @foreach($tabs as $key => $label)
                    <li>
                        <a href="{{ route('transaksibarang.index',['status'=>$key]) }}"
                            class="inline-block px-4 py-2 rounded-t-lg font-semibold
                               {{ $status===$key ? 'bg-white border border-b-0 border-gray-300 text-blue-600' : 'text-gray-600 hover:text-blue-600' }}">
                            {{ $label }}
                        </a>
                    </li>
                    @endforeach
                </ul>
            </nav>

            {{-- Alert --}}
            @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
            @endif

            {{-- Tabel --}}
            <div class="bg-white shadow-md rounded-lg overflow-hidden overflow-x-auto">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr>
                            @if($status === 'pending')
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100">
                                <input type="checkbox" id="checkAll" class="cursor-pointer" />
                            </th>
                            @endif
                            <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Barang</th>
                            <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Jumlah</th>
                            <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Harga</th>
                            <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                            <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Jenis</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transaksibarangs as $trx)
                        <tr class="hover:bg-gray-50">
                            @if($status === 'pending')
                            <td class="px-5 py-5 border-b text-sm">
                                <input type="checkbox" name="ids[]" value="{{ $trx->id }}" class="checkbox-item cursor-pointer" />
                            </td>
                            @endif
                            <td class="px-5 py-5 border-b text-sm">{{ $trx->barang->nama_barang ?? '-' }}</td>
                            <td class="px-5 py-5 border-b text-sm">{{ $trx->jumlah }}</td>
                            <td class="px-5 py-5 border-b text-sm">{{ $trx->harga }}</td>
                            <td class="px-5 py-5 border-b text-sm">{{ ucfirst($trx->status) }}</td>
                            <td class="px-5 py-5 border-b text-sm">{{ ucfirst($trx->jenis) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ $status==='pending' ? 7 : 6 }}" class="px-5 py-5 border-b text-center text-gray-500">
                                Tidak ada transaksi ditemukan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-8">
                {{ $transaksibarangs->links() }}
            </div>
        </div>
    </main>
</div>

{{-- Modal Kirim --}}
<div id="modalKirim" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 transition">
    <div class="bg-white rounded-lg shadow-lg max-w-3xl w-full p-6 relative animate-scaleIn">
        <button id="closeModal" class="absolute top-3 right-3 text-gray-600 hover:text-gray-900 text-xl font-bold">&times;</button>
        <h2 class="text-xl font-semibold mb-4">Pilih Warung Tujuan</h2>

        <form id="formKirim" method="POST" action="{{ route('admin.transaksibarang.kirim.mass.proses') }}">
            @csrf
            <input type="hidden" name="transaksi_ids" id="transaksi_ids" />

            <div class="mb-4">
                <label class="block font-semibold mb-2">Warung Tujuan</label>
                <select name="warung_id" id="warung_id" required class="w-full border rounded px-3 py-2">
                    <option value="" disabled selected>Pilih Warung</option>
                    @foreach($warungs as $warung)
                    <option value="{{ $warung->id }}">{{ $warung->nama_warung }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex justify-end space-x-4 mt-6">
                <button type="button" id="btnCancel" class="px-4 py-2 rounded border border-gray-300 hover:bg-gray-100">Batal</button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Kirim</button>
            </div>
        </form>
    </div>
</div>

{{-- Script --}}
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const checkAll = document.getElementById('checkAll');
        const checkboxes = document.querySelectorAll('.checkbox-item');
        const openModalBtn = document.getElementById('openModalBtn');
        const modal = document.getElementById('modalKirim');
        const closeModalBtn = document.getElementById('closeModal');
        const btnCancel = document.getElementById('btnCancel');
        const transaksiIdsInput = document.getElementById('transaksi_ids');
        const formKirim = document.getElementById('formKirim');

        function toggleModal() {
            modal.classList.toggle('hidden');
            modal.classList.toggle('flex');
        }

        function updateButtonState() {
            const checkedCount = document.querySelectorAll('.checkbox-item:checked').length;
            openModalBtn.disabled = checkedCount === 0;
        }

        // Event listener untuk tombol 'Pilih Semua'
        if (checkAll) {
            checkAll.addEventListener('change', e => {
                checkboxes.forEach(cb => cb.checked = e.target.checked);
                updateButtonState();
            });
        }

        // Event listener untuk setiap checkbox
        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateButtonState);
        });

        // Event listener untuk tombol 'Kirim Terpilih' yang membuka modal
        openModalBtn.addEventListener('click', () => {
            const checkedIds = Array.from(document.querySelectorAll('.checkbox-item:checked')).map(cb => cb.value);
            if (checkedIds.length > 0) {
                // Mengisi input hidden di modal dengan ID yang dipilih
                transaksiIdsInput.value = checkedIds.join(',');
                toggleModal();
            } else {
                alert('Pilih setidaknya satu transaksi untuk dikirim.');
            }
        });

        // Event listeners untuk menutup modal
        closeModalBtn.addEventListener('click', toggleModal);
        btnCancel.addEventListener('click', toggleModal);
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                toggleModal();
            }
        });
    });
</script>

{{-- Animasi modal --}}
<style>
    @keyframes scaleIn {
        from {
            transform: scale(0.9);
            opacity: 0;
        }

        to {
            transform: scale(1);
            opacity: 1;
        }
    }

    .animate-scaleIn {
        animation: scaleIn 0.2s ease-out;
    }
</style>
@endsection

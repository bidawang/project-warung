@extends('layouts.admin')

@section('title', 'Tambah Asset')

@section('content')

<div class="max-w-2xl mx-auto">

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-6">

        <h2 class="text-xl font-bold text-gray-800">Tambah Asset</h2>

        <form method="POST" action="{{ route('admin.asset.store') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Warung</label>
                <select name="id_warung"
                    class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                    required>

                    <option value="">-- Pilih Warung --</option>

                    @foreach($warungs as $w)
                        <option value="{{ $w->id }}">{{ $w->nama_warung }}</option>
                    @endforeach

                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Asset</label>
                <input type="text" name="nama"
                    class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                    required>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Harga Asset</label>
                <input type="number" step="0.01" name="harga_asset"
                    class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                    required>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Pembelian</label>
                <input type="date" name="tanggal_pembelian"
                    class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                    required>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Total Dibayar</label>
                <input type="number" step="0.01" name="total_dibayar"
                    class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Sisa Pembayaran</label>
                <input type="number" step="0.01" name="sisa_pembayaran"
                    class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Keterangan</label>
                <textarea name="keterangan"
                    class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500"></textarea>
            </div>

            <div class="flex justify-end gap-3 pt-4">

                <a href="{{ route('admin.asset.index') }}"
                   class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition">
                    Batal
                </a>

                <button
                    class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Simpan
                </button>

            </div>

        </form>

    </div>

</div>

@endsection
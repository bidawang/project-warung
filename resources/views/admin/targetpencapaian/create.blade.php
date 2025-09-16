@extends('layouts.admin')

@section('title', 'Tambah Target Pencapaian')

@section('content')
    <div class="flex-1 flex flex-col overflow-hidden">
        {{-- Header --}}
        <header class="flex justify-between items-center p-6 bg-white border-b-2 border-gray-200">
            <button id="openSidebarBtn" class="text-gray-500 hover:text-gray-900 lg:hidden">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Tambah Target Pencapaian</h1>
            </div>
            <div class="flex items-center">
                <span class="mr-4 font-semibold hidden sm:inline">Admin User</span>
                <div class="w-10 h-10 bg-blue-500 rounded-full"></div>
            </div>
        </header>

        {{-- Main --}}
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6 md:p-10">
            <div class="container mx-auto max-w-3xl">
                <div class="bg-white shadow-md rounded-lg p-6">
                    <form action="{{ route('admin.targetpencapaian.store') }}" method="POST" class="space-y-6">
                        @csrf

                        {{-- Warung --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Warung</label>
                            <select name="id_warung"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                required>
                                <option value="">-- Pilih Warung --</option>
                                @foreach($warung as $w)
                                    <option value="{{ $w->id }}">{{ $w->nama_warung }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Periode Awal --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Periode Awal</label>
                            <input type="date" name="periode_awal"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                required>
                        </div>

                        {{-- Periode Akhir --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Periode Akhir</label>
                            <input type="date" name="periode_akhir"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                required>
                        </div>

                        {{-- Target Pencapaian --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Target Pencapaian</label>
                            <input type="number" name="target_pencapaian"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Masukkan nilai target"
                                required>
                        </div>

                        {{-- Status --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status_pencapaian"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                required>
                                <option value="belum tercapai">Belum Tercapai</option>
                                <option value="tercapai">Tercapai</option>
                            </select>
                        </div>

                        {{-- Keterangan --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                            <textarea name="keterangan" rows="3"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Opsional"></textarea>
                        </div>

                        {{-- Tombol --}}
                        <div class="flex justify-end space-x-4">
                            <a href="{{ route('admin.targetpencapaian.index') }}"
                                class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition-colors">
                                Batal
                            </a>
                            <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
@endsection

@extends('layouts.admin')

@section('title', 'Setting Warung')

@section('content')

    <div class="p-4 bg-gray-50 min-h-screen pb-28">

        <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">

            <div>
                <h1 class="text-xl font-bold text-gray-800">Setting Warung</h1>
                <p class="text-sm text-gray-500">{{ $warung->nama_warung }}</p>
            </div>

            <a href="{{ route('admin.warung.index') }}"
                class="bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 font-bold py-2 px-6 rounded-full shadow-sm">
                Kembali
            </a>

        </div>

        <form action="" method="POST">
            @csrf

            <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm">

                {{-- USER --}}
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Pemilik</label>

                    <select name="id_user" class="w-full border-gray-300 rounded-lg p-2">

                        <option value="">Pilih User</option>

                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" {{ $warung->id_user == $user->id ? 'selected' : '' }}>

                                {{ $user->name }}

                            </option>
                        @endforeach

                    </select>
                </div>


                {{-- AREA --}}
                <div class="mb-4">

                    <label class="block text-sm font-bold text-gray-700 mb-2">Area</label>

                    <select name="id_area" class="w-full border-gray-300 rounded-lg p-2">

                        <option value="">Pilih Area</option>

                        @foreach ($areas as $area)
                            <option value="{{ $area->id }}" {{ $warung->id_area == $area->id ? 'selected' : '' }}>

                                {{ $area->area }}

                            </option>
                        @endforeach

                    </select>

                </div>


                {{-- NAMA --}}
                <div class="mb-4">

                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Nama Warung
                    </label>

                    <input type="text" name="nama_warung" value="{{ $warung->nama_warung }}"
                        class="w-full border-gray-300 rounded-lg p-2">

                </div>


                {{-- MODAL --}}
                <div class="mb-4">

                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Modal
                    </label>

                    <input type="number" name="modal" value="{{ $warung->modal }}"
                        class="w-full border-gray-300 rounded-lg p-2">

                </div>


                {{-- KETERANGAN --}}
                <div class="mb-4">

                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Keterangan
                    </label>

                    <textarea name="keterangan" class="w-full border-gray-300 rounded-lg p-2">{{ $warung->keterangan }}</textarea>

                </div>

            </div>


            {{-- DAFTAR BARANG --}}
            <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm mt-6">

                <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">

                    <span class="w-2 h-6 bg-blue-600 rounded-full mr-2"></span>

                    Stok Awal Barang

                </h2>


                <div id="barangList">

                    @include('admin.warung.partials.barang-list', [
                        'barangs' => $barangs,
                    ])

                </div>

            </div>


            {{-- FLOAT BUTTON --}}
            <div
                class="fixed bottom-0 left-0 right-0 md:left-64 bg-white border-t border-gray-200 p-4 flex justify-end shadow">

                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-10 rounded-full shadow-lg">

                    Simpan Setting

                </button>

            </div>

        </form>

    </div>

@endsection

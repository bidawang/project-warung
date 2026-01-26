@extends('layouts.admin')

@section('title', 'Tambah Harga Pulsa')

@section('content')

    {{-- Main Content --}}
    <div class="flex-1 flex flex-col overflow-hidden">

        {{-- Main Content --}}
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6 md:p-10">
            <div class="container mx-auto max-w-3xl">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-3xl font-bold text-gray-800">Tambah Data Harga Pulsa</h2>
                    <a href="{{ route('admin.harga-pulsa.index') }}"
                        class="text-blue-600 hover:text-blue-800 transition-colors duration-200 flex items-center">
                        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali ke Daftar
                    </a>
                </div>

                {{-- Card Form --}}
                <div class="bg-white shadow-xl rounded-lg p-6 md:p-8">
                    {{-- Form Tambah Harga Pulsa --}}
                    {{-- Notifikasi Error --}}
                    @if ($errors->any())
                        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                            <strong class="font-bold">Terjadi kesalahan!</strong>
                            <ul class="mt-2 list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Notifikasi Sukses --}}
                    @if (session('success'))
                        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('admin.harga-pulsa.store') }}" method="POST">
                        @csrf

                        {{-- Input Jenis Pulsa --}}
                        <div class="mb-5">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Jenis Pulsa
                            </label>

                            <div class="flex gap-2">
                                <select name="jenis_pulsa_id"
                                    class="flex-1 border border-gray-300 rounded-lg py-2 px-4 focus:ring-2 focus:ring-blue-500"
                                    required>
                                    <option value="">-- Pilih Jenis Pulsa --</option>
                                    @foreach ($jenisPulsa as $item)
                                        <option value="{{ $item->id }}"
                                            {{ old('jenis_pulsa_id') == $item->id ? 'selected' : '' }}>
                                            {{ $item->nama_jenis }}
                                        </option>
                                    @endforeach
                                </select>

                                <button type="button" onclick="toggleJenisPulsa()"
                                    class="bg-green-600 hover:bg-green-700 text-white px-4 rounded-lg">
                                    +
                                </button>
                            </div>

                            @error('jenis_pulsa_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Tambah Jenis Pulsa --}}
                        <div id="formJenisPulsa" class="mb-5 hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Tambah Jenis Pulsa Baru
                            </label>

                            <div class="flex gap-2">
                                <input type="text" name="jenis_pulsa_baru"
                                    class="flex-1 border border-gray-300 rounded-lg py-2 px-4"
                                    placeholder="Contoh: Voucher Game">

                                <button type="button" onclick="submitJenisPulsa()"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 rounded-lg">
                                    Simpan
                                </button>
                            </div>
                        </div>

                        {{-- Input Jumlah Pulsa --}}
                        <div class="mb-5">
                            <label for="jumlah_pulsa" class="block text-sm font-medium text-gray-700 mb-2">Jumlah Pulsa
                                (contoh: 10000)</label>
                            <input type="number" name="jumlah_pulsa" id="jumlah_pulsa" value="{{ old('jumlah_pulsa') }}"
                                class="w-full border border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('jumlah_pulsa') border-red-500 @enderror"
                                placeholder="Masukkan nominal pulsa (tanpa titik atau koma)" required min="1">
                            @error('jumlah_pulsa')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Input Harga Jual --}}
                        <div class="mb-5">
                            <label for="harga" class="block text-sm font-medium text-gray-700 mb-2">Harga Jual (contoh:
                                12000)</label>
                            <input type="number" name="harga" id="harga" value="{{ old('harga') }}"
                                class="w-full border border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('harga') border-red-500 @enderror"
                                placeholder="Masukkan harga jual pulsa (tanpa titik atau koma)" required min="1">
                            @error('harga')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Tombol Submit --}}
                        <div class="flex justify-end mt-6">
                            <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition-colors duration-200 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                Simpan Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
    <script>
        function toggleJenisPulsa() {
            document.getElementById('formJenisPulsa').classList.toggle('hidden');
        }

        function submitJenisPulsa() {
            const input = document.querySelector('input[name="jenis_pulsa_baru"]');
            if (!input.value.trim()) {
                alert('Nama jenis pulsa tidak boleh kosong');
                return;
            }

            fetch("{{ route('admin.jenis-pulsa.store') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        nama_jenis: input.value
                    })
                })
                .then(res => res.json())
                .then(() => location.reload())
                .catch(() => alert('Gagal menambah jenis pulsa'));
        }
    </script>

@endsection

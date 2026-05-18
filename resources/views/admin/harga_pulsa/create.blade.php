@extends('layouts.admin')

@section('title', 'Tambah Harga Pulsa')

@section('content')

    {{-- Main Content Container --}}
    <div class="flex-1 flex flex-col overflow-hidden bg-gray-50">

        <main class="flex-1 overflow-x-hidden overflow-y-auto p-4 md:p-8">
            <div class="container mx-auto max-w-2xl">
                
                {{-- Header Section --}}
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-8">
                    <div>
                        <h2 class="text-2xl font-extrabold text-gray-900 tracking-tight">Tambah Harga Pulsa</h2>
                        <p class="text-sm text-gray-500 mt-1">Konfigurasi nominal dan penentuan skema harga jual kasir.</p>
                    </div>
                    <a href="{{ route('admin.harga-pulsa.index') }}"
                        class="inline-flex items-center text-sm font-medium text-gray-600 hover:text-blue-600 bg-white hover:bg-gray-100 border border-gray-200 shadow-sm rounded-lg px-4 py-2 transition-all duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali
                    </a>
                </div>

                {{-- Alert System --}}
                @if ($errors->any())
                    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-xl shadow-sm">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-semibold text-red-800">Periksa kembali isian formulir:</h3>
                                <ul class="mt-1 list-disc list-inside text-sm text-red-700 space-y-0.5">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                @if (session('success'))
                    <div class="mb-6 bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-xl shadow-sm flex items-center">
                        <svg class="h-5 w-5 text-emerald-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="text-sm font-medium text-emerald-800">{{ session('success') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    {{-- Perbaikan Bug Panggilan: session('success') diganti menjadi session('error') --}}
                    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-xl shadow-sm flex items-center">
                        <svg class="h-5 w-5 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-sm font-medium text-red-800">{{ session('error') }}</span>
                    </div>
                @endif

                {{-- Form Card --}}
                <div class="bg-white border border-gray-100 shadow-xl rounded-2xl p-6 md:p-8">
                    <form action="{{ route('admin.harga-pulsa.store') }}" method="POST" class="space-y-6">
                        @csrf

                        {{-- Input Jenis Pulsa --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                                Jenis Layanan / Provider
                            </label>
                            <div class="flex gap-2">
                                <div class="relative flex-1">
                                    <select name="jenis_pulsa_id"
                                        class="w-full bg-gray-50 border border-gray-300 rounded-xl py-2.5 pl-4 pr-10 focus:ring-4 focus:ring-blue-100 focus:border-blue-500 text-gray-800 transition-all appearance-none cursor-pointer @error('jenis_pulsa_id') border-red-400 focus:ring-red-500 @enderror"
                                        required>
                                        <option value="">-- Pilih Jenis Pulsa --</option>
                                        @foreach ($jenisPulsa as $item)
                                            <option value="{{ $item->id }}" {{ old('jenis_pulsa_id') == $item->id ? 'selected' : '' }}>
                                                {{ $item->nama_jenis }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </div>
                                </div>
                                <button type="button" onclick="toggleJenisPulsa()" id="btnToggleJenis"
                                    class="bg-gray-800 hover:bg-gray-900 text-white font-medium px-4 rounded-xl transition-colors flex items-center justify-center shadow-sm"
                                    title="Tambah Jenis Baru">
                                    <svg class="w-5 h-5 transition-transform duration-200" id="iconToggle" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                                </button>
                            </div>
                            @error('jenis_pulsa_id')
                                <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Section Form Inline Tambah Jenis Pulsa (Hidden/Show) --}}
                        <div id="formJenisPulsa" class="hidden bg-gray-50 border border-gray-200 rounded-xl p-4 space-y-2 transition-all">
                            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide">
                                Nama Jenis Pulsa Baru
                            </label>
                            <div class="flex gap-2">
                                <input type="text" name="jenis_pulsa_baru"
                                    class="flex-1 bg-white border border-gray-300 rounded-lg py-2 px-3.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                    placeholder="Contoh: Voucher Game, PLN Pascabayar">
                                <button type="button" onclick="submitJenisPulsa()"
                                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm px-4 rounded-lg shadow-sm transition-colors">
                                    Simpan
                                </button>
                            </div>
                        </div>

                        <hr class="border-gray-100">

                        {{-- Input Jumlah Pulsa --}}
                        <div>
                            <label for="jumlah_pulsa" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                                Nominal / Jumlah Pulsa
                            </label>
                            <div class="relative">
                                <input type="number" name="jumlah_pulsa" id="jumlah_pulsa" value="{{ old('jumlah_pulsa') }}"
                                    class="w-full bg-gray-50 border border-gray-300 rounded-xl py-2.5 px-4 focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all @error('jumlah_pulsa') border-red-400 focus:ring-red-100 @enderror"
                                    placeholder="Contoh: 10000" required min="1">
                            </div>
                            @error('jumlah_pulsa')
                                <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Baris Struktur Harga (Grid Responsif) --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            
                            {{-- Harga Alomogada --}}
                            <div>
                                <label for="harga_alomogada" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                                    Harga Alomogada
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400 text-sm font-semibold">Rp</span>
                                    <input type="number" name="harga_alomogada" id="harga_alomogada" value="{{ old('harga_alomogada') }}"
                                        class="w-full bg-gray-50 border border-gray-300 rounded-xl py-2.5 pl-10 pr-4 focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all @error('harga_alomogada') border-red-400 @enderror"
                                        placeholder="0" required>
                                </div>
                                @error('harga_alomogada')
                                    <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Harga Modal --}}
                            <div>
                                <label for="harga_modal" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                                    Harga Modal Toko
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400 text-sm font-semibold">Rp</span>
                                    <input type="number" name="harga_modal" id="harga_modal" value="{{ old('harga_modal') }}"
                                        class="w-full bg-gray-50 border border-gray-300 rounded-xl py-2.5 pl-10 pr-4 focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all @error('harga_modal') border-red-400 @enderror"
                                        placeholder="0" required>
                                </div>
                                @error('harga_modal')
                                    <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Harga Jual Tunai --}}
                            <div>
                                <label for="harga_jual" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                                    Harga Jual Tunai (Cash)
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-blue-500 text-sm font-bold">Rp</span>
                                    <input type="number" name="harga_jual" id="harga_jual" value="{{ old('harga_jual') }}"
                                        class="w-full bg-blue-50/30 border border-blue-200 rounded-xl py-2.5 pl-10 pr-4 font-semibold text-blue-900 focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all @error('harga_jual') border-red-400 @enderror"
                                        placeholder="0" required>
                                </div>
                                @error('harga_jual')
                                    <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Harga Hutang --}}
                            <div>
                                <label for="harga_hutang" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                                    Harga Jual Hutang (Member)
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-amber-600 text-sm font-bold">Rp</span>
                                    <input type="number" name="harga_hutang" id="harga_hutang" value="{{ old('harga_hutang') }}"
                                        class="w-full bg-amber-50/30 border border-amber-200 rounded-xl py-2.5 pl-10 pr-4 font-semibold text-amber-900 focus:ring-4 focus:ring-amber-100 focus:border-amber-500 transition-all @error('harga_hutang') border-red-400 @enderror"
                                        placeholder="0" required>
                                </div>
                                @error('harga_hutang')
                                    <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>

                        {{-- Action Button --}}
                        <div class="flex justify-end pt-4">
                            <button type="submit"
                                class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-8 rounded-xl shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Simpan Konfigurasi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script>
        function toggleJenisPulsa() {
            const form = document.getElementById('formJenisPulsa');
            const icon = document.getElementById('iconToggle');
            const btn = document.getElementById('btnToggleJenis');
            
            form.classList.toggle('hidden');
            
            // Animasi rotasi tombol saat form dibuka
            if(form.classList.contains('hidden')) {
                icon.style.transform = 'rotate(0deg)';
                btn.className = "bg-gray-800 hover:bg-gray-900 text-white font-medium px-4 rounded-xl transition-colors flex items-center justify-center shadow-sm";
            } else {
                icon.style.transform = 'rotate(45deg)';
                btn.className = "bg-red-500 hover:bg-red-600 text-white font-medium px-4 rounded-xl transition-colors flex items-center justify-center shadow-sm";
            }
        }

        function submitJenisPulsa() {
            const input = document.querySelector('input[name="jenis_pulsa_baru"]');

            if (!input.value.trim()) {
                alert('Nama jenis pulsa tidak boleh kosong');
                return;
            }

            console.log('Mengirim data:', input.value);

            fetch("{{ route('admin.jenis-pulsa.store') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        nama_jenis: input.value
                    })
                })
                .then(async res => {
                    console.log('HTTP Status:', res.status);

                    if (!res.ok) {
                        const errorData = await res.json();
                        console.error('Detail Kesalahan Server:', errorData);
                        throw new Error(errorData.message || 'Terjadi kesalahan pada server');
                    }
                    return res.json();
                })
                .then(data => {
                    console.log('Berhasil:', data);
                    location.reload();
                })
                .catch(error => {
                    console.error('Fetch Error:', error);
                    alert('Gagal menambah jenis pulsa: ' + error.message);
                });
        }
    </script>

@endsection
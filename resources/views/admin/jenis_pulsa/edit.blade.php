@extends('layouts.admin')

@section('title', 'Edit Jenis Pulsa')

@section('content')
<div class="flex-1 flex flex-col overflow-hidden">
    <main class="flex-1 overflow-y-auto bg-gray-100 p-6 md:p-10">
        <div class="container mx-auto max-w-xl">

            <h2 class="text-3xl font-bold text-gray-800 mb-6">
                Edit Jenis Pulsa
            </h2>

            {{-- Error --}}
            @if ($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white shadow rounded-lg p-6">
                <form action="{{ route('admin.jenis-pulsa.update', $jenisPulsa->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Jenis Pulsa
                        </label>
                        <input type="text" name="nama_jenis"
                               value="{{ old('nama_jenis', $jenisPulsa->nama_jenis) }}"
                               class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500"
                               required>
                    </div>

                    <div class="flex justify-end gap-2">
                        <a href="{{ route('admin.jenis-pulsa.index') }}"
                           class="px-4 py-2 rounded-lg border">
                            Batal
                        </a>
                        <button class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg">
                            Update
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </main>
</div>
@endsection

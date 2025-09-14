@extends('layouts.admin')

@section('title', 'Edit Area')

@section('content')
<div class="p-6">
    <h1 class="text-xl font-bold mb-4">Edit Area Pembelian</h1>

    <form action="{{ route('admin.areapembelian.update', $areapembelian->id) }}" method="POST" class="space-y-4">
        @csrf @method('PUT')

        <div>
            <label class="block font-semibold">Nama Area</label>
            <input type="text" name="area" value="{{ old('area', $areapembelian->area) }}" class="w-full border rounded p-2" required>
            @error('area') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block font-semibold">Markup (%)</label>
            <input type="number" step="0.01" name="markup" value="{{ old('markup', $areapembelian->markup) }}" class="w-full border rounded p-2" required>
            @error('markup') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block font-semibold">Keterangan</label>
            <textarea name="keterangan" class="w-full border rounded p-2">{{ old('keterangan', $areapembelian->keterangan) }}</textarea>
            @error('keterangan') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <div class="flex space-x-3">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Update</button>
            <a href="{{ route('admin.areapembelian.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded">Batal</a>
        </div>
    </form>
</div>
@endsection

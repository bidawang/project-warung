@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Import Laba</h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('laba.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="file" class="form-label">Upload CSV (modal,harga_jual)</label>
            <input type="file" class="form-control" name="file" required>
        </div>
        <button class="btn btn-primary">Import</button>
    </form>
</div>
@endsection

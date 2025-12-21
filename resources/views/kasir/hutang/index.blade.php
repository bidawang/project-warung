@extends('layouts.app')

@section('title', 'Data Hutang')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-warning text-white">
            <h5 class="mb-0">Daftar Hutang Pelanggan</h5>
        </div>
        <div class="card-body">
            <form method="GET" class="d-flex justify-content-between align-items-center mb-3">
                <div class="input-group" style="max-width: 300px;">
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nama pelanggan...">
                    <button class="btn btn-outline-secondary" type="submit">Cari</button>
                </div>
                <div class="btn-group" role="group">
                    <a href="{{ route('kasir.hutang.index') }}"
                       class="btn btn-outline-secondary {{ request('status') == null ? 'active' : '' }}">
                       Semua
                    </a>
                    <a href="{{ route('kasir.hutang.index', ['status' => 'belum_lunas']) }}"
                       class="btn btn-outline-secondary {{ request('status') == 'belum_lunas' ? 'active' : '' }}">
                       Belum Lunas
                    </a>
                    <a href="{{ route('kasir.hutang.index', ['status' => 'lunas']) }}"
                       class="btn btn-outline-secondary {{ request('status') == 'lunas' ? 'active' : '' }}">
                       Lunas
                    </a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Pelanggan</th>
                            <th>Jumlah Hutang</th>
                            <th>Tanggal Jatuh Tempo</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($hutangList as $index => $hutang)
                            <tr>
                                <td>{{ $loop->iteration + ($hutangList->firstItem() - 1) }}</td>
                                <td>{{ $hutang->user->name ?? '-' }}</td>
                                <td>Rp {{ number_format($hutang->jumlah_hutang_awal, 0, ',', '.') }}</td>
                                <td>{{ \Carbon\Carbon::parse($hutang->tenggat)->format('Y-m-d') }}</td>
                                <td>
                                    @if($hutang->status == 'belum_lunas')
                                        <span class="badge bg-danger">Belum Lunas</span>
                                    @else
                                        <span class="badge bg-success">Lunas</span>
                                    @endif
                                </td>
                                <td>
                                    {{-- " class="btn btn-sm btn-info text-white">Detail</a>
                                    @if($hutang->status == 'belum lunas')
                                        <a href="{{ route('kasir.hutang.bayar', $hutang->id) }}" class="btn btn-sm btn-success">Bayar</a>
                                    @endif --}}
                                    <a type="button" href="{{ url('/kasir/hutang/detail/' . $hutang->id ) }}" class="btn btn-sm btn-info text-white" >Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada data hutang</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $hutangList->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection

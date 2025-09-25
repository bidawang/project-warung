@extends('layouts.app')

@section('title', 'Data Hutang Barang Masuk')

@section('content')

<div class="container py-4">
<div class="card shadow-lg border-0 rounded-4">
<div class="card-header bg-warning text-white rounded-top-4 py-3">
<h5 class="mb-0 fw-bold"><i class="fas fa-hand-holding-usd me-2"></i>Daftar Hutang Barang Masuk</h5>
</div>
<div class="card-body p-4">
<form method="GET" class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
<div class="input-group" style="max-width: 350px;">
<input type="text" name="q" value="{{ request('q') }}" class="form-control rounded-start-pill" placeholder="Cari nama pelanggan...">
<button class="btn btn-warning text-white rounded-end-pill" type="submit"><i class="fas fa-search"></i></button>
</div>
<div class="btn-group" role="group">
<a href="{{ url()->current() . '?' . http_build_query(request()->except(['status'])) }}"
class="btn btn-outline-warning {{ request('status') == null ? 'active' : '' }}">
Semua
</a>
<a href="{{ url()->current() . '?' . http_build_query(array_merge(request()->except(['status']), ['status' => 'belum lunas'])) }}"
class="btn btn-outline-warning {{ request('status') == 'belum lunas' ? 'active' : '' }}">
Belum Lunas
</a>
<a href="{{ url()->current() . '?' . http_build_query(array_merge(request()->except(['status']), ['status' => 'lunas'])) }}"
class="btn btn-outline-warning {{ request('status') == 'lunas' ? 'active' : '' }}">
Lunas
</a>
</div>
</form>

        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead class="bg-light">
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">Jumlah Hutang</th>
                        <th scope="col">Tanggal Barang Masuk</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($hutangList as $index => $hutang)
                        <tr class="align-middle">
                            <td>{{ $loop->iteration + ($hutangList->firstItem() - 1) }}</td>
                            <td>Rp {{ number_format($hutang->total, 0, ',', '.') }}</td>
                            <td>{{ \Carbon\Carbon::parse($hutang->created_at)->format('d-m-Y') }}</td>
                            <td>
                                <a href="{{ url('/kasir/hutang/detail/' . $hutang->id ) }}" class="btn btn-sm btn-info text-white rounded-pill px-3"><i class="fas fa-info-circle me-1"></i>Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Tidak ada data hutang barang masuk.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $hutangList->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

</div>
@endsection

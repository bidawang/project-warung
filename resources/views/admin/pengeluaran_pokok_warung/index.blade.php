@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Pengeluaran Pokok Warung</h4>
            <small class="text-muted">Manajemen pengeluaran seluruh warung</small>
        </div>
        <a href="{{ route('admin.pengeluaran-pokok-warung.create') }}"
           class="btn btn-primary">
            + Tambah Pengeluaran
        </a>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">

            <form method="GET" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <select name="warung" class="form-select">
                        <option value="">Semua Warung</option>
                        @foreach($warungs as $w)
                            <option value="{{ $w->id }}"
                                {{ request('warung') == $w->id ? 'selected' : '' }}>
                                {{ $w->nama_warung }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <button class="btn btn-dark">Filter</button>
                </div>
            </form>

        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Tanggal</th>
                        <th>Warung</th>
                        <th>Redaksi</th>
                        <th class="text-end">Jumlah</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $item)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($item->date)->format('d M Y') }}</td>
                            <td>{{ $item->warung->nama_warung ?? '-' }}</td>
                            <td>{{ $item->redaksi }}</td>
                            <td class="text-end">
                                Rp {{ number_format($item->jumlah,0,',','.') }}
                            </td>
                            <td class="text-center">
                                <span class="badge 
                                    {{ $item->status == 'belum terpenuhi' ? 'bg-warning text-dark' : 'bg-success' }}">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>
                            <td class="text-center">

                                <a href="{{ route('admin.pengeluaran-pokok-warung.show',$item) }}"
                                   class="btn btn-sm btn-outline-info">Detail</a>

                                @if($item->status == 'belum terpenuhi')
                                    <a href="{{ route('admin.pengeluaran-pokok-warung.edit',$item) }}"
                                       class="btn btn-sm btn-outline-primary">Edit</a>

                                    <form action="{{ route('admin.pengeluaran-pokok-warung.destroy',$item) }}"
                                          method="POST"
                                          class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button onclick="return confirm('Yakin hapus data ini?')"
                                                class="btn btn-sm btn-outline-danger">
                                            Hapus
                                        </button>
                                    </form>
                                @endif

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                Belum ada data.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer bg-white">
            {{ $data->withQueryString()->links() }}
        </div>
    </div>

</div>
@endsection
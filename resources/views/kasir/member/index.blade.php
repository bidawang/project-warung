@extends('layouts.app')

@section('title', 'Manajemen Member')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h3 class="fw-bold text-dark mb-1">Daftar Member</h3>
                            <p class="text-muted mb-0">Kelola data pelanggan setia dan program loyalitas warung.</p>
                        </div>
                        <div class="col-md-6 text-md-end mt-3 mt-md-0">
                            <a href="{{ route('kasir.member.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
                                <i class="fas fa-user-plus me-2"></i>Tambah Member Baru
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-0">
                    <div class="p-4 border-bottom">
                        <form method="GET" action="{{ url()->current() }}" class="row g-3">
                            <div class="col-md-4">
                                <div class="input-group bg-light rounded-pill px-3 py-1 border">
                                    <span class="input-group-text bg-transparent border-0 text-muted">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <input type="text" name="search" class="form-control bg-transparent border-0 ps-2"
                                           placeholder="Cari nama, email, atau No. HP..." value="{{ request('search') }}">
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr class="text-muted small fw-bold text-uppercase">
                                    <th class="ps-4 border-0">#</th>
                                    <th class="border-0">Member</th>
                                    <th class="border-0">Kontak</th>
                                    <th class="border-0">Keterangan</th>
                                    <th class="text-end pe-4 border-0">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($member as $item)
                                <tr>
                                    <td class="ps-4 text-muted small">{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle me-3 bg-soft-primary text-primary fw-bold">
                                                {{ strtoupper(substr($item->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark">{{ $item->name }}</div>
                                                <div class="text-muted small">ID: MBR-{{ str_pad($item->id, 4, '0', STR_PAD_LEFT) }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small"><i class="fas fa-envelope me-2 text-muted"></i>{{ $item->email }}</div>
                                        <div class="small"><i class="fas fa-phone me-2 text-muted"></i>{{ $item->nomor_hp }}</div>
                                    </td>
                                    <td>
                                        @if($item->keterangan)
                                            <span class="text-muted small">{{ Str::limit($item->keterangan, 40) }}</span>
                                        @else
                                            <span class="text-muted small italic opacity-50">Tidak ada catatan</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="btn-group shadow-sm rounded-pill overflow-hidden">
                                            <a href="{{ url('kasir/member/detail', $item->id) }}"
                                               class="btn btn-white btn-sm px-3 border-end" title="Detail">
                                                <i class="fas fa-eye text-info"></i>
                                            </a>
                                            <a href="{{ url('kasir/member/edit', $item->id) }}"
                                               class="btn btn-white btn-sm px-3 border-end" title="Edit">
                                                <i class="fas fa-edit text-warning"></i>
                                            </a>
                                            <form action="{{ url('kasir/member/delete', $item->id) }}"
                                                  method="POST" class="d-inline" onsubmit="return confirm('Hapus member ini?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-white btn-sm px-3" title="Hapus">
                                                    <i class="fas fa-trash text-danger"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <img src="https://illustrations.popsy.co/gray/manager.svg" style="width: 150px;" class="mb-3 opacity-50">
                                        <p class="text-muted">Belum ada data member yang terdaftar.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* UI Enhancements */
    .bg-soft-primary { background-color: #e7f1ff; color: #0d6efd; }

    .avatar-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
    }

    .table thead th {
        padding: 1.2rem 0.5rem;
        letter-spacing: 0.5px;
    }

    .btn-group .btn-white {
        background: #fff;
        border: none;
    }

    .btn-group .btn-white:hover {
        background: #f8f9fa;
    }

    /* Row Hover Effect */
    tbody tr { transition: all 0.2s; }
    tbody tr:hover { background-color: #fbfbfb; }

    /* Custom Scrollbar for Table */
    .table-responsive::-webkit-scrollbar { height: 6px; }
    .table-responsive::-webkit-scrollbar-thumb { background: #dee2e6; border-radius: 10px; }
</style>

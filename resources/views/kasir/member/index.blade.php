@extends('layouts.app')

@section('title', 'Manajemen Member')

@section('content')
    <div class="container-fluid py-3 py-md-4" style="background-color: #f8fafb; min-height: 100vh;">

        {{-- HEADER & ACTION --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-3 p-md-4">
                <div class="row align-items-center">
                    <div class="col-md-7">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 p-3 rounded-4 me-3 d-none d-md-block">
                                <i class="fas fa-users fs-3 text-primary"></i>
                            </div>
                            <div>
                                <h4 class="fw-bold text-dark mb-1">Daftar Member</h4>
                                <p class="text-muted small mb-0">Kelola data pelanggan setia untuk program poin & diskon.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5 text-md-end mt-3 mt-md-0">
                        <a href="{{ route('kasir.member.create') }}"
                            class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm w-100 w-md-auto">
                            <i class="fas fa-user-plus me-2"></i>Tambah Member
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- DATA TABLE CARD --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-0">
                {{-- Search Bar --}}
                <div class="p-3 p-md-4 border-bottom bg-white sticky-top shadow-sm-mobile" style="top: 0; z-index: 10;">
                    <form method="GET" action="{{ url()->current() }}">
                        <div class="input-group bg-light rounded-pill px-3 py-1 border-0">
                            <span class="input-group-text bg-transparent border-0 text-muted">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" name="search" class="form-control bg-transparent border-0 ps-2"
                                placeholder="Cari nama, email, atau No. HP..." value="{{ request('search') }}">
                            @if (request('search'))
                                <a href="{{ url()->current() }}"
                                    class="btn btn-transparent border-0 text-muted small mt-1">Clear</a>
                            @endif
                        </div>
                    </form>
                </div>

                {{-- Table --}}
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr class="text-muted small fw-bold text-uppercase"
                                style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                <th class="ps-4 py-3 border-0">Profil Member</th>
                                <th class="border-0">Kontak</th>
                                <th class="border-0 d-none d-lg-table-cell">Keterangan</th>
                                <th class="text-end pe-4 border-0">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0">
                            @forelse ($member as $item)
                                <tr>
                                    <td class="ps-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle me-3 shadow-sm text-white"
                                                style="background: linear-gradient(45deg, #0d6efd, #0dcaf0);">
                                                {{ strtoupper(substr($item->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark">{{ $item->name }}</div>
                                                <div class="badge bg-soft-secondary text-muted rounded-pill"
                                                    style="font-size: 10px;">
                                                    MBR-{{ str_pad($item->id, 4, '0', STR_PAD_LEFT) }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="small text-dark mb-1"><i class="fas fa-phone-alt me-2 text-muted"
                                                    style="width: 14px;"></i>{{ $item->nomor_hp }}</span>
                                            <span class="small text-muted"><i class="fas fa-envelope me-2 text-muted"
                                                    style="width: 14px;"></i>{{ $item->email }}</span>
                                        </div>
                                    </td>
                                    <td class="d-none d-lg-table-cell">
                                        <span class="text-muted small">
                                            {{ $item->keterangan ? Str::limit($item->keterangan, 35) : '-' }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="dropdown">
                                            <button class="btn btn-light btn-sm rounded-pill px-3 shadow-sm" type="button"
                                                data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v text-muted"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow rounded-3">
                                                <li><a class="dropdown-item py-2"
                                                        href="{{ url('kasir/member/detail', $item->id) }}"><i
                                                            class="fas fa-eye text-info me-2"></i> Detail</a></li>
                                                <li><a class="dropdown-item py-2"
                                                        href="{{ url('kasir/member/edit', $item->id) }}"><i
                                                            class="fas fa-edit text-warning me-2"></i> Edit</a></li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li>
                                                    {{-- Tombol Hapus Pemicu Modal --}}
                                                    <button type="button" class="dropdown-item py-2 text-danger"
                                                        data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                        data-id="{{ $item->id }}" data-name="{{ $item->name }}">
                                                        <i class="fas fa-trash me-2"></i> Hapus
                                                    </button>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <img src="https://illustrations.popsy.co/gray/manager.svg" style="width: 120px;"
                                            class="mb-3 opacity-50">
                                        <h6 class="text-muted fw-normal">Tidak ada member ditemukan.</h6>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Space --}}
                @if (method_exists($member, 'links'))
                    <div class="px-4 py-3 bg-light border-top">
                        {{ $member->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- MODAL KONFIRMASI HAPUS --}}
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-body p-4 text-center">
                    <div class="mb-3">
                        <i class="fas fa-exclamation-circle text-danger display-4"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Hapus Member?</h5>
                    <p class="text-muted small mb-4">
                        Anda akan menghapus <span id="memberName" class="fw-bold text-dark"></span>. Tindakan ini tidak
                        dapat dibatalkan.
                    </p>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light w-100 rounded-pill fw-bold"
                            data-bs-dismiss="modal">Batal</button>
                        <form id="deleteForm" method="POST" class="w-100">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100 rounded-pill fw-bold">Ya, Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* UI Palette */
        .bg-soft-secondary {
            background-color: #f0f2f5;
        }

        .avatar-circle {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            /* Slightly squircle for modern look */
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.1rem;
        }

        /* Table Improvements */
        .table thead th {
            border-bottom: 1px solid #edf2f9;
        }

        tbody tr {
            transition: all 0.2s ease-in-out;
        }

        tbody tr:hover {
            background-color: #f4f7ff !important;
            transform: translateY(-1px);
        }

        /* Mobile shadow for sticky search */
        @media (max-width: 767px) {
            .shadow-sm-mobile {
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05) !important;
            }

            .avatar-circle {
                width: 36px;
                height: 36px;
                border-radius: 10px;
                font-size: 0.9rem;
            }
        }

        /* Dropdown Hover */
        .dropdown-item:active {
            background-color: #0d6efd;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteModal = document.getElementById('deleteModal');
            if (deleteModal) {
                deleteModal.addEventListener('show.bs.modal', function(event) {
                    // Tombol yang memicu modal
                    const button = event.relatedTarget;

                    // Ambil data dari atribut data-bs-*
                    const id = button.getAttribute('data-id');
                    const name = button.getAttribute('data-name');

                    // Update isi modal
                    const modalNameSpan = deleteModal.querySelector('#memberName');
                    const deleteForm = deleteModal.querySelector('#deleteForm');

                    modalNameSpan.textContent = name;
                    // Update action URL form (sesuaikan dengan route delete Anda)
                    deleteForm.action = `/kasir/member/delete/${id}`;
                });
            }
        });
    </script>

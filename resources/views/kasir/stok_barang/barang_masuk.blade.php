@extends('layouts.app')

@section('title', 'Notifikasi Barang Masuk')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">Daftar Barang Masuk</h5>
        </div>
        <div class="card-body">
            {{-- Form untuk mengonfirmasi banyak barang sekaligus --}}
            <form action="{{ url('kasir/stok-barang/barang-masuk/konfirmasi') }}" method="POST">
                @csrf

                {{-- Tombol aksi di atas kanan tabel --}}
                <div class="d-flex justify-content-end mb-3">
                    <button type="submit" name="action" value="reject" class="btn btn-danger me-2">
                        <i class="fas fa-times-circle me-2"></i> Tolak
                    </button>
                    <button type="submit" name="action" value="confirm" class="btn btn-success">
                        <i class="fas fa-check-circle me-2"></i> Konfirmasi
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th scope="col">
                                    {{-- Kotak centang untuk memilih semua item (opsional, perlu JS) --}}
                                    <input type="checkbox" id="select-all">
                                </th>
                                <th scope="col">#</th>
                                <th scope="col">Nama Barang</th>
                                <th scope="col">Jumlah</th>
                                <th scope="col">Tanggal Masuk</th>
                                <th scope="col">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Contoh data barang masuk --}}
                            <tr>
                                <td>
                                    <input type="checkbox" name="item_ids[]" value="1">
                                </td>
                                <th scope="row">1</th>
                                <td>Kerupuk Udang</td>
                                <td>100 pcs</td>
                                <td>2025-09-17</td>
                                <td>Stok baru, langsung pajang</td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="checkbox" name="item_ids[]" value="2">
                                </td>
                                <th scope="row">2</th>
                                <td>Biskuit Regal</td>
                                <td>12 dus</td>
                                <td>2025-09-16</td>
                                <td>Barang titipan, jangan campur</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

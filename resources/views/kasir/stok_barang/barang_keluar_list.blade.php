<div class="card shadow-lg border-0 rounded-3 mb-4">
    <div class="card-header bg-warning text-dark py-3 rounded-top-3">
        {{-- PERUBAHAN DI SINI --}}
        <h6 class="mb-0 fw-bold"><i class="fas fa-calendar-day me-2"></i> Barang Keluar Hari Ini</h6>
    </div>
    <div class="card-body p-3">
        <ul class="list-group list-group-flush">
            @forelse($barangKeluar as $keluar)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <span class="fw-bold">{{ $keluar->stokWarung->barang->nama_barang ?? 'Barang Hilang' }}</span>
                        <br>
                        <small class="text-muted">
                            Keluar: {{ $keluar->jumlah }} unit ({{ $keluar->jenis }})
                            @if($keluar->transaksiBarangKeluar)
                                | Kas: Rp {{ number_format($keluar->transaksiBarangKeluar->transaksiKas->total, 0, ',', '.') }}
                            @endif
                        </small>
                        <br>
                        {{-- Tampilkan Status Hutang --}}
                        @if($keluar->is_hutang)
                            @php
                                $badgeColor = $keluar->status_hutang === 'Lunas' ? 'success' : 'danger';
                            @endphp
                            <span class="badge bg-{{ $badgeColor }} mt-1">
                                <i class="fas fa-money-bill-wave me-1"></i> Hutang: {{ $keluar->status_hutang }}
                            </span>
                        @endif
                    </div>
                    {{-- Waktu tetap penting untuk melihat urutan kejadian hari ini --}}
                    <small class="text-nowrap text-end text-muted">
                        {{ $keluar->created_at->format('H:i') }}
                    </small>
                </li>
            @empty
                <li class="list-group-item text-center text-muted">
                    Belum ada riwayat barang keluar hari ini.
                </li>
            @endforelse
        </ul>
        {{-- Jika Anda ingin menampilkan total barang keluar hari ini, Anda bisa menambahkannya di sini --}}
        @if($barangKeluar->count() > 0)
            <div class="text-center mt-3 pt-2 border-top">
                <p class="mb-0 fw-bold">Total Transaksi Hari Ini: {{ $barangKeluar->count() }}</p>
            </div>
        @endif
    </div>
</div>

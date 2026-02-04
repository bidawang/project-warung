<div class="table-responsive">
    <table class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Barang</th>
                <th>{{ $tipe }}</th>
                <th>Jumlah</th>
                <th>Status</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $row)
            <tr>
                <td>{{ $row->id }}</td>
                <td>{{ $row->stokWarung->barang->nama_barang ?? '-' }}</td>
                <td>{{ $row->warungTujuan->nama_warung ?? '-' }}</td>
                <td>{{ $row->jumlah }}</td>
                <td>{{ ucfirst($row->status) }}</td>
                <td>{{ $row->keterangan }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">Belum ada data.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

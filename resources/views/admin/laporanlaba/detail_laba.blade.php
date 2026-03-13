@extends('layouts.admin')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Detail Warung</h1>
                <p class="text-slate-500 text-sm">Informasi lengkap dan performa keuangan unit bisnis.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-8">

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                        <p class="text-sm font-medium text-slate-500">Modal Awal</p>
                        <p class="text-xl font-bold text-slate-900 mt-2">Rp {{ number_format($warung->modal, 0, ',', '.') }}
                        </p>
                    </div>

                    <div class="bg-emerald-50 p-6 rounded-2xl border border-emerald-100">
                        <p class="text-sm font-medium text-emerald-600">Laba Bersih</p>
                        <p class="text-xl font-bold text-emerald-700 mt-2">Rp
                            {{ number_format($warung->laba, 0, ',', '.') }}</p>
                    </div>

                    <div class="bg-rose-50 p-6 rounded-2xl border border-rose-100">
                        <p class="text-sm font-medium text-rose-600">Total Hutang</p>
                        <p class="text-xl font-bold text-rose-700 mt-2">Rp {{ number_format($warung->hutang, 0, ',', '.') }}
                        </p>
                    </div>
                </div>

                <div class="mt-10">
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                            <h3 class="font-bold text-slate-800">History Pendapatan Laba</h3>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-50">
                                        <th class="px-6 py-3 text-xs font-bold text-slate-500 uppercase">Tanggal</th>
                                        <th class="px-6 py-3 text-xs font-bold text-slate-500 uppercase">Barang</th>
                                        <th class="px-6 py-3 text-xs font-bold text-slate-500 uppercase text-center">Qty
                                        </th>
                                        <th class="px-6 py-3 text-xs font-bold text-slate-500 uppercase">Jenis</th>
                                        <th class="px-6 py-3 text-xs font-bold text-slate-500 uppercase text-right">Laba
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="history-data">
                                    @include('admin.laporanlaba._item_history')
                                </tbody>
                            </table>
                        </div>

                        <div id="ajax-load-status" class="hidden text-center py-4 bg-slate-50 text-slate-500 text-sm">
                            <div
                                class="animate-spin inline-block w-4 h-4 border-2 border-current border-t-transparent rounded-full mr-2">
                            </div>
                            Memuat data lebih banyak...
                        </div>
                    </div>
                </div>

                @push('scripts')
                    <script>
                        var page = 1;
                        var hasMoreData = true;
                        var loading = false;

                        $(window).scroll(function() {
                            // Jika scroll sudah mendekati bawah (100px dari bawah)
                            if ($(window).scrollTop() + $(window).height() >= $(document).height() - 100) {
                                if (!loading && hasMoreData) {
                                    page++;
                                    loadMoreData(page);
                                }
                            }
                        });

                        function loadMoreData(page) {
                            loading = true;
                            $('#ajax-load-status').show();

                            $.ajax({
                                    url: "?page=" + page,
                                    type: "get",
                                    beforeSend: function() {
                                        // Bisa tambah animasi
                                    }
                                })
                                .done(function(data) {
                                    if (data.trim() == "") {
                                        hasMoreData = false;
                                        $('#ajax-load-status').html("Semua data telah dimuat.");
                                        return;
                                    }
                                    $('#ajax-load-status').hide();
                                    $("#history-data").append(data); // Tambahkan data ke bawah baris terakhir
                                    loading = false;
                                })
                                .fail(function(jqXHR, ajaxOptions, thrownError) {
                                    console.log('Server tidak merespon...');
                                    loading = false;
                                });
                        }
                    </script>
                @endpush

                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                        <h3 class="font-bold text-slate-800">Catatan & Keterangan</h3>
                    </div>
                    <div class="p-6">
                        <p class="text-slate-600 leading-relaxed italic">
                            "{{ $warung->keterangan ?? 'Tidak ada catatan tambahan untuk warung ini.' }}"
                        </p>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                    <h3 class="font-bold text-slate-800 mb-4">Informasi Unit</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center pb-3 border-b border-slate-50">
                            <span class="text-sm text-slate-500">Nama Warung</span>
                            <span class="text-sm font-semibold text-slate-900">{{ $warung->nama_warung }}</span>
                        </div>
                        <div class="flex justify-between items-center pb-3 border-b border-slate-50">
                            <span class="text-sm text-slate-500">ID Area</span>
                            <span
                                class="px-2 py-1 bg-indigo-50 text-indigo-700 text-xs font-bold rounded">{{ $warung->id_area }}</span>
                        </div>
                        <div class="flex justify-between items-center pb-3 border-b border-slate-50">
                            <span class="text-sm text-slate-500">Tanggal Input</span>
                            <span class="text-sm text-slate-900">{{ $warung->created_at->format('d F Y') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-slate-500">Update Terakhir</span>
                            <span class="text-sm text-slate-900">{{ $warung->updated_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

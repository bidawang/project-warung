<div class="grid grid-cols-1 xl:grid-cols-2 gap-5 mt-4">

    {{-- ========================================================= --}}
    {{-- LEFT : MONITORING TRANSAKSI --}}
    {{-- ========================================================= --}}
    <div class="h-full">

        <div x-data="{ openRow: null }"
            class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden h-full flex flex-col">

            {{-- HEADER --}}
            <div
                class="px-5 py-5 border-b border-gray-100 bg-gradient-to-r from-indigo-50 via-white to-white flex items-center justify-between">

                <div>

                    <p class="text-[10px] uppercase tracking-[0.25em] text-indigo-400">
                        Monitoring
                    </p>

                    <h3 class="text-xl font-black text-gray-900 mt-1">
                        Transaksi
                    </h3>

                </div>

                <a href="{{ route('admin.riwayat_transaksi.index') }}"
                    class="px-4 py-2 rounded-2xl bg-indigo-600 text-white text-[11px] font-black hover:bg-indigo-700 transition shadow-lg shadow-indigo-500/20">

                    Lihat Semua

                </a>

            </div>

            {{-- CONTENT --}}
            <div class="flex-1 overflow-auto max-h-[760px] divide-y divide-gray-100">

                @forelse($riwayatTransaksi as $index => $trx)

                    {{-- ITEM --}}
                    <div class="transition">

                        {{-- TOP --}}
                        <div class="p-4 hover:bg-gray-50 cursor-pointer transition"
                            @click="openRow === {{ $index }} ? openRow = null : openRow = {{ $index }}">

                            <div class="flex items-start justify-between gap-4">

                                {{-- LEFT --}}
                                <div class="flex-1 min-w-0">

                                    <div class="flex items-center gap-2 flex-wrap">

                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-xl text-[9px] font-black uppercase tracking-wide
                                            {{ (float) $trx->total >= 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">

                                            {{ $trx->jenis_transaksi }}

                                        </span>

                                        <span class="text-[10px] text-gray-400 font-medium">

                                            {{ $trx->tanggal->translatedFormat('d M Y • H:i') }}

                                        </span>

                                    </div>

                                    <div class="mt-3">

                                        <h4 class="font-black text-gray-800 text-sm leading-relaxed">

                                            {{ $trx->deskripsi ?: 'Tanpa deskripsi transaksi' }}

                                        </h4>

                                    </div>

                                </div>

                                {{-- RIGHT --}}
                                <div class="text-right shrink-0">

                                    <div
                                        class="font-black text-base whitespace-nowrap
                                        {{ (float) $trx->total >= 0 ? 'text-green-600' : 'text-red-600' }}">

                                        Rp{{ number_format((float) $trx->total, 0, ',', '.') }}

                                    </div>

                                    <div class="mt-3">

                                        <div
                                            class="h-8 w-8 rounded-xl bg-gray-100 flex items-center justify-center ml-auto">

                                            <span
                                                :class="openRow === {{ $index }} ?
                                                    'rotate-180 text-indigo-600' :
                                                    'text-gray-400'"
                                                class="transition-all duration-300 text-[11px] font-black">

                                                ▼

                                            </span>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                        {{-- ACCORDION --}}
                        <div x-show="openRow === {{ $index }}" x-transition x-cloak class="px-4 pb-4">

                            <div class="rounded-2xl border border-gray-100 bg-gray-50/70 p-4">

                                <div class="grid md:grid-cols-2 gap-4">

                                    {{-- ITEM --}}
                                    @if (count($trx->items))
                                        <div>

                                            <div
                                                class="text-[10px] uppercase tracking-wide text-gray-400 font-black mb-3">

                                                Detail Item

                                            </div>

                                            <div class="space-y-2">

                                                @foreach ($trx->items as $item)
                                                    <div
                                                        class="bg-white border border-gray-100 rounded-2xl px-3 py-2 flex items-center justify-between">

                                                        <div>

                                                            <div class="font-bold text-gray-700 text-[11px]">

                                                                {{ $item->nama_barang }}

                                                            </div>

                                                            <div class="text-[10px] text-gray-400">

                                                                Qty x{{ $item->jumlah }}

                                                            </div>

                                                        </div>

                                                        <div class="font-black text-gray-800 text-[11px]">

                                                            {{ number_format($item->subtotal, 0, ',', '.') }}

                                                        </div>

                                                    </div>
                                                @endforeach

                                            </div>

                                        </div>
                                    @endif

                                    {{-- PEMBAYARAN --}}
                                    <div>

                                        <div class="text-[10px] uppercase tracking-wide text-gray-400 font-black mb-3">

                                            Pembayaran

                                        </div>

                                        <div class="bg-white border border-gray-100 rounded-2xl p-4 space-y-3">

                                            @if ($trx->uang_dibayar)
                                                <div class="flex items-center justify-between text-sm">

                                                    <span class="text-gray-500">
                                                        Dibayar
                                                    </span>

                                                    <span class="font-bold text-gray-800">

                                                        Rp{{ number_format($trx->uang_dibayar, 0, ',', '.') }}

                                                    </span>

                                                </div>
                                            @endif

                                            <div class="border-t pt-3 flex items-center justify-between">

                                                <span class="font-black text-gray-700">
                                                    Total
                                                </span>

                                                <span
                                                    class="font-black text-sm
                                                    {{ (float) $trx->total >= 0 ? 'text-green-600' : 'text-red-600' }}">

                                                    Rp{{ number_format((float) $trx->total, 0, ',', '.') }}

                                                </span>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                @empty

                    <div class="py-20 text-center">

                        <div class="text-5xl">
                            📭
                        </div>

                        <div class="mt-4 font-bold text-gray-500">
                            Belum ada transaksi
                        </div>

                    </div>

                @endforelse

            </div>

        </div>

    </div>

    {{-- ========================================================= --}}
    {{-- RIGHT : PENGELUARAN --}}
    {{-- ========================================================= --}}
    <div class="h-full relative" x-data="{
        openTambah: false,
        openKonfirmasi: false,
    
        pengeluaranSelected: {
            id: '',
            redaksi: '',
            jumlah: 0
        },
    
        confirmPengeluaran(item) {
            this.pengeluaranSelected = item
            this.openKonfirmasi = true
        },
    
        init() {
            window.addEventListener('open-konfirmasi-pengeluaran', (e) => {
                this.confirmPengeluaran(e.detail)
            })
        }
    }" x-init="init()">

        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden h-full flex flex-col">

            {{-- HEADER --}}
            <div
                class="px-5 py-5 border-b border-gray-100 bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 text-white">

                <div class="flex items-start justify-between">

                    <div>

                        <p class="text-[10px] uppercase tracking-[0.25em] text-gray-400">
                            Operasional
                        </p>

                        <h3 class="text-xl font-black mt-1">
                            Pengeluaran Bulanan
                        </h3>

                    </div>

                    <div
                        class="h-11 w-11 rounded-2xl bg-white/10 backdrop-blur flex items-center justify-center text-xl shadow-inner">

                        💸

                    </div>

                </div>

                {{-- SUMMARY --}}
                <div class="grid grid-cols-3 gap-2 mt-5">

                    <div class="rounded-2xl bg-white/5 border border-white/10 p-3 text-center">

                        <p class="text-[9px] uppercase tracking-wide text-gray-400">
                            Total
                        </p>

                        <h4 id="totalPengeluaran" class="text-xs font-black text-white mt-1 truncate">
                            -
                        </h4>

                    </div>

                    <div class="rounded-2xl bg-green-500/10 border border-green-400/10 p-3 text-center">

                        <p class="text-[9px] uppercase tracking-wide text-green-200">
                            Lunas
                        </p>

                        <h4 id="totalTerpenuhi" class="text-xs font-black text-green-300 mt-1 truncate">
                            -
                        </h4>

                    </div>

                    <div class="rounded-2xl bg-red-500/10 border border-red-400/10 p-3 text-center">

                        <p class="text-[9px] uppercase tracking-wide text-red-200">
                            Sisa
                        </p>

                        <h4 id="totalBelum" class="text-xs font-black text-red-300 mt-1 truncate">
                            -
                        </h4>

                    </div>

                </div>

                {{-- ACTION --}}
                <div class="mt-5">

                    <button @click.stop="openTambah = true"
                        class="w-full rounded-2xl bg-white text-gray-900 py-3 text-sm font-black hover:bg-gray-100 transition">

                        + Tambah Pengeluaran

                    </button>

                </div>

            </div>

            {{-- TABLE --}}
            <div class="flex-1 overflow-auto max-h-[760px]">

                <table class="min-w-full text-[11px]">

                    <thead class="sticky top-0 bg-white border-b border-gray-100 z-10">

                        <tr class="text-[9px] uppercase tracking-wide text-gray-400">

                            <th class="px-4 py-3 text-left font-bold">
                                Tanggal
                            </th>

                            <th class="px-4 py-3 text-left font-bold">
                                Pengeluaran
                            </th>

                            <th class="px-4 py-3 text-right font-bold">
                                Nominal
                            </th>

                            <th class="px-4 py-3 text-center font-bold w-[90px]">
                                Aksi
                            </th>

                        </tr>

                    </thead>

                    <tbody id="tablePengeluaran" class="divide-y divide-gray-50">

                        <tr>

                            <td colspan="4" class="text-center py-10 text-gray-400 italic">

                                Memproses...

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

        </div>

        {{-- ========================================================= --}}
        {{-- MODAL TAMBAH --}}
        {{-- ========================================================= --}}
        <div x-show="openTambah" x-transition x-cloak
            class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 p-4">

            <div @click.away="openTambah = false"
                class="bg-white rounded-3xl w-full max-w-lg overflow-hidden shadow-2xl">

                {{-- HEADER --}}
                <div class="px-6 py-5 border-b">

                    <div class="flex items-center justify-between">

                        <div>

                            <p class="text-xs uppercase tracking-widest text-gray-400">
                                Form Input
                            </p>

                            <h3 class="text-xl font-black text-gray-900 mt-1">
                                Tambah Pengeluaran
                            </h3>

                        </div>

                        <button @click.stop="openTambah = false"
                            class="h-10 w-10 rounded-xl hover:bg-gray-100 text-gray-500">

                            ✕

                        </button>

                    </div>

                </div>

                {{-- FORM --}}
                <form method="POST" action="{{ route('admin.pengeluaran-pokok-warung.store') }}"
                    class="p-6 space-y-5">

                    @csrf

                    <input type="hidden" name="id_warung" value="{{ $warung->id }}">

                    {{-- REDAKSI --}}
                    <div>

                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Redaksi
                        </label>

                        <input type="text" name="redaksi" required
                            class="w-full rounded-2xl border-gray-200 focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="Contoh: Bayar listrik">

                    </div>

                    {{-- JUMLAH --}}
                    <div>

                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Jumlah
                        </label>

                        <input type="number" step="0.01" name="jumlah" required
                            class="w-full rounded-2xl border-gray-200 focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="0">

                    </div>

                    {{-- TANGGAL --}}
                    <div>

                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Tanggal
                        </label>

                        <input type="date" name="date" value="{{ now()->format('Y-m-d') }}" required
                            class="w-full rounded-2xl border-gray-200 focus:ring-indigo-500 focus:border-indigo-500">

                    </div>

                    {{-- ACTION --}}
                    <div class="flex justify-end gap-3 pt-3">

                        <button type="button" @click.stop="openTambah = false"
                            class="px-5 py-2.5 rounded-2xl bg-gray-100 hover:bg-gray-200 font-semibold text-gray-700">

                            Batal

                        </button>

                        <button
                            class="px-6 py-2.5 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold shadow-lg shadow-indigo-500/20">

                            Simpan

                        </button>

                    </div>

                </form>

            </div>

        </div>

        {{-- ========================================================= --}}
        {{-- MODAL KONFIRMASI --}}
        {{-- ========================================================= --}}
        <div x-show="openKonfirmasi" x-transition x-cloak
            class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 p-4">

            <div @click.away="openKonfirmasi = false"
                class="bg-white rounded-3xl w-full max-w-md overflow-hidden shadow-2xl">

                <div class="p-6 text-center">

                    <div class="h-16 w-16 rounded-3xl bg-green-100 text-3xl flex items-center justify-center mx-auto">

                        ✅

                    </div>

                    <h3 class="text-2xl font-black text-gray-900 mt-5">

                        Penuhi Pengeluaran?

                    </h3>

                    <p class="text-sm text-gray-500 mt-2 leading-relaxed">

                        Aksi ini akan membuat transaksi kas keluar otomatis.

                    </p>

                    {{-- DETAIL --}}
                    <div class="mt-5 bg-gray-50 rounded-2xl p-4 text-left text-sm space-y-2">

                        <div class="flex justify-between">

                            <span class="text-gray-500">
                                Keterangan
                            </span>

                            <span class="font-bold" x-text="pengeluaranSelected?.redaksi">
                            </span>

                        </div>

                        <div class="flex justify-between">

                            <span class="text-gray-500">
                                Nominal
                            </span>

                            <span class="font-black text-red-600"
                                x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(pengeluaranSelected?.jumlah || 0)">
                            </span>

                        </div>

                    </div>

                    {{-- FORM --}}
                    <form method="POST" action="{{ route('admin.transaksikas.store') }}" class="mt-6">

                        @csrf

                        <input type="hidden" name="jenis" value="keluar">

                        <input type="hidden" name="id_kas_warung"
                            value="{{ $warung->kasWarung->where('jenis_kas', 'cash')->first()?->id }}">

                        <input type="hidden" name="total" :value="pengeluaranSelected.jumlah">

                        <input type="hidden" name="keterangan"
                            :value="'Pengeluaran: ' + pengeluaranSelected.redaksi">

                        <input type="hidden" name="id_pengeluaran" :value="pengeluaranSelected.id">

                        <div class="flex gap-3">

                            <button type="button" @click.stop="openKonfirmasi = false"
                                class="flex-1 py-3 rounded-2xl bg-gray-100 hover:bg-gray-200 font-semibold text-gray-700">

                                Batal

                            </button>

                            <button type="submit"
                                class="flex-1 py-3 rounded-2xl bg-green-600 hover:bg-green-700 text-white font-black shadow-lg shadow-green-500/20">

                                Ya, Penuhi

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

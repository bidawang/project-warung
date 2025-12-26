<?php

use App\Http\Controllers\{
    KategoriController,
    SubKategoriController,
    BarangController,
    AturanTenggatController,
    UserController,
    WarungController,
    AreaController,
    KasWarungController,
    DetailKasWarungController,
    TransaksiKasController,
    DetailTransaksiController,
    StokWarungController,
    BarangMasukController,
    HutangController,
    BungaController,
    PembayaranHutangController,
    BarangHutangController,
    TargetPencapaianController,
    StokBarangController,
    AuthController,
    LabaController
};

use App\Http\Controllers\Admin\{
    DashboardControllerAdmin,
    UserControllerAdmin,
    AreaControllerAdmin,
    BarangControllerAdmin,
    SubKategoriControllerAdmin,
    KategoriControllerAdmin,
    AreaPembelianController,
    TransaksiBarangController,
    AturanTenggatControllerAdmin,
    HargaJualControllerAdmin,
    LabaControllerAdmin,
    KuantitasControllerAdmin,
    StokOpnameControllerAdmin,
    HargaPulsaControllerAdmin,
    SaldoPulsaControllerAdmin,
    RiwayatTransaksiControllerAdmin,
    HutangControllerAdmin,
    AsalBarangControllerAdmin,
    RencanaBelanjaControllerAdmin,
    SatuanBarangControllerAdmin,
    SatuanControllerAdmin
};

use App\Http\Controllers\Kasir\{
    BarangKeluarController,
    BarangMasukControllerKasir,
    KuantitasController,
    HutangControllerKasir,
    HutangBarangMasukControllerKasir,
    RencanaBelanjaControllerKasir,
    KasControllerKasir,
    KasirControllerAdmin,
    KasirControllerKasir,
    MutasiBarangController,
    MutasiBarangControllerKasir,
    ProfilControllerKasir,
    StokBarangControllerAdmin,
    StokBarangControllerKasir,
    MemberControllerKasir,
    PulsaControllerKasir,
    RiwayatTransaksiControllerKasir
};

use Illuminate\Support\Facades\{Route, Password};
use Illuminate\Http\Request;


// Route::get('/', function () {
//     return view('welcome');
// })->name('home');
Route::get('/testailwind', function () {
    return view('testailwind');
});

Route::get('/', [AuthController::class, 'showLogin']);
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('password.request');

Route::post('/forgot-password', function (Request $request) {
    $request->validate(['email' => 'required|email']);

    $status = Password::sendResetLink(
        $request->only('email')
    );

    return $status === Password::RESET_LINK_SENT
        ? back()->with(['status' => __($status)])
        : back()->withErrors(['email' => __($status)]);
})->name('password.email');


Route::prefix('/admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [DashboardControllerAdmin::class, 'index'])->name('dashboard');
    Route::get('/user', [UserControllerAdmin::class, 'index'])->name('user.index');

    // Ganti route area manual menjadi resource
    Route::resource('/area', AreaControllerAdmin::class);

    Route::resource('/kuantitas', KuantitasControllerAdmin::class);
    Route::resource('/stokopname', StokOpnameControllerAdmin::class);

    Route::resource('/barang', BarangControllerAdmin::class);
    Route::resource('transaksibarang', TransaksiBarangController::class)->except(['show']);

    Route::resource('rencana', RencanaBelanjaControllerAdmin::class)->except(['show']);

    Route::get('rencana/pembelian-per-area', [RencanaBelanjaControllerAdmin::class, 'createByArea'])->name('rencana.pembelian_per_area');

    Route::get('transaksibarang/rencana', [RencanaBelanjaControllerAdmin::class, 'indexRencana'])->name('transaksibarang.rencana.index');
    Route::get('transaksi-barang/rencana/create', [RencanaBelanjaControllerAdmin::class, 'createRencana'])->name('createrencanabelanja'); // Menampilkan form
    Route::post('/store/rencana', [RencanaBelanjaControllerAdmin::class, 'storeRencana'])->name('store.rencana');


    Route::post('transaksibarang/update-status-massal', [TransaksiBarangController::class, 'updateStatusMassal'])->name('transaksibarang.updateStatusMassal');
    Route::post('/kirim-massal-proses', [TransaksiBarangController::class, 'kirimMassalProses'])->name('transaksibarang.kirim.mass.proses');
    Route::post('/kirim-rencana-proses', [RencanaBelanjaControllerAdmin::class, 'kirimRencanaProses'])->name('transaksibarang.kirim.rencana.proses');
    Route::resource('kategori', KategoriControllerAdmin::class);
    Route::resource('areapembelian', AreaPembelianController::class)->names('areapembelian');
    Route::resource('targetpencapaian', TargetPencapaianController::class);
    Route::resource('subkategori', SubKategoriControllerAdmin::class);
    Route::resource('warung', WarungController::class);
    Route::resource('aturanTenggat', AturanTenggatControllerAdmin::class);
    Route::resource('laba', LabaControllerAdmin::class);
    Route::get('/mutasibarang', [MutasiBarangController::class, 'index'])->name('mutasibarang.index');

    Route::resource('harga-pulsa', HargaPulsaControllerAdmin::class);
    Route::resource('saldo-pulsa', SaldoPulsaControllerAdmin::class);

    Route::get('barang/prices/monitor', [HargaJualControllerAdmin::class, 'indexAllBarangPrices'])->name('harga_jual.monitor_all_prices');
    // Rute detail per barang (Jika Anda ingin mempertahankan kemampuan drill-down)
    Route::get('barang/{id}/prices', [HargaJualControllerAdmin::class, 'showWarungPrices'])->name('harga_jual.show_warung_prices');
    Route::put('update', [HargaJualControllerAdmin::class, 'updateHargaJual'])->name('harga_jual.update');

    Route::get('/riwayat-transaksi', [RiwayatTransaksiControllerAdmin::class, 'index'])->name('riwayat_transaksi.index');

    Route::get('/hutang', [HutangControllerAdmin::class, 'index'])->name('hutang.index');
    Route::get('/hutang/{hutang}/detail', [HutangControllerAdmin::class, 'detailAllWarung'])->name('hutang.detail');
    Route::resource('asalbarang', AsalBarangControllerAdmin::class)->except(['show', 'destroy'])
        ->parameters([
            'asalbarang' => 'idArea' // supaya edit($idArea)
        ]);
    Route::get('asalbarang/filter', [AsalBarangControllerAdmin::class, 'filterBarang'])->name('asalbarang.filter');
    // Ubah juga route create Anda jika belum sesuai dengan name()
    Route::get('asalbarang/create', [AsalBarangControllerAdmin::class, 'create'])->name('asalbarang.create');

    //Satuan
    Route::resource('satuan-barang', SatuanBarangControllerAdmin::class)->except(['create','edit','update']);
    Route::resource('satuan', SatuanControllerAdmin::class)->except(['create','edit','show']);

});
// Route::resource('transaksibarang', TransaksiBarangController::class);


Route::prefix('kasir')->name('kasir.')->group(function () {
    Route::get('/', [KasirControllerKasir::class, 'index'])->name('kasir');

    // Kuantitas routes manual
    Route::get('kuantitas/create', [KuantitasController::class, 'create'])->name('kuantitas.create');
    Route::post('kuantitas/store', [KuantitasController::class, 'store'])->name('kuantitas.store');

    Route::get('kuantitas/{id}/edit', [KuantitasController::class, 'edit'])->name('kuantitas.edit');
    Route::put('kuantitas/{id}/update', [KuantitasController::class, 'update'])->name('kuantitas.update');
    Route::delete('kuantitas/{id}', [KuantitasController::class, 'destroy'])->name('kuantitas.destroy');

    Route::resource('riwayat-transaksi', RiwayatTransaksiControllerKasir::class);

    Route::get('/stok-barang', [StokBarangControllerKasir::class, 'index'])->name('stokbarang.index');
    Route::get('/stok-barang/barang-masuk', [StokBarangControllerKasir::class, 'barangMasuk'])->name('stokbarang.barangmasuk');

    Route::get('/kas', [KasControllerKasir::class, 'index'])->name('kas.index');
    Route::get('/create', [KasControllerKasir::class, 'create'])->name('kas.create');
    Route::post('/', [KasControllerKasir::class, 'store'])->name('kas.store');

    Route::get('/hutang', [HutangControllerKasir::class, 'index'])->name('hutang.index');
    Route::get('/hutang/detail/{id}', [HutangControllerKasir::class, 'detail'])->name('hutang.detail');
    Route::post('/hutang/bayar/{id}', [HutangControllerKasir::class, 'bayar'])->name('hutang.bayar');
    // Hutang Barang Masuk
    Route::get('/hutangBarangamMasuk', [HutangBarangMasukControllerKasir::class, 'index'])->name('hutang.barangmasuk.index');
    Route::get('/{id}/bayar', [HutangBarangMasukControllerKasir::class, 'showDetailPembayaran'])->name('bayar.detail');
    Route::post('/{id}/proses-bayar', [HutangBarangMasukControllerKasir::class, 'processPembayaranHutang'])->name('bayar.process');

    Route::get('/rencana-belanja', [RencanaBelanjaControllerKasir::class, 'rencanaBelanja'])->name('rencanabelanja.index');
    Route::get('/rencana-belanja-list', [RencanaBelanjaControllerKasir::class, 'rencanaBelanja'])->name('rencanabelanja.list');
    Route::get('/rencana-belanja/history', [RencanaBelanjaControllerKasir::class, 'history'])->name('rencanabelanja.history');

    // Rute BARU untuk CREATE dan STORE
    Route::get('/rencana-belanja/create', [RencanaBelanjaControllerKasir::class, 'create'])->name('rencanabelanja.create');
    Route::post('/rencana-belanja/store', [RencanaBelanjaControllerKasir::class, 'store'])->name('rencanabelanja.store');

    Route::resource('barangkeluar', BarangKeluarController::class);
    Route::post('kasir/stok-barang/barang-masuk/konfirmasi', [BarangMasukControllerKasir::class, 'updateStatus'])->name('barang-masuk.konfirmasi');
    Route::post('rencana-belanja/konfirmasi', [RencanaBelanjaControllerKasir::class, 'konfirmasiSelesai'])->name('rencanabelanja.konfirmasi');
    Route::get('/mutasibarang', [MutasiBarangController::class, 'index'])->name('mutasibarang.index');

    Route::get('/member', [MemberControllerKasir::class, 'index'])->name('member.index');
    Route::get('/member/detail/{id}', [MemberControllerKasir::class, 'detail'])->name('member.detail');
    Route::get('/member/create', [MemberControllerKasir::class, 'create'])->name('member.create');
    Route::post('/member/store', [MemberControllerKasir::class, 'store'])->name('member.store');
    Route::get('/member/edit/{id}', [MemberControllerKasir::class, 'edit'])->name('member.edit');
    Route::put('/member/update/{id}', [MemberControllerKasir::class, 'update'])->name('member.update');
    Route::delete('/member/delete/{id}', [MemberControllerKasir::class, 'destroy'])->name('member.destroy');


    Route::get('pulsa', [PulsaControllerKasir::class, 'index'])->name('pulsa.index');
    Route::get('pulsa/create', [PulsaControllerKasir::class, 'createSaldoPulsa'])->name('pulsa.create');
    Route::post('pulsa/store', [PulsaControllerKasir::class, 'storeSaldoPulsa'])->name('pulsa.saldo.store');
    Route::get('pulsa/harga-pulsa/create', [PulsaControllerKasir::class, 'createHargaPulsa'])->name('pulsa.harga-pulsa.create');
    Route::post('pulsa/harga-pulsa/store', [PulsaControllerKasir::class, 'storeHargaPulsa'])->name('pulsa.harga-pulsa.store');
    Route::get('pulsa/harga-pulsa/edit/{id}', [PulsaControllerKasir::class, 'editHargaPulsa'])->name('pulsa.harga-pulsa.edit');
    Route::put('pulsa/harga-pulsa/update/{id}', [PulsaControllerKasir::class, 'updateHargaPulsa'])->name('pulsa.harga-pulsa.update');
    Route::delete('pulsa/harga-pulsa/delete/{id}', [PulsaControllerKasir::class, 'destroyHargaPulsa'])->name('pulsa.harga-pulsa.destroy');
    Route::get('pulsa/jual/create', [PulsaControllerKasir::class, 'createJualPulsa'])->name('pulsa.jual.create');
    Route::post('pulsa/jual/store', [PulsaControllerKasir::class, 'storeJualPulsa'])->name('pulsa.jual.store');
    Route::get('/profil', [ProfilControllerKasir::class, 'index'])->name('profil.index');
});


Route::resource('kategori', KategoriController::class);
Route::resource('subkategori', SubKategoriController::class);
Route::resource('barang', BarangController::class);
// Route::resource('aturantenggat', AturanTenggatController::class); // Tambahkan route untuk AturanTenggat
Route::resource('user', UserController::class);
Route::resource('mutasibarang', MutasiBarangController::class); // Tambahkan route untuk MutasiBarang

Route::resource('area', AreaController::class);
Route::resource('kaswarung', KasWarungController::class);
Route::resource('detailkaswarung', DetailKasWarungController::class);
Route::resource('transaksikas', TransaksiKasController::class);
Route::resource('detailtransaksi', DetailTransaksiController::class);
Route::resource('stokwarung', StokWarungController::class);
Route::resource('stokbarang', StokBarangController::class);
Route::resource('barangmasuk', BarangMasukController::class);
Route::resource('hutang', HutangController::class);
Route::resource('bunga', BungaController::class);
Route::resource('pembayaranhutang', PembayaranHutangController::class);
Route::resource('baranghutang', BarangHutangController::class);

// // Route::resource('kuantitas', KuantitasController::class);
// route::post('kuantitas/store', [KuantitasController::class, 'store'])->name('kuantitas.store');
// Route::post('kuantitas/create', [KuantitasController::class, 'create'])->name('kuantitas.create');


//Barang Masuk dari kasir
Route::post('barangmasuk/update-status', [BarangMasukController::class, 'updateStatus'])->name('barangmasuk.update_status');

//Mutasi Barang dari kasir
Route::post('mutasibarang/update_status', [MutasiBarangController::class, 'updateStatus'])->name('mutasibarang.update_status');

Route::resource('laba', LabaController::class);
//Harga Jual
Route::get('/laba/import', [LabaController::class, 'formImport'])->name('laba.formImport');
Route::post('/laba/import', [LabaController::class, 'import'])->name('laba.import');

Route::get('/kasir/api/notif-barang-masuk', function () {
    $count = \App\Models\BarangMasuk::whereHas('stokWarung.warung', function ($q) {
        $q->where('id_user', auth()->id());
    })->where('status', 'kirim')->count();

    return response()->json(['count' => $count]);
})->middleware('auth');

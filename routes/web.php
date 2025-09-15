<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\SubKategoriController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\AturanTenggatController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WarungController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\KasWarungController;
use App\Http\Controllers\DetailKasWarungController;
use App\Http\Controllers\TransaksiKasController;
use App\Http\Controllers\DetailTransaksiController;
use App\Http\Controllers\StokWarungController;
use App\Http\Controllers\BarangMasukController;
use App\Http\Controllers\MutasiBarangController;
use App\Http\Controllers\HutangController;
use App\Http\Controllers\BungaController;
use App\Http\Controllers\PembayaranHutangController;
use App\Http\Controllers\BarangHutangController;
use App\Http\Controllers\KuantitasController;
use App\Http\Controllers\TargetPencapaianController;
use App\Http\Controllers\BarangKeluarController;

use App\Http\Controllers\Admin\DashboardControllerAdmin;
use App\Http\Controllers\Admin\UserControllerAdmin;
use App\Http\Controllers\Admin\AreaControllerAdmin;
use App\Http\Controllers\Admin\BarangControllerAdmin;
use App\Http\Controllers\Admin\SubKategoriControllerAdmin;
use App\Http\Controllers\Admin\KategoriControllerAdmin;
use App\Http\Controllers\Admin\AreaPembelianController;
use App\Http\Controllers\Admin\TransaksiBarangController;
use App\Http\Controllers\LabaController;

use App\Http\Controllers\AuthController;

use Illuminate\Support\Facades\Password;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
});

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


Route::prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [DashboardControllerAdmin::class, 'index'])->name('dashboard');
    Route::get('/user', [UserControllerAdmin::class, 'index'])->name('user.index');
    Route::get('/area', [AreaControllerAdmin::class, 'index'])->name('area.index');
    Route::get('/area/create', [AreaControllerAdmin::class, 'create'])->name('area.create');
    Route::post('/area/store', [AreaControllerAdmin::class, 'store'])->name('area.store');
    Route::resource('/barang', BarangControllerAdmin::class);
    Route::resource('transaksibarang', TransaksiBarangController::class);
    Route::post('transaksibarang/update-status-massal', [TransaksiBarangController::class, 'updateStatusMassal'])->name('transaksibarang.updateStatusMassal');
    Route::post('/kirim-massal-proses', [TransaksiBarangController::class, 'kirimMassalProses'])->name('transaksibarang.kirim.mass.proses');
    Route::resource('kategori', KategoriControllerAdmin::class);
    Route::resource('areapembelian', AreaPembelianController::class)->names('areapembelian');

    Route::resource('subkategori', SubKategoriControllerAdmin::class);

});


Route::resource('kategori', KategoriController::class);
Route::resource('subkategori', SubKategoriController::class);
Route::resource('barang', BarangController::class);
Route::resource('aturantenggat', AturanTenggatController::class); // Tambahkan route untuk AturanTenggat
Route::resource('user', UserController::class);

Route::resource('warung', WarungController::class);
Route::resource('area', AreaController::class);
Route::resource('kaswarung', KasWarungController::class);
Route::resource('detailkaswarung', DetailKasWarungController::class);
Route::resource('transaksikas', TransaksiKasController::class);
Route::resource('detailtransaksi', DetailTransaksiController::class);
Route::resource('transaksibarang', TransaksiBarangController::class);
Route::resource('stokwarung', StokWarungController::class);
Route::resource('barangmasuk', BarangMasukController::class);
Route::resource('mutasibarang', MutasiBarangController::class); // Tambahkan route untuk MutasiBarang
Route::resource('hutang', HutangController::class);
Route::resource('bunga', BungaController::class);
Route::resource('pembayaranhutang', PembayaranHutangController::class);
Route::resource('baranghutang', BarangHutangController::class);
Route::resource('kuantitas', KuantitasController::class);
Route::resource('targetpencapaian', TargetPencapaianController::class);
Route::resource('barangkeluar', BarangKeluarController::class);


//Barang Masuk dari kasir
Route::post('barangmasuk/update-status', [BarangMasukController::class, 'updateStatus'])->name('barangmasuk.update_status');

//Mutasi Barang dari kasir
Route::post('mutasibarang/update_status', [MutasiBarangController::class, 'updateStatus'])->name('mutasibarang.update_status');

Route::resource('laba', LabaController::class);
//Harga Jual
Route::get('/laba/import', [LabaController::class, 'formImport'])->name('laba.formImport');
Route::post('/laba/import', [LabaController::class, 'import'])->name('laba.import');


<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\SubKategoriController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\AturanTenggatController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\WarungController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\KasWarungController;
use App\Http\Controllers\DetailKasWarungController;
use App\Http\Controllers\TransaksiKasController;
use App\Http\Controllers\DetailTransaksiController;
use App\Http\Controllers\TransaksiBarangController;
use App\Http\Controllers\StokWarungController;
use App\Http\Controllers\BarangMasukController;
use App\Http\Controllers\MutasiBarangController;
use App\Http\Controllers\HutangController;
use App\Http\Controllers\BungaController;
use App\Http\Controllers\PembayaranHutangController;
use App\Http\Controllers\BarangHutangController;
use App\Http\Controllers\KuantitasController;
use App\Http\Controllers\TargetPencapaianController;

use App\Http\Controllers\Admin\DashboardControllerAdmin;
use App\Http\Controllers\Admin\UserControllerAdmin;
use App\Http\Controllers\Admin\AreaControllerAdmin;
use App\Http\Controllers\Admin\BarangControllerAdmin;
use App\Http\Controllers\Admin\SubKategoriControllerAdmin;
use App\Http\Controllers\Admin\KategoriControllerAdmin;


Route::get('/', function () {
    return view('welcome');
});

// GUNAKAN ROUTE GROUP BARU DI BAWAH INI:
Route::prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [DashboardControllerAdmin::class, 'index'])->name('dashboard');

    Route::get('/user', [UserControllerAdmin::class, 'index'])->name('user.index');

    Route::get('/area', [AreaControllerAdmin::class, 'index'])->name('area.index');

    Route::resource('/barang', BarangControllerAdmin::class);
    Route::resource('transaksibarang', TransaksiBarangController::class);

    Route::resource('kategori', KategoriControllerAdmin::class);

    Route::resource('subkategori', SubKategoriControllerAdmin::class);
});


Route::resource('kategori', KategoriController::class);
Route::resource('subkategori', SubKategoriController::class);
Route::resource('barang', BarangController::class);
Route::resource('aturantenggat', AturanTenggatController::class); // Tambahkan route untuk AturanTenggat
Route::resource('user', UserController::class);
Route::resource('kasir', KasirController::class);
Route::resource('member', MemberController::class);
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

//Barang Masuk dari kasir
Route::post('barangmasuk/update-status', [BarangMasukController::class, 'updateStatus'])->name('barangmasuk.update_status');

//Mutasi Barang dari kasir
Route::post('mutasibarang/update_status', [MutasiBarangController::class, 'updateStatus'])->name('mutasibarang.update_status');

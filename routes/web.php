<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\BukuBesarController;
use App\Http\Controllers\ProdukMasukController;
use App\Http\Controllers\ProdukKeluarController;

Route::get('/', function () {
    return view('Login');
});


Route::post('/login', [UserController::class, 'login'])->name('login');

Route::post('/logout', [UserController::class,'logout']);

Route::middleware(['auth'])->group(function () {
    // Route::get('/dashboard', function () {
    //     return view('menu.dashboard');
    // })->name('dashboard');

    Route::get('/dashboard', [UserController::class, 'index'])->name('dashboard');

    Route::get('/akun', [UserController::class, 'akun'])->name('akun');
    Route::post('/akun/password', [UserController::class, 'updatePassword'])->name('akun.password.update');
    Route::get('/akunlist', [UserController::class, 'akunlist'])->name('akun.list');
    Route::delete('/akun/{id}', [UserController::class, 'destroy'])->name('akun.destroy');
    Route::post('/akun-reset/{id}', [UserController::class, 'resetPassword'])->name('akun.reset');
    Route::post('/akun/store', [UserController::class, 'store'])->name('akun.store');

    Route::resource('users', UserController::class);

    Route::get('/produk/update-list', [ProdukController::class, 'updateList'])->name('produk.updateList');
    Route::put('/produk/quick-update/{id}', [ProdukController::class, 'quickUpdate'])->name('produk.quickUpdate');

    Route::get('supplier-search', [ProdukMasukController::class, 'supplierSearch'])->name('supplier.search');
    Route::get('supplier-search0', [ProdukKeluarController::class, 'supplierSearch0'])->name('supplier0.search');
    // Route::post('supplier-store', [ProdukMasukController::class, 'supplierStore'])->name('supplier.store');
    Route::post('supplier-store', [ProdukMasukController::class, 'supplierStore1'])->name('supplier.store1');
    Route::get('produk-search', [ProdukMasukController::class, 'produkSearch'])->name('produk.search');



    Route::get('input/pending', [ProdukMasukController::class, 'pending'])->name('input.pending');
    Route::get('input/pending/{id}  ', [ProdukMasukController::class, 'pendingDetails'])->name('input.pendingdetail');
    Route::patch('/pending/proses/{id}', [ProdukMasukController::class, 'prosesPending'])->name('pending.proses');
    Route::get('input/history', [ProdukMasukController::class, 'history'])->name('input.history');
    Route::get('input/history/{id}', [ProdukMasukController::class, 'historyDetail'])->name('input.historyDetail');
    Route::get('/input/history/{produk}/excel', [ProdukMasukController::class, 'historyExcel'])->name('input.history.excel');
    Route::get('/input/laporan/excel', [ProdukMasukController::class, 'laporanExcel'])->name('input.laporan.excel');

    Route::get('input/laporan', [ProdukMasukController::class, 'laporan'])->name('input.laporan');
    Route::post('input/data', [ProdukMasukController::class, 'store'])->name('input.data');


    Route::get('output/history', [ProdukKeluarController::class, 'history'])->name('output.history');
    Route::get('output/history/{id}', [ProdukKeluarController::class, 'historyDetail'])->name('output.historyDetail');
    Route::get('output/laporan', [ProdukKeluarController::class, 'laporan'])->name('output.laporan');
    Route::get('/output/history/{produk}/excel', [ProdukKeluarController::class, 'historyExcel'])->name('output.history.excel');
    Route::get('/output/laporan/excel', [ProdukKeluarController::class, 'laporanExcel'])->name('output.laporan.excel');

    Route::get('stok/history', [KategoriController::class, 'history'])->name('stok.history');
    Route::get('stok/history/{id}', [KategoriController::class, 'historyDetail'])->name('stok.historyDetail');
    Route::get('stok/laporan', [KategoriController::class, 'laporan'])->name('stok.laporan');
    Route::get('/stok/history/{produk}/excel', [KategoriController::class, 'historyExcel'])->name('stok.history.excel');
    Route::get('stok/{id}/periksa', [KategoriController::class, 'periksa'])->name('stok.periksa');
    Route::patch('/periksa/proses/{id}', [KategoriController::class, 'prosesPeriksa'])->name('periksa.proses');
    Route::patch('/koreksi/proses/{id}', [KategoriController::class, 'koreksiStok'])->name('stok.koreksi');
    Route::get('stok/{id}/history', [KategoriController::class, 'stoksisa'])->name('input.historyDetail');

    Route::get('/kategori', [KategoriController::class, 'kategori'])->name('kategori');
    Route::post('/kategori/store', [KategoriController::class, 'store'])->name('kategori.store');
    Route::delete('/kategori/{id}', [KategoriController::class, 'destroy'])->name('kategori.destroy');
    Route::put('/kategori/{id}', [KategoriController::class, 'update'])->name('kategori.update');


    Route::post('/buku_besar/tambah', [BukuBesarController::class, 'tambahUang'])->name('buku_besar.tambah');
    Route::post('/buku_besar/kurangi', [BukuBesarController::class, 'kurangiUang'])->name('buku_besar.kurangi');

    Route::resource('supplier', SupplierController::class);
    Route::get('suppfrom', [SupplierController::class, 'index'])->name('suppfrom');
    Route::get('suppfrom/{id}', [SupplierController::class, 'show'])->name('suppfrom.show');
    Route::get('/nota/pembelian/{produkMasuk}', [SupplierController::class, 'pembelian']);
    Route::get('/nota/pembelian/{produkMasuk}/pdf', [SupplierController::class, 'pembelianPdf']);

    Route::get('suppto', [SupplierController::class, 'index1'])->name('suppto');
    Route::get('suppto/{id}', [SupplierController::class, 'show1'])->name('suppto.show1');
    Route::get('/faktur/penjualan/{produkKeluar}', [SupplierController::class, 'penjualan']);
    Route::get('/faktur/penjualan/{produkKeluar}/pdf', [SupplierController::class, 'penjualanPdf']);

    Route::post('supplier/store', [SupplierController::class, 'store'])->name('supplier.store');
    Route::put('/supplier/{supplier}', [SupplierController::class, 'update'])->name('supplier.update');


    Route::resource('produk', ProdukController::class);
    Route::resource('input', ProdukMasukController::class);
    Route::resource('output', ProdukKeluarController::class);
    Route::resource('stok', KategoriController::class);
    Route::resource('buku', BukuBesarController::class);

});
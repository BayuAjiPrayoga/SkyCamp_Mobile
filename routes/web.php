<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\KavlingController;
use App\Http\Controllers\Admin\PeralatanController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\VerifikasiController;
use App\Http\Controllers\Admin\GaleriController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\PengumumanController;
use App\Http\Controllers\Admin\ProfilController;
use App\Http\Controllers\Auth\LoginController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public Routes
Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

// Auth Routes
Route::get('/fix-storage', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('storage:link');
        return 'Storage link has been created. Check your images now. <a href="/admin/galeri">Go back</a>';
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Admin Routes (Protected)
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Master Data
    Route::resource('kavling', KavlingController::class);
    Route::resource('peralatan', PeralatanController::class);

    // Transaksi
    Route::get('/booking/export', [BookingController::class, 'export'])->name('booking.export');
    Route::get('/booking/scan', [BookingController::class, 'scanPage'])->name('booking.scan');
    Route::post('/booking/scan', [BookingController::class, 'scanCheckIn'])->name('booking.scan-check-in');

    Route::resource('booking', BookingController::class)->only(['index', 'show']);
    Route::post('/booking/{booking}/check-in', [BookingController::class, 'checkIn'])->name('booking.check-in');
    Route::get('/verifikasi', [VerifikasiController::class, 'index'])->name('verifikasi.index');
    Route::post('/verifikasi/{booking}/confirm', [VerifikasiController::class, 'confirm'])->name('verifikasi.confirm');
    Route::post('/verifikasi/{booking}/reject', [VerifikasiController::class, 'reject'])->name('verifikasi.reject');

    // Galeri
    Route::get('/galeri', [GaleriController::class, 'index'])->name('galeri.index');
    Route::post('/galeri/bulk-approve', [GaleriController::class, 'bulkApprove'])->name('galeri.bulk-approve');
    Route::post('/galeri/bulk-reject', [GaleriController::class, 'bulkReject'])->name('galeri.bulk-reject');
    Route::post('/galeri/bulk-destroy', [GaleriController::class, 'bulkDestroy'])->name('galeri.bulk-destroy');
    Route::post('/galeri/{gallery}/approve', [GaleriController::class, 'approve'])->name('galeri.approve');
    Route::post('/galeri/{gallery}/reject', [GaleriController::class, 'reject'])->name('galeri.reject');
    Route::delete('/galeri/{gallery}', [GaleriController::class, 'destroy'])->name('galeri.destroy');

    // Laporan
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/pdf', [LaporanController::class, 'exportPdf'])->name('laporan.pdf');
    Route::get('/laporan/excel', [LaporanController::class, 'exportExcel'])->name('laporan.excel');

    // Pengaturan
    Route::resource('pengumuman', PengumumanController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::get('/profil', [ProfilController::class, 'index'])->name('profil');
    Route::put('/profil', [ProfilController::class, 'update'])->name('profil.update');
    Route::put('/profil/password', [ProfilController::class, 'updatePassword'])->name('profil.password');
});

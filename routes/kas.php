<?php

use App\Http\Controllers\Kas\AnggotaController;
use App\Http\Controllers\Kas\AuthController;
use App\Http\Controllers\Kas\DashboardController;
use App\Http\Controllers\Kas\LaporanController;
use App\Http\Controllers\Kas\PembayaranController;
use App\Http\Controllers\Kas\PengeluaranController;
use App\Http\Controllers\Kas\PeriodeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Modul Kas Kelas (prefix /kas, guard `kas`, tabel `pengguna`)
|--------------------------------------------------------------------------
| Terpisah dari login bawaan (guard `web`) milik kelompok lain.
*/

Route::prefix('kas')->name('kas.')->group(function () {

    // Login (dibatasi 5 percobaan per menit)
    Route::get('/login', [AuthController::class, 'showForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.attempt');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Semua peran yang sudah login: admin, bendahara, anggota
    Route::middleware('kas')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/anggota', [AnggotaController::class, 'index'])->name('anggota.index');
    });

    // Admin: kelola data anggota
    Route::middleware('kas:admin')->group(function () {
        Route::post('/anggota', [AnggotaController::class, 'store'])->name('anggota.store');
        Route::put('/anggota/{anggota}', [AnggotaController::class, 'update'])->name('anggota.update');
        Route::patch('/anggota/{anggota}/nonaktif', [AnggotaController::class, 'nonaktifkan'])->name('anggota.nonaktif');
    });

    // Bendahara: periode iuran, pembayaran, pengeluaran
    Route::middleware('kas:bendahara')->group(function () {
        Route::get('/periode', [PeriodeController::class, 'index'])->name('periode.index');
        Route::post('/periode', [PeriodeController::class, 'store'])->name('periode.store');
        Route::put('/periode/{periode}', [PeriodeController::class, 'update'])->name('periode.update');
        Route::delete('/periode/{periode}', [PeriodeController::class, 'destroy'])->name('periode.destroy');

        Route::get('/pembayaran', [PembayaranController::class, 'index'])->name('pembayaran.index');
        Route::post('/pembayaran', [PembayaranController::class, 'store'])->name('pembayaran.store');
        Route::delete('/pembayaran/{pembayaran}', [PembayaranController::class, 'destroy'])->name('pembayaran.destroy');

        Route::get('/pengeluaran', [PengeluaranController::class, 'index'])->name('pengeluaran.index');
        Route::post('/pengeluaran', [PengeluaranController::class, 'store'])->name('pengeluaran.store');
        Route::put('/pengeluaran/{pengeluaran}', [PengeluaranController::class, 'update'])->name('pengeluaran.update');
        Route::delete('/pengeluaran/{pengeluaran}', [PengeluaranController::class, 'destroy'])->name('pengeluaran.destroy');
    });

    // Admin dan bendahara: laporan kas
    Route::middleware('kas:admin,bendahara')->group(function () {
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan');
    });
});

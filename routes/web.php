<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\StrukturKelasController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;


Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

// Guest (belum login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');

    Route::get('/register', [RegisterController::class, 'showForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.store');
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'send'])->name('password.email');

    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
});

// Logout
Route::post('/logout', [LogoutController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Dashboard & CRUD khusus Admin (level:1)
Route::middleware(['auth', 'verified', 'level:1'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'admin'])->name('dashboard.admin');

    // CRUD Profil Kelas (Create, Edit, Update, Delete)
    Route::resource('kelas', KelasController::class)->except(['index', 'show']);

    // CRUD Data Mahasiswa (Create, Edit, Update, Delete)
    Route::resource('mahasiswa', MahasiswaController::class)->except(['index', 'show']);

    // Atur Jabatan Pengurus
    Route::post('/struktur-kelas/pengurus', [StrukturKelasController::class, 'storePengurus'])->name('struktur-kelas.pengurus.store');
    Route::put('/struktur-kelas/pengurus/{nim}', [StrukturKelasController::class, 'updatePengurus'])->name('struktur-kelas.pengurus.update');
    Route::delete('/struktur-kelas/pengurus/{nim}', [StrukturKelasController::class, 'destroyPengurus'])->name('struktur-kelas.pengurus.destroy');
});

Route::middleware(['auth', 'verified', 'level:2'])->group(function () {
    Route::get('/user/dashboard', [DashboardController::class, 'user'])->name('dashboard.user');
});

// Modul Kelompok 4 (Read Profil Kelas, Mahasiswa, Struktur Kelas & Generate Laporan)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/kelas', [KelasController::class, 'index'])->name('kelas.index');
    Route::get('/kelas/{kela}', [KelasController::class, 'show'])->name('kelas.show');

    Route::get('/mahasiswa', [MahasiswaController::class, 'index'])->name('mahasiswa.index');
    Route::get('/mahasiswa/{mahasiswa}', [MahasiswaController::class, 'show'])->name('mahasiswa.show');

    Route::get('/struktur-kelas', [StrukturKelasController::class, 'index'])->name('struktur-kelas.index');

    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/mahasiswa/pdf', [LaporanController::class, 'mahasiswaPdf'])->name('laporan.mahasiswa.pdf');
    Route::get('/laporan/mahasiswa/excel', [LaporanController::class, 'mahasiswaExcel'])->name('laporan.mahasiswa.excel');
    Route::get('/laporan/kelas/pdf', [LaporanController::class, 'kelasPdf'])->name('laporan.kelas.pdf');
});

// Verifikasi email
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', [EmailVerificationController::class, 'notice'])
        ->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('/email/verification-notification', [EmailVerificationController::class, 'send'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
});
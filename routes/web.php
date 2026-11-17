<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\DashboardController;
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

// Dashboard per level
Route::middleware(['auth', 'verified', 'level:1'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'admin'])->name('dashboard.admin');
    Route::post('/struktur-kelas/pengurus', [StrukturKelasController::class, 'storePengurus'])->name('struktur-kelas.pengurus.store');
    Route::put('/struktur-kelas/pengurus/{nim}', [StrukturKelasController::class, 'updatePengurus'])->name('struktur-kelas.pengurus.update');
    Route::delete('/struktur-kelas/pengurus/{nim}', [StrukturKelasController::class, 'destroyPengurus'])->name('struktur-kelas.pengurus.destroy');
});

Route::middleware(['auth', 'verified', 'level:2'])->group(function () {
    Route::get('/user/dashboard', [DashboardController::class, 'user'])->name('dashboard.user');
});

// Modul Struktur Kelas & Pengurus (Kelompok 4 â€” dapat diakses Admin & User)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/struktur-kelas', [StrukturKelasController::class, 'index'])->name('struktur-kelas.index');
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
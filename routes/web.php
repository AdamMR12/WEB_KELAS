<?php

use App\Http\Controllers\AgendaController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ScheduleController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;


Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route(auth()->user()->dashboardRoute());
    }

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

    Route::get('/jadwal/create', [ScheduleController::class, 'create'])->name('jadwal.create');
    Route::post('/jadwal', [ScheduleController::class, 'store'])->name('jadwal.store');
    Route::delete('/jadwal/{jadwalMk}', [ScheduleController::class, 'destroy'])->name('jadwal.destroy');

    Route::get('/agenda/create', [AgendaController::class, 'create'])->name('agenda.create');
    Route::post('/agenda', [AgendaController::class, 'store'])->name('agenda.store');
    Route::delete('/agenda/{jadwalAgenda}', [AgendaController::class, 'destroy'])->name('agenda.destroy');

    Route::post('/agenda/kategori', [AgendaController::class, 'storeCategory'])->name('agenda.category.store');
    Route::delete('/agenda/kategori/{category}', [AgendaController::class, 'destroyCategory'])->name('agenda.category.destroy');
    Route::post('/agenda/status', [AgendaController::class, 'storeStatus'])->name('agenda.status.store');
    Route::delete('/agenda/status/{status}', [AgendaController::class, 'destroyStatus'])->name('agenda.status.destroy');
});

Route::middleware(['auth', 'verified', 'level:2'])->group(function () {
    Route::get('/user/dashboard', [DashboardController::class, 'user'])->name('dashboard.user');
});

// Jadwal & Agenda Non-Akademik (semua user login terverifikasi)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/jadwal', [ScheduleController::class, 'index'])->name('jadwal.index');
    Route::get('/agenda', [AgendaController::class, 'index'])->name('agenda.index');
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
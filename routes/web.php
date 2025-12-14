<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\UserController;
use App\Http\Controllers\Web\PaketController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
    Route::get('forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('forgot-password', [AuthController::class, 'forgotPassword'])->name('password.email');
    Route::get('reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

// Protected web routes
Route::middleware(['jwt.auth', 'web'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    // User Management (Admin only)
    Route::middleware('role:admin')->prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('{user}', [UserController::class, 'update'])->name('update');
        Route::delete('{user}', [UserController::class, 'destroy'])->name('destroy');
        Route::patch('{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
    });

    // Package Management
    Route::middleware('role:admin,petugas_pos,keamanan')->prefix('paket')->name('paket.')->group(function () {
        Route::get('/', [PaketController::class, 'index'])->name('index');
        Route::get('{paket}', [PaketController::class, 'show'])->name('show');
        
        // Petugas Pos can create and update packages
        Route::middleware('role:admin,petugas_pos')->group(function () {
            Route::get('create', [PaketController::class, 'create'])->name('create');
            Route::post('/', [PaketController::class, 'store'])->name('store');
            Route::get('{paket}/edit', [PaketController::class, 'edit'])->name('edit');
            Route::put('{paket}', [PaketController::class, 'update'])->name('update');
        });

        // Status updates (all roles with proper authorization)
        Route::patch('{paket}/update-status', [PaketController::class, 'updateStatus'])->name('update-status');
    });

    // Reports (Admin and Petugas Pos)
    Route::middleware('role:admin,petugas_pos')->prefix('reports')->name('reports.')->group(function () {
        Route::get('paket', [PaketController::class, 'reports'])->name('paket');
        Route::get('export-paket', [PaketController::class, 'exportPaket'])->name('export-paket');
    });
});
<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\WilayahController;
use App\Http\Controllers\Api\AsramaController;
use App\Http\Controllers\Api\PaketController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);
});

// Protected routes
Route::middleware('jwt.auth')->group(function () {
    // Auth routes
    Route::prefix('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::get('me', [AuthController::class, 'me']);
        Route::post('change-password', [AuthController::class, 'changePassword']);
    });

    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index']);

    // User management (Admin only)
    Route::prefix('users')->middleware('role:admin')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store']);
        Route::get('{user}', [UserController::class, 'show']);
        Route::put('{user}', [UserController::class, 'update']);
        Route::delete('{user}', [UserController::class, 'destroy']);
        Route::patch('{user}/toggle-status', [UserController::class, 'toggleStatus']);
        Route::patch('{user}/assign-wilayah', [UserController::class, 'assignWilayah']);
    });

    // User self-management
    Route::prefix('users')->middleware('role:admin,petugas_pos,keamanan')->group(function () {
        Route::get('keamanan/{wilayah_id}', [UserController::class, 'getKeamananByWilayah']);
    });

    // Wilayah management
    Route::prefix('wilayah')->middleware('role:admin,petugas_pos,keamanan')->group(function () {
        Route::get('/', [WilayahController::class, 'index']);
        Route::get('{wilayah}', [WilayahController::class, 'show'])->middleware('wilayah');
    });

    // Admin only can manage wilayah
    Route::prefix('wilayah')->middleware('role:admin')->group(function () {
        Route::post('/', [WilayahController::class, 'store']);
        Route::put('{wilayah}', [WilayahController::class, 'update']);
        Route::delete('{wilayah}', [WilayahController::class, 'destroy']);
    });

    // Asrama management
    Route::prefix('asrama')->middleware('role:admin,petugas_pos,keamanan')->group(function () {
        Route::get('/', [AsramaController::class, 'index']);
        Route::get('wilayah/{wilayah_id}', [AsramaController::class, 'getByWilayah']);
        Route::get('{asrama}', [AsramaController::class, 'show']);
    });

    // Admin only can manage asrama
    Route::prefix('asrama')->middleware('role:admin')->group(function () {
        Route::post('/', [AsramaController::class, 'store']);
        Route::put('{asrama}', [AsramaController::class, 'update']);
        Route::delete('{asrama}', [AsramaController::class, 'destroy']);
    });

    // Paket management
    Route::prefix('paket')->middleware('role:admin,petugas_pos,keamanan')->group(function () {
        Route::get('/', [PaketController::class, 'index']);
        Route::get('{paket}', [PaketController::class, 'show']);
        Route::get('stats/summary', [PaketController::class, 'getStatsSummary']);
        Route::get('stats/by-status', [PaketController::class, 'getStatsByStatus']);
        Route::get('stats/by-wilayah', [PaketController::class, 'getStatsByWilayah']);
    });

    // Paket operations based on role
    Route::prefix('paket')->group(function () {
        // Petugas Pos can create packages
        Route::post('/', [PaketController::class, 'store'])->middleware('role:admin,petugas_pos');
        
        // Admin and Petugas Pos can update any package
        Route::put('{paket}', [PaketController::class, 'update'])->middleware('role:admin,petugas_pos');
        
        // Keamanan can only update packages in their wilayah
        Route::put('{paket}/update-status', [PaketController::class, 'updateStatus'])->middleware('role:admin,petugas_pos,keamanan');
        
        // Admin can delete packages
        Route::delete('{paket}', [PaketController::class, 'destroy'])->middleware('role:admin');
        
        // Soft delete restore (Admin only)
        Route::patch('{paket}/restore', [PaketController::class, 'restore'])->middleware('role:admin');
    });

    // Status logs
    Route::prefix('paket/{paket}')->middleware('role:admin,petugas_pos,keamanan')->group(function () {
        Route::get('status-logs', [PaketController::class, 'getStatusLogs']);
    });

    // Audit logs (Admin only)
    Route::prefix('audit-logs')->middleware('role:admin')->group(function () {
        Route::get('/', [PaketController::class, 'getAuditLogs']);
    });
});
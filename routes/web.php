<?php

use App\Http\Controllers\PaketController;
use App\Http\Controllers\PaketMasukController;
use App\Http\Controllers\PaketKeluarController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    
    // Paket Masuk Routes
    Route::get('/paket/masuk', [PaketMasukController::class, 'index'])->name('paket.masuk');
    
    // Paket Keluar Routes
    Route::get('/paket/keluar', [PaketKeluarController::class, 'index'])->name('paket.keluar');
    
    // Main Paket Routes
    Route::get('/paket', [PaketController::class, 'index'])->name('paket.index');
    Route::get('/paket/create', [PaketController::class, 'create'])->name('paket.create');
    Route::post('/paket', [PaketController::class, 'store'])->name('paket.store');
    Route::get('/paket/{paket}', [PaketController::class, 'show'])->name('paket.show');
    Route::get('/paket/{paket}/edit', [PaketController::class, 'edit'])->name('paket.edit');
    Route::put('/paket/{paket}', [PaketController::class, 'update'])->name('paket.update');
    Route::delete('/paket/{paket}', [PaketController::class, 'destroy'])->name('paket.destroy');
    
    // Status Update Route
    Route::post('/paket/{paket}/status', [PaketController::class, 'updateStatus'])->name('paket.update-status');
    
    // Force Delete and Restore Routes
    Route::delete('/paket/{id}/force', [PaketController::class, 'forceDestroy'])->name('paket.force-destroy');
    Route::post('/paket/{id}/restore', [PaketController::class, 'restore'])->name('paket.restore');
    
    // AJAX Routes
    Route::post('/paket/check-resi', [PaketController::class, 'checkResi'])->name('paket.check-resi');
});

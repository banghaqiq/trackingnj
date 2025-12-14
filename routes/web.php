<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PaketKeluarController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
Route::get('/dashboard/chart-data', [DashboardController::class, 'chartData'])->name('dashboard.chart-data');

Route::get('/paket/keluar', [PaketKeluarController::class, 'index'])->name('paket.keluar');

Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
Route::get('/laporan/export/excel', [LaporanController::class, 'exportExcel'])->name('laporan.export-excel');
Route::get('/laporan/export/pdf', [LaporanController::class, 'exportPdf'])->name('laporan.export-pdf');

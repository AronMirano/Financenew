<?php

use App\Http\Controllers\Annualization\AnnualizationController;
use App\Http\Controllers\Auth\SessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Pre\LedgerEntryController;
use App\Http\Controllers\Pre\MonitoringController;
use App\Http\Controllers\Pre\ObligationController;
use App\Http\Controllers\Pre\PreController;
use App\Http\Controllers\Pre\SaobController;
use App\Http\Controllers\Pre\WorkingPaperController;
use Illuminate\Support\Facades\Route;

// ---- Authentication --------------------------------------------------
Route::middleware('guest')->group(function () {
    Route::get('/login', [SessionController::class, 'create'])->name('login');
    Route::post('/login', [SessionController::class, 'store'])->name('login.store');
});
Route::post('/logout', [SessionController::class, 'destroy'])->name('logout');

// ---- Application -----------------------------------------------------
Route::middleware('auth')->group(function () {
    Route::redirect('/', '/dashboard');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // PRE — Program of Receipts and Expenditures
    Route::prefix('pre')->name('pre.')->group(function () {
        Route::get('/overview', [PreController::class, 'overview'])->name('overview');
        Route::get('/submit', [PreController::class, 'submitForm'])->name('submit');
        Route::post('/submit', [PreController::class, 'submitStore'])->name('submit.store');
        Route::get('/ppmp', [PreController::class, 'ppmp'])->name('ppmp');
        Route::get('/expenditures', [PreController::class, 'expenditures'])->name('expenditures');
        Route::get('/reports', [PreController::class, 'reports'])->name('reports');
        Route::post('/reports/generate', [PreController::class, 'generateReport'])->name('reports.generate');
        Route::get('/analytics', [PreController::class, 'analytics'])->name('analytics');

        Route::get('/working-paper', WorkingPaperController::class)->name('workingpaper');
        Route::get('/saob', SaobController::class)->name('saob');
        Route::get('/monitoring', MonitoringController::class)->name('monitoring');

        // OBRS (Appendix 11) and BURS (Appendix 14) share one controller;
        // the {kind} segment selects obligation vs. utilization.
        Route::prefix('obrs')->name('obrs.')->group(function () {
            Route::get('/', [ObligationController::class, 'index'])->name('index')->defaults('kind', 'obligation');
            Route::get('/create', [ObligationController::class, 'create'])->name('create')->defaults('kind', 'obligation');
            Route::post('/', [ObligationController::class, 'store'])->name('store')->defaults('kind', 'obligation');
            Route::get('/{document}', [ObligationController::class, 'show'])->name('show')->defaults('kind', 'obligation');
        });

        Route::prefix('burs')->name('burs.')->group(function () {
            Route::get('/', [ObligationController::class, 'index'])->name('index')->defaults('kind', 'utilization');
            Route::get('/create', [ObligationController::class, 'create'])->name('create')->defaults('kind', 'utilization');
            Route::post('/', [ObligationController::class, 'store'])->name('store')->defaults('kind', 'utilization');
            Route::get('/{document}', [ObligationController::class, 'show'])->name('show')->defaults('kind', 'utilization');
        });

        // Status of Obligation / Utilization ledger entries.
        Route::post('/documents/{document}/ledger', LedgerEntryController::class)->name('ledger.store');
    });

    // Annualization
    Route::prefix('annualization')->name('ann.')->group(function () {
        Route::get('/overview', [AnnualizationController::class, 'overview'])->name('overview');
        Route::get('/import', [AnnualizationController::class, 'import'])->name('import');
        Route::get('/records', [AnnualizationController::class, 'records'])->name('records');
        Route::get('/employee/{record?}', [AnnualizationController::class, 'employee'])->name('employee');
        Route::get('/deductions', [AnnualizationController::class, 'deductions'])->name('deductions');
        Route::get('/reports', [AnnualizationController::class, 'reports'])->name('reports');
        Route::post('/reports/generate', [AnnualizationController::class, 'generateReport'])->name('reports.generate');
        Route::get('/analytics', [AnnualizationController::class, 'analytics'])->name('analytics');
    });
});

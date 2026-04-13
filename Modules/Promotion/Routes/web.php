<?php

use Illuminate\Support\Facades\Route;
use Modules\Promotion\Http\Controllers\RewardReportController;

Route::middleware('web')->group(function () {
    Route::prefix('rewards')->name('rewards.')->group(function () {
        Route::get('/',       [RewardReportController::class, 'index'])->name('index');
        Route::get('/export', [RewardReportController::class, 'export'])->name('export');
    });
});

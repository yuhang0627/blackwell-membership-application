<?php

use Illuminate\Support\Facades\Route;
use Modules\Promotion\Http\Controllers\RewardReportController;

Route::middleware('web')->group(function () {
    Route::prefix('promotions')->name('promotions.')->group(function () {
        Route::get('/',        [RewardReportController::class, 'promotions'])->name('index');
        Route::get('/create',  [RewardReportController::class, 'createPromotion'])->name('create');
        Route::post('/',       [RewardReportController::class, 'storePromotion'])->name('store');
        Route::get('/{promotion}/edit', [RewardReportController::class, 'editPromotion'])->name('edit');
        Route::put('/{promotion}',      [RewardReportController::class, 'updatePromotion'])->name('update');
    });

    Route::prefix('rewards')->name('rewards.')->group(function () {
        Route::get('/',       [RewardReportController::class, 'index'])->name('index');
        Route::get('/export', [RewardReportController::class, 'export'])->name('export');
    });
});

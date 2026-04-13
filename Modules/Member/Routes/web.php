<?php

use Illuminate\Support\Facades\Route;
use Modules\Member\Http\Controllers\MemberController;

Route::middleware('web')->group(function () {
    Route::prefix('members')->name('members.')->group(function () {
        Route::get('/',               [MemberController::class, 'index'])->name('index');
        Route::get('/create',         [MemberController::class, 'create'])->name('create');
        Route::get('/export/csv',     [MemberController::class, 'exportCsv'])->name('export');
        Route::post('/',              [MemberController::class, 'store'])->name('store');
        Route::get('/{member}',       [MemberController::class, 'show'])->name('show');
        Route::get('/{member}/edit',  [MemberController::class, 'edit'])->name('edit');
        Route::put('/{member}',       [MemberController::class, 'update'])->name('update');
        Route::delete('/{member}',    [MemberController::class, 'destroy'])->name('destroy');
    });
});

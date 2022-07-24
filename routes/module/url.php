<?php

use App\Http\Controllers\IndexUrlController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin/url')->name('url.')->middleware(['auth', 'role:developer|super'])
    ->group(function () {

    Route::get('/', [IndexUrlController::class, 'index'])
        ->name('index');
    Route::get('/trash', [IndexUrlController::class, 'trash'])
        ->name('trash');

    Route::get('/create', [IndexUrlController::class, 'create'])
        ->name('create');
    Route::post('/store', [IndexUrlController::class, 'store'])
        ->name('store');
    Route::get('/{id}/edit', [IndexUrlController::class, 'edit'])
        ->name('edit');
    Route::put('/{id}', [IndexUrlController::class, 'update'])
        ->name('update');
    Route::delete('/{id}/soft', [IndexUrlController::class, 'softDelete'])
        ->name('delete.soft');
    Route::delete('/{id}/permanent', [IndexUrlController::class, 'permanentDelete'])
        ->name('delete.permanent');
    Route::put('/{id}/restore', [IndexUrlController::class, 'restore'])
        ->name('restore');

});
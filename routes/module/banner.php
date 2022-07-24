<?php

use App\Http\Controllers\Module\Banner\BannerController;
use App\Http\Controllers\Module\Banner\BannerFileController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin/banner')->name('banner.')->middleware('auth')->group(function () {

    Route::get('/', [BannerController::class, 'index'])
        ->name('index')
        ->middleware('permission:banners');
    Route::get('/trash', [BannerController::class, 'trash'])
        ->name('trash')
        ->middleware('role:developer|super');

    Route::get('/create', [BannerController::class, 'create'])
        ->name('create')
        ->middleware('permission:banner_create');
    Route::post('/store', [BannerController::class, 'store'])
        ->name('store')
        ->middleware('permission:banner_create');
    Route::get('/{id}/edit', [BannerController::class, 'edit'])
        ->name('edit')
        ->middleware('permission:banner_update');
    Route::put('/{id}', [BannerController::class, 'update'])
        ->name('update')
        ->middleware('permission:banner_update');
    Route::put('/{id}', [BannerController::class, 'update'])
        ->name('update')
        ->middleware('permission:banner_update');
    Route::put('/{id}/publish', [BannerController::class, 'publish'])
        ->name('publish')
        ->middleware('permission:banner_update');
    Route::put('/{id}/approved', [BannerController::class, 'approved'])
        ->name('approved')
        ->middleware('role:developer|super');
    Route::post('/sort', [BannerController::class, 'sort'])
        ->name('sort')
        ->middleware('permission:banner_update');
    Route::put('/{id}/position/{position}', [BannerController::class, 'position'])
        ->name('position')
        ->middleware('permission:banner_update');
    Route::delete('/{id}/soft', [BannerController::class, 'softDelete'])
        ->name('delete.soft')
        ->middleware('permission:banner_category_delete');
    Route::delete('/{id}/permanent', [BannerController::class, 'permanentDelete'])
        ->name('delete.permanent')
        ->middleware('role:developer|super');
    Route::put('/{id}/restore', [BannerController::class, 'restore'])
        ->name('restore')
        ->middleware('role:developer|super');

    //--- Banner File
    Route::prefix('{bannerId}/file')->name('file.')->group(function() {

        Route::get('/', [BannerFileController::class, 'index'])
            ->name('index')
            ->middleware('permission:banner_files');
        Route::get('/trash', [BannerFileController::class, 'trash'])
            ->name('trash')
            ->middleware('role:developer|super');
            
        Route::get('/create', [BannerFileController::class, 'create'])
            ->name('create')
            ->middleware('permission:banner_file_create');
        Route::post('/', [BannerFileController::class, 'store'])
            ->name('store')
            ->middleware('permission:banner_file_create');
        Route::post('/multiple', [BannerFileController::class, 'storeMultiple'])
            ->name('store.multiple')
            ->middleware('permission:banner_file_create');
        Route::get('/{id}/edit', [BannerFileController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:banner_file_update');
        Route::put('/{id}', [BannerFileController::class, 'update'])
            ->name('update')
            ->middleware('permission:banner_file_update');
        Route::put('/{id}/publish', [BannerFileController::class, 'publish'])
            ->name('publish')
            ->middleware('permission:banner_file_update');
        Route::put('/{id}/approved', [BannerFileController::class, 'approved'])
            ->name('approved')
            ->middleware('role:developer|super|support|admin');
        Route::post('/sort', [BannerFileController::class, 'sort'])
            ->name('sort')
            ->middleware('permission:banner_file_update');
        Route::put('/{id}/position/{position}', [BannerFileController::class, 'position'])
            ->name('position')
            ->middleware('permission:banner_file_update');
        Route::delete('/{id}/soft', [BannerFileController::class, 'softDelete'])
            ->name('delete.soft')
            ->middleware('permission:banner_file_delete');
        Route::delete('/{id}/permanent', [BannerFileController::class, 'permanentDelete'])
            ->name('delete.permanent')
            ->middleware('role:developer|super');
        Route::put('/{id}/restore', [BannerFileController::class, 'restore'])
            ->name('restore')
            ->middleware('role:developer|super');

    });

});
<?php

use App\Http\Controllers\Module\Banner\BannerCategoryController;
use App\Http\Controllers\Module\Banner\BannerController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin/banner')->name('banner.')->middleware('auth')->group(function () {

    //--- Category
    Route::prefix('category')->name('category.')->group(function() {

        Route::get('/', [BannerCategoryController::class, 'index'])
            ->name('index')
            ->middleware('permission:banner_categories');
        Route::get('/trash', [BannerCategoryController::class, 'trash'])
            ->name('trash')
            ->middleware('role:super');

        Route::get('/create', [BannerCategoryController::class, 'create'])
            ->name('create')
            ->middleware('permission:banner_category_create');
        Route::post('/store', [BannerCategoryController::class, 'store'])
            ->name('store')
            ->middleware('permission:banner_category_create');
        Route::get('/{id}/edit', [BannerCategoryController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:banner_category_update');
        Route::put('/{id}', [BannerCategoryController::class, 'update'])
            ->name('update')
            ->middleware('permission:banner_category_update');
        Route::put('/{id}', [BannerCategoryController::class, 'update'])
            ->name('update')
            ->middleware('permission:banner_category_update');
        Route::put('/{id}/publish', [BannerCategoryController::class, 'publish'])
            ->name('publish')
            ->middleware('permission:banner_category_update');
        Route::put('/{id}/approved', [BannerCategoryController::class, 'approved'])
            ->name('approved')
            ->middleware('role:super');
        Route::delete('/{id}/soft', [BannerCategoryController::class, 'softDelete'])
            ->name('delete.soft')
            ->middleware('permission:banner_category_delete');
        Route::delete('/{id}/permanent', [BannerCategoryController::class, 'permanentDelete'])
            ->name('delete.permanent')
            ->middleware('role:super');
        Route::put('/{id}/restore', [BannerCategoryController::class, 'restore'])
            ->name('restore')
            ->middleware('role:super');

    });

    //--- Banner
    Route::prefix('{categoryId}')->group(function() {

        Route::get('/', [BannerController::class, 'index'])
            ->name('index')
            ->middleware('permission:banners');
        Route::get('/trash', [BannerController::class, 'trash'])
            ->name('trash')
            ->middleware('role:super');
            
        Route::get('/create', [BannerController::class, 'create'])
            ->name('create')
            ->middleware('permission:banner_create');
        Route::post('/', [BannerController::class, 'store'])
            ->name('store')
            ->middleware('permission:banner_create');
        Route::post('/multiple', [BannerController::class, 'storeMultiple'])
            ->name('store.multiple')
            ->middleware('permission:banner_create');
        Route::get('/{id}/edit', [BannerController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:banner_update');
        Route::put('/{id}', [BannerController::class, 'update'])
            ->name('update')
            ->middleware('permission:banner_update');
        Route::put('/{id}/publish', [BannerController::class, 'publish'])
            ->name('publish')
            ->middleware('permission:banner_update');
        Route::put('/{id}/approved', [BannerController::class, 'approved'])
            ->name('approved')
            ->middleware('role:super|support|admin');
        Route::put('/{id}/position/{position}', [BannerController::class, 'position'])
            ->name('position')
            ->middleware('permission:banner_update');
        Route::post('/sort', [BannerController::class, 'sort'])
            ->name('sort')
            ->middleware('permission:banner_update');
        Route::delete('/{id}/soft', [BannerController::class, 'softDelete'])
            ->name('delete.soft')
            ->middleware('permission:banner_delete');
        Route::delete('/{id}/permanent', [BannerController::class, 'permanentDelete'])
            ->name('delete.permanent')
            ->middleware('role:super');
        Route::put('/{id}/restore', [BannerController::class, 'restore'])
            ->name('restore')
            ->middleware('role:super');

    });

});
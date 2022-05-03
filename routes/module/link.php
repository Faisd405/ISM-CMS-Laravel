<?php

use App\Http\Controllers\Module\Link\LinkCategoryController;
use App\Http\Controllers\Module\Link\LinkMediaController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin/link')->name('link.')->middleware('auth')->group(function () {

    //--- Category
    Route::prefix('category')->name('category.')->group(function() {

        Route::get('/', [LinkCategoryController::class, 'index'])
            ->name('index')
            ->middleware('permission:link_categories');
        Route::get('/trash', [LinkCategoryController::class, 'trash'])
            ->name('trash')
            ->middleware('role:super');

        Route::get('/create', [LinkCategoryController::class, 'create'])
            ->name('create')
            ->middleware('permission:link_category_create');
        Route::post('/store', [LinkCategoryController::class, 'store'])
            ->name('store')
            ->middleware('permission:link_category_create');
        Route::get('/{id}/edit', [LinkCategoryController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:link_category_update');
        Route::put('/{id}', [LinkCategoryController::class, 'update'])
            ->name('update')
            ->middleware('permission:link_category_update');
        Route::put('/{id}', [LinkCategoryController::class, 'update'])
            ->name('update')
            ->middleware('permission:link_category_update');
        Route::put('/{id}/publish', [LinkCategoryController::class, 'publish'])
            ->name('publish')
            ->middleware('permission:link_category_update');
        Route::put('/{id}/approved', [LinkCategoryController::class, 'approved'])
            ->name('approved')
            ->middleware('role:super|support|admin');
        Route::put('/{id}/position/{position}', [LinkCategoryController::class, 'position'])
            ->name('position')
            ->middleware('permission:link_category_update');
        Route::delete('/{id}/soft', [LinkCategoryController::class, 'softDelete'])
            ->name('delete.soft')
            ->middleware('permission:link_category_delete');
        Route::delete('/{id}/permanent', [LinkCategoryController::class, 'permanentDelete'])
            ->name('delete.permanent')
            ->middleware('role:super');
        Route::put('/{id}/restore', [LinkCategoryController::class, 'restore'])
            ->name('restore')
            ->middleware('role:super');

    });

    //--- Media
    Route::prefix('category/{categoryId}')->name('media.')->group(function() {

        Route::get('/', [LinkMediaController::class, 'index'])
            ->name('index')
            ->middleware('permission:link_medias');
        Route::get('/trash', [LinkMediaController::class, 'trash'])
            ->name('trash')
            ->middleware('role:super');
            
        Route::get('/create', [LinkMediaController::class, 'create'])
            ->name('create')
            ->middleware('permission:link_media_create');
        Route::post('/', [LinkMediaController::class, 'store'])
            ->name('store')
            ->middleware('permission:link_media_create');
        Route::get('/{id}/edit', [LinkMediaController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:link_media_update');
        Route::put('/{id}', [LinkMediaController::class, 'update'])
            ->name('update')
            ->middleware('permission:link_media_update');
        Route::put('/{id}/publish', [LinkMediaController::class, 'publish'])
            ->name('publish')
            ->middleware('permission:link_media_update');
        Route::put('/{id}/approved', [LinkMediaController::class, 'approved'])
            ->name('approved')
            ->middleware('role:super|support|admin');
        Route::put('/{id}/position/{position}', [LinkMediaController::class, 'position'])
            ->name('position')
            ->middleware('permission:link_media_update');
        Route::post('/sort', [LinkMediaController::class, 'sort'])
            ->name('sort')
            ->middleware('permission:link_media_update');
        Route::delete('/{id}/soft', [LinkMediaController::class, 'softDelete'])
            ->name('delete.soft')
            ->middleware('permission:link_media_delete');
        Route::delete('/{id}/permanent', [LinkMediaController::class, 'permanentDelete'])
            ->name('delete.permanent')
            ->middleware('role:super');
        Route::put('/{id}/restore', [LinkMediaController::class, 'restore'])
            ->name('restore')
            ->middleware('role:super');

    });

});

/**
 * Frontend
 */
$group = ['middleware' => ['language']];

if (config('cms.module.feature.language.needLocale')) {
    $group['prefix'] = '{locale?}';
}

Route::group($group, function () {

    //--- List
    Route::get('link', [LinkCategoryController::class, 'list'])
        ->name('link.list');

    //--- Category
    Route::get('link/{slugCategory}', [LinkCategoryController::class, 'read'])
        ->name('link.category.read');

});
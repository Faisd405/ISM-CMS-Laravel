<?php

use App\Http\Controllers\Menu\MenuCategoryController;
use App\Http\Controllers\Menu\MenuController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin/menu')->name('menu.')->middleware('auth')->group(function () {

    //--- Category
    Route::prefix('category')->name('category.')->group(function() {

        Route::get('/', [MenuCategoryController::class, 'index'])
            ->name('index')
            ->middleware('permission:menus');
        Route::get('/trash', [MenuCategoryController::class, 'trash'])
            ->name('trash')
            ->middleware('role:super');

        Route::get('/create', [MenuCategoryController::class, 'create'])
            ->name('create')
            ->middleware('role:super');
        Route::post('/store', [MenuCategoryController::class, 'store'])
            ->name('store')
            ->middleware('role:super');
        Route::get('/{id}/edit', [MenuCategoryController::class, 'edit'])
            ->name('edit')
            ->middleware('role:super');
        Route::put('/{id}', [MenuCategoryController::class, 'update'])
            ->name('update')
            ->middleware('role:super');
        Route::put('/{id}/activate', [MenuCategoryController::class, 'activate'])
            ->name('activate')
            ->middleware('role:super');
        Route::delete('/{id}/soft', [MenuCategoryController::class, 'softDelete'])
            ->name('delete.soft')
            ->middleware('role:super');
        Route::delete('/{id}/permanent', [MenuCategoryController::class, 'permanentDelete'])
            ->name('delete.permanent')
            ->middleware('role:super');
        Route::put('/{id}/restore', [MenuCategoryController::class, 'restore'])
            ->name('restore')
            ->middleware('role:super');

    });

    //--- Menu
    Route::prefix('{categoryId}')->group(function() {

        Route::get('/', [MenuController::class, 'index'])
            ->name('index')
            ->middleware('permission:menus');
        Route::get('/trash', [MenuController::class, 'trash'])
            ->name('trash')
            ->middleware('role:super');
            
        Route::get('/create', [MenuController::class, 'create'])
            ->name('create')
            ->middleware('permission:menu_create');
        Route::post('/store', [MenuController::class, 'store'])
            ->name('store')
            ->middleware('permission:menu_create');
        Route::get('/{id}/edit', [MenuController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:menu_update');
        Route::put('/{id}', [MenuController::class, 'update'])
            ->name('update')
            ->middleware('permission:menu_update');
        Route::put('/{id}/publish', [MenuController::class, 'publish'])
            ->name('publish')
            ->middleware('permission:menu_update');
        Route::put('/{id}/approved', [MenuController::class, 'approved'])
            ->name('approved')
            ->middleware('role:super|support|admin');
        Route::put('/{id}/position/{position}', [MenuController::class, 'position'])
            ->name('position')
            ->middleware('permission:menu_update');
        Route::delete('/{id}/soft', [MenuController::class, 'softDelete'])
            ->name('delete.soft')
            ->middleware('permission:menu_delete');
        Route::delete('/{id}/permanent', [MenuController::class, 'permanentDelete'])
            ->name('delete.permanent')
            ->middleware('role:super');
        Route::put('/{id}/restore', [MenuController::class, 'restore'])
            ->name('restore')
            ->middleware('role:super');

    });
        
});
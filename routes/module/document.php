<?php

use App\Http\Controllers\Module\Document\DocumentCategoryController;
use App\Http\Controllers\Module\Document\DocumentFileController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin/document')->name('document.')->middleware('auth')->group(function () {

    //--- Category
    Route::prefix('category')->name('category.')->group(function() {

        Route::get('/', [DocumentCategoryController::class, 'index'])
            ->name('index')
            ->middleware('permission:document_categories');
        Route::get('/trash', [DocumentCategoryController::class, 'trash'])
            ->name('trash')
            ->middleware('role:super');

        Route::get('/create', [DocumentCategoryController::class, 'create'])
            ->name('create')
            ->middleware('permission:document_category_create');
        Route::post('/store', [DocumentCategoryController::class, 'store'])
            ->name('store')
            ->middleware('permission:document_category_create');
        Route::get('/{id}/edit', [DocumentCategoryController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:document_category_update');
        Route::put('/{id}', [DocumentCategoryController::class, 'update'])
            ->name('update')
            ->middleware('permission:document_category_update');
        Route::put('/{id}', [DocumentCategoryController::class, 'update'])
            ->name('update')
            ->middleware('permission:document_category_update');
        Route::put('/{id}/publish', [DocumentCategoryController::class, 'publish'])
            ->name('publish')
            ->middleware('permission:document_category_update');
        Route::put('/{id}/approved', [DocumentCategoryController::class, 'approved'])
            ->name('approved')
            ->middleware('role:super|support|admin');
        Route::put('/{id}/position/{position}', [DocumentCategoryController::class, 'position'])
            ->name('position')
            ->middleware('permission:document_category_update');
        Route::delete('/{id}/soft', [DocumentCategoryController::class, 'softDelete'])
            ->name('delete.soft')
            ->middleware('permission:document_category_delete');
        Route::delete('/{id}/permanent', [DocumentCategoryController::class, 'permanentDelete'])
            ->name('delete.permanent')
            ->middleware('role:super');
        Route::put('/{id}/restore', [DocumentCategoryController::class, 'restore'])
            ->name('restore')
            ->middleware('role:super');

    });

    //--- File
    Route::prefix('category/{categoryId}')->name('file.')->group(function() {

        Route::get('/', [DocumentFileController::class, 'index'])
            ->name('index')
            ->middleware('permission:document_files');
        Route::get('/trash', [DocumentFileController::class, 'trash'])
            ->name('trash')
            ->middleware('role:super');
            
        Route::get('/create', [DocumentFileController::class, 'create'])
            ->name('create')
            ->middleware('permission:document_file_create');
        Route::post('/', [DocumentFileController::class, 'store'])
            ->name('store')
            ->middleware('permission:document_file_create');
        Route::post('/multiple', [DocumentFileController::class, 'storeMultiple'])
            ->name('store.multiple')
            ->middleware('permission:document_file_create');
        Route::get('/{id}/edit', [DocumentFileController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:document_file_update');
        Route::put('/{id}', [DocumentFileController::class, 'update'])
            ->name('update')
            ->middleware('permission:document_file_update');
        Route::put('/{id}/publish', [DocumentFileController::class, 'publish'])
            ->name('publish')
            ->middleware('permission:document_file_update');
        Route::put('/{id}/approved', [DocumentFileController::class, 'approved'])
            ->name('approved')
            ->middleware('role:super|support|admin');
        Route::put('/{id}/position/{position}', [DocumentFileController::class, 'position'])
            ->name('position')
            ->middleware('permission:document_file_update');
        Route::post('/sort', [DocumentFileController::class, 'sort'])
            ->name('sort')
            ->middleware('permission:document_file_update');
        Route::delete('/{id}/soft', [DocumentFileController::class, 'softDelete'])
            ->name('delete.soft')
            ->middleware('permission:document_file_delete');
        Route::delete('/{id}/permanent', [DocumentFileController::class, 'permanentDelete'])
            ->name('delete.permanent')
            ->middleware('role:super');
        Route::put('/{id}/restore', [DocumentFileController::class, 'restore'])
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
    Route::get('document', [DocumentCategoryController::class, 'list'])
        ->name('document.list');

    //--- Category
    Route::get('document/{slugCategory}', [DocumentCategoryController::class, 'read'])
        ->name('document.category.read');

});

Route::get('/document/{id}/download', [DocumentFileController::class, 'download'])
    ->name('document.download');
<?php

use App\Http\Controllers\Module\Document\DocumentController;
use App\Http\Controllers\Module\Document\DocumentFileController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin/document')->name('document.')->middleware('auth')->group(function () {

    Route::get('/', [DocumentController::class, 'index'])
        ->name('index')
        ->middleware('permission:documents');
    Route::get('/trash', [DocumentController::class, 'trash'])
        ->name('trash')
        ->middleware('role:developer|super');

    Route::get('/create', [DocumentController::class, 'create'])
        ->name('create')
        ->middleware('permission:document_create');
    Route::post('/store', [DocumentController::class, 'store'])
        ->name('store')
        ->middleware('permission:document_create');
    Route::get('/{id}/edit', [DocumentController::class, 'edit'])
        ->name('edit')
        ->middleware('permission:document_update');
    Route::put('/{id}', [DocumentController::class, 'update'])
        ->name('update')
        ->middleware('permission:document_update');
    Route::put('/{id}', [DocumentController::class, 'update'])
        ->name('update')
        ->middleware('permission:document_update');
    Route::put('/{id}/publish', [DocumentController::class, 'publish'])
        ->name('publish')
        ->middleware('permission:document_update');
    Route::put('/{id}/approved', [DocumentController::class, 'approved'])
        ->name('approved')
        ->middleware('role:developer|super|support|admin');
    Route::post('/sort', [DocumentController::class, 'sort'])
        ->name('sort')
        ->middleware('permission:document_update');
    Route::put('/{id}/position/{position}', [DocumentController::class, 'position'])
        ->name('position')
        ->middleware('permission:document_update');
    Route::delete('/{id}/soft', [DocumentController::class, 'softDelete'])
        ->name('delete.soft')
        ->middleware('permission:document_delete');
    Route::delete('/{id}/permanent', [DocumentController::class, 'permanentDelete'])
        ->name('delete.permanent')
        ->middleware('role:developer|super');
    Route::put('/{id}/restore', [DocumentController::class, 'restore'])
        ->name('restore')
        ->middleware('role:developer|super');

    //--- File
    Route::prefix('{documentId}/file')->name('file.')->group(function() {

        Route::get('/', [DocumentFileController::class, 'index'])
            ->name('index')
            ->middleware('permission:document_files');
        Route::get('/trash', [DocumentFileController::class, 'trash'])
            ->name('trash')
            ->middleware('role:developer|super');
            
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
            ->middleware('role:developer|super|support|admin');
        Route::post('/sort', [DocumentFileController::class, 'sort'])
            ->name('sort')
            ->middleware('permission:document_file_update');
        Route::put('/{id}/position/{position}', [DocumentFileController::class, 'position'])
            ->name('position')
            ->middleware('permission:document_file_update');
        Route::delete('/{id}/soft', [DocumentFileController::class, 'softDelete'])
            ->name('delete.soft')
            ->middleware('permission:document_file_delete');
        Route::delete('/{id}/permanent', [DocumentFileController::class, 'permanentDelete'])
            ->name('delete.permanent')
            ->middleware('role:developer|super');
        Route::put('/{id}/restore', [DocumentFileController::class, 'restore'])
            ->name('restore')
            ->middleware('role:developer|super');

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
    Route::get('document', [DocumentController::class, 'list'])
        ->name('document.list');

    //--- Category
    Route::get('document/{slugDocument}', [DocumentController::class, 'read'])
        ->name('document.read');

});

Route::get('/document/{id}/download', [DocumentFileController::class, 'download'])
    ->name('document.download');
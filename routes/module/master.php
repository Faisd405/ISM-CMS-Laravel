<?php

use App\Http\Controllers\Master\MediaController;
use App\Http\Controllers\Master\TagController;
use App\Http\Controllers\Master\TemplateController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->middleware('auth')->group(function () {

    //--- Template
    Route::prefix('template')->name('template.')->group(function () {

        Route::get('/', [TemplateController::class, 'index'])
            ->name('index')
            ->middleware('permission:templates');
        Route::get('/trash', [TemplateController::class, 'trash'])
            ->name('trash')
            ->middleware('role:super');

        Route::get('/create', [TemplateController::class, 'create'])
            ->name('create')
            ->middleware('permission:template_create');
        Route::post('/store', [TemplateController::class, 'store'])
            ->name('store')
            ->middleware('permission:template_create');
        Route::get('/{id}/edit', [TemplateController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:template_update');
        Route::put('/{id}', [TemplateController::class, 'update'])
            ->name('update')
            ->middleware('permission:template_update');
        Route::delete('/{id}/soft', [TemplateController::class, 'softDelete'])
            ->name('delete.soft')
            ->middleware('permission:template_delete');
        Route::delete('/{id}/permanent', [TemplateController::class, 'permanentDelete'])
            ->name('delete.permanent')
            ->middleware('role:super');
        Route::put('/{id}/restore', [TemplateController::class, 'restore'])
            ->name('restore')
            ->middleware('role:super');

    });

    //--- Tags
    Route::prefix('tags')->name('tags.')->group(function () {

        Route::get('/', [TagController::class, 'index'])
            ->name('index')
            ->middleware('permission:tags');
        Route::get('/trash', [TagController::class, 'trash'])
            ->name('trash')
            ->middleware('role:super');

        Route::get('/create', [TagController::class, 'create'])
            ->name('create')
            ->middleware('permission:tag_create');
        Route::post('/store', [TagController::class, 'store'])
            ->name('store')
            ->middleware('permission:tag_create');
        Route::get('/{id}/edit', [TagController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:tag_update');
        Route::put('/{id}', [TagController::class, 'update'])
            ->name('update')
            ->middleware('permission:tag_update');
        Route::put('/{id}/flags', [TagController::class, 'flags'])
            ->name('flags')
            ->middleware('permission:tag_update');
        Route::put('/{id}/standar', [TagController::class, 'standar'])
            ->name('standar')
            ->middleware('permission:tag_update');
        Route::delete('/{id}/soft', [TagController::class, 'softDelete'])
            ->name('delete.soft')
            ->middleware('permission:tag_delete');
        Route::delete('/{id}/permanent', [TagController::class, 'permanentDelete'])
            ->name('delete.permanent')
            ->middleware('role:super');
        Route::put('/{id}/restore', [TagController::class, 'restore'])
            ->name('restore')
            ->middleware('role:super');

    });

    //--- Media
    Route::prefix('media/{moduleId}/{moduleType}')->name('media.')->group(function () {
        
        Route::get('/', [MediaController::class, 'index'])
            ->name('index')
            ->middleware('permission:medias');
        Route::get('/trash', [MediaController::class, 'trash'])
            ->name('trash')
            ->middleware('role:super');

        Route::get('/create', [MediaController::class, 'create'])
            ->name('create')
            ->middleware('permission:media_create');
        Route::post('/', [MediaController::class, 'store'])
            ->name('store')
            ->middleware('permission:media_create');
        Route::get('/{id}/edit', [MediaController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:media_update');
        Route::put('/{id}', [MediaController::class, 'update'])
            ->name('update')
            ->middleware('permission:media_update');
        Route::put('{id}/position/{position}', [MediaController::class, 'position'])
            ->name('position')
            ->middleware('permission:media_update');
        Route::delete('/{id}/soft', [MediaController::class, 'softDelete'])
            ->name('delete.soft')
            ->middleware('permission:media_delete');
        Route::delete('/{id}/permanent', [MediaController::class, 'permanentDelete'])
            ->name('delete.permanent')
            ->middleware('role:super');
        Route::put('/{id}/restore', [MediaController::class, 'restore'])
            ->name('restore')
            ->middleware('role:super');

    });

});
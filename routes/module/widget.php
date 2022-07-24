<?php

use App\Http\Controllers\Module\WidgetController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin/widget')->name('widget.')->middleware('auth')->group(function () {


    Route::get('/', [WidgetController::class, 'index'])
        ->name('index')
        ->middleware('permission:widgets');
    Route::get('/trash', [WidgetController::class, 'trash'])
        ->name('trash')
        ->middleware('role:developer|super');

    Route::get('/create/{type}', [WidgetController::class, 'create'])
        ->name('create')
        ->middleware('permission:widget_create');
    Route::post('/store/{type}', [WidgetController::class, 'store'])
        ->name('store')
        ->middleware('permission:widget_create');

    Route::put('/{id}/publish', [WidgetController::class, 'publish'])
        ->name('publish')
        ->middleware('permission:widget_update');
    Route::put('/{id}/approved', [WidgetController::class, 'approved'])
        ->name('approved')
        ->middleware('role:developer|super');
    Route::post('/sort', [WidgetController::class, 'sort'])
        ->name('sort')
        ->middleware('permission:widget_update');
    Route::put('/{id}/position/{position}', [WidgetController::class, 'position'])
        ->name('position')
        ->middleware('permission:widget_update');
    Route::delete('/{id}/soft', [WidgetController::class, 'softDelete'])
        ->name('delete.soft')
        ->middleware('permission:widget_delete');
    Route::delete('/{id}/permanent', [WidgetController::class, 'permanentDelete'])
        ->name('delete.permanent')
        ->middleware('role:developer|super');
    Route::put('/{id}/restore', [WidgetController::class, 'restore'])
        ->name('restore')
        ->middleware('role:developer|super');

    Route::get('{type}/{id}/edit', [WidgetController::class, 'edit'])
        ->name('edit')
        ->middleware('permission:widget_update');
    Route::put('{type}/{id}', [WidgetController::class, 'update'])
        ->name('update')
        ->middleware('permission:widget_update');

});
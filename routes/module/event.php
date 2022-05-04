<?php

use App\Http\Controllers\Module\Event\EventController;
use App\Http\Controllers\Module\Event\EventFieldController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin/event')->name('event.')->middleware('auth')->group(function () {

    //--- Event
    Route::get('/', [EventController::class, 'index'])
        ->name('index')
        ->middleware('permission:events');
    Route::get('/trash', [EventController::class, 'trash'])
        ->name('trash')
        ->middleware('role:super');

    Route::get('/create', [EventController::class, 'create'])
        ->name('create')
        ->middleware('permission:event_create');
    Route::post('/', [EventController::class, 'store'])
        ->name('store')
        ->middleware('permission:event_create');
    Route::get('/{id}/edit', [EventController::class, 'edit'])
        ->name('edit')
        ->middleware('permission:event_update');
    Route::put('/{id}/update', [EventController::class, 'update'])
        ->name('update')
        ->middleware('permission:event_update');
    Route::put('/{id}/publish', [EventController::class, 'publish'])
        ->name('publish')
        ->middleware('permission:event_update');
    Route::put('/{id}/approved', [EventController::class, 'approved'])
        ->name('approved')
        ->middleware('role:super|support|admin');
    Route::put('/{id}/position/{position}', [EventController::class, 'position'])
        ->name('position')
        ->middleware('permission:event_update');
    Route::delete('/{id}/soft', [EventController::class, 'softDelete'])
        ->name('delete.soft')
        ->middleware('permission:event_delete');
    Route::delete('/{id}/permanent', [EventController::class, 'permanentDelete'])
        ->name('delete.permanent')
        ->middleware('role:super');
    Route::put('/{id}/restore', [EventController::class, 'restore'])
        ->name('restore')
        ->middleware('role:super');

    //--- Form
    Route::get('/{eventId}/form', [EventController::class, 'form'])
        ->name('form')
        ->middleware('permission:inquiries');
    Route::post('/{eventId}/export', [EventController::class, 'exportForm'])
        ->name('form.export')
        ->middleware('permission:inquiries');
    Route::put('/{eventId}/form/{id}/status', [EventController::class, 'statusForm'])
        ->name('form.status')
        ->middleware('permission:inquiries');
    Route::delete('/{eventId}/form/{id}', [EventController::class, 'destroyForm'])
        ->name('form.destroy')
        ->middleware('permission:inquiries');

    //--- Field
    Route::prefix('{eventId}/field')->name('field.')->group(function () {
        
        Route::get('/', [EventFieldController::class, 'index'])
            ->name('index')
            ->middleware('permission:event_fields');
        Route::get('/trash', [EventFieldController::class, 'trash'])
            ->name('trash')
            ->middleware('role:super');

        Route::get('/create', [EventFieldController::class, 'create'])
            ->name('create')
            ->middleware('permission:event_field_create');
        Route::post('/', [EventFieldController::class, 'store'])
            ->name('store')
            ->middleware('permission:event_field_create');
        Route::get('/{id}/edit', [EventFieldController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:event_field_update');
        Route::put('/{id}/update', [EventFieldController::class, 'update'])
            ->name('update')
            ->middleware('permission:event_field_update');
        Route::put('/{id}/publish', [EventFieldController::class, 'publish'])
            ->name('publish')
            ->middleware('permission:event_field_update');
        Route::put('/{id}/approved', [EventFieldController::class, 'approved'])
            ->name('approved')
            ->middleware('role:super|support|admin');
        Route::put('/{id}/position/{position}', [EventFieldController::class, 'position'])
            ->name('position')
            ->middleware('permission:event_field_update');
        Route::delete('/{id}/soft', [EventFieldController::class, 'softDelete'])
            ->name('delete.soft')
            ->middleware('permission:event_field_delete');
        Route::delete('/{id}/permanent', [EventFieldController::class, 'permanentDelete'])
            ->name('delete.permanent')
            ->middleware('role:super');
        Route::put('/{id}/restore', [EventFieldController::class, 'restore'])
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

    //event
    Route::get('/event', [EventController::class, 'list'])
        ->name('event.list');

    Route::get('event/{slugEvent}', [EventController::class, 'read'])
        ->name('event.read');
});

//submit form
Route::post('/event/{id}/submit', [EventController::class, 'submitForm'])
    ->name('event.submit');
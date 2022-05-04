<?php

use App\Http\Controllers\Module\Inquiry\InquiryController;
use App\Http\Controllers\Module\Inquiry\InquiryFieldController;
use App\Models\IndexingUrl;
use Illuminate\Support\Facades\Route;

Route::prefix('admin/inquiry')->name('inquiry.')->middleware('auth')->group(function () {

    //--- Inquiry
    Route::get('/', [InquiryController::class, 'index'])
        ->name('index')
        ->middleware('permission:inquiries');
    Route::get('/trash', [InquiryController::class, 'trash'])
        ->name('trash')
        ->middleware('role:super');

    Route::get('/create', [InquiryController::class, 'create'])
        ->name('create')
        ->middleware('permission:inquiry_create');
    Route::post('/', [InquiryController::class, 'store'])
        ->name('store')
        ->middleware('permission:inquiry_create');
    Route::get('/{id}/edit', [InquiryController::class, 'edit'])
        ->name('edit')
        ->middleware('permission:inquiry_update');
    Route::put('/{id}/update', [InquiryController::class, 'update'])
        ->name('update')
        ->middleware('permission:inquiry_update');
    Route::put('/{id}/publish', [InquiryController::class, 'publish'])
        ->name('publish')
        ->middleware('permission:inquiry_update');
    Route::put('/{id}/approved', [InquiryController::class, 'approved'])
        ->name('approved')
        ->middleware('role:super|support|admin');
    Route::put('/{id}/position/{position}', [InquiryController::class, 'position'])
        ->name('position')
        ->middleware('permission:inquiry_update');
    Route::delete('/{id}/soft', [InquiryController::class, 'softDelete'])
        ->name('delete.soft')
        ->middleware('permission:inquiry_delete');
    Route::delete('/{id}/permanent', [InquiryController::class, 'permanentDelete'])
        ->name('delete.permanent')
        ->middleware('role:super');
    Route::put('/{id}/restore', [InquiryController::class, 'restore'])
        ->name('restore')
        ->middleware('role:super');

    //--- Form
    Route::get('/{inquiryId}/form', [InquiryController::class, 'form'])
        ->name('form')
        ->middleware('permission:inquiries');
    Route::post('/{inquiryId}/export', [InquiryController::class, 'exportForm'])
        ->name('form.export')
        ->middleware('permission:inquiries');
    Route::put('/{inquiryId}/form/{id}/status', [InquiryController::class, 'statusForm'])
        ->name('form.status')
        ->middleware('permission:inquiries');
    Route::delete('/{inquiryId}/form/{id}', [InquiryController::class, 'destroyForm'])
        ->name('form.destroy')
        ->middleware('permission:inquiries');

    //--- Field
    Route::prefix('{inquiryId}/field')->name('field.')->group(function () {
        
        Route::get('/', [InquiryFieldController::class, 'index'])
            ->name('index')
            ->middleware('permission:inquiry_fields');
        Route::get('/trash', [InquiryFieldController::class, 'trash'])
            ->name('trash')
            ->middleware('role:super');

        Route::get('/create', [InquiryFieldController::class, 'create'])
            ->name('create')
            ->middleware('permission:inquiry_field_create');
        Route::post('/', [InquiryFieldController::class, 'store'])
            ->name('store')
            ->middleware('permission:inquiry_field_create');
        Route::get('/{id}/edit', [InquiryFieldController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:inquiry_field_update');
        Route::put('/{id}/update', [InquiryFieldController::class, 'update'])
            ->name('update')
            ->middleware('permission:inquiry_field_update');
        Route::put('/{id}/publish', [InquiryFieldController::class, 'publish'])
            ->name('publish')
            ->middleware('permission:inquiry_field_update');
        Route::put('/{id}/approved', [InquiryFieldController::class, 'approved'])
            ->name('approved')
            ->middleware('role:super|support|admin');
        Route::put('/{id}/position/{position}', [InquiryFieldController::class, 'position'])
            ->name('position')
            ->middleware('permission:inquiry_field_update');
        Route::delete('/{id}/soft', [InquiryFieldController::class, 'softDelete'])
            ->name('delete.soft')
            ->middleware('permission:inquiry_field_delete');
        Route::delete('/{id}/permanent', [InquiryFieldController::class, 'permanentDelete'])
            ->name('delete.permanent')
            ->middleware('role:super');
        Route::put('/{id}/restore', [InquiryFieldController::class, 'restore'])
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

    //inquiry
    Route::get('/inquiry', [InquiryController::class, 'list'])
        ->name('inquiry.list');

    if (config('cms.setting.index_url') == true) {
        $indexing = IndexingUrl::where('module', 'inquiry')->get();
        if ($indexing->count() > 0) {
            foreach ($indexing as $key => $value) {
                Route::get($value['slug'], [InquiryController::class, 'read'])
                    ->name('inquiry.read.'.$value['slug']);
            }
        }
    }
});

//submit form
Route::post('/inquiry/{id}/submit', [InquiryController::class, 'submitForm'])
    ->name('inquiry.submit');
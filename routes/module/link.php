<?php

use App\Http\Controllers\Module\Link\LinkController;
use App\Http\Controllers\Module\Link\LinkMediaController;
use App\Models\IndexingUrl;
use Illuminate\Support\Facades\Route;

Route::prefix('admin/link')->name('link.')->middleware('auth')->group(function () {

    Route::get('/', [LinkController::class, 'index'])
        ->name('index')
        ->middleware('permission:links');
    Route::get('/trash', [LinkController::class, 'trash'])
        ->name('trash')
        ->middleware('role:developer|super');

    Route::get('/create', [LinkController::class, 'create'])
        ->name('create')
        ->middleware('permission:link_create');
    Route::post('/store', [LinkController::class, 'store'])
        ->name('store')
        ->middleware('permission:link_create');
    Route::get('/{id}/edit', [LinkController::class, 'edit'])
        ->name('edit')
        ->middleware('permission:link_update');
    Route::put('/{id}', [LinkController::class, 'update'])
        ->name('update')
        ->middleware('permission:link_update');
    Route::put('/{id}', [LinkController::class, 'update'])
        ->name('update')
        ->middleware('permission:link_update');
    Route::put('/{id}/publish', [LinkController::class, 'publish'])
        ->name('publish')
        ->middleware('permission:link_update');
    Route::put('/{id}/approved', [LinkController::class, 'approved'])
        ->name('approved')
        ->middleware('role:developer|super|support|admin');
    Route::post('/sort', [LinkController::class, 'sort'])
        ->name('sort')
        ->middleware('permission:link_update');
    Route::put('/{id}/position/{position}', [LinkController::class, 'position'])
        ->name('position')
        ->middleware('permission:link_update');
    Route::delete('/{id}/soft', [LinkController::class, 'softDelete'])
        ->name('delete.soft')
        ->middleware('permission:link_delete');
    Route::delete('/{id}/permanent', [LinkController::class, 'permanentDelete'])
        ->name('delete.permanent')
        ->middleware('role:developer|super');
    Route::put('/{id}/restore', [LinkController::class, 'restore'])
        ->name('restore')
        ->middleware('role:developer|super');

    //--- Media
    Route::prefix('{linkId}/media')->name('media.')->group(function() {

        Route::get('/', [LinkMediaController::class, 'index'])
            ->name('index')
            ->middleware('permission:link_medias');
        Route::get('/trash', [LinkMediaController::class, 'trash'])
            ->name('trash')
            ->middleware('role:developer|super');

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
            ->middleware('role:developer|super|support|admin');
        Route::post('/sort', [LinkMediaController::class, 'sort'])
            ->name('sort')
            ->middleware('permission:link_media_update');
        Route::put('/{id}/position/{position}', [LinkMediaController::class, 'position'])
            ->name('position')
            ->middleware('permission:link_media_update');
        Route::delete('/{id}/soft', [LinkMediaController::class, 'softDelete'])
            ->name('delete.soft')
            ->middleware('permission:link_media_delete');
        Route::delete('/{id}/permanent', [LinkMediaController::class, 'permanentDelete'])
            ->name('delete.permanent')
            ->middleware('role:developer|super');
        Route::put('/{id}/restore', [LinkMediaController::class, 'restore'])
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
    Route::get('link', [LinkController::class, 'list'])
        ->name('link.list');

    if (config('cms.setting.index_url') == true && !App::runningInConsole()) {
        $indexing = IndexingUrl::where('module', 'link')->get();
        if ($indexing->count() > 0) {
            foreach ($indexing as $key => $value) {
                Route::get($value['slug'], [LinkController::class, 'read'])
                    ->name('link.read.'.$value['slug']);
            }
        }
    }

});

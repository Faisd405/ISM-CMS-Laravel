<?php

use App\Http\Controllers\Module\PageController;
use App\Models\IndexingUrl;
use Illuminate\Support\Facades\Route;

Route::prefix('admin/page')->name('page.')->middleware('auth')->group(function () {

    Route::get('/', [PageController::class, 'index'])
        ->name('index')
        ->middleware('permission:pages');
    Route::get('/trash', [PageController::class, 'trash'])
        ->name('trash')
        ->middleware('role:super');
        
    Route::get('/create', [PageController::class, 'create'])
        ->name('create')
        ->middleware('permission:page_create');
    Route::post('/store', [PageController::class, 'store'])
        ->name('store')
        ->middleware('permission:page_create');
    Route::get('/{id}/edit', [PageController::class, 'edit'])
        ->name('edit')
        ->middleware('permission:page_update');
    Route::put('/{id}', [PageController::class, 'update'])
        ->name('update')
        ->middleware('permission:page_update');
    Route::put('/{id}/publish', [PageController::class, 'publish'])
        ->name('publish')
        ->middleware('permission:page_update');
    Route::put('/{id}/approved', [PageController::class, 'approved'])
        ->name('approved')
        ->middleware('role:super|support|admin');
    Route::put('/{id}/position/{position}', [PageController::class, 'position'])
        ->name('position')
        ->middleware('permission:page_update');
    Route::delete('/{id}/soft', [PageController::class, 'softDelete'])
        ->name('delete.soft')
        ->middleware('permission:page_delete');
    Route::delete('/{id}/permanent', [PageController::class, 'permanentDelete'])
        ->name('delete.permanent')
        ->middleware('role:super');
    Route::put('/{id}/restore', [PageController::class, 'restore'])
        ->name('restore')
        ->middleware('role:super');

});

/**
 * Frontend
 */
$group = ['middleware' => ['language']];
if (config('cms.module.feature.language.needLocale')) {
    $group['prefix'] = '{locale?}';
}

Route::group($group, function () {

    Route::get('/page', [PageController::class, 'list'])
        ->name('page.list');
    
    if (config('cms.setting.index_url') == true) {
        $indexing = IndexingUrl::where('module', 'page')->get();
        if ($indexing->count() > 0) {
            foreach ($indexing as $key => $value) {
                Route::get($value['slug'], [PageController::class, 'read'])
                    ->name('page.read.'.$value['slug']);
            }
        }
    }
        
});
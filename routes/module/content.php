<?php

use App\Http\Controllers\Module\Content\ContentCategoryController;
use App\Http\Controllers\Module\Content\ContentPostController;
use App\Http\Controllers\Module\Content\ContentSectionController;
use App\Models\IndexingUrl;
use Illuminate\Support\Facades\Route;

Route::prefix('admin/content')->name('content.')->middleware('auth')->group(function () {

    //--- Section
    Route::prefix('section')->name('section.')->group(function () {

        Route::get('/', [ContentSectionController::class, 'index'])
            ->name('index')
            ->middleware('permission:content_sections');
        Route::get('/trash', [ContentSectionController::class, 'trash'])
            ->name('trash')
            ->middleware('role:super');

        Route::get('/create', [ContentSectionController::class, 'create'])
            ->name('create')
            ->middleware('permission:content_section_create');
        Route::post('/store', [ContentSectionController::class, 'store'])
            ->name('store')
            ->middleware('permission:content_section_create');
        Route::get('/{id}/edit', [ContentSectionController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:content_section_update');
        Route::put('/{id}', [ContentSectionController::class, 'update'])
            ->name('update')
            ->middleware('permission:content_section_update');
        Route::put('/{id}/publish', [ContentSectionController::class, 'publish'])
            ->name('publish')
            ->middleware('permission:content_section_update');
        Route::put('/{id}/approved', [ContentSectionController::class, 'approved'])
            ->name('approved')
            ->middleware('role:super');
        Route::put('/{id}/position/{position}', [ContentSectionController::class, 'position'])
            ->name('position')
            ->middleware('permission:content_section_update');
        Route::delete('/{id}/soft', [ContentSectionController::class, 'softDelete'])
            ->name('delete.soft')
            ->middleware('permission:content_section_delete');
        Route::delete('/{id}/permanent', [ContentSectionController::class, 'permanentDelete'])
            ->name('delete.permanent')
            ->middleware('role:super');
        Route::put('/{id}/restore', [ContentSectionController::class, 'restore'])
            ->name('restore')
            ->middleware('role:super');

    });

    //--- Category
    Route::prefix('{sectionId}/category')->name('category.')->group(function () {

        Route::get('/', [ContentCategoryController::class, 'index'])
            ->name('index')
            ->middleware('permission:content_categories');
        Route::get('/trash', [ContentCategoryController::class, 'trash'])
            ->name('trash')
            ->middleware('role:super');
        
        Route::get('/create', [ContentCategoryController::class, 'create'])
            ->name('create')
            ->middleware('permission:content_category_create');
        Route::post('/store', [ContentCategoryController::class, 'store'])
            ->name('store')
            ->middleware('permission:content_category_create');
        Route::get('/{id}/edit', [ContentCategoryController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:content_category_update');
        Route::put('/{id}', [ContentCategoryController::class, 'update'])
            ->name('update')
            ->middleware('permission:content_category_update');
        Route::put('/{id}/publish', [ContentCategoryController::class, 'publish'])
            ->name('publish')
            ->middleware('permission:content_category_update');
        Route::put('/{id}/approved', [ContentCategoryController::class, 'approved'])
            ->name('approved')
            ->middleware('role:super|support|admin');
        Route::put('/{id}/position/{position}', [ContentCategoryController::class, 'position'])
            ->name('position')
            ->middleware('permission:content_category_update');
        Route::delete('/{id}/soft', [ContentCategoryController::class, 'softDelete'])
            ->name('delete.soft')
            ->middleware('permission:content_category_delete');
        Route::delete('/{id}/permanent', [ContentCategoryController::class, 'permanentDelete'])
            ->name('delete.permanent')
            ->middleware('role:super');
        Route::put('/{id}/restore', [ContentCategoryController::class, 'restore'])
            ->name('restore')
            ->middleware('role:super');
            
    });

    //--- Post
    Route::prefix('{sectionId}/post')->name('post.')->group(function () {

        Route::get('/', [ContentPostController::class, 'index'])
            ->name('index')
            ->middleware('permission:content_posts');
        Route::get('/trash', [ContentPostController::class, 'trash'])
            ->name('trash')
            ->middleware('role:super');
        
        Route::get('/create', [ContentPostController::class, 'create'])
            ->name('create')
            ->middleware('permission:content_post_create');
        Route::post('/store', [ContentPostController::class, 'store'])
            ->name('store')
            ->middleware('permission:content_post_create');
        Route::get('/{id}/edit', [ContentPostController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:content_post_update');
        Route::put('/{id}', [ContentPostController::class, 'update'])
            ->name('update')
            ->middleware('permission:content_post_update');
        Route::put('/{id}/publish', [ContentPostController::class, 'publish'])
            ->name('publish')
            ->middleware('permission:content_post_update');
        Route::put('/{id}/selected', [ContentPostController::class, 'selected'])
            ->name('selected')
            ->middleware('permission:content_post_update');
        Route::put('/{id}/approved', [ContentPostController::class, 'approved'])
            ->name('approved')
            ->middleware('role:super|support|admin');
        Route::put('/{id}/position/{position}', [ContentPostController::class, 'position'])
            ->name('position')
            ->middleware('permission:content_post_update');
        Route::delete('/{id}/soft', [ContentPostController::class, 'softDelete'])
            ->name('delete.soft')
            ->middleware('permission:content_post_delete');
        Route::delete('/{id}/permanent', [ContentPostController::class, 'permanentDelete'])
            ->name('delete.permanent')
            ->middleware('role:super');
        Route::put('/{id}/restore', [ContentPostController::class, 'restore'])
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

    //--- Section
    Route::get('content/section', [ContentSectionController::class, 'list'])
        ->name('content.section.list');
    //--- Category
    Route::get('content/cat', [ContentCategoryController::class, 'list'])
        ->name('content.category.list');
    //--- Post
    Route::get('content/post', [ContentPostController::class, 'list'])
        ->name('content.post.list');

    if (config('cms.setting.index_url') == true) {
        $indexing = IndexingUrl::where('module', 'content_section')->get();
        if ($indexing->count() > 0) {
            foreach ($indexing as $key => $value) {

                //--- Section
                Route::get($value['slug'], [ContentSectionController::class, 'read'])
                    ->name('content.section.read.'.$value['slug']);

                //--- Category
                Route::get($value['slug'].'/cat/{slugCategory}', [ContentCategoryController::class, 'read'])
                    ->name('content.category.read.'.$value['slug']);

                //--- Post
                Route::get($value['slug'].'/{slugPost}', [ContentPostController::class, 'read'])
                    ->name('content.post.read.'.$value['slug']);
            }
        }
    }

});
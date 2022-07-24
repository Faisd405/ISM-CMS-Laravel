<?php

use App\Http\Controllers\Module\Gallery\GalleryAlbumController;
use App\Http\Controllers\Module\Gallery\GalleryCategoryController;
use App\Http\Controllers\Module\Gallery\GalleryFileController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin/gallery')->name('gallery.')->middleware('auth')->group(function () {

    //--- Category
    Route::prefix('category')->name('category.')->group(function() {

        Route::get('/', [GalleryCategoryController::class, 'index'])
            ->name('index')
            ->middleware('permission:gallery_categories');
        Route::get('/trash', [GalleryCategoryController::class, 'trash'])
            ->name('trash')
            ->middleware('role:developer|super');

        Route::get('/create', [GalleryCategoryController::class, 'create'])
            ->name('create')
            ->middleware('permission:gallery_category_create');
        Route::post('/store', [GalleryCategoryController::class, 'store'])
            ->name('store')
            ->middleware('permission:gallery_category_create');
        Route::get('/{id}/edit', [GalleryCategoryController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:gallery_category_update');
        Route::put('/{id}', [GalleryCategoryController::class, 'update'])
            ->name('update')
            ->middleware('permission:gallery_category_update');
        Route::put('/{id}', [GalleryCategoryController::class, 'update'])
            ->name('update')
            ->middleware('permission:gallery_category_update');
        Route::put('/{id}/publish', [GalleryCategoryController::class, 'publish'])
            ->name('publish')
            ->middleware('permission:gallery_category_update');
        Route::put('/{id}/approved', [GalleryCategoryController::class, 'approved'])
            ->name('approved')
            ->middleware('role:developer|super|support|admin');
        Route::post('/sort', [GalleryCategoryController::class, 'sort'])
            ->name('sort')
            ->middleware('permission:gallery_category_update');
        Route::put('/{id}/position/{position}', [GalleryCategoryController::class, 'position'])
            ->name('position')
            ->middleware('permission:gallery_category_update');
        Route::delete('/{id}/soft', [GalleryCategoryController::class, 'softDelete'])
            ->name('delete.soft')
            ->middleware('permission:gallery_category_delete');
        Route::delete('/{id}/permanent', [GalleryCategoryController::class, 'permanentDelete'])
            ->name('delete.permanent')
            ->middleware('role:developer|super');
        Route::put('/{id}/restore', [GalleryCategoryController::class, 'restore'])
            ->name('restore')
            ->middleware('role:developer|super');

    });

    //--- Album
    Route::prefix('album')->name('album.')->group(function() {

        Route::get('/', [GalleryAlbumController::class, 'index'])
            ->name('index')
            ->middleware('permission:gallery_albums');
        Route::get('/trash', [GalleryAlbumController::class, 'trash'])
            ->name('trash')
            ->middleware('role:developer|super');

        Route::get('/create', [GalleryAlbumController::class, 'create'])
            ->name('create')
            ->middleware('permission:gallery_album_create');
        Route::post('/store', [GalleryAlbumController::class, 'store'])
            ->name('store')
            ->middleware('permission:gallery_album_create');
        Route::get('/{id}/edit', [GalleryAlbumController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:gallery_album_update');
        Route::put('/{id}', [GalleryAlbumController::class, 'update'])
            ->name('update')
            ->middleware('permission:gallery_album_update');
        Route::put('/{id}', [GalleryAlbumController::class, 'update'])
            ->name('update')
            ->middleware('permission:gallery_album_update');
        Route::put('/{id}/publish', [GalleryAlbumController::class, 'publish'])
            ->name('publish')
            ->middleware('permission:gallery_album_update');
        Route::put('/{id}/approved', [GalleryAlbumController::class, 'approved'])
            ->name('approved')
            ->middleware('role:developer|super|support|admin');
        Route::post('/sort', [GalleryAlbumController::class, 'sort'])
            ->name('sort')
            ->middleware('permission:gallery_album_update');
        Route::put('/{id}/position/{position}', [GalleryAlbumController::class, 'position'])
            ->name('position')
            ->middleware('permission:gallery_album_update');
        Route::delete('/{id}/soft', [GalleryAlbumController::class, 'softDelete'])
            ->name('delete.soft')
            ->middleware('permission:gallery_album_delete');
        Route::delete('/{id}/permanent', [GalleryAlbumController::class, 'permanentDelete'])
            ->name('delete.permanent')
            ->middleware('role:developer|super');
        Route::put('/{id}/restore', [GalleryAlbumController::class, 'restore'])
            ->name('restore')
            ->middleware('role:developer|super');

    });

    //--- File
    Route::prefix('album/{albumId}/file')->name('file.')->group(function() {

        Route::get('/', [GalleryFileController::class, 'index'])
            ->name('index')
            ->middleware('permission:gallery_files');
        Route::get('/trash', [GalleryFileController::class, 'trash'])
            ->name('trash')
            ->middleware('role:developer|super');
            
        Route::get('/create', [GalleryFileController::class, 'create'])
            ->name('create')
            ->middleware('permission:gallery_file_create');
        Route::post('/', [GalleryFileController::class, 'store'])
            ->name('store')
            ->middleware('permission:gallery_file_create');
        Route::post('/multiple', [GalleryFileController::class, 'storeMultiple'])
            ->name('store.multiple')
            ->middleware('permission:gallery_file_create');
        Route::get('/{id}/edit', [GalleryFileController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:gallery_file_update');
        Route::put('/{id}', [GalleryFileController::class, 'update'])
            ->name('update')
            ->middleware('permission:gallery_file_update');
        Route::put('/{id}/publish', [GalleryFileController::class, 'publish'])
            ->name('publish')
            ->middleware('permission:gallery_file_update');
        Route::put('/{id}/approved', [GalleryFileController::class, 'approved'])
            ->name('approved')
            ->middleware('role:developer|super|support|admin');
        Route::post('/sort', [GalleryFileController::class, 'sort'])
            ->name('sort')
            ->middleware('permission:gallery_file_update');
        Route::put('/{id}/position/{position}', [GalleryFileController::class, 'position'])
            ->name('position')
            ->middleware('permission:gallery_file_update');
        Route::delete('/{id}/soft', [GalleryFileController::class, 'softDelete'])
            ->name('delete.soft')
            ->middleware('permission:gallery_file_delete');
        Route::delete('/{id}/permanent', [GalleryFileController::class, 'permanentDelete'])
            ->name('delete.permanent')
            ->middleware('role:developer|super');
        Route::put('/{id}/restore', [GalleryFileController::class, 'restore'])
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
    Route::get('gallery', [GalleryCategoryController::class, 'list'])
        ->name('gallery.list');

    //--- Category
    Route::get('gallery/cat/{slugCategory}', [GalleryCategoryController::class, 'read'])
        ->name('gallery.category.read');

    //--- Album
    Route::get('gallery/{slugAlbum}', [GalleryAlbumController::class, 'read'])
        ->name('gallery.album.read');

});
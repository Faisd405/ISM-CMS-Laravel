<?php

use App\Http\Controllers\Api\ApiMenuController;
use Illuminate\Support\Facades\Route;

Route::prefix('menu/module')->group(function () {

    //--- Page
    Route::get('page', [ApiMenuController::class, 'pageList']);

    //--- Content
    Route::get('content_section', [ApiMenuController::class, 'contentSectionList']);
    Route::get('content_category', [ApiMenuController::class, 'contentCategoryList']);
    Route::get('content_post', [ApiMenuController::class, 'contentPostList']);

    //--- Gallery
    Route::get('gallery_category', [ApiMenuController::class, 'galleryCategoryList']);
    Route::get('gallery_album', [ApiMenuController::class, 'galleryAlbumList']);

    //--- Document
    Route::get('document', [ApiMenuController::class, 'documentList']);

    //--- Link
    Route::get('link', [ApiMenuController::class, 'linkList']);

    //--- Inquiry
    Route::get('inquiry', [ApiMenuController::class, 'inquiryList']);

    //--- Event
    Route::get('event', [ApiMenuController::class, 'eventList']);
});
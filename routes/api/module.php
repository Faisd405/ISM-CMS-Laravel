<?php

use App\Http\Controllers\Api\ApiModuleController;
use Illuminate\Support\Facades\Route;

Route::prefix('module')->group(function () {

    //--- Page
    Route::get('page', [ApiModuleController::class, 'pageList']);

    //--- Content
    Route::get('content_section', [ApiModuleController::class, 'contentSectionList']);
    Route::get('content_category', [ApiModuleController::class, 'contentCategoryList']);
    Route::get('content_post', [ApiModuleController::class, 'contentPostList']);

    //--- Gallery
    Route::get('gallery_category', [ApiModuleController::class, 'galleryCategoryList']);
    Route::get('gallery_album', [ApiModuleController::class, 'galleryAlbumList']);

    //--- Document
    Route::get('document', [ApiModuleController::class, 'documentList']);

    //--- Link
    Route::get('link', [ApiModuleController::class, 'linkList']);

    //--- Inquiry
    Route::get('inquiry', [ApiModuleController::class, 'inquiryList']);

    //--- Event
    Route::get('event', [ApiModuleController::class, 'eventList']);
});
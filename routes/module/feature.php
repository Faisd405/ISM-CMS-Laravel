<?php

use App\Http\Controllers\Feature\ApiController;
use App\Http\Controllers\Feature\ConfigurationController;
use App\Http\Controllers\Feature\LanguageController;
use App\Http\Controllers\Feature\NotificationController;
use App\Http\Controllers\Feature\RegistrationController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->middleware('auth')->group(function () {

    //--- Language
    Route::prefix('language')->name('language.')->group(function () {

        Route::get('/', [LanguageController::class, 'index'])
            ->name('index')
            ->middleware('permission:languages');
        Route::get('/trash', [LanguageController::class, 'trash'])
            ->name('trash')
            ->middleware('role:super');

        Route::get('/create', [LanguageController::class, 'create'])
            ->name('create')
            ->middleware('permission:language_create');
        Route::post('/store', [LanguageController::class, 'store'])
            ->name('store')
            ->middleware('permission:language_create');
        Route::get('/{id}/edit', [LanguageController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:language_update');
        Route::put('/{id}', [LanguageController::class, 'update'])
            ->name('update')
            ->middleware('permission:language_update');
        Route::put('/{id}/activate', [LanguageController::class, 'activate'])
            ->name('activate')
            ->middleware('permission:language_update');
        Route::delete('/{id}/soft', [LanguageController::class, 'softDelete'])
            ->name('delete.soft')
            ->middleware('permission:language_delete');
        Route::delete('/{id}/permanent', [LanguageController::class, 'permanentDelete'])
            ->name('delete.permanent')
            ->middleware('role:super');
        Route::put('/{id}/restore', [LanguageController::class, 'restore'])
            ->name('restore')
            ->middleware('role:super');

    });

    //--- Registration
    Route::prefix('registration')->name('registration.')->group(function () {

        Route::get('/', [RegistrationController::class, 'index'])
            ->name('index')
            ->middleware('permission:registrations');
        Route::get('/trash', [RegistrationController::class, 'trash'])
            ->name('trash')
            ->middleware('role:super');
            
        Route::get('/create', [RegistrationController::class, 'create'])
            ->name('create')
            ->middleware('permission:registration_create');
        Route::post('/store', [RegistrationController::class, 'store'])
            ->name('store')
            ->middleware('permission:registration_create');
        Route::get('/{id}/edit', [RegistrationController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:registration_update');
        Route::put('/{id}', [RegistrationController::class, 'update'])
            ->name('update')
            ->middleware('permission:registration_update');
        Route::put('/{id}/activate', [RegistrationController::class, 'activate'])
            ->name('activate')
            ->middleware('permission:registration_update');
        Route::delete('/{id}/soft', [RegistrationController::class, 'softDelete'])
            ->name('delete.soft')
            ->middleware('permission:registration_delete');
        Route::delete('/{id}/permanent', [RegistrationController::class, 'permanentDelete'])
            ->name('delete.permanent')
            ->middleware('role:super');
        Route::put('/{id}/restore', [RegistrationController::class, 'restore'])
            ->name('restore')
            ->middleware('role:super');

    });

    //--- API
    Route::prefix('api')->name('api.')->group(function () {

        Route::get('/', [ApiController::class, 'index'])
            ->name('index')
            ->middleware('permission:apis');
        Route::get('/trash', [ApiController::class, 'trash'])
            ->name('trash')
            ->middleware('role:super');

        Route::get('/create', [ApiController::class, 'create'])
            ->name('create')
            ->middleware('permission:api_create');
        Route::post('/store', [ApiController::class, 'store'])
            ->name('store')
            ->middleware('permission:api_create');
        Route::get('/{id}/edit', [ApiController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:api_update');
        Route::put('/{id}', [ApiController::class, 'update'])
            ->name('update')
            ->middleware('permission:api_update');
        Route::put('/{id}/activate', [ApiController::class, 'activate'])
            ->name('activate')
            ->middleware('permission:api_update');
        Route::put('/{id}/regenerate', [ApiController::class, 'regenerate'])
            ->name('regenerate')
            ->middleware('permission:api_update');
        Route::delete('/{id}/soft', [ApiController::class, 'softDelete'])
            ->name('delete.soft')
            ->middleware('permission:api_delete');
        Route::delete('/{id}/permanent', [ApiController::class, 'permanentDelete'])
            ->name('delete.permanent')
            ->middleware('role:super');
        Route::put('/{id}/restore', [ApiController::class, 'restore'])
            ->name('restore')
            ->middleware('role:super');

    });

    //--- Notification
    Route::get('notification', [NotificationController::class, 'index'])
        ->name('notification');
    Route::get('notification/latest', [NotificationController::class, 'latest'])
        ->name('notification.latest');
    Route::get('notification/{id}/read', [NotificationController::class, 'read'])
        ->name('notification.read');

    //--- Configuration
    Route::prefix('configuration')->name('configuration.')->group(function() {
        Route::get('website', [ConfigurationController::class, 'configWeb'])
            ->name('website')
            ->middleware('permission:configurations');
        Route::put('website', [ConfigurationController::class, 'updateConfigWeb'])
            ->name('website.update')
            ->middleware('permission:configurations');
        Route::put('website/{name}/upload', [ConfigurationController::class, 'uploadConfigWeb'])
            ->name('website.upload')
            ->middleware('permission:configurations');
        Route::put('website/{name}/delete', [ConfigurationController::class, 'deleteUploadConfigWeb'])
            ->name('website.delete')
            ->middleware('permission:configurations');
        Route::get('text/{lang}', [ConfigurationController::class, 'configText'])
            ->name('text')
            ->middleware('permission:configurations');
    });

    /**
     * Extra
     */
    //---  Visitor
    Route::get('/visitor', [ConfigurationController::class, 'visitor'])
        ->name('visitor')
        ->middleware('permission:visitor');

    //--- File Manager
    Route::get('/filemanager', [ConfigurationController::class, 'filemanager'])
        ->name('filemanager')
        ->middleware('permission:filemanager');
    
});
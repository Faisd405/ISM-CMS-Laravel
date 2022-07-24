<?php

use App\Http\Controllers\User\PermissionController;
use App\Http\Controllers\User\RoleController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->middleware('auth')->group(function () {

    /**
     * Access Control List
     */
    Route::prefix('acl')->middleware('role:developer|super')->group(function () {

        //--- Role
        Route::prefix('role')->name('role.')->group(function () {
            Route::get('/', [RoleController::class, 'index'])
                ->name('index');
            Route::get('/create', [RoleController::class, 'create'])
                ->name('create');
            Route::post('/', [RoleController::class, 'store'])
                ->name('store');
            Route::get('/{id}/edit', [RoleController::class, 'edit'])
                ->name('edit');
            Route::put('/{id}', [RoleController::class, 'update'])
                ->name('update');
            Route::delete('/{id}', [RoleController::class, 'destroy'])
                ->name('destroy');
        });
        
        //--- Permission
        Route::prefix('permission')->name('permission.')->group(function () {
            Route::get('/', [PermissionController::class, 'index'])
                ->name('index');
            Route::get('/create', [PermissionController::class, 'create'])
                ->name('create');
            Route::post('/', [PermissionController::class, 'store'])
                ->name('store');
            Route::get('/{id}/edit', [PermissionController::class, 'edit'])
                ->name('edit');
            Route::put('/{id}', [PermissionController::class, 'update'])
                ->name('update');
            Route::delete('/{id}', [PermissionController::class, 'destroy'])
                ->name('destroy');
        });

    });

    //--- Users
    Route::prefix('user')->name('user.')->group(function () {

        Route::get('/', [UserController::class, 'index'])
            ->name('index')
            ->middleware('permission:users');
        Route::get('/trash', [UserController::class, 'trash'])
            ->name('trash')
            ->middleware('role:developer|super');
        Route::get('/log', [UserController::class, 'log'])
            ->name('log')
            ->middleware('permission:users');
        Route::delete('/log/reset', [UserController::class, 'logReset'])
            ->name('log.reset')
            ->middleware('role:developer|super');
        Route::get('/login-failed', [UserController::class, 'loginFailed'])
            ->name('login-failed')
            ->middleware('role:developer|super');
        Route::delete('/login-failed/reset', [UserController::class, 'loginFailedReset'])
            ->name('login-failed.reset')
            ->middleware('role:developer|super');

        Route::get('/create', [UserController::class, 'create'])
            ->name('create')
            ->middleware('permission:user_create');
        Route::post('/', [UserController::class, 'store'])
            ->name('store')
            ->middleware('permission:user_create');
        Route::put('/{id}/bypass', [UserController::class, 'bypass'])
            ->name('bypass')
            ->middleware('permission:users');
        Route::get('/{id}/edit', [UserController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:user_update');
        Route::put('/{id}', [UserController::class, 'update'])
            ->name('update')
            ->middleware('permission:user_update');
        Route::put('/{id}/activate', [UserController::class, 'activate'])
            ->name('activate')
            ->middleware('permission:user_update');
        Route::delete('/{id}/soft', [UserController::class, 'softDelete'])
            ->name('delete.soft')
            ->middleware('permission:user_delete');
        Route::delete('/{id}/permanent', [UserController::class, 'permanentDelete'])
            ->name('delete.permanent')
            ->middleware('role:developer|super');
        Route::put('/{id}/restore', [UserController::class, 'restore'])
            ->name('restore')
            ->middleware('role:developer|super');
        Route::delete('/delete/{id}/log', [UserController::class, 'logDelete'])
            ->name('log.destroy')
            ->middleware('role:developer|super');
        Route::delete('delete/{ip}/login-failed', [UserController::class, 'loginFailedDelete'])
            ->name('login-failed.destroy')
            ->middleware('role:developer|super');

    });

    //--- Profile
    Route::prefix('profile')->group(function () {
        
        Route::get('/', [UserController::class, 'profile'])
            ->name('profile');
        Route::put('/', [UserController::class, 'updateProfile']);
        //-- Change Photo
        Route::put('/photo/change', [UserController::class, 'changePhoto'])
            ->name('profile.photo.change');
        Route::put('/photo/remove', [UserController::class, 'removePhoto'])
            ->name('profile.photo.remove');
        //-- Verification Email
        Route::get('/email/send', [UserController::class, 'sendMailVerification'])
            ->name('profile.email.send');
        Route::get('/email/verification/{email}/{expired}', [UserController::class, 'verified'])
            ->name('profile.email.verification');

    });

});
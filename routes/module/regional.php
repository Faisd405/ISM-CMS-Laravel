<?php

use App\Http\Controllers\Regional\CityController;
use App\Http\Controllers\Regional\DistrictController;
use App\Http\Controllers\Regional\ProvinceController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin/regional')->middleware('auth')->group(function () {

    //--- Province
    Route::prefix('province')->group(function () {
        
        Route::get('/', [ProvinceController::class, 'index'])
            ->name('province.index')
            ->middleware('permission:regionals');
        Route::get('/trash', [ProvinceController::class, 'trash'])
            ->name('province.trash')
            ->middleware('role:super');
        
        Route::get('/create', [ProvinceController::class, 'create'])
            ->name('province.create')
            ->middleware('permission:regional_create');
        Route::post('/', [ProvinceController::class, 'store'])
            ->name('province.store')
            ->middleware('permission:regional_create');
        Route::get('/{id}/edit', [ProvinceController::class, 'edit'])
            ->name('province.edit')
            ->middleware('permission:regional_update');
        Route::put('/{id}', [ProvinceController::class, 'update'])
            ->name('province.update')
            ->middleware('permission:regional_update');
        Route::delete('/{id}/soft', [ProvinceController::class, 'softDelete'])
            ->name('province.delete.soft')
            ->middleware('permission:regional_delete');
        Route::delete('/{id}/permanent', [ProvinceController::class, 'permanentDelete'])
            ->name('province.delete.permanent')
            ->middleware('role:super');
        Route::put('/{id}/restore', [ProvinceController::class, 'restore'])
            ->name('province.restore')
            ->middleware('role:super');

        //--- City
        Route::prefix('{provinceCode}/city')->group(function () {

            Route::get('/', [CityController::class, 'index'])
                ->name('city.index')
                ->middleware('permission:regionals');
            Route::get('/trash', [CityController::class, 'trash'])
                ->name('city.trash')
                ->middleware('role:super');

            Route::get('/create', [CityController::class, 'create'])
                ->name('city.create')
                ->middleware('permission:regional_create');
            Route::post('/', [CityController::class, 'store'])
                ->name('city.store')
                ->middleware('permission:regional_create');
            Route::get('/{id}/edit', [CityController::class, 'edit'])
                ->name('city.edit')
                ->middleware('permission:regional_update');
            Route::put('/{id}', [CityController::class, 'update'])
                ->name('city.update')
                ->middleware('permission:regional_update');
            Route::delete('/{id}/soft', [CityController::class, 'softDelete'])
                ->name('city.delete.soft')
                ->middleware('permission:regional_delete');
            Route::delete('/{id}/permanent', [CityController::class, 'permanentDelete'])
                ->name('city.delete.permanent')
                ->middleware('role:super');
            Route::put('/{id}/restore', [CityController::class, 'restore'])
                ->name('city.restore')
                ->middleware('role:super');

            //--- District
            Route::prefix('{cityCode}/district')->group(function () {

                Route::get('/', [DistrictController::class, 'index'])
                    ->name('district.index')
                    ->middleware('permission:regionals');
                Route::get('/trash', [DistrictController::class, 'trash'])
                    ->name('district.trash')
                    ->middleware('role:super');

                Route::get('/create', [DistrictController::class, 'create'])
                    ->name('district.create')
                    ->middleware('permission:regional_create');
                Route::post('/', [DistrictController::class, 'store'])
                    ->name('district.store')
                    ->middleware('permission:regional_create');
                Route::get('/{id}/edit', [DistrictController::class, 'edit'])
                    ->name('district.edit')
                    ->middleware('permission:regional_update');
                Route::put('/{id}', [DistrictController::class, 'update'])
                    ->name('district.update')
                    ->middleware('permission:regional_update');
                Route::delete('/{id}/soft', [DistrictController::class, 'softDelete'])
                    ->name('district.delete.soft')
                    ->middleware('permission:regional_delete');
                Route::delete('/{id}/permanent', [DistrictController::class, 'permanentDelete'])
                    ->name('district.delete.permanent')
                    ->middleware('role:super');
                Route::put('/{id}/restore', [DistrictController::class, 'restore'])
                    ->name('district.restore')
                    ->middleware('role:super');
            });

        });

    });
    
});
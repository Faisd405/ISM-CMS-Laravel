<?php

use App\Http\Controllers\Master\TagController;
use App\Http\Controllers\Regional\CityController;
use App\Http\Controllers\Regional\DistrictController;
use App\Http\Controllers\Regional\ProvinceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//--- Regional
Route::get('/regional/province', [ProvinceController::class, 'listProvinceApi']);
Route::get('/regional/city', [CityController::class, 'listCityApi']);
Route::get('/regional/district', [DistrictController::class, 'listDistrictApi']);

//--- Autocomplete
Route::get('/autocomplete/tags', [TagController::class, 'autocomplete']);
<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Grouping
$group = ['middleware' => ['language']];
$groupAuth = ['middleware' => ['guest', 'language']];

if (config('cms.module.feature.language.needLocale')) {
    $group['prefix'] = '{locale?}';
    $groupAuth['prefix'] = '{locale?}';
}

//------------
// BACKEND
//------------

//--- Login Backend
Route::get('/backend/authentication', [LoginController::class, 'showLoginBackendForm'])
    ->name('login')
    ->middleware('guest');
Route::post('/backend/authentication', [LoginController::class, 'loginBackend'])
    ->middleware('guest');

Route::group($groupAuth, function () {

    //--- Login Frontend
    Route::get('/login', [LoginController::class, 'showLoginFrontendForm'])
        ->name('login.frontend');
    Route::post('/login', [LoginController::class, 'loginFrontend']);

    //--- Register
    Route::get('/register', [RegisterController::class, 'showRegisterForm'])
        ->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

    //--- Activate Account
    Route::get('/register/activate', [RegisterController::class, 'showActivateForm'])
        ->name('register.activate.form');
    Route::post('/register/activate/send', [RegisterController::class, 'sendLinkActivate'])
        ->name('register.activate.send');
    Route::get('/register/activate/{email}/{expired}', [RegisterController::class, 'activate'])
        ->name('register.activate');


    //--- Forgot Password
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])
        ->name('password.email');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail']);

    //--- Reset Password
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])
        ->name('password.reset');
    Route::post('/reset-password/send', [ResetPasswordController::class, 'reset'])
        ->name('password.update');
});

//--- Admin Panel
Route::prefix('admin')->middleware('auth')->group(function () {

    //--- Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');
    Route::get('/dashboard/analytics', [DashboardController::class, 'analytics'])
        ->name('dashboard.analytics')
        ->middleware('permission:visitor');

    //--- Logout Backend
    Route::post('/logout', [LoginController::class, 'logoutBackend'])
        ->name('logout');

    //--- Logout Frontend
    Route::post('/logout/frontend', [LoginController::class, 'logoutFrontend'])
        ->name('logout.frontend');
    
});

//------------
// FRONTEND
//------------

//--- Sitemap
Route::get('/sitemap.xml', [HomeController::class, 'sitemap'])
    ->name('sitemap');

//--- RSS
Route::get('/feed', [HomeController::class, 'feed'])
    ->name('rss.feed');
Route::get('/feed/post', [HomeController::class, 'post'])
    ->name('rss.post');

Route::group($group, function () {
    
    //--- Landing
    Route::get('/landing', [HomeController::class, 'landing'])
        ->name('landing');
    //--- Home
    Route::get('/', [HomeController::class, 'home'])
        ->name('home');
    //--- Search
    Route::get('/search', [HomeController::class, 'search'])
        ->name('home.search');

    //--- Maintenance
    Route::get('/maintenance', [HomeController::class, 'maintenance'])
        ->name('maintenance');

});

//------------
// CACHE
//------------
Route::get('/clear-cache', function() {
    Artisan::call('cache:clear');
    return back()->with('success', 'Cache cleared');
})->name('cache.clear');

Route::get('/optimize-clear', function() {
    Artisan::call('optimize:clear');
    return back()->with('success', ' All cleared');
})->name('optimize.clear');
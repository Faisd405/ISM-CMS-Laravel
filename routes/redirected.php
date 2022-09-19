<?php

use Illuminate\Support\Facades\Route;

Route::get('/backend', function () {
    return redirect('/backend/authentication');
});

Route::get('/'.config('app.fallback_locale'), function () {
    return redirect()->route('home');
});

//custom redirect
<?php

use Illuminate\Support\Facades\Route;

Route::get('/backend', function () {
    return redirect('/backend/authentication');
});

Route::get('/'.config('cms.module.feature.language.default'), function () {
    return redirect()->route('home');
});

//custom redirect
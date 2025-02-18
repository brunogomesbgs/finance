<?php

use Illuminate\Support\Facades\Route;

Route::get('/api/welcome', function () {
    return view('welcome');
});


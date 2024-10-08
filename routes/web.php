<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/invitation', function () {
    $url = url('/invite');
    return view('invitation')->with('url', $url);
});

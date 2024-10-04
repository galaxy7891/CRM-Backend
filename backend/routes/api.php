<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OTPController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerController;
use Illuminate\Auth\Middleware\Authenticate;
use App\Http\Controllers\OrganizationController;


// customer


Route::apiResource('/customers', CustomerController::class);
Route::apiResource('/organizations', OrganizationController::class);
Route::apiResource('/products', ProductController::class);



Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {

    /* 
     * register
     */
    Route::post('/register', [AuthController::class, 'register'])->name('register');

    /* 
     * otp
     */
    Route::post('/sendOTP', [OTPController::class, 'sendOTP'])->name('sendOTP');
    Route::post('/verifyOTP', [OTPController::class, 'verifyOTP'])->name('verifyOTP');

    /* 
     * oauth google
     */
    Route::get('/google', [AuthController::class, 'redirectToGoogle']);
    Route::get('/google/callback', [AuthController::class, 'handleGoogleCallback']);

    /* 
     * login
     */
    Route::post('/login', [AuthController::class, 'login'])->name('login');

    /* 
     * logout
     */
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    //refresh jwt
    Route::post('/refresh', [AuthController::class, 'refresh'])->middleware('auth:api')->name('refresh');
});

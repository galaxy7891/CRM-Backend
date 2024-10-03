<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OTPController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrganizationController;
use Illuminate\Auth\Middleware\Authenticate;
use App\Http\Controllers\UserController;


Route::group(['middleware' => 'api'], function ($router) {
    Route::post('/password/forgot', [UserController::class, 'sendResetLink']);
    Route::post('/password/reset', [UserController::class, 'reset'])->name('password.reset');
});

// customer
Route::apiResource('/customers', CustomerController::class);
Route::apiResource('/organizations', OrganizationController::class);

Route::group(['middleware' => 'api', 'prefix' => 'auth'], function ($router) {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/sendOTP', [OTPController::class, 'sendOTP'])->name('sendOTP');
    Route::post('/verifyOTP', [OTPController::class, 'verifyOTP'])->name('verifyOTP');
    Route::get('/google', [AuthController::class, 'redirectToGoogle']);
    Route::get('/google/callback', [AuthController::class, 'handleGoogleCallback']);
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/refresh', [AuthController::class, 'refresh'])->middleware('auth:api')->name('refresh');
});

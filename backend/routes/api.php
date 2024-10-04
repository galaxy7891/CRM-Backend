<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OTPController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrganizationController;
use Illuminate\Auth\Middleware\Authenticate;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserInvitationController;
use App\Http\Middleware\JwtMiddleware;

Route::group(['middleware' => 'api'], function () {
    Route::group(['prefix' => 'auth'], function () {
        Route::post('/login', [AuthController::class, 'login'])->name('login');
        Route::post('/register', [AuthController::class, 'register'])->name('register');

        Route::group(['prefix' => 'otp'], function () {
            Route::post('/send', [OTPController::class, 'sendOTP']);
            Route::post('/verify', [OTPController::class, 'verifyOTP']);
        });        
        
        Route::group(['prefix' => 'password'], function () {
            Route::post('/forgot', [UserController::class, 'sendResetLink']);
            Route::post('/reset', [UserController::class, 'reset'])->name('password.reset'); 
        });
    });

    Route::group(['prefix' => 'oauth'], function () {
        Route::get('/google', [AuthController::class, 'redirectToGoogle']);
        Route::get('/google/callback', [AuthController::class, 'handleGoogleCallback']);
    });

    Route::post('/invitation/accept', [UserInvitationController::class, 'createUser']);
    
    Route::group(['middleware' => JwtMiddleware::class], function () { //authentikasi terlebih dahulu
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::post('/refresh', [AuthController::class, 'refresh'])->name('refresh');
        Route::post('/invitation/send', [UserInvitationController::class, 'sendInvitation']);
        Route::apiResource('/customers', CustomerController::class);
        Route::apiResource('/organizations', OrganizationController::class);

    });

});

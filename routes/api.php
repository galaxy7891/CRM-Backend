<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OTPController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DealController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\UserInvitationController;
use App\Http\Middleware\JwtMiddleware;
use App\Http\Middleware\RoleMiddleware;

Route::group(['middleware' => 'api'], function () {
    Route::group(['prefix' => 'auth'], function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/register', [AuthController::class, 'register']); 

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

    Route::group(['middleware' => JwtMiddleware::class], function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::get('/dashboard', [UserController::class, 'getSummary']);
        Route::post('/import/{type}', [ImportController::class, 'import']);
        Route::apiResource('/customers', CustomerController::class);
        Route::apiResource('/organizations', OrganizationController::class);
        Route::apiResource('/products', ProductController::class);
        Route::apiResource('/deals', DealController::class);
        Route::group(['middleware' => RoleMiddleware::class . ':super_admin'], function () {
            Route::put('/users/{id}', [ProductController::class, 'update']);
            Route::delete('/users/{id}', [UserController::class, 'destroy']);
        });
        Route::group(['middleware' => RoleMiddleware::class . ':super_admin, admin'], function () {
            Route::post('/invitation/send', [UserInvitationController::class, 'sendInvitation']);
            Route::get('/users', [UserController::class, 'index']);
            Route::get('/users/{id}', [UserController::class, 'show']);
        });
    });
});

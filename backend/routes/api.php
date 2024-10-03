<?php
 
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OTPController;
use App\Http\Controllers\UserController;


Route::group([

    'middleware' => 'auth:'

], function ($router) {
    /*
     * forgot password
     */
    Route::post('/password/forgot', [UserController::class, 'sendResetLink']);

    /*
     * reset password
     */
    Route::post('/password/reset', [UserController::class, 'reset'])->name('password.reset');
});



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
    Route::get('/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('handleGoogleCallback');
    
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
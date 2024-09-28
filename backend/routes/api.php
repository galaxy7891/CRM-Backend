<?php
 
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
 
Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {
    
    /* 
     * register
     */
    Route::post('/otp', [AuthController::class, 'sendOTP'])->name('otp');
    Route::post('/verifyotp', [AuthController::class, 'verifyOtp'])->name('verifyotp');
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
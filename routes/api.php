<?php

use App\Http\Controllers\ActivityLogController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OTPController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompaniesController;
use App\Http\Controllers\DealController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\EmployeeController;
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
            Route::post('/change', [UserController::class, 'changePassword']);
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
        
        // Route::get('/activity/log', [ActivityLogController::class, 'indexUser']);
        Route::get('/activity/log/{type}', [ActivityLogController::class, 'indexUser']);
        Route::get('/activity/log/detail', [ActivityLogController::class, 'detail']);
        
        Route::get('/user', [UserController::class, 'show']);
        Route::post('/user', [UserController::class, 'update']);
        Route::post('/user/profile', [UserController::class, 'updateProfilePhoto']);
        Route::delete('/user', [UserController::class, 'destroy']);

        Route::get('/companies', [CompaniesController::class, 'index']);
        Route::post('/companies/{companyId}', [CompaniesController::class, 'update']);
        Route::post('/companies/logo/{companyId}', [CompaniesController::class, 'updateLogo']);
        
        Route::get('/leads', [CustomerController::class, 'indexLeads']);
        Route::get('/leads/{leads}', [CustomerController::class, 'showLeads']);
        Route::post('/leads', [CustomerController::class, 'storeLeads']);
        Route::post('/leads/{leadsId}', [CustomerController::class, 'updateLeads']);
        Route::post('/leads/convert/{leadsId}', [CustomerController::class, 'convert']);
        Route::delete('/leads/{leadsId}', [CustomerController::class, 'destroyLeads']);
        
        Route::get('/contact', [CustomerController::class, 'indexContact']);
        Route::get('/contact/{contactId}', [CustomerController::class, 'showContact']);
        Route::post('/contact', [CustomerController::class, 'storeContact']);
        Route::post('/contact/{contactId}', [CustomerController::class, 'updateContact']);
        Route::delete('/contact/{contactId}', [CustomerController::class, 'destroyContact']);
        
        Route::get('/organization', [OrganizationController::class, 'index']);
        Route::get('/organization/{organizationId}', [OrganizationController::class, 'show']);
        Route::post('/organization', [OrganizationController::class, 'store']);
        Route::post('/organization/{organizationId}', [OrganizationController::class, 'update']);
        Route::delete('/organization/{organizationId}', [OrganizationController::class, 'destroy']);
        
        Route::get('/products', [ProductController::class, 'index']);
        Route::get('/products/{productId}', [ProductController::class, 'show']);
        Route::post('/products', [ProductController::class, 'store']);
        Route::post('/products/{productsId}', [ProductController::class, 'update']);
        Route::delete('/products/{productsId}', [ProductController::class, 'destroy']);
        Route::post('/product/photo/{productId}', [ProductController::class, 'updatePhotoProduct']);

        Route::apiResource('/deals', DealController::class);
        
        Route::group(['middleware' => RoleMiddleware::class . ':super_admin'], function () {
            Route::post('/employee/{employeeId}', [EmployeeController::class, 'update']);
            Route::delete('/employee/{id}', [EmployeeController::class, 'destroy']);
        });

        Route::group(['middleware' => RoleMiddleware::class . ':super_admin, admin'], function () {
            Route::post('/invitation/send', [UserInvitationController::class, 'sendInvitation']);
            Route::get('/employee', [EmployeeController::class, 'index']);
            Route::get('/employee/{id}', [EmployeeController::class, 'show']);
        });
    });
});

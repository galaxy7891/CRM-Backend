<?php

use App\Http\Controllers\AccountsTypeController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\ArticlesController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OTPController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsersCompaniesController;
use App\Http\Controllers\DealController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\CustomersCompanyController;
use App\Http\Controllers\DashboardController;
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

        Route::get('/user', [UserController::class, 'show']);
        Route::post('/user', [UserController::class, 'update']);
        Route::post('/user/profile', [UserController::class, 'updateProfilePhoto']);
        Route::delete('/user', [UserController::class, 'destroy']);

        Route::group(['middleware' => RoleMiddleware::class . ':super_admin_lc'], function () {
            Route::get('/dashboard/admin', [DashboardController::class, 'getSummaryAdmin']);

            Route::get('/article', [ArticlesController::class, 'index']);
            Route::get('/article/{articleId}', [ArticlesController::class, 'show']);
            Route::post('/article', [ArticlesController::class, 'store']);
            Route::post('/article/{articleId}', [ArticlesController::class, 'update']);
            Route::delete('/article', [ArticlesController::class, 'destroy']);

            Route::get('/accountstypes', [AccountsTypeController::class, 'index']);
            Route::post('/accountstypes/{accountsTypeId}', [AccountsTypeController::class, 'update']);
        });

        Route::group(['middleware' => RoleMiddleware::class . ':super_admin,admin,employee'], function () {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::post('/refresh', [AuthController::class, 'refresh']);
            Route::get('/dashboard', [UserController::class, 'getSummary']);
            Route::get('/report/conversion/contact', [DashboardController::class, 'getConversionContact']);
            Route::get('/report/status', [DashboardController::class, 'getStatusReport']);

            Route::post('/import/{type}', [ImportController::class, 'import']);

            Route::get('/activity/log/{type}', [ActivityLogController::class, 'index']);
            Route::get('/detail/activity/log', [ActivityLogController::class, 'detail']);

            Route::get('/users/companies', [UsersCompaniesController::class, 'index']);

            Route::get('/leads', [CustomerController::class, 'indexLeads']);
            Route::get('/leads/{leads}', [CustomerController::class, 'showLeads']);
            Route::post('/leads', [CustomerController::class, 'storeLeads']);
            Route::post('/leads/{leadsId}', [CustomerController::class, 'updateLeads']);
            Route::post('/leads/convert/{leadsId}', [CustomerController::class, 'convert']);
            Route::delete('/leads', [CustomerController::class, 'destroyLeads']);

            Route::get('/contact', [CustomerController::class, 'indexContact']);
            Route::get('/contact/{contactId}', [CustomerController::class, 'showContact']);
            Route::post('/contact', [CustomerController::class, 'storeContact']);
            Route::post('/contact/{contactId}', [CustomerController::class, 'updateContact']);
            Route::delete('/contact', [CustomerController::class, 'destroyContact']);

            Route::get('/customers/companies', [CustomersCompanyController::class, 'index']);
            Route::get('/customers/companies/{customersCompaniesId}', [CustomersCompanyController::class, 'show']);
            Route::post('/customers/companies', [CustomersCompanyController::class, 'store']);
            Route::post('/customers/companies/{customersCompaniesId}', [CustomersCompanyController::class, 'update']);
            Route::delete('/customers/companies', [CustomersCompanyController::class, 'destroy']);

            Route::get('/products', [ProductController::class, 'index']);
            Route::get('/products/{productId}', [ProductController::class, 'show']);
            Route::post('/products', [ProductController::class, 'store']);
            Route::post('/products/{productsId}', [ProductController::class, 'update']);
            Route::delete('/products', [ProductController::class, 'destroy']);
            Route::post('/product/photo/{productId}', [ProductController::class, 'updatePhotoProduct']);

            Route::get('/deals', [DealController::class, 'index']);
            Route::get('/deals/value', [DealController::class, 'value']);
            Route::get('/deals/{dealsId}', [DealController::class, 'show']);
            Route::post('/deals', [DealController::class, 'store']);
            Route::post('/deals/stage/{dealsId}', [DealController::class, 'updateStage']);
            Route::post('/deals/{dealsId}', [DealController::class, 'update']);
            Route::delete('/deals', [DealController::class, 'destroy']);

            Route::group(['middleware' => RoleMiddleware::class . ':super_admin'], function () {
                Route::post('/employee/{employeeId}', [EmployeeController::class, 'update']);
                Route::delete('/employee', [EmployeeController::class, 'destroy']);

                Route::post('/users/companies', [UsersCompaniesController::class, 'update']);
                Route::post('/users/companies/logo', [UsersCompaniesController::class, 'updateLogo']);
            });

            Route::group(['middleware' => RoleMiddleware::class . ':super_admin,admin'], function () {
                Route::post('/invitation/send', [UserInvitationController::class, 'sendInvitation']);
                Route::get('/employee', [EmployeeController::class, 'index']);
                Route::get('/employee/{id}', [EmployeeController::class, 'show']);
            });
        });
    });
});

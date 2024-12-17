<?php

namespace App\Providers;

use App\Models\Customer;
use App\Models\Deal;
use App\Models\CustomersCompany;
use App\Models\Product;
use App\Models\User;
use App\Models\UserInvitation;
use App\Models\UsersCompany;

use App\Observers\CustomerObserver;
use App\Observers\DealObserver;
use App\Observers\CustomersCompaniesObserver;
use App\Observers\ProductObserver;
use App\Observers\UserInvitationObserver;
use App\Observers\UserObserver;
use App\Observers\UsersCompaniesObserver;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Validator::extend('unique_customers_company_name', function ($attribute, $value, $parameters, $validator) {
            $userCompanyId = auth()->user()->company->id;
            return !\App\Models\CustomersCompany::where('user_company_id', $userCompanyId)
                ->where('name', $value)
                ->exists();
        });
    
        Validator::extend('unique_product_code', function ($attribute, $value, $parameters, $validator) {
            $userCompanyId = auth()->user()->company->id;
            return !\App\Models\Product::where('user_company_id', $userCompanyId)
                ->where('code', $value)
                ->exists();
        });

        Validator::extend('unique_product_name', function ($attribute, $value, $parameters, $validator) {
            $userCompanyId = auth()->user()->company->id;
            return !\App\Models\Product::where('user_company_id', $userCompanyId)
                ->where('name', $value)
                ->exists();
        });
    
        Validator::extend('unique_product_code', function ($attribute, $value, $parameters, $validator) {
            $userCompanyId = auth()->user()->company->id;
            return !\App\Models\Product::where('user_company_id', $userCompanyId)
                ->where('code', $value)
                ->exists();
        });

        Validator::extend('unique_product_name', function ($attribute, $value, $parameters, $validator) {
            $userCompanyId = auth()->user()->company->id;
            return !\App\Models\Product::where('user_company_id', $userCompanyId)
                ->where('name', $value)
                ->exists();
        });
    
        Validator::extend('unique_product_code', function ($attribute, $value, $parameters, $validator) {
            $userCompanyId = auth()->user()->company->id;
            return !\App\Models\Product::where('user_company_id', $userCompanyId)
                ->where('code', $value)
                ->exists();
        });

        UsersCompany::observe(UsersCompaniesObserver::class);
        Customer::observe(CustomerObserver::class);
        User::observe(UserObserver::class);
        CustomersCompany::observe(CustomersCompaniesObserver::class);
        Product::observe(ProductObserver::class);
        Deal::observe(DealObserver::class);
        UserInvitation::observe(UserInvitationObserver::class);
    }
}

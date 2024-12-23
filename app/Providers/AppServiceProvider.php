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
        /**
         * Validator Unique
         */
        
        // User
        Validator::extend('unique_user_email', function ($attribute, $value, $parameters, $validator) {
            $userCompanyId = auth()->user()->company->id;
            $excludeId = $parameters[0] ?? null;

            return !User::where('email', $value)
                ->whereNull('deleted_at')
                ->when($excludeId, function ($query) use ($excludeId) {
                    $query->where('id', '!=', $excludeId);
                })
                ->exists();
        });
        Validator::extend('unique_user_phone', function ($attribute, $value, $parameters, $validator) {
            $userCompanyId = auth()->user()->company->id;
            $excludeId = $parameters[0] ?? null;

            return !User::where('user_company_id', $userCompanyId)
                ->where('phone', $value)
                ->whereNull('deleted_at')
                ->when($excludeId, function ($query) use ($excludeId) {
                    $query->where('id', '!=', $excludeId);
                })
                ->exists();
        });
        
        // User Companies
        Validator::extend('unique_usercompanies_name', function ($attribute, $value, $parameters, $validator) {
            $excludeId = $parameters[0] ?? null;

            return !UsersCompany::where('name', $value)
                ->whereNull('deleted_at')
                ->when($excludeId, function ($query) use ($excludeId) {
                    $query->where('id', '!=', $excludeId);
                })
                ->exists();
        });
        Validator::extend('unique_usercompanies_email', function ($attribute, $value, $parameters, $validator) {
            $excludeId = $parameters[0] ?? null;

            return !UsersCompany::where('email', $value)
                ->whereNull('deleted_at')
                ->when($excludeId, function ($query) use ($excludeId) {
                    $query->where('id', '!=', $excludeId);
                })
                ->exists();
        });
        Validator::extend('unique_usercompanies_phone', function ($attribute, $value, $parameters, $validator) {
            $excludeId = $parameters[0] ?? null;

            return !UsersCompany::where('phone', $value)
                ->whereNull('deleted_at')
                ->when($excludeId, function ($query) use ($excludeId) {
                    $query->where('id', '!=', $excludeId);
                })
                ->exists();
        });
        Validator::extend('unique_usercompanies_website', function ($attribute, $value, $parameters, $validator) {
            $excludeId = $parameters[0] ?? null;

            return !UsersCompany::where('website', $value)
                ->whereNull('deleted_at')
                ->when($excludeId, function ($query) use ($excludeId) {
                    $query->where('id', '!=', $excludeId);
                })
                ->exists();
        });

        // Customers
        Validator::extend('unique_customers_email', function ($attribute, $value, $parameters, $validator) {
            $userCompanyId = auth()->user()->company->id;
            $excludeId = $parameters[0] ?? null;

            return !Customer::whereHas('user', function ($ownerQuery) use ($userCompanyId) {
                $ownerQuery->where('user_company_id', $userCompanyId);
            })->where('email', $value)
                ->whereNull('deleted_at')
                ->when($excludeId, function ($query) use ($excludeId) {
                    $query->where('id', '!=', $excludeId);
                })
                ->exists();
        });
        Validator::extend('unique_customers_phone', function ($attribute, $value, $parameters, $validator) {
            $userCompanyId = auth()->user()->company->id;
            $excludeId = $parameters[0] ?? null;
            
            return !Customer::whereHas('user', function ($ownerQuery) use ($userCompanyId) {
                $ownerQuery->where('user_company_id', $userCompanyId);
            })->where('phone', $value)
                ->whereNull('deleted_at')
                ->when($excludeId, function ($query) use ($excludeId) {
                    $query->where('id', '!=', $excludeId);
                })
                ->exists();
        });

        // Customers Companies
        Validator::extend('unique_customerscompanies_name', function ($attribute, $value, $parameters, $validator) {
            $userCompanyId = auth()->user()->company->id;
            $excludeId = $parameters[0] ?? null;
            
            return !CustomersCompany::whereHas('user', function ($ownerQuery) use ($userCompanyId) {
                $ownerQuery->where('user_company_id', $userCompanyId);
            })->where('name', $value)
                ->whereNull('deleted_at')
                ->when($excludeId, function ($query) use ($excludeId) {
                    $query->where('id', '!=', $excludeId);
                })
                ->exists();
        });
        Validator::extend('unique_customerscompanies_email', function ($attribute, $value, $parameters, $validator) {
            $userCompanyId = auth()->user()->company->id;
            $excludeId = $parameters[0] ?? null;
            
            return !CustomersCompany::whereHas('user', function ($ownerQuery) use ($userCompanyId) {
                $ownerQuery->where('user_company_id', $userCompanyId);
            })->where('email', $value)
                ->whereNull('deleted_at')
                ->when($excludeId, function ($query) use ($excludeId) {
                    $query->where('id', '!=', $excludeId);
                })
                ->exists();
        }); 
        Validator::extend('unique_customerscompanies_phone', function ($attribute, $value, $parameters, $validator) {
            $userCompanyId = auth()->user()->company->id;
            $excludeId = $parameters[0] ?? null;
            
            return !CustomersCompany::whereHas('user', function ($ownerQuery) use ($userCompanyId) {
                $ownerQuery->where('user_company_id', $userCompanyId);
            })->where('phone', $value)
                ->whereNull('deleted_at')
                ->when($excludeId, function ($query) use ($excludeId) {
                    $query->where('id', '!=', $excludeId);
                })
                ->exists();
        });
        Validator::extend('unique_customerscompanies_website', function ($attribute, $value, $parameters, $validator) {
            $userCompanyId = auth()->user()->company->id;
            $excludeId = $parameters[0] ?? null;
            
            return !CustomersCompany::whereHas('user', function ($ownerQuery) use ($userCompanyId) {
                $ownerQuery->where('user_company_id', $userCompanyId);
            })->where('website', $value)
                ->whereNull('deleted_at')
                ->when($excludeId, function ($query) use ($excludeId) {
                    $query->where('id', '!=', $excludeId);
                })
                ->exists();
        });

        // Product
        Validator::extend('unique_product_name', function ($attribute, $value, $parameters, $validator) {
            $userCompanyId = auth()->user()->company->id;
            $excludeId = $parameters[0] ?? null;
            
            return !Product::where('user_company_id', $userCompanyId)
                ->where('name', $value)
                ->whereNull('deleted_at')
                ->when($excludeId, function ($query) use ($excludeId) {
                    $query->where('id', '!=', $excludeId);
                })
                ->exists();
        });
        Validator::extend('unique_product_code', function ($attribute, $value, $parameters, $validator) {
            $userCompanyId = auth()->user()->company->id;
            $excludeId = $parameters[0] ?? null;
            
            return !Product::where('user_company_id', $userCompanyId)
                ->where('code', $value)
                ->whereNull('deleted_at')
                ->when($excludeId, function ($query) use ($excludeId) {
                    $query->where('id', '!=', $excludeId);
                })
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

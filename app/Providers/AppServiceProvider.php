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
        UsersCompany::observe(UsersCompaniesObserver::class);
        Customer::observe(CustomerObserver::class);
        User::observe(UserObserver::class);
        CustomersCompany::observe(CustomersCompaniesObserver::class);
        Product::observe(ProductObserver::class);
        Deal::observe(DealObserver::class);
        UserInvitation::observe(UserInvitationObserver::class);
    }
}

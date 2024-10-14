<?php

namespace App\Providers;

use App\Models\Customer;
use App\Models\Deal;
use App\Models\Organization;
use App\Models\Product;
use App\Models\User;
use App\Observers\CustomerObserver;
use App\Observers\DealObserver;
use App\Observers\OrganizationObserver;
use App\Observers\ProductObserver;
use App\Observers\UserObserver;
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
        Customer::observe(CustomerObserver::class);
        User::observe(UserObserver::class);
        Organization::observe(OrganizationObserver::class);
        Product::observe(ProductObserver::class);
        Deal::observe(DealObserver::class);
    }
}

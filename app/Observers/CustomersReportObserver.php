<?php

namespace App\Observers;

use App\Models\CustomersReport;

class CustomersReportObserver
{
    /**
     * Handle the CustomersReport "created" event.
     */
    public function created(CustomersReport $customersReport): void
    {
        //
    }
    
    /**
     * Handle the CustomersReport "updated" event.
     */
    public function updated(CustomersReport $customersReport): void
    {
        //
    }

    /**
     * Handle the CustomersReport "deleted" event.
     */
    public function deleted(CustomersReport $customersReport): void
    {
        //
    }

    /**
     * Handle the CustomersReport "restored" event.
     */
    public function restored(CustomersReport $customersReport): void
    {
        //
    }

    /**
     * Handle the CustomersReport "force deleted" event.
     */
    public function forceDeleted(CustomersReport $customersReport): void
    {
        //
    }
}

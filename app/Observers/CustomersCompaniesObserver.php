<?php

namespace App\Observers;

use App\Helpers\ModelChangeLoggerHelper;
use App\Models\ActivityLog;
use App\Models\CustomersCompany;

class CustomersCompaniesObserver
{
    /**
     * Handle the Customers Company "created" event.
     */
    public function created(CustomersCompany $customersCompany): void
    {
        $changes = ModelChangeLoggerHelper::getModelChanges($customersCompany, 'customers_companies', 'CREATE');

        ActivityLog::create([
            'user_id' => auth()->id() ? auth()->id() : '123e4567-e89b-12d3-a456-426614174100',
            'model_name' => 'customers_companies',
            'action' => 'CREATE',
            'changes' => $changes ? json_encode($changes) : null,
        ]);
    }

    /**
     * Handle the Customers Company "updated" event.
     */
    public function updated(CustomersCompany $customersCompany): void
    {
        $changes = ModelChangeLoggerHelper::getModelChanges($customersCompany, 'customers_companies', 'UPDATE');

        ActivityLog::create([
            'user_id' => auth()->id() ? auth()->id() : '123e4567-e89b-12d3-a456-426614174100',
            'model_name' => 'customers_companies',
            'action' => 'UPDATE',
            'changes' => $changes ? json_encode($changes) : null,
        ]);
    }

    /**
     * Handle the Customers Company "deleted" event.
     */
    public function deleted(CustomersCompany $customersCompany): void
    {
        $changes = ModelChangeLoggerHelper::getModelChanges($customersCompany, 'customers_companies', 'DELETE');

        ActivityLog::create([
            'user_id' => auth()->id() ? auth()->id() : '123e4567-e89b-12d3-a456-426614174100',
            'model_name' => 'customers_companies',
            'action' => 'DELETE',
            'changes' => $changes ? json_encode($changes) : null,
        ]);
    }
}

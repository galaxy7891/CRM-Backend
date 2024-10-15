<?php

namespace App\Observers;

use App\Helpers\ModelChangeLoggerHelper;
use App\Models\ActivityLog;
use App\Models\Customer;

use Illuminate\Support\Str;

class CustomerObserver
{
    /**
     * Handle the Customer "created" event.
     */
    public function created(Customer $customer): void
    {
        // $changes = ModelChangeLoggerHelper::getModelChanges($customer);

        // ActivityLog::create([
        //     'user_id' => auth()->id(),
        //     'model_name' => 'customers',
        //     'action' => 'CREATE',
        //     'changes' => $changes ? json_encode($changes) : null,
        // ]);
    }

    /**
     * Handle the Customer "updated" event.
     */
    public function updated(Customer $customer): void
    {
        $changes = ModelChangeLoggerHelper::getModelChanges($customer);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'model_name' => 'customers',
            'action' => 'UPDATE',
            'changes' => $changes ? json_encode($changes) : null,
        ]);
    }

    /**
     * Handle the Customer "deleted" event.
     */
    public function deleted(Customer $customer): void
    {
        $changes = ModelChangeLoggerHelper::getModelChanges($customer);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'model_name' => 'customers',
            'action' => 'DELETE',
            'changes' => $changes ? json_encode($changes) : null,
        ]);
    }

    /**
     * Handle the Customer "restored" event.
     */
    public function restored(Customer $customer): void
    {
        //
    }

    /**
     * Handle the Customer "force deleted" event.
     */
    public function forceDeleted(Customer $customer): void
    {
        //
    }
}

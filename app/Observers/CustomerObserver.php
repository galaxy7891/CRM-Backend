<?php

namespace App\Observers;

use App\Helpers\ModelChangeLoggerHelper;
use App\Helpers\ReportsHelper;
use App\Models\ActivityLog;
use App\Models\Customer;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CustomerObserver
{
    /**
     * Handle the Customer "created" event.
     */
    public function created(Customer $customer): void
    {
        $userCompanyId = $customer->user()
            ->whereHas('company', function ($query) use ($customer) {
                $query->where('id', $customer->user->user_company_id);
            })
            ->value('user_company_id');

        if ($userCompanyId) {
            if ($customer->customerCategory === 'leads') {
                ReportsHelper::recordAddedLeads($userCompanyId, $customer);
            }
    
            if ($customer->customerCategory === 'contact') {
                ReportsHelper::recordAddedContact($userCompanyId, $customer);
            }
        }

        $changes = ModelChangeLoggerHelper::getModelChanges($customer, 'customers', 'CREATE');
        ActivityLog::create([
            'user_id' => auth()->id() ? auth()->id() : '123e4567-e89b-12d3-a456-426614174100',
            'model_name' => 'customers',
            'action' => 'CREATE',
            'changes' => $changes ? json_encode($changes) : null,
        ]);
    }

    /**
     * Handle the Customer "updated" event.
     */
    public function updated(Customer $customer): void
    {   
        $userCompanyId = $customer->user()
            ->whereHas('company', function ($query) use ($customer) {
                $query->where('id', $customer->user->user_company_id);
            })
            ->value('user_company_id');

        if ($userCompanyId) {
            if ($customer->isDirty('customerCategory') && $customer->customerCategory === 'contact') {
                ReportsHelper::recordConversionContact($userCompanyId, $customer);
            }
        }

        $changes = ModelChangeLoggerHelper::getModelChanges($customer, 'customers', 'UPDATE');

        ActivityLog::create([
            'user_id' => auth()->id() ? auth()->id() : '123e4567-e89b-12d3-a456-426614174100',
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
        $changes = ModelChangeLoggerHelper::getModelChanges($customer, 'customers', 'DELETE');

        $customer->update([
            'email' => time() . '::' . $customer->email,
            'phone' => time() . '::' . $customer->phone,
        ]);
        
        ActivityLog::create([
            'user_id' => auth()->id() ? auth()->id() : '123e4567-e89b-12d3-a456-426614174100',
            'model_name' => 'customers',
            'action' => 'DELETE',
            'changes' => $changes ? json_encode($changes) : null,
        ]);
    }
}

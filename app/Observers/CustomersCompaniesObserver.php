<?php

namespace App\Observers;

use App\Helpers\ModelChangeLoggerHelper;
use App\Helpers\ReportsHelper;
use App\Models\ActivityLog;
use App\Models\CustomersCompany;
use Illuminate\Support\Facades\Log;

class CustomersCompaniesObserver
{
    /**
     * Handle the Customers Company "created" event.
     */
    public function created(CustomersCompany $customersCompany): void
    {
        $userCompanyId = $customersCompany->user()
            ->whereHas('company', function ($query) use ($customersCompany) {
                $query->where('id', $customersCompany->user->user_company_id);
            })
            ->value('user_company_id');

        if ($userCompanyId) {
            ReportsHelper::recordAddedCompany($userCompanyId, $customersCompany);
        }
        
        $changes = ModelChangeLoggerHelper::getModelChanges($customersCompany, 'customers_companies', 'CREATE');

        $activityLog = new ActivityLog();
        $activityLog->user_id = auth()->id() ? auth()->id() : '123e4567-e89b-12d3-a456-426614174100';
        $activityLog->model_name = 'customers_companies';
        $activityLog->action = 'CREATE';
        $activityLog->changes = $changes ? json_encode($changes) : null;
        $activityLog->save();
    }

    /**
     * Handle the Customers Company "updated" event.
     */
    public function updated(CustomersCompany $customersCompany): void
    {   
        $changes = ModelChangeLoggerHelper::getModelChanges($customersCompany, 'customers_companies', 'UPDATE');

        $activityLog = new ActivityLog();
        $activityLog->user_id = auth()->id() ? auth()->id() : '123e4567-e89b-12d3-a456-426614174100';
        $activityLog->model_name = 'customers_companies';
        $activityLog->action = 'UPDATE';
        $activityLog->changes = $changes ? json_encode($changes) : null;
        $activityLog->save();
    }

    /**
     * Handle the Customers Company "deleted" event.
     */
    public function deleted(CustomersCompany $customersCompany): void
    {
        $changes = ModelChangeLoggerHelper::getModelChanges($customersCompany, 'customers_companies', 'DELETE');
        $email = $customersCompany->email ?? null;
        $phone = $customersCompany->phone ?? null;

        $customersCompany->update([
            'name' => time() . '::' . $customersCompany->name,
        ]);
        if (isset($email)){
            $customersCompany->update([
                'email' => time() . '::' . $customersCompany->email,
            ]);
        }
        if (isset($phone)){
            $customersCompany->update([
                'phone' => time() . '::' . $customersCompany->phone,
            ]);
        }

        $activityLog = new ActivityLog();
        $activityLog->user_id = auth()->id() ? auth()->id() : '123e4567-e89b-12d3-a456-426614174100';
        $activityLog->model_name = 'customers_companies';
        $activityLog->action = 'DELETE';
        $activityLog->changes = $changes ? json_encode($changes) : null;
        $activityLog->save();
    }
}

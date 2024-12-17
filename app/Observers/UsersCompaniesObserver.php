<?php

namespace App\Observers;

use App\Helpers\ModelChangeLoggerHelper;
use App\Models\ActivityLog;
use App\Models\UsersCompany;

class UsersCompaniesObserver
{
    /**
     * Handle the UsersCompany "created" event.
     */
    public function created(UsersCompany $userCompany): void
    {
        $changes = ModelChangeLoggerHelper::getModelChanges($userCompany, 'companies', 'CREATE');

        $activityLog = new ActivityLog();
        $activityLog->user_id = auth()->id() ? auth()->id() : null;
        $activityLog->model_name = 'users_companies';
        $activityLog->action = 'CREATE';
        $activityLog->changes = $changes ? json_encode($changes) : null;
        $activityLog->save();
    }

    /**
     * Handle the UsersCompany "updated" event.
     */
    public function updated(UsersCompany $userCompany): void
    {
        $changes = ModelChangeLoggerHelper::getModelChanges($userCompany, 'users_companies', 'UPDATE');

        $activityLog = new ActivityLog();
        $activityLog->user_id = auth()->id() ? auth()->id() : '123e4567-e89b-12d3-a456-426614174100';
        $activityLog->model_name = 'users_companies';
        $activityLog->action = 'UPDATE';
        $activityLog->changes = $changes ? json_encode($changes) : null;
        $activityLog->save();
    }

    /**
     * Handle the Company "deleted" event.
     */
    public function deleted(UsersCompany $userCompany): void
    {
        $changes = ModelChangeLoggerHelper::getModelChanges($userCompany, 'users_companies', 'DELETE');
        $email = $customersCompany->email ?? null;
        $phone = $customersCompany->phone ?? null;
        $website = $customersCompany->website ?? null;

        $userCompany->update([
            'name' => time() . '::' . $userCompany->name,
        ]);
        if (isset($email)){
            $userCompany->update([
                'email' => time() . '::' . $userCompany->email,
            ]);
        }
        if (isset($phone)){
            $userCompany->update([
                'phone' => time() . '::' . $userCompany->phone,
            ]);
        }
        if (isset($website)){
            $userCompany->update([
                'website' => time() . '::' . $userCompany->website,
            ]);
        }

        $activityLog = new ActivityLog();
        $activityLog->user_id = auth()->id() ? auth()->id() : '123e4567-e89b-12d3-a456-426614174100';
        $activityLog->model_name = 'users_companies';
        $activityLog->action = 'DELETE';
        $activityLog->changes = $changes ? json_encode($changes) : null;
        $activityLog->save();
    }
}
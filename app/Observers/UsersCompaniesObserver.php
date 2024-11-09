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

        ActivityLog::create([
            'user_id' => auth()->id() ? auth()->id() : null,
            'model_name' => 'users_companies',
            'action' => 'CREATE',
            'changes' => $changes ? json_encode($changes) : null,
        ]);
    }

    /**
     * Handle the UsersCompany "updated" event.
     */
    public function updated(UsersCompany $userCompany): void
    {
        $changes = ModelChangeLoggerHelper::getModelChanges($userCompany, 'users_companies', 'UPDATE');

        ActivityLog::create([
            'user_id' => auth()->id() ? auth()->id() : '123e4567-e89b-12d3-a456-426614174100',
            'model_name' => 'users_companies',
            'action' => 'UPDATE',
            'changes' => $changes ? json_encode($changes) : null,
        ]);
    }

    /**
     * Handle the Company "deleted" event.
     */
    public function deleted(UsersCompany $userCompany): void
    {
        $changes = ModelChangeLoggerHelper::getModelChanges($userCompany, 'users_companies', 'DELETE');

        ActivityLog::create([
            'user_id' => auth()->id() ? auth()->id() : '123e4567-e89b-12d3-a456-426614174100',
            'model_name' => 'users_companies',
            'action' => 'DELETE',
            'changes' => $changes ? json_encode($changes) : null,
        ]);
    }
}

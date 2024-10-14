<?php

namespace App\Observers;

use App\Helpers\ModelChangeLoggerHelper;
use App\Models\ActivityLog;
use App\Models\Company;

class CompaniesObserver
{
    /**
     * Handle the Company "created" event.
     */
    public function created(Company $company): void
    {
        $changes = ModelChangeLoggerHelper::getModelChanges($company);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'model_name' => 'companies',
            'action' => 'CREATE',
            'changes' => $changes ? json_encode($changes) : null,
        ]);
    }

    /**
     * Handle the Company "updated" event.
     */
    public function updated(Company $company): void
    {
        $changes = ModelChangeLoggerHelper::getModelChanges($company);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'model_name' => 'companies',
            'action' => 'UPDATE',
            'changes' => $changes ? json_encode($changes) : null,
        ]);
    }

    /**
     * Handle the Company "deleted" event.
     */
    public function deleted(Company $company): void
    {
        $changes = ModelChangeLoggerHelper::getModelChanges($company);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'model_name' => 'companies',
            'action' => 'DELETE',
            'changes' => $changes ? json_encode($changes) : null,
        ]);
    }

    /**
     * Handle the Company "restored" event.
     */
    public function restored(Company $company): void
    {
        //
    }

    /**
     * Handle the Company "force deleted" event.
     */
    public function forceDeleted(Company $company): void
    {
        //
    }
}

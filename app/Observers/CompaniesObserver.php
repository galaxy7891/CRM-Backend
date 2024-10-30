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
        $changes = ModelChangeLoggerHelper::getModelChanges($company, 'companies', 'CREATE');

        ActivityLog::create([
            'user_id' => auth()->id() ? auth()->id() : '123e4567-e89b-12d3-a456-426614174100',
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
        $changes = ModelChangeLoggerHelper::getModelChanges($company, 'companies', 'UPDATE');

        ActivityLog::create([
            'user_id' => auth()->id() ? auth()->id() : '123e4567-e89b-12d3-a456-426614174100',
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
        $changes = ModelChangeLoggerHelper::getModelChanges($company, 'companies', 'DELETE');

        ActivityLog::create([
            'user_id' => auth()->id() ? auth()->id() : '123e4567-e89b-12d3-a456-426614174100',
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

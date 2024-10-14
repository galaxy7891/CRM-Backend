<?php

namespace App\Observers;

use App\Helpers\ModelChangeLoggerHelper;
use App\Models\ActivityLog;
use App\Models\Organization;

class OrganizationObserver
{
    /**
     * Handle the Organization "created" event.
     */
    public function created(Organization $organization): void
    {
        $changes = ModelChangeLoggerHelper::getModelChanges($organization);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'model_name' => 'organizations',
            'action' => 'CREATE',
            'changes' => $changes ? json_encode($changes) : null,
        ]);
    }

    /**
     * Handle the Organization "updated" event.
     */
    public function updated(Organization $organization): void
    {
        $changes = ModelChangeLoggerHelper::getModelChanges($organization);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'model_name' => 'organizations',
            'action' => 'UPDATE',
            'changes' => $changes ? json_encode($changes) : null,
        ]);
    }

    /**
     * Handle the Organization "deleted" event.
     */
    public function deleted(Organization $organization): void
    {
        $changes = ModelChangeLoggerHelper::getModelChanges($organization);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'model_name' => 'organizations',
            'action' => 'DELETE',
            'changes' => $changes ? json_encode($changes) : null,
        ]);
    }

    /**
     * Handle the Organization "restored" event.
     */
    public function restored(Organization $organization): void
    {
        //
    }

    /**
     * Handle the Organization "force deleted" event.
     */
    public function forceDeleted(Organization $organization): void
    {
        //
    }
}

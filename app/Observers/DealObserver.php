<?php

namespace App\Observers;

use App\Helpers\ModelChangeLoggerHelper;
use App\Models\ActivityLog;
use App\Models\Deal;

class DealObserver
{
    /**
     * Handle the Deal "created" event.
     */
    public function created(Deal $deal): void
    {
        $changes = ModelChangeLoggerHelper::getModelChanges($deal, 'deals', 'CREATE');

        ActivityLog::create([
            'user_id' => auth()->id() ? auth()->id() : '123e4567-e89b-12d3-a456-426614174100',
            'model_name' => 'deals',
            'action' => 'CREATE',
            'changes' => $changes ? json_encode($changes) : null,
        ]);
    }

    /**
     * Handle the Deal "updated" event.
     */
    public function updated(Deal $deal): void
    {
        $changes = ModelChangeLoggerHelper::getModelChanges($deal, 'deals', 'UPDATE');

        ActivityLog::create([
            'user_id' => auth()->id() ? auth()->id() : '123e4567-e89b-12d3-a456-426614174100',
            'model_name' => 'deals',
            'action' => 'UPDATE',
            'changes' => $changes ? json_encode($changes) : null,
        ]);
    }

    /**
     * Handle the Deal "deleted" event.
     */
    public function deleted(Deal $deal): void
    {
        $changes = ModelChangeLoggerHelper::getModelChanges($deal, 'deals', 'DELETE');

        ActivityLog::create([
            'user_id' => auth()->id() ? auth()->id() : '123e4567-e89b-12d3-a456-426614174100',
            'model_name' => 'deals',
            'action' => 'DELETE',
            'changes' => $changes ? json_encode($changes) : null,
        ]);
    }

    /**
     * Handle the Deal "restored" event.
     */
    public function restored(Deal $deal): void
    {
        //
    }

    /**
     * Handle the Deal "force deleted" event.
     */
    public function forceDeleted(Deal $deal): void
    {
        //
    }
}

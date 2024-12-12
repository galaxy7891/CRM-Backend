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

        $activityLog = new ActivityLog();
        $activityLog->user_id = auth()->id() ? auth()->id() : '123e4567-e89b-12d3-a456-426614174100';
        $activityLog->model_name = 'deals';
        $activityLog->action = 'CREATE';
        $activityLog->changes = $changes ? json_encode($changes) : null;
        $activityLog->save();
    }
    
    /**
     * Handle the Deal "updated" event.
     */
    public function updated(Deal $deal): void
    {
        $changes = ModelChangeLoggerHelper::getModelChanges($deal, 'deals', 'UPDATE'); 

        $activityLog = new ActivityLog();
        $activityLog->user_id = auth()->id() ? auth()->id() : '123e4567-e89b-12d3-a456-426614174100';
        $activityLog->model_name = 'deals';
        $activityLog->action = 'UPDATE';
        $activityLog->changes = $changes ? json_encode($changes) : null;
        $activityLog->save();
    }

    /**
     * Handle the Deal "deleted" event.
     */
    public function deleted(Deal $deal): void
    {
        $changes = ModelChangeLoggerHelper::getModelChanges($deal, 'deals', 'DELETE');

        $activityLog = new ActivityLog();
        $activityLog->user_id = auth()->id() ? auth()->id() : '123e4567-e89b-12d3-a456-426614174100';
        $activityLog->model_name = 'deals';
        $activityLog->action = 'DELETE';
        $activityLog->changes = $changes ? json_encode($changes) : null;
        $activityLog->save();
    }
}

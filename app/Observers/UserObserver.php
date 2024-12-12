<?php

namespace App\Observers;

use App\Helpers\ModelChangeLoggerHelper;
use App\Models\ActivityLog;
use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    { 
        $changes = ModelChangeLoggerHelper::getModelChanges($user, 'users', 'CREATE');

        $activityLog = new ActivityLog();
        $activityLog->user_id = auth()->id() ? auth()->id() : '123e4567-e89b-12d3-a456-426614174100';
        $activityLog->model_name = 'users';
        $activityLog->action = 'CREATE';
        $activityLog->changes = $changes ? json_encode($changes) : null;
        $activityLog->save();
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        $changes = ModelChangeLoggerHelper::getModelChanges($user, 'users', 'UPDATE');

        $activityLog = new ActivityLog();
        $activityLog->user_id = auth()->id() ? auth()->id() : '123e4567-e89b-12d3-a456-426614174100';
        $activityLog->model_name = 'users';
        $activityLog->action = 'UPDATE';
        $activityLog->changes = $changes ? json_encode($changes) : null;
        $activityLog->save();
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        $changes = ModelChangeLoggerHelper::getModelChanges($user, 'users', 'DELETE');

        $user->update([
            'name' => time() . '::' . $user->name,
            'phone' => time() . '::' . $user->phone,
        ]);

        $activityLog = new ActivityLog();
        $activityLog->user_id = auth()->id() ? auth()->id() : '123e4567-e89b-12d3-a456-426614174100';
        $activityLog->model_name = 'users';
        $activityLog->action = 'DELETE';
        $activityLog->changes = $changes ? json_encode($changes) : null;
        $activityLog->save();
    }
}

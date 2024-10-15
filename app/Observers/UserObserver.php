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
        // $changes = ModelChangeLoggerHelper::getModelChanges($user);

        // ActivityLog::create([
        //     'user_id' => auth()->id(),
        //     'model_name' => 'users',
        //     'action' => 'CREATE',
        //     'changes' => $changes ? json_encode($changes) : null,
        // ]);
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        $changes = ModelChangeLoggerHelper::getModelChanges($user);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'model_name' => 'users',
            'action' => 'UPDATE',
            'changes' => $changes ? json_encode($changes) : null,
        ]);
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        $changes = ModelChangeLoggerHelper::getModelChanges($user);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'model_name' => 'users',
            'action' => 'DELETE',
            'changes' => $changes ? json_encode($changes) : null,
        ]);
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}

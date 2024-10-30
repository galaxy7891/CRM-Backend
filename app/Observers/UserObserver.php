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

        ActivityLog::create([
            'user_id' => auth()->id() ? auth()->id() : $user->id,
            'model_name' => 'users',
            'action' => 'CREATE',
            'changes' => $changes ? json_encode($changes) : null,
        ]);
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        $changes = ModelChangeLoggerHelper::getModelChanges($user, 'users', 'UPDATE');

        ActivityLog::create([
            'user_id' => auth()->id() ? auth()->id() : $user->id,
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
        $changes = ModelChangeLoggerHelper::getModelChanges($user, 'users', 'DELETE');

        ActivityLog::create([
            'user_id' => auth()->id() ? auth()->id() : $user->id,
            'model_name' => 'users',
            'action' => 'DELETE',
            'changes' => $changes ? json_encode($changes) : null,
        ]);
    }
}

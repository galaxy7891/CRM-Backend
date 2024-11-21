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
    // public function created(User $user): void
    // { 
    //     $changes = ModelChangeLoggerHelper::getModelChanges($user, 'users', 'CREATE');

    //     ActivityLog::create([
    //         'user_id' => auth()->id() ? auth()->id() : '123e4567-e89b-12d3-a456-426614174100',
    //         'model_name' => 'users',
    //         'action' => 'CREATE',
    //         'changes' => $changes ? json_encode($changes) : null,
    //     ]);
    // }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        $changes = ModelChangeLoggerHelper::getModelChanges($user, 'users', 'UPDATE');

        ActivityLog::create([
            'user_id' => auth()->id() ? auth()->id() : '123e4567-e89b-12d3-a456-426614174100',
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

        $user->update([
            'name' => time() . '::' . $user->name,
            'phone' => time() . '::' . $user->phone,
        ]);

        ActivityLog::create([
            'user_id' => auth()->id() ? auth()->id() : '123e4567-e89b-12d3-a456-426614174100',
            'model_name' => 'users',
            'action' => 'DELETE',
            'changes' => $changes ? json_encode($changes) : null,
        ]);
    }
}

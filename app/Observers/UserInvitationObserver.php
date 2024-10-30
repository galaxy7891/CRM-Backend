<?php

namespace App\Observers;

use App\Helpers\ModelChangeLoggerHelper;
use App\Models\ActivityLog;
use App\Models\UserInvitation;

class UserInvitationObserver
{
    /**
     * Handle the UserInvitation "created" event.
     */
    public function created(UserInvitation $userInvitation): void
    {
        $changes = ModelChangeLoggerHelper::getModelChanges($userInvitation, 'products', 'CREATE');

        ActivityLog::create([
            'user_id' => auth()->id() ? auth()->id() : '123e4567-e89b-12d3-a456-426614174100',
            'model_name' => 'user_invitations',
            'action' => 'CREATE',
            'changes' => $changes ? json_encode($changes) : null,
        ]);
    }
}

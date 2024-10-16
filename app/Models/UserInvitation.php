<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserInvitation extends Model
{
    use HasFactory;

    protected $primaryKey = 'email';
    /**
     * The attributes that are mass assignable.
     * 
     * @var array
     */
    protected $fillable = [
        'email',
        'token',
        'expired_at',
        'status',
        'invited_by',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast to date instances.
     * 
     * @var array
     */
    protected $dates = ['expired_at', 'created_at', 'updated_at'];

    /**
     * Get the inviter(email) that owns the token.
     * 
     * This defines a many-to-one relationship where the user belongs to a company.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function inviter()
    {
        return $this->belongsTo(User::class, 'invited_by', 'email');
    }

    /**
     * Update the status of the invitation.
     *
     * @param string $status
     * @return bool
     */
    public function updateStatus(string $status): bool
    {
        $this->status = $status;
        return $this->save();
    }

    /**
     * Get the invitation has been sent to email within 1 week period.
     *
     * @param string $email
     * @return UserInvitation|null
     */
    public static function getRecentInvitation(string $email): ?self
    {
        return self::where('email', $email)
            ->where('created_at', '>=', now()->subDays(7))
            ->first();
    }

    /**
     * Get the remaining time until the invitation can be resent.
     *
     * @param UserInvitation $invitation
     * @return array
     */
    public static function getRemainingTime(self $invitation): array
    {
        $nextInviteTime = $invitation->created_at;
        $remainingTime = $nextInviteTime->diff(now()->subDays(7));

        return [
            'days' => $remainingTime->d,
            'hours' => $remainingTime->h,
            'minutes' => $remainingTime->i,
            'seconds' => $remainingTime->s,
        ];
    }

    /**
     * Find the user's invitation for the given email.
     *
     * @param array $dataInvitation
     * @return object
     */
    public static function findInvitation(array $dataInvitation)
    {
        return self::where('email', $dataInvitation['email'])
            ->where('token', $dataInvitation['token'])
            ->where('status', 'pending')
            ->where('expired_at', '>', now())
            ->first();
    }

    /**
     * Create a new user invitation .
     *
     * @param array $dataUser
     * @return UserInvitation
     */
    public static function createInvitation(array $dataUser): self
    {
        return self::updateOrCreate(
            ['email' => $dataUser['email']],
            [
            'token' => $dataUser['token'],
            'expired_at' => $dataUser['expired_at'],
            'status' => $dataUser['status'],
            'invited_by' => $dataUser['invited_by'],
            'created_at' => now()
            ]
        );
    }
}
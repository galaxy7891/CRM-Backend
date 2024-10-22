<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordResetToken extends Model
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
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that should be cast to date instances.
     * 
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * Get the reset password token has been sent to email within 1 minutes period.
     *
     * @param string $email
     * @return PasswordResetToken|null
     */
    public static function getRecentResetPasswordToken(string $email): ?self
    {
        return self::where('email', $email)
            ->where('created_at', '>=', now()->subMinutes(1))
            ->first();
    }

    /**
     * Get the remaining time until the reset password token can be resent.
     *
     * @param PasswordResetToken $resetPassword
     * @return array
     */
    public static function getRemainingTime(self $resetPassword): array
    {
        $nextResetPasswordTime = $resetPassword->created_at;
        $remainingTime = $nextResetPasswordTime->diff(now()->subminutes(1));

        return [
            'minutes' => $remainingTime->i,
            'seconds' => $remainingTime->s,
        ];
    }

    /**
     * Find the password reset token for the given email.
     *
     * @param array $dataResetPassword
     * @return object
     */
    public static function findPasswordResetToken(array $dataResetPassword)
    {
        return self::where('email', $dataResetPassword['email'])
            ->where('token', $dataResetPassword['token'])
            ->first();
    }

    /**
     * Create a new password reset token.
     *
     * @param array $dataUser
     * @return PasswordResetToken
     */
    public static function createPasswordResetToken(array $dataUser): self
    {
        return self::updateOrCreate(
            ['email' => $dataUser['email']],
            [
            'token' => $dataUser['token'],
            'created_at' => now()
            ]
        );
    }

    /**
     * Delete the password reset token based on the provided email.
     *
     * @param string|null $email
     * @return null
     */
    public static function deletePasswordResetToken(string $email)
    {
        return self::where('email', $email)
            ->delete();
    }
}

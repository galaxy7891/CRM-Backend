<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class Otp extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'email';
    
    /**
     * The attributes that are mass assignable.
     * 
     * @var array
     */
    protected $fillable = [
        'email',
        'code',
        'expired_at',
        'is_used',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * The attributes that should be cast to date instances.
     * 
     * @var array
     */
    protected $dates = ['expired_at', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * Get the invitation has been sent to email within 5 minutes.
     *
     * @param string $email
     * @return OTP|null
     */
    public static function getRecentOTP(string $email): ?self
    {    
        return self::where('email', $email)
            ->where('created_at', '>=', now()->subMinutes(5))
            ->first();
    }

    /**
     * Get the remaining time until the otp can be resent.
     *
     * @param OTP $otp
     * @return array
     */
    public static function getRemainingTime(self $otp): array
    {
        $nextOtpTime = $otp->created_at;
        $remainingTime = $nextOtpTime->diff(now()->subMinutes(5));


        return [
            'minutes' => $remainingTime->i,
            'seconds' => $remainingTime->s,
        ];
    }

    /**
     * Mark the OTP as used.
     *
     * @param string $email
     * @return void
     */
    public function markAsUsed(string $email)
    {
        return self::where('email', $email)
            ->update(['is_used' => true]);
    }

    /**
     * Method to create a new OTP record.
     *
     * @param string $email
     * @param string $code
     * @return OTP|null
     */
    public static function findOTP(string $email, string $code)
    {
        return self::where('email', $email)
            ->where('code', $code)
            ->where('is_used', false)
            ->where('expired_at', '>', now())
            ->latest('created_at')
            ->first();
    }

    /**
     * Method to create a new OTP record.
     *
     * @param array $dataOtp
     * @return OTP
     */
    public static function createOTP(array $dataOtp): self
    {
        return self::updateOrCreate(
            ['email' => $dataOtp['email']],
            [
            'code' => $dataOtp['code'],
            'expired_at' => now()->addMinutes(5),
            'is_used' => false,
            'created_at' => now()
            ]
        );
    }
}

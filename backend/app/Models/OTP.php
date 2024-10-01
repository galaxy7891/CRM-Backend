<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Otp extends Model
{
    use HasFactory, SoftDeletes, HasUuid;

    protected $primaryKey = 'otp_id';

    /**
     * The attributes that are mass assignable.
     * 
     * @var array
     */
    protected $fillable = [
        'otp_id',
        'email',
        'code',
        'expire_at',
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
    protected $dates = ['expire_at', 'created_at', 'updated_at', 'deleted_at'];



    /**
     * Method to create a new OTP record.
     *
     * @param string $email
     * @param string $code
     * @return self
     */
    public static function createOTP(string $email, string $code): self
    {
        return self::create([
            'email' => $email,
            'code' => $code,
            'expire_at' => now()->addMinutes(5),
            'is_used' => false,
        ]);
    }



    /**
     * Find the OTP code for the given email.
     *
     * @param string $email
     * @param string $code
     * @return object
     */
    public static function findOTP(string $email)
    {
        return self::where('email', $email)
            ->where('is_used', false)
            ->where('expire_at', '>', now())
            ->latest('created_at')
            ->first();
    }

}
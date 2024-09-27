<?php

namespace App\Models;

use Illuminate\Support\Str;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, SoftDeletes, Notifiable;

    /**
     * The attributes that are mass assignable.
     * 
     * @var array
     */
    protected $fillable = [
        'user_id',
        'company_id',
        'email',
        'first_name',
        'last_name',
        'password',
        'phone',
        'role',
        'gender',
        'photo',
        'created_at',
        'updated_at',
        'deleted_at',
    ];



    /**
     * The attributes that should be cast to date instances.
     * 
     * @var array
     */
    protected $dates = ['createdAt', 'updatedAt', 'deletedAt'];



    /**
     * Get the company that owns the user.
     * 
     * This defines a many-to-one relationship where the user belongs to a company.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }



    /**
     * Get the customers associated with the user.
     * Get the deals associated with the user.
     * Get the loggers associated with the user.
     * 
     * This defines a one-to-many relationship where the user can have multiple customers.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function customers()
    {
        return $this->hasMany(Customer::class, 'user_id');
    }

    public function deals()
    {
        return $this->hasMany(Deal::class, 'user_id');
    }

    public function loggers()
    {
        return $this->hasMany(Logger::class, 'user_id');
    }



    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }



    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }



    /**
     * Create or update a user based on Google OAuth data.
     * 
     * This method is used for authenticating users through their Google account.
     * It will either create a new user or update the existing user's details.
     * 
     * @param object $googleUser The user object returned from Google OAuth
     * @return \App\Models\User The created or updated user model
     */
    public static function createOrUpdateGoogleUser($googleUser, $nameParts)
    {

        return self::updateOrCreate(
            ['email' => $googleUser->email],
            [
                'first_name' => $nameParts['first_name'],
                'last_name' => $nameParts['last_name'],
                'photo' => $googleUser->avatar,
                'google_id' => $googleUser->id,
            ]
        );
    }
}


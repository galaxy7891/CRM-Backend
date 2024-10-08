<?php

namespace App\Models;

use App\Traits\HasUuid;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Contracts\JWTSubject;


class User extends Authenticatable implements JWTSubject
{
    use HasFactory, SoftDeletes, Notifiable, HasUuid;

    /**
     * The attributes that are mass assignable.
     * 
     * @var array
     */
    protected $fillable = [
        'id',
        'company_id',
        'google_id',
        'email',
        'first_name',
        'last_name',
        'password',
        'phone',
        'job_position',
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
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * Get the company that owns the user.
     * 
     * This defines a many-to-one relationship where the user belongs to a company.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'id');
    }

    /**
     * Get the customers associated with the user.
     * Get the deals associated with the user.
     * Get the loggers associated with the user.
     * 
     * This defines a one-to-many relationship where the user can have multiple customers, deals, loggers.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function customers()
    {
        return $this->hasMany(Customer::class, 'id');
    }

    public function deals()
    {
        return $this->hasMany(Deal::class, 'id');
    }

    public function loggers()
    {
        return $this->hasMany(Logger::class, 'id');
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
     * Find user by email
     * 
     * @param string $email
     * @return User|null
     */
    public static function findByEmail($email)
    {
        return self::where('email', $email)->first();
    }

    /**
     * Update password user.
     *
     * @param string $new_password
     * @return User
     */
    public function updatePassword(string $new_password)
    {
        $this->password = Hash::make($new_password);
        return $this->save();
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
                'role' => 'super_admin',
                'photo' => $googleUser->avatar,
                'google_id' => $googleUser->id,
            ]
        );
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $dataUser
     * @param string $company_id 
     * @return User
     */
    public static function createUser(array $dataUser, ?string $company_id): self
    {
        return self::create([
            'company_id' => $company_id ?? null,
            'email' => $dataUser['email'],
            'first_name' => $dataUser['first_name'],
            'last_name' => $dataUser['last_name'],
            'password' => Hash::make($dataUser['password']) ?? null,
            'phone' => $dataUser['phone'] ?? null,
            'job_position' => $dataUser['job_position'] ?? null,
        ]);
    }
}

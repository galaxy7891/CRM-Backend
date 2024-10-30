<?php

namespace App\Models;

use App\Traits\HasUuid;
use Cloudinary\Cloudinary;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Contracts\JWTSubject;


class User extends Authenticatable implements JWTSubject
{
    use SoftDeletes, Notifiable, HasUuid;

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
        'image_url',
        'image_public_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $hidden = [
        'password',
        'google_id',
    ];

    /**
     * The attributes that should be cast to date instances.
     * 
     * @var array
     */
    protected $dates = [
        'created_at', 
        'updated_at', 
        'deleted_at'
    ];

    /**
     * Get the company that owns the user.
     * 
     * This defines a many-to-one relationship where the user belongs to a company.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class,'company_id', 'id');
    }

    /**
     * Get the customers associated with the user.
     * Get the deals associated with the user.
     * Get the activitylogs associated with the user.
     * Get the invitation associated with the user.
     * 
     * This defines a one-to-many relationship where the user can have multiple customers, deals, activitylogs.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function customers()
    {
        return $this->hasMany(Customer::class, 'owner', 'email');
    }

    public function deals()
    {
        return $this->hasMany(Deal::class, 'owner','email');
    }

    public function activitylogs()
    {
        return $this->hasMany(ActivityLog::class, 'user_id', 'id');
    }

    public function invitations()
    {  
        return $this->hasMany(UserInvitation::class, 'invited_by', 'email');
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
    public static function findByEmail(string $email)
    {
        return self::where('email', $email)->first();
    }

    /**
     * Get the user's full name by ID.
     *
     * @param  int|string  $id
     * @return string|null
     */
    public static function getUserNameById($id)
    {
        $user = self::select('first_name', 'last_name')
            ->where('id', $id)
            ->first();

        return $user ? trim($user->first_name . ' ' . $user->last_name) : null;
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $dataUser
     * @param string $companyId 
     * @return User
     */
    public static function createUser(array $dataUser, ?string $companyId): self
    {
        return self::create([
            'google_id' => $dataUser['google_id'] ?? null,
            'company_id' => $companyId ?? null,
            'email' => $dataUser['email'],
            'first_name' => $dataUser['first_name'],
            'last_name' => $dataUser['last_name'] ?? null,
            'password' => Hash::make($dataUser['password']) ?? null,
            'phone' => $dataUser['phone'],
            'job_position' => $dataUser['job_position'] ?? null,
            'image_url' => $dataUser['photo'] ?? null,
        ]);
    }

    /**
     * Update user
     *
     * @param array $dataUser
     * @param string $userId 
     * @return User
     */
    public static function updateUser(array $dataUser, string $userId): self
    {
        $user = self::findOrFail($userId);

        $user->update([
            'company_id' => $dataUser['company_id'] ?? $user->company_id,
            'email' => $dataUser['email'] ?? $user->email,
            'first_name' => $dataUser['first_name'] ?? $user->first_name,
            'last_name' => $dataUser['last_name'] ?? $user->last_name,
            'phone' => $dataUser['phone'] ?? $user->phone,
            'job_position' => $dataUser['job_position'] ?? $user->job_position,
            'role' => $dataUser['role'] ?? $user->role,
            'gender' => $dataUser['gender'] ?? $user->gender,
        ]);

        return $user; 
    }

    /**
     * Update password user.
     *
     * @param string $newPassword
     * @return User
     */
    public function updatePassword(string $newPassword)
    {
        $this->password = Hash::make($newPassword);
        return $this->save();
    }

    /**
     * Update the profile photo URL and public_id of the user.
     *
     * @param \Illuminate\Http\UploadedFile $photo
     * @param string $userId 
     * @return array 
     */
    public function updateProfilePhoto($photo, string $userId)
    {
        $user = self::findOrFail($userId);

        $cloudinary = new Cloudinary();
        if ($user->image_public_id) {
            $cloudinary->uploadApi()->destroy($user->image_public_id);
        }

        $uploadResult = $cloudinary->uploadApi()->upload($photo->getRealPath(), [
            'folder' => 'users',
        ]);
        $user->update([
            'image_url' => $uploadResult['secure_url'],
            'image_public_id' => $uploadResult['public_id'],
        ]);

        return [
            'image_url' => $uploadResult['secure_url'],
            'image_public_id' => $uploadResult['public_id'],
        ];
    }

    public static function deleteUser($id): self
    {
        $user = self::find($id);
        $cloudinary = new Cloudinary();

        if ($user->image_public_id) {
            $cloudinary->uploadApi()->destroy($user->image_public_id);
        }

        $user->delete();
        return $user;
    }
}

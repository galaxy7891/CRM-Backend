<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Organization extends Model
{
    use HasFactory, SoftDeletes, HasUuid;

    /**
     * The attributes that are mass assignable.
     * 
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'industry',
        'email',
        'status',
        'phone',
        'owner',
        'description',
        'website',
        'address',
        'province',
        'city',
        'subdistrict',
        'village',
        'zip_code',
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
     * Get the user that owns the organization.
     * Get the customers associated with the organization.
     * 
     * This defines a one-to-many relationship where the user can have multiple customers.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    
     public function user()
     {
         return $this->belongsTo(User::class, 'owner', 'email');
     }

    public function customers()
    {
        return $this->hasMany(Customer::class, 'organization_id', 'id');
    }

    /**
     * Get the organization's full name by ID.
     *
     * @param  int|string  $id
     * @return string|null
     */
    public static function getOrganizationNameById($id)
    {
        $organization = self::select('name')
            ->where('id', $id)
            ->first();

        return $organization ? $organization->name : null;
    }

    /**
     * Count Organization Owned
     * 
     * @param string $email
     * @return int
     */
    public static function countOrganization($email)
    {
        return self::where('owner', $email)
            ->count();
    }

    public static function createOrganization(array $dataOrganization): self
    {
        return self::create([
            'name' => $dataOrganization['name'],
            'industry' => $dataOrganization['industry'] ?? null,
            'status' => $dataOrganization['status'],
            'email' => $dataOrganization['email'] ?? null,
            'phone' => $dataOrganization['phone'] ?? null,
            'owner' => $dataOrganization['owner'],
            'website' => $dataOrganization['website'] ?? null,
            'address' => $dataOrganization['address'] ?? null,
            'province' => $dataOrganization['province'] ?? null,
            'city' => $dataOrganization['city'] ?? null,
            'subdistrict' => $dataOrganization['subdistrict'] ?? null,
            'village' => $dataOrganization['village'] ?? null,
            'zip_code' => $dataOrganization['zip_code'] ?? null,
            'description' => $dataOrganization['description'] ?? null
        ]);
    }

    public static function updateOrganization(array $dataOrganization, string $organizationId): self
    {
        $organization = self::findOrFail($organizationId);
        $organization->update([
            'name' => $dataOrganization['name'] ?? $organization->name,
            'industry' => $dataOrganization['industry'] ?? $organization->industry,
            'status' => $dataOrganization['status'] ?? $organization->status,
            'email' => $dataOrganization['email'] ?? $organization->email,
            'phone' => $dataOrganization['phone'] ?? $organization->phone,
            'owner' => $dataOrganization['owner'] ?? $organization->owner,
            'website' => $dataOrganization['website'] ?? $organization->website,
            'address' => $dataOrganization['address'] ?? $organization->address,
            'province' => $dataOrganization['province'] ?? $organization->province,
            'city' => $dataOrganization['city'] ?? $organization->city,
            'subdistrict' => $dataOrganization['subdistrict'] ?? $organization->subdistrict,
            'village' => $dataOrganization['village'] ?? $organization->village,
            'zip_code' => $dataOrganization['zip_code'] ?? $organization->zip_code,
            'description' => $dataOrganization['description'] ?? $organization->description,
        ]);

        return $organization;
    }
}

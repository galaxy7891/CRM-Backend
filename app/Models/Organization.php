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
        'website',
        'address',
        'country',
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
     * Get the customers associated with the organization.
     * 
     * This defines a one-to-many relationship where the user can have multiple customers.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function customers()
    {
        return $this->hasMany(Customer::class, 'organization_id', 'id');
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

    public static function createOrganization(array $data): self
    {
        return self::create([
            'name' => $data['name'],
            'industry' => $data['industry'] ?? null,
            'status' => $data['status'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'owner' => $data['owner'],
            'website' => $data['website'] ?? null,
            'address' => $data['address'] ?? null,
            'country' => $data['country'] ?? null,
            'city' => $data['city'] ?? null,
            'subdistrict' => $data['subdistrict'] ?? null,
            'village' => $data['village'] ?? null,
            'zip_code' => $data['zip_code'] ?? null
        ]);
    }

    public static function updateOrganization(array $data, $id): self
    {
        $organization = self::findOrFail($id);
        $organization->fill([
            'name' => $data['name'],
            'industry' => $data['industry'] ?? null,
            'status' => $data['status'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'owner' => $data['owner'],
            'website' => $data['website'] ?? null,
            'address' => $data['address'] ?? null,
            'country' => $data['country'] ?? null,
            'city' => $data['city'] ?? null,
            'subdistrict' => $data['subdistrict'] ?? null,
            'village' => $data['village'] ?? null,
            'zip_code' => $data['zip_code'] ?? null,
        ]);

        $organization->save();
        return $organization;
    }
}

<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomersCompany extends Model
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
     * Get the user that owns the CustomersCompany.
     * Get the customers associated with the CustomersCompany.
     * Get the deals associated with the CustomersCompany.
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
        return $this->hasMany(Customer::class, 'customers_company_id', 'id');
    }

    public function deals()
    {
        return $this->hasMany(Deal::class, 'customers_company_id', 'id');
    }

    /**
     * Get the CustomersCompany's full name by ID.
     *
     * @param  int|string  $id
     * @return string|null
     */
    public static function getCustomersCompanyNameById($id)
    {
        $CustomersCompany = self::select('name')
            ->where('id', $id)
            ->first();

        return $CustomersCompany ? $CustomersCompany->name : null;
    }

    /**
     * Count CustomersCompany Owned
     * 
     * @param string $email
     * @return int
     */
    public static function countCustomersCompany($email, $role, $userCompanyId)
    {
        $query = self::whereHas('user', function ($ownerQuery) use ($userCompanyId) {
            $ownerQuery->where('user_company_id', $userCompanyId);
        });

        if ($role !== 'super_admin' && $role !== 'admin') {
            $query->where('owner', $email);
        }
        
        return $query->count();
    }

    public static function createCustomersCompany(array $dataCustomersCompany): self
    {
        return self::create([
            'name' => $dataCustomersCompany['name'],
            'industry' => $dataCustomersCompany['industry'] ?? null,
            'status' => $dataCustomersCompany['status'],
            'email' => $dataCustomersCompany['email'] ?? null,
            'phone' => $dataCustomersCompany['phone'] ?? null,
            'owner' => $dataCustomersCompany['owner'],
            'website' => $dataCustomersCompany['website'] ?? null,
            'address' => $dataCustomersCompany['address'] ?? null,
            'province' => $dataCustomersCompany['province'] ?? null,
            'city' => $dataCustomersCompany['city'] ?? null,
            'subdistrict' => $dataCustomersCompany['subdistrict'] ?? null,
            'village' => $dataCustomersCompany['village'] ?? null,
            'zip_code' => $dataCustomersCompany['zip_code'] ?? null,
            'description' => $dataCustomersCompany['description'] ?? null
        ]);
    }

    public static function updateCustomersCompany(array $dataCustomersCompany, string $customersCompanyId): self
    {
        $customersCompany = self::findOrFail($customersCompanyId);
        $customersCompany->update([
            'name' => $dataCustomersCompany['name'] ?? $customersCompany->name,
            'industry' => $dataCustomersCompany['industry'] ?? $customersCompany->industry,
            'status' => $dataCustomersCompany['status'] ?? $customersCompany->status,
            'email' => $dataCustomersCompany['email'] ?? $customersCompany->email,
            'phone' => $dataCustomersCompany['phone'] ?? $customersCompany->phone,
            'owner' => $dataCustomersCompany['owner'] ?? $customersCompany->owner,
            'website' => $dataCustomersCompany['website'] ?? $customersCompany->website,
            'address' => $dataCustomersCompany['address'] ?? $customersCompany->address,
            'province' => $dataCustomersCompany['province'] ?? $customersCompany->province,
            'city' => $dataCustomersCompany['city'] ?? $customersCompany->city,
            'subdistrict' => $dataCustomersCompany['subdistrict'] ?? $customersCompany->subdistrict,
            'village' => $dataCustomersCompany['village'] ?? $customersCompany->village,
            'zip_code' => $dataCustomersCompany['zip_code'] ?? $customersCompany->zip_code,
            'description' => $dataCustomersCompany['description'] ?? $customersCompany->description,
        ]);

        return $customersCompany;
    }
}

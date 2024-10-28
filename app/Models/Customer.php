<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes, HasUuid;

    /**
     * The attributes that are mass assignable.
     * 
     * @var array
     */
    protected $fillable = [
        'id',
        'organization_id',
        'first_name',
        'last_name',
        'customerCategory',
        'job',
        'description',
        'status',
        'birthdate',
        'email',
        'phone',
        'owner',
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
    protected $dates = ['created_at', 'updated_at', 'deleted_at', 'birthdate'];

    /**
     * Get the user that owns the customer.
     * Get the organization that owns the customer.
     * 
     * This defines a many-to-one relationship where the user belongs to a company.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'owner', 'email');
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id', 'id');
    }

    /**
     * Get the deals associated with the customer.
     * 
     * This defines a one-to-many relationship where the user can have multiple customers.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function deals()
    {
        return $this->hasMany(Deal::class, 'customer_id', 'id');
    }
    
    public static function findCustomerByIdCategory(string $id, string $customerCategory)
    {
        return self::where('id', $id)
            ->where('customerCategory', $customerCategory)
            ->first();
    }

    /**
     * Count Customer User by Category
     * 
     * @param string $email
     * @param string $category
     * @return int
     */
    public static function countCustomerByCategory($email, $category)
    {
        return self::where('owner', $email)
            ->where('customerCategory', $category)
            ->count();
    }

    public static function createCustomer(array $dataCustomer): self
    {
        return self::create([
            'organization_id' => $dataCustomer['organization_id'] ?? null,
            'first_name' => $dataCustomer['first_name'],
            'last_name' => $dataCustomer['last_name'] ?? null,
            'customerCategory' => $dataCustomer['customerCategory'],
            'job' => $dataCustomer['job'] ?? null,
            'description' => $dataCustomer['description'] ?? null,
            'status' => $dataCustomer['status'],
            'birthdate' => $dataCustomer['birthdate'] ?? null,
            'email' => $dataCustomer['email'] ?? null,
            'phone' => $dataCustomer['phone'],
            'owner' => $dataCustomer['owner'],
            'address' => $dataCustomer['address'] ?? null,
            'country' => $dataCustomer['country'] ?? null,
            'city' => $dataCustomer['city'] ?? null,
            'subdistrict' => $dataCustomer['subdistrict'] ?? null,
            'village' => $dataCustomer['village'] ?? null,
            'zip_code' => $dataCustomer['zip_code'] ?? null,
        ]);
    }

    public static function updateCustomer(array $dataCustomer, string $customerId): self
    {
        $customer = self::findOrFail($customerId);
        
        $customer->update([
            'organization_id' => $dataCustomer['organization_id'] ?? $customer->organization_id,
            'first_name' => $dataCustomer['first_name'] ?? $customer->first_name,
            'last_name' => $dataCustomer['last_name'] ?? $customer->last_name,
            'customerCategory' => $dataCustomer['customerCategory'] ?? $customer->customerCategory,
            'job' => $dataCustomer['job'] ?? $customer->job,
            'description' => $dataCustomer['description'] ?? $customer->description,
            'status' => $dataCustomer['status'] ?? $customer->status,
            'birthdate' => $dataCustomer['birthdate'] ?? $customer->birthdate,
            'email' => $dataCustomer['email'] ?? $customer->email,
            'phone' => $dataCustomer['phone'] ?? $customer->phone,
            'owner' => $dataCustomer['owner'] ?? $customer->owner,
            'address' => $dataCustomer['address'] ?? $customer->address,
            'country' => $dataCustomer['country'] ?? $customer->country,
            'city' => $dataCustomer['city'] ?? $customer->city,
            'subdistrict' => $dataCustomer['subdistrict'] ?? $customer->subdistrict,
            'village' => $dataCustomer['village'] ?? $customer->village,
            'zip_code' => $dataCustomer['zip_code'] ?? $customer->zip_code,
        ]);

        return $customer;
    }

    public static function convert(array $dataCustomer, string $customerId): self
    {
        $customer = self::findOrFail($customerId);
        
        $customer->update([
            'organization_id' => $dataCustomer['organization_id'] ?? $customer->organization_id,
            'first_name' => $dataCustomer['first_name'] ?? $customer->first_name,
            'last_name' => $dataCustomer['last_name'] ?? $customer->last_name,
            'customerCategory' => 'contact',
            'job' => $dataCustomer['job'] ?? $customer->job,
            'description' => $dataCustomer['description'] ?? $customer->description,
            'status' => $dataCustomer['status'] ?? $customer->status,
            'birthdate' => $dataCustomer['birthdate'] ?? $customer->birthdate,
            'email' => $dataCustomer['email'] ?? $customer->email,
            'phone' => $dataCustomer['phone'] ?? $customer->phone,
            'owner' => $dataCustomer['owner'] ?? $customer->owner,
            'address' => $dataCustomer['address'] ?? $customer->address,
            'country' => $dataCustomer['country'] ?? $customer->country,
            'city' => $dataCustomer['city'] ?? $customer->city,
            'subdistrict' => $dataCustomer['subdistrict'] ?? $customer->subdistrict,
            'village' => $dataCustomer['village'] ?? $customer->village,
            'zip_code' => $dataCustomer['zip_code'] ?? $customer->zip_code,
        ]);

        return $customer;
    }
}

<?php

namespace App\Models;

use App\Traits\HasUuid;
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
        'customers_company_id',
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
    protected $dates = ['created_at', 'updated_at', 'deleted_at', 'birthdate'];

    /**
     * Get the user that owns the customer.
     * Get the customersCompany that owns the customer.
     * 
     * This defines a many-to-one relationship where the user belongs to a company.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'owner', 'email');
    }

    public function customersCompany()
    {
        return $this->belongsTo(customersCompany::class, 'customers_company_id', 'id');
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

    /**
     * Get the customer's full name by ID.
     *
     * @param  int|string  $id
     * @return string|null
     */
    public static function getCustomerNameById(string $id)
    {
        $customer = self::select('first_name', 'last_name')
            ->where('id', $id)
            ->first();

        return $customer ? trim($customer->first_name . ' ' . $customer->last_name) : null;
    }

    /**
     * Get the customer's by ID and category.
     *
     * @param string $id
     * @param string $customerCategory
     * @return self
     */
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
            'customers_company_id' => $dataCustomer['customers_company_id'] ?? null,
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
            'province' => $dataCustomer['province'] ?? null,
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
            'customers_company_id' => $dataCustomer['customers_company_id'] ?? $customer->customers_company_id,
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
            'province' => $dataCustomer['province'] ?? $customer->province,
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
            'customers_company_id' => $dataCustomer['customers_company_id'] ?? $customer->customers_company_id,
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
            'province' => $dataCustomer['province'] ?? $customer->province,
            'city' => $dataCustomer['city'] ?? $customer->city,
            'subdistrict' => $dataCustomer['subdistrict'] ?? $customer->subdistrict,
            'village' => $dataCustomer['village'] ?? $customer->village,
            'zip_code' => $dataCustomer['zip_code'] ?? $customer->zip_code,
        ]);

        return $customer;
    }
}

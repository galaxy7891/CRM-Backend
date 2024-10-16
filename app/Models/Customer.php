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

    public static function createCustomer(array $data): self
    {
        return self::create([
            'organization_id' => $data['organization_id'] ?? null,
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'] ?? null,
            'customerCategory' => $data['customerCategory'],
            'job' => $data['job'] ?? null,
            'description' => $data['description'] ?? null,
            'status' => $data['status'],
            'birthdate' => $data['birthdate'] ?? null,
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'],
            'owner' => $data['owner'],
            'address' => $data['address'] ?? null,
            'country' => $data['country'] ?? null,
            'city' => $data['city'] ?? null,
            'subdistrict' => $data['subdistrict'] ?? null,
            'village' => $data['village'] ?? null,
            'zip_code' => $data['zip_code'] ?? null,
        ]);
    }

    public static function updateCustomer(array $data, $id): self
    {
        $customer = self::findOrFail($id);
        $customer->fill([
            'organization_id' => $data['organization_id'] ?? null,
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'] ?? null,
            'customerCategory' => $data['customerCategory'],
            'job' => $data['job'] ?? null,
            'description' => $data['description'] ?? null,
            'status' => $data['status'],
            'birthdate' => $data['birthdate'] ?? null,
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'],
            'owner' => $data['owner'] ?? null,
            'address' => $data['address'] ?? null,
            'country' => $data['country'] ?? null,
            'city' => $data['city'] ?? null,
            'subdistrict' => $data['subdistrict'] ?? null,
            'village' => $data['village'] ?? null,
            'zip_code' => $data['zip_code'] ?? null,
        ]);

        $customer->save();
        return $customer;
    }
}

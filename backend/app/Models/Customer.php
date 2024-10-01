<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;


    /**
     * The attributes that are mass assignable.
     * 
     * @var array
     */
    protected $fillable = [
        'id',
        'organization_id',
        'user_id',
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
        return $this->belongsTo(User::class, 'user_id');
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id');
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
        return $this->hasMany(Deal::class, 'customer_id');
    }
}

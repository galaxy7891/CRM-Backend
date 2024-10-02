<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Organization extends Model
{

    use HasFactory, SoftDeletes;
    protected $primaryKey = 'organization_id';

    /**
     * The attributes that are mass assignable.
     * 
     * @var array
     */
    protected $fillable = [
        'organization_id',
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
        return $this->hasMany(Customer::class, 'organization_id');
    }
}

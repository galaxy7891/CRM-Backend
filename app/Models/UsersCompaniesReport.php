<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UsersCompaniesReport extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     * 
     * @var array
     */
    protected $fillable = [
        'id',
        'user_company_id',
        'total_trial',
        'total_regular',
        'total_professional',
        'total_business',
        'total_unactive',
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
     * Get the user companies that owns the users companies report.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function userCompany()
    {
        return $this->belongsTo(UsersCompany::class, 'user_company_id');
    }
}

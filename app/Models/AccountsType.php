<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountsType extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'id',
        'account_type',
        'start_date',
        'end_date',
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
     * Get the user companies that owns the accounts type.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function userCompany()
    {
        return $this->belongsTo(UsersCompany::class, 'user_company_id');
    }

    /**
     * Get the total companies for each account type.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function countCompaniesByAccountType()
    {
        return self::withCount('userCompany')
            ->get()
            ->mapWithKeys(function ($accountType) {
                return [
                    $accountType->account_type => $accountType->user_company_count
                ];
            });
    }
}

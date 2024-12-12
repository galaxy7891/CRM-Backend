<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountsType extends Model
{
    use HasFactory, SoftDeletes, HasUuid;
    
    protected $fillable = [
        'id',
        'user_company_id',
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
        return $this->belongsTo(UsersCompany::class, 'user_company_id', 'id');
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
    
    public static function createAccountsType(string $userCompanyId): self
    {
        $accountsTypes = new AccountsType();
        $accountsTypes->account_type = 'trial';
        $accountsTypes->user_company_id = $userCompanyId;
        $accountsTypes->start_date = now();
        $accountsTypes->end_date = now()->addDays(7);
        $accountsTypes->save();

        return $accountsTypes;
    }
    
    public static function updateAccountsType(array $dataAccountsType, string $accountsTypeId): self
    {   
        $accountsType = self::findOrFail($accountsTypeId);
        $accountsType->update([
            'account_type' => $dataAccountsType['account_type'] ?? $accountsType->account_type,
            'user_company_id' => $dataAccountsType['user_company_id'] ?? $accountsType->user_company_id,
            'start_date' => $dataAccountsType['start_date'],
            'end_date' => $dataAccountsType['end_date'],
        ]);

        return $accountsType; 
    } 
}

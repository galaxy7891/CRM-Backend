<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Deal extends Model
{
    use HasFactory, SoftDeletes, HasUuid;

    /**
     * The attributes that are mass assignable.
     * 
     * @var array
     */
    protected $fillable = [
        'id',
        'category',
        'customer_id',
        'customers_company_id',
        'name',
        'description',
        'tag',
        'stage',
        'status',
        'open_date',
        'close_date',
        'expected_close_date',
        'value_estimated',
        'value_actual',
        'payment_category',
        'payment_duration',
        'owner',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * The attributes that should be cast to date instances.
     * 
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at', 'open_date', 'close_date', 'expected_close_date'];

    /**
     * Scope a query to search by various attributes.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|null $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function search($query, $search)
    {
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%$search%");
            });
        }
        
        return $query;
    }

    /**
     * The products that belong to the deal.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'deals_products', 'deals_id', 'product_id')
                    ->withPivot('quantity', 'unit')
                    ->select(['products.id', 'products.name', 'products.price']);
    }

    /**
     * Get the customer that owns the deal.
     * Get the user that owns the deal.
     * Get the customers company that owns the deal.
     * 
     * 
     * This defines a many-to-one relationship where the user belongs to a company.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'owner', 'email');
    }

    public function customersCompany()
    {
        return $this->belongsTo(CustomersCompany::class, 'customers_company_id', 'id');
    }

    /**
     * Get the deals products associated with the deals.
     * 
     * This defines a one-to-many relationship where the deals product can have multiple deals.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function dealsProducts()
    {
        return $this->hasMany(DealsProduct::class, 'deals_id', 'id');
    }

    /** 
     * Get the deals's by ID.
     * 
     * @param  int  $id
     * @return self
     */
    public static function findDealsById(string $id)
    {
        return self::where('id', $id)
            ->first();
    }

    /**
     * count the deal.
     *  
     * @return self
     */
    public static function countDeals($userCompanyIds)
    {
        return self::whereHas('user', function ($ownerQuery) use ($userCompanyIds) {
            $ownerQuery->where('user_company_id', $userCompanyIds);
        })->count();
    }

    /**
     * Get the deals's full name by ID.
     *
     * @param  int|string  $id
     * @return string|null
     */
    public static function getDealsNameById($id)
    {
        $deals = self::select('name')
            ->where('id', $id)
            ->first();

        return $deals ? $deals->name : null;
    }

    /**
     * Count Deals User by Stage
     * 
     * @param string $email
     * @param string $stage
     * @return int
     */
    public static function countDealsByStage($email, $role, $userCompanyId, $stage)
    {
        $query = self::whereHas('user', function ($ownerQuery) use ($userCompanyId) {
            $ownerQuery->where('user_company_id', $userCompanyId);
        });
        
        $query->where('stage', $stage);

        if ($role !== 'super_admin' && $role !== 'admin') {
            $query->where('owner', $email);
        }
        
        return $query->count();
    }

    /**
     * Get total value_estimated for each stage.
     * 
     * @param string $email
     * @return \Illuminate\Support\Collection
     */
    public static function sumValueEstimatedByStage($email, $role, $userCompanyId)
    {
        $query = self::select('stage', \Illuminate\Support\Facades\DB::raw("
                SUM(
                    CASE 
                        WHEN stage = 'won' THEN value_actual 
                        ELSE value_estimated 
                    END
                ) as total_value
            "))
            ->groupBy('stage');
        
        $query->whereHas('user', function ($ownerQuery) use ($userCompanyId) {
            $ownerQuery->where('user_company_id', $userCompanyId);
        });

        if ($role !== 'super_admin' && $role !== 'admin') {
            $query->where('owner', $email);
        }

        $results = $query->pluck('total_value', 'stage');
        
        return [
            'qualification' => $results->get('qualificated', 0),
            'proposal' => $results->get('proposal', 0),
            'negotiation' => $results->get('negotiate', 0),
            'won' => $results->get('won', 0),
            'lose' => $results->get('lose', 0),
        ];
    }


    public static function createDeal(array $dataDeals): self
    {
        $deals = new Deal();
        $deals->name = $dataDeals['name'] ?? null;
        $deals->category = $dataDeals['category'] ?? null;
        $deals->customer_id = $dataDeals['customer_id'] ?? null;
        $deals->customers_company_id = $dataDeals['customers_company_id'] ?? null; 
        $deals->payment_category = $dataDeals['payment_category'] ?? null;
        $deals->payment_duration = $dataDeals['payment_duration'] ?? null;
        $deals->value_estimated = $dataDeals['value_estimated'] ?? null;
        $deals->value_actual = $dataDeals['value_actual'] ?? null;
        $deals->stage = $dataDeals['stage'] ?? null;
        $deals->open_date = $validatedData['open_date'] ?? now()->format('Y-m-d');
        $deals->expected_close_date = $dataDeals['expected_close_date'] ?? null;
        $deals->close_date = $dataDeals['close_date'] ?? null;
        $deals->status = $dataDeals['status'] ?? null;
        $deals->tag = $dataDeals['tag'] ?? null;
        $deals->owner = $dataDeals['owner'] ?? null;
        $deals->description = $dataDeals['description'] ?? null;

        $deals->save();
        return $deals;
    }

    public static function updateDeal(array $dataDeals, string $dealsId): self 
    {
        $deals = self::findOrFail($dealsId);
        $deals->update([
            'name' => $dataDeals['name'] ?? $deals->name,
            'category' => $datadeal['category'] ?? $deals->category,
            'customer_id' => $dataDeals['customer_id'] ?? $deals->customer_id,
            'customers_company_id' => $dataDeals['customers_company_id'] ?? $deals->customers_company_id,
            'payment_category' => $dataDeals['payment_category'] ?? $deals->payment_category,
            'payment_duration' => $dataDeals['payment_duration'] ?? $deals->payment_duration,
            'value_estimated' => $dataDeals['value_estimated'] ?? $deals->value_estimated,
            'value_actual' => $dataDeals['value_actual'] ?? $deals->value_actual,
            'stage' => $dataDeals['stage'] ?? $deals->stage,
            'expected_close_date' => $dataDeals['expected_close_date'] ?? $deals->expected_close_date,
            'close_date' => $dataDeals['close_date'] ?? $deals->close_date,
            'status' => $dataDeals['status'] ?? $deals->status,
            'tag' => $dataDeals['tag'] ?? $deals->tag,
            'owner' => $dataDeals['owner'] ?? $deals->owner,
            'description' => $dataDeals['description'] ?? $deals->description,
        ]);

        return $deals;
    }
}

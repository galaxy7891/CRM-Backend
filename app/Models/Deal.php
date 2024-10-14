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
        'customer_id',
        'name',
        'deals_customer',
        'description',
        'tag',
        'stage',
        'open_date',
        'close_date',
        'expected_close_date',
        'payment_expected',
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
     * Get the customer that owns the deal.
     * Get the user that owns the deal.
     * 
     * 
     * This defines a many-to-one relationship where the user belongs to a company.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'deals_products', 'deals_id', 'product_id');
    }

    /**
     * Count Deals User by Stage
     * 
     * @param string $email
     * @param string $stage
     * @return int
     */
    public static function countDealsByStage($email, $stage)
    {
        return self::where('owner', $email)
            ->where('stage', $stage)
            ->count();
    }

    public static function createDeal(array $data): self
    {
        return self::create([
            'customer_id' => $data['customer_id'],
            'name' => $data['name'],
            'deals_customer' => $data['deals_customer'],
            'description' => $data['description'] ?? null,
            'tag' => $data['tag'],
            'stage' => $data['stage'],
            'open_date' => $data['open_date'],
            'close_date' => $data['close_date'] ?? null,
            'expected_close_date' => $data['expected_close_date'],
            'payment_expected' => $data['payment_expected'] ?? null,
            'payment_category' => $data['payment_category'],
            'payment_duration' => $data['payment_duration'] ?? null,
            'owner' => $data['owner'],
        ]);
    }

    public static function updateDeal(array $data, $id): self
    {
        $deal = self::findOrFail($id);
        $deal->fill([
            'customer_id' => $data['customer_id'],
            'name' => $data['name'],
            'deals_customer' => $data['deals_customer'],
            'description' => $data['description']  ?? null,
            'tag' => $data['tag'],
            'stage' => $data['stage'],
            'open_date' => $data['open_date'],
            'close_date' => $data['close_date']  ?? null,
            'expected_close_date' => $data['expected_close_date'],
            'payment_expected' => $data['payment_expected'] ?? null,
            'payment_category' => $data['payment_category'],
            'payment_duration' => $data['payment_duration'] ?? null,
            'owner' => $data['owner'],
        ]);
        $deal->save();
        return $deal;
    }
}

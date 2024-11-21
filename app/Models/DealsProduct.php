<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DealsProduct extends Model
{
    use HasFactory, SoftDeletes, HasUuid;
    public $timestamps = false;
    
    /**
     * The attributes that are mass assignable.
     * 
     * @var array
     */
    protected $fillable = [
        'id',
        'deals_id',
        'product_id',
        'quantity',
        'unit'
    ];

    public static function createDealsProducts(array $dataDealsProduct, string $dealsId): self
    {
        return self::create([
            'deals_id' => $dealsId ?? null,
            'product_id' => $dataDealsProduct['product_id'] ?? null,
            'quantity' => $dataDealsProduct['quantity'] ?? null,
            'unit' => $dataDealsProduct['unit'] ?? null, 
        ]);
    }

    public static function updateDealsProducts(array $dataDealsProduct, string $dealsId): self
    {
        $product = Product::findOrFail($dataDealsProduct['product_id']);
        $dealsProducts = self::findOrFail($dealsId);

        if ($product['category'] === 'service'){
            $quantity = null;
            $unit = null;
        } else {
            $quantity = $dataDealsProduct['quantity'] ?? $dealsProducts->quantity;
            $unit = $dataDealsProduct['unit'] ?? $dealsProducts->unit;
        }
        
        $dealsProducts->update([
            'product_id' => $dataDealsProduct['product_id'] ?? $dealsProducts->product_id,
            'quantity' => $quantity,
            'unit' => $unit, 
        ]);
        
        return $dealsProducts; 
    }

    // public static function sumProductQuantity($productId)
    // {
    //     return self::join('deals', 'deals_products.deals_id', '=', 'deals.id')
    //         ->where('deals_products.product_id', $productId)
    //         ->whereNotIn('deals.stage', ['lose','won'])
    //         ->sum('deals_products.quantity');
    // }

}

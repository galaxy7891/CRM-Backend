<?php

namespace App\Models;

use App\Traits\HasUuid;
use Cloudinary\Cloudinary;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory, SoftDeletes, HasUuid;

    /**
     * The attributes that are mass assignable.
     * 
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'user_company_id',
        'category',
        'code',
        'quantity',
        'unit',
        'price',
        'description',
        'image_url',
        'image_public_id',
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
     * The deals that belong to the product.
     */
    public function deals()
    {
        return $this->belongsToMany(Deal::class, 'deals_products', 'product_id', 'deals_id')
                    ->withPivot('quantity', 'unit')
                    ->withTimestamps();
    }
    
    /**
     * Get the user company that owns the product.
     * 
     * This defines a many-to-one relationship where the user belongs to a company.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function userCompany()
    {
        return $this->belongsTo(UsersCompany::class, 'user_company_id', 'id');
    }
    
    /**
     * Get the product's full name by ID.
     *
     * @param  int|string  $id
     * @return string|null
     */
    public static function getProductNameById($id)
    {
        $product = self::select('name')
            ->where('id', $id)
            ->first();

        return $product ? $product->name : null;
    }

    /**
     * Get the category of a product by ID.
     *
     * @param string $id
     * @return string|null
     */
    public static function getCategoryById($id)
    {
        return self::where('id', $id)->value('category');
    }

    /**
     * count the products.
     *  
     * @return self
     */
    public static function countProducts($userCompanyIds)
    {
        return self::where('user_company_id', $userCompanyIds)
            ->count();
    }

    /**
     * Upload photo product of the product.
     *
     * @param \Illuminate\Http\UploadedFile $photo
     * @return array
     */
    public function uploadPhoto($photo)
    {
        $cloudinary = new Cloudinary();
        if ($this->image_public_id) {
            $cloudinary->uploadApi()->destroy($this->image_public_id);
        }

        $result = $cloudinary->uploadApi()->upload($photo->getRealPath(), [
            'folder' => 'products',
        ]);
        
        return [
            'image_url' => $result['secure_url'],                        
            'image_public_id' => $result['public_id'],                   
        ];                                                               
    }                                                                    

    public static function createProduct(array $data): self
    {       
        $products = new Product();
        $products->name = $data['name'];
        $products->user_company_id = $data['user_company_id'];
        $products->category = $data['category'];
        $products->code = $data['code'] ?? null;
        $products->quantity = $data['quantity'] ?? null;
        $products->unit = $data['unit'] ?? null;
        $products->price = $data['price'];
        $products->description = $data['description'] ?? null;
        
        $products->save();
        return $products;
    }

    public static function updateProduct(array $dataProduct, string $productId): self
    {
        $product = self::findOrFail($productId);

        if (isset($dataProduct['category']) && $dataProduct['category'] === 'service'){
            $quantity = null;
            $unit = null;
        } else {
            $quantity = $dataProduct['quantity'] ?? $product->quantity;
            $unit = $dataProduct['unit'] ?? $product->unit;
        }
        
        $product->update([
            'name' => $dataProduct['name'] ?? $product->name,
            'category' => $dataProduct['category'] ?? $product->category,
            'code' => $dataProduct['code'] ?? $product->code,
            'quantity' => $quantity,
            'unit' => $unit,
            'price' => $dataProduct['price'] ?? $product->price,
            'description' => $dataProduct['description'] ?? $product->description,
        ]);
        
        return $product;
    }   

    /**
     * Update the photo product URL and public_id of the product.
     *
     * @param \Illuminate\Http\UploadedFile $photo
     * @param string $productId 
     * @return array
     */
    public function updatePhotoProduct($photo, string $productId)
    {
        $product = self::findOrFail($productId);

        $uploadResult = $product->uploadPhoto($photo);
        $product->update([
            'image_url' => $uploadResult['image_url'],
            'image_public_id' => $uploadResult['image_public_id'],
        ]);

        return [
            'image_url' => $uploadResult['image_url'],
            'image_public_id' => $uploadResult['image_public_id'],
        ];
    }

    public static function deleteProduct($id): self
    {
        $product = self::find($id);
        $cloudinary = new Cloudinary();

        if ($product->image_public_id) {
            $cloudinary->uploadApi()->destroy($product->image_public_id);
        }

        $product->delete();
        return $product;
    }
}

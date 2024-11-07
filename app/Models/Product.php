<?php

namespace App\Models;

use App\Traits\HasUuid;
use Cloudinary\Cloudinary;
use Illuminate\Support\Str;
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
        if (isset($data['photo_product'])) {
            $product = new Product();
            $uploadResult = $product->uploadPhoto($data['photo_product']);
            $photoUrl = $uploadResult['image_url'];
            $publicId = $uploadResult['image_public_id'];
        } else {
            $photoUrl = null;
            $publicId = null;
        }

        return self::create([
            'user_company_id' => $data['user_company_id'],
            'name' => $data['name'],
            'category' => $data['category'],
            'code' => $data['code'] ?? null,
            'quantity' => $data['quantity'] ?? null,
            'unit' => $data['unit'] ?? null,
            'price' => $data['price'],
            'description' => $data['description'] ?? null,
            'image_url' => $photoUrl ?? null,
            'image_public_id' => $publicId ?? null,
        ]);
    }

    public static function updateProduct(array $dataProduct, string $productId): self
    {
        $product = self::findOrFail($productId);

        $product->update([
            'name' => $dataProduct['name'] ?? $product->name,
            'category' => $dataProduct['category'] ?? $product->category,
            'code' => $dataProduct['code'] ?? $product->code,
            'quantity' => $dataProduct['quantity'] ?? $product->quantity,
            'unit' => $dataProduct['unit'] ?? $product->unit,
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
            'image_url' => $uploadResult['secure_url'],
            'image_public_id' => $uploadResult['public_id'],
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

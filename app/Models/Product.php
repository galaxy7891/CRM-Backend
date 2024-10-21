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
            $uploadResult = self::uploadPhoto($data['photo_product']);
            $photoUrl = $uploadResult['image_url'];
            $publicId = $uploadResult['image_public_id'];
        } else {
            $photoUrl = null;
            $publicId = null;
        }

        return self::create([
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

    public static function updateProduct(array $data, string $productId): self
    {
        $product = self::findOrFail($productId);

        $product->update([
            'name' => $data['name'],
            'category' => $data['category'],
            'code' => $data['code'] ?? null,
            'quantity' => $data['quantity'] ?? null,
            'unit' => $data['unit'] ?? null,
            'price' => $data['price'],
            'description' => $data['description'] ?? null,
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

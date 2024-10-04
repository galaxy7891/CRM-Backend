<?php

namespace App\Models;

use Cloudinary\Cloudinary;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $keyType = 'string'; // String untuk UUID / agar uuid mau dibaca postman
    public $incrementing = false; //  Non-incrementing karena UUID / agar uuid mau dibaca postman

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
        'photo_product',
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

    public static function uploadPhoto($photo)
    {
        $cloudinary = new Cloudinary();

        // Upload the photo
        $result = $cloudinary->uploadApi()->upload($photo->getRealPath(), [
            'folder' => 'products', // optional: specify a folder in your Cloudinary account
        ]);

        // Return the URL of the uploaded image
        return $result['secure_url'];
    }

    public static function createProduct(array $data): self
    {
        // Check if a photo is provided
        if (isset($data['photo_product'])) {
            $photoUrl = self::uploadPhoto($data['photo_product']);
        } else {
            $photoUrl = null; // Default value if no photo is uploaded
        }

        return self::create([
            'id' => Str::uuid(),
            'name' => $data['name'],
            'category' => $data['category'],
            'code' => $data['code'],
            'quantity' => $data['quantity'],
            'unit' => $data['unit'],
            'price' => $data['price'],
            'description' => $data['description'] ?? null,
            'photo_product' => $photoUrl ?? null, // Store the Cloudinary URL
        ]);
    }
    public static function updateProduct(array $data, string $productId): self
    {
        // Find the existing product
        $product = self::findOrFail($productId);

        // Create a Cloudinary instance
        $cloudinary = new Cloudinary();

        // Check if a new photo is provided
        if (isset($data['photo_product'])) {
            // If there is an existing photo, delete it from Cloudinary
            if ($product->photo_product) {
                // Use the public ID stored in the database to delete the old photo
                $cloudinary->uploadApi()->destroy($product->image_public_id);
            }

            // Upload the new photo
            $photoUrl = self::uploadPhoto($data['photo_product']);
        } else {
            // Keep the existing photo URL if no new photo is provided
            $photoUrl = $product->photo_product;
        }

        // Update the product's attributes
        $product->update([
            'name' => $data['name'],
            'category' => $data['category'],
            'code' => $data['code'],
            'quantity' => $data['quantity'],
            'unit' => $data['unit'],
            'price' => $data['price'],
            'description' => $data['description'] ?? null,
            'photo_product' => $photoUrl, // Store the updated Cloudinary URL
        ]);

        return $product; // Return the updated product
    }
}

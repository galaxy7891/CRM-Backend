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
        'image_url',         // Update this line
        'image_public_id',   // Add this line
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
        return [
            'url' => $result['secure_url'],
            'public_id' => $result['public_id'],
        ];
    }

    public static function createProduct(array $data): self
    {

        if (isset($data['photo_product'])) {

            // $photoUrl = self::uploadPhoto($data['photo_product']);
            $uploadResult = self::uploadPhoto($data['photo_product']);
            $photoUrl = $uploadResult['url'];
            $publicId = $uploadResult['public_id'];
        } else {
            $photoUrl = null;
            $publicId = null;
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
            'image_url' => $photoUrl ?? null, // Store the Cloudinary URL
            'image_public_id' => $publicId ?? null, // Store the Cloudinary URL
        ]);
    }

    public static function deleteProduct($id): self
    {
        $product = Product::find($id);
        // Create a Cloudinary instance
        $cloudinary = new Cloudinary();

        // Check if the product has an associated image
        if ($product->image_public_id) {
            // Delete the image from Cloudinary using the public ID
            $cloudinary->uploadApi()->destroy($product->image_public_id);
        }

        // Delete the product from the database
        $product->delete();
        return $product;
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
            if ($product->image_public_id) {
                // Use the public ID stored in the database to delete the old photo
                $cloudinary->uploadApi()->destroy($product->image_public_id);
            }

            // Upload the new photo
            $uploadResult = self::uploadPhoto($data['photo_product']);
            $photoUrl = $uploadResult['url'];
            $publicId = $uploadResult['public_id'];
        } else {
            // Keep the existing photo URL if no new photo is provided
            $photoUrl = $product->image_url;
            $publicId = $product->image_public_id; // Keep the existing public ID
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
            'image_url' => $photoUrl, // Store the updated Cloudinary URL
            'image_public_id' => $publicId, // Store the updated Cloudinary public ID
        ]);

        return $product; // Return the updated product
    }
}

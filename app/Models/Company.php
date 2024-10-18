<?php

namespace App\Models;

use App\Traits\HasUuid;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use SoftDeletes, HasUuid;

    /**
     * The attributes that are mass assignable.
     * 
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'industry',
        'image_url',
        'image_public_id',
        'email',
        'phone',
        'website',
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
     * Get the users associated with the company.
     * 
     * This defines a one-to-many relationship where the user can have multiple customers.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany(User::class, 'company_id', 'id');
    }

    /**
     * Update the logo of the companies.
     *
     * @param \Illuminate\Http\UploadedFile $logo
     * @return array
     */
    public function updateLogo($lofo)
    {
        $cloudinary = new Cloudinary();

        $result = $cloudinary->uploadApi()->upload($lofo->getRealPath(), [
            'folder' => 'companies',
        ]);

        $this->update([
            'image_url' => $result['secure_url'],
            'image_public_id' => $result['public_id'],
        ]);
    }

    /**
     * Create a new company instance associated with a user.
     *
     * @param array $dataCompany
     * @return Company
     */
    public static function registerCompany(array $dataCompany): self
    {
        return self::create([
            'name' => $dataCompany['name'],
            'industry' => $dataCompany['industry']
        ]);
    }
}

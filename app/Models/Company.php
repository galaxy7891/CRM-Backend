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
     * Create a new company instance associated with a user.
     *
     * @param array $dataCompany
     * @return Company
     */
    public static function createCompany(array $dataCompany): self
    {
        return self::create([
            'name' => $dataCompany['name'],
            'industry' => $dataCompany['industry'],
            'email' => $dataCompany['email'] ?? null,
            'phone' => $dataCompany['phone'] ?? null,
            'website' => $dataCompany['website'] ?? null,
        ]);
    }

    /**
     * Update company
     *
     * @param array $dataCompany
     * @param array $companyId
     * @return Company
     */
    public static function updateCompany(array $dataCompany, string $companyId) :self
    {
        $company = self::findOrFail($companyId);

        $company->update([
            'email' => $dataCompany['email'] ?? $company->email,
            'first_name' => $dataCompany['first_name'] ?? $company->first_name,
            'last_name' => $dataCompany['last_name'] ?? $company->last_name,
            'phone' => $dataCompany['phone'] ?? $company->phone,
            'job_position' => $dataCompany['job_position'] ?? $company->job_position,
            'role' => $dataCompany['role'] ?? $company->role,
            'gender' => $dataCompany['gender'] ?? $company->gender,
        ]);

        return $company;
    }

    /**
     * Update the logo of the companies.
     *
     * @param \Illuminate\Http\UploadedFile $logo
     * @param string $companyId 
     * @return array
     */
    public function updateLogo($logo, string $companyId)
    {
        $comapny = self::findOrFail($companyId);

        $cloudinary = new Cloudinary();
        if ($comapny->image_public_id) {
            $cloudinary->uploadApi()->destroy($comapny->image_public_id);
        }

        $uploadResult = $cloudinary->uploadApi()->upload($logo->getRealPath(), [
            'folder' => 'companies',
        ]);
        $comapny->update([
            'image_url' => $uploadResult['secure_url'],
            'image_public_id' => $uploadResult['public_id'],
        ]);

        return [
            'image_url' => $uploadResult['secure_url'],
            'image_public_id' => $uploadResult['public_id'],
        ];
    }
}
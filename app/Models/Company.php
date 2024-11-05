<?php

namespace App\Models;

use App\Traits\HasUuid;
use Cloudinary\Cloudinary;
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
     * Get the products associated with the company.
     * 
     * This defines a one-to-many relationship where the user can have multiple customers.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany(User::class, 'company_id', 'id');
    }
    public function products()
    {
        return $this->hasMany(Product::class, 'company_id', 'id');
    }

    /**
     * Get the company's full name by ID.
     *
     * @param  int|string  $id
     * @return string|null
     */
    public static function getCompaniesNameById($id)
    {
        $company = self::select('name')
            ->where('id', $id)
            ->first();

        return $company ? $company->name : null;
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
            'name' => $dataCompany['name']?? $company->name,
            'industry' => $dataCompany['industry']?? $company->industry,
            'email' => $dataCompany['email'] ?? $company->email,
            'phone' => $dataCompany['phone'] ?? $company->phone,
            'website' => $dataCompany['website'] ?? $company->website,
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
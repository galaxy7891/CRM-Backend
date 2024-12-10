<?php

namespace App\Models;

use App\Traits\HasUuid;
use Cloudinary\Cloudinary;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UsersCompany extends Model
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
     * Get the account type that've relation the user company.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function accountType()
    {
        return $this->hasOne(AccountsType::class, 'user_company_id', 'id');
    }
    
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
        return $this->hasMany(User::class, 'user_company_id', 'id');
    }
    public function products()
    {
        return $this->hasMany(Product::class, 'user_company_id', 'id');
    }

    /**
     * Get the company's full name by ID.
     *
     * @param  int|string  $id
     * @return string|null
     */
    public static function getCompaniesNameById($id)
    {
        $userCompany = self::select('name')
            ->where('id', $id)
            ->first();

        return $userCompany ? $userCompany->name : null;
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
     * @param array $userCompanyId
     * @return Company
     */
    public static function updateCompany(array $dataCompany, string $userCompanyId) :self
    {
        $userCompany = self::findOrFail($userCompanyId);

        $userCompany->update([
            'name' => $dataCompany['name']?? $userCompany->name,
            'industry' => $dataCompany['industry']?? $userCompany->industry,
            'email' => $dataCompany['email'] ?? $userCompany->email,
            'phone' => $dataCompany['phone'] ?? $userCompany->phone,
            'website' => $dataCompany['website'] ?? $userCompany->website,
        ]);

        return $userCompany;
    }

    /**
     * Update the logo of the companies.
     *
     * @param \Illuminate\Http\UploadedFile $logo
     * @param string $userCompanyId 
     * @return array
     */
    public function updateLogo($logo, string $userCompanyId)
    {
        $comapny = self::findOrFail($userCompanyId);

        $cloudinary = new Cloudinary();
        if ($comapny->image_public_id) {
            $cloudinary->uploadApi()->destroy($comapny->image_public_id);
        }

        $uploadResult = $cloudinary->uploadApi()->upload($logo->getRealPath(), [
            'folder' => 'usercompanies',
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

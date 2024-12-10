<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApiResponseResource;
use App\Models\UsersCompany;
use App\Traits\Filter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UsersCompaniesController extends Controller
{
    use Filter;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return new ApiResponseResource(
                false,
                'Unauthorized',
                null
            );
        }

        try {
            $userCompany = UsersCompany::where('id', $user->user_company_id)
                    ->first();

            return new ApiResponseResource(
                true,
                'Daftar data perusahaan user',
                $userCompany
            );

        } catch (\Exception $e) {
            return new ApiResponseResource(
                false, 
                $e->getMessage(),
                null
            );
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {       
        $user = auth()->user();
        if (!$user) {
            return new ApiResponseResource(
                false,
                'Unauthorized',
                null
            );
        }

        $userCompanyId = $user->user_company_id;
        $userCompany = UsersCompany::where('id', $userCompanyId)->first();
        if (!$userCompany) {
            return new ApiResponseResource(
                false, 
                'Data perusahaan user tidak ditemukan.',
                null
            );
        }   
        
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:100|'. Rule::unique('users_companies', 'name')->ignore($userCompanyId)->whereNull('deleted_at'),
            'industry' => 'sometimes|required|string|max:50',
            'email' => 'sometimes|nullable|email|'.  Rule::unique('users_companies', 'email')->ignore($userCompanyId)->whereNull('deleted_at'),
            'phone' => 'sometimes|nullable|numeric|max_digits:15|'.  Rule::unique('users_companies', 'phone')->ignore($userCompanyId)->whereNull('deleted_at'),
            'website' => 'sometimes|nullable|string|max:255|'.  Rule::unique('users_companies', 'website')->ignore($userCompanyId)->whereNull('deleted_at'),
        ], [
            'name.required' => 'Nama perusahaan tidak boleh kosong.',
            'name.string' => 'Nama perusahaan harus berupa teks.',
            'name.max' => 'Nama perusahaan maksimal 100 karakter.',
            'name.unique' => 'Nama perusahaan sudah terdaftar.',
            'industry.string' => 'Jenis industri harus berupa teks.',
            'industry.max' => 'Jenis industri maksimal 50 karakter.',
            'email.email' => 'Email harus valid',
            'email.unique' => 'Email sudah terdaftar',
            'phone.numeric' => 'Nomor telepon harus berupa angka',
            'phone.max_digits' => 'Nomor telepon maksimal 15 angka',
            'phone.unique' => 'Nomor telepon sudah terdaftar.',
            'website.string' => 'Website harus berupa teks.',
            'website.max' => 'Website maksimal 255 karakter',
            'website.unique' => 'Website sudah terdaftar',
        ]);
        if ($validator->fails()) {
            return new ApiResponseResource(
                false,
                $validator->errors(),
                null
            );
        }
        
        try {
            $userCompany = UsersCompany::updateCompany($request->all(), $userCompanyId);
            return new ApiResponseResource(
                true,
                "Data perusahaan {$userCompany->name} berhasil diubah",
                $userCompany
            );

        } catch (\Exception $e) {
            return new ApiResponseResource(
                false, 
                $e->getMessage(),
                null
            );
        }
    }
    
    /**
     * Update logo profile in cloudinary.
     */
    public function updateLogo(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return new ApiResponseResource(
                false,
                'Unauthorized',
                null
            );
        }
        
        $userCompanyId = $user->user_company_id;
        $userCompany = UsersCompany::where('id', $userCompanyId)->first();
        if (!$userCompany) {
            return new ApiResponseResource(
                false, 
                'Data perusahaan user tidak ditemukan.',
                null
            );
        }

        $validator = Validator::make($request->all(), [
            'logo' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'logo.required' => 'Logo tidak boleh kosong.',
            'logo.image' => 'Logo harus berupa gambar.',
            'logo.mimes' => 'Logo tidak sesuai format.',
            'logo.max' => 'Logo maksimal 2mb.',
        ]);
        if ($validator->fails()) {
            return new ApiResponseResource(
                false,
                $validator->errors(),
                null
            );
        }

        try {
            $logoData = $userCompany->updateLogo($request->file('logo'), $userCompanyId);

            return new ApiResponseResource(
                true,
                "Logo perusahaan {$userCompany->name} berhasil diperbarui",
                $logoData
            );
        
        } catch (\Exception $e) {
            return new ApiResponseResource(
                false,
                $e->getMessage(),
                null
            );
        }
    }
}

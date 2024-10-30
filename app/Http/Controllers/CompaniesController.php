<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApiResponseResource;
use App\Models\Company;
use App\Traits\Filter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CompaniesController extends Controller
{
    use Filter;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Company::query();

            $company = $this->applyFilters($request, $query);

            return new ApiResponseResource(
                true,
                'Daftar data perusahaan',
                $company
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
    public function update(Request $request, $companyId)
    {
        $company = Company::find($companyId);
        if (!$company) {
            return new ApiResponseResource(
                false, 
                'Data perusahaan tidak ditemukan.',
                null
            );
        }
        
        $validator = Validator::make($request->all(), [
            'name' => "sometimes|required|string|max:100|unique:company,name,$companyId",
            'industry' => 'sometimes|required|string|max:50',
            'email' => "sometimes|nullable|email|unique:company,email,$companyId",
            'phone' => "sometimes|nullable|numeric|max_digits:15|unique:company,phone,$companyId",
            'website' => 'sometimes|nullable|string|max:255',
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
        ]);
        if ($validator->fails()) {
            return new ApiResponseResource(
                false,
                $validator->errors(),
                null
            );
        }
        
        try {
            $company = Company::updateCompany($request->all(), $companyId);
            return new ApiResponseResource(
                true,
                "Data perusahaan {$company->name} berhasil diubah",
                $company
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
    public function updateLogo(Request $request, $companyId)
    {
        $company = Company::find($companyId);
        if (!$company) {
            return new ApiResponseResource(
                false, 
                'Data perusahaan tidak ditemukan',
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
            $logoData = $company->updateLogo($request->file('logo'), $companyId);

            return new ApiResponseResource(
                true,
                "Logo perusahaan {$company->name} berhasil diperbarui",
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

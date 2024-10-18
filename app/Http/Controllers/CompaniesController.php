<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApiResponseResource;
use App\Models\Company;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CompaniesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        
    }

    /**
     * Update logo profile in cloudinary.
     */
    public function updateLogo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'logo' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'logo.required' => 'Foto profil tidak boleh kosong.',
            'logo.image' => 'Foto profil harus berupa gambar.',
            'logo.mimes' => 'Foto profil tidak sesuai format.',
            'logo.max' => 'Foto profil maksimal 2mb.',
        ]);
        if ($validator->fails()) {
            return new ApiResponseResource(
                false,
                $validator->errors(),
                null
            );
        }

        try {
            $user = auth()->user();
            $organization = Organization::where('company_id', $user->company_id);
            $logo = $request->file('logo');
            $logoData = $organization->updateLogo($logo);

            return new ApiResponseResource(
                true,
                'Logo perusahaan berhasil diperbarui',
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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Company $company)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Company $company)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company)
    {
        //
    }
}

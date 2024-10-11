<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApiResponseResource;
use App\Models\Organization;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrganizationController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            // Is employee accessing his own organization data?
            $user = auth()->user();
            if ($user->role == 'employee') {
                $organizations = Organization::where('owner', $user->id)->latest()->paginate(25);
            } else {
                $organizations = Organization::latest()->paginate(25);
            }

            return new ApiResponseResource(
                true,
                'Daftar Organization',
                $organizations
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:organizations,name|string|max:100',
            'industry' => 'nullable|string|max:50',
            'status' => 'required|in:hot,warm,cold',
            'email' => 'nullable|email|unique:organizations,email|max:100',
            'phone' => 'nullable|numeric|max_digits:15|unique:organizations,phone',
            'website' => 'nullable|string|max:255',
            'owner' => 'required|email|max:100',
            'country' => 'nullable|string|max:50',
            'city' => 'nullable|string|max:100',
            'subdistrict' => 'nullable|string|max:100',
            'village' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:5',
            'address' => 'nullable|string|max:100',
        ], [
            'name.required' => 'Nama organisasi wajib diisi.',
            'name.unique' => 'Nama organisasi sudah terdaftar.',
            'name.string' => 'Nama organisasi harus berupa teks.',
            'name.max' => 'Nama maksimal 100 karakter.',
            'industry.string' => 'Jenis industri harus berupa teks.',
            'industry.max' => 'Jenis industri maksimal 50 karakter.',
            'status.required' => 'Status wajib diisi.',
            'status.in' => 'Status harus pilih salah satu dari: hot, warm, cold.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'email.max' => 'Email maksimal 100 karakter.',
            'phone.numeric' => 'Nomor telepon harus berupa angka.',
            'phone.max_digits' => 'Nomor telepon maksimal 15 angka.',
            'phone.unique' => 'Nomor telepon sudah terdaftar.',
            'website.string' => 'Website harus berupa teks.',
            'website.max' => 'Website maksimal 255 karakter.',
            'owner.required' => 'Pemilik kontak wajib diisi.',
            'owner.email' => 'Pemilik kontak harus berupa email valid.',
            'owner.max' => 'Pemilik maksimal 100 karakter.',
            'country.string' => 'Asal negara harus berupa teks.',
            'country.max' => 'Asal negara maksimal 50 karakter.',
            'city.string' => 'Kota harus berupa teks.',
            'city.max' => 'Kota maksimal 100 karakter.',
            'subdistrict.string' => 'Kecamatan harus berupa teks.',
            'subdistrict.max' => 'Kecamatan maksimal 100 karakter.',
            'village.string' => 'Desa/Kelurahan harus berupa teks.',
            'village.max' => 'Desa/Kelurahan maksimal 100 karakter.',
            'zip_code.string' => 'Kode pos harus berupa teks.',
            'zip_code.max' => 'Kode pos maksimal 5 karakter.',
            'address.string' => 'Alamat harus berupa teks.',
            'address.max' => 'Alamat maksimal 100 karakter.',
        ]);


        //check if validation fails
        if ($validator->fails()) {
            return new ApiResponseResource(
                false,
                $validator->errors(),
                null
            );
        }

        try {
            $organization = Organization::createOrganization($request->all());
            return new ApiResponseResource(
                true,
                "Data {$organization->name} Berhasil Ditambahkan!", 
                $organization
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
     * Display the specified resource.
     */
    public function show($id)
    {
        try {

            // Check if organization exists
            $organization = Organization::find($id);
            if (is_null($organization)) {
                return new ApiResponseResource(
                    false, 
                    'Data Organisasi Tidak Ditemukan!',
                    null
                );
            }

            // Is employee accessing his own organization data?
            $user = auth()->user();
            if ($user->role == 'employee' && $organization->owner !== $user->id) {
                return new ApiResponseResource(
                    false, 
                    'Anda tidak memiliki akses untuk menampilkan data organisasi ini!',
                    null
                );
            }

            return new ApiResponseResource(
                true,
                'Data Organisasi Ditemukan!',
                $organization
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
    public function update(Request $request, $id)
    {
        // Check if organization exists
        $organization = Organization::find($id);

        if (!$organization) {
            return new ApiResponseResource(
                false, 
                'Organization tidak ditemukan',
                null
            );
        }

        // Is employee accessing his own customer data?
        $user = auth()->auth();
        if ($user == 'employee' && $organization->owner !== $user->id) {
            return new ApiResponseResource(
                false, 
                'Anda tidak memiliki akses untuk mengubah data organisasi ini!',
                null
            );
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:organizations,name|string|max:100',
            'industry' => 'nullable|string|max:50',
            'status' => 'required|in:hot,warm,cold',
            'email' => 'nullable|email|unique:organizations,email|max:100',
            'phone' => 'nullable|numeric|max_digits:15|unique:organizations,phone',
            'website' => 'nullable|string|max:255',
            'owner' => 'required|email|max:100',
            'country' => 'nullable|string|max:50',
            'city' => 'nullable|string|max:100',
            'subdistrict' => 'nullable|string|max:100',
            'village' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:5',
            'address' => 'nullable|string|max:100',
        ], [
            'name.required' => 'Nama organisasi wajib diisi.',
            'name.unique' => 'Nama organisasi sudah terdaftar.',
            'name.string' => 'Nama organisasi harus berupa teks.',
            'name.max' => 'Nama maksimal 100 karakter.',
            'industry.string' => 'Jenis industri harus berupa teks.',
            'industry.max' => 'Jenis industri maksimal 50 karakter.',
            'status.required' => 'Status wajib diisi.',
            'status.in' => 'Status harus pilih salah satu dari: hot, warm, cold.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'email.max' => 'Email maksimal 100 karakter.',
            'phone.numeric' => 'Nomor telepon harus berupa angka.',
            'phone.max_digits' => 'Nomor telepon maksimal 15 angka.',
            'phone.unique' => 'Nomor telepon sudah terdaftar.',
            'website.string' => 'Website harus berupa teks.',
            'website.max' => 'Website maksimal 255 karakter.',
            'owner.required' => 'Pemilik kontak wajib diisi.',
            'owner.email' => 'Pemilik kontak harus berupa email valid.',
            'owner.max' => 'Pemilik maksimal 100 karakter.',
            'country.string' => 'Asal negara harus berupa teks.',
            'country.max' => 'Asal negara maksimal 50 karakter.',
            'city.string' => 'Kota harus berupa teks.',
            'city.max' => 'Kota maksimal 100 karakter.',
            'subdistrict.string' => 'Kecamatan harus berupa teks.',
            'subdistrict.max' => 'Kecamatan maksimal 100 karakter.',
            'village.string' => 'Desa/Kelurahan harus berupa teks.',
            'village.max' => 'Desa/Kelurahan maksimal 100 karakter.',
            'zip_code.string' => 'Kode pos harus berupa teks.',
            'zip_code.max' => 'Kode pos maksimal 5 karakter.',
            'address.string' => 'Alamat harus berupa teks.',
            'address.max' => 'Alamat maksimal 100 karakter.',
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return new ApiResponseResource(
                false, 
                $validator->errors(),
                null
            );
        }

        try {
            $organization = Organization::updateOrganization($request->all(), $id);
            return new ApiResponseResource(
                true, 
                'Data Organization Berhasil Diubah!',
                $organization 
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
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {

            $organization = Organization::find($id);
            if (!$organization) {
                return new ApiResponseResource(
                    false, 
                    'Organization tidak ditemukan',
                    null
                );
            }

            $user = auth()->auth();
            if ($user == 'employee' && $organization->owner !== $user->id) {
                return new ApiResponseResource(
                    false, 
                    'Anda tidak memiliki akses untuk menghapus data organisasi ini!',
                    null
                );
            }


            // Delete the organization
            $organization->delete();

            // Return response with first and last name
            return new ApiResponseResource(
                true,
                "Organization {$organization->name} Berhasil Dihapus!",
                null
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

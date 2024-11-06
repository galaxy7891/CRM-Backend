<?php

namespace App\Http\Controllers;

use App\Helpers\ActionMapperHelper;
use App\Http\Resources\ApiResponseResource;
use App\Models\CustomersCompany;
use App\Traits\Filter;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomersCompanyController extends Controller
{
    use Filter;
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $user = auth()->user();
            $query = CustomersCompany::query();

            $query->whereHas('user', function ($ownerQuery) use ($user) {
                $ownerQuery->where('user_company_id', $user->user_company_id);
            });

            if ($user->role === 'employee') {
                $query->where('owner', $user->email);
            }

            $CustomersCompanies = $this->applyFilters($request, $query);
            if (!$CustomersCompanies) {
                return new ApiResponseResource(
                    false,
                    'Data perusahaan pelanggan tidak ditemukan',
                    null
                );
            }

            $CustomersCompanies->getCollection()->transform(function ($CustomersCompany) {
                $CustomersCompany->status = ActionMapperHelper::mapStatus($CustomersCompany->status);
                return $CustomersCompany;
            });

            return new ApiResponseResource(
                true,
                'Daftar perusahaan pelanggan',
                $CustomersCompanies
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
            'name' => 'required|unique:customers_companies,name|string|max:100',
            'industry' => 'nullable|string|max:50',
            'status' => 'required|in:hot,warm,cold',
            'email' => 'nullable|email|unique:customers_companies,email|max:100',
            'phone' => 'nullable|numeric|max_digits:15|unique:customers_companies,phone',
            'website' => 'nullable|string|max:255',
            'owner' => 'required|email|max:100',
            'province' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'subdistrict' => 'nullable|string|max:100',
            'village' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:5',
            'address' => 'nullable|string|max:100',
        ], [
            'name.required' => 'Nama organisasi tidak boleh kosong.',
            'name.unique' => 'Nama organisasi sudah terdaftar.',
            'name.string' => 'Nama organisasi harus berupa teks.',
            'name.max' => 'Nama maksimal 100 karakter.',
            'industry.string' => 'Jenis industri harus berupa teks.',
            'industry.max' => 'Jenis industri maksimal 50 karakter.',
            'status.required' => 'Status tidak boleh kosong.',
            'status.in' => 'Status harus pilih salah satu dari: hot, warm, cold.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'email.max' => 'Email maksimal 100 karakter.',
            'phone.numeric' => 'Nomor telepon harus berupa angka.',
            'phone.max_digits' => 'Nomor telepon maksimal 15 angka.',
            'phone.unique' => 'Nomor telepon sudah terdaftar.',
            'website.string' => 'Website harus berupa teks.',
            'website.max' => 'Website maksimal 255 karakter.',
            'owner.required' => 'Pemilik kontak tidak boleh kosong.',
            'owner.email' => 'Pemilik kontak harus berupa email valid.',
            'owner.max' => 'Pemilik maksimal 100 karakter.',
            'province.string' => 'Provinsi harus berupa teks.',
            'province.max' => 'Provinsi maksimal 100 karakter.',
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
        if ($validator->fails()) {
            return new ApiResponseResource(
                false,
                $validator->errors(),
                null
            );
        }

        try {
            $CustomersCompany = CustomersCompany::createCustomersCompany($request->all());
            return new ApiResponseResource(
                true,
                "Data {$CustomersCompany->name} berhasil ditambahkan!", 
                $CustomersCompany
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
    public function show($customersCompanyId)
    {
        try {
            $CustomersCompany = CustomersCompany::find($customersCompanyId);
            if (is_null($CustomersCompany)) {
                return new ApiResponseResource(
                    false, 
                    'Data perusahaan pelanggan tidak ditemukan!',
                    null
                );
            }

            $user = auth()->user();
            if ($user->role == 'employee' && $CustomersCompany->owner !== $user->email) {
                return new ApiResponseResource(
                    false, 
                    'Anda tidak memiliki akses untuk menampilkan data perusahaan pelanggan ini!',
                    null
                );
            }

            $CustomersCompany->status = ActionMapperHelper::mapStatus($CustomersCompany->status);

            return new ApiResponseResource(
                true,
                'Data perusahaan pelanggan',
                $CustomersCompany
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
    public function update(Request $request, $customersCompanyId)
    {
        $CustomersCompany = CustomersCompany::find($customersCompanyId);

        if (!$CustomersCompany) {
            return new ApiResponseResource(
                false, 
                'Perusahaan pelanggan tidak ditemukan',
                null
            );
        }

        $user = auth()->user();
        if ($user == 'employee' && $CustomersCompany->owner !== $user->email) {
            return new ApiResponseResource(
                false, 
                'Anda tidak memiliki akses untuk mengubah data perusahaan pelanggan ini!',
                null
            );
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|unique:customers_companies,name|string|max:100',
            'industry' => 'sometimes|nullable|string|max:50',
            'status' => 'sometimes|required|in:hot,warm,cold',
            'email' => "sometimes|nullable|email|unique:customers_companies,email,$customersCompanyId|max:100",
            'phone' => "sometimes|nullable|numeric|max_digits:15|unique:customers_companies,phone,$customersCompanyId",
            'website' => 'sometimes|nullable|string|max:255',
            'owner' => 'sometimes|required|email|max:100',
            'province' => 'sometimes|nullable|string|max:100',
            'city' => 'sometimes|nullable|string|max:100',
            'subdistrict' => 'sometimes|nullable|string|max:100',
            'village' => 'sometimes|nullable|string|max:100',
            'zip_code' => 'sometimes|nullable|string|max:5',
            'address' => 'sometimes|nullable|string|max:100',
            'description' => 'sometimes|nullable|string|max:200',
        ], [
            'name.required' => 'Nama organisasi tidak boleh kosong.',
            'name.unique' => 'Nama organisasi sudah terdaftar.',
            'name.string' => 'Nama organisasi harus berupa teks.',
            'name.max' => 'Nama maksimal 100 karakter.',
            'industry.string' => 'Jenis industri harus berupa teks.',
            'industry.max' => 'Jenis industri maksimal 50 karakter.',
            'status.required' => 'Status tidak boleh kosong.',
            'status.in' => 'Status harus pilih salah satu dari: hot, warm, cold.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'email.max' => 'Email maksimal 100 karakter.',
            'phone.numeric' => 'Nomor telepon harus berupa angka.',
            'phone.max_digits' => 'Nomor telepon maksimal 15 angka.',
            'phone.unique' => 'Nomor telepon sudah terdaftar.',
            'website.string' => 'Website harus berupa teks.',
            'website.max' => 'Website maksimal 255 karakter.',
            'owner.required' => 'Pemilik kontak tidak boleh kosong.',
            'owner.email' => 'Pemilik kontak harus berupa email valid.',
            'owner.max' => 'Pemilik maksimal 100 karakter.',
            'province.string' => 'Provinsi harus berupa teks.',
            'province.max' => 'Provinsi maksimal 100 karakter.',
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
            'description.string' => 'Deskripsi harus berupa teks.',
            'description.max' => 'Deskripsi maksimal 200 karakter.',
        ]);
        if ($validator->fails()) {
            return new ApiResponseResource(
                false, 
                $validator->errors(),
                null
            );
        }

        try {
            $CustomersCompany = CustomersCompany::updateCustomersCompany($request->all(), $customersCompanyId);
            return new ApiResponseResource(
                true, 
                'Data perusahaan pelanggan berhasil diubah!',
                $CustomersCompany 
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
    public function destroy(Request $request)
    {
        $id = $request->input('id', []);
        if (empty($id)) {
            return new ApiResponseResource(
                true,
                "Pilih data perusahaan pelanggan yang ingin dihapus terlebih dahulu",
                null
            );
        }
        
        try {
            $deletedCount = CustomersCompany::whereIn('id', $id)->delete();
            if ($deletedCount > 0) {
                return new ApiResponseResource(
                    true,
                    $deletedCount . ' data perusahaan pelanggan berhasil dihapus',
                    null
                );
            }

            return new ApiResponseResource(
                false,
                'Data perusahaan pelanggan tidak ditemukan',
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

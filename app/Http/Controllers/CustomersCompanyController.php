<?php

namespace App\Http\Controllers;

use App\Helpers\ActionMapperHelper;
use App\Http\Resources\ApiResponseResource;
use App\Models\Customer;
use App\Models\CustomersCompany;
use App\Services\DataLimitService;
use App\Traits\Filter;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

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
        $user = auth()->user();
        if (!$user) {
            return new ApiResponseResource(
                false,
                'Unauthorized',
                null
            );
        }

        try {
            $search = $request->input('search');
            $query = CustomersCompany::query();
            $query->whereHas('user', function ($ownerQuery) use ($user) {
                $ownerQuery->where('user_company_id', $user->user_company_id);
            });
            
            if ($user->role === 'employee') {
                $query->where('owner', $user->email);
            }

            $query = CustomersCompany::search($query, $search);
            $CustomersCompanies = $this->applyFilters($request, $query);
            if ($CustomersCompanies->isEmpty()) {
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
        $user = auth()->user();
        if (!$user) {
            return new ApiResponseResource(
                false,
                'Unauthorized',
                null
            );
        }
        
        $userCompanyId = $user->user_company_id;
        $limitCheck = DataLimitService::checkCustomersLimit($userCompanyId);
        if ($limitCheck['isExceeded']) {
            return new ApiResponseResource(
                false, 
                $limitCheck['message'], 
                null
            );
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100|unique_customerscompanies_name',
            'industry' => 'nullable|string|max:50',
            'status' => 'required|in:Tinggi,Sedang,Rendah',
            'email' => 'nullable|email|max:100|unique_customerscompanies_email',
            'phone' => 'nullable|numeric|max_digits:15|unique_customerscompanies_phone',
            'website' => 'nullable|string|max:255||unique_customerscompanies_website',
            'owner' => 'required|email|max:100|exists:users,email',
            'province' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'subdistrict' => 'nullable|string|max:100',
            'village' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:5',
            'address' => 'nullable|string|max:100',
        ], [
            'name.required' => 'Nama perusahaan tidak boleh kosong.',
            'name.unique_customerscompanies_name' => 'Nama perusahaan sudah terdaftar.',
            'name.string' => 'Nama perusahaan harus berupa teks.',
            'name.max' => 'Nama maksimal 100 karakter.',
            'industry.string' => 'Jenis industri harus berupa teks.',
            'industry.max' => 'Jenis industri maksimal 50 karakter.',
            'status.required' => 'Status tidak boleh kosong.',
            'status.in' => 'Status harus pilih salah satu dari: Tinggi, Sedang, Rendah.',
            'email.email' => 'Format email tidak valid.',
            'email.unique_customerscompanies_email' => 'Email sudah terdaftar.',
            'email.max' => 'Email maksimal 100 karakter.',
            'phone.numeric' => 'Nomor telepon harus berupa angka.',
            'phone.max_digits' => 'Nomor telepon maksimal 15 angka.',
            'phone.unique_customerscompanies_phone' => 'Nomor telepon sudah terdaftar.',
            'website.unique_customerscompanies_website' => 'Website sudah terdaftar.',
            'website.string' => 'Website harus berupa teks.',
            'website.max' => 'Website maksimal 255 karakter.',
            'owner.required' => 'Penanggung jawab kontak tidak boleh kosong.',
            'owner.email' => 'Penanggung jawab kontak harus berupa email valid.',
            'owner.max' => 'Penanggung jawab maksimal 100 karakter.',
            'owner.exists' => 'Penanggung jawab tidak tersedia.',
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
        
        $data = $request->all();
        if (isset($data['status'])) {
            $data['status'] = ActionMapperHelper::mapStatusToDatabase($data['status']);
        }
        
        try {
            $CustomersCompany = CustomersCompany::createCustomersCompany($data);
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
        $user = auth()->user();
        if (!$user) {
            return new ApiResponseResource(
                false,
                'Unauthorized',
                null
            );
        }
        
        try {
            $CustomersCompany = CustomersCompany::find($customersCompanyId);
            if (is_null($CustomersCompany)) {
                return new ApiResponseResource(
                    false, 
                    'Data perusahaan pelanggan tidak ditemukan!',
                    null
                );
            }

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
            'name' => 'sometimes|required|string|max:100|unique_customerscompanies_name:' . $customersCompanyId,
            'industry' => 'sometimes|nullable|string|max:50',
            'status' => 'sometimes|required|in:Tinggi,Sedang,Rendah',
            'email' => 'sometimes|nullable|email|max:100|unique_customerscompanies_email:' . $customersCompanyId,
            'phone' => 'sometimes|nullable|numeric|max_digits:15|unique_customerscompanies_phone:' . $customersCompanyId,
            'website' => 'sometimes|nullable|string|max:255|unique_customerscompanies_website:' .  $customersCompanyId,
            'owner' => 'sometimes|required|email|max:100|exists:users,email',
            'province' => 'sometimes|nullable|string|max:100',
            'city' => 'sometimes|nullable|string|max:100',
            'subdistrict' => 'sometimes|nullable|string|max:100',
            'village' => 'sometimes|nullable|string|max:100',
            'zip_code' => 'sometimes|nullable|string|max:5',
            'address' => 'sometimes|nullable|string|max:100',
            'description' => 'sometimes|nullable|string|max:200',
        ], [    
            'name.required' => 'Nama perusahaan tidak boleh kosong.',
            'name.unique_customerscompanies_name' => 'Nama perusahaan sudah terdaftar.',
            'name.string' => 'Nama perusahaan harus berupa teks.',
            'name.max' => 'Nama maksimal 100 karakter.',
            'industry.string' => 'Jenis industri harus berupa teks.',
            'industry.max' => 'Jenis industri maksimal 50 karakter.',
            'status.required' => 'Status tidak boleh kosong.',
            'status.in' => 'Status harus pilih salah satu dari: Tinggi, Sedang, Rendah.',
            'email.email' => 'Format email tidak valid.',
            'email.unique_customerscompanies_email' => 'Email sudah terdaftar.',
            'email.max' => 'Email maksimal 100 karakter.',
            'phone.numeric' => 'Nomor telepon harus berupa angka.',
            'phone.max_digits' => 'Nomor telepon maksimal 15 angka.',
            'phone.unique_customerscompanies_phone' => 'Nomor telepon sudah terdaftar.',
            'website.unique_customerscompanies_website' => 'Website sudah terdaftar.',
            'website.string' => 'Website harus berupa teks.',
            'website.max' => 'Website maksimal 255 karakter.',
            'owner.required' => 'Penanggung jawab kontak tidak boleh kosong.',
            'owner.email' => 'Penanggung jawab kontak harus berupa email valid.',
            'owner.max' => 'Penanggung jawab maksimal 100 karakter.',
            'owner.exists' => 'Penanggung jawab tidak tersedia.',
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

        $data = $request->all();
        if (isset($data['status'])) {
            $data['status'] = ActionMapperHelper::mapStatusToDatabase($data['status']);
        }

        try {
            $CustomersCompany = CustomersCompany::updateCustomersCompany($data, $customersCompanyId);
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
        $ids = $request->input('id', []);
        if (empty($ids)) {
            return new ApiResponseResource(
                false,
                "Pilih data perusahaan pelanggan yang ingin dihapus terlebih dahulu",
                null
            );
        }

        $companiesWithDeals = [];
        $companiesWithoutDeals = [];
        $companiesToNullifyContacts = [];
        $companiesWithDealsNames = [];

        foreach ($ids as $companyId) {
            $company = CustomersCompany::find($companyId);
            if (!$company) {
                continue;
            }

            if ($company->deals()->exists()) {
                $companiesWithDeals[] = $company->id;
                $companiesWithDealsNames[] = $company->name;
            } 
            elseif ($company->customers()->exists()) {
                $companiesToNullifyContacts[] = $company->id;
            } 
            else {
                $companiesWithoutDeals[] = $company->id;
            }
        }

        if (!empty($companiesWithDeals)) {
            return new ApiResponseResource(
                false,
                "Data perusahaan pelanggan tidak dapat dihapus karena terdapat " . count($companiesWithDeals) . " perusahaan pelanggan terhubung dengan data deals.",
                $companiesWithDealsNames
            );
        }

        try {
            if (!empty($companiesToNullifyContacts)) {
                Customer::nullifyCompanyAssociation($companiesToNullifyContacts);
            }

            $deletedCount = CustomersCompany::whereIn('id', $companiesWithoutDeals)->delete();

            $message = $deletedCount . " data perusahaan pelanggan berhasil dihapus.";
            return new ApiResponseResource(
                true,
                $message,
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

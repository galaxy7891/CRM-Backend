<?php

namespace App\Http\Controllers;

use App\Helpers\ActionMapperHelper;
use App\Models\Customer;
use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResponseResource;
use App\Services\DataLimitService;
use App\Traits\Filter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    use Filter;

    /** 
     * Display a listing of the resource. 
     * 
     * @return \Illuminate\Http\Response 
     */ 

    public function indexLeads(Request $request)
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
            $query = Customer::where('customerCategory', 'leads');

            $query->whereHas('user', function ($ownerQuery) use ($user) {
                $ownerQuery->where('user_company_id', $user->user_company_id);
            });
              
            if ($user->role == 'employee') {
                $query->where('owner', $user->email);
            }

            $leads = $this->applyFilters($request, $query);
            if ($leads->isEmpty()) {
                return new ApiResponseResource(
                    false,
                    'Data leads tidak ditemukan',
                    null
                );
            }
            
            $leads->getCollection()->transform(function ($lead) {
                $lead->customerCategory = ActionMapperHelper::mapCustomerCategory($lead->customerCategory);
                $lead->status = ActionMapperHelper::mapStatus($lead->status);
                return $lead;
            });

            return new ApiResponseResource(
                true,
                'Daftar leads',
                $leads
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
     * Display a listing of the resource.
     *  
     * @return \Illuminate\Http\Response
     */

    public function indexContact(Request $request)
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
            $query = Customer::with(['customersCompany:id,name'])
                    ->where('customerCategory', 'contact');

            $query->whereHas('user', function ($ownerQuery) use ($user) {
                $ownerQuery->where('user_company_id', $user->user_company_id);
            });
            
            if ($user->role == 'employee') {
                $query->where('owner', $user->email);
            }

            $contacts = $this->applyFilters($request, $query);
            if ($contacts->isEmpty()) {
                return new ApiResponseResource(
                    false,
                    'Data kontak tidak ditemukan',
                    null
                );
            }

            $contacts->getCollection()->transform(function ($contact) {
                $contact->customerCategory = ActionMapperHelper::mapCustomerCategory($contact->customerCategory);
                $contact->status = ActionMapperHelper::mapStatus($contact->status);
                return $contact;
            });

            return new ApiResponseResource(
                true,
                'Daftar kontak',
                $contacts
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
    public function storeLeads(Request $request)
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
            'first_name' => 'required|string|max:50',
            'last_name' => 'nullable|string|max:50',
            'phone' => 'required|numeric|max_digits:15|unique_customers_phone',
            'email' => 'nullable|email|max:100|unique_customers_email',
            'status' => 'required|in:Tinggi,Sedang,Rendah',
            'birthdate' => 'nullable|date',
            'job' => 'nullable|string|max:100',
            'owner' => 'required|email|max:100|exists:users,email',
            'address' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'subdistrict' => 'nullable|string|max:100',
            'village' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:5',
            'description' => 'nullable|string|max:200',
        ], [
            'first_name.required' => 'Nama depan tidak boleh kosong',
            'first_name.string' => 'Nama depan harus berupa teks',
            'first_name.max' => 'Nama depan maksimal 50 karakter',
            'last_name.string' => 'Nama belakang harus berupa teks',
            'last_name.max' => 'Nama belakang maksimal 50 karakter',
            'phone.required' => 'Nomor telepon tidak boleh kosong',
            'phone.numeric' => 'Nomor telepon harus berupa angka.',
            'phone.max_digits' => 'Nomor telepon maksimal 15 angka.',
            'phone.unique_customers_phone' => 'Nomor telepon sudah terdaftar.',
            'email.email' => 'Format email tidak valid.',
            'email.unique_customers_email' => 'Email sudah terdaftar.',
            'email.max' => 'Email maksimal 100 karakter.',
            'status.required' => 'Status leads wajib dipilih.',
            'status.in' => 'Status leads harus berupa pilih salah satu: Tinggi, Sedang, atau Rendah.',
            'job.string' => 'Pekerjaan harus berupa teks.',
            'job.max' => 'Pekerjaan maksimal 100 karakter.',
            'birthdate.date' => 'Tanggal lahir harus berupa tanggal yang valid.',
            'owner.required' => 'Penanggung jawab kontak tidak boleh kosong.',
            'owner.email' => 'Penanggung jawab kontak harus berupa email valid.',
            'owner.max' => 'Penanggung jawab maksimal 100 karakter.',
            'owner.exists' => 'Penanggung jawab tidak tersedia.',
            'address.string' => 'Alamat harus berupa teks.',
            'address.max' => 'Alamat maksimal 100 karakter.',
            'province.string' => 'Provinsi harus berupa teks.',
            'province.max' => 'Provinsi maksimal 100 karakter.',
            'city.string' => 'Kota harus berupa teks.',
            'city.max' => 'Kota maksimal 100 karakter.',
            'subdistrict.string' => 'Kecamatan harus berupa teks.',
            'subdistrict.max' => 'Kecamatan maksimal 100 karakter.',
            'village.string' => 'Desa/Kelurahan harus berupa teks.',
            'village.max' => 'Desa/Kelurahan maksimal 100 karakter.',
            'zip_code.string' => 'Kode pos harus berupa teks.',
            'zip_code.max' => 'Kode pos maksimal 10 karakter.',
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
        $dataLeads = $request->all();
        if (isset($dataLeads['status'])) {
            $dataLeads['status'] = ActionMapperHelper::mapStatusToDatabase($dataLeads['status']);
        }
        $dataLeads['customerCategory'] = 'leads';

        try {
            $customer = Customer::createCustomer($dataLeads);
            return new ApiResponseResource(
                true,
                'Data leads ' . ucfirst($customer->first_name) . ' ' . ucfirst($customer->last_name) . ' berhasil ditambahkan!',
                $customer
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
    public function storeContact(Request $request)
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
            'first_name' => 'required|string|max:50',
            'last_name' => 'nullable|string|max:50',
            'phone' => 'required|numeric|max_digits:15|unique_customers_phone',
            'status' => 'required|in:Tinggi,Sedang,Rendah',
            'birthdate' => 'nullable|date',
            'email' => 'nullable|email|max:100|unique_customers_email',
            'job' => 'nullable|string|max:100',
            'customers_company_id' => 'required|exists:customers_companies,id',
            'owner' => 'required|email|max:100|exists:users,email',
            'address' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'subdistrict' => 'nullable|string|max:100',
            'village' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:5',
            'description' => 'nullable|string|max:200',
        ], [
            'first_name.required' => 'Nama depan tidak boleh kosong',
            'first_name.string' => 'Nama depan harus berupa teks',
            'first_name.max' => 'Nama depan maksimal 50 karakter',
            'last_name.string' => 'Nama belakang harus berupa teks',
            'last_name.max' => 'Nama belakang maksimal 50 karakter',
            'phone.required' => 'Nomor telepon tidak boleh kosong',
            'phone.numeric' => 'Nomor telepon harus berupa angka.',
            'phone.max_digits' => 'Nomor telepon maksimal 15 angka.',
            'phone.unique_customers_phone' => 'Nomor telepon sudah terdaftar.',
            'email.email' => 'Format email tidak valid.',
            'email.unique_customers_email' => 'Email sudah terdaftar.',
            'email.max' => 'Email maksimal 100 karakter.',
            'status.required' => 'Status kontak wajib dipilih.',
            'status.in' => 'Status leads harus berupa pilih salah satu: Tinggi, Sedang, atau Rendah.',
            'birthdate.date' => 'Tanggal lahir harus berupa tanggal yang valid.',
            'job.string' => 'Pekerjaan harus berupa teks.',
            'job.max' => 'Pekerjaan maksimal 100 karakter.',
            'customers_company_id.required' => 'Perusahaan wajib dipilih.',
            'customers_company_id.exists' => 'Perusahaan belum terdaftar.',
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
            'zip_code.max' => 'Kode pos maksimal 10 karakter.',
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

        $dataContact = $request->all();
        if (isset($dataContact ['status'])) {
            $dataContact['status'] = ActionMapperHelper::mapStatusToDatabase($dataContact['status']);
        }
        $dataContact['customerCategory'] = 'contact';

        try {
            $customer = Customer::createCustomer($dataContact);
            return new ApiResponseResource(
                true,
                'Data kontak ' . ucfirst($customer->first_name) . ' ' .  ucfirst($customer->last_name) . ' berhasil ditambahkan!',
                $customer
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
    public function showLeads($leadsId)
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
            $leads = Customer::findCustomerByIdCategory($leadsId, 'leads');
            
            if (!$leads) {
                return new ApiResponseResource(
                    false,
                    'Data leads tidak ditemukan!',
                    null
                );
            }
            
            if ($leads->customers_company_id) {
                $leads->customers_company_name = $leads->customersCompany->name;
            } else {
                $leads->customers_company_name = null;
            }
            
            unset($leads->customersCompany);

            if ($user->role == 'employee' && $leads->owner !== $user->email) {
                return new ApiResponseResource(
                    false,
                    'Anda tidak memiliki akses untuk menampilkan data leads ini!',
                    null
                );
            }
            
            $leads->status = ActionMapperHelper::mapStatus($leads->status);

            return new ApiResponseResource(
                true,
                'Data leads',
                $leads
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
    public function showContact($contactId)
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
            $contacts = Customer::findCustomerByIdCategory($contactId, 'contact');

            if (!$contacts) {
                return new ApiResponseResource(
                    false,
                    'Data kontak tidak ditemukan!',
                    null
                );
            }
            
            if ($contacts->customers_company_id) {
                $contacts->customers_company_name = $contacts->customersCompany->name;
            } else {
                $contacts->customers_company_name = null;
            }
            
            unset($contacts->customersCompany);

            if ($user->role == 'employee' && $contacts->owner !== $user->email) {
                return new ApiResponseResource(
                    false,
                    'Anda tidak memiliki akses untuk menampilkan data kontak ini!',
                    null
                );
            }

            $contacts->status = ActionMapperHelper::mapStatus($contacts->status);

            return new ApiResponseResource(
                true,
                'Data kontak',
                $contacts
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
    public function updateLeads(Request $request, $leadsId)
    {
        $user = auth()->user();
        if (!$user) {
            return new ApiResponseResource(
                false,
                'Unauthorized',
                null
            );
        }

        $leads = Customer::findCustomerByIdCategory($leadsId, 'leads');
        if (!$leads) {
            return new ApiResponseResource(
                false,
                'Data leads tidak ditemukan',
                null
            );
        }

        if ($user->role == 'employee' && $leads->owner !== $user->email) {
            return new ApiResponseResource(
                false,
                'Anda tidak memiliki akses untuk mengubah data leads ini!',
                null
            );
        }
        
        $validator = Validator::make($request->all(), [
            'first_name' => 'sometimes|required|string|max:50',
            'last_name' => 'sometimes|nullable|string|max:50',
            'phone' => 'sometimes|required|numeric|max_digits:15|unique_customers_phone',
            'email' => 'sometimes|nullable|email|max:100|unique_customers_email',
            'status' => 'sometimes|required|in:Tinggi,Sedang,Rendah',
            'birthdate' => 'sometimes|nullable|date',
            'job' => 'sometimes|nullable|string|max:100',
            'customers_company_id' => 'sometimes|nullable|exists:customers_companies,id',
            'owner' => 'sometimes|required|email|max:100|exists:users,email',
            'address' => 'sometimes|nullable|string|max:100',
            'province' => 'sometimes|nullable|string|max:100',
            'city' => 'sometimes|nullable|string|max:100',
            'subdistrict' => 'sometimes|nullable|string|max:100',
            'village' => 'sometimes|nullable|string|max:100',
            'zip_code' => 'sometimes|nullable|string|max:5',
            'description' => 'sometimes|nullable|string',
        ], [
            'first_name.required' => 'Nama depan tidak boleh kosong',
            'first_name.string' => 'Nama depan harus berupa teks',
            'first_name.max' => 'Nama depan maksimal 50 karakter',
            'last_name.string' => 'Nama belakang harus berupa teks',
            'last_name.max' => 'Nama belakang maksimal 50 karakter',
            'phone.numeric' => 'Nomor telepon harus berupa angka.',
            'phone.max_digits' => 'Nomor telepon maksimal 15 angka.',
            'phone.unique_customers_phone' => 'Nomor telepon sudah terdaftar.',
            'email.email' => 'Format email tidak valid.',
            'email.unique_customers_email' => 'Email sudah terdaftar.',
            'email.max' => 'Email maksimal 100 karakter.',
            'status.required' => 'Status pelanggan wajib dipilih.',
            'status.in' => 'Status harus berupa pilih salah satu: Tinggi, Sedang, atau Rendah.',
            'birthdate.date' => 'Tanggal lahir harus berupa tanggal yang valid.',
            'job.string' => 'Pekerjaan harus berupa teks.',
            'job.max' => 'Pekerjaan maksimal 100 karakter.',
            'customers_company_id.exists'  => 'Perusahaan belum terdaftar.',
            'owner.required' => 'Penanggung jawab kontak tidak boleh kosong.',
            'owner.email' => 'Penanggung jawab kontak harus berupa email valid.',
            'owner.max' => 'Penanggung jawab maksimal 100 karakter.',
            'owner.exists' => 'Penanggung jawab tidak tersedia.',
            'address.string' => 'Alamat harus berupa teks.',
            'address.max' => 'Alamat maksimal 100 karakter.',
            'province.string' => 'Provinsi harus berupa teks.',
            'province.max' => 'Provinsi maksimal 100 karakter.',
            'city.string' => 'Kota harus berupa teks.',
            'city.max' => 'Kota maksimal 100 karakter.',
            'subdistrict.string' => 'Kecamatan harus berupa teks.',
            'subdistrict.max' => 'Kecamatan maksimal 100 karakter.',
            'village.string' => 'Desa/Kelurahan harus berupa teks.',
            'village.max' => 'Desa/Kelurahan maksimal 100 karakter.',
            'zip_code.string' => 'Kode pos harus berupa teks.',
            'zip_code.max' => 'Kode pos maksimal 10 karakter.',
            'description.string' => 'Deskripsi harus berupa teks.',
        ]);
        if ($validator->fails()) {
            return new ApiResponseResource(
                false,
                $validator->errors(),
                null
            );
        }
        $dataLeads = $request->all();
        if (isset($dataLeads['status'])) {
            $dataLeads['status'] = ActionMapperHelper::mapStatusToDatabase($dataLeads['status']);
        }
        
        try {
            $leads = Customer::updateCustomer($dataLeads, $leadsId);
            return new ApiResponseResource(
                true,
                'Data leads berhasil diubah!',
                $leads
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
    public function updateContact(Request $request, $contactId)
    {
        $user = auth()->user();
        if (!$user) {
            return new ApiResponseResource(
                false,
                'Unauthorized',
                null
            );
        }

        $contacts = Customer::findCustomerByIdCategory($contactId, 'contact');

        if (!$contacts) {
            return new ApiResponseResource(
                false,
                'Data kontak tidak ditemukan',
                null
            );
        }

        if ($user->role == 'employee' && $contacts->owner !== $user->email) {
            return new ApiResponseResource(
                false,
                'Anda tidak memiliki akses untuk mengubah data kontak ini!',
                null
            );
        }
        
        $validator = Validator::make($request->all(), [
            'first_name' => 'sometimes|required|string|max:50',
            'last_name' => 'sometimes|nullable|string|max:50',
            'phone' => 'sometimes|required|numeric|max_digits:15|unique_customers_phone',
            'email' => 'sometimes|nullable|email|max:100|unique_customers_email',
            'status' => 'sometimes|required|in:Tinggi,Sedang,Rendah',
            'birthdate' => 'sometimes|nullable|date',
            'job' => 'sometimes|nullable|string|max:100',
            'customers_company_id' => 'sometimes|nullable|exists:customers_companies,id',
            'owner' => 'sometimes|required|email|max:100|exists:users,email',
            'address' => 'sometimes|nullable|string|max:100',
            'province' => 'sometimes|nullable|string|max:100',
            'city' => 'sometimes|nullable|string|max:100',
            'subdistrict' => 'sometimes|nullable|string|max:100',
            'village' => 'sometimes|nullable|string|max:100',
            'zip_code' => 'sometimes|nullable|string|max:5',
            'description' => 'sometimes|nullable|string',
        ],  [
            'first_name.required' => 'Nama depan tidak boleh kosong',
            'first_name.string' => 'Nama depan harus berupa teks',
            'first_name.max' => 'Nama depan maksimal 50 karakter',
            'last_name.string' => 'Nama belakang harus berupa teks',
            'last_name.max' => 'Nama belakang maksimal 50 karakter',
            'phone.numeric' => 'Nomor telepon harus berupa angka.',
            'phone.max_digits' => 'Nomor telepon maksimal 15 angka.',
            'phone.unique_customers_phone' => 'Nomor telepon sudah terdaftar.',
            'email.email' => 'Format email tidak valid.',
            'email.unique_customers_email' => 'Email sudah terdaftar.',
            'email.max' => 'Email maksimal 100 karakter.',
            'status.required' => 'Status pelanggan wajib dipilih.',
            'status.in' => 'Status harus berupa pilih salah satu: Tinggi, Sedang, atau Rendah.',
            'birthdate.date' => 'Tanggal lahir harus berupa tanggal yang valid.',
            'job.string' => 'Pekerjaan harus berupa teks.',
            'job.max' => 'Pekerjaan maksimal 100 karakter.',
            'customers_company_id.exists' => 'Perusahaan belum terdaftar.',
            'owner.required' => 'Penanggung jawab kontak tidak boleh kosong.',
            'owner.email' => 'Penanggung jawab kontak harus berupa email valid.',
            'owner.max' => 'Penanggung jawab maksimal 100 karakter.',
            'owner.exists' => 'Penanggung jawab tidak tersedia.',
            'address.string' => 'Alamat harus berupa teks.',
            'address.max' => 'Alamat maksimal 100 karakter.',
            'province.string' => 'Provinsi harus berupa teks.',
            'province.max' => 'Provinsi maksimal 100 karakter.',
            'city.string' => 'Kota harus berupa teks.',
            'city.max' => 'Kota maksimal 100 karakter.',
            'subdistrict.string' => 'Kecamatan harus berupa teks.',
            'subdistrict.max' => 'Kecamatan maksimal 100 karakter.',
            'village.string' => 'Desa/Kelurahan harus berupa teks.',
            'village.max' => 'Desa/Kelurahan maksimal 100 karakter.',
            'zip_code.string' => 'Kode pos harus berupa teks.',
            'zip_code.max' => 'Kode pos maksimal 10 karakter.',
            'description.string' => 'Deskripsi harus berupa teks.',
        ]);
        if ($validator->fails()) {
            return new ApiResponseResource(
                false,
                $validator->errors(),
                null
            );
        }
        
        $dataContact = $request->all();
        if (isset($dataContact['status'])) {
            $dataContact['status'] = ActionMapperHelper::mapStatusToDatabase($dataContact['status']);
        }

        try {
            $contacts = Customer::updateCustomer($dataContact, $contactId);
            return new ApiResponseResource(
                true,
                'Data kontak berhasil diubah!',
                $contacts
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
    public function convert(Request $request, $leadsId)
    {
        $user = auth()->user();
        if (!$user) {
            return new ApiResponseResource(
                false,
                'Unauthorized',
                null
            );
        }
        
        $leads = Customer::findCustomerByIdCategory($leadsId, 'leads');
        if (!$leads) {
            return new ApiResponseResource(
                false,
                'Data leads tidak ditemukan',
                null
            );
        }
        
        if ($user->role == 'employee' && $leads->owner !== $user->email) {
            return new ApiResponseResource(
                false,
                'Anda tidak memiliki akses untuk konversi data leads ini!',
                null
            );
        }

        $validator = Validator::make($request->all(), [
            'first_name' => 'sometimes|required|string|max:50',
            'last_name' => 'sometimes|nullable|string|max:50',
            'phone' => 'sometimes|required|numeric|max_digits:15|unique_customers_phone',
            'email' => 'sometimes|nullable|email|max:100|unique_customers_email',
            'status' => 'sometimes|required|in:Tinggi,Sedang,Rendah',
            'birthdate' => 'sometimes|nullable|date',
            'job' => 'sometimes|nullable|string|max:100',
            'customers_company_id' => 'sometimes|nullable|exists:customers_companies,id',
            'owner' => 'sometimes|required|email|max:100|exists:users,email',
            'address' => 'sometimes|nullable|string|max:100',
            'province' => 'sometimes|nullable|string|max:100',
            'city' => 'sometimes|nullable|string|max:100',
            'subdistrict' => 'sometimes|nullable|string|max:100',
            'village' => 'sometimes|nullable|string|max:100',
            'zip_code' => 'sometimes|nullable|string|max:5',
            'description' => 'sometimes|nullable|string',
        ], [
            'first_name.required' => 'Nama depan tidak boleh kosong',
            'first_name.string' => 'Nama depan harus berupa teks',
            'first_name.max' => 'Nama depan maksimal 50 karakter',
            'last_name.string' => 'Nama belakang harus berupa teks',
            'last_name.max' => 'Nama belakang maksimal 50 karakter',
            'phone.numeric' => 'Nomor telepon harus berupa angka.',
            'phone.max_digits' => 'Nomor telepon maksimal 15 angka.',
            'phone.unique_customers_phone' => 'Nomor telepon sudah terdaftar.',
            'email.email' => 'Format email tidak valid.',
            'email.unique_customers_email' => 'Email sudah terdaftar.',
            'email.max' => 'Email maksimal 100 karakter.',
            'status.required' => 'Status pelanggan wajib dipilih.',
            'status.in' => 'Status harus berupa pilih salah satu: Tinggi, Sedang, atau Rendah.',
            'birthdate.date' => 'Tanggal lahir harus berupa tanggal yang valid.',
            'job.string' => 'Pekerjaan harus berupa teks.',
            'job.max' => 'Pekerjaan maksimal 100 karakter.',
            'customers_company_id.exists' => 'Perusahaan belum terdaftar.',
            'owner.required' => 'Penanggung jawab kontak tidak boleh kosong.',
            'owner.email' => 'Penanggung jawab kontak harus berupa email valid.',
            'owner.max' => 'Penanggung jawab maksimal 100 karakter.',
            'owner.exists' => 'Penanggung jawab tidak tersedia.',
            'address.string' => 'Alamat harus berupa teks.',
            'address.max' => 'Alamat maksimal 100 karakter.',
            'province.string' => 'Provinsi harus berupa teks.',
            'province.max' => 'Provinsi maksimal 100 karakter.',
            'city.string' => 'Kota harus berupa teks.',
            'city.max' => 'Kota maksimal 100 karakter.',
            'subdistrict.string' => 'Kecamatan harus berupa teks.',
            'subdistrict.max' => 'Kecamatan maksimal 100 karakter.',
            'village.string' => 'Desa/Kelurahan harus berupa teks.',
            'village.max' => 'Desa/Kelurahan maksimal 100 karakter.',
            'zip_code.string' => 'Kode pos harus berupa teks.',
            'zip_code.max' => 'Kode pos maksimal 10 karakter.',
            'description.string' => 'Deskripsi harus berupa teks.',
        ]);
        if ($validator->fails()) {
            return new ApiResponseResource(
                false,
                $validator->errors(),
                null
            );
        }
        
        $dataLeads = $request->all();
        if (isset($dataLeads['status'])) {
            $dataLeads['status'] = ActionMapperHelper::mapStatusToDatabase($dataLeads['status']);
        }

        try {
            $leads = Customer::convert($dataLeads, $leadsId);
            return new ApiResponseResource(
                true,
                'Data leads berhasil di konversi ke kontak',
                $leads
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
     * Remove the specified resource from storage for leads.
     */
    public function destroyLeads(Request $request)
    {
        $ids = $request->input('id', []);
        if (empty($ids)) {
            return new ApiResponseResource(
                false,
                "Pilih data yang ingin dihapus terlebih dahulu",
                null
            );
        }

        $leadsWithDeals = [];
        $leadsWithoutDeals = [];
        $leadsWithDealsNames = [];
        
        foreach ($ids as $leadId) {
            $lead = Customer::find($leadId);
            if (!$lead) {
                continue;
            }

            if ($lead->deals()->exists()) {
                $leadsWithDeals[] = $lead->id;
                $leadsWithDealsNames[] = ucfirst($lead->first_name) . ' ' . ucfirst($lead->last_name);
            } else {
                $leadsWithoutDeals[] = $lead->id;
            }
        }   

        if (count($leadsWithDeals) > 0) {
            return new ApiResponseResource(
                false,
                "Data leads tidak dapat dihapus karena terdapat " . count($leadsWithDeals) . " leads terhubung dengan data deals.",
                $leadsWithDealsNames
            );
        }

        try {
            $deletedCount = Customer::whereIn('id', $leadsWithoutDeals)->delete();

            return new ApiResponseResource(
                true,
                $deletedCount . " data leads berhasil dihapus.",
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

    /**
     * Remove the specified resource from storage for contacts.
     */
    public function destroyContact(Request $request)
    {
        $ids = $request->input('id', []);
        if (empty($ids)) {
            return new ApiResponseResource(
                false,
                "Pilih data yang ingin dihapus terlebih dahulu",
                null
            );
        }

        $contactsWithDeals = [];
        $contactsWithoutDeals = [];
        $contactsWithDealsNames = [];

        foreach ($ids as $contactId) {
            $contact = Customer::find($contactId);
            if (!$contact) {
                continue;
            }

            if ($contact->deals()->exists()) {
                $contactsWithDeals[] = $contact->id;
                $contactsWithDealsNames[] = ucfirst($contact->first_name) . ' ' . ucfirst($contact->last_name);
            } else {
                $contactsWithoutDeals[] = $contact->id;
            }
        }

        if (count($contactsWithDeals) > 0) {
            return new ApiResponseResource(
                false,
                "Data kontak tidak dapat dihapus karena terdapat " . count($contactsWithDeals) . " kontak terhubung dengan data deals.",
                $contactsWithDealsNames
            );
        }

        try {
            $deletedCount = Customer::whereIn('id', $contactsWithoutDeals)->delete();

            return new ApiResponseResource(
                true,
                $deletedCount . " data kontak berhasil dihapus.",
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

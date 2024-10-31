<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResponseResource;
use App\Traits\Filter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
        try {
            $user = auth()->user();
            $query = Customer::where('customerCategory', 'leads');
            
            if ($user->role == 'employee') {
                $query->where('owner', $user->email);
            }

            $leads = $this->applyFilters($request, $query);

            return new ApiResponseResource(
                true,
                'Daftar leads',
                $leads
            );
        } catch (\Exception $e) {
            return new ApiResponseResource(
                true,
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
        try {
            $user = auth()->user();
            $query = Customer::where('customerCategory', 'contact');

            if ($user->role == 'employee') {
                $query->where('owner', $user->email);
            }

            $contact = $this->applyFilters($request, $query);

            return new ApiResponseResource(
                true,
                'Daftar kontak',
                $contact
            );
        
        } catch (\Exception $e) {
            return new ApiResponseResource(
                true,
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
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:50',
            'last_name' => 'nullable|string|max:50',
            'phone' => 'required|numeric|max_digits:15|unique:customers,phone',
            'email' => 'nullable|email|unique:customers,email|max:100',
            'status' => 'required|in:hot,warm,cold',
            'birthdate' => 'nullable|date',
            'job' => 'nullable|string|max:100',
            'owner' => 'required|email|max:100',
            'address' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'subdistrict' => 'nullable|string|max:100',
            'village' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:5',
            'description' => 'nullable|string',
        ], [
            'first_name.required' => 'Nama depan tidak boleh kosong',
            'first_name.string' => 'Nama depan harus berupa teks',
            'first_name.max' => 'Nama depan maksimal 50 karakter',
            'last_name.string' => 'Nama belakang harus berupa teks',
            'last_name.max' => 'Nama belakang maksimal 50 karakter',
            'phone.required' => 'Nomor telepon tidak boleh kosong',
            'phone.numeric' => 'Nomor telepon harus berupa angka.',
            'phone.max_digits' => 'Nomor telepon maksimal 15 angka.',
            'phone.unique' => 'Nomor telepon sudah terdaftar.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'email.max' => 'Email maksimal 100 karakter.',
            'status.required' => 'Status pelanggan wajib dipilih.',
            'status.in' => 'Status harus berupa pilih salah satu: hot, warm, atau cold.',
            'job.string' => 'Pekerjaan harus berupa teks.',
            'job.max' => 'Pekerjaan maksimal 100 karakter.',
            'birthdate.date' => 'Tanggal lahir harus berupa tanggal yang valid.',
            'owner.required' => 'Pemilik kontak tidak boleh kosong.',
            'owner.email' => 'Pemilik kontak harus berupa email valid.',
            'owner.max' => 'Pemilik maksimal 100 karakter.',
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

        try {
            $dataLeads = array_merge($request->all(), [
                'customerCategory' => 'leads',
            ]);

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
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:50',
            'last_name' => 'nullable|string|max:50',
            'phone' => 'required|numeric|max_digits:15|unique:customers,phone',
            'status' => 'required|in:hot,warm,cold',
            'birthdate' => 'nullable|date',
            'email' => 'nullable|email|unique:customers,email|max:100',
            'job' => 'nullable|string|max:100',
            'organization_id' => 'nullable|uuid',
            'owner' => 'required|email|max:100',
            'address' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'subdistrict' => 'nullable|string|max:100',
            'village' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:5',
            'description' => 'nullable|string',
        ], [
            'first_name.required' => 'Nama depan tidak boleh kosong',
            'first_name.string' => 'Nama depan harus berupa teks',
            'first_name.max' => 'Nama depan maksimal 50 karakter',
            'last_name.string' => 'Nama belakang harus berupa teks',
            'last_name.max' => 'Nama belakang maksimal 50 karakter',
            'phone.numeric' => 'Nomor telepon harus berupa angka.',
            'phone.max_digits' => 'Nomor telepon maksimal 15 angka.',
            'phone.unique' => 'Nomor telepon sudah terdaftar.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'email.max' => 'Email maksimal 100 karakter.',
            'status.required' => 'Status pelanggan wajib dipilih.',
            'status.in' => 'Status harus berupa pilih salah satu: hot, warm, atau cold.',
            'birthdate.date' => 'Tanggal lahir harus berupa tanggal yang valid.',
            'job.string' => 'Pekerjaan harus berupa teks.',
            'job.max' => 'Pekerjaan maksimal 100 karakter.',
            'organization_id.uuid' => 'ID organisasi harus berupa UUID yang valid.',
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
            'zip_code.max' => 'Kode pos maksimal 10 karakter.',
            'address.string' => 'Alamat harus berupa teks.',
            'address.max' => 'Alamat maksimal 100 karakter.',
            'description.string' => 'Deskripsi harus berupa teks.',
        ]);
        if ($validator->fails()) {
            return new ApiResponseResource(
                false,
                $validator->errors(),
                null
            );
        }

        try {
            $dataContact = array_merge($request->all(), [
                'customerCategory' => 'contact',
            ]);

            $customer = Customer::createCustomer($dataContact);
            return new ApiResponseResource(
                true,
                'Data contact ' . ucfirst($customer->first_name) . ' ' .  ucfirst($customer->last_name) . ' berhasil ditambahkan!',
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
        try {
            $customer = Customer::findCustomerByIdCategory($leadsId, 'leads');

            if (!$customer) {
                return new ApiResponseResource(
                    false,
                    'Data leads tidak ditemukan!',
                    null
                );
            }

            $user = auth()->user();
            if ($user->role == 'employee' && $customer->owner !== $user->email) {
                return new ApiResponseResource(
                    false,
                    'Anda tidak memiliki akses untuk menampilkan data leads ini!',
                    null
                );
            }

            return new ApiResponseResource(
                true,
                'Data leads',
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
    public function showContact($contactId)
    {
        try {
            $customer = Customer::findCustomerByIdCategory($contactId, 'contact');

            if (!$customer) {
                return new ApiResponseResource(
                    false,
                    'Data contact tidak ditemukan!',
                    null
                );
            }

            $user = auth()->user();
            if ($user->role == 'employee' && $customer->owner !== $user->email) {
                return new ApiResponseResource(
                    false,
                    'Anda tidak memiliki akses untuk menampilkan data contact ini!',
                    null
                );
            }

            return new ApiResponseResource(
                true,
                'Data contact',
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
     * Update the specified resource in storage.
     */
    public function updateLeads(Request $request, $leadsId)
    {
        $customer = Customer::findCustomerByIdCategory($leadsId, 'leads');

        if (!$customer) {
            return new ApiResponseResource(
                false,
                'Data leads tidak ditemukan',
                null
            );
        }

        $user = auth()->user();
        if ($user->role == 'employee' && $customer->owner !== $user->email) {
            return new ApiResponseResource(
                false,
                'Anda tidak memiliki akses untuk mengubah data leads ini!',
                null
            );
        }

        $validator = Validator::make($request->all(), [
            'first_name' => 'sometimes|required|string|max:50',
            'last_name' => 'sometimes|nullable|string|max:50',
            'phone' => "sometimes|required|numeric|max_digits:15|unique:customers,phone,$leadsId",
            'email' => "sometimes|nullable|email|unique:customers,email,$leadsId|max:100",
            'status' => 'sometimes|required|in:hot,warm,cold',
            'birthdate' => 'sometimes|nullable|date',
            'job' => 'sometimes|nullable|string|max:100',
            'organization_id' => 'sometimes|nullable|uuid',
            'owner' => 'sometimes|required|email|max:100',
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
            'phone.unique' => 'Nomor telepon sudah terdaftar.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'email.max' => 'Email maksimal 100 karakter.',
            'status.required' => 'Status pelanggan wajib dipilih.',
            'status.in' => 'Status harus berupa pilih salah satu: hot, warm, atau cold.',
            'birthdate.date' => 'Tanggal lahir harus berupa tanggal yang valid.',
            'job.string' => 'Pekerjaan harus berupa teks.',
            'job.max' => 'Pekerjaan maksimal 100 karakter.',
            'organization_id.uuid' => 'ID organisasi harus berupa UUID yang valid.',
            'owner.required' => 'Pemilik kontak tidak boleh kosong.',
            'owner.email' => 'Pemilik kontak harus berupa email valid.',
            'owner.max' => 'Pemilik maksimal 100 karakter.',
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

        try {
            $customer = Customer::updateCustomer($request->all(), $leadsId);
            return new ApiResponseResource(
                true,
                'Data leads berhasil diubah!',
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
     * Update the specified resource in storage.
     */
    public function updateContact(Request $request, $contactId)
    {
        $customer = Customer::findCustomerByIdCategory($contactId, 'contact');

        if (!$customer) {
            return new ApiResponseResource(
                false,
                'Data contact tidak ditemukan',
                null
            );
        }

        $user = auth()->user();
        if ($user->role == 'employee' && $customer->owner !== $user->email) {
            return new ApiResponseResource(
                false,
                'Anda tidak memiliki akses untuk mengubah data contact ini!',
                null
            );
        }

        $validator = Validator::make($request->all(), [
            'first_name' => 'sometimes|required|string|max:50',
            'last_name' => 'sometimes|nullable|string|max:50',
            'phone' => "sometimes|required|numeric|max_digits:15|unique:customers,phone,$contactId",
            'email' => "sometimes|nullable|email|unique:customers,email,$contactId|max:100",
            'status' => 'sometimes|required|in:hot,warm,cold',
            'birthdate' => 'sometimes|nullable|date',
            'job' => 'sometimes|nullable|string|max:100',
            'organization_id' => 'sometimes|nullable|uuid',
            'owner' => 'sometimes|required|email|max:100',
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
            'phone.unique' => 'Nomor telepon sudah terdaftar.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'email.max' => 'Email maksimal 100 karakter.',
            'status.required' => 'Status pelanggan wajib dipilih.',
            'status.in' => 'Status harus berupa pilih salah satu: hot, warm, atau cold.',
            'birthdate.date' => 'Tanggal lahir harus berupa tanggal yang valid.',
            'job.string' => 'Pekerjaan harus berupa teks.',
            'job.max' => 'Pekerjaan maksimal 100 karakter.',
            'organization_id.uuid' => 'ID organisasi harus berupa UUID yang valid.',
            'owner.required' => 'Pemilik kontak tidak boleh kosong.',
            'owner.email' => 'Pemilik kontak harus berupa email valid.',
            'owner.max' => 'Pemilik maksimal 100 karakter.',
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

        try {
            $customer = Customer::updateCustomer($request->all(), $contactId);
            return new ApiResponseResource(
                true,
                'Data contact berhasil diubah!',
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
     * Update the specified resource in storage.
     */
    public function convert(Request $request, $leadsId)
    {
        $customer = Customer::findCustomerByIdCategory($leadsId, 'leads');

        if (!$customer) {
            return new ApiResponseResource(
                false,
                'Data leads tidak ditemukan',
                null
            );
        }

        $user = auth()->user();
        if ($user->role == 'employee' && $customer->owner !== $user->email) {
            return new ApiResponseResource(
                false,
                'Anda tidak memiliki akses untuk konversi data leads ini!',
                null
            );
        }

        $validator = Validator::make($request->all(), [
            'first_name' => 'sometimes|required|string|max:50',
            'last_name' => 'sometimes|nullable|string|max:50',
            'phone' => "sometimes|required|numeric|max_digits:15|unique:customers,phone,$leadsId",
            'email' => "sometimes|nullable|email|unique:customers,email,$leadsId|max:100",
            'status' => 'sometimes|required|in:hot,warm,cold',
            'birthdate' => 'sometimes|nullable|date',
            'job' => 'sometimes|nullable|string|max:100',
            'organization_id' => 'sometimes|nullable|uuid',
            'owner' => 'sometimes|required|email|max:100',
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
            'phone.unique' => 'Nomor telepon sudah terdaftar.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'email.max' => 'Email maksimal 100 karakter.',
            'status.required' => 'Status pelanggan wajib dipilih.',
            'status.in' => 'Status harus berupa pilih salah satu: hot, warm, atau cold.',
            'birthdate.date' => 'Tanggal lahir harus berupa tanggal yang valid.',
            'job.string' => 'Pekerjaan harus berupa teks.',
            'job.max' => 'Pekerjaan maksimal 100 karakter.',
            'organization_id.uuid' => 'ID organisasi harus berupa UUID yang valid.',
            'owner.required' => 'Pemilik kontak tidak boleh kosong.',
            'owner.email' => 'Pemilik kontak harus berupa email valid.',
            'owner.max' => 'Pemilik maksimal 100 karakter.',
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

        try {
            $customer = Customer::convert($request->all(), $leadsId);
            return new ApiResponseResource(
                true,
                'Data leads berhasil di konversi ke kontak',
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
     * Remove the specified resource from storage.
     */
    public function destroyLeads(Request $request)
    {
        $id = $request->input('id', []);
        if (empty($id)) {
            return new ApiResponseResource(
                true,
                "Pilih data yang ingin dihapus terlebih dahulu",
                null
            );
        }

        try {
            $deletedCount = Customer::whereIn('id', $id)->delete();
            if ($deletedCount > 0) {
                return new ApiResponseResource(
                    true,
                    $deletedCount . ' data leads berhasil dihapus',
                    null
                );
            }

            return new ApiResponseResource(
                false,
                'Data leads tidak ditemukan',
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
     * Remove the specified resource from storage.
     */
    public function destroyContact(Request $request)
    {
        $id = $request->input('id', []);
        if (empty($id)) {
            return new ApiResponseResource(
                true,
                "Pilih data yang ingin dihapus terlebih dahulu",
                null
            );
        }
        
        try {
            $deletedCount = Customer::whereIn('id', $id)->delete();
            if ($deletedCount > 0) {
                return new ApiResponseResource(
                    true,
                    $deletedCount . ' data kontak berhasil dihapus',
                    null
                );
            }

            return new ApiResponseResource(
                false,
                'Data kontak tidak ditemukan',
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

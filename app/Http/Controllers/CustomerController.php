<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResponseResource;
use App\Imports\CustomersImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        try {
            $user = auth()->user();

            if ($user->role == 'employee') {
                $customers = Customer::where('owner', $user->id)->latest()->paginate(25);
            } else {
                $customers = Customer::latest()->paginate(25);
            }

            return new ApiResponseResource(
                true,
                'Daftar Customer',
                $customers
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
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'organization_id' => 'nullable|uuid',
            'first_name' => 'required|string|max:50',
            'last_name' => 'nulalable|string|max:50',
            'customerCategory' => 'required|in:leads,contact',
            'job' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'status' => 'required|in:hot,warm,cold',
            'birthdate' => 'nullable|date',
            'email' => 'nullable|email|unique:customers,email|max:100',
            'phone' => 'nullable|numeric|max_digits:15|unique:customers,phone',
            'owner' => 'required|email|max:100',
            'country' => 'nullable|string|max:50',
            'province' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'subdistrict' => 'nullable|string|max:100',
            'village' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:5',
            'address' => 'nullable|string|max:100',
        ], [
            'organization_id.uuid' => 'ID organisasi harus berupa UUID yang valid.',
            'first_name.required' => 'Nama depan wajib diisi',
            'first_name.string' => 'Nama depan harus berupa teks',
            'first_name.max' => 'Nama depan maksimal 50 karakter',
            'last_name.string' => 'Nama belakang harus berupa teks',
            'last_name.max' => 'Nama belakang maksimal 50 karakter',
            'customerCategory.required' => 'Kategori pelanggan wajib dipilih.',
            'customerCategory.in' => 'Kategori pelanggan harus pilih salah satu: leads atau contact.',
            'job.string' => 'Pekerjaan harus berupa teks.',
            'job.max' => 'Pekerjaan maksimal 100 karakter.',
            'description.string' => 'Pekerjaan maksimal 100 karakter.',
            'status.required' => 'Status pelanggan wajib dipilih.',
            'status.in' => 'Status harus berupa pilih salah satu: hot, warm, atau cold.',
            'birthdate.date' => 'Tanggal lahir harus berupa tanggal yang valid.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'email.max' => 'Email maksimal 100 karakter.',
            'phone.numeric' => 'Nomor telepon harus berupa angka.',
            'phone.max_digits' => 'Nomor telepon maksimal 15 angka.',
            'phone.unique' => 'Nomor telepon sudah terdaftar.',
            'owner.required' => 'Pemilik kontak wajib diisi.',
            'owner.email' => 'Pemilik kontak harus berupa email valid.',
            'owner.max' => 'Pemilik maksimal 100 karakter.',
            'country.string' => 'Asal negara harus berupa teks.',
            'country.max' => 'Asal negara maksimal 50 karakter.',
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
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return new ApiResponseResource(
                false,
                $validator->errors(),
                null
            );
        }

        //create customer
        try {
            $customer = Customer::createCustomer($request->all());
            return new ApiResponseResource(
                true,
                "Data {$customer->first_name} {$customer->last_name} Berhasil Ditambahkan!",
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
    public function show($id)
    {
        try {

            // Check if customer exists
            $customer = Customer::find($id);
            if (is_null($customer)) {
                return new ApiResponseResource(
                    false,
                    'Data Customer Tidak Ditemukan!',
                    null
                );
            }

            $user = auth()->user();
            if ($user->role == 'employee' && $customer->owner !== $user->id) {
                return new ApiResponseResource(
                    false,
                    'Anda tidak memiliki akses untuk menampilkan data customer ini!',
                    null
                );
            }

            return new ApiResponseResource(
                true,
                'Data Customer Ditemukan!',
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
    public function update(Request $request, $id)
    {
        // Check if customer exists
        $customer = Customer::find($id);
        if (!$customer) {
            return new ApiResponseResource(
                false,
                'Customer tidak ditemukan',
                null
            );
        }

        // Is employee accessing his own customer data?
        $user = auth()->user();
        if ($user->role == 'employee' && $customer->owner !== $user->id) {
            return new ApiResponseResource(
                false,
                'Anda tidak memiliki akses untuk mengubah data customer ini!',
                null
            );
        }

        $validator = Validator::make($request->all(), [
            'organization_id' => 'nullable|uuid',
            'first_name' => 'required|string|max:50',
            'last_name' => 'nullable|string|max:50',
            'customerCategory' => 'required|in:leads,contact',
            'job' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'status' => 'required|in:hot,warm,cold',
            'birthdate' => 'nullable|date',
            'email' => 'nullable|email|unique:customers,email|max:100',
            'phone' => 'nullable|numeric|max_digits:15|unique:customers,phone',
            'owner' => 'required|email|max:100',
            'country' => 'nullable|string|max:50',
            'city' => 'nullable|string|max:100',
            'subdistrict' => 'nullable|string|max:100',
            'village' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:5',
            'address' => 'nullable|string|max:100',
        ], [
            'organization_id.uuid' => 'ID organisasi harus berupa UUID yang valid.',
            'first_name.required' => 'Nama depan wajib diisi',
            'first_name.string' => 'Nama depan harus berupa teks',
            'first_name.max' => 'Nama depan maksimal 50 karakter',
            'last_name.string' => 'Nama belakang harus berupa teks',
            'last_name.max' => 'Nama belakang maksimal 50 karakter',
            'customerCategory.required' => 'Kategori pelanggan wajib dipilih.',
            'customerCategory.in' => 'Kategori pelanggan harus pilih salah satu: leads atau contact.',
            'job.string' => 'Pekerjaan harus berupa teks.',
            'job.max' => 'Pekerjaan maksimal 100 karakter.',
            'description.string' => 'Pekerjaan maksimal 100 karakter.',
            'status.required' => 'Status pelanggan wajib dipilih.',
            'status.in' => 'Status harus berupa pilih salah satu: hot, warm, atau cold.',
            'birthdate.date' => 'Tanggal lahir harus berupa tanggal yang valid.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'email.max' => 'Email maksimal 100 karakter.',
            'phone.numeric' => 'Nomor telepon harus berupa angka.',
            'phone.max_digits' => 'Nomor telepon maksimal 15 angka.',
            'phone.unique' => 'Nomor telepon sudah terdaftar.',
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
            'zip_code.max' => 'Kode pos maksimal 10 karakter.',
            'address.string' => 'Alamat harus berupa teks.',
            'address.max' => 'Alamat maksimal 100 karakter.',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return new ApiResponseResource(
                false,
                $validator->errors(),
                null
            );
        }

        try {
            $customer = Customer::updateCustomer($request->all(), $id);
            return new ApiResponseResource(
                true,
                'Data Customer Berhasil Diubah!',
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
    public function destroy($id)
    {
        try {

            // Check if customer exists
            $customer = Customer::find($id);
            if (!$customer) {
                return new ApiResponseResource(
                    false,
                    'Customer tidak ditemukan',
                    null
                );
            }

            // Is employee accessing his own customer data?
            $user = auth()->user();
            if ($user->role == 'employee' && $customer->owner !== $user->id) {
                return new ApiResponseResource(
                    false,
                    'Anda tidak memiliki akses untuk menghapus data customer ini!',
                    null
                );
            }

            // Delete the customer
            $customer->delete();

            // Return response with first and last name
            return new ApiResponseResource(
                true,
                "Customer {$customer->first_name} {$customer->last_name} Berhasil Dihapus!",
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

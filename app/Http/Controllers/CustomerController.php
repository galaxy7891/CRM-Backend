<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

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

            return new CustomerResource(
                true, // success
                'Daftar Customer', // message
                $customers // data
            );
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'organization_id' => 'nullable|uuid',
            'user_id' => 'required|uuid|exists:users,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'customerCategory' => 'required|in:leads,contact',
            'job' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:hot,warm,cold',
            'birthdate' => 'nullable|date',
            'email' => 'nullable|email|unique:customers,email',
            'phone' => 'nullable|string|max:15|unique:customers,phone',
            'owner' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'subdistrict' => 'nullable|string|max:255',
            'village' => 'nullable|string|max:255',
            'zip_code' => 'nullable|string|max:10',
        ], [
            'organization_id.uuid' => 'ID organisasi harus berupa UUID yang valid.',
            'user_id.required' => 'Pengguna wajib diisi.',
            'user_id.uuid' => 'ID pengguna harus berupa UUID yang valid.',
            'first_name.required' => 'Nama depan wajib diisi.',
            'last_name.required' => 'Nama belakang wajib diisi.',
            'customerCategory.required' => 'Kategori pelanggan wajib dipilih.',
            'customerCategory.in' => 'Kategori pelanggan harus berupa salah satu: leads atau contact.',
            'job.string' => 'Pekerjaan harus berupa teks.',
            'status.required' => 'Status pelanggan wajib diisi.',
            'status.in' => 'Status harus berupa salah satu: hot, warm, atau cold.',
            'birthdate.date' => 'Tanggal lahir harus berupa tanggal yang valid.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'phone.string' => 'Nomor telepon harus berupa teks.',
            'phone.unique' => 'Nomor telepon sudah terdaftar.',
            'owner.required' => 'Pemilik kontak wajib diisi.',
            'address.string' => 'Alamat harus berupa teks.',
            'zip_code.max' => 'Kode pos maksimal 10 karakter.',
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
                'data' => null
            ], 422);
        }

        //create customer
        try {
            $customer = Customer::createCustomer($request->all());
            return new CustomerResource(
                true, // success
                "Data {$customer->first_name} {$customer->last_name} Berhasil Ditambahkan!", // message
                $customer // data
            );
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
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
                return new CustomerResource(
                    false, // success
                    'Data Customer Tidak Ditemukan!', // message
                    null // data
                );
            }

            // Is employee accessing his own customer data?
            $user = auth()->user();
            if ($user->role == 'employee' && $customer->owner !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk menampilkan data customer ini!',
                    'data' => null
                ], 403);
            }

            return new CustomerResource(
                true, // success
                'Data Customer Ditemukan!', // message
                $customer // data
            );
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
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
            return response()->json([
                'success' => false,
                'message' => 'Customer tidak ditemukan',
                'data' => null
            ], 404);
        }

        // Is employee accessing his own customer data?
        $user = auth()->user();
        if ($user->role == 'employee' && $customer->owner !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk mengubah data customer ini!',
                'data' => null
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'organization_id' => 'nullable|uuid',
            'user_id' => 'required|uuid',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'customerCategory' => 'required|in:leads,contact',
            'job' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:hot,warm,cold',
            'birthdate' => 'nullable|date',
            'email' =>
            [
                'sometimes', // only update if email is provided
                'nullable',
                'string',
                'max:255',
                Rule::unique('customers', 'email')->ignore($id) // ignore validation for this id
            ],
            'phone' =>
            [
                'sometimes', // only update if phone is provided
                'nullable',
                'string',
                'max:255',
                Rule::unique('customers', 'phone')->ignore($id) // ignore validation for this id
            ],
            'owner' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'subdistrict' => 'nullable|string|max:255',
            'village' => 'nullable|string|max:255',
            'zip_code' => 'nullable|string|max:10',
        ], [
            'organization_id.uuid' => 'ID organisasi harus berupa UUID yang valid.',
            'user_id.required' => 'Pengguna wajib diisi.',
            'user_id.uuid' => 'ID pengguna harus berupa UUID yang valid.',
            'first_name.required' => 'Nama depan wajib diisi.',
            'last_name.required' => 'Nama belakang wajib diisi.',
            'customerCategory.required' => 'Kategori pelanggan wajib dipilih.',
            'customerCategory.in' => 'Kategori pelanggan harus berupa salah satu: leads atau contact.',
            'job.string' => 'Pekerjaan harus berupa teks.',
            'status.required' => 'Status pelanggan wajib diisi.',
            'status.in' => 'Status harus berupa salah satu: hot, warm, atau cold.',
            'birthdate.date' => 'Tanggal lahir harus berupa tanggal yang valid.',
            'email.email' => 'Format email tidak valid.',
            'phone.string' => 'Nomor telepon harus berupa teks.',
            'owner.required' => 'Pemilik customer wajib diisi.',
            'address.string' => 'Alamat harus berupa teks.',
            'zip_code.max' => 'Kode pos maksimal 10 karakter.',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
                'data' => null
            ], 422);
        }

        try {
            $customer = Customer::updateCustomer($request->all(), $id);
            return new CustomerResource(
                true, // successs
                'Data Customer Berhasil Diubah!', // message
                $customer // data
            );
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
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
                return response()->json([
                    'success' => false,
                    'message' => 'Customer tidak ditemukan',
                    'data' => null
                ], 404);
            }

            // Is employee accessing his own customer data?
            $user = auth()->user();
            if ($user->role == 'employee' && $customer->owner !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk menghapus data customer ini!',
                    'data' => null
                ], 403);
            }

            // Delete the customer
            $customer->delete();

            // Return response with first and last name
            return new CustomerResource(
                true, // success
                "Customer {$customer->first_name} {$customer->last_name} Berhasil Dihapus!", // message
                null // data
            );
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use App\Models\Organization;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\OrganizationResource;

class OrganizationController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $organizations = Organization::latest()->paginate(10);

        return new OrganizationResource(true, 'Daftar Organization', $organizations);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'unique:organizations,name|required|string|max:255',
            'industry' => 'nullable|string|max:255',
            'status' => 'required|in:hot,warm,cold',
            'email' => 'nullable|email|unique:organizations,email',
            'phone' => 'nullable|string|max:15|unique:organizations,phone',
            'owner' => 'required|string|max:255',
            'website' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'subdistrict' => 'nullable|string|max:255',
            'village' => 'nullable|string|max:255',
            'zip_code' => 'nullable|string|max:10',
        ], [
            'name.unique' => 'Organisasi sudah terdaftar.',
            'name.required' => 'Nama wajib diisi.',
            'name.string' => 'Nama harus berupa string.',
            'name.max' => 'Nama terlalu panjang.',
            'industry.string' => 'Bidang usaha harus berupa string.',
            'industry.max' => 'Bidang usaha terlalu panjang.',
            'status.required' => 'Status wajib diisi.',
            'status.in' => 'Status harus salah satu dari: hot, warm, cold.',
            'email.email' => 'Email harus valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'phone.string' => 'Nomor telepon harus berupa teks.',
            'phone.unique' => 'Nomor telepon sudah terdaftar.',
            'owner.required' => 'Pemilik kontak wajib diisi.',
            'address.string' => 'Alamat harus berupa teks.',
            'zip_code.max' => 'Kode pos maksimal 10 karakter.',
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors(), 'data' => null], 422);
        }

        $organization = Organization::create([
            'id' => Str::uuid(),
            'name' => $request->name,
            'industry' => $request->industry,
            'status' => $request->status,
            'email' => $request->email,
            'phone' => $request->phone,
            'owner' => $request->owner,
            'website' => $request->website,
            'address' => $request->address,
            'country' => $request->country,
            'city' => $request->city,
            'subdistrict' => $request->subdistrict,
            'village' => $request->village,
            'zip_code' => $request->zip_code,
        ]);

        return new OrganizationResource(true, "Data {$organization->name} Berhasil Ditambahkan!", $organization);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $organization = Organization::find($id);
        if (is_null($organization)) {
            return new OrganizationResource(false, 'Data Organisasi Tidak Ditemukan!', null);
        }
        return new OrganizationResource(true, 'Data Organisasi Ditemukan!', $organization);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Check if customer exists
        $organization = Organization::find($id);

        if (!$organization) {
            return response()->json(['success' => false, 'message' => 'Organization tidak ditemukan', 'data' => null], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => [

                'required',
                'string',
                'max:255',
                Rule::unique('organizations', 'name')->ignore($id) // ignore validation for this id
            ],
            'industry' => 'nullable|string|max:255',
            'status' => 'required|in:hot,warm,cold',
            'email' => [
                'sometimes', // only update if email is provided
                'required',
                'string',
                'max:255',
                Rule::unique('organizations', 'email')->ignore($id) // ignore validation for this id
            ],
            'phone' =>
            [
                'sometimes', // only update if phone is provided
                'required',
                'string',
                'max:255',
                Rule::unique('organizations', 'phone')->ignore($id) // ignore validation for this id
            ],
            'owner' => 'required|string|max:255',
            'website' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'subdistrict' => 'nullable|string|max:255',
            'village' => 'nullable|string|max:255',
            'zip_code' => 'nullable|string|max:10',
        ], [
            'name.unique' => 'Organisasi sudah terdaftar.',
            'name.required' => 'Nama wajib diisi.',
            'name.string' => 'Nama harus berupa string.',
            'name.max' => 'Nama terlalu panjang.',
            'industry.string' => 'Bidang usaha harus berupa string.',
            'industry.max' => 'Bidang usaha terlalu panjang.',
            'status.required' => 'Status wajib diisi.',
            'status.in' => 'Status harus salah satu dari: hot, warm, cold.',
            'email.email' => 'Email harus valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'phone.string' => 'Nomor telepon harus berupa teks.',
            'phone.unique' => 'Nomor telepon sudah terdaftar.',
            'owner.required' => 'Pemilik kontak wajib diisi.',
            'address.string' => 'Alamat harus berupa teks.',
            'zip_code.max' => 'Kode pos maksimal 10 karakter.',
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors(), 'data' => null], 422);
        }

        $organization->update([
            'name' => $request->name,
            'industry' => $request->industry,
            'status' => $request->status,
            'email' => $request->email,
            'phone' => $request->phone,
            'owner' => $request->owner,
            'website' => $request->website,
            'address' => $request->address,
            'country' => $request->country,
            'city' => $request->city,
            'subdistrict' => $request->subdistrict,
            'village' => $request->village,
            'zip_code' => $request->zip_code,
        ]);

        return new OrganizationResource(true, 'Data Customer Berhasil Diubah!', $organization);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $organization = Organization::find($id);

        if (!$organization) {
            return response()->json(['success' => false, 'message' => 'Customer tidak ditemukan', 'data' => null], 404);
        }

        // Delete the customer
        $organization->delete();

        // Return response with first and last name
        return new OrganizationResource(true, "Organization {$organization->name} Berhasil Dihapus!", null);
    }
}

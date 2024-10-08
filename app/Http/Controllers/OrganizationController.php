<?php

namespace App\Http\Controllers;

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
        try {
            // Is employee accessing his own organization data?
            $user = auth()->user();
            if ($user->role == 'employee') {
                $organizations = Organization::where('owner', $user->id)->latest()->paginate(25);
            } else {
                $organizations = Organization::latest()->paginate(25);
            }

            return new OrganizationResource(
                true, // success
                'Daftar Organization', // message
                $organizations // data
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
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
                'data' => null
            ], 422);
        }

        try {
            $organization = Organization::createOrganization($request->all());
            return new OrganizationResource(
                true,
                "Data {$organization->name} Berhasil Ditambahkan!", 
                $organization
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

            // Check if organization exists
            $organization = Organization::find($id);
            if (is_null($organization)) {
                return response()->json([
                    'success' => false, // success
                    'message' => 'Data Organisasi Tidak Ditemukan!', // message
                    'data' => null // data
                ], 404);
            }

            // Is employee accessing his own organization data?
            $user = auth()->user();
            if ($user->role == 'employee' && $organization->owner !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk menampilkan data organisasi ini!',
                    'data' => null
                ], 403);
            }

            return new OrganizationResource(
                true, // success
                'Data Organisasi Ditemukan!', // message
                $organization // data
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
        // Check if organization exists
        $organization = Organization::find($id);

        if (!$organization) {
            return response()->json([
                'success' => false,
                'message' => 'Organization tidak ditemukan',
                'data' => null
            ], 404);
        }

        // Is employee accessing his own customer data?
        $user = auth()->auth();
        if ($user == 'employee' && $organization->owner !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk mengubah data organisasi ini!',
                'data' => null
            ], 403);
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
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
                'data' => null
            ], 422);
        }

        try {
            $organization = Organization::updateOrganization($request->all(), $id);
            return new OrganizationResource(
                true, // success
                'Data Organization Berhasil Diubah!', // message
                $organization // data
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

            $organization = Organization::find($id);
            if (!$organization) {
                return response()->json([
                    'success' => false,
                    'message' => 'Organization tidak ditemukan',
                    'data' => null
                ], 404);
            }

            $user = auth()->auth();
            if ($user == 'employee' && $organization->owner !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk menghapus data organisasi ini!',
                    'data' => null
                ], 403);
            }


            // Delete the organization
            $organization->delete();

            // Return response with first and last name
            return new OrganizationResource(
                true, // success
                "Organization {$organization->name} Berhasil Dihapus!", // message
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

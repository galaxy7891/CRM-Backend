<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApiResponseResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $users = User::latest()->paginate(25);

            return new ApiResponseResource(
                true,
                'Daftar Karyawan',
                $users
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
    public function show($id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return new ApiResponseResource(
                    false,
                    'Data karyawan tidak ditemukan',
                    null
                );
            }

            return new ApiResponseResource(
                true,
                "Data karyawan {$user->first_name} " . strtolower($user->last_name),
                $user
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
        $user = User::find($id);

        if (!$user) {
            return new ApiResponseResource(
                false,
                'Data karyawan tidak ditemukan',
                null
            );
        }

        $validator = Validator::make($request->all(), [
            'company_id' => 'nullable|uuid',
            'email' => 'required|email|unique:users,email',
            'first_name' => 'required|string|max:50',
            'last_name' => 'nullable|string|max:50',
            'phone' => 'required|numeric|max_digits:15|unique:users, phone',
            'job_position' => 'required|max:50',
            'role' => 'required|in:super_admin,admin,employee',
            'gender' => 'nullable|in:male,female,other',
        ], [
            'company_id.uuid' => 'ID Company harus berupa UUID yang valid.',
            'email.required' => 'Email tidak boleh kosong',
            'email.email' => 'Email harus valid',
            'email.unique' => 'Email sudah terdaftar',
            'first_name.required' => 'Nama depan tidak boleh kosong',
            'first_name.string' => 'Nama depan harus berupa teks',
            'first_name.max' => 'Nama depan maksimal 50 karakter',
            'last_name.required' => 'Nama belakang tidak boleh kosong',
            'last_name.string' => 'Nama belakang harus berupa teks',
            'last_name.max' => 'Nama belakang maksimal 50 karakter',
            'phone.required' => 'Nomor telepon tidak boleh kosong',
            'phone.numeric' => 'Nomor telepon harus berupa angka',
            'phone.max_digits' => 'Nomor telepon maksimal 15 angka',
            'phone.unique' => 'Nomor telepon sudah terdaftar.',
            'job_position.required' => 'Posisi pekerjaan tidak boleh kosong',
            'job_position.max' => 'Posisi pekerjaan maksimal 50 karakter',
            'role.required' => 'Akses user harus diisi',
            'role.in' => 'Akses harus pilih salah satu: rendah, sedang, atau tinggi.',
            'gender.in' => 'Gender harus pilih salah satu: Laki-laki, Perempuan, Lain-lain.',
        ]);

        if ($validator->fails()) {
            return new ApiResponseResource(
                false,
                $validator->errors(),
                null
            );
        }

        try {

            $user = User::updateUser($request->all(), $id);
            return new ApiResponseResource(
                true, 
                "Data karyawan {$user->first_name} " . strtolower($user->last_name) . "berhasil diubah",
                $user
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

            $user = User::find($id);
            if (!$user) {
                return new ApiResponseResource(
                    false,
                    'Karyawan tidak ditemukan',
                    null
                );
            }

            $first_name = $user->first_name;
            $last_name = $user->last_name;
            $user->delete();

            return new ApiResponseResource(
                true,
                "Karyawan {$first_name} {$last_name} Berhasil Dihapus!",
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

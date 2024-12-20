<?php

namespace App\Http\Controllers;

use App\Helpers\ActionMapperHelper;
use App\Http\Resources\ApiResponseResource;
use App\Models\User;
use App\Traits\Filter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    use Filter;

    /**
     * Display a listing of the resource.
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
            $query = User::where('user_company_id', $user->user_company_id)
                ->where('id', '!=', $user->id);
            
            $employees = $this->applyFilters($request, $query);
            if (!$employees) {
                return new ApiResponseResource(
                    false,
                    'Data karyawan tidak ditemukan',
                    null
                );
            }

            $employees->getCollection()->transform(function ($employee) {
                $employee->role = ActionMapperHelper::mapRole($employee->role);
                $employee->gender = ActionMapperHelper::mapGender($employee->gender);
                return $employee;
            });
            
            return new ApiResponseResource(
                true,
                'Daftar Karyawan',
                $employees
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
            $user = User::find($id);

            if (!$user) {
                return new ApiResponseResource(
                    false,
                    'Data karyawan tidak ditemukan',
                    null
                );
            }

            $user->role = ActionMapperHelper::mapRole($user->role);
            $user->gender = ActionMapperHelper::mapGender($user->gender);

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
    public function update(Request $request, $employeeId)
    {
        $user = User::find($employeeId);

        if (!$user) {
            return new ApiResponseResource(
                false,
                'Data karyawan tidak ditemukan',
                null
            );
        }
        
        $validator = Validator::make($request->all(), [
            'first_name' => 'sometimes|required|string|max:50',
            'last_name' => 'sometimes|nullable|string|max:50',
            'phone' => 'sometimes|required|numeric|max_digits:15|unique_user_phone',
            'email' => 'sometimes|required|email|unique_user_email',
            'job_position' => 'sometimes|required|max:50',
            'user_company_id' => 'sometimes|nullable|uuid',
            'role' => 'sometimes|required|in:Super Admin,Admin,Karyawan',
            'gender' => 'sometimes|nullable|in:Laki-laki,Perempuan,Lainnya',
        ], [
            'email.required' => 'Email tidak boleh kosong',
            'email.email' => 'Email harus valid',
            'email.unique_user_email' => 'Email sudah terdaftar',
            'first_name.required' => 'Nama depan tidak boleh kosong',
            'first_name.string' => 'Nama depan harus berupa teks',
            'first_name.max' => 'Nama depan maksimal 50 karakter',
            'last_name.required' => 'Nama belakang tidak boleh kosong',
            'last_name.string' => 'Nama belakang harus berupa teks',
            'last_name.max' => 'Nama belakang maksimal 50 karakter',
            'phone.required' => 'Nomor telepon tidak boleh kosong',
            'phone.numeric' => 'Nomor telepon harus berupa angka',
            'phone.max_digits' => 'Nomor telepon maksimal 15 angka',
            'phone.unique_user_phone' => 'Nomor telepon sudah terdaftar.',
            'job_position.required' => 'Jabatan tidak boleh kosong',
            'job_position.max' => 'Jabatan maksimal 50 karakter',
            'user_company_id.uuid' => 'ID Company harus berupa UUID yang valid.',
            'role.required' => 'Akses user harus diisi',
            'role.in' => 'Akses harus pilih salah satu: Super Admin, Admin, atau Karyawan.',
            'gender.in' => 'Gender harus pilih salah satu: Laki-laki, Perempuan, Lainnya.',
        ]);
        if ($validator->fails()) {
            return new ApiResponseResource(
                false,
                $validator->errors(),
                null
            );
        }
        
        $data = $request->all();
        if (isset($data['gender'])) {
            $data['gender'] = ActionMapperHelper::mapGenderToDatabase($data['gender']);
        }
        if (isset($data['role'])) {
            $data['role'] = ActionMapperHelper::mapRoleToDatabase($data['role']);
        }
        
        try {
            $user = User::updateUser($data, $employeeId);
            return new ApiResponseResource(
                true, 
                'Data karyawan ' .  ucfirst($user->first_name) . ' ' . ucfirst($user->last_name) . " berhasil diubah",
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
    public function destroy(Request $request)
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
            $deletedCount = User::whereIn('id', $id)->delete();

            if ($deletedCount > 0) {
                return new ApiResponseResource(
                    true,
                    $deletedCount . ' data karyawan berhasil dihapus',
                    null
                );
            }
            return new ApiResponseResource(
                false,
                'Data karyawan tidak ditemukan',
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

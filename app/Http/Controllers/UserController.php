<?php

namespace App\Http\Controllers;

use App\Helpers\ActionMapperHelper;
use App\Mail\TemplateForgetPassword;
use App\Models\Customer;
use App\Models\Deal;
use App\Models\CustomersCompany;
use App\Models\PasswordResetToken;
use App\Models\User;
use Illuminate\Support\Str;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\ApiResponseResource;

class UserController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show()
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
            $user->role = ActionMapperHelper::mapRole($user->role);
            $user->gender = ActionMapperHelper::mapGender($user->gender);
            return new ApiResponseResource(
                true,
                "Data user {$user->first_name} " . strtolower($user->last_name),
                $user->load('company'),
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
    public function update(Request $request)
    {   
        $user = auth()->user();
        $id = $user->id;
        if (!$user) {
            return new ApiResponseResource(
                false,
                'Unauthorized',
                null
            );
        }

        $validator = Validator::make($request->all(), [
            'user_company_id' => 'sometimes|nullable|uuid',
            'email' => 'sometimes|required|email|'. Rule::unique('users', 'email')->ignore($id)->whereNull('deleted_at'),
            'first_name' => 'sometimes|required|string|max:50',
            'last_name' => 'sometimes|nullable|string|max:50',
            'phone' => 'sometimes|required|numeric|max_digits:15|'. Rule::unique('users', 'phone')->ignore($id)->whereNull('deleted_at'),
            'role' => 'sometimes|required|in:super admin,admin,karyawan',
            'job_position' => 'sometimes|required|max:50',
            'gender' => 'sometimes|nullable|in:laki-laki,perempuan,lainnya',
        ], [
            'user_company_id.uuid' => 'ID Company harus berupa UUID yang valid.',
            'email.required' => 'Email tidak boleh kosong',
            'email.email' => 'Email harus valid',
            'email.unique' => 'Email sudah terdaftar',
            'first_name.required' => 'Nama depan tidak boleh kosong',
            'first_name.string' => 'Nama depan harus berupa teks',
            'first_name.max' => 'Nama depan maksimal 50 karakter',
            'last_name.string' => 'Nama belakang harus berupa teks',
            'last_name.max' => 'Nama belakang maksimal 50 karakter',
            'phone.required' => 'Nomor telepon tidak boleh kosong',
            'phone.numeric' => 'Nomor telepon harus berupa angka',
            'phone.max_digits' => 'Nomor telepon maksimal 15 angka',
            'phone.unique' => 'Nomor telepon sudah terdaftar.',
            'job_position.required' => 'Jabatan tidak boleh kosong',
            'job_position.max' => 'Jabatan maksimal 50 karakter',
            'role.required' => 'Akses user harus diisi',
            'role.in' => 'Akses harus pilih salah satu: super admin, admin, atau karyawan.',
            'gender.in' => 'Gender harus pilih salah satu: laki-laki, perempuan, lainnya.',
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
            $updatedUser = User::updateUser($data, $user->id);

            return new ApiResponseResource(
                true,
                "Data pengguna {$updatedUser->first_name} " . strtolower($updatedUser->last_name) . " berhasil diubah",
                $updatedUser
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
     * Update the authenticated user's password.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|min:8',
            'new_password' => 'required|min:8',
            'confirm_new_password' => 'required|min:8|same:new_password',
        ], [
            'password.required' => 'Kata sandi tidak boleh kosong',
            'password.min' => 'Kata sandi minimal 8 digit',
            'new_password.required' => 'Kata sandi baru tidak boleh kosong',
            'new_password.min' => 'Kata sandi baru minimal 8 digit',
            'confirm_new_password.required' => 'Konfirmasi kata sandi tidak boleh kosong',
            'confirm_new_password.min' => 'Konfirmasi kata sandi minimal 8 digit',
            'confirm_new_password.same' => 'Konfirmasi kata sandi tidak sama'
        ]);
        if ($validator->fails()) {
            return new ApiResponseResource(
                false,
                $validator->errors(),
                null
            );
        }

        $user = auth()->user();

        try {
            if (!Hash::check($request->password, $user->password)) {
                return new ApiResponseResource(
                    true,
                    'Password tidak sesuai',
                    null
                );
            }

            $user->updatePassword($request->new_password);

            return new ApiResponseResource(
                true,
                'Password berhasil diubah',
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
     * Update photo profile in cloudinary.
     */
    public function updateProfilePhoto(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return new ApiResponseResource(
                false,
                'Unauthorized',
                null
            );
        }

        $validator = Validator::make($request->only('photo'), [
            'photo' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'photo.required' => 'Foto profil tidak boleh kosong.',
            'photo.image' => 'Foto profil harus berupa gambar.',
            'photo.mimes' => 'Foto profil tidak sesuai format.',
            'photo.max' => 'Foto profil maksimal 2mb.',
        ]);
        if ($validator->fails()) {
            return new ApiResponseResource(
                false,
                $validator->errors(),
                null
            );
        }

        try {

            $photoData = $user->updateProfilePhoto($request->file('photo'), $user->id);

            return new ApiResponseResource(
                true,
                "Foto profil pengguna {$user->first_name} " . strtolower($user->last_name) . "berhasil diubah",
                $photoData
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
    public function destroy()
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
            $first_name = $user->first_name;
            $last_name = $user->last_name;
            $user = User::deleteUser($user->id);

            return new ApiResponseResource(
                true,
                "Data pengguna {$first_name} " . strtolower($last_name) . "berhasil dihapus",
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
     * Send a password reset link to the given email address.
     *
     * @param \Illuminate\Http\Request $request 
     *
     * @return \Illuminate\Http\JsonResponse 
     */
    public function sendResetLink(Request $request)
    {
        $email = '';
        $frontendPath = '';

        if (auth()->check()) {
            $user = auth()->user();
            $email = $user->email;
            $frontendPath = '/change-password-email?email=';

        } else {
            $validator = Validator::make($request->only('email'), [
                'email' => 'required|email|exists:users,email|max:100'
            ], [
                'email.required' => 'Email tidak boleh kosong',
                'email.email' => 'Email harus valid',
                'email.exists' => 'Email belum terdaftar',
                'email.max' => 'Email maksimal 100 karakter',
            ]);
            if ($validator->fails()) {
                return new ApiResponseResource(
                    false,
                    $validator->errors(),
                    null
                );
            }

            $email = $request->email;
            $frontendPath = '/reset-password?email=';
        }

        $recentResetPassword = PasswordResetToken::getRecentResetPasswordToken($email);

        if ($recentResetPassword) {
            $remainingTime = PasswordResetToken::getRemainingTime($recentResetPassword);
            return new ApiResponseResource(
                false,
                'Dapat mengirim ulang link reset kata sandi dalam ' . "{$remainingTime['minutes']} menit, dan {$remainingTime['seconds']} detik.",
                $remainingTime['minutes'] . ':' . $remainingTime['seconds'],
            );
        }

        try {
            $token = Str::uuid()->toString(); 
            $user = User::findByEmail($email); 
            $nama = $user->first_name . ' ' . $user->last_name;

            $dataUser = [
                'email' => $email,
                'token' => $token
            ];

            $frontendUrl = env('FRONTEND_URL', 'http://localhost:3000');
            $url = $frontendUrl . $frontendPath . urlencode($email) . '&token=' . $token; 
            Mail::to($email)->send(new TemplateForgetPassword($email, $url, $nama)); 

            PasswordResetToken::createPasswordResetToken($dataUser);

            return new ApiResponseResource(
                true,
                'Link reset kata sandi telah dikirim ke email anda.',
                [
                    'email' => $email
                ]
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
     * Reset the user's password using the provided token and new password.
     *
     * @param \Illuminate\Http\Request $request 
     *
     * @return \Illuminate\Http\JsonResponse 
     */
    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email|max:100',
            'token' => 'required',
            'new_password' => 'required|min:8',
            'confirm_new_password' => 'required|min:8|same:new_password',
        ], [
            'email.required' => 'Email tidak boleh kosong',
            'email.email' => 'Email harus valid',
            'email.exists' => 'Email belum terdaftar',
            'email.max' => 'Email maksimal 100 karakter',
            'token.required' => 'Token harus diisi',
            'new_password.required' => 'Kata sandi baru tidak boleh kosong',
            'new_password.min' => 'Kata sandi baru minimal 8 digit',
            'confirm_new_password.required' => 'Konfirmasi kata sandi baru tidak boleh kosong',
            'confirm_new_password.min' => 'Konfirmasi kata sandi baru minimal 8 digit',
            'confirm_new_password.same' => 'Konfirmasi kata sandi tidak sama'
        ]);
        if ($validator->fails()) {
            return new ApiResponseResource(
                false,
                $validator->errors(),
                null
            );
        }

        if (!PasswordResetToken::findPasswordResetToken($request->only('email', 'token'))) {
            return new ApiResponseResource(
                false,
                'Token reset kata sandi tidak valid atau telah kadaluarsa.',
                null
            );
        }

        try {
            $user = User::findByEmail($request->email);
            $user->updatePassword($request->new_password);
            PasswordResetToken::deletePasswordResetToken($request->email);

            return new ApiResponseResource(
                true,
                'kata sandi berhasil diubah.',
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
     * Get Summary data for dashboard user
     *
     * @return \Illuminate\Http\JsonResponse 
     */
    public function getSummary()
    {   
        $user = auth()->user();
        $nama = $user->first_name . ' ' . strtolower($user->last_name);

        $greetingMessage = \App\Helpers\TimeGreetingHelper::getGreeting();
        
        $leadsCount = Customer::countCustomerSummary($user->email, 'leads', $user->role, $user->user_company_id);
        $contactsCount = Customer::countCustomerSummary($user->email, 'contact', $user->role, $user->user_company_id);
        
        $customersCompanyCount = CustomersCompany::countCustomersCompany($user->email, $user->role, $user->user_company_id);

        $dealsQualification = Deal::countDealsByStage($user->email, $user->role, $user->user_company_id, 'qualificated');
        
        $dealsProposal = Deal::countDealsByStage($user->email,  $user->role, $user->user_company_id, 'proposal');

        $dealsNegotiation = Deal::countDealsByStage($user->email,  $user->role, $user->user_company_id, 'negotiate');

        $dealsWon = Deal::countDealsByStage($user->email, $user->role, $user->user_company_id, 'won');

        $dealsLost = Deal::countDealsByStage($user->email, $user->role, $user->user_company_id, 'lose');
        $dealsValue = Deal::sumValueEstimatedByStage($user->email, $user->role, $user->user_company_id);

        Carbon::setLocale('id');
        $formattedDate = now()->translatedFormat('l, d F Y');

        return new ApiResponseResource(
            true,
            'Dashboard user',
            [
                'user' => $nama,
                'greeting' => $greetingMessage,
                'date' => $formattedDate,
                'activities' => [
                    'leads' => $leadsCount,
                    'contacts' => $contactsCount,
                    'customers_companies' => $customersCompanyCount,
                ],
                'deals_pipeline' => [
                    'count' => [
                        'qualification' => $dealsQualification,
                        'proposal' => $dealsProposal,
                        'negotiation' => $dealsNegotiation,
                        'won' => $dealsWon,
                        'lose' => $dealsLost
                    ],
                    'value' => $dealsValue
                ],
            ]
        );
    }
}

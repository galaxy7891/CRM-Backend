<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApiResponseResource;
use App\Mail\TemplateForgetPassword;
use App\Models\Customer;
use App\Models\Deal;
use App\Models\Organization;
use App\Models\PasswordResetToken;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
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

            return $user->load('company');

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

        } catch (\Exception $e) {

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
                'Data user tidak ditemukan',
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
                "Data User {$user->first_name} " . strtolower($user->last_name) . "berhasil diubah",
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
                    'User tidak ditemukan',
                    null
                );
            }

            $first_name = $user->first_name;
            $last_name = $user->last_name;
            $user->delete();

            return new ApiResponseResource(
                true,
                "User {$first_name} {$last_name} Berhasil Dihapus!",
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

        $recentResetPassword = PasswordResetToken::getRecentResetPasswordToken($request->email);

        if ($recentResetPassword) {
            $remainingTime = PasswordResetToken::getRemainingTime($recentResetPassword);

            return new ApiResponseResource(
                false,
                'Dapat mengirim ulang link reset password dalam ' . "{$remainingTime['minutes']} menit, dan {$remainingTime['seconds']} detik.",
                null
            );
        }

        try {

            $email = $request->email;
            $token = Str::uuid()->toString();

            $user = User::findByEmail($email);
            $nama = $user->first_name . ' ' . $user->last_name;

            $dataUser = [
                'email' => $email,
                'token' => $token
            ];

            $frontendUrl = env('FRONTEND_URL', 'http://localhost:3000');
            $url = $frontendUrl . '/reset-password?email=' . urlencode($email) . '&token=' . $token;
            Mail::to($email)->send(new TemplateForgetPassword($email, $url, $nama));

            PasswordResetToken::createPasswordResetToken($dataUser);

            return new ApiResponseResource(
                true,
                'Link Reset Password telah dikirim ke email anda.',
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
            'password' => 'required|min:8',
            'new_password' => 'required|min:8|same:new_password',
        ], [
            'email.required' => 'Email tidak boleh kosong',
            'email.email' => 'Email harus valid',
            'email.exists' => 'Email belum terdaftar',
            'email.max' => 'Email maksimal 100 karakter',
            'token.required' => 'Token harus diisi',
            'password.required' => 'Kata sandi baru tidak boleh kosong',
            'password.min' => 'Kata sandi baru minimal 8 digit',
            'new_password.required' => 'Kata sandi baru tidak boleh kosong',
            'new_password.min' => 'Konfirmasi kata sandi baru minimal 8 digit',
            'new_password.same' => 'Konfirmasi kata sandi baru minimal 8 digit'
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
                'Token reset password tidak valid atau telah kadaluarsa.',
                null
            );
        }

        try {

            $user = User::findByEmail($request->email);
            $user->updatePassword($request->new_password);
            PasswordResetToken::deletePasswordResetToken($request->email);

            return new ApiResponseResource(
                true,
                'Password berhasil diubah.',
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

        $greetingMessage = \App\Helpers\TimeGreetingHelper::getGreeting() . ', ' . $nama;

        $leadsCount = Customer::countCustomerByCategory($user->email, 'leads');
        $contactsCount = Customer::countCustomerByCategory($user->email, 'contact');

        $organizationsCount = Organization::countOrganization($user->email);

        $dealsQualification = Deal::countDealsByStage($user->email, 'qualificated');
        $dealsProposal = Deal::countDealsByStage($user->email, 'proposal');
        $dealsNegotiation = Deal::countDealsByStage($user->email, 'negotiate');
        $dealsWon = Deal::countDealsByStage($user->email, 'won');
        $dealsLost = Deal::countDealsByStage($user->email, 'lose');
        $dealsValue = Deal::sumValueEstimatedByStage($user->email);

        Carbon::setLocale('id');
        $formattedDate = now()->translatedFormat('l, d F Y');

        return new ApiResponseResource(
            true,
            $greetingMessage,
            [
                'user' => $nama,
                'date' => $formattedDate,
                'activities' => [
                    'leads' => $leadsCount,
                    'contacts' => $contactsCount,
                    'organizations' => $organizationsCount,
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

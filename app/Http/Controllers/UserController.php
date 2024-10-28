<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Support\Str;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use App\Models\PasswordResetToken;
use App\Mail\TemplateForgetPassword;
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
                'Data user tidak ditemukan',
                null
            );
        }

        try {
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
                'Data user tidak ditemukan',
                null
            );
        }

        $validator = Validator::make($request->all(), [
            'company_id' => 'sometimes|nullable|uuid',
            'first_name' => 'sometimes|required|string|max:50',
            'last_name' => 'sometimes|nullable|string|max:50',
            'job_position' => 'sometimes|required|max:50',
            'role' => 'sometimes|required|in:super_admin,admin,employee',
            'phone' => "sometimes|required|numeric|max_digits:15|unique:users,phone,$id",
            'email' => "sometimes|required|email|unique:users,email,$id",
            'gender' => 'sometimes|nullable|in:male,female,other',
        ], [
            'company_id.uuid' => 'ID Company harus berupa UUID yang valid.',
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
            $updatedUser = User::updateUser($request->all(), $user->id);

            return new ApiResponseResource(
                true,
                "Data user {$updatedUser->first_name} " . strtolower($updatedUser->last_name) . " berhasil diubah",
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
                'Data user tidak ditemukan',
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
                "Foto profil user {$user->first_name} " . strtolower($user->last_name) . "berhasil diubah",
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
                'Data user tidak ditemukan',
                null
            );
        }

        try {
            $first_name = $user->first_name;
            $last_name = $user->last_name;
            $user = User::deleteUser($user->id);

            return new ApiResponseResource(
                true,
                "Data user {$first_name} " . strtolower($last_name) . "berhasil dihapus",
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
        // Inisialisasi variabel
        $email = '';
        $frontendPath = '';

        // Cek apakah pengguna terautentikasi
        if (auth()->check()) {
            $user = auth()->user();
            $email = $user->email; // Ambil email dari pengguna yang terautentikasi
            $frontendPath = '/change-password-email?email='; // Path untuk pengguna terautentikasi
        } else {
            // Validasi email jika pengguna tidak terautentikasi
            $validator = Validator::make($request->only('email'), [
                'email' => 'required|email|exists:users,email|max:100'
            ], [
                'email.required' => 'Email tidak boleh kosong',
                'email.email' => 'Email harus valid',
                'email.exists' => 'Email belum terdaftar',
                'email.max' => 'Email maksimal 100 karakter',
            ]);

            // Jika validasi gagal
            if ($validator->fails()) {
                return new ApiResponseResource(
                    false,
                    $validator->errors(),
                    null
                );
            }

            // Ambil email dari permintaan
            $email = $request->email;
            $frontendPath = '/reset-password?email='; // Path untuk pengguna tidak terautentikasi
        }

        // Cek apakah ada token reset password yang belum expired
        $recentResetPassword = PasswordResetToken::getRecentResetPasswordToken($email);
        if ($recentResetPassword) {
            $remainingTime = PasswordResetToken::getRemainingTime($recentResetPassword);
            return new ApiResponseResource(
                false,
                'Dapat mengirim ulang link reset kata sandi dalam ' . "{$remainingTime['minutes']} menit, dan {$remainingTime['seconds']} detik.",
                $remainingTime['minutes'] . ':' . $remainingTime['seconds'],
            );
        }

        // Jika tidak ada token yang masih valid, lanjutkan dengan pembuatan token baru
        try {
            $token = Str::uuid()->toString(); // Buat token baru
            $user = User::findByEmail($email); // Cari pengguna berdasarkan email
            $nama = $user->first_name . ' ' . $user->last_name; // Ambil nama pengguna

            $dataUser = [
                'email' => $email,
                'token' => $token
            ];

            $frontendUrl = env('FRONTEND_URL', 'http://localhost:3000');
            $url = $frontendUrl . $frontendPath . urlencode($email) . '&token=' . $token; // URL untuk reset password
            Mail::to($email)->send(new TemplateForgetPassword($email, $url, $nama)); // Kirim email reset password

            PasswordResetToken::createPasswordResetToken($dataUser); // Simpan token reset password

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
            'Dashboard user',
            [
                'user' => $nama,
                'greeting' => $greetingMessage,
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

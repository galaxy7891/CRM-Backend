<?php

namespace App\Http\Controllers;

use App\Helpers\ActionMapperHelper;
use App\Http\Resources\ApiResponseResource;
use App\Mail\TemplateInviteUser;
use App\Models\User;
use App\Models\UserInvitation;
use App\Services\DataLimitService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserInvitationController extends Controller
{
    /**
     * Send link invitation to the user's email.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse 
     */
    public function sendInvitation(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return new ApiResponseResource(
                false,
                'Unauthorized',
                null
            );
        }
        
        $userCompanyId = $user->user_company_id;
        $limitCheck = DataLimitService::checkUsersLimit($userCompanyId);
        if ($limitCheck['isExceeded']) {
            return new ApiResponseResource(
                false, 
                $limitCheck['message'], 
                null
            );
        }
        
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:100|'. Rule::unique('users', 'email')->whereNull('deleted_at'),
            'role' => 'required|in:Admin,Karyawan',
            'job_position' => 'required|max:50',
        ], [
            'email.required' => 'Email tidak boleh kosong',
            'email.email' => 'Email harus valid',
            'email.unique' => 'Email sudah terdaftar',
            'email.max' => 'Email maksimal 100 karakter',
            'role.required' => 'Akses user harus diisi',
            'role.in' => 'Akses user harus pilih salah satu: Admin, atau Karyawan.',
            'job_position.required' => 'Jabatan tidak boleh kosong',
            'job_position.max' => 'Jabatan maksimal 50 karakter',
        ]);
        
        if ($validator->fails()) {
            return new ApiResponseResource(
                false,
                $validator->errors(),
                null
            );
        }

        $data = $request->all();
        if (isset($data['role'])) {
            $data['role'] = ActionMapperHelper::mapRoleToDatabase($data['role']);
        }

        $recentInvitation = UserInvitation::getRecentInvitation($data['email']);

        if ($recentInvitation) {
            $remainingTime = UserInvitation::getRemainingTime($recentInvitation);

            return new ApiResponseResource(
                false,
                'Anda hanya dapat mengundang pengguna ini sekali dalam seminggu. Dapat mengirim undangan ulang dalam ' .
                    "{$remainingTime['days']} hari, {$remainingTime['hours']} jam, {$remainingTime['minutes']} menit, dan {$remainingTime['seconds']} detik.",
                null
            );
        }

        try {
            $email = $data['email'];
            $token = Str::uuid()->toString();
            $expired_at = now()->addWeek()->toDateTimeString();
            $invited_by = Auth::user()->email;
            $nama = explode('@', $email)[0];
            
            $dataUser = [
                'email' => $email,
                'token' => $token,
                'expired_at' => $expired_at,
                'status' => 'pending',
                'invited_by' => $invited_by,
                'role' => $data['role'],
                'job_position' => $data['job_position'],
            ];

            $frontendUrl = env('FRONTEND_URL', 'https://loyalcust.vercel.app/');
            $url = $frontendUrl . '/accept-invitation?email=' . urlencode($email) . '&token=' . $token;

            Mail::to($email)->send(new TemplateInviteUser($email, $url, $nama, $invited_by));

            UserInvitation::createInvitation($dataUser);
            
            return new ApiResponseResource(
                true,
                'Link invitasi berhasil dikirimkan ke email anda.',
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
     * Create a new user after accepting the invitation.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createUser(Request $request)
    {
        $invitation = UserInvitation::findInvitation($request->only('email', 'token'));

        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email|max:100|'. Rule::unique('users', 'email')->whereNull('deleted_at'),
            'password' => 'required|string|min:8',
            'password_confirmation' => 'required|min:8|same:password',
            'first_name' => 'required|string|max:50',
            'last_name' => 'nullable|string|max:50',
            'phone' => 'required|numeric|max_digits:15|'. Rule::unique('users', 'phone')->where('user_company_id', $invitation->inviter->user_company_id)->whereNull('deleted_at'),
        ], [
            'token.required' => 'Token tidak boleh kosong',
            'email.required' => 'Email tidak boleh kosong',
            'email.email' => 'Email harus valid',
            'email.unique' => 'Email sudah terdaftar',
            'email.max' => 'Email maksimal 100 karakter',
            'password.required' => 'Password tidak boleh kosong',
            'password.mnin' => 'Password tidak boleh kosong',
            'password_confirmation.required' => 'Kata sandi tidak boleh kosong',
            'password_confirmation.same' => 'Kata sandi tidak sama',
            'first_name.required' => 'Nama depan tidak boleh kosong',
            'first_name.string' => 'Nama depan harus berupa teks',
            'first_name.max' => 'Nama depan maksimal 50  karakter',
            'last_name.required' => 'Nama belakang tidak boleh kosong',
            'last_name.string' => 'Nama belakang harus berupa teks',
            'last_name.max' => 'Nama belakang maksimal 50 karakter',
            'phone.required' => 'Nomor telepon tidak boleh kosong',
            'phone.numeric' => 'Nomor telepon harus berupa angka',
            'phone.max_digits' => 'Nomor telepon maksimal 15 angka',
            'phone.unique' => 'Nomor telepon sudah terdaftar.',
        ]);
        if ($validator->fails()) {
            return new ApiResponseResource(
                false,
                $validator->errors(),
                null
            );
        }

        if (!$invitation) {
            return new ApiResponseResource(
                false,
                'Token invitation tidak valid atau telah kadaluarsa.',
                null
            );
        }

        try {
            $inviter = $invitation->inviter;
            $user_company_id = $inviter?->user_company_id;

            $dataUser = [
                'user_company_id' => $user_company_id,
                'email' => $request->email,
                'role' => $invitation->role,
                'job_position' => $invitation->job_position,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'password' => $request->password,
                'phone' => $request->phone,
            ];

            $user = User::createUser($dataUser, $user_company_id);
            $invitation->updateStatus('accepted');

            return new ApiResponseResource(
                true,
                'Akun berhasil dibuat.',
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
}
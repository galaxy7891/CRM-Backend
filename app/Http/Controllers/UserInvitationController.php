<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApiResponseResource;
use App\Mail\TemplateInviteUser;
use App\Models\User;
use App\Models\UserInvitation;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

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
        $validator = Validator::make($request->only('email'), [
            'email' => 'required|email|unique:users,email|max:100',
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Email harus valid',
            'email.unique' => 'Email sudah terdaftar',
            'email.max' => 'Email maksimal 100 karakter',
        ]);

        if ($validator->fails()) {
            return new ApiResponseResource(
                false, 
                $validator->errors(),
                null
            );
        }
        
        $recentInvitation = UserInvitation::getRecentInvitation($request->email);

        if ($recentInvitation){
            $remainingTime = UserInvitation::getRemainingTime($recentInvitation);

            return new ApiResponseResource(
                false,
                'Anda hanya dapat mengundang pengguna ini sekali dalam seminggu. Dapat mengirim undangan ulang dalam ' . 
                "{$remainingTime['days']} hari, {$remainingTime['hours']} jam, {$remainingTime['minutes']} menit, dan {$remainingTime['seconds']} detik.",
                null
            );
        }

        try {
            $email = $request->email;
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
            ];

            $url = url('/accept-invitation?email=' . urlencode($email) . '&token=' . $token);
            Mail::to($email)->send(new TemplateInviteUser($email, $url, $nama, $invited_by));

            UserInvitation::createInvitation($dataUser);

            return new ApiResponseResource(
                true,
                'Link invitasi berhasil dikirimkan ke email anda.',
                [
                    'email' => $email,
                    'nama' => $nama
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
     * Create a new user after accepting the invitation.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users, email|max:100',
            'token' => 'required',
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'password' => 'required|string|min:8',
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Email harus valid',
            'email.unique' => 'Email sudah terdaftar',
            'email.max' => 'Email maksimal 100 karakter',
            'token.required' => 'Token wajib diisi',
            'first_name.required' => 'Nama depan wajib diisi',
            'first_name.string' => 'Nama depan harus berupa teks',
            'first_name.max' => 'Nama depan maksimal 50  karakter',
            'last_name.required' => 'Nama belakang wajib diisi',
            'last_name.string' => 'Nama belakang harus berupa teks',
            'last_name.max' => 'Nama belakang maksimal 50 karakter',
        ]);

        if ($validator->fails()) {
            return new ApiResponseResource(
                false,
                $validator->errors(),
                null
            );
        }
        
        if (!$invitation = UserInvitation::findInvitation($request->only('email', 'token'))) {
            return new ApiResponseResource(
                false,
                'Token invitation tidak valid atau telah kadaluarsa.',
                null
            );
        }

        try {
            $dataUser = [
                'email' => $request->email,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'password' => $request->password,
            ];

            $user = User::createUser($dataUser, null);
            $invitation->updateStatus('accepted');

            return new ApiResponseResource(
                true,
                'Akun berhasil dibuat.',
                $user,
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

<?php

namespace App\Http\Controllers;

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
            'email' => 'required|email|unique:users,email',
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Email harus valid',
            'email.unique' => 'Email sudah terdaftar',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $recentInvitation = UserInvitation::getRecentInvitation($request->email);

        if ($recentInvitation){
            $remainingTime = UserInvitation::getRemainingTime($recentInvitation);

            return response()->json([
                'success' => false,
                'message' => 'Anda hanya dapat mengundang pengguna ini sekali dalam seminggu. Dapat mengirim undangan ulang dalam ' . 
                "{$remainingTime['days']} hari, {$remainingTime['hours']} jam, {$remainingTime['minutes']} menit, dan {$remainingTime['seconds']} detik.",
                'data' => null
            ], 429);

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

            return response()->json([
                'success' => true,
                'message' => 'Link invitasi berhasil dikirimkan ke email anda.',
                'data' => [
                    'email' => $email,
                    'nama' => $nama
                ]
            ], 200);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Internal Server Error',
                'errors' => $e->getMessage()
            ], 500);
        
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
            'email' => 'required|email',
            'token' => 'required',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'password' => 'required|string|min:8',
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Email harus valid',
            'token.required' => 'Token wajib diisi',
            'first_name.required' => 'Nama depan wajib diisi',
            'first_name.string' => 'Nama depan harus berupa string',
            'first_name.max' => 'Nama depan maksimal 255 karakter',
            'last_name.required' => 'Nama belakang wajib diisi',
            'last_name.string' => 'Nama belakang harus berupa string',
            'last_name.max' => 'Nama belakang maksimal 255 karakter',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }
        
        if (!$invitation = UserInvitation::findInvitation($request->only('email', 'token'))) {
            return response()->json([
                'success' => false,
                'message' => 'Token invitation tidak valid atau telah kadaluarsa.'
            ], 400);
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

            return response()->json([
                'success' => true,
                'message' => 'Akun berhasil dibuat.',
                'data' => $user,
            ], 201);

        } catch (\Exception $e) {
            
            return response()->json([
                'success' => false,
                'message' => 'Internal Server Error',
                'errors' => $e->getMessage(),
            ], 500);
        
        }
    }

}

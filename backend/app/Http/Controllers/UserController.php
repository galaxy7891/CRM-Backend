<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

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
            'email' => 'required|email'
        ]);
        
        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);            
        }

        try {

            $email = $request->only('email');
            $status = Password::sendResetLink($email);
            
            if ($status === Password::RESET_LINK_SENT) {
                return response()->json([
                    'success' => true,
                    'message' => 'Link verifikasi telah dikirim ke email anda.',
                    'data' => null
                ], 200);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Email belum terdaftar.',
                'data' => null
            ], 404);


        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Internal Server Error',
                'errors' => $e->getMessage()
            ], 500);

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
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {

            $status = Password::reset(
                $request->only('email', 'password', 'token'),
                function ($user, $password) {
                    $user->forceFill([
                        'password' => Hash::make($password),
                    ])->save();
                }
            );
    
            if ($status === Password::PASSWORD_RESET) {
                return response()->json([
                    'success' => true,
                    'message' => 'Password berhasil diubah.',
                    'data' => null
                ], 200);
            }
    
            return response()->json([
                'success' => false,
                'message' => 'Password gagal diubah.',
                'data' => null
            ], 500);

        } catch (\Exception $e){

            return response()->json([
                'success' => false,
                'message' => 'Internal Server Error',
                'errors' => $e->getMessage()
            ], 500);   

        }
    }
}

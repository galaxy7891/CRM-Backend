<?php

namespace App\Http\Controllers;

use App\Models\Otp;
use App\Services\SendOTPService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class OtpController extends Controller
{

    /**
     * Send OTP to the user's email.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Services\SendOTPService $SendOTPService
     *
     * @return \Illuminate\Http\JsonResponse 
     */
    public function sendOTP(Request $request, SendOTPService $SendOTPService)
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

        $recentOTP = Otp::getRecentOTP($request->email);

        if ($recentOTP) {
            $remainingTime = Otp::getRemainingTime($recentOTP);

            return response()->json([
                'success' => false,
                'message' => 'Kirim ulang kode otp dalam ' .
                    "{$remainingTime['minutes']} menit, dan {$remainingTime['seconds']} detik.",
                'data' => null
            ], 429);
        }

        try {
            
            $email = $request->email;
            $nama = explode('@', $email)[0];

            $SendOTPService->generateAndSendOtp($email, $nama);

            return response()->json([
                'success' => true,
                'message' => 'OTP berhasil dikirimkan ke email anda.',
                'data' => [
                    'email' => $email,
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
     * Verify the OTP sent to the user's email.
     *
     * @param \Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyOTP(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'code' => 'required|digits:6',
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Email harus valid',
            'code.required' => 'Code OTP wajib diisi',
            'code.digits' => 'Code OTP harus 6 digit'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {

            $email = $request->email;
            $code = $request->code;

            $otp = Otp::findOTP($email, $code);
            if (!$otp) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kode OTP invalid atau OTP telah digunakan atau hangus.',
                    'data' => null
                ], 404);
            }

            if ($otp->code === $code) {
                $otp->markAsUsed($email);

                return response()->json([
                    'success' => true,
                    'message' => 'OTP berhasil diverifikasi.',
                    'data' => null
                ], 200);
            }

            return response()->json([
                'success' => false,
                'message' => 'OTP tidak sesuai.',
                'data' => null
            ], 400);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Internal Server Error',
                'errors' => $e->getMessage()
            ], 500);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApiResponseResource;
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

        $recentOTP = Otp::getRecentOTP($request->email);

        if ($recentOTP) {
            $remainingTime = Otp::getRemainingTime($recentOTP);

            return new ApiResponseResource(
                false,
                'Kirim ulang kode otp dalam ' . `{$remainingTime['minutes']} menit, dan {$remainingTime['seconds']} detik.`,
                null
            );
        }

        try {
            
            $email = $request->email;
            $nama = explode('@', $email)[0];

            $SendOTPService->generateAndSendOtp($email, $nama);

            return new ApiResponseResource(
                true,
                'OTP berhasil dikirimkan ke email anda.',
                [
                    'email' => $email,
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
     * Verify the OTP sent to the user's email.
     *
     * @param \Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyOTP(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:100',
            'code' => 'required|numeric|digits:6',
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Email harus valid',
            'email.email' => 'Email maksimal 100 karakter',
            'code.required' => 'Code OTP wajib diisi',
            'code.numeric' => 'Code OTP harus berupa angka',
            'code.digits' => 'Code OTP harus 6 digit'
        ]);

        if ($validator->fails()) {
            return new ApiResponseResource(
                false,
                $validator->errors(),
                null
            );
        }

        try {

            $email = $request->email;
            $code = $request->code;

            $otp = Otp::findOTP($email, $code);
            if (!$otp) {
                return new ApiResponseResource(
                    false,
                    'Kode OTP invalid atau OTP telah digunakan atau hangus.',
                    null
                );
            }

            if ($otp->code === $code) {
                $otp->markAsUsed($email);
                return new ApiResponseResource(
                    true,
                    'OTP berhasil diverifikasi.',
                    null
                );
            }

            return new ApiResponseResource(
                false,
                'OTP tidak sesuai.',
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

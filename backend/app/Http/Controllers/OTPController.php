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
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {

            $email = $request->email;

            $SendOTPService->generateAndSendOtp($email);

            return response()->json([
                'status' => 'success',
                'message' => 'OTP sent to your email.',
                'data' => [
                    'email' => $email
                ]
            ], 200);
        } catch (\Exception $e) {

            return response()->json([
                'status' => 'error',
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

        $validator = Validator::make($request->only('code'), [
            'code' => 'required|digits:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {

            $email = $request->email;
            $code = $request->code;

            $otp = Otp::findOTP($email);
            if (!$otp) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid OTP or OTP has already been used or expired.',
                ], 404);
            }

            if ($otp->code === $code) {
                $otp->is_used = true;
                $otp->save();

                return response()->json([
                    'status' => 'success',
                    'message' => 'OTP verified successfully.',
                ], 200);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'OTP does not match.',
            ], 400);
        } catch (\Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => 'Internal Server Error',
                'errors' => $e->getMessage()
            ], 500);
        }
    }
}

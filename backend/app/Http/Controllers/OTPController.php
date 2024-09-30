<?php

namespace App\Http\Controllers;

use App\Services\SendOTPService;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;

class OTPController extends Controller
{
    /**
     * Step A: User submits email to receive OTP
     */
    public function sendOTP(Request $request, SendOTPService $SendOTPService)
    {
    
        $validator = Validator::make($request->only('email'), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()){
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try{
    
            $SendOTPService->generateAndSendOtp($request->email);
    
            return response()->json([
                'status' => 'success',
                'message' => 'OTP sent to your email.'
            ], 200);

        } catch (\Exception $e){
            
            return response()->json([
                'status' => 'error',
                'message' => 'Internal Server Error',
                'errors' => $e->getMessage()
            ], 500);

        }
    }


    
    /**
     * Step A: Verify OTP
     */
    public function verifyOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp' => 'required|digits:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        // Ambil OTP dan waktu kadaluarsa dari session
        $sessionOtp = session('otp');
        $otpExpiresAt = session('otp_expires_at');

        // Validasi OTP
        if ($sessionOtp !== $request->otp) {
            return response()->json([
                'otp' => $sessionOtp,
                'error' => 'Invalid OTP.'
            ], 400);
        }

        // Validasi waktu kadaluarsa OTP
        if (now()->greaterThan($otpExpiresAt)) {
            return response()->json([
                'error' => 'OTP has expired.'
            ], 400);
        }

        // Clear OTP dari session
        session()->forget(['otp', 'otp_expires_at']);

        // Simpan data ke database (akan dilakukan di langkah berikutnya)
        // Di sini Anda bisa melanjutkan ke langkah berikutnya, misalnya ke Step B

        return response()->json(['message' => 'OTP verified. Proceed to the next step.'], 200);
    }
}

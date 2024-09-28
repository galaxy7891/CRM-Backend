<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Services\OtpService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{  
    /**
     * Login a User and Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
               
        try {

            $credentials= $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if (!$token = auth()->attempt($credentials)) {
                return response()->json(
                    ['error' => 'Email and password do not match'],
                    401
                );
            }

            return $this->respondWithToken($token);

        } catch (ValidationException $e) {
            
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $e->errors(),
            ], 422);
        
        } catch (\Exception $e) {

            return response()->json([
                'error' => 'Internal Server Error',
                'message' => $e->getMessage()
            ], 500);

        }
    }




    /**
     * Redirect the user to the Google login page.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }




    /**
     * Handle the callback from Google after authentication.
     *
     * Creates or updates the user in the local 
     * database and generates a JWT token for 
     * authentication in subsequent requests.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $nameParts = \App\Helpers\StringHelper::splitName($googleUser->name);

            $user = User::createOrUpdateGoogleUser($googleUser, $nameParts);

            $token = auth()->login($user);

            return $this->respondWithToken($token);
            
        } catch (\Exception $e) {
            
            return response()->json([
                'error' => 'Internal Server Error',
                'message' => $e->getMessage()
            ], 500);

        }
    }
  



    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        try {

            auth()->logout();

            return response()->json([
                'message' => 'Successfully logged out'
            ]);

        } catch (\Exception $e) {
            
            return response()->json([
                'error' => 'Failed to log out',
                'message' => $e->getMessage()
            ], 500);
        
        }
    }





    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }
  



    /**
     * Get the JWT array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }















    










    /**
     * Step A: User submits email to receive OTP
     */
    public function sendOTP(Request $request, OtpService $otpService)
    {
        try{

            $request->validate([
                'email' => 'required|email|unique:users,email',
            ]);
    
            session(['email' => $request->email]);
    
            $otpService->generateAndSendOtp($request->email);
    
            return response()->json([
                'message' => 'OTP sent to your email.',
            ], 200);

        } catch (ValidationException $e){
            
            return response()->json([
                'error' => 'Validation error',
                'message' => $e->getMessage()
            ], 422);

        } catch (\Exception $e){
            
            return response()->json([
                'error' => 'Internal Server Error',
                'message' => $e->getMessage()
            ], 500);

        }
    }


    
    /**
     * Step A: Verify OTP
     */
    public function verifyOtp(Request $request)
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


    /**
     * Step B: Submit personal data
     */
    public function registerStepB(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Simpan data diri di session
        session([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'password' => bcrypt($request->password), // Encrypt password
        ]);

        return response()->json(['message' => 'Personal data saved.'], 200);
    }


    /**
     * Step C: Submit company data
     */
    public function registerStepC(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'job_position' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'industry' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Simpan data perusahaan di session
        session([
            'job_position' => $request->job_position,
            'company_name' => $request->company_name,
            'industry' => $request->industry,
        ]);

        // Ketika semua data terkumpul, simpan ke database
        $user = User::create([
            'email' => session('email'),
            'first_name' => session('first_name'),
            'last_name' => session('last_name'),
            'password' => session('password'),
            'phone' => session('phone'),
        ]);

        // Simpan data perusahaan ke tabel terkait
        $user->company()->create([
            'name' => session('company_name'),
            'job_position' => session('job_position'),
            'industry' => session('industry'),
        ]);

        // Hapus data session setelah selesai
        session()->forget(['email', 'first_name', 'last_name', 'password', 'phone', 'job_position', 'company_name', 'industry']);

        return response()->json(['message' => 'Registration completed.'], 200);
    }

}

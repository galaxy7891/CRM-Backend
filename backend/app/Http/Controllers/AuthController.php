<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Email harus valid',
            'password.required' => 'Password wajib diisi',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {

            $credentials = $request->only(['email', 'password']);

            if (!$token = auth()->attempt($credentials)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email dan password tidak sesuai',
                    'data' => null
                ], 401);
            }

            return $this->respondWithToken($token);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Internal Server Error',
                'errors' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Register a User and Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'password' => 'required',
            'phone' => 'required',
            'job_position' => 'required',
            'name' => 'required',
            'industry' => 'required',
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Email harus valid',
            'email.unique' => 'Email sudah terdaftar',
            'first_name.required' => 'Nama depan wajib diisi',
            'first_name.string' => 'Nama depan harus berupa string',
            'first_name.max' => 'Nama depan maksimal 255 karakter',
            'last_name.required' => 'Nama belakang wajib diisi',
            'last_name.string' => 'Nama belakang harus berupa string',
            'last_name.max' => 'Nama belakang maksimal 255 karakter',
            'password.required' => 'Password wajib diisi',
            'phone.required' => 'Nomor telepon wajib diisi',
            'job_position.required' => 'Posisi pekerjaan wajib diisi',
            'name' => 'Nama perusahaan wajib diisi',
            'industry.required' => 'Jenis industri wajib diisi'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {

            $company = Company::registerCompany($request->all());
            User::createUser($request->all(), $company->id);

            $credentials = [
                'email' => $request->email,
                'password' => $request->password,
            ];

            if (!$token = auth()->attempt($credentials)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email dan password tidak sesuai',
                    'data' => null
                ], 401);
            }

            return $this->respondWithToken($token);
            
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Internal Server Error',
                'errors' => $e->getMessage()
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
                'success' => false,
                'message' => 'Internal Server Error',
                'errors' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Logout (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        try {

            auth()->logout();

            return response()->json([
                'status' => true,
                'message' => 'Logout berhasil',
                'data' => null
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Logout gagal',
                'errors' => $e->getMessage()
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
            'success' => true,
            'message' => 'Token berhasil dibuat',
            'data' => [
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60
            ]
        ], 200);
    }
}

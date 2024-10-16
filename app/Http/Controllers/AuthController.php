<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResponseResource;

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
            'email.required' => 'Email tidak boleh kosong',
            'email.email' => 'Email harus valid',
            'password.required' => 'Password tidak boleh kosong',
        ]);

        if ($validator->fails()) {
            return new ApiResponseResource(
                false,
                $validator->errors(),
                null,
            );
        }

        try {

            $credentials = $request->only(['email', 'password']);

            if (!$token = auth()->attempt($credentials)) {
                return new ApiResponseResource(
                    false,
                    'Email dan password tidak sesuai',
                    null,
                );
            }

            return $this->respondWithToken($token);
        } catch (\Exception $e) {

            return new ApiResponseResource(
                false,
                $e->getMessage(),
                null,
            );
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
            'google_id' => 'nullable|string|unique:users,google_id',
            'email' => 'required|email|unique:users,email',
            'first_name' => 'required|string|max:50',
            'last_name' => 'nullable|string|max:50',
            'password' => 'required|min:8',
            'new_password' => 'required|min:8|same:password',
            'phone' => 'required|numeric|max_digits:15|unique:users,phone',
            'job_position' => 'required|max:50',
            'name' => 'required|max:100',
            'industry' => 'required|max:50',
            'photo' => 'nullable|url',
        ], [
            'google_id.unique' => 'Akun Google sudah terdaftar',
            'email.required' => 'Email tidak boleh kosong',
            'email.email' => 'Email harus valid',
            'email.unique' => 'Email sudah terdaftar',
            'first_name.required' => 'Nama depan tidak boleh kosong',
            'first_name.string' => 'Nama depan harus berupa teks',
            'first_name.max' => 'Nama depan maksimal 50 karakter',
            'last_name.string' => 'Nama belakang harus berupa teks',
            'last_name.max' => 'Nama belakang maksimal 50 karakter',
            'password.required' => 'Password tidak boleh kosong',
            'password.mnin' => 'Password tidak boleh kosong',
            'new_password.required' => 'Password tidak boleh kosong',
            'new_password.required' => 'Password tidak boleh kosong',
            'phone.required' => 'Nomor telepon tidak boleh kosong',
            'phone.numeric' => 'Nomor telepon harus berupa angka',
            'phone.max_digits' => 'Nomor telepon maksimal 15 angka',
            'phone.unique' => 'Nomor telepon sudah terdaftar.',
            'job_position.required' => 'Posisi pekerjaan tidak boleh kosong',
            'job_position.max' => 'Posisi pekerjaan maksimal 50 karakter',
            'name' => 'Nama perusahaan tidak boleh kosong',
            'industry.required' => 'Jenis industri tidak boleh kosong',
            'industry.max' => 'Jenis industri maksimal 50 karakter',
           'photo.url' => 'URL photo tidak valid',
        ]);

        if ($validator->fails()) {
            return new ApiResponseResource(
                false,
                $validator->errors(),
                null,
            );
        }

        try {

            $company = Company::registerCompany($request->all());
            User::createUser($request->all(), $company->id);

            $credentials = [
                'email' => $request->email,
                'password' => $request->password,
            ];

            if (!$token = auth()->attempt($credentials)) {
                return new ApiResponseResource(
                    false,
                    'Email dan password tidak sesuai',
                    null,
                );
            }

            return $this->respondWithToken($token);
            
        } catch (\Exception $e) {
            return new ApiResponseResource(
                false,
                $e->getMessage(),
                null,
            );
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

            return new ApiResponseResource(
                true,
                'Data User Google',
                [   
                    'email' => $googleUser->email,
                    'first_name' => $nameParts['first_name'],
                    'last_name' => $nameParts['last_name'],
                    'role' => 'super_admin',
                    'photo' => $googleUser->avatar,
                    'google_id' => $googleUser->id,
                ]
            );

        } catch (\Exception $e) {
            return new ApiResponseResource(
                false,
                $e->getMessage(),
                null,
            );
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

            return new ApiResponseResource(
                true,
                'Logout berhasil',
                null,
            );
        } catch (\Exception $e) {

            return new ApiResponseResource(
                false,
                $e->getMessage(),
                null,
            );
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
        return new ApiResponseResource(
            true,
            'Token berhasil dibuat',
            [
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60
            ],
        );
    }
}

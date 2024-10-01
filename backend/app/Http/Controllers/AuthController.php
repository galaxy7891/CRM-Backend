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
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {

            $credentials = $request->only(['email', 'password']);

            if (!$token = auth()->attempt($credentials)) {
                return response()->json(
                    [
                        'status' => 'error',
                        'message' => 'Unauthorized',
                        'errors' => 'Email and password do not match'
                    ],
                    401
                );
            }

            return $this->respondWithToken($token);
        } catch (\Exception $e) {

            return response()->json([
                'status' => 'error',
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

        try {

            $company = Company::registerCompany($request->all());
            User::registerUser($request->all(), $company->company_id);

            $credentials = [
                'email' => $request->input('email'),
                'password' => $request->input('password'),
            ];

            if (!$token = auth()->attempt($credentials)) {
                return response()->json(
                    [
                        'status' => 'error',
                        'message' => 'Unauthorized',
                        'errors' => 'Email and password do not match'
                    ],
                    401
                );
            }

            return $this->respondWithToken($token);
        } catch (\Exception $e) {

            return response()->json([
                'status' => 'error',
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
                'status' => 'error',
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
            'status' => 'success',
            'message' => 'Token generated successfully',
            'data' => [
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60
            ]
        ], 200);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApiResponseResource;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    /**
     * Login a Admin and Get a JWT via given credentials.
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
     * Get the JWT array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        $admin = Admin::auth();

        return new ApiResponseResource(
            true,
            'Token berhasil dibuat',
            [
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60,
                'admin' => [
                   'id' => $admin->id,
                   'name' => ucfirst($admin->first_name) . ' ' . ucfirst($admin->last_name),
                   'email' => $admin->email,
                   'phone' => $admin->phone,
                   'photo' => $admin->image_url,
                ]
            ],
        );
    }
}
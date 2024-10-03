<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, $role)
    {
        try {
            
            $user = JWTAuth::parseToken()->authenticate();
            
            if ($user->role !== $role) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki izin untuk mengakses halaman ini'
                ], 403);
            }

        } catch (JWTException $e) {
            
            return response()->json([
                'success' => false,
                'message' => 'Token tidak valid'
            ], 401);
        
        }

        return $next($request);
    }

}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        try {
            
            JWTAuth::parseToken()->authenticate();

        } catch (JWTException $e) {
            
            return response()->json([
                'success' => false,
                'message' => 'Token tidak valid',
                'data' => null
            ], 401);
        
        }

        return $next($request);
    }
}

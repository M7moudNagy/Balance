<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class EnsureUserIsType
{
    public function handle($request, Closure $next, $role)
    {
        $user = JWTAuth::parseToken()->authenticate();

        if (!$user || $user->getJWTCustomClaims()['role'] !== $role) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return $next($request);
    }
}

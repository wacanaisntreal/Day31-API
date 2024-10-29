<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\Key;

class JWTMiddleware
{
    public function handle($request, Closure $next)
    {
        $token = $request->header('Authorization');

        if (!$token) {
            return response()->json(['success' => false, 'msg' => 'Token not provided'], 401);
        }

        try {
            $token = explode(' ', $token)[1];
            $credentials = JWT::decode($token, new Key(env('JWT_SECRET'), 'HS256'));
            $request->user = $credentials;
        } catch (ExpiredException $e) {
            return response()->json(['success' => false, 'msg' => 'Token expired'], 401);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => 'Invalid token'], 401);
        }

        return $next($request);
    }
}

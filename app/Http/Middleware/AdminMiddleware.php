<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class AdminMiddleware
{
    public function handle($request, Closure $next)
    {
        if (!isset($request->user) || !isset($request->user->role)) {
            return response()->json(['success' => false, 'msg' => 'Token not provided or invalid'], 401);
        }

        if ($request->user->role !== 'admin') {
            return response()->json(['success' => false, 'msg' => 'Access denied, admin role required'], 403);
        }

        return $next($request);
    }
}

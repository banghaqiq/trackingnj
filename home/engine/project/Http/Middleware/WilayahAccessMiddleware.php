<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;

class WilayahAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string|null  ...$roles
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        if (!$user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Account is deactivated'
            ], 403);
        }

        // Admin and Petugas Pos can access all wilayah
        if ($user->isAdmin() || $user->isPetugasPos()) {
            return $next($request);
        }

        // Keamanan users are limited to their assigned wilayah
        if ($user->isKeamanan()) {
            $wilayahId = $request->route('wilayah') ?? $request->wilayah_id ?? $request->get('wilayah_id');
            
            if ($wilayahId && $user->wilayah_id !== $wilayahId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. You can only access your assigned wilayah.'
                ], 403);
            }
        }

        return $next($request);
    }
}
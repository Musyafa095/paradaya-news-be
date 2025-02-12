<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Role;

class RoleAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        $roleAdmin = Role::where('name', 'admin')->first();

        if ($user->role_id != $roleAdmin->id) {
            return response()->json([
                'message' => 'User tidak memiliki akses halaman ini, hanya admin yang bisa mengakses'
            ], 403);
        }
        return $next($request);
    }
}

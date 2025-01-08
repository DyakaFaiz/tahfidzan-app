<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $roleIds)
    {
        $roles = explode('|', $roleIds);

        // Memeriksa apakah session role user cocok dengan salah satu role dalam array
        if (in_array(session('idRole'), $roles)) {
            return $next($request);
        }

        abort(404);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Login
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $userRoles = session()->get('idUser');
        
        if (!isset($userRoles)) {
            return redirect()->route('login')->with('error', 'Silahkan login terlebih dahulu');
        }
        return $next($request);
    }
}

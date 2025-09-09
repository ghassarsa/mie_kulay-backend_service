<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!auth()->check()) {
            abort(404); // kembalikan 404 jika belum login
        }

        $roles = explode(',', $role);
        if (!in_array(auth()->user()->role, $roles)) {
            abort(404); // kembalikan 404 jika role tidak cocok
        }

        return $next($request);
    }
}

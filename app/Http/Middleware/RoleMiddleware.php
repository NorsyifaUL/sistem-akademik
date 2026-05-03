<?php

namespace App\Http\Middleware;

use Closure;
class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, $role)
    {
        if (!auth()->check()) {
            return redirect('/');
        }

        if (auth()->user()->role != $role) {
            abort(403); // forbidden
        }

        return $next($request);
    }
}

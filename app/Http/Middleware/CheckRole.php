<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        // Check if the user is authenticated
        if (!$request->user()) {
            return response()->json(['error' => 'Unauthorized: Not authenticated'], 401);
        }

        // Check if the user has a role and if it matches the required role
        if (!$request->user()->role || $request->user()->role->name !== $role) {
            return response()->json(['error' => 'Unauthorized: Access denied'], 403);
        }

        return $next($request);
    }
}

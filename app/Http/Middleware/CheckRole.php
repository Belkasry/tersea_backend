<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle($request, Closure $next, $role)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Not logged in'], 401);
        }

        $user = Auth::user();

        if ($user->role !== $role) {
            return response()->json(['message' => 'Unauthorized - Incorrect role'], 403);
        }
        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('api')->user()?->role !== 'admin') {
            return response()->json(['message' => 'No autorizado. Se requiere rol de administrador.'], 403);
        }

        return $next($request);
    }
}
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminOrStaff
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->user()->role === 'admin' || auth()->user()->role === 'staff') {
            return $next($request);
        }

        return response()->json(['error' => 'Unauthorized'], 403);
    }
}

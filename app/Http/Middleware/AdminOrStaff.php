<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminOrStaff
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && in_array($user->role->slug, ['admin', 'staff'])) {
            return $next($request);
        }

        return response()->json(['error' => 'Unauthorized'], 403);
    }
}
